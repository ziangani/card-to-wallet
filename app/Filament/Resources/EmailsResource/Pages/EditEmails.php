<?php

namespace App\Filament\Resources\EmailsResource\Pages;

use App\Filament\Resources\EmailsResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditEmails extends EditRecord
{
    protected static string $resource = EmailsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
