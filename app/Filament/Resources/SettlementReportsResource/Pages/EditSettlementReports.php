<?php

namespace App\Filament\Resources\SettlementReportsResource\Pages;

use App\Filament\Resources\SettlementReportsResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSettlementReports extends EditRecord
{
    protected static string $resource = SettlementReportsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
