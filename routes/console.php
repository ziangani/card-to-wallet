<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

//Schedule::command(\App\Console\Commands\updateAirtelTokens::class)->everyMinute()->runInBackground()->withoutOverlapping();
//Schedule::command(\App\Console\Commands\updateAirtelTxn::class)->everyTenSeconds()->runInBackground()->withoutOverlapping();
//Schedule::command(\App\Console\Commands\updateCgrateTxn::class)->everyTenSeconds()->runInBackground()->withoutOverlapping();
// Schedule::command(\App\Console\Commands\Reporting\DownloadMPGSReport::class)->everyMinute()->runInBackground()->withoutOverlapping();
//Schedule::command('wallet:post-deposits')->everyFiveMinutes()->runInBackground()->withoutOverlapping();

Schedule::command(\App\Console\Commands\PostToMobileWallets::class)->everyMinute()->runInBackground()->withoutOverlapping();
Schedule::command(\App\Console\Commands\ProcessBulkDisbursements::class)->everyMinute()->runInBackground()->withoutOverlapping();
Schedule::command(\App\Console\Commands\sendEmail::class)->everyMinute()->runInBackground()->withoutOverlapping();
Schedule::command(\App\Console\Commands\sendSMS::class)->everyMinute()->runInBackground()->withoutOverlapping();

// Schedule::command(\App\Console\Commands\Reporting\ImportMPGSTransactions::class)->everyMinute()->runInBackground()->withoutOverlapping();
// Schedule::command(\App\Console\Commands\Reporting\GetCybersourceTransactions::class)->everyThirtyMinutes()->runInBackground()->withoutOverlapping();
// Run transaction exception monitoring after all transaction data is synced
// Schedule::command(\App\Console\Commands\Reporting\MonitorSuspiciousTransactions::class)->dailyAt('03:30')->runInBackground()->withoutOverlapping();
// Schedule::command(\App\Console\Commands\Reporting\MonitorFlaggedTransactionsV2::class)->dailyAt('03:30')->runInBackground()->withoutOverlapping();

// Schedule::command(\App\Console\Commands\Reporting\GetImprovedDailyStats::class)->dailyAt('04:00')->runInBackground()->withoutOverlapping();
// Schedule::command(\App\Console\Commands\Reporting\CreateTechpayDailyBatchReport::class)->dailyAt('04:00')->runInBackground()->withoutOverlapping();
// Schedule::command(\App\Console\Commands\Reporting\GetImprovedWeeklyStats::class)->weeklyOn(0, '04:00')->runInBackground()->withoutOverlapping();
// Schedule::command(\App\Console\Commands\Reporting\GetImprovedMonthlyStats::class)->monthlyOn(1, '04:00')->runInBackground()->withoutOverlapping();

// // Update dashboard stats cache every 3 minutes
// Schedule::command(\App\Console\Commands\UpdateStatsCache::class)->everyThreeMinutes()->runInBackground()->withoutOverlapping();

// // Refresh materialized view for transaction stats every hour
// Schedule::command(\App\Console\Commands\RefreshTransactionStats::class)->hourly()->runInBackground()->withoutOverlapping();

// // Run settlement daily
// Schedule::command(\App\Console\Commands\Reporting\DownloadCyberSourceSettlementReport::class)->everyThirtyMinutes()->runInBackground()->withoutOverlapping();

// // Download detailed Cybersource reports daily
// Schedule::command(\App\Console\Commands\Reporting\DownloadCyberSourceDetailedReports::class)
//     ->dailyAt('02:30') // Run after settlement report download
//     ->runInBackground()
//     ->withoutOverlapping();

// // Send Cybersource report emails daily after downloads complete
// Schedule::command('reporting:send-cybersource-emails')
//     ->dailyAt('03:00') // Run after detailed reports download
//     ->runInBackground()
//     ->withoutOverlapping();

// // Send Cybersource settlement summary on Monday and Thursday
// Schedule::command('reporting:send-cybersource-settlement-summary')
//     ->weeklyOn(1, '14:00') // Sunday at 3:30 AM
//     ->weeklyOn(4, '14:00') // Wed at 3:30 AM
//     ->runInBackground()
//     ->withoutOverlapping();

// // Process charges every 5 minutes
// Schedule::command('charges:process')
//     ->everyFiveMinutes()
//     ->runInBackground()
//     ->withoutOverlapping();

// // Generate merchant reconciliation daily
// Schedule::command('reconciliation:generate')
//     ->dailyAt('04:30') // Run after all reports and stats are generated
//     ->runInBackground()
//     ->withoutOverlapping();
