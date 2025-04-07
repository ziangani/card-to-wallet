<?php

namespace App\Filament\Resources\TransactionChargeResource\Pages;

use App\Filament\Resources\TransactionChargeResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewTransactionCharge extends ViewRecord
{
    protected static string $resource = TransactionChargeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // No edit action needed as per requirements
        ];
    }
}
