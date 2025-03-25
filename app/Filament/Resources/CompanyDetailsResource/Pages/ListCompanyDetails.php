<?php

namespace App\Filament\Resources\CompanyDetailsResource\Pages;

use App\Filament\Resources\CompanyDetailsResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCompanyDetails extends ListRecords
{
    protected static string $resource = CompanyDetailsResource::class;

    protected function getActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}