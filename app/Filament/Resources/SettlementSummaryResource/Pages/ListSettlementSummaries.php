<?php

namespace App\Filament\Resources\SettlementSummaryResource\Pages;

use App\Filament\Resources\SettlementSummaryResource;
use Filament\Actions\Exports\Enums\ExportFormat;
use Filament\Resources\Pages\ListRecords;
use App\Filament\Exports\SettlementSummaryExporter;
use Filament\Tables;

class ListSettlementSummaries extends ListRecords
{
    protected static string $resource = SettlementSummaryResource::class;

    protected function getTableHeaderActions(): array
    {
        return [
            Tables\Actions\ExportAction::make()
                ->exporter(SettlementSummaryExporter::class)
                ->formats([ExportFormat::Csv])
                ->fileName(fn () => 'settlement-summary-' . now()->format('Y-m-d')),
        ];
    }
}
