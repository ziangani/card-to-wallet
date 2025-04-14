<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Notifications\Notification;

class ViewUser extends ViewRecord
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('unlockAccount')
                ->label('Unlock Account')
                ->color('warning')
                ->icon('heroicon-o-lock-open')
                ->requiresConfirmation()
                ->visible(fn () => $this->record->login_attempts >= 3)
                ->action(function () {
                    $this->record->update([
                        'login_attempts' => 0,
                        'locked_at' => null,
                        'is_active' => true,
                    ]);

                    Notification::make()
                        ->title('Account unlocked successfully')
                        ->success()
                        ->send();
                }),
        ];
    }
}
