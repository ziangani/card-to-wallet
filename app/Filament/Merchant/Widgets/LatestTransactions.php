<?php

namespace App\Filament\Merchant\Widgets;

use App\Filament\Merchant\Resources\TransactionsResource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class LatestTransactions extends BaseWidget
{
    protected static ?int $sort = 2;
    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                TransactionsResource::getEloquentQuery()
            )->defaultPaginationPageOption(5)
            ->defaultSort('created_at', 'desc')
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
            ]);
    }
}
