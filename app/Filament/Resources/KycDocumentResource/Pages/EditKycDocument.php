<?php

namespace App\Filament\Resources\KycDocumentResource\Pages;

use App\Filament\Resources\KycDocumentResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditKycDocument extends EditRecord
{
    protected static string $resource = KycDocumentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
        ];
    }
    
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('view', ['record' => $this->getRecord()]);
    }
}
