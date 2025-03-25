<?php

namespace App\Filament\Resources\Backend\MerchantsResource\Pages;

use App\Filament\Resources\Backend\MerchantsResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListMerchants extends ListRecords
{
    protected static string $resource = MerchantsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
