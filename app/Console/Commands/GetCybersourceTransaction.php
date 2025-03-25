<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Integrations\Cybersource\CyberSourceConfiguration;
use CyberSource\ApiClient;
use CyberSource\Api\TransactionDetailsApi;
use Exception;

class GetCybersourceTransaction extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cybersource:get-transaction {merchantId} {transactionId}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Retrieve a specific Cybersource transaction by ID for a given merchant';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $transactionId = $this->argument('transactionId');
        $merchantId = $this->argument('merchantId');

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

            // Initialize Transaction Details API
            $api = new TransactionDetailsApi($apiClient);

            // Display request info first
            $this->line("Host: " . $merchantConfig->getHost());
            $this->line("Path: /tss/v2/transactions/" . $transactionId);
            $this->line("Method: GET");
            $this->line("Authentication: " . $merchantConfig->getAuthenticationType());
            $this->line("");

            // Get transaction details
            $result = $api->getTransaction($transactionId);

            if (!$result) {
                $this->error("Transaction not found");
                return 1;
            }

            // Display raw response
            $this->line(json_encode(json_decode($result[0], true), JSON_PRETTY_PRINT));

        } catch (Exception $e) {
            $this->error("\nError retrieving transaction:");
            $this->error($e->getMessage());
            return 1;
        }

        return 0;
    }
}
