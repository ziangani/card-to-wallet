<?php

namespace App\Filament\Resources\Backend\MerchantsResource\RelationManagers;

use App\Common\GeneralStatus;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class ApisRelationManager extends RelationManager
{
    protected static string $relationship = 'apis';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('api_key')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('api_key')
            ->columns([

                Tables\Columns\TextColumn::make('created_at'),
                Tables\Columns\TextColumn::make('api_key')->copyable(),
                Tables\Columns\TextColumn::make('api_secret')->limit(3)->copyable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        GeneralStatus::STATUS_ACTIVE => 'success',
                        GeneralStatus::STATUS_DISABLED => 'danger',
                        default => 'secondary',
                    }),



            ])
            ->filters([
                //
            ])
            ->headerActions([
//                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
//                Tables\Actions\EditAction::make(),
//                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
//                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
