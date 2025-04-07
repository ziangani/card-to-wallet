<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\TransactionResource\Pages\ListTransactions;
use App\Models\Transaction;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class TransactionTableStatsWidget extends BaseWidget
{
    protected static ?string $pollingInterval = '10s';
    
    // Make this widget only appear on the Transaction list page
    protected static string $page = ListTransactions::class;
    
    // Prevent this widget from appearing on the dashboard
    public static function canView(): bool
    {
        // Only show this widget on the ListTransactions page
        return request()->routeIs('filament.backend.resources.transactions.index');
    }
    
    protected function getStats(): array
    {
        // Get transaction counts by status
        $totalCount = Transaction::count();
        $totalAmount = Transaction::sum('total_amount');
        $feeAmount = Transaction::sum('fee_amount');
        $completedCount = Transaction::where('status', 'COMPLETED')->count();
        $pendingCount = Transaction::where('status', 'PENDING')->count();
        $failedCount = Transaction::where('status', 'FAILED')->count();
        
        // Calculate success rate
        $successRate = $totalCount > 0 ? round(($completedCount / $totalCount) * 100) : 0;
        
        // Calculate average transaction value
        $avgValue = $totalCount > 0 ? $totalAmount / $totalCount : 0;
        
        return [
            Stat::make('Total Transactions', number_format($totalCount))
                ->description('ZMW ' . number_format($totalAmount, 2) . ' total value')
                ->descriptionIcon('heroicon-m-currency-dollar')
                ->color('primary'),
                
            Stat::make('Completed', number_format($completedCount))
                ->description('Success rate: ' . $successRate . '%')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success'),
                
            Stat::make('Pending', number_format($pendingCount))
                ->description('Awaiting processing')
                ->descriptionIcon('heroicon-m-clock')
                ->color('warning'),
                
            Stat::make('Fees Collected', 'ZMW ' . number_format($feeAmount, 2))
                ->description('Avg. transaction: ZMW ' . number_format($avgValue, 2))
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('info'),
        ];
    }
}
