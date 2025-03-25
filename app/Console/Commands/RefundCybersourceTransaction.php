<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Integrations\Cybersource\CyberSourceConfiguration;
use CyberSource\ApiClient;
use CyberSource\Api\RefundApi;
use CyberSource\Model\RefundPaymentRequest;
use CyberSource\Model\Ptsv2paymentsClientReferenceInformation;
use CyberSource\Model\Ptsv2paymentsOrderInformation;
use CyberSource\Model\Ptsv2paymentsOrderInformationAmountDetails;
use Exception;

class RefundCybersourceTransaction extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cybersource:refund-transaction {merchantId} {transactionId} {amount?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Refund a specific Cybersource transaction by ID for a given merchant';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $transactionId = $this->argument('transactionId');
        $merchantId = $this->argument('merchantId');
        $amount = $this->argument('amount');

        // Get merchant credentials
        $merchants = config('cybersource_keys');
        $merchant = collect($merchants)->firstWhere('mid', $merchantId);

        if (!$merchant) {
            $this->error("Merchant ID not found in configuration");
            return 1;
        }

        try {
            // Initialize Cybersource configuration
            $config = new CyberSourceConfiguration(
                $merchant['key'],
                $merchant['sharedkey'],
                $merchant['mid']
            );

            // Set up API client
            $hostConfig = $config->ConnectionHost();
            $merchantConfig = $config->merchantConfigObject();
            $apiClient = new ApiClient($hostConfig, $merchantConfig);

            // Initialize Refund API
            $api = new RefundApi($apiClient);

            // Display request info first
            $this->line("Host: " . $merchantConfig->getHost());
            $this->line("Path: /pts/v2/payments/" . $transactionId . "/refunds");
            $this->line("Method: POST");
            $this->line("Authentication: " . $merchantConfig->getAuthenticationType());
            $this->line("");

            // Create client reference information
            $clientReferenceInformation = new Ptsv2paymentsClientReferenceInformation([
                'code' => 'Techpay-refund-' . uniqid()
            ]);

            // Create refund request
            $refundRequest = new RefundPaymentRequest([
                'clientReferenceInformation' => $clientReferenceInformation
            ]);

            // If amount is provided, add order information for partial refund
            if ($amount) {
                $amountDetails = new Ptsv2paymentsOrderInformationAmountDetails([
                    'totalAmount' => $amount,
                    'currency' => $merchant['currency'] ?? 'USD' // Use merchant currency or default to USD
                ]);

                $orderInformation = new Ptsv2paymentsOrderInformation([
                    'amountDetails' => $amountDetails
                ]);

                $refundRequest['orderInformation'] = $orderInformation;

                $this->line("Refund Amount: " . $amount . " " . ($merchant['currency'] ?? 'USD'));
            } else {
                $this->line("Full Refund");
            }

            // Process refund
            $result = $api->refundPayment($refundRequest, $transactionId);

            if (!$result) {
                $this->error("Refund failed - no response received");
                return 1;
            }

            // Display raw response
            $this->line(json_encode(json_decode($result[0], true), JSON_PRETTY_PRINT));

            // Check if refund was successful
            $response = json_decode($result[0], true);
            print_r($response);
            if (isset($response['status']) && in_array($response['status'], ['PENDING', 'COMPLETED', 'SUCCEEDED'])) {
                $this->info("Refund processed successfully with status: " . $response['status']);
            } else {
                $this->error("Refund may not have been successful. Please check the response details.");
            }

        } catch (Exception $e) {
            $this->error("\nError processing refund:");
            $this->error($e->getMessage());
            return 1;
        }

        return 0;
    }
}
