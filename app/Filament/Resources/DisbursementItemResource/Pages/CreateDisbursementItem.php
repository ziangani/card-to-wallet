<?php

namespace App\Filament\Resources\DisbursementItemResource\Pages;

use App\Filament\Resources\DisbursementItemResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateDisbursementItem extends CreateRecord
{
    protected static string $resource = DisbursementItemResource::class;
    
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
