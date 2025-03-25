<?php

namespace App\Filament\Resources\Backend\TerminalsResource\Pages;

use App\Filament\Resources\Backend\TerminalsResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTerminals extends ListRecords
{
    protected static string $resource = TerminalsResource::class;

    protected function getActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
