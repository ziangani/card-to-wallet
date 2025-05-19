<?php

namespace App\Filament\Resources\CorporateWalletResource\Pages;

use App\Filament\Resources\CorporateWalletResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCorporateWallet extends EditRecord
{
    protected static string $resource = CorporateWalletResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
