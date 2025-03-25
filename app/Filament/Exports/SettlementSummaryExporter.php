<?php

namespace App\Filament\Exports;

use App\Models\SettlementSummary;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class SettlementSummaryExporter extends Exporter
{
    protected static ?string $model = SettlementSummary::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('merchant')
                ->label('Merchant ID'),
            ExportColumn::make('merchant_name')
                ->label('Merchant Name'),
            ExportColumn::make('currency'),
            ExportColumn::make('debit_value')
                ->label('Debit Value'),
            ExportColumn::make('credit_value')
                ->label('Credit Value'),
            ExportColumn::make('net_settlement')
                ->label('Net Settlement'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        return 'Your settlement summary export has been completed and is ready to download.';
    }
}
