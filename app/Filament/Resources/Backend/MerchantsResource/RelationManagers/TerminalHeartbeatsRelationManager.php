<?php

namespace App\Filament\Resources\Backend\MerchantsResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TerminalHeartbeatsRelationManager extends RelationManager
{
    protected static string $relationship = 'terminalHeartbeats';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('terminal_id')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('location'),
                Forms\Components\TextInput::make('battery_health'),
                Forms\Components\TextInput::make('transactions_count')
                    ->numeric(),
                Forms\Components\Textarea::make('misc'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('id')
            ->columns([
                Tables\Columns\TextColumn::make('terminal_id'),
                Tables\Columns\TextColumn::make('location'),
                Tables\Columns\TextColumn::make('battery_health'),
                Tables\Columns\TextColumn::make('transactions_count'),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
