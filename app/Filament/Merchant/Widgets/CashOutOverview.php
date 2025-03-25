<?php

namespace App\Filament\Merchant\Widgets;

use App\Models\Transactions;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;

class CashOutOverview extends BaseWidget
{
    protected function getStats(): array
    {
        // Get merchant ID from authenticated user
        $merchantId = Auth::user()->merchant_id;
        
        // Get available balance and count
        $availableBalance = Transactions::getAvailableBalance($merchantId);
        $availableCount = Transactions::getAvailableTransactionsCount($merchantId);
        
        return [
            Stat::make('Available Balance', 'ZMW ' . number_format($availableBalance, 2))
                ->description($availableCount . ' transactions available for cashout')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success'),

            Stat::make('Initiated Cashout', 'ZMW ' . number_format(Transactions::getInitiatedCashoutAmount($merchantId), 2))
                ->description(Transactions::getInitiatedTransactionsCount($merchantId) . ' transactions initiated for cashout')
                ->descriptionIcon('heroicon-m-arrow-trending-down')
                ->color('warning'),
                
            Stat::make('Actual Balance', 'ZMW ' . number_format(Transactions::getActualBalance($merchantId), 2))
                ->description('Total balance after charges')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('primary'),
        ];
    }
}
