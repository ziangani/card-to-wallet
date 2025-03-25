<?php

namespace App\Console\Commands\Reporting;

use App\Integrations\Cybersource\CyberSourceConfiguration;
use App\Models\SettlementReports;
use CyberSource\Api\PaymentBatchSummariesApi;
use CyberSource\ApiClient;
use DateInterval;
use DatePeriod;
use DateTime;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class DownloadCyberSourceSettlementReport extends Command
{
    protected $signature = 'reporting:download-cybersource-settlement';
    protected $description = 'Download transaction reports from CyberSource';

    private $outputFilename;
    private $lastRunFilename = 'reportingLastCyberSourceRunTime';
    private $inFolder = 'cybersource_reports' . DIRECTORY_SEPARATOR . 'in' . DIRECTORY_SEPARATOR;

    // Define charge rates as constants
    const VAT_RATE = 0.03; // 3% VAT
    const ROLLING_RESERVE_RATE = 0.05; // 5% rolling reserve
    const COMMISSION_RATE = 0.012; // 1.2% commission

    public function __construct()
    {
        $this->outputFilename = 'cybersource_transactions_' . date('Y-m-d\TH:i:s\Z') . '.csv';
        parent::__construct();
    }

    public function handle()
    {
        ini_set('max_execution_time', 0);
        ini_set('memory_limit', '2048M');

        $start = new DateTime('90 days ago');
        $end = new DateTime('tomorrow');
        $interval = new DateInterval('P1D');
        $period = new DatePeriod($start, $interval, $end);

        $dates = [];
        foreach ($period as $date) {
            $startTime = $date->format('Y-m-d\T00:00:00\Z');
            $endDate = $date->format('Y-m-d\T23:59:59\Z');
            $dates[] = ['start' => $startTime, 'end' => $endDate];
        }

        $this->output->progressStart(count($dates));
        foreach ($dates as $dateRange) {
            $this->output->progressAdvance();

            $startTime = $dateRange['start'];
            $endTime = $dateRange['end'];
            $merchants = config('cybersource_keys');
            foreach ($merchants as $merchant) {
//                $this->info("\nProcessing report for merchant: " . $merchant['merchant'] . " for period: $startTime to $endTime.");
                try {
                    $mid = $merchant['mid'];
                    $key = $merchant['key'];
                    $sharedSecret = $merchant['sharedkey'];

                    $config = new CyberSourceConfiguration($key, $sharedSecret, $mid);
                    $config->setMerchantID($mid);
                    $config->setApiKey($key, $sharedSecret);

                    $host_config = $config->ConnectionHost();
                    $merchantConfig = $config->merchantConfigObject();
                    $apiClient = new ApiClient($host_config, $merchantConfig);

                    $organizationId = $mid;
                    $transactionDetailsApi = new PaymentBatchSummariesApi($apiClient);

                    $response = $transactionDetailsApi->getPaymentBatchSummary($startTime, $endTime, $organizationId);
                    $rawResponse = json_decode($response[0], true);
                    $reports = $response[0]['paymentBatchSummaries'] ?? [];

                    foreach ($reports as $report) {
                        if ($report->getSalesCount() == 0 && $report->getCreditCount() == 0) {
//                            $this->info("\nSkipping report for merchant: " . $report->getMerchantId() . " as it has no transactions.");
                            continue;
                        }
//
                        $paymentType = $report->getPaymentSubTypeDescription();

                        //Check if report already exists
                        $exists = SettlementReports::where('source', 'ABSA')
                            ->where('merchant', $report->getMerchantId())
                            ->where('settlement_date', $report->getEndTime()->format('Y-m-d'))
                            ->where('currency', $report->getCurrencyCode())
                            ->where('payment_type', $paymentType)
                            ->where('volume', $report->getSalesCount())
                            ->where('value', $report->getSalesAmount())
                            ->where('credit_volume', $report->getCreditCount())
                            ->where('credit_value', $report->getCreditAmount())
                            ->exists();
                        if ($exists) {
//                            $this->error("\nSkipping report for merchant: " . $report->getMerchantId() . " as it already exists.");
                            continue;
                        }

                        // Calculate transaction amounts
                        $totalTransactionAmount = $report->getSalesAmount() - $report->getCreditAmount();

                        // Calculate bank charges
                        $vatCharge = $totalTransactionAmount * self::VAT_RATE;
                        $rollingReserve = $totalTransactionAmount * self::ROLLING_RESERVE_RATE;
                        $totalBankCharge = $vatCharge + $rollingReserve;

                        // What bank settled us (after VAT and rolling reserve)
                        $bankSettlement = $totalTransactionAmount - $totalBankCharge;

                        // Calculate our commission from bank settlement amount
                        $ourCommission = $bankSettlement * self::COMMISSION_RATE;

                        // What we settled merchant (bank settlement minus our commission)
                        $merchantSettlement = $bankSettlement - $ourCommission;

                        $settlement = new SettlementReports();
                        $settlement->source = 'ABSA';
                        $settlement->merchant = $report->getMerchantId();
                        $settlement->settlement_date = $report->getEndTime()->format('Y-m-d');
                        $settlement->start_time = $report->getStartTime()->format('Y-m-d H:i:s');
                        $settlement->end_time = $report->getEndTime()->format('Y-m-d H:i:s');
                        $settlement->currency = $report->getCurrencyCode();
                        $settlement->payment_type = $paymentType;
                        $settlement->volume = $report->getSalesCount();
                        $settlement->value = $report->getSalesAmount();
                        $settlement->credit_volume = $report->getCreditCount();
                        $settlement->credit_value = $report->getCreditAmount();
                        $settlement->vat_charge = $vatCharge;
                        $settlement->rolling_reserve = $rollingReserve;
                        $settlement->bank_charge = $totalBankCharge;
                        $settlement->bank_settlement = $bankSettlement;
                        $settlement->our_charge = $ourCommission;
                        $settlement->merchant_settlement = $merchantSettlement;
                        $settlement->raw_data = json_encode($rawResponse);
                        $settlement->status = 'PROCESSED';
                        $settlement->save();
                        $this->info("\nProcessing report for merchant: " . $report->getMerchantId() . " " . $merchant['merchant'] . " for period: $startTime to $endTime.");

                    }

                } catch (\Exception $e) {
                    $this->error("Error downloading report: " . $e->getMessage());
                }
            }
        }
    }
}
