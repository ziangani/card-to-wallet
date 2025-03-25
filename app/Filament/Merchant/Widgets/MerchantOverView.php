<?php

namespace App\Filament\Merchant\Widgets;

use App\Models\Merchants;
use App\Models\OnboardingApplications;
use App\Models\Transactions;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;

class MerchantOverView extends BaseWidget
{
    protected ?string $heading = 'Merchant Overview';

    protected function getStats(): array
    {
        $startDate = date('Y-m-d') . ' 00:00:00';
        $endDate = date('Y-m-d') . ' 23:59:59';


        return [

            Stat::make('Transaction Volume', number_format(Transactions::where('status', 'COMPLETE')->where('provider_push_status', 'SUCCESS')->where('created_at', '>=', $startDate)->where('created_at', '<=', $endDate)->count()))
                ->description('Successful Today')
                ->color('success'),

            Stat::make('Transaction Value',  number_format(Transactions::where('status', 'COMPLETE')->where('provider_push_status', 'SUCCESS')->where('created_at', '>=', $startDate)->where('created_at', '<=', $endDate)->sum('amount')), 2)
                ->description('Successful Today')
                ->color('success'),

            Stat::make('Transaction Volume', number_format(Transactions::where('status', 'FAILED')->where('provider_push_status', 'SUCCESS')->where('created_at', '>=', $startDate)->where('created_at', '<=', $endDate)->count()))
                ->description('Failed Today')
                ->color('danger'),
        ];
    }
}
