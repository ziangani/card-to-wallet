<?php

namespace App\Filament\Resources\Backend;

use App\Filament\Resources\Backend;
use App\Filament\Resources\Backend\TerminalsResource\Pages;
use App\Filament\Resources\Backend\TerminalsResource\RelationManagers;
use App\Models\Merchants;
use App\Models\Terminals;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class TerminalsResource extends Resource
{
    protected static ?string $model = Terminals::class;

    protected static ?string $navigationIcon = 'heroicon-o-phone';
    protected static ?string $navigationLabel = 'Terminals';
     protected static ?string $navigationGroup = 'Terminals';
     protected static bool $shouldRegisterNavigation = false;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Terminal Information')
                    ->schema([
                        Forms\Components\TextInput::make('serial_number')
                            ->required()
                            ->unique(ignoreRecord: true),
                        Forms\Components\Select::make('type')
                            ->required()
                            ->options(Terminals::TYPES),
                        Forms\Components\TextInput::make('model')
                            ->required(),
                        Forms\Components\TextInput::make('manufacturer')
                            ->required(),
                    ])->columnSpan(1),
                Forms\Components\Section::make('Status Information')
                    ->schema([
                        Forms\Components\Select::make('merchant_id')
                            ->relationship('merchant', 'name')
                            ->required(),
                        Forms\Components\Select::make('status')
                            ->required()
                            ->options([
                                Terminals::STATUS_UPLOADED => 'Uploaded',
                            ])->default(Terminals::STATUS_UPLOADED),
                        Forms\Components\TextInput::make('terminal_id')
                            ->disabled()
                            ->unique(ignoreRecord: true)
                            ->integer(),
                    ])->columnSpan(1),
            ])->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Date Uploaded'),
                Tables\Columns\TextColumn::make('terminal_id')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('serial_number')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('merchant.name')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('type')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('model')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('status')
                    ->sortable()
                    ->searchable()
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        Terminals::STATUS_UPLOADED => 'info',
                        Terminals::STATUS_ACTIVATED => 'success',
                    }),
                Tables\Columns\TextColumn::make('date_activated')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Last Updated'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
//            RelationManagers\TerminalHeartbeatsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Backend\TerminalsResource\Pages\ListTerminals::route('/'),
            'create' => Backend\TerminalsResource\Pages\CreateTerminals::route('/create'),
            'view' => Backend\TerminalsResource\Pages\ViewTerminals::route('/{record}'),
            'edit' => Backend\TerminalsResource\Pages\EditTerminals::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return parent::getEloquentQuery()->withoutGlobalScopes();
    }

    public static function getNavigationBadge(): ?string
    {
        /** @var class-string<Model> $modelClass */
        $modelClass = static::$model;

        return (string)$modelClass::where('status', Merchants::STATUS_ACTIVE)->count();
    }
}
