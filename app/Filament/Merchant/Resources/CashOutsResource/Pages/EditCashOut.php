<?php

namespace App\Filament\Merchant\Resources\CashOutsResource\Pages;

use App\Filament\Merchant\Resources\CashOutsResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCashOut extends EditRecord
{
    protected static string $resource = CashOutsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
