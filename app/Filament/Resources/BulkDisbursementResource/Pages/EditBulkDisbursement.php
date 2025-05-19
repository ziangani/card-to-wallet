<?php

namespace App\Filament\Resources\BulkDisbursementResource\Pages;

use App\Filament\Resources\BulkDisbursementResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditBulkDisbursement extends EditRecord
{
    protected static string $resource = BulkDisbursementResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
