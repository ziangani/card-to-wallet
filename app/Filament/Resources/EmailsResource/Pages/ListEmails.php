<?php

namespace App\Filament\Resources\EmailsResource\Pages;

use App\Filament\Resources\EmailsResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListEmails extends ListRecords
{
    protected static string $resource = EmailsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
