<?php

namespace App\Filament\Resources\Backend;

use App\Filament\Resources\Backend;
use App\Filament\Resources\TransactionsResource\Pages;
use App\Filament\Resources\TransactionsResource\RelationManagers;
use App\Models\Transactions;
use Filament\Forms\Form;
use Filament\Navigation\NavigationGroup;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class TransactionsResource extends Resource
{
    protected static ?string $model = Transactions::class;

    protected static ?string $navigationIcon = 'heroicon-o-ticket';
//    protected static bool $shouldRegisterNavigation = false;

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

                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Last Updated')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Created At')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('merchant_reference')
                    ->label('Merchant Reference')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('reference_1')
                    ->label('Reference 1')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('amount')
                    ->label('Amount')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('merchants.name')
                    ->label('Merchant Name')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('card_prefix')
                    ->label('Card Prefix')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('card_suffix')
                    ->label('Card Suffix')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('status')
                    ->label('Payment Status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'PENDING' => 'info',
                        'COMPLETE' => 'success',
                        'FAILED' => 'danger',
                    })->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('provider_push_status')
                    ->label('Provider Push Status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'PENDING' => 'info',
                        'COMPLETE' => 'success',
                        'SUCCESS' => 'success',
                        'FAILED' => 'danger',
                    })->searchable()
                    ->sortable(),


            ])
            ->filters([
                //
            ])
            ->actions([
//                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
//                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])->defaultSort('updated_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            Backend\TransactionsResource\RelationManagers\LogsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Backend\TransactionsResource\Pages\ListTransactions::route('/'),
//            'create' => Pages\CreateTransactions::route('/create'),
//            'edit' => Pages\EditTransactions::route('/{record}/edit'),
            'view' => Backend\TransactionsResource\Pages\ViewTransaction::route('/{record}'),
        ];
    }
}
