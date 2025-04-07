<?php

namespace App\Filament\Resources\TransactionChargeResource\Pages;

use App\Filament\Resources\TransactionChargeResource;
use App\Filament\Widgets\TransactionChargeTableStatsWidget;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTransactionCharges extends ListRecords
{
    protected static string $resource = TransactionChargeResource::class;

    protected function getHeaderWidgets(): array
    {
        return [
            TransactionChargeTableStatsWidget::class,
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            // No create action needed as per requirements
        ];
    }
}
