<?php

namespace App\Filament\Resources\Backend;

use App\Filament\Resources\Backend;
use App\Filament\Resources\Backend\PaymentProvidersResource\Pages;
use App\Models\PaymentProviders;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Filament\Facades\Filament;

class PaymentProvidersResource extends Resource
{
    protected static ?string $model = PaymentProviders::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Settings';
    protected static ?int $navigationSort = 100;

    public static function shouldRegisterNavigation(): bool
    {
        return true;
        return Filament::auth()->user()?->isSysAdmin() ?? false;
    }

    public static function canAccess(): bool
    {
        return true;
        return Filament::auth()->user()?->isSysAdmin() ?? false;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Provider Details')
                    ->description('Enter the provider details')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->placeholder('Enter the provider name')
                            ->label('Provider Name'),

                        Forms\Components\TextInput::make('code')
                            ->required()
                            ->placeholder('Enter the provider code')
                            ->label('Provider Code'),

                        Forms\Components\Select::make('status')
                            ->options([
                                'ACTIVE' => 'Active',
                                'DISABLED' => 'Disabled',
                            ])
                            ->required()
                            ->placeholder('Select the provider status')
                            ->label('Provider Status'),

                        Forms\Components\Select::make('environment')
                            ->options([
                                'sandbox' => 'Sandbox',
                                'production' => 'Production',
                            ])
                            ->required()
                            ->placeholder('Select the environment')
                            ->label('Environment'),
                    ])->columns(2)->columnSpan(1),

                Forms\Components\Section::make('API Details')
                    ->description('Enter the API details')
                    ->schema([
                        Forms\Components\TextInput::make('api_key_id')
                            ->required()
                            ->placeholder('Enter the API key ID')
                            ->label('API Key ID'),

                        Forms\Components\TextInput::make('api_key_secret')
                            ->required()
                            ->placeholder('Enter the API key secret')
                            ->label('API Key Secret'),

                        Forms\Components\TextInput::make('api_url')
                            ->required()
                            ->placeholder('Enter the API URL')
                            ->label('API URL'),

                        Forms\Components\TextInput::make('api_token')
                            ->placeholder('Enter the API token')
                            ->label('API Token'),
                    ])->columns(2)->columnSpan(1),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('code')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->sortable()
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'ACTIVE' => 'success',
                        'DISABLED' => 'danger',
                        default => 'info',
                    }),
                Tables\Columns\TextColumn::make('environment')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'sandbox' => 'danger',
                        'production' => 'success',
                        default => 'info',
                    }),

            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
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
            'index' => Pages\ListPaymentProviders::route('/'),
            'create' => Pages\CreatePaymentProviders::route('/create'),
            'view' => Pages\ViewPaymentProviders::route('/{record}'),
            'edit' => Pages\EditPaymentProviders::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        /** @var class-string<Model> $modelClass */
        $modelClass = static::$model;

        return (string)$modelClass::count();
    }
}
