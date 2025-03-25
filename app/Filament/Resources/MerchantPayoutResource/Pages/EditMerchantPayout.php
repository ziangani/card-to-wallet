<?php

namespace App\Filament\Resources\MerchantPayoutResource\Pages;

use App\Filament\Resources\MerchantPayoutResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditMerchantPayout extends EditRecord
{
    protected static string $resource = MerchantPayoutResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
