<?php

namespace App\Filament\Resources\Backend\TransactionsResource\RelationManagers;

use App\Common\Helpers;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class LogsRelationManager extends RelationManager
{
    protected static string $relationship = 'logs';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('reference')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('reference')
            ->columns([

                Tables\Columns\TextColumn::make('created_at')->date('d-m-Y')->label('Date'),
                Tables\Columns\TextColumn::make('request_time')
                    ->state(function (Model $record) {
                        return Helpers::diffInSeconds($record->request_time, $record->response_time);
                    })->label('Time Taken(s)'),
                Tables\Columns\TextColumn::make('request_status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'COMPLETE', 'SUCCESS' => 'success',
                        'FAILED' => 'danger',
                        default => 'info',
                    }),
                Tables\Columns\TextColumn::make('source_ip'),
                Tables\Columns\TextColumn::make('source_reference'),
                Tables\Columns\TextColumn::make('request_type'),

                Tables\Columns\TextColumn::make('request')->limit(20)
                ->copyable(),
                Tables\Columns\TextColumn::make('response')->limit(20)->copyable(),

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
