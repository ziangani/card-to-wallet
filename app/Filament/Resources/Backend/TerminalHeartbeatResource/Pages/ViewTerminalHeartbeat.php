<?php

namespace App\Filament\Resources\Backend\TerminalHeartbeatResource\Pages;

use App\Filament\Resources\Backend\TerminalHeartbeatResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewTerminalHeartbeat extends ViewRecord
{
    protected static string $resource = TerminalHeartbeatResource::class;

    protected function getActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
