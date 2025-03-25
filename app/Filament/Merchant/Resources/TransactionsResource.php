<?php

namespace App\Filament\Merchant\Resources;

use App\Filament\Merchant\Resources\TransactionsResource\Pages;
use App\Filament\Merchant\Resources\TransactionsResource\RelationManagers;
use App\Models\Transactions;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists\Components\Group;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Infolists;
use Filament\Infolists\Infolist;

class TransactionsResource extends Resource
{
    protected static ?string $model = Transactions::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

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
            ->deferLoading()
            ->columns([
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Date Initiated')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('merchant_reference')
                    ->label('Reference')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('request.description')
                    ->label('Description')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('reference_1')
                    ->label('Payer')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('amount')
                    ->label('Amount')
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
                    ->label('Request Status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'PENDING' => 'info',
                        'COMPLETE', 'SUCCESS' => 'success',
                        'FAILED' => 'danger',
                    })->searchable()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make('Detail')
                    ->icon('heroicon-o-eye'),
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
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTransactions::route('/'),
//            'create' => Pages\CreateTransactions::route('/create'),
//            'edit' => Pages\EditTransactions::route('/{record}/edit'),
        ];
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist->schema([
            Section::make('Transaction Details')
                ->schema([
                    TextEntry::make('created_at')
                        ->label('Date Created')
                    ->inlineLabel(),

                    TextEntry::make('id')
                        ->label('Transaction Id')
                        ->inlineLabel(),

                    TextEntry::make('status')
                        ->label('Status')
                        ->badge()
                        ->color(fn(string $state): string => match ($state) {
                            'PENDING' => 'info',
                            'COMPLETE' => 'success',
                            'SUCCESS' => 'success',
                            'FAILED' => 'danger',
                        })
                        ->inlineLabel(),

                    TextEntry::make('amount')
                        ->label('Amount')
                        ->inlineLabel(),

                    TextEntry::make('merchant_reference')
                        ->label('Your Reference')
                        ->inlineLabel(),

                    TextEntry::make('payment_channel')
                        ->label('Payment Channel')
                        ->default('Unknown')
                        ->inlineLabel(),


                    TextEntry::make('reference_1')
                        ->label('Reference 1')->default('N/A')
                        ->inlineLabel(),

                    TextEntry::make('reference_2')
                        ->label('Reference 2')->default('N/A')
                        ->inlineLabel(),


                ])->columnSpan(2)->columns(2),

            Group::make([
                Section::make('Provider Details')
                    ->schema([
                        TextEntry::make('provider_name')
                            ->label('Provider Name')
                            ->inlineLabel(),

                        TextEntry::make('provider_push_status')
                            ->label('Push Status')
                            ->badge()
                            ->color(fn(string $state): string => match ($state) {
                                'PENDING' => 'info',
                                'COMPLETE', 'SUCCESS' => 'success',
                                'FAILED' => 'danger',
                            })
                            ->inlineLabel(),
                    ])->columns(2),

            ])->columnSpan(2)->columns(2),


        ]);
    }
}
