<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TransactionResource\Pages;
use App\Models\Transaction;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;
use Illuminate\Support\Carbon;

class TransactionResource extends Resource
{
    protected static ?string $model = Transaction::class;

    protected static ?string $navigationIcon = 'heroicon-o-banknotes';

    protected static ?string $navigationGroup = 'Transactions';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Transaction Details')
                    ->schema([
                        Forms\Components\TextInput::make('uuid')
                            ->label('Transaction ID')
                            ->disabled(),
                        Forms\Components\TextInput::make('transaction_type')
                            ->disabled(),
                        Forms\Components\TextInput::make('reference_4')
                        ->label('Receipient')
                            ->disabled(),
                        Forms\Components\TextInput::make('reference_1')
                        ->label('Mobile')
                            ->disabled(),
                        Forms\Components\TextInput::make('amount')
                            ->disabled()
                            ->prefix('ZMW'),
                        Forms\Components\TextInput::make('fee_amount')
                            ->disabled()
                            ->prefix('ZMW'),
                        Forms\Components\TextInput::make('total_amount')
                            ->disabled()
                            ->prefix('ZMW'),
                        Forms\Components\TextInput::make('status')
                            ->disabled(),
                    ])->columns(2),

                Forms\Components\Section::make('Payment Details')
                    ->schema([
                        Forms\Components\TextInput::make('mpgs_order_id')
                            ->label('MPGS Order ID')
                            ->disabled(),
                        Forms\Components\TextInput::make('mpgs_result_code')
                            ->label('MPGS Result Code')
                            ->disabled(),
                        Forms\Components\TextInput::make('provider_reference')
                            ->disabled(),
                        Forms\Components\TextInput::make('provider_name')
                            ->disabled(),
                        Forms\Components\TextInput::make('provider_push_status')
                            ->disabled(),
                        Forms\Components\TextInput::make('provider_status_description')
                            ->disabled(),
                        Forms\Components\TextInput::make('provider_payment_reference')
                            ->disabled(),
                        Forms\Components\TextInput::make('provider_payment_date')
                            ->disabled(),
                    ])->columns(2),

                Forms\Components\Section::make('Additional Information')
                    ->schema([
                        Forms\Components\TextInput::make('ip_address')
                            ->disabled(),
                        Forms\Components\TextInput::make('user_agent')
                            ->disabled(),
                        Forms\Components\TextInput::make('merchant_reference')
                            ->disabled(),
                        Forms\Components\TextInput::make('merchant_code')
                            ->disabled(),
                        Forms\Components\TextInput::make('merchant_settlement_status')
                            ->disabled(),
                        Forms\Components\TextInput::make('merchant_settlement_date')
                            ->disabled(),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Date')
                    ->dateTime()
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('reference_2')
                    ->label('Transaction ID')
                    ->searchable(),
                Tables\Columns\TextColumn::make('reference_1')
                ->label('Mobile')
                    ->searchable(),
                Tables\Columns\TextColumn::make('reference_4')
                ->label('Client')
                    ->searchable(),
                Tables\Columns\TextColumn::make('fee_amount')
                    ->money('ZMW')
                    ->sortable(),
                Tables\Columns\TextColumn::make('total_amount')
                    ->money('ZMW')
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'COMPLETED' => 'success',
                        'PENDING' => 'warning',
                        'FAILED' => 'danger',
                        'PROCESSING' => 'info',
                        default => 'gray',
                    })
                    ->searchable(),
                    Tables\Columns\TextColumn::make('merchant_settlement_status')
                    ->badge()
                    ->label('Funding Status')
                    ->color(fn (string $state): string => match ($state) {
                        'SUCCESS' => 'success',
                        'PENDING' => 'warning',
                        'FAILED' => 'danger',
                        'PROCESSING' => 'info',
                        default => 'gray',
                    })
                    ->searchable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'COMPLETED' => 'Completed',
                        'PENDING' => 'Pending',
                        'FAILED' => 'Failed',
                        'PROCESSING' => 'Processing',
                    ]),
                SelectFilter::make('provider_name')
                    ->label('Provider')
                    ->relationship('walletProvider', 'name'),
                Filter::make('created_at')
                    ->form([
                        Forms\Components\DatePicker::make('created_from'),
                        Forms\Components\DatePicker::make('created_until'),
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
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];

                        if ($data['created_from'] ?? null) {
                            $indicators['created_from'] = 'From ' . Carbon::parse($data['created_from'])->toFormattedDateString();
                        }

                        if ($data['created_until'] ?? null) {
                            $indicators['created_until'] = 'Until ' . Carbon::parse($data['created_until'])->toFormattedDateString();
                        }

                        return $indicators;
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([
                // No bulk actions needed
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
            'index' => Pages\ListTransactions::route('/'),
            'view' => Pages\ViewTransaction::route('/{record}'),
        ];
    }
}
