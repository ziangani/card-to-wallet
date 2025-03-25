<?php

namespace App\Filament\Resources\CompanyDetailsResource\Pages;

use App\Filament\Resources\CompanyDetailsResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateCompanyDetails extends CreateRecord
{
    protected static string $resource = CompanyDetailsResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        $companyDetail = static::getModel()::create($data);

        // Only create related records if the data is provided
        if (isset($data['contact'])) {
            $companyDetail->contact()->create($data['contact']);
        }
        
        if (isset($data['ownership'])) {
            $companyDetail->ownership()->create($data['ownership']);
        }

        return $companyDetail;
    }
}
