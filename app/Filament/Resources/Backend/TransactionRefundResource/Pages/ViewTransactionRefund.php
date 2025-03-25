<?php

namespace App\Filament\Resources\Backend\TransactionRefundResource\Pages;

use App\Filament\Resources\Backend\TransactionRefundResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewTransactionRefund extends ViewRecord
{
    protected static string $resource = TransactionRefundResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // No edit action as refunds should not be edited
        ];
    }
}
