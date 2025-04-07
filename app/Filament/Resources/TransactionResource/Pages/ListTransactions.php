<?php

namespace App\Filament\Resources\TransactionResource\Pages;

use App\Filament\Resources\TransactionResource;
use App\Filament\Widgets\TransactionTableStatsWidget;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTransactions extends ListRecords
{
    protected static string $resource = TransactionResource::class;

    protected function getHeaderWidgets(): array
    {
        return [
            TransactionTableStatsWidget::class,
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            // No create action needed as per requirements
        ];
    }
}
