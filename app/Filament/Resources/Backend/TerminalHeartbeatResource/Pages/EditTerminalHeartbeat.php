<?php

namespace App\Filament\Resources\Backend\TerminalHeartbeatResource\Pages;

use App\Filament\Resources\Backend\TerminalHeartbeatResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTerminalHeartbeat extends EditRecord
{
    protected static string $resource = TerminalHeartbeatResource::class;

    protected function getActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
