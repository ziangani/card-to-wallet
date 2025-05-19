<?php

namespace App\Filament\Resources\CorporateWalletResource\Pages;

use App\Filament\Resources\CorporateWalletResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewCorporateWallet extends ViewRecord
{
    protected static string $resource = CorporateWalletResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
            Actions\Action::make('deposit')
                ->label('Deposit')
                ->icon('heroicon-o-plus-circle')
                ->color('success')
                ->form([
                    \Filament\Forms\Components\TextInput::make('amount')
                        ->numeric()
                        ->required()
                        ->minValue(0.01)
                        ->prefix('ZMW'),
                        
                    \Filament\Forms\Components\TextInput::make('description')
                        ->required()
                        ->maxLength(255),
                        
                    \Filament\Forms\Components\TextInput::make('reference')
                        ->maxLength(255),
                ])
                ->action(function (array $data): void {
                    $record = $this->getRecord();
                    
                    $record->deposit(
                        $data['amount'],
                        $data['description'],
                        $data['reference'] ?? 'DEPOSIT-' . time(),
                        1 // Default admin user ID
                    );
                    
                    \Filament\Notifications\Notification::make()
                        ->title('Deposit successful')
                        ->success()
                        ->send();
                        
                    $this->redirect($this->getResource()::getUrl('view', ['record' => $record]));
                }),
        ];
    }
}
