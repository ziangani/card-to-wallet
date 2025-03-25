<?php

namespace App\Filament\Resources\RollingReserveReturnResource\Pages;

use App\Filament\Resources\RollingReserveReturnResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListRollingReserveReturns extends ListRecords
{
    protected static string $resource = RollingReserveReturnResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('refresh')
                ->label('Refresh Data')
                ->icon('heroicon-o-arrow-path')
                ->action(fn () => $this->refreshData())
        ];
    }
    
    protected function refreshData(): void
    {
        $this->resetTable();
        $this->notify('success', 'Data refreshed successfully');
    }
}
