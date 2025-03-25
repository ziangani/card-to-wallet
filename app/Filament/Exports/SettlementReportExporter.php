<?php

namespace App\Filament\Exports;

use App\Models\SettlementReports;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class SettlementReportExporter extends Exporter
{
    protected static ?string $model = SettlementReports::class;

    public static function maxRows(): ?int 
    {
        return 0; // This makes the export synchronous
    }

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('merchant')
                ->label('Merchant ID'),
            ExportColumn::make('merchants.name')
                ->label('Merchant'),
            ExportColumn::make('settlement_date')
                ->label('Settlement Date'),
            ExportColumn::make('currency'),
            ExportColumn::make('volume')
                ->label('Sales Volume'),
            ExportColumn::make('value')
                ->label('Sales Value'),
            ExportColumn::make('credit_volume'),
            ExportColumn::make('credit_value'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your settlement report export has completed and ' . number_format($export->successful_rows) . ' ' . str('row')->plural($export->successful_rows) . ' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to export.';
        }

        return $body;
    }
}
