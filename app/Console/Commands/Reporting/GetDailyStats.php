<?php

namespace App\Console\Commands\Reporting;

use App\Common\SystemStatsGenerator;
use \Exception;
use \DB;
use Illuminate\Console\Command;
use \Mail;


class GetDailyStats extends Command
{

    protected $signature = 'reporting:send-stats';
    protected $description = 'Command description';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        ini_set('max_execution_time', 0);
        ini_set('memory_limit', '2048M');

        $this->info('Collecting Data...');
        $startDate = date('Y-m-d 00:00:00', strtotime('yesterday'));
        $endDate = date('Y-m-d 23:59:59', strtotime('yesterday'));

        $summary = SystemStatsGenerator::getSummary($startDate, $endDate);
        $merchant_stats = SystemStatsGenerator::getByMerchant($startDate, $endDate);
        $misc_stats = SystemStatsGenerator::miscStats($startDate, $endDate);
        $data = array(
            'summary' => $summary,
            'merchant_stats' => $merchant_stats,
            'misc_stats' => $misc_stats,
        );

        $this->info('Data Collection Done');
        $this->info('Sending Email');
        $this->SendEmail($data);
    }


    private function SendEmail($data)
    {
        try {

            \Mail::send('emails.stats', $data, function ($message) {
                $message->to('EddieMuyeba@techmasters.co.zm')->subject
                ('TechPay daily report as at ' . date('d-M-Y', strtotime('yesterday')));
                $message->from('stats@techpay.co.zm', 'TechPay')
//                    ->cc('mweemba@techmasters.co.zm')
//                    ->cc('Andrewmbewe@techpay.co.zm')
//                    ->cc('charles@techpay.co.zm')
//                    ->cc('choolwe@techpay.co.zm')
                    ->cc('chinedukoggu@techmasters.co.zm');
//                    ;
            });

            $this->info('Emails sent successfully!');
        } catch (Exception $ex) {
            $this->error($ex->getMessage());
        }
    }

}
