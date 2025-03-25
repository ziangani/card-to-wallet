<?php

namespace App\Filament\Merchant\Resources\PaymentRequestsResource\Pages;

use App\Filament\Merchant\Resources\PaymentRequestsResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPaymentRequests extends EditRecord
{
    protected static string $resource = PaymentRequestsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
