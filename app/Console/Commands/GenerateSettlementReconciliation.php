<?php

namespace App\Console\Commands;

use App\Models\Chargeback;
use App\Models\Charges;
use App\Models\MerchantReconciliation;
use App\Models\Merchants;
use App\Models\SettlementReports;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class GenerateSettlementReconciliation extends Command
{
    protected $signature = 'settlement:reconcile
                          {--merchant= : Specific merchant ID or all if not specified}
                          {--date= : Date to reconcile (defaults to yesterday)}
                          {--reason= : Reason for regeneration}
                          {--days= : Number of past days to reconcile}';

    protected $description = 'Generate merchant reconciliation records from settlement reports';

    public function handle()
    {
        $merchantId = $this->option('merchant');
        $date = $this->option('date');
        $days = $this->option('days');
        $reason = $this->option('reason') ?? 'Regular daily reconciliation';

        // Get merchants to process
        $merchants = $merchantId
            ? [Merchants::where('code', $merchantId)->firstOrFail()]
            : Merchants::all();

        foreach ($merchants as $merchant) {
            // Get date range
            if ($date) {
                $startDate = Carbon::parse($date);
                $endDate = $startDate->copy()->addDays($days ? (int)$days - 1 : 0);
            } else {
                // Get earliest settlement report
                $earliest = SettlementReports::where('merchant', $merchant->code)
                    ->orderBy('settlement_date', 'asc')
                    ->first();

                if (!$earliest) {
                    $this->warn("No settlement reports found for merchant {$merchant->code}");
                    continue;
                }

                $startDate = $earliest->settlement_date;
                $endDate = Carbon::now();
            }

            $this->info("Processing reconciliation for {$merchant->code} from {$startDate->format('Y-m-d')} to {$endDate->format('Y-m-d')}");

            // Process each date in range
            for ($date = $startDate; $date->lte($endDate); $date->addDay()) {
                try {
                    $this->processReconciliation($merchant, $date->copy(), $reason);
                } catch (\Exception $e) {
                    $this->error("Error processing {$merchant->code} for {$date->format('Y-m-d')}: {$e->getMessage()}");
                    Log::error("Reconciliation error", [
                        'merchant' => $merchant->code,
                        'date' => $date->format('Y-m-d'),
                        'error' => $e->getMessage()
                    ]);
                    continue;
                }
            }
        }

        $this->info('Reconciliation completed successfully');
    }

    private function processReconciliation(Merchants $merchant, Carbon $date, string $reason)
    {
        $this->info("Processing {$merchant->code} for {$date->format('Y-m-d')}");

        // Get settlement reports for the day
        $settlements = SettlementReports::where('merchant', $merchant->code)
            ->whereDate('settlement_date', $date)
            ->get();

        // Skip days with no settlement reports
        if ($settlements->isEmpty()) {
            $this->info("Skipping merchant: {$merchant->code} date {$date->format('Y-m-d')} - No settlement reports found");
            return;
        }

        // Calculate totals from settlement reports
        $transactionCount = $settlements->sum('volume') + $settlements->sum('credit_volume');
        $totalAmount = $settlements->sum('value');
        $settledAmount = $totalAmount; // Initial value, will be adjusted after fees

        // Initialize fees
        $platformFee = 0;
        $applicationFee = 0;
        $bankFee = 0;
        $rollingReserve = 0;
        $chargebackFees = 0;

        // Process fees for each settlement report
        foreach ($settlements as $settlement) {
            // Get applicable charges using model method
            $charges = Charges::getApplicableCharges(
                $settlement->source, // Using source as the payment channel/provider
                $merchant->code,
                $merchant->company_id
            );

            // Skip if no charges found
            if ($charges->isEmpty()) {
                $this->info("No charges found for settlement report {$settlement->id}, skipping fee calculation...");
                continue;
            }

            // Calculate and accumulate charges
            foreach ($charges as $charge) {
                // For TRANSACTION_FEE, use transaction count instead of amount
                if ($charge->charge_name->value === 'TRANSACTION_FEE') {
                    // If it's a fixed fee, multiply by transaction count
                    if ($charge->charge_type->value === 'FIXED') {
                        $calculatedAmount = $charge->charge_value * ($settlement->volume + $settlement->credit_volume);
                    } else {
                        // For percentage, still use the transaction amount
                        $calculatedAmount = $charge->calculateCharge($settlement->value);
                    }
                    $applicationFee += $calculatedAmount;
                } else {

                    $calculatedAmount = $charge->calculateCharge($settlement->value);

                    switch ($charge->charge_name->value) {
                        case 'PLATFORM_FEE':
                            $platformFee += $calculatedAmount;
                            break;
                        case 'TRANSACTION_FEE':
                            $applicationFee += $calculatedAmount;
                            break;
                        case 'BANK_FEE':
                            $bankFee += $calculatedAmount;
                            break;
                        case 'ROLLING_RESERVE':
                            $rollingReserve += $calculatedAmount;
                            break;
                        case 'CHARGEBACK_FEE':
                            $chargebackFees += $calculatedAmount;
                            break;
                    }
                }
            }
        }


        // Calculate return of reserve (from 120 days ago)
        $oldSettlements = SettlementReports::where('merchant', $merchant->code)
            ->whereDate('settlement_date', $date->copy()->subDays(120))
            ->get();

        $returnReserve = 0;

        foreach ($oldSettlements as $oldSettlement) {
            $oldCharges = Charges::getApplicableCharges(
                $oldSettlement->source,
                $merchant->code,
                $merchant->company_id
            );

            foreach ($oldCharges as $charge) {
                if ($charge->charge_name === 'ROLLING_RESERVE') {
                    $returnReserve += $charge->calculateCharge($oldSettlement->value);
                }
            }
        }

        // Get refunds from credit columns
        $refundAmount = $settlements->sum('credit_value');

        // Get chargebacks
        $chargebacks = Chargeback::where('card_acceptor_id', $merchant->acceptor_point)
            ->whereDate('chargeback_date', $date)
            ->get();

        $chargebackCount = $chargebacks->count();
        $chargebackAmount = $chargebacks->sum('orig_clear_amount');

        // Calculate net processed amount
        $netProcessed = $totalAmount
            - $platformFee
            - $applicationFee
            - $bankFee
            - $rollingReserve
            - $refundAmount
            - $chargebackAmount
            - $chargebackFees;

        // Calculate settled amount (net processed + return reserve)
        $settledAmount = $netProcessed + $returnReserve;

        // Get existing active reconciliation
        $existing = MerchantReconciliation::getActive($merchant->code, $date);

        // Mark existing as superseded if it exists
        if ($existing) {
            $existing->markAsSuperseded($reason);
        }

        // Get next version number
        $version = MerchantReconciliation::getNextVersion($merchant->code, $date);

        // Create new reconciliation record
        DB::transaction(function () use (
            $merchant, $date, $version, $reason,
            $transactionCount, $totalAmount, $bankFee,
            $platformFee, $applicationFee, $rollingReserve,
            $returnReserve, $refundAmount, $chargebackCount,
            $chargebackAmount, $chargebackFees, $netProcessed,
            $settledAmount
        ) {
            MerchantReconciliation::create([
                'merchant_id' => $merchant->code,
                'date' => $date,
                'status' => 'ACTIVE',
                'version' => $version,
                'reason' => $reason,
                'transaction_count' => $transactionCount,
                'total_amount' => $totalAmount,
                'bank_fee' => $bankFee,
                'platform_fee' => $platformFee,
                'application_fee' => $applicationFee,
                'rolling_reserve' => $rollingReserve,
                'return_reserve' => $returnReserve,
                'refund_amount' => $refundAmount,
                'chargeback_count' => $chargebackCount,
                'chargeback_amount' => $chargebackAmount,
                'chargeback_fees' => $chargebackFees,
                'net_processed' => $netProcessed,
                'settled_amount' => $settledAmount,
                'generated_at' => now(),
            ]);
        });

        $this->info("Completed reconciliation for {$merchant->code}");
    }
}
