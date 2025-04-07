<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\TransactionChargeResource\Pages\ListTransactionCharges;
use App\Models\TransactionCharge;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class TransactionChargeTableStatsWidget extends BaseWidget
{
    protected static ?string $pollingInterval = '10s';
    
    // Make this widget only appear on the TransactionCharge list page
    protected static string $page = ListTransactionCharges::class;
    
    // Prevent this widget from appearing on the dashboard
    public static function canView(): bool
    {
        // Only show this widget on the ListTransactionCharges page
        return request()->routeIs('filament.backend.resources.transaction-charges.index');
    }
    
    protected function getStats(): array
    {
        // Get charge counts and amounts
        $totalCount = TransactionCharge::count();
        $totalAmount = TransactionCharge::sum('calculated_amount');
        $percentageCount = TransactionCharge::where('charge_type', 'PERCENTAGE')->count();
        $fixedCount = TransactionCharge::where('charge_type', 'FIXED')->count();
        $percentageAmount = TransactionCharge::where('charge_type', 'PERCENTAGE')->sum('calculated_amount');
        $fixedAmount = TransactionCharge::where('charge_type', 'FIXED')->sum('calculated_amount');
        
        // Calculate average charge
        $avgCharge = $totalCount > 0 ? $totalAmount / $totalCount : 0;
        
        return [
            Stat::make('Total Charges', number_format($totalCount))
                ->description('ZMW ' . number_format($totalAmount, 2) . ' total value')
                ->descriptionIcon('heroicon-m-currency-dollar')
                ->color('primary'),
                
            Stat::make('Percentage Charges', number_format($percentageCount))
                ->description('ZMW ' . number_format($percentageAmount, 2) . ' value')
                ->descriptionIcon('heroicon-m-chart-pie')
                ->color('info'),
                
            Stat::make('Fixed Charges', number_format($fixedCount))
                ->description('ZMW ' . number_format($fixedAmount, 2) . ' value')
                ->descriptionIcon('heroicon-m-currency-dollar')
                ->color('success'),
                
            Stat::make('Average Charge', 'ZMW ' . number_format($avgCharge, 2))
                ->description(($percentageCount > 0 ? round(($percentageCount / $totalCount) * 100) : 0) . '% are percentage based')
                ->descriptionIcon('heroicon-m-calculator')
                ->color('warning'),
        ];
    }
}
