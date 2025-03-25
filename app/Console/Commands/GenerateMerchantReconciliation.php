<?php

namespace App\Console\Commands;

use App\Models\Chargeback;
use App\Models\MerchantReconciliation;
use App\Models\Merchants;
use App\Models\SettlementRecord;
use App\Models\TransactionCharges;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class GenerateMerchantReconciliation extends Command
{
    protected $signature = 'reconciliation:generate
                          {--merchant= : Specific merchant ID or all if not specified}
                          {--date= : Date to reconcile (defaults to yesterday)}
                          {--reason= : Reason for regeneration}
                          {--days= : Number of past days to reconcile}';

    protected $description = 'Generate merchant reconciliation records';

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
                // Get earliest settlement record
                $earliest = SettlementRecord::where('merchant_id', $merchant->acceptor_point)
                    ->orderBy('transaction_date', 'asc')
                    ->first();

                if (!$earliest) {
                    $this->warn("No settlement records found for merchant {$merchant->code}");
                    continue;
                }

                $startDate = $earliest->transaction_date;
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

        // Get settlement records for the day
        $settlements = SettlementRecord::where('merchant_id', $merchant->acceptor_point)
            ->whereDate('transaction_date', $date)
            ->get();

        // Skip days with no settlement records
        if ($settlements->isEmpty()) {
            $this->info("Skipping merchant: $merchant->acceptor_point date {$date->format('Y-m-d')} - No settlement records found");
            return;
        }

        // Get settlement IDs for the day
        $settlementIds = $settlements->pluck('id');

        // Get transaction charges linked to these settlements
        $charges = TransactionCharges::where('merchant_id', $merchant->code)
            ->whereIn('settlement_id', $settlementIds)
            ->get()
            ->groupBy('charge_type');


        // Initialize fees to 0 if no charges found
        if ($charges->isEmpty()) {
            $this->info("No transaction charges found for {$date->format('Y-m-d')} - Using zero fees");
            $charges = collect([
                'PLATFORM_FEE' => collect(),
                'TRANSACTION_FEE' => collect(),
                'BANK_FEE' => collect(),
                'ROLLING_RESERVE' => collect(),
                'CHARGEBACK_FEE' => collect(),
            ]);
        }

        // Calculate totals from settlement records
        $transactionCount = $settlements->count();
        $totalAmount = $settlements->sum('original_amount');
        $settledAmount = $settlements->sum('settlement_amount');

        // Get fees from transaction charges
        $platformFee = TransactionCharges::whereIn('settlement_id', $settlementIds)->where('charge_name', 'PLATFORM_FEE')->sum('calculated_amount');
        $applicationFee = TransactionCharges::whereIn('settlement_id', $settlementIds)->where('charge_name', 'TRANSACTION_FEE')->sum('calculated_amount');
        $bankFee = TransactionCharges::whereIn('settlement_id', $settlementIds)->where('charge_name', 'BANK_FEE')->sum('calculated_amount');
        $rollingReserve = TransactionCharges::whereIn('settlement_id', $settlementIds)->where('charge_name', 'ROLLING_RESERVE')->sum('calculated_amount');

        // Calculate return of reserve (from 120 days ago)
        $oldSettlementIds = SettlementRecord::where('merchant_id', $merchant->acceptor_point)
            ->whereDate('transaction_date', $date->copy()->subDays(120))
            ->pluck('id');

        $returnReserve = TransactionCharges::where('merchant_id', $merchant->code)
            ->where('charge_type', 'ROLLING_RESERVE')
            ->whereIn('settlement_id', $oldSettlementIds)
            ->sum('calculated_amount');

        // Get refunds
        $refundAmount = $settlements->whereIn('type', ['Refund', 'REFUND'])->sum('original_amount');

        // Get chargebacks
        $chargebacks = Chargeback::where('card_acceptor_id', $merchant->acceptor_point)
            ->whereDate('chargeback_date', $date)
            ->get();

        $chargebackCount = $chargebacks->count();
        $chargebackAmount = $chargebacks->sum('orig_clear_amount');
        $chargebackFees = TransactionCharges::whereIn('settlement_id', $settlementIds)->where('charge_name', 'CHARGEBACK_FEE')->sum('calculated_amount');

        // Calculate net processed amount
        $netProcessed = $totalAmount
            - $platformFee
            - $applicationFee
            - $bankFee
            - $rollingReserve
            - $refundAmount
            - $chargebackAmount
            - $chargebackFees;

        // Get existing active reconciliation
        $existing = MerchantReconciliation::getActive($merchant->code, $date);

        // Mark existing as superseded if it exists
        if ($existing) {
            $existing->markAsSuperseded($reason);
        }

        // Get next version number
        $version = MerchantReconciliation::getNextVersion($merchant->code, $date);

        // Create new reconciliation record
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

        $this->info("Completed reconciliation for {$merchant->code}");
    }
}
