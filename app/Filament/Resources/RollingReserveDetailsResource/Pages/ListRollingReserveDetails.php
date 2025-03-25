<?php

namespace App\Filament\Resources\RollingReserveDetailsResource\Pages;

use App\Filament\Resources\RollingReserveDetailsResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Route;

class ListRollingReserveDetails extends ListRecords
{
    protected static string $resource = RollingReserveDetailsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('back_to_summary')
                ->label('Back to Summary')
                ->url(route('filament.backend.resources.rolling-reserve-returns.index'))
                ->color('secondary')
                ->icon('heroicon-o-arrow-left'),
            Actions\Action::make('refresh')
                ->label('Refresh Data')
                ->icon('heroicon-o-arrow-path')
                ->action(fn () => $this->refreshData()),
        ];
    }
    
    protected function refreshData(): void
    {
        $this->resetTable();
        $this->notify('success', 'Data refreshed successfully');
    }
    
    public function getTitle(): string
    {
        if (Route::current() && Route::current()->hasParameter('merchant')) {
            $merchantId = Route::current()->parameter('merchant');
            return "Rolling Reserve Details for Merchant: $merchantId";
        }
        
        return parent::getTitle();
    }
}
