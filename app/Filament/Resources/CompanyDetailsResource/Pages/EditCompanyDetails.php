<?php

namespace App\Filament\Resources\CompanyDetailsResource\Pages;

use App\Filament\Resources\CompanyDetailsResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCompanyDetails extends EditRecord
{
    protected static string $resource = CompanyDetailsResource::class;

    protected function getActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function handleRecordUpdate($record, array $data): \Illuminate\Database\Eloquent\Model
    {
        $record->update($data);

        if (isset($data['contact'])) {
            $record->contact()->updateOrCreate(
                ['company_detail_id' => $record->id],
                $data['contact']
            );
        }

        if (isset($data['ownership'])) {
            $record->ownership()->updateOrCreate(
                ['company_detail_id' => $record->id],
                $data['ownership']
            );
        }
        return $record;
    }
}
