<?php

namespace App\Console\Commands\Reporting;

use App\Common\ImprovedSystemStatsGenerator;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class GetImprovedDailyStats extends Command
{
    protected $signature = 'reporting:improved-stats';
    protected $description = 'Get improved daily transaction stats with detailed platform analysis';

    public function handle()
    {
        ini_set('max_execution_time', 0);
        ini_set('memory_limit', '2048M');

        $this->info('Collecting Data...');
        $startDate = date('Y-m-d 00:00:00', strtotime('yesterday'));
        $endDate = date('Y-m-d 23:59:59', strtotime('yesterday'));

        $data = [
            'settlement_stats' => ImprovedSystemStatsGenerator::getSettlementStats($startDate, $endDate),
            'summary' => ImprovedSystemStatsGenerator::getSummaryBySource($startDate, $endDate),
            'top_merchants' => ImprovedSystemStatsGenerator::getTopMerchants($startDate, $endDate),
            'hourly_stats' => ImprovedSystemStatsGenerator::getHourlyDistribution($startDate, $endDate),
            'card_stats' => ImprovedSystemStatsGenerator::getCardDistribution($startDate, $endDate),
            'cumulative_stats' => ImprovedSystemStatsGenerator::getCumulativeStats($startDate, $endDate),
            'report_date' => date('d-M-Y', strtotime('yesterday'))
        ];

        $this->info('Data Collection Done');
        $this->info('Sending Email');
        $this->SendEmail($data);
    }

    private function SendEmail($data)
    {
        try {
            Mail::send('emails.improved-stats', $data, function ($message) {
                $message->to('EddieMuyeba@techmasters.co.zm')->subject
                ('TechPay daily report as at ' . date('d-M-Y', strtotime('yesterday')));
                $message->from('stats@techpay.co.zm', 'TechPay')
                    ->cc('mweemba@techmasters.co.zm')
                    ->cc('Andrewmbewe@techpay.co.zm')
                    ->cc('charles@techpay.co.zm')
                    ->cc('choolwe@techpay.co.zm')
                    ->cc('Zacharia@techmasters.co.zm')
                    ->cc('kadipa@techpay.co.zm')
                    ->cc('mutintamachila@techpay.co.zm')
                    ->cc('chinedukoggu@techmasters.co.zm');
            });

            $this->info('Emails sent successfully!');
        } catch (Exception $ex) {
            $this->error($ex->getMessage());
        }
    }
}
