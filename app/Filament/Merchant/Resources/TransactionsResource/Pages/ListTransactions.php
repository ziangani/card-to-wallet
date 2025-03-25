<?php

namespace App\Filament\Merchant\Resources\TransactionsResource\Pages;

use App\Filament\Merchant\Resources\TransactionsResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTransactions extends ListRecords
{
    protected static string $resource = TransactionsResource::class;

    protected function getHeaderActions(): array
    {
        return [
//            Actions\CreateAction::make(),
        ];
    }
}
