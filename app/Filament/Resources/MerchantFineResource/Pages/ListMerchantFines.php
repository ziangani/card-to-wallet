<?php

namespace App\Filament\Resources\MerchantFineResource\Pages;

use App\Filament\Resources\MerchantFineResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListMerchantFines extends ListRecords
{
    protected static string $resource = MerchantFineResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
