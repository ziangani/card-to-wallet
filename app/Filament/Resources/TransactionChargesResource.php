<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TransactionChargesResource\Pages;
use App\Models\TransactionCharges;
use App\Models\Merchants;
use App\Enums\ChargeName;
use App\Enums\ChargeType;
use App\Enums\PaymentChannel;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TransactionChargesResource extends Resource
{
    protected static ?string $model = TransactionCharges::class;

    protected static ?string $navigationIcon = 'heroicon-o-calculator';
    protected static ?string $navigationLabel = 'Transaction Charges';
    protected static ?string $navigationGroup = 'Finance';
    protected static ?int $navigationSort = 3;

    protected static bool $shouldRegisterNavigation = false;


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Charge Information')
                    ->schema([
                        Forms\Components\TextInput::make('transaction_id')
                            ->label('Transaction ID')
                            ->disabled(),
                        Forms\Components\TextInput::make('settlement_id')
                            ->label('Settlement ID')
                            ->disabled(),
                        Forms\Components\TextInput::make('merchant_id')
                            ->label('Merchant Code')
                            ->disabled(),
                        Forms\Components\TextInput::make('charge_name')
                            ->disabled(),
                        Forms\Components\TextInput::make('charge_type')
                            ->disabled(),
                        Forms\Components\TextInput::make('charge_value')
                            ->disabled()
                            ->numeric()
                            ->step(0.01),
                        Forms\Components\TextInput::make('base_amount')
                            ->disabled()
                            ->numeric()
                            ->step(0.01),
                        Forms\Components\TextInput::make('calculated_amount')
                            ->disabled()
                            ->numeric()
                            ->step(0.01),
                        Forms\Components\DateTimePicker::make('created_at')
                            ->disabled(),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),

                Tables\Columns\TextColumn::make('transaction_id')
                    ->label('Transaction ID')
                    // ->url(fn ($record) => $record->transaction_id ? url("/admin/transactions/{$record->transaction_id}") : null)
                    ->color('primary')
                    ->searchable(isIndividual:true),
                Tables\Columns\TextColumn::make('settlement_id')
                    ->label('Settlement ID')
                    // ->url(fn ($record) => $record->settlement_id ? url("/admin/settlement-records/{$record->settlement_id}") : null)
                    ->color('primary')
                    ->searchable(isIndividual:true),
                Tables\Columns\TextColumn::make('merchant.name')
                    ->label('Merchant')
                    ->searchable(['merchant_id', 'merchants.name']),

                Tables\Columns\TextColumn::make('charge_name')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'PROVIDER_FEE' => 'warning',
                        'BANK_FEE' => 'info',
                        'PLATFORM_FEE' => 'success',
                        'TRANSACTION_FEE' => 'danger',
                        'ROLLING_RESERVE' => 'gray',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'PROVIDER_FEE' => 'Provider Fee',
                        'BANK_FEE' => 'Bank Fee',
                        'PLATFORM_FEE' => 'Platform Fee',
                        'TRANSACTION_FEE' => 'Transaction Fee',
                        'ROLLING_RESERVE' => 'Rolling Reserve',
                        default => $state,
                    })
                    ->sortable(),
                Tables\Columns\TextColumn::make('charge_type')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'FIXED' => 'info',
                        'PERCENTAGE' => 'warning',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'FIXED' => 'Fixed',
                        'PERCENTAGE' => 'Percentage',
                        default => $state,
                    })
                    ->sortable(),
                Tables\Columns\TextColumn::make('charge_value')
                    ->formatStateUsing(fn ($state, $record) => $record->charge_type === 'PERCENTAGE' ?
                        number_format($state, 2) . '%' : number_format($state, 2))
                    ->sortable(),
                Tables\Columns\TextColumn::make('base_amount')
                    ->formatStateUsing(fn ($state) => number_format($state, 2))
                    ->sortable(),
                Tables\Columns\TextColumn::make('calculated_amount')
                    ->label('Charge Amount')
                    ->formatStateUsing(fn ($state) => number_format($state, 2))
                    ->sortable(),
                Tables\Columns\TextColumn::make('transaction.payment_channel')
                    ->label('Channel')
                    ->badge()
                    ->color('gray')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('charge_name')
                    ->options(ChargeName::toArray())
                    ->label('Charge Name'),
                Tables\Filters\SelectFilter::make('charge_type')
                    ->options(ChargeType::toArray())
                    ->label('Charge Type'),
                Tables\Filters\SelectFilter::make('merchant_id')
                    ->label('Merchant')
                    ->options(fn () => Merchants::pluck('name', 'code')->toArray())
                    ->searchable(),
                Tables\Filters\Filter::make('source_type')
                    ->form([
                        Forms\Components\Select::make('source')
                            ->options([
                                'transaction' => 'Transaction',
                                'settlement' => 'Settlement',
                                'both' => 'Both',
                            ])
                            ->default('both'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query->when(
                            $data['source'] === 'transaction',
                            fn (Builder $query): Builder => $query->whereNotNull('transaction_id')->whereNull('settlement_id'),
                        )->when(
                            $data['source'] === 'settlement',
                            fn (Builder $query): Builder => $query->whereNotNull('settlement_id')->whereNull('transaction_id'),
                        );
                    }),
                Tables\Filters\Filter::make('created_at')
                    ->form([
                        Forms\Components\DatePicker::make('created_from')
                            ->label('From'),
                        Forms\Components\DatePicker::make('created_until')
                            ->label('Until'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['created_from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['created_until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                            );
                    }),
                Tables\Filters\SelectFilter::make('transaction.payment_channel')
                    ->label('Payment Channel')
                    ->options(PaymentChannel::toArray())
                    ->query(function (Builder $query, array $data): Builder {
                        return $query->when(
                            $data['value'],
                            fn (Builder $query, $channel): Builder => $query
                                ->whereHas('transaction', fn ($q) => $q->where('payment_channel', $channel))
                        );
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    // No bulk actions needed for this resource
                ]),
            ])
            ->defaultSort('created_at', 'desc');
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
            'index' => Pages\ListTransactionCharges::route('/'),
            'view' => Pages\ViewTransactionCharge::route('/{record}'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes();
    }
}
