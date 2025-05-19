<?php

namespace App\Filament\Resources\BulkDisbursementResource\Pages;

use App\Filament\Resources\BulkDisbursementResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListBulkDisbursements extends ListRecords
{
    protected static string $resource = BulkDisbursementResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
