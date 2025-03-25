<?php

namespace App\Filament\Resources\Backend\TransactionRefundResource\Pages;

use App\Filament\Resources\Backend\TransactionRefundResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTransactionRefunds extends ListRecords
{
    protected static string $resource = TransactionRefundResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // No create action as refunds should only be created from transactions
        ];
    }
}
