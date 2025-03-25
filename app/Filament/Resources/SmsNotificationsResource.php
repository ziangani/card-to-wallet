<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SmsNotificationsResource\Pages;
use App\Filament\Resources\SmsNotificationsResource\RelationManagers;
use App\Models\SmsNotifications;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class SmsNotificationsResource extends Resource
{
    protected static ?string $model = SmsNotifications::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Settings';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([


                Tables\Columns\TextColumn::make('created_at')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('mobile')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('status')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('sent_at')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('attempts')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('response')
                    ->searchable()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])->defaultSort('created_at', 'desc');
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
            'index' => Pages\ListSmsNotifications::route('/'),
//            'create' => Pages\CreateSmsNotifications::route('/create'),
//            'edit' => Pages\EditSmsNotifications::route('/{record}/edit'),
        ];
    }
}
