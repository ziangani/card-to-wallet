<?php

namespace App\Console\Commands\Reporting;

use App\Common\ImprovedSystemStatsGenerator;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class GetImprovedMonthlyStats extends Command
{
    protected $signature = 'reporting:improved-monthly-stats';
    protected $description = 'Get improved monthly transaction stats with detailed platform analysis';

    public function handle()
    {
        ini_set('max_execution_time', 0);
        ini_set('memory_limit', '2048M');

        $this->info('Collecting Data...');
        // Get previous month's date range
        // We run this on first day of new month, so we look at the complete previous month
        $startDate = date('Y-m-01 00:00:00', strtotime('first day of last month'));
        $endDate = date('Y-m-t 23:59:59', strtotime('last day of last month'));

        $data = [
            'settlement_stats' => ImprovedSystemStatsGenerator::getWeeklySettlementStats($startDate, $endDate), // reusing weekly method as it works for any date range
            'summary' => ImprovedSystemStatsGenerator::getSummaryBySource($startDate, $endDate),
            'top_merchants' => ImprovedSystemStatsGenerator::getTopMerchants($startDate, $endDate),
            'card_stats' => ImprovedSystemStatsGenerator::getCardDistribution($startDate, $endDate),
            'cumulative_stats' => ImprovedSystemStatsGenerator::getCumulativeStats($startDate, $endDate),
            'report_date' => date('d-M-Y', strtotime('first day of last month')) . ' to ' . date('d-M-Y', strtotime('last day of last month'))
        ];

        $this->info('Data Collection Done');
        $this->info('Sending Email');
        $this->SendEmail($data);
    }

    private function SendEmail($data)
    {
        try {
            $dateRange = date('d-M-Y', strtotime('first day of last month')) . ' to ' . date('d-M-Y', strtotime('last day of last month'));

            Mail::send('emails.improved-monthly-stats', $data, function ($message) use ($dateRange) {
                $message->to('EddieMuyeba@techmasters.co.zm')->subject
                ('TechPay monthly report for ' . $dateRange);
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
