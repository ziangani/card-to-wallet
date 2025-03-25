<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MerchantReconciliationResource\Pages;
use App\Enums\ReconciliationStatus;
use App\Models\MerchantReconciliation;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class MerchantReconciliationResource extends Resource
{
    protected static ?string $model = MerchantReconciliation::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationGroup = 'Finance';

    protected static bool $shouldRegisterNavigation = false;


    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('status', ReconciliationStatus::ACTIVE->value);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query) => $query->withoutGlobalScopes())
            ->columns([
                Tables\Columns\TextColumn::make('merchant.name')
                    ->label('Merchant Name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('merchant_id')
                    ->label('Merchant Code')
                    ->searchable(),
                Tables\Columns\TextColumn::make('date')
                    ->date('Y-m-d')
                    ->sortable(),
//                Tables\Columns\TextColumn::make('status')
//                    ->badge()
//                    ->formatStateUsing(fn (ReconciliationStatus $state): string => $state->label())
//                    ->color(fn (ReconciliationStatus $state): string => match ($state) {
//                        ReconciliationStatus::ACTIVE => 'success',
//                        ReconciliationStatus::SUPERSEDED => 'danger',
//                    }),
//                Tables\Columns\TextColumn::make('version'),
                Tables\Columns\TextColumn::make('transaction_count')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('total_amount')
                    ->numeric(
                        decimalPlaces: 2,
                        thousandsSeparator: ',',
                    )
                    ->sortable(),
                Tables\Columns\TextColumn::make('platform_fee')
                    ->numeric(
                        decimalPlaces: 2,
                        thousandsSeparator: ',',
                    ),
                Tables\Columns\TextColumn::make('bank_fee')
                    ->numeric(
                        decimalPlaces: 2,
                        thousandsSeparator: ',',
                    ),
                Tables\Columns\TextColumn::make('application_fee')
                    ->numeric(
                        decimalPlaces: 2,
                        thousandsSeparator: ',',
                    )->label('Techpay Fee'),
                Tables\Columns\TextColumn::make('rolling_reserve')
                    ->numeric(
                        decimalPlaces: 2,
                        thousandsSeparator: ',',
                    ),
                Tables\Columns\TextColumn::make('return_reserve')
                    ->numeric(
                        decimalPlaces: 2,
                        thousandsSeparator: ',',
                    ),
                Tables\Columns\TextColumn::make('refund_amount')
                    ->numeric(
                        decimalPlaces: 2,
                        thousandsSeparator: ',',
                    ),
                Tables\Columns\TextColumn::make('chargeback_count')
                    ->numeric(
                        decimalPlaces: 0,
                        thousandsSeparator: ',',
                    ),
                Tables\Columns\TextColumn::make('chargeback_amount')
                    ->numeric(
                        decimalPlaces: 2,
                        thousandsSeparator: ',',
                    ),
                Tables\Columns\TextColumn::make('chargeback_fees')
                    ->numeric(
                        decimalPlaces: 2,
                        thousandsSeparator: ',',
                    ),
                Tables\Columns\TextColumn::make('net_processed')
                    ->numeric(
                        decimalPlaces: 2,
                        thousandsSeparator: ',',
                    )
                    ->sortable(),
                Tables\Columns\TextColumn::make('settled_amount')
                    ->numeric(
                        decimalPlaces: 2,
                        thousandsSeparator: ',',
                    )
                    ->sortable(),
                Tables\Columns\TextColumn::make('generated_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options(ReconciliationStatus::toArray())
                    ->default(ReconciliationStatus::ACTIVE->value),
                Tables\Filters\Filter::make('date')
                    ->form([
                        Forms\Components\DatePicker::make('date')
                            ->label('Date'),
                    ])
                    ->query(function ($query, array $data) {
                        if (isset($data['date'])) {
                            $query->whereDate('date', $data['date']);
                        }
                    }),
            ])->defaultSort('date', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMerchantReconciliations::route('/'),
        ];
    }
}
