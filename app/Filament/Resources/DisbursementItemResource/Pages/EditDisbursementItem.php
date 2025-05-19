<?php

namespace App\Filament\Resources\DisbursementItemResource\Pages;

use App\Filament\Resources\DisbursementItemResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditDisbursementItem extends EditRecord
{
    protected static string $resource = DisbursementItemResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
            Actions\ViewAction::make(),
        ];
    }
    
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
