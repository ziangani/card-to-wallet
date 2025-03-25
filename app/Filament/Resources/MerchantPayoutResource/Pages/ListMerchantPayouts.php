<?php

namespace App\Filament\Resources\MerchantPayoutResource\Pages;

use App\Filament\Resources\MerchantPayoutResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListMerchantPayouts extends ListRecords
{
    protected static string $resource = MerchantPayoutResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
