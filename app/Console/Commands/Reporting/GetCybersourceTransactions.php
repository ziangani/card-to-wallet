<?php

namespace App\Console\Commands\Reporting;

use App\Integrations\Cybersource\CyberSourceConfiguration;
use App\Models\AllTransactions;
use CyberSource\Api\ReportDownloadsApi;
use CyberSource\Api\SearchTransactionsApi;
use CyberSource\Api\TransactionDetailsApi;
use CyberSource\ApiClient;
use CyberSource\Model\CreateSearchRequest;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class GetCybersourceTransactions extends Command
{
    protected $signature = 'reporting:cybersource-sync';
    protected $description = 'Sync transactions from Cybersource to local database';

    public function handle()
    {
        ini_set('max_execution_time', 0);
        ini_set('memory_limit', '2048M');

        $merchants = config('cybersource_keys');

        $this->info('Starting Cybersource transaction sync...');
        $this->output->progressStart(count($merchants));

        foreach ($merchants as $merchant) {
            $this->output->progressAdvance();

            try {
                $this->info("\nProcessing transactions for merchant: " . $merchant['merchant']);

                $mid = $merchant['mid'];
                $key = $merchant['key'];
                $sharedSecret = $merchant['sharedkey'];

                // Initialize Cybersource configuration
                $config = new CyberSourceConfiguration($key, $sharedSecret, $mid);
                $config->setMerchantID($mid);
                $config->setApiKey($key, $sharedSecret);

                // Set up API client
                $host_config = $config->ConnectionHost();
                $merchantConfig = $config->merchantConfigObject();
                $apiClient = new ApiClient($host_config, $merchantConfig);

                // Get transaction details
                $transactionDetailsApi = new SearchTransactionsApi($apiClient);

                $offset = 0;
                $limit = 1000;

                do {
                    $requestObjArr = [
                        "save" => false,
                        "name" => "MRN",
                        "timezone" => "Africa/Lusaka",
                        "query" => "submitTimeUtc:[NOW/DAY-2DAYS TO NOW/DAY+1DAY}",
                        "offset" => $offset,
                        "limit" => $limit,
                        "sort" => "id:asc,submitTimeUtc:asc"
                    ];
                    $requestObj = new \CyberSource\Model\CreateSearchRequest($requestObjArr);

                    // Get list of transaction IDs for the date range
                    $response = $transactionDetailsApi->createSearch($requestObj);
                    $responseData = json_decode($response[0], true);

                    $totalCount = $responseData['totalCount'] ?? 0;
                    $this->info("\nProcessing batch: Offset $offset of $totalCount total records");

                    if (isset($responseData['_embedded']['transactionSummaries'])) {
                        $transactions = $responseData['_embedded']['transactionSummaries'];

                        foreach ($transactions as $transaction) {
                            try {
                                $reason_code = $transaction['applicationInformation']['reasonCode'] ?? '';
                                $result = ($reason_code == '100') ? 'SUCCESS' : 'FAILURE';
                                $transactionData = [
                                    'txn_id' => $transaction['id'],
                                    'source' => 'CYBERSOURCE',
                                    'merchant' => $mid,
                                    'result' => $result,
                                    'order_currency' => $transaction['orderInformation']['amountDetails']['currency'] ?? null,
                                    'txn_date' => isset($transaction['submitTimeUtc']) ? date('Y-m-d H:i:s', strtotime($transaction['submitTimeUtc'])) : null,
                                    'order_id' => $transaction['clientReferenceInformation']['code'] ?? null,
                                    'card_number' => $transaction['paymentInformation']['card']['suffix'] ?? null,
                                    'txn_amount' => $transaction['orderInformation']['amountDetails']['totalAmount'] ?? null,
                                    'txn_currency' => $transaction['orderInformation']['amountDetails']['currency'] ?? null,
                                    'txn_type' => $transaction['paymentInformation']['paymentType']['type'] ?? 'CARD',
                                    'response_acquirer_code' => $transaction['processorInformation']['responseCode'] ?? null,
                                    'submit_time_utc' => isset($transaction['submitTimeUtc']) ? date('Y-m-d H:i:s', strtotime($transaction['submitTimeUtc'])) : null,
                                    'application_name' => $transaction['clientReferenceInformation']['applicationName'] ?? 'CYBERSOURCE',
                                    'reason_code' => $transaction['applicationInformation']['reasonCode'] ?? null,
                                    'r_code' => $transaction['applicationInformation']['rCode'] ?? null,
                                    'r_flag' => $transaction['applicationInformation']['rFlag'] ?? null,
                                    'r_message' => $transaction['applicationInformation']['applications'][0]['rMessage'] ?? null,
                                    'client_reference_code' => $transaction['clientReferenceInformation']['code'] ?? null,
                                    'eci_raw' => $transaction['consumerAuthenticationInformation']['eciRaw'] ?? null,
                                    'bill_to_address1' => $transaction['orderInformation']['billTo']['address1'] ?? null,
                                    'bill_to_country' => $transaction['orderInformation']['billTo']['country'] ?? null,
                                    'bill_to_email' => $transaction['orderInformation']['billTo']['email'] ?? null,
                                    'bill_to_phone_number' => $transaction['orderInformation']['billTo']['phoneNumber'] ?? null,
                                    'bill_to_first_name' => $transaction['orderInformation']['billTo']['firstName'] ?? null,
                                    'bill_to_last_name' => $transaction['orderInformation']['billTo']['lastName'] ?? null,
                                    'amount_details_total_amount' => $transaction['orderInformation']['amountDetails']['totalAmount'] ?? null,
                                    'amount_details_currency' => $transaction['orderInformation']['amountDetails']['currency'] ?? null,
                                    'payment_type' => $transaction['paymentInformation']['paymentType']['type'] ?? 'CARD',
                                    'payment_method' => $transaction['paymentInformation']['paymentType']['method'] ?? null,
                                    'card_suffix' => $transaction['paymentInformation']['card']['suffix'] ?? null,
                                    'card_prefix' => $transaction['paymentInformation']['card']['prefix'] ?? null,
                                    'card_type' => $transaction['paymentInformation']['card']['type'] ?? null,
                                    'commerce_indicator' => $transaction['processingInformation']['commerceIndicator'] ?? null,
                                    'processor_name' => $transaction['processorInformation']['processor']['name'] ?? 'CYBERSOURCE',
                                    'approval_code' => $transaction['processorInformation']['approvalCode'] ?? null,
                                    'raw_data' => json_encode($transaction),
                                    'status' => 'PROCESSED'
                                ];

                                $existingTransaction = AllTransactions::where('txn_id', $transactionData['txn_id'])
                                    ->where('order_id', $transactionData['order_id'])
                                    ->where('source', $transactionData['source'])
                                    ->first();

                                if ($existingTransaction) {
                                    $this->info("Transaction already exists: " . $transactionData['txn_id']);
                                    continue;
                                }
                                // Create or update transaction
                                AllTransactions::updateOrCreate(
                                    [
                                        'txn_id' => $transactionData['txn_id'],
                                        'order_id' => $transactionData['order_id'],
                                        'source' => $transactionData['source']
                                    ],
                                    $transactionData
                                );

                            } catch (Exception $e) {
                                Log::error('Error processing transaction: ' . $e->getMessage());
                                $this->error('Error processing transaction: ' . $e->getMessage());
                            }
                        }
                    }

                    $offset += $limit;
                } while ($offset < $totalCount);

            } catch (Exception $e) {
                $this->error("Error processing merchant {$merchant['merchant']}: " . $e->getMessage());
                Log::error("Cybersource Transaction Sync Error for merchant {$merchant['merchant']}: " . $e->getMessage());
            }
        }

        $this->output->progressFinish();
        $this->info("\nTransaction sync completed");
    }

}
