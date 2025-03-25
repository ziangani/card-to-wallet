<?php

namespace App\Filament\Resources\SmsNotificationsResource\Pages;

use App\Filament\Resources\SmsNotificationsResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSmsNotifications extends EditRecord
{
    protected static string $resource = SmsNotificationsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
