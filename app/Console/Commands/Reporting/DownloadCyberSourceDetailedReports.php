<?php

namespace App\Console\Commands\Reporting;

use App\Integrations\Cybersource\CyberSourceConfiguration;
use App\Models\DownloadedReport;
use CyberSource\Api\ReportsApi;
use CyberSource\Api\ReportDownloadsApi;
use CyberSource\ApiClient;
use CyberSource\ApiException;
use DateInterval;
use DatePeriod;
use DateTime;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class DownloadCyberSourceDetailedReports extends Command
{
    protected $signature = 'reporting:download-cybersource-detailed';
    protected $description = 'Download detailed transaction reports from CyberSource';

    private $reportsFolder = 'cybersource_reports/daily';

    public function handle()
    {
        ini_set('max_execution_time', 0);
        ini_set('memory_limit', '2048M');

        $start = new DateTime('3 days ago');
        $end = new DateTime('tomorrow');

        $startTime = $start->format('Y-m-d\T00:00:00\Z');
        $endTime = $end->format('Y-m-d\T23:59:59\Z');

        $merchants = config('cybersource_keys');

        $this->info('Starting detailed report download...');

        foreach ($merchants as $merchant) {
            try {
                $this->info("\nProcessing reports for merchant: " . $merchant['merchant']);

                $config = new CyberSourceConfiguration(
                    $merchant['key'],
                    $merchant['sharedkey'],
                    $merchant['mid']
                );
                $config->setMerchantID($merchant['mid']);
                $config->setApiKey($merchant['key'], $merchant['sharedkey']);

                $hostConfig = $config->ConnectionHost();
                $merchantConfig = $config->merchantConfigObject();
                $apiClient = new ApiClient($hostConfig, $merchantConfig);

                $organizationId = $merchant['mid'];

                // First get available reports
                $reportsApi = new ReportsApi($apiClient);
                $reportMimeType = "text/csv";
                $timeQueryType = "executedTime";
                try {
                    $availableReports = $reportsApi->searchReports(
                        $startTime,
                        $endTime,
                        $timeQueryType,
                        $organizationId,
                        $reportMimeType
                    );

                } catch (ApiException $e) {
                    $this->warn("No reports available for merchant " . $merchant['merchant']);
                    continue;
                }

                if ($availableReports && isset($availableReports[0])) {
                    $reports = json_decode($availableReports[0], true);


                    if (!empty($reports)) {
                        $downloadApi = new ReportDownloadsApi($apiClient);

                        foreach ($reports['reportSearchResults'] as $report) {

                            $reportName = $report['reportName'];
                            if ($report['status'] != 'COMPLETED' || $report['reportFrequency'] != 'DAILY') {
                                $this->warn("Skipping report $reportName: status is {$report['status']}, frequency is {$report['reportFrequency']}");
                                continue;
                            }
                            $reportDate = (new DateTime($report['reportStartTime']))->format('Y-m-d');

                            // Check if report already exists
                            $file = $merchant['mid'] . '_' .
                                $report['reportStartTime'] . '_' .
                                $report['reportEndTime'] . '_' .
                                $reportName . '_ID_' .
                                $report['reportId']
                                . '.csv';

                            $filename = $this->reportsFolder . DIRECTORY_SEPARATOR . $report['organizationId'] . DIRECTORY_SEPARATOR . $report['reportName'] . DIRECTORY_SEPARATOR . $file;

                            if (!Storage::exists($filename)) {
                                try {
                                    $response = $downloadApi->downloadReport(
                                        $reportDate,
                                        $reportName,
                                        $organizationId
                                    );

                                    if ($response && isset($response[0])) {
                                        Storage::put($filename, $response[0]);

                                        // Record successful download
                                        if (DownloadedReport::where('source_report_id', $report['reportId'])->exists()) {
                                            $this->warn("Report already exists in database: $filename");
                                            continue;
                                        }
                                        DownloadedReport::create([
                                            'merchant_id' => $merchant['mid'],
                                            'report_type' => $report['subscriptionType'],
                                            'report_name' => $reportName,
                                            'report_format' => $report['reportMimeType'],
                                            'source_system' => 'CYBERSOURCE',
                                            'source_report_id' => $report['reportId'],
                                            'source_definition_id' => $report['reportDefinitionId'] ?? null,
                                            'frequency' => $report['reportFrequency'],
                                            'status' => 'completed',
                                            'report_start_time' => $report['reportStartTime'],
                                            'report_end_time' => $report['reportEndTime'],
                                            'timezone' => $report['timezone'] ?? 'UTC',
                                            'queued_time' => $report['queuedTime'] ?? null,
                                            'completed_time' => $report['reportCompletedTime'] ?? null,
                                            'file_path' => $this->reportsFolder . DIRECTORY_SEPARATOR . $report['organizationId'] . DIRECTORY_SEPARATOR . $report['reportName'],
                                            'file_name' => $file,
                                            'source_metadata' => [
                                                'organization_id' => $report['organizationId'],
                                                'subscription_type' => $report['subscriptionType'] ?? null,
                                            ],
                                            'report_meta' => $report,
                                        ]);

                                        $this->info("Successfully downloaded report: $filename");
                                    }
                                } catch (Exception $e) {
                                    $this->error("Error downloading report $reportName: " . $e->getMessage());
                                    Log::error("CyberSource Report Download Error for report $reportName: " . $e->getMessage());
                                }
                            } else {
                                $this->warn("Report already exists: $filename");
                            }
                        }
                    } else {
                        $this->warn("No reports available for merchant " . $merchant['merchant']);
                    }
                }
            } catch (Exception $e) {
                $this->error("Error processing merchant {$merchant['merchant']}: " . $e->getMessage());
                Log::error("CyberSource Report Download Error for merchant {$merchant['merchant']}: " . $e->getMessage());
            }
        }

        $this->info("\nReport download completed");
    }
}
