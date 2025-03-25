<?php

namespace App\Filament\Resources;

use App\Common\Helpers;
use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Models\User;
use App\Models\Voter;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Facades\Filament;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup = 'Settings';

    public static function shouldRegisterNavigation(): bool
    {
        // @phpstan-ignore-line
        return Filament::auth()->user()?->isSysAdmin() ?? false;
    }

    public static function canAccess(): bool
    {
        // @phpstan-ignore-line
        return Filament::auth()->user()?->isSysAdmin() ?? false;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label('Names')
                    ->required(),
                Forms\Components\TextInput::make('email')
                    ->label('Email')
                    ->required(),

                Forms\Components\TextInput::make('mobile')
                    ->label('Mobile')
                    ->required(),

                Forms\Components\Select::make('user_type')
                    ->label('User Type')
                    ->options(User::$userTypes)
                    ->required(),
                Forms\Components\Select::make('merchant_id')
                    ->label('Merchant')
                    ->relationship('merchants', 'name')
                    ->getOptionLabelFromRecordUsing(fn ($record) => "{$record->name} ({$record->code})")
                    ->preload()
                    ->searchable()
                    ->required(),
//                Forms\Components\Select::make('company_detail_id')
//                    ->label('Company')
//                    ->relationship('companyDetails', 'company_name')
//                    ->preload()
//                    ->searchable()
//                    ->required()
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('user_type')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('last_login_date')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->searchable()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('Reset Password')
                    ->requiresConfirmation()
                    ->action(fn(User $record) => Helpers::resetUserPassword($record))
                    ->icon('heroicon-o-rectangle-stack')
                    ->successNotification(Notification::make('Notification sent')),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
    public static function getNavigationBadge(): ?string
    {
        /** @var class-string<Model> $modelClass */
        $modelClass = static::$model;

        return (string)$modelClass::count();
    }
}
