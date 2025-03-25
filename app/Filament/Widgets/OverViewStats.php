<?php

namespace App\Filament\Widgets;

use App\Common\SystemStatsGenerator;
use App\Models\CacheRecord;
use App\Models\Merchants;
use App\Models\OnboardingApplications;
use App\Models\Transactions;
use App\Models\SettlementReports;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class OverViewStats extends BaseWidget
{
    // Use Filament's defer loading
    protected static bool $isLazy = true;

    protected int | string | array $columnSpan = 'full';

    protected function getStats(): array
    {
        $startDate = date('Y-m-d') . ' 00:00:00';
        $endDate = date('Y-m-d') . ' 23:59:59';

        // Get today's stats from cache
        $today_result = CacheRecord::getWithTimestamp('dashboard_stats_today');
        $today_summary = $today_result['data'] ?? SystemStatsGenerator::getSummary($startDate, $endDate);
        $today_updated = $today_result['updated_at'] ? Carbon::parse($today_result['updated_at'])->diffForHumans() : 'Never';

        $overall_summary = SystemStatsGenerator::getSummary('2021-01-01 00:00:00', $endDate);

        // Get settlement stats for today
        $today_settlements = SettlementReports::where('settlement_date', date('Y-m-d'))
            ->selectRaw('SUM(volume) as total_volume, SUM(value) as total_value')
            ->first();

        // Get overall settlement stats
        $overall_settlements = SettlementReports::selectRaw('SUM(volume) as total_volume, SUM(value) as total_value')
            ->first();

        $stats = [];

        if (!in_array(Auth::user()->user_type, ['DATA_ENTRY', 'MERCHANT'])) {
            $stats = [
                Stat::make('Payments Value Today', '$' . $today_summary[0]['payments']['value']['success'], 0)
                    ->description('Volume: ' . number_format($today_summary[0]['payments']['volume']['success'], 0))
                    ->color('success'),

                Stat::make('Authorizations Value Today', '$' . $today_summary[0]['authorizations']['value']['success'])
                    ->description('Volume: ' . number_format($today_summary[0]['authorizations']['volume']['success'] ,0))
                    ->color('success'),

                Stat::make('Settlements Value Today', '$' . number_format($today_settlements->total_value ?? 0, 2))
                    ->description('Volume: ' . number_format($today_settlements->total_volume ?? 0, 0))
                    ->color('success'),

                Stat::make('Payments Value Overall', '$' . $overall_summary[0]['payments']['value']['success'])
                    ->description('Volume: ' . number_format($overall_summary[0]['payments']['volume']['success'], 0))
                    ->color('info'),

                Stat::make('Authorizations Value Overall', '$' . $overall_summary[0]['authorizations']['value']['success'])
                    ->description('Volume: ' . number_format($overall_summary[0]['authorizations']['volume']['success'], 0))
                    ->color('info'),

                Stat::make('Settlements Value Overall', '$' . number_format($overall_settlements->total_value ?? 0, 2))
                    ->description('Volume: ' . number_format($overall_settlements->total_volume ?? 0, 0))
                    ->color('info'),
            ];
        }

        $nonFinancialStats = [
            Stat::make('Total Merchants', Merchants::count())
                ->color('info')
                ->description('All Merchants')
                ->icon('heroicon-o-building-office-2'),

            Stat::make('Merchants', Merchants::where('status', Merchants::STATUS_ACTIVE)->count())
                ->color('success')
                ->description('Active')
                ->icon('heroicon-o-building-office-2'),

            Stat::make('Merchants', Merchants::where('status', Merchants::STATUS_DISABLED)->count())
                ->color('danger')
                ->description('Disabled')
                ->icon('heroicon-o-building-office-2'),

            Stat::make('Stats Last Updated', $today_updated)
                ->color('info')
                ->icon('heroicon-o-clock'),

           Stat::make('Onboarding Applications', OnboardingApplications::where('status', 'APPROVED')->count())
               ->color('success')
               ->description('Approved')
               ->icon('heroicon-o-document-text'),

           Stat::make('Onboarding Applications', OnboardingApplications::count())
               ->color('info')
               ->description('Since Inception')
               ->icon('heroicon-o-document-text'),

           Stat::make('Onboarding Applications', OnboardingApplications::where('status', 'PENDING')->count())
               ->color('warning')
               ->description('Awaiting Approval')
               ->icon('heroicon-o-clock'),

           Stat::make('Onboarding Applications', OnboardingApplications::where('status', 'REJECTED')->count())
               ->color('danger')
               ->description('Rejected')
               ->icon('heroicon-o-x-circle'),

//            Stat::make('Transaction Volume', number_format(Transactions::where('status', 'COMPLETE')->where('provider_push_status', 'SUCCESS')->where('created_at', '>=', $startDate)->where('created_at', '<=', $endDate)->count()))
//                ->color('danger')
//                ->description('Successful Today')
//                ->color('success'),
//
           Stat::make('Transaction Value', 'K' . number_format(Transactions::where('status', 'COMPLETE')->where('provider_push_status', 'SUCCESS')->where('created_at', '>=', $startDate)->where('created_at', '<=', $endDate)->sum('amount')), 2)
               ->description('Successful Local Traffic Today')
               ->color('success'),
//
//            Stat::make('Transaction Volume', number_format(Transactions::where('status', 'FAILED')->where('provider_push_status', 'SUCCESS')->where('created_at', '>=', $startDate)->where('created_at', '<=', $endDate)->count()))
//                ->description('Failed Today')
//                ->color('danger'),
//
//            Stat::make('Transaction Value', 'K' . number_format(Transactions::where('status', 'FAILED')->where('provider_push_status', 'SUCCESS')->where('created_at', '>=', $startDate)->where('created_at', '<=', $endDate)->sum('amount')), 2)
//                ->description('Failed Today')
//                ->color('danger'),
//
//            Stat::make('Transaction Value', 'K' . number_format(Transactions::where('status', 'PENDING')->where('provider_push_status', 'SUCCESS')->where('created_at', '>=', $startDate)->where('created_at', '<=', $endDate)->sum('amount')))
//                ->description('Pending Today')
//                ->color('info'),
//
//            Stat::make('Transaction Volume', number_format(Transactions::where('status', 'PENDING')->where('provider_push_status', 'SUCCESS')->where('created_at', '>=', $startDate)->where('created_at', '<=', $endDate)->count()))
//                ->description('Pending Today')
//                ->color('info'),
//
//            //Overall
//
//            Stat::make('To-date Volume', number_format(Transactions::where('status', 'COMPLETE')->where('provider_push_status', 'SUCCESS')->count()))
//                ->description('Successful')
//                ->color('success'),
//
//            Stat::make('To-date Txn Value', 'K' . number_format(Transactions::where('status', 'COMPLETE')->where('provider_push_status', 'SUCCESS')->sum('amount'), 2))
//                ->description('Successful')
//                ->color('success'),
//
//            Stat::make('To-date Txn Volume', number_format(Transactions::where('status', 'FAILED')->where('provider_push_status', 'SUCCESS')->count()))
//                ->description('Failed')
//                ->color('danger'),
//
//            Stat::make('To-date Txn Value', 'K' . number_format(Transactions::where('status', 'FAILED')->sum('amount'), 2))
//                ->description('Failed')
//                ->color('danger'),
//
//            Stat::make('To-date Txn Volume', number_format(Transactions::where('status', 'PENDING')->where('provider_push_status', 'SUCCESS')->count()))
//                ->description('Pending')
//                ->color('info'),
//
//            Stat::make('To-date Txn Value', 'K' . number_format(Transactions::where('status', 'PENDING')->where('provider_push_status', 'SUCCESS')->sum('amount'), 2))
//                ->description('Pending')
//                ->color('info'),
        ];

        return array_merge($stats, $nonFinancialStats);
    }

    protected function getRefreshInterval(): ?string
    {
        // Refresh every 3 minutes to match the cache update interval
        return '3m';
    }
}
