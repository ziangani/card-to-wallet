<?php

namespace App\Filament\Resources\Backend\PaymentRequestsResource\Pages;

use App\Filament\Resources\Backend\PaymentRequestsResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPaymentRequests extends EditRecord
{
    protected static string $resource = PaymentRequestsResource::class;

    protected function getHeaderActions(): array
    {
        return [
//            Actions\DeleteAction::make(),
        ];
    }
}
