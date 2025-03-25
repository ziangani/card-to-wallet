<?php

namespace App\Filament\Resources\Backend\AllTransactionsResource\Pages;

use App\Filament\Resources\Backend\AllTransactionsResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListAllTransactions extends ListRecords
{
    protected static string $resource = AllTransactionsResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
