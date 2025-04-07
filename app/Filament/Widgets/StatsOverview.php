<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        // Today's stats
        $todayTransactions = \App\Models\Transaction::whereDate('created_at', today())->count();
        $todayVolume = \App\Models\Transaction::whereDate('created_at', today())->sum('amount');
        $todayFees = \App\Models\Transaction::whereDate('created_at', today())->sum('fee_amount');

        // Total stats
        $totalTransactions = \App\Models\Transaction::count();
        $totalVolume = \App\Models\Transaction::sum('amount');
        $totalFees = \App\Models\Transaction::sum('fee_amount');

        // Success rate calculation
        $total = \App\Models\Transaction::whereDate('created_at', today())->count();
        $COMPLETED = \App\Models\Transaction::whereDate('created_at', today())->where('status', 'COMPLETED')->count();
        $successRate = $total > 0 ? round(($COMPLETED / $total) * 100) : 0;

        // User and beneficiary count
        $userCount = \App\Models\User::count();
        $beneficiaryCount = \App\Models\Beneficiary::count();

        return [
            Stat::make('Today\'s Transactions', $todayTransactions)
                ->description('Total transactions processed today')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('primary'),

            Stat::make('Today\'s Volume', 'ZMW ' . number_format($todayVolume, 2))
                ->description('Total amount processed today')
                ->descriptionIcon('heroicon-m-currency-dollar')
                ->color('success'),

            Stat::make('Today\'s Fees', 'ZMW ' . number_format($todayFees, 2))
                ->description('Total fees collected today')
                ->descriptionIcon('heroicon-m-receipt-percent')
                ->color('warning'),

            Stat::make('Success Rate', $successRate . '%')
                ->description('Transaction success rate today')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('info'),

            Stat::make('Total Transactions', $totalTransactions)
                ->description('All-time transaction count')
                ->descriptionIcon('heroicon-m-document-text')
                ->color('primary'),

            Stat::make('Total Volume', 'ZMW ' . number_format($totalVolume, 2))
                ->description('All-time processing volume')
                ->descriptionIcon('heroicon-m-chart-bar')
                ->color('success'),

            Stat::make('Total Fees', 'ZMW ' . number_format($totalFees, 2))
                ->description('All-time fees collected')
                ->descriptionIcon('heroicon-m-calculator')
                ->color('warning'),

            Stat::make('Registered Users', $userCount)
                ->description('Total user accounts')
                ->descriptionIcon('heroicon-m-users')
                ->color('info'),

            Stat::make('Saved Beneficiaries', $beneficiaryCount)
                ->description('Total saved recipients')
                ->descriptionIcon('heroicon-m-user-group')
                ->color('primary'),
        ];
    }
}
