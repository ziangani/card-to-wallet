<?php

namespace App\Filament\Resources\Backend;

use App\Filament\Resources\Backend\TerminalHeartbeatResource\Pages;
use App\Models\Merchants;
use App\Models\TerminalHeartbeat;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TerminalHeartbeatResource extends Resource
{
    protected static ?string $model = TerminalHeartbeat::class;

    protected static ?string $navigationIcon = 'heroicon-o-heart';

    protected static ?string $navigationGroup = 'Terminals';

    protected static bool $shouldRegisterNavigation = false;

    public static function form(Forms\Form $form): Forms\Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('terminal.serial_number')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('location'),
                Forms\Components\TextInput::make('battery_health'),
                Forms\Components\TextInput::make('transactions_count')
                    ->numeric(),
                Forms\Components\Textarea::make('misc'),
            ]);
    }

    public static function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('terminal.serial_number'),
                Tables\Columns\TextColumn::make('terminal.model'),
                Tables\Columns\TextColumn::make('location'),
                Tables\Columns\TextColumn::make('battery_health'),
                Tables\Columns\TextColumn::make('transactions_count'),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime(),
            ])
            ->filters([
                //
            ])
            ->actions([
//                Tables\Actions\ViewAction::make(),
//                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
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
            'index' => Pages\ListTerminalHeartbeats::route('/'),
            'create' => Pages\CreateTerminalHeartbeat::route('/create'),
//            'view' => Pages\ViewTerminalHeartbeat::route('/{record}'),
            'edit' => Pages\EditTerminalHeartbeat::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        /** @var class-string<Model> $modelClass */
        $modelClass = static::$model;

        return (string)$modelClass::count();
    }
}
