<?php

namespace App\Filament\Resources\ChargebackResource\Pages;

use App\Filament\Resources\ChargebackResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListChargebacks extends ListRecords
{
    protected static string $resource = ChargebackResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
