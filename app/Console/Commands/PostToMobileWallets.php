<?php

namespace App\Console\Commands;

use App\Common\Helpers;
use App\Integrations\KonseKonse\cGrate;
use App\Models\Transaction;
use Illuminate\Console\Command;

class PostToMobileWallets extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'wallet:post-deposits';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Post cash deposits to mobile money wallets for successful settlements';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Find transactions with pending settlement status and complete transaction status
        $transactions = Transaction::where('merchant_settlement_status', 'PENDING')->where('status', 'completed')->get();
            
        $this->info("Found " . count($transactions) . " transactions to process");
        
        
        foreach ($transactions as $transaction) {
            try {
                // Get wallet number and amount
                $walletNumber = '260' . $transaction->reference_1;
                $amount = $transaction->amount;
                
                //Determine network provider
                $network = Helpers::determineMobileNetwork('0' . $transaction->reference_1);
                
                $this->info("Processing transaction {$transaction->reference_2}: {$amount} to {$walletNumber} ({$network})");
                
                // Process the cash deposit
                $client = new cGrate($transaction->reference_2);
               
                $result = $client->processCashDeposit(
                    $amount,
                    $walletNumber,
                    $network,
                    $transaction->reference_2
                );
                
                // Update transaction record
                $transaction->provider_external_reference = $result['internalReferenceNumber'] ?? '';
                $transaction->provider_status_description = $result['responseMessage'] ?? '';
                $transaction->provider_payment_date = now();
                
                if ($result['errorCode'] == 0) {
                    $transaction->provider_push_status = 'SUCCESS';
                    $transaction->merchant_settlement_status = 'SUCCESS';
                    $transaction->merchant_settlement_date = now();
                    $this->info("Successfully processed transaction {$transaction->reference_2}");
                } else {
                    $transaction->provider_push_status = 'FAILED';
                    $transaction->merchant_settlement_status = 'FAILED';
                    $this->error("Failed to process transaction {$transaction->reference_2}: {$result['responseMessage']}");
                }
                
                $transaction->save();
                
            } catch (\Exception $e) {
                $this->error("Error processing transaction {$transaction->reference_2}: " . $e->getMessage());
                
                // Update transaction with error
                $transaction->provider_status_description = "Error: " . $e->getMessage();
                $transaction->provider_push_status = 'FAILED';
                $transaction->save();
                
                // Log the error and continue with next transaction
                \Illuminate\Support\Facades\Log::error("Mobile wallet posting error: " . $e->getMessage(), [
                    'transaction_id' => $transaction->id,
                    'reference_2' => $transaction->reference_2,
                    'wallet_number' => $transaction->reference_1 ?? 'unknown'
                ]);
            }
        }
    }
}
