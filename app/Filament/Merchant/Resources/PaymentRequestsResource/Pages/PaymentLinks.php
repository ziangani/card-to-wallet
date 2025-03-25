<?php

namespace App\Filament\Merchant\Resources\PaymentRequestsResource\Pages;

use App\Filament\Merchant\Resources\PaymentRequestsResource;
use Filament\Actions\Action;
use Filament\Resources\Pages\Page;

class PaymentLinks extends Page
{
    protected static string $resource = PaymentRequestsResource::class;

    protected static string $view = 'filament.merchant.resources.payment-requests-resource.pages.payment-links';

    protected function getHeaderActions(): array
    {
        return [
            Action::make('edit')
                ->url(url('/')),
            Action::make('delete')
                ->requiresConfirmation()
                ->action(function ($record) {
//                    $record->delete();
                }),
        ];
    }
}
