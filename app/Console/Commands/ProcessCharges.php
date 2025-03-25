<?php

namespace App\Console\Commands;

use App\Models\Charges;
use App\Models\Merchants;
use App\Models\SettlementRecord;
use App\Models\TransactionCharges;
use App\Models\Transactions;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\{DB, Log};

class ProcessCharges extends Command
{
    protected $signature = 'charges:process {type=all : Type of records to process (transactions/settlements/all)}';
    protected $description = 'Process charges for completed transactions and settlement records';

    public function handle()
    {
        $type = $this->argument('type');
        $this->info("Processing charges for type: $type");

        if ($type === 'all' || $type === 'transactions') {
            $this->processTransactions();
        }

        if ($type === 'all' || $type === 'settlements') {
            $this->processSettlements();
        }

        $this->info('Charge processing completed');
    }

    private function processTransactions()
    {
        $this->info('Processing transaction charges...');

        Transactions::where('status', 'COMPLETE')
            ->where('charges_status', 'PENDING')
            ->chunk(10000, function ($transactions) {
                foreach ($transactions as $transaction) {
                    try {
                        // Get merchant to find company
                        $merchant = Merchants::where('code', $transaction->merchant_code)->first();
                        if (!$merchant) {
                            throw new \Exception("Merchant not found: {$transaction->merchant_code}");
                        }

                        // Get applicable charges using model method
                        $charges = Charges::getApplicableCharges(
                            $transaction->payment_channel,
                            $transaction->merchant_code,
                            $merchant->company_id
                        );

                        // Skip if no charges found
                        if ($charges->isEmpty()) {
                            $this->info("No charges found for transaction {$transaction->id}, skipping...");
                            continue;
                        }

                        DB::transaction(function () use ($transaction, $charges) {
                            // Calculate and store charges
                            foreach ($charges as $charge) {
                                TransactionCharges::create([
                                    'transaction_id' => $transaction->id,
                                    'charge_id' => $charge->id,
                                    'charge_name' => $charge->charge_name,
                                    'charge_type' => $charge->charge_type,
                                    'charge_value' => $charge->charge_value,
                                    'base_amount' => $transaction->amount,
                                    'calculated_amount' => $charge->calculateCharge($transaction->amount),
                                    'merchant_id' => $transaction->merchant_code
                                ]);
                            }

                            $transaction->charges_status = 'PROCESSED';
                            $transaction->save();
                        });

                        $this->info("Processed charges for transaction {$transaction->id}");
                    } catch (\Exception $e) {
                        $transaction->charges_status = 'FAILED';
                        $transaction->save();
                        Log::error("Failed to process charges for transaction {$transaction->id}: " . $e->getMessage());
                        $this->error("Failed to process charges for transaction {$transaction->id}: " . $e->getMessage());
                        die();
                    }
                }
            });
    }

    private function processSettlements()
    {
        $this->info('Processing settlement charges...');

        $totalSettlements = SettlementRecord::where('charges_status', 'PENDING')->count();
        $progressBar = $this->output->createProgressBar($totalSettlements);
        $progressBar->start();

        SettlementRecord::where('charges_status', 'PENDING')
            ->chunk(10000, function ($settlements) use ($progressBar) {
                foreach ($settlements as $settlement) {
                    try {
                        // Get merchant to find company
                        $merchant = Merchants::where('code', $settlement->merchant_id)->orWhere('acceptor_point', $settlement->merchant_id)->first();
                        if (!$merchant) {
                            $this->warn("Merchant not found: {$settlement->merchant_id}");
                        }

                        // Use settlement provider directly as channel (ABSA, FNB, UBA)
                        $charges = Charges::getApplicableCharges(
                            $settlement->provider,
                            $settlement->merchant_id,
                            $merchant->company_id
                        );

                        // Skip if no charges found
                        if ($charges->isEmpty()) {
                            Log::warning("No charges found for settlement {$settlement->id}, provider: {$settlement->provider}");
                            continue;
                        }

                        DB::transaction(function () use ($settlement, $charges, $merchant) {
                            // Calculate and store charges
                            foreach ($charges as $charge) {
                                TransactionCharges::create([
                                    'settlement_id' => $settlement->id,
                                    'charge_id' => $charge->id,
                                    'charge_name' => $charge->charge_name,
                                    'charge_type' => $charge->charge_type,
                                    'charge_value' => $charge->charge_value,
                                    'base_amount' => $settlement->original_amount,
                                    'calculated_amount' => $charge->calculateCharge($settlement->original_amount),
                                    'merchant_id' => $merchant->code,
                                ]);
                            }

                            $settlement->charges_status = 'PROCESSED';
                            $settlement->save();
                        });

//                        $this->info("Processed charges for settlement {$settlement->id}");
                    } catch (\Exception $e) {
                        $settlement->charges_status = 'FAILED';
                        $settlement->save();
                        Log::error("Failed to process charges for settlement {$settlement->id}: " . $e->getMessage());
                        $this->error("Failed to process charges for settlement {$settlement->id}: " . $e->getMessage());
                    }

                    $progressBar->advance();
                }
            });

        $progressBar->finish();
        $this->info('Charge processing completed');
    }
}
