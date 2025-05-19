<?php

namespace App\Filament\Resources\DisbursementItemResource\Pages;

use App\Filament\Resources\DisbursementItemResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewDisbursementItem extends ViewRecord
{
    protected static string $resource = DisbursementItemResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make()
                ->visible(fn ($record) => $record->status === 'pending'),
        ];
    }
}
