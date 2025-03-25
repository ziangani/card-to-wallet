<?php

namespace App\Filament\Widgets;

use App\Models\MerchantPayout;
use App\Models\MerchantFine;
use App\Models\MerchantReconciliation;
use App\Models\Merchants;
use Carbon\Carbon;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class MerchantFinanceStatsWidget extends BaseWidget
{
    protected static ?int $sort = 5;
    protected int | string | array $columnSpan = 'full';
    protected static ?string $pollingInterval = null;

    protected function getStats(): array
    {
        // Get total pending payouts
        $pendingPayouts = MerchantPayout::where('status', 'PENDING')->sum('amount');
        $pendingPayoutsCount = MerchantPayout::where('status', 'PENDING')->count();
        
        // Get total completed payouts in the last 30 days
        $recentPayouts = MerchantPayout::where('status', 'COMPLETED')
            ->where('completed_at', '>=', now()->subDays(30))
            ->sum('amount');
        $recentPayoutsCount = MerchantPayout::where('status', 'COMPLETED')
            ->where('completed_at', '>=', now()->subDays(30))
            ->count();
            
        // Get total pending rolling reserve returns
        $pendingRollingReserves = MerchantPayout::where('status', 'PENDING')
            ->where('type', 'ROLLING_RESERVE_RETURN')
            ->sum('amount');
        $pendingRollingReservesCount = MerchantPayout::where('status', 'PENDING')
            ->where('type', 'ROLLING_RESERVE_RETURN')
            ->count();
            
        // Get total pending fines
        $pendingFines = MerchantFine::where('status', 'PENDING')->sum('amount');
        $pendingFinesCount = MerchantFine::where('status', 'PENDING')->count();
        
        // Format numbers
        $formatNumber = fn ($number) => number_format($number, 2);
        
        return [
            Stat::make('Pending Payouts', $formatNumber($pendingPayouts))
                ->description("{$pendingPayoutsCount} payouts awaiting processing")
                ->descriptionIcon('heroicon-m-clock')
                ->color('warning'),
                
            Stat::make('Recent Payouts (30 days)', $formatNumber($recentPayouts))
                ->description("{$recentPayoutsCount} payouts completed")
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success'),
                
            Stat::make('Pending Rolling Reserves', $formatNumber($pendingRollingReserves))
                ->description("{$pendingRollingReservesCount} reserve returns pending")
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('info'),
                
            Stat::make('Pending Fines', $formatNumber($pendingFines))
                ->description("{$pendingFinesCount} fines awaiting resolution")
                ->descriptionIcon('heroicon-m-exclamation-triangle')
                ->color('danger'),
        ];
    }
}
