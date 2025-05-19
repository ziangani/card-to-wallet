<?php

namespace App\Filament\Resources\CorporateWalletResource\Pages;

use App\Filament\Resources\CorporateWalletResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCorporateWallets extends ListRecords
{
    protected static string $resource = CorporateWalletResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
