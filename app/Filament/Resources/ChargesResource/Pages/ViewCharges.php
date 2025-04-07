<?php

namespace App\Filament\Resources\ChargesResource\Pages;

use App\Filament\Resources\ChargesResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewCharges extends ViewRecord
{
    protected static string $resource = ChargesResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
