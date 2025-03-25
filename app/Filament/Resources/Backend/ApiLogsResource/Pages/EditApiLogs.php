<?php

namespace App\Filament\Resources\Backend\ApiLogsResource\Pages;

use App\Filament\Resources\Backend\ApiLogsResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditApiLogs extends EditRecord
{
    protected static string $resource = ApiLogsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
