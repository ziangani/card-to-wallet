<?php

namespace App\Console\Commands\Reporting;

use DateTime;
use Exception;
use Illuminate\Console\Command;
use App\Integrations\Cybersource\CyberSourceConfiguration;
use CyberSource\ApiClient;
use CyberSource\ApiException;
use CyberSource\Api\ReportSubscriptionsApi;
use CyberSource\Api\ReportDefinitionsApi;
use CyberSource\Model\PredefinedSubscriptionRequestBean;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class CreateTechpayDailyBatchReport extends Command
{
    protected $signature = 'report:create-techpay-daily-batch';
    protected $description = 'Creates or verifies existence of Techpay daily batch details report subscription';

    private $reportsFolder = 'cybersource_reports/daily_batch';

    public function handle()
    {
        ini_set('max_execution_time', 0);
        ini_set('memory_limit', '2048M');

        $merchants = config('cybersource_keys');

        $this->info('Starting report subscription setup...');
        $this->output->progressStart(count($merchants));

        $hasErrors = false;

        foreach ($merchants as $merchant) {
            $this->output->progressAdvance();

            try {
                $this->info("\nProcessing merchant: " . $merchant['merchant']);

                // Initialize Cybersource configuration
                $config = new CyberSourceConfiguration(
                    $merchant['key'],
                    $merchant['sharedkey'],
                    $merchant['mid']
                );
                $config->setMerchantID($merchant['mid']);
                $config->setApiKey($merchant['key'], $merchant['sharedkey']);

                // Set up API client
                $hostConfig = $config->ConnectionHost();
                $merchantConfig = $config->merchantConfigObject();
                $apiClient = new ApiClient($hostConfig, $merchantConfig);
                $apiInstance = new ReportSubscriptionsApi($apiClient);
                $report_types = [
                    'PaymentBatchDetailClass',
                    'TransactionRequestClass',
                    'ChargebackAndRetrievalDetailClass',
                    'PayerAuthDetailClass'
                ];

                foreach ($report_types as $report_type) {
                    try {
                        $requestObjArr = [
                            "reportDefinitionName" => $report_type,
                            "subscriptionType" => "STANDARD",
                        ];
                        $requestObj = new PredefinedSubscriptionRequestBean($requestObjArr);
                        $organizationId = null;
                        $apiInstance->createStandardOrClassicSubscription($requestObj, $organizationId);
                        $this->info("Report subscription {$report_type} created successfully for " . $merchant['merchant']);
                    } catch (ApiException $e) {
                        $this->error("Report subscription {$report_type} already exists for " . $merchant['merchant']);
                    }
                }
            } catch (Exception $e) {
                $this->error("Unexpected error for merchant {$merchant['merchant']}: " . $e->getMessage());
                Log::error("Unexpected error creating report subscription for merchant {$merchant['merchant']}: " . $e->getMessage());
                $hasErrors = true;
            }
        }

        $this->output->progressFinish();
        $this->info("\nReport subscription setup completed");

        return $hasErrors ? Command::FAILURE : Command::SUCCESS;
    }
}
