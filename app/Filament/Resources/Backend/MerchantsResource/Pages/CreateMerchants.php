<?php

namespace App\Filament\Resources\Backend\MerchantsResource\Pages;

use App\Common\MerchantServices;
use App\Filament\Resources\Backend\MerchantsResource;
use Filament\Resources\Pages\CreateRecord;

class CreateMerchants extends CreateRecord
{
    protected static string $resource = MerchantsResource::class;

    public function mutateFormDataBeforeCreate(array $data): array
    {
        $data['created_by'] = auth()->id();
        return $data;
    }

    protected function getCreatedNotificationMessage(): ?string
    {
        return 'Merchant successfully created.';
    }

    protected function afterCreate(): void
    {
//        MerchantServices::createMerchantApiKeys($this->record);
    }
}
