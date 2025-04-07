<?php

namespace App\Filament\Resources\ChargesResource\Pages;

use App\Filament\Resources\ChargesResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateCharges extends CreateRecord
{
    protected static string $resource = ChargesResource::class;
    
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
