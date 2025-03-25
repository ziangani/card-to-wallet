<?php

namespace App\Filament\Resources\SettlementReportsResource\Pages;

use App\Filament\Resources\SettlementReportsResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSettlementReports extends ListRecords
{
    protected static string $resource = SettlementReportsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
