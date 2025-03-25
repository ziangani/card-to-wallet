<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Integrations\Cybersource\CyberSourceConfiguration;
use CyberSource\Api\SearchTransactionsApi;
use CyberSource\ApiClient;
use Exception;

class TestCyberSourceCredentials extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:test-cybersource-credentials';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test all CyberSource credentials for validity and print a report to the console';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $merchants = config('cybersource_keys');
        $report = [];
        $this->output->progressStart(count($merchants));
        foreach ($merchants as $merchant) {
            $this->output->progressAdvance();
//            $this->info("Testing merchant: " . $merchant['merchant']);

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

                // Initialize API with minimal query to test credentials
                $api = new SearchTransactionsApi($apiClient);
                $requestObj = new \CyberSource\Model\CreateSearchRequest([
                    "save" => false,
                    "name" => "MRN",
                    "timezone" => "Africa/Lusaka",
                    "query" => "submitTimeUtc:[NOW/DAY-1DAY TO NOW/DAY]",
                    "offset" => 0,
                    "limit" => 1
                ]);

                // Try to execute a search - if credentials are invalid, this will throw an exception
                $api->createSearch($requestObj);

                $report[] = [
                    'merchant' => $merchant['merchant'],
                    'mid' => $merchant['mid'],
                    'valid' => 'Valid',
                    'message' => 'Successfully authenticated'
                ];
            } catch (Exception $e) {
                $report[] = [
                    'merchant' => $merchant['merchant'],
                    'mid' => $merchant['mid'],
                    'valid' => 'Invalid',
                    'message' => $e->getMessage()
                ];
            }
        }
        $this->output->progressFinish();
        $this->info("\nCyberSource Credentials Report:");
        $this->newLine();

        foreach ($report as $entry) {
            if ($entry['valid'] === 'Invalid') {
                $this->error(sprintf(
                    "Merchant: %s (MID: %s) - %s\nError: %s",
                    $entry['merchant'],
                    $entry['mid'],
                    $entry['valid'],
                    $entry['message']
                ));
            } else {
                $this->info(sprintf(
                    "Merchant: %s (MID: %s) - %s",
                    $entry['merchant'],
                    $entry['mid'],
                    $entry['valid']
                ));
            }
            $this->newLine();
        }
    }
}
