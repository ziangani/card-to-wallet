<?php

namespace App\Filament\Resources\ChargesResource\Pages;

use App\Filament\Resources\ChargesResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCharges extends EditRecord
{
    protected static string $resource = ChargesResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
