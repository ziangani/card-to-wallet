<?php

namespace App\Console\Commands;

use App\Common\Helpers;
use App\Integrations\KonseKonse\cGrate;
use App\Models\BulkDisbursement;
use App\Models\DisbursementItem;
use App\Models\Transaction;
use App\Models\CorporateWallet;
use App\Models\CorporateWalletTransaction;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ProcessBulkDisbursements extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:process-bulk-disbursements {--disbursement_id= : Process a specific disbursement}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Process approved bulk disbursements and send funds to mobile wallets';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting bulk disbursement processing...');

        // Check if a specific disbursement ID was provided
        $disbursementId = $this->option('disbursement_id');
        
        if ($disbursementId) {
            $query = BulkDisbursement::where('id', $disbursementId)
                ->where('status', 'approved');
        } else {
            // Get all approved bulk disbursements
            $query = BulkDisbursement::where('status', 'approved');
        }
        
        $disbursements = $query->get();
        
        $this->info("Found " . count($disbursements) . " disbursements to process");
        
        foreach ($disbursements as $disbursement) {
            $this->processDisbursement($disbursement);
        }
        
        $this->info('Bulk disbursement processing completed');
    }
    
    /**
     * Process a single bulk disbursement.
     *
     * @param BulkDisbursement $disbursement
     * @return void
     */
    private function processDisbursement(BulkDisbursement $disbursement)
    {
        $this->info("Processing disbursement #{$disbursement->id}: {$disbursement->name}");
        
        try {
            // Start processing
            $disbursement->startProcessing();
            
            // Get the corporate wallet
            $wallet = $disbursement->corporateWallet;
            
            // Get all pending items
            $items = $disbursement->items()->where('status', 'pending')->get();
            $this->info("Found " . count($items) . " items to process");
            
            // Process each item individually
            $successCount = 0;
            $failCount = 0;
            
            foreach ($items as $item) {
                // Check if the wallet has sufficient balance for this item
                $itemTotalWithFee = $item->getTotalWithFee();
                
                if (!$wallet->hasSufficientBalance($itemTotalWithFee)) {
                    $this->error("Insufficient wallet balance for item #{$item->id}");
                    
                    // Update item status
                    $item->status = 'failed';
                    $item->error_message = 'Insufficient wallet balance';
                    $item->save();
                    
                    $failCount++;
                    continue;
                }
                
                // Process the item with a transaction for each
                $result = $this->processItem($item, $disbursement, $wallet);
                if ($result) {
                    $successCount++;
                } else {
                    $failCount++;
                }
            }
            
            // Update disbursement status based on results
            if ($failCount === 0 && $successCount > 0) {
                $disbursement->complete(false); // Not partial
                $this->info("Disbursement #{$disbursement->id} completed successfully");
            } elseif ($successCount === 0) {
                $disbursement->fail();
                $this->error("Disbursement #{$disbursement->id} failed completely");
            } else {
                $disbursement->complete(true); // Partial completion
                $this->warn("Disbursement #{$disbursement->id} partially completed: {$successCount} succeeded, {$failCount} failed");
            }
            
        } catch (\Exception $e) {
            $this->error("Error processing disbursement #{$disbursement->id}: " . $e->getMessage());
            
            // Update disbursement status
            $disbursement->status = 'failed';
            $disbursement->notes = 'Error: ' . $e->getMessage();
            $disbursement->save();
            
            // Log the error
            Log::error("Bulk disbursement processing error: " . $e->getMessage(), [
                'disbursement_id' => $disbursement->id,
                'exception' => $e
            ]);
        }
    }
    
    /**
     * Process a single disbursement item.
     *
     * @param DisbursementItem $item
     * @param BulkDisbursement $disbursement
     * @param CorporateWallet $wallet
     * @return bool
     */
    private function processItem(DisbursementItem $item, BulkDisbursement $disbursement, CorporateWallet $wallet)
    {
        $this->info("Processing item #{$item->id}: {$item->amount} to {$item->wallet_number}");
        
        try {
            // Update item status to processing
            $item->status = 'processing';
            $item->save();
            
            // Begin transaction
            DB::beginTransaction();
            
            try {
                // Create a transaction record
                $transaction = Transaction::create([
                    'uuid' => (string)Str::uuid(),
                    'transaction_type' => 'corporate_disbursement',
                    'wallet_provider_id' => $item->wallet_provider_id,
                    'wallet_number' => $item->wallet_number,
                    'recipient_name' => $item->recipient_name ?? 'Unknown',
                    'amount' => $item->amount,
                    'fee_amount' => $item->fee,
                    'total_amount' => $item->amount + $item->fee,
                    'currency' => $item->currency,
                    'status' => 'PENDING', // Start as pending
                    'merchant_settlement_status' => 'PENDING', // Will be processed immediately
                    'merchant_code' => 'CORPORATE_DISBURSEMENT',
                    'payment_channel' => 'CORPORATE_WALLET',
                    'reference_1' => preg_replace('/[^0-9]/', '', $item->wallet_number), // Clean wallet number
                    'reference_2' => $item->reference, // Item reference
                    'reference_3' => $disbursement->id, // Disbursement ID
                    'reference_4' => $item->recipient_name ?? 'Unknown', // Recipient name
                ]);
                
                // Create transaction charges
                Transaction::createCorporateTransactionCharges($transaction);
                
                // Link the transaction to the disbursement item
                $item->transaction_id = $transaction->id;
                $item->save();
                
                $this->info("Created transaction #{$transaction->id} for item #{$item->id}");
                
                // Deduct the amount from the corporate wallet
                $itemTotalWithFee = $item->getTotalWithFee();
                $walletTransaction = $wallet->withdraw(
                    $itemTotalWithFee,
                    "Disbursement to {$item->wallet_number} ({$item->recipient_name})",
                    $item->reference,
                    $disbursement->approved_by
                );
                
                if (!$walletTransaction) {
                    throw new \Exception("Failed to withdraw funds from corporate wallet");
                }
                
                // Link the wallet transaction to the disbursement item
                $walletTransaction->related_entity_type = 'disbursement_item';
                $walletTransaction->related_entity_id = $item->id;
                $walletTransaction->save();
                
                // Commit the database transaction
                DB::commit();
                
                // Now process the actual mobile wallet posting
                $result = $this->postToMobileWallet($transaction, $item);
                
                if ($result['success']) {
                    // Update transaction and item status
                    $transaction->status = 'COMPLETED';
                    $transaction->merchant_settlement_status = 'SUCCESS';
                    $transaction->merchant_settlement_date = now();
                    $transaction->provider_push_status = 'SUCCESS';
                    $transaction->provider_external_reference = $result['reference'] ?? '';
                    $transaction->provider_status_description = $result['message'] ?? 'Success';
                    $transaction->provider_payment_date = now();
                    $transaction->save();
                    
                    $item->status = 'completed';
                    $item->save();
                    
                    $this->info("Successfully processed item #{$item->id}");
                    return true;
                } else {
                    // Handle failure - update transaction and item status
                    $transaction->status = 'FAILED';
                    $transaction->merchant_settlement_status = 'FAILED';
                    $transaction->provider_push_status = 'FAILED';
                    $transaction->provider_status_description = $result['message'] ?? 'Failed to post to mobile wallet';
                    $transaction->save();
                    
                    $item->status = 'failed';
                    $item->error_message = $result['message'] ?? 'Failed to post to mobile wallet';
                    $item->save();
                    
                    // Refund the amount to the corporate wallet
                    $wallet->adjust(
                        $itemTotalWithFee,
                        "Refund for failed disbursement to {$item->wallet_number} ({$item->recipient_name})",
                        "REFUND-{$item->reference}",
                        $disbursement->approved_by
                    );
                    
                    $this->error("Failed to process item #{$item->id}: {$result['message']}");
                    return false;
                }
                
            } catch (\Exception $e) {
                // Rollback the transaction if something goes wrong
                DB::rollBack();
                throw $e;
            }
            
        } catch (\Exception $e) {
            $this->error("Error processing item #{$item->id}: " . $e->getMessage());
            
            // Update item status
            $item->status = 'failed';
            $item->error_message = 'Error: ' . $e->getMessage();
            $item->save();
            
            // Log the error
            Log::error("Disbursement item processing error: " . $e->getMessage(), [
                'item_id' => $item->id,
                'disbursement_id' => $disbursement->id,
                'exception' => $e
            ]);
            
            return false;
        }
    }
    
    /**
     * Post funds to a mobile wallet.
     *
     * @param Transaction $transaction
     * @param DisbursementItem $item
     * @return array
     */
    private function postToMobileWallet(Transaction $transaction, DisbursementItem $item)
    {
        try {
            // Get wallet number and amount
            $walletNumber = '260' . $transaction->reference_1;
            $amount = $transaction->amount;
            
            // Determine network provider
            $network = Helpers::determineMobileNetwork('0' . $transaction->reference_1);
            
            $this->info("Posting to mobile wallet: {$amount} to {$walletNumber} ({$network})");
            
            // Process the cash deposit
            $client = new cGrate($transaction->reference_2);
            
            $result = $client->processCashDeposit(
                $amount,
                $walletNumber,
                $network,
                $transaction->reference_2
            );
            
            if ($result['errorCode'] == 0) {
                return [
                    'success' => true,
                    'reference' => $result['internalReferenceNumber'] ?? '',
                    'message' => $result['responseMessage'] ?? 'Success'
                ];
            } else {
                return [
                    'success' => false,
                    'message' => $result['responseMessage'] ?? 'Unknown error'
                ];
            }
            
        } catch (\Exception $e) {
            Log::error("Mobile wallet posting error: " . $e->getMessage(), [
                'transaction_id' => $transaction->id,
                'item_id' => $item->id,
                'wallet_number' => $transaction->reference_1 ?? 'unknown'
            ]);
            
            return [
                'success' => false,
                'message' => 'Exception: ' . $e->getMessage()
            ];
        }
    }
}
