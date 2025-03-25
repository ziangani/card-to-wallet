<?php

namespace App\Filament\Resources\Backend\PaymentProvidersResource\Pages;

use App\Filament\Resources\Backend\PaymentProvidersResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPaymentProviders extends EditRecord
{
    protected static string $resource = PaymentProvidersResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
