<?php

namespace App\Filament\Resources\Backend\PaymentRequestsResource\Pages;

use App\Filament\Resources\Backend\PaymentRequestsResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPaymentRequests extends ListRecords
{
    protected static string $resource = PaymentRequestsResource::class;

    protected function getHeaderActions(): array
    {
        return [
//            Actions\CreateAction::make(),
        ];
    }
}
