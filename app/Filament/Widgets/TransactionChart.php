<?php

namespace App\Filament\Widgets;

use App\Models\Transaction;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;

class TransactionChart extends ChartWidget
{
    protected static ?string $heading = 'Transaction Trends';

    protected static ?int $sort = 2;

protected int | string | array $columnSpan = 'full';

    protected function getData(): array
    {
        $data = $this->getTransactionData();

        return [
            'datasets' => [
                [
                    'label' => 'Transactions',
                    'data' => $data['counts'],
                    'backgroundColor' => 'rgba(59, 130, 246, 0.5)',
                    'borderColor' => 'rgb(59, 130, 246)',
                ],
                [
                    'label' => 'Value (ZMW)',
                    'data' => $data['amounts'],
                    'backgroundColor' => 'rgba(16, 185, 129, 0.5)',
                    'borderColor' => 'rgb(16, 185, 129)',
                    'yAxisID' => 'y1',
                ],
            ],
            'labels' => $data['labels'],
        ];
    }

    protected function getTransactionData(): array
    {
        $days = 14; // Last 14 days
        $labels = [];
        $counts = [];
        $amounts = [];

        for ($i = $days - 1; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $labels[] = $date->format('M d');

            $dailyTransactions = Transaction::whereDate('created_at', $date)->get();
            $counts[] = $dailyTransactions->count();
            $amounts[] = $dailyTransactions->sum('amount');
        }

        return [
            'labels' => $labels,
            'counts' => $counts,
            'amounts' => $amounts,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }

    protected function getOptions(): array
    {
        return [
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                    'title' => [
                        'display' => true,
                        'text' => 'Transaction Count',
                    ],
                ],
                'y1' => [
                    'beginAtZero' => true,
                    'position' => 'right',
                    'title' => [
                        'display' => true,
                        'text' => 'Transaction Value (ZMW)',
                    ],
                    'grid' => [
                        'drawOnChartArea' => false,
                    ],
                ],
            ],
        ];
    }
}
