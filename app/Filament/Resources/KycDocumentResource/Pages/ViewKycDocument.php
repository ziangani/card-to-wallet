<?php

namespace App\Filament\Resources\KycDocumentResource\Pages;

use App\Filament\Resources\KycDocumentResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewKycDocument extends ViewRecord
{
    protected static string $resource = KycDocumentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
