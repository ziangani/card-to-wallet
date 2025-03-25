<?php

namespace App\Filament\Resources\Backend\OnboardingApplicationsResource\Pages;

use App\Filament\Resources\Backend\OnboardingApplicationsResource;
use Filament\Actions;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;

class ListOnboardingApplications extends ListRecords
{
    protected static string $resource = OnboardingApplicationsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    public function getTabs(): array
    {
        return [
            null => Tab::make('All'),
            'Pending Approval' => Tab::make()->query(fn ($query) => $query->where('status', 'PENDING')),
            'Approved' => Tab::make()->query(fn ($query) => $query->where('status', 'APPROVED')),
            'Rejected' => Tab::make()->query(fn ($query) => $query->where('status', 'REJECTED')),
        ];
    }
}
