<?php

namespace App\Filament\Resources\Backend\OnboardingApplicationsResource\Pages;

use App\Filament\Resources\Backend\OnboardingApplicationsResource;
use App\Models\OnboardingApplications;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class EditOnboardingApplications extends EditRecord
{
    protected static string $resource = OnboardingApplicationsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        // Save the main record first
        $record->update($data);

        // Save related models if they exist in the form data
        if (isset($data['company'])) {
            $record->company()->update($data['company']);
        }

        if (isset($data['owners'])) {
            // Delete existing owners and create new ones
            $record->owners()->delete();
            foreach ($data['owners'] as $ownerData) {
                $ownerData['company_id'] = $record->company_id;
                $record->owners()->create($ownerData);
            }
        }

        if (isset($data['contact'])) {
            if ($record->contact) {
                $record->contact()->update($data['contact']);
            } else {
                $data['contact']['company_id'] = $record->company_id;
                $record->contact()->create($data['contact']);
            }
        }

        if (isset($data['bank'])) {
            if ($record->bank) {
                $record->bank()->update($data['bank']);
            } else {
                $data['bank']['company_id'] = $record->company_id;
                $record->bank()->create($data['bank']);
            }
        }

        if (isset($data['website'])) {
            if ($record->website) {
                $record->website()->update($data['website']);
            } else {
                $data['website']['company_id'] = $record->company_id;
                $record->website()->create($data['website']);
            }
        }

        Notification::make()
            ->title('Application updated successfully')
            ->success()
            ->send();

        return $record;
    }

    protected function authorizeAccess(): void
    {
        $record = $this->getRecord();

        abort_unless(
            $record->approval_level === 0 && $record->status !== 'APPROVED',
            403
        );
    }
}
