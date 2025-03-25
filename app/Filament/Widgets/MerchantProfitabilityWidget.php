<?php

namespace App\Filament\Widgets;

use App\Models\Merchants;
use App\Models\MerchantPayout;
use App\Models\MerchantFine;
use Carbon\Carbon;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class MerchantProfitabilityWidget extends ChartWidget
{
    protected static ?string $heading = 'Merchant Profitability';
    protected static ?string $pollingInterval = null;
    protected static ?int $sort = 10;
    protected int | string | array $columnSpan = 'full';
    
    protected function getFilters(): ?array
    {
        return [
            'last_30_days' => 'Last 30 Days',
            'last_90_days' => 'Last 90 Days',
            'this_year' => 'This Year',
            'last_year' => 'Last Year',
            'all_time' => 'All Time',
        ];
    }

    protected function getData(): array
    {
        $activeFilter = $this->filter ?? 'last_30_days';
        
        // Define date range based on filter
        $startDate = match ($activeFilter) {
            'last_30_days' => now()->subDays(30),
            'last_90_days' => now()->subDays(90),
            'this_year' => now()->startOfYear(),
            'last_year' => now()->subYear()->startOfYear(),
            'all_time' => Carbon::parse('2000-01-01'), // Far in the past
            default => now()->subDays(30),
        };
        
        $endDate = match ($activeFilter) {
            'last_year' => now()->subYear()->endOfYear(),
            default => now(),
        };
        
        // Get top 10 merchants by transaction volume
        $topMerchants = Merchants::select('code', 'name')
            ->take(10)
            ->get();
        
        $datasets = [];
        $labels = [];
        
        // Revenue data
        $revenueData = [];
        // Payout data
        $payoutData = [];
        // Net profit data
        $profitData = [];
        
        foreach ($topMerchants as $merchant) {
            $labels[] = $merchant->name;
            
            // Calculate profitability for each merchant
            $profitability = $merchant->calculateProfitability($startDate, $endDate);
            
            $revenueData[] = round($profitability['total_revenue'], 2);
            $payoutData[] = round($profitability['total_payouts'], 2);
            $profitData[] = round($profitability['net_profit'], 2);
        }
        
        // Add datasets
        $datasets = [
            [
                'label' => 'Revenue',
                'data' => $revenueData,
                'backgroundColor' => 'rgba(59, 130, 246, 0.5)', // Blue
                'borderColor' => 'rgb(59, 130, 246)',
            ],
            [
                'label' => 'Payouts',
                'data' => $payoutData,
                'backgroundColor' => 'rgba(239, 68, 68, 0.5)', // Red
                'borderColor' => 'rgb(239, 68, 68)',
            ],
            [
                'label' => 'Net Profit',
                'data' => $profitData,
                'backgroundColor' => 'rgba(16, 185, 129, 0.5)', // Green
                'borderColor' => 'rgb(16, 185, 129)',
            ],
        ];
        
        return [
            'datasets' => $datasets,
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
    
    protected function getOptions(): array
    {
        return [
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                    'ticks' => [
                        'precision' => 0,
                    ],
                ],
            ],
            'plugins' => [
                'legend' => [
                    'display' => true,
                ],
                'tooltip' => [
                    'enabled' => true,
                ],
            ],
        ];
    }
}
