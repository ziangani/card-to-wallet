<?php

namespace App\Filament\Merchant\Resources\PaymentRequestsResource\Pages;

use App\Filament\Merchant\Resources\PaymentRequestsResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPaymentRequests extends ListRecords
{
    protected static string $resource = PaymentRequestsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
