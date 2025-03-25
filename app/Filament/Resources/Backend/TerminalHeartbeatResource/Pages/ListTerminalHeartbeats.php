<?php

namespace App\Filament\Resources\Backend\TerminalHeartbeatResource\Pages;

use App\Filament\Resources\Backend\TerminalHeartbeatResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTerminalHeartbeats extends ListRecords
{
    protected static string $resource = TerminalHeartbeatResource::class;

    protected function getActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
