<?php

namespace App\Filament\Resources\Backend;

use App\Filament\Resources\Backend\TransactionRefundResource\Pages;
use App\Models\TransactionRefund;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class TransactionRefundResource extends Resource
{
    protected static ?string $model = TransactionRefund::class;

    protected static ?string $navigationIcon = 'heroicon-o-arrow-path';
    protected static ?string $label = 'Transaction Refunds';
    protected static ?string $navigationGroup = 'Finance';
    protected static bool $shouldRegisterNavigation = false;

    protected static ?int $navigationSort = 6;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Refund Details')
                    ->schema([
                        Forms\Components\TextInput::make('reference_id')
                            ->label('Reference ID')
                            ->disabled(),
                        Forms\Components\TextInput::make('cybersource_id')
                            ->label('Cybersource ID')
                            ->disabled(),
                        Forms\Components\TextInput::make('amount')
                            ->label('Refund Amount')
                            ->disabled(),
                        Forms\Components\TextInput::make('currency')
                            ->label('Currency')
                            ->disabled(),
                        Forms\Components\TextInput::make('status')
                            ->label('Status')
                            ->disabled(),
                        Forms\Components\TextInput::make('reason')
                            ->label('Reason')
                            ->disabled(),
                        Forms\Components\TextInput::make('arn')
                            ->label('ARN')
                            ->disabled(),
                        Forms\Components\DateTimePicker::make('created_at')
                            ->label('Request Date')
                            ->disabled(),
                        Forms\Components\DateTimePicker::make('processed_at')
                            ->label('Processed Date')
                            ->disabled(),
                        Forms\Components\TextInput::make('user.name')
                            ->label('Initiated By')
                            ->disabled(),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Original Transaction')
                    ->schema([
                        Forms\Components\TextInput::make('originalTransaction.txn_id')
                            ->label('Transaction ID')
                            ->disabled(),
                        Forms\Components\TextInput::make('originalTransaction.merchant')
                            ->label('Merchant ID')
                            ->disabled(),
                        Forms\Components\TextInput::make('originalTransaction.merchants.name')
                            ->label('Merchant Name')
                            ->disabled(),
                        Forms\Components\TextInput::make('originalTransaction.txn_amount')
                            ->label('Original Amount')
                            ->disabled(),
                        Forms\Components\TextInput::make('originalTransaction.txn_currency')
                            ->label('Original Currency')
                            ->disabled(),
                        Forms\Components\TextInput::make('originalTransaction.txn_type')
                            ->label('Transaction Type')
                            ->disabled(),
                        Forms\Components\DateTimePicker::make('originalTransaction.txn_date')
                            ->label('Transaction Date')
                            ->disabled(),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Response Data')
                    ->schema([
                        Forms\Components\Textarea::make('response_data')
                            ->label('API Response')
                            ->disabled()
                            ->columnSpanFull()
                            ->formatStateUsing(fn ($state) => $state ? json_encode($state, JSON_PRETTY_PRINT) : null),
                    ])
                    ->collapsible(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Request Date')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('processed_at')
                    ->label('Processed Date')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('originalTransaction.txn_id')
                    ->label('Original Transaction ID')
                    ->searchable(),
                Tables\Columns\TextColumn::make('originalTransaction.merchant')
                    ->label('Merchant ID')
                    ->searchable(),
                Tables\Columns\TextColumn::make('originalTransaction.merchants.name')
                    ->label('Merchant Name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Initiated By')
                    ->searchable(),
                Tables\Columns\TextColumn::make('amount')
                    ->label('Refund Amount')
                    ->money(fn ($record) => $record->currency)
                    ->sortable(),
                Tables\Columns\TextColumn::make('currency')
                    ->label('Currency'),
                Tables\Columns\TextColumn::make('reason')
                    ->label('Reason')
                    ->limit(30),
                Tables\Columns\TextColumn::make('arn')
                    ->label('ARN')
                    ->searchable(),
                Tables\Columns\TextColumn::make('cybersource_id')
                    ->label('Cybersource ID')
                    ->searchable(),
                Tables\Columns\TextColumn::make('reference_id')
                    ->label('Reference ID')
                    ->searchable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'COMPLETED' => 'success',
                        'PENDING' => 'warning',
                        'PROCESSING' => 'info',
                        'FAILED' => 'danger',
                        default => 'gray',
                    }),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options(TransactionRefund::STATUSES),
                Tables\Filters\SelectFilter::make('user_id')
                    ->label('Initiated By')
                    ->relationship('user', 'name'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([])
            ->deferLoading();
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
            'index' => Pages\ListTransactionRefunds::route('/'),
            'view' => Pages\ViewTransactionRefund::route('/{record}'),
        ];
    }
}
