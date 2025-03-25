<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Common\Helpers;
use App\Filament\Resources\UserResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Hash;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['first_name'] = $data['name'];
        $data['surname'] = $data['name'];
        $data['auth_id'] = $data['email'];
        $data['created_by'] = auth()->id();
        $password = Helpers::generatePassword(6, true);
        $data['password'] = $password;
        $data['auth_password'] = Hash::make($password);
        return $data;
    }

    protected function afterCreate(): void
    {
        Helpers::resetUserPassword($this->record);
    }
}
