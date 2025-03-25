<?php

namespace App\Console\Commands\Reporting;

use App\Common\ImprovedSystemStatsGenerator;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class GetImprovedWeeklyStats extends Command
{
    protected $signature = 'reporting:improved-weekly-stats';
    protected $description = 'Get improved weekly transaction stats with detailed platform analysis';

    public function handle()
    {
        ini_set('max_execution_time', 0);
        ini_set('memory_limit', '2048M');

        $this->info('Collecting Data...');
        // Get previous week's date range (Monday to Sunday)
        // We run this early Sunday morning, so we look at the complete previous week
        $startDate = date('Y-m-d 00:00:00', strtotime('last monday -1 week'));
        $endDate = date('Y-m-d 23:59:59', strtotime('last sunday'));

        $data = [
            'settlement_stats' => ImprovedSystemStatsGenerator::getWeeklySettlementStats($startDate, $endDate),
            'summary' => ImprovedSystemStatsGenerator::getSummaryBySource($startDate, $endDate),
            'top_merchants' => ImprovedSystemStatsGenerator::getTopMerchants($startDate, $endDate),
            // 'daily_stats' => ImprovedSystemStatsGenerator::getDailyDistribution($startDate, $endDate),
            'card_stats' => ImprovedSystemStatsGenerator::getCardDistribution($startDate, $endDate),
            'cumulative_stats' => ImprovedSystemStatsGenerator::getCumulativeStats($startDate, $endDate),
            'report_date' => date('d-M-Y', strtotime('last monday -1 week')) . ' to ' . date('d-M-Y', strtotime('last sunday'))
        ];

        $this->info('Data Collection Done');
        $this->info('Sending Email');
        $this->SendEmail($data);
    }

    private function SendEmail($data)
    {
        try {
            $dateRange = date('d-M-Y', strtotime('last monday -1 week')) . ' to ' . date('d-M-Y', strtotime('last sunday'));

            Mail::send('emails.improved-weekly-stats', $data, function ($message) use ($dateRange) {
                $message->to('EddieMuyeba@techmasters.co.zm')->subject
                ('TechPay weekly report for ' . $dateRange);
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
