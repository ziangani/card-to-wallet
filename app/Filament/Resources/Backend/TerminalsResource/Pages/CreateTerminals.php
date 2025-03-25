<?php

namespace App\Filament\Resources\Backend\TerminalsResource\Pages;

use App\Filament\Resources\Backend\TerminalsResource;
use App\Models\Terminals;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Str;

class CreateTerminals extends CreateRecord
{
    protected static string $resource = TerminalsResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['terminal_id'] = Terminals::generateTerminalId();
        $data['activation_code'] = Str::random(16);

        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
