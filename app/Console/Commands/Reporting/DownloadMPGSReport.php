<?php

namespace App\Console\Commands\Reporting;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class DownloadMPGSReport extends Command
{
    protected $signature = 'reporting:download-mpgs';
    protected $description = 'Download transaction reports from Mastercard Gateway Service';

    private $merchantId = 'TECHMASTER';
    private $host = 'https://eu-gateway.mastercard.com';
    private $outputFilename;
    private $lastRunFilename = 'reportingLastMpgsRunTime';
    private $inFolder = 'mpgs_reports' . DIRECTORY_SEPARATOR . 'in' . DIRECTORY_SEPARATOR;

    public function __construct()
    {
        $this->outputFilename = 'mpgs_transactions_' . date('Y-m-d\TH:i:s\Z') . '.csv';
        parent::__construct();
    }

    public function handle()
    {
        //update php max execution time and memory limit

        ini_set('max_execution_time', 0);
        ini_set('memory_limit', '2048M');

//        $startDate = '2024-10-10T18:11:46Z';
//        $endDate = '2024-10-29T18:11:46Z';

        $startDate = $this->getLastRunDate() ?? date('Y-m-d\TH:i:s\Z', strtotime('-24 hours'));
        $endDate = date('Y-m-d\TH:i:s\Z', strtotime('-4 hours'));

        $columns = 'merchant,result,timeOfRecord,order.id,transaction.id,sourceOfFunds.provided.card.number,sourceOfFunds.provided.card.expiry.month,sourceOfFunds.provided.card.expiry.year,transaction.amount,transaction.currency,transaction.type,transaction.acquirer.id,response.acquirerCode';
        $header = 'merchant,result,time,order_id,txn_id,card_number,card_expiry_month,card_expiry_year,amount,currency,type,acquirer,acquirerCode';
        $safeHeader = urlencode($header);

        $url = "{$this->host}/history/version/1/mso/{$this->merchantId}/transaction?timeOfRecord.start={$startDate}&timeOfRecord.end={$endDate}&columns={$columns}&columnHeaders={$safeHeader}";

        try {
            $this->info("Starting download from URL: $url");

            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => $url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'GET',
                CURLOPT_HTTPHEADER => array(
                    'Authorization: Basic ' . base64_encode(config('services.mpgs.username') . ':' . config('services.mpgs.password')),
                ),
            ));

            $response = curl_exec($curl);
            $error_no = curl_errno($curl);
            $error_msg = curl_error($curl);
            curl_close($curl);

            if ($error_no == 0) {
                Storage::disk('local')->put($this->inFolder . $this->outputFilename, $response);
                $this->storeLastRunDate($endDate);
                $this->info("Report downloaded successfully.");
            } else {
                $this->error("Failed to download report.: " . $error_msg . "Response: " . $response);
            }
        } catch (\Exception $e) {
            $this->error("Error downloading report: " . $e->getMessage());
        }
    }

    private function getLastRunDate()
    {
        return Storage::disk('local')->exists($this->lastRunFilename)
            ? Storage::disk('local')->get($this->lastRunFilename)
            : null;
    }

    private function storeLastRunDate($date)
    {
        Storage::disk('local')->put($this->lastRunFilename, $date);
    }
}
