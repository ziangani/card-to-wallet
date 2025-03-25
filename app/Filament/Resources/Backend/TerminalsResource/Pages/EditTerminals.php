<?php

namespace App\Filament\Resources\Backend\TerminalsResource\Pages;

use App\Filament\Resources\Backend\TerminalsResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTerminals extends EditRecord
{
    protected static string $resource = TerminalsResource::class;

    protected function getActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
