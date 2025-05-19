<?php

namespace App\Filament\Resources\DisbursementItemResource\Pages;

use App\Filament\Resources\DisbursementItemResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListDisbursementItems extends ListRecords
{
    protected static string $resource = DisbursementItemResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
