<?php

namespace App\Filament\Resources\ChargesResource\Pages;

use App\Filament\Resources\ChargesResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCharges extends ListRecords
{
    protected static string $resource = ChargesResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
