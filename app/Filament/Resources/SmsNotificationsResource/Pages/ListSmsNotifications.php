<?php

namespace App\Filament\Resources\SmsNotificationsResource\Pages;

use App\Filament\Resources\SmsNotificationsResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSmsNotifications extends ListRecords
{
    protected static string $resource = SmsNotificationsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
