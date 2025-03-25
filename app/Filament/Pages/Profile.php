<?php

namespace App\Filament\Pages;

use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Pages\Auth\EditProfile as BaseEditProfile;
use Filament\Actions\Action;

class Profile extends BaseEditProfile
{
    protected static bool $shouldRegisterNavigation = true;

    protected function getActions(): array
    {
        return [
            Action::make('back')
                ->label('Back')
                ->url(url()->previous())
                ->color('gray')
        ];
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('General')
                    ->schema([
                        TextInput::make('name')
                            ->required()
                            ->readOnly()
                            ->maxLength(255),
                        TextInput::make('email')
                            ->label('Email Address')
                            ->required()
                            ->email()
                            ->readOnly()
                            ->maxLength(255),
                    ]),
                
                Section::make('Update Password')
                    ->schema([
                        TextInput::make('current_password')
                            ->label('Current Password')
                            ->password()
                            ->rules(['required_with:new_password']),
                        TextInput::make('new_password')
                            ->label('New Password')
                            ->password()
                            ->rules(['confirmed']),
                        TextInput::make('new_password_confirmation')
                            ->label('Confirm New Password')
                            ->password()
                            ->dehydrated(false),
                    ])
                    ->collapsed(),
            ]);
    }
}
