<?php

namespace App\Filament\Merchant\Resources\PaymentRequestsResource\Pages;

use App\Filament\Merchant\Resources\PaymentRequestsResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreatePaymentRequests extends CreateRecord
{
    protected static string $resource = PaymentRequestsResource::class;
}
