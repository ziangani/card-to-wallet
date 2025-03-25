<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ChargebackResource\Pages;
use App\Models\Chargeback;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Builder;

class ChargebackResource extends Resource
{
    protected static ?string $model = Chargeback::class;

    protected static bool $persistFilters = true;

    protected static ?string $navigationIcon = 'heroicon-o-arrow-uturn-left';

    protected static ?string $navigationLabel = 'Chargebacks & Disputes';

    protected static ?string $navigationGroup = 'Finance';

    protected static ?int $navigationSort = 4;

    protected static bool $shouldRegisterNavigation = false;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Chargeback Details')
                    ->schema([
                        Forms\Components\TextInput::make('approval_code')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255),
                        Forms\Components\DateTimePicker::make('chargeback_date')
                            ->required()
                            ->label('Chargeback Date'),
                        Forms\Components\Select::make('reason_code')
                            ->label('Reason')
                            ->options(Chargeback::REASON_CODES)
                            ->required(),
                        Forms\Components\Select::make('status')
                            ->options(Chargeback::STATUSES)
                            ->required(),
                        Forms\Components\TextInput::make('arn')
                            ->label('ARN')
                            ->maxLength(255),
                    ])->columns(2),

                Forms\Components\Section::make('Amount Information')
                    ->schema([
                        Forms\Components\TextInput::make('orig_clear_amount')
                            ->label('Clear Amount')
                            ->numeric()
                            ->disabled(),
                        Forms\Components\TextInput::make('orig_clear_currency')
                            ->label('Clear Currency')
                            ->length(3)
                            ->disabled(),
                        Forms\Components\TextInput::make('original_amount')
                            ->label('Original Amount')
                            ->numeric()
                            ->disabled(),
                        Forms\Components\TextInput::make('original_currency')
                            ->label('Original Currency')
                            ->length(3)
                            ->disabled(),
                    ])->columns(2),

                Forms\Components\Section::make('Merchant Information')
                    ->schema([
                        Forms\Components\TextInput::make('merchant_title')
                            ->label('Merchant Name')
                            ->disabled(),
                        Forms\Components\TextInput::make('card_acceptor_id')
                            ->label('Merchant ID')
                            ->disabled(),
                        Forms\Components\TextInput::make('merchant_location')
                            ->disabled(),
                        Forms\Components\TextInput::make('merchant_city')
                            ->disabled(),
                        Forms\Components\TextInput::make('term_name')
                            ->label('Terminal Name')
                            ->disabled(),
                        Forms\Components\TextInput::make('acquirer_id')
                            ->disabled(),
                    ])->columns(2),

                Forms\Components\Section::make('Card Information')
                    ->schema([
                        Forms\Components\TextInput::make('pan')
                            ->label('Card Number')
                            ->disabled(),
                        Forms\Components\TextInput::make('condition_code')
                            ->disabled(),
                    ])->columns(2),

                Forms\Components\Section::make('Transaction Type')
                    ->schema([
                        Forms\Components\TextInput::make('tran_type_desc')
                            ->label('Transaction Type')
                            ->disabled(),
                        Forms\Components\TextInput::make('tran_code_desc')
                            ->label('Transaction Code')
                            ->disabled(),
                    ])->columns(2),

                Forms\Components\Section::make('Additional Information')
                    ->schema([
                        Forms\Components\Textarea::make('text_message')
                            ->maxLength(65535)
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('chargeback_date')
                    ->label('Chargeback Date')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('approval_code')
                    ->label('Approval Code')
                    ->searchable(),
                Tables\Columns\TextColumn::make('merchant_title')
                    ->label('Merchant')
                    ->searchable(),
                Tables\Columns\TextColumn::make('orig_clear_amount')
                    ->label('Amount')
                    ->money(fn ($record) => $record->orig_clear_currency)
                    ->sortable(),
                Tables\Columns\TextColumn::make('reason_code')
                    ->label('Reason')
                    ->formatStateUsing(fn (string $state): string => Chargeback::REASON_CODES[$state] ?? $state)
                    ->searchable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'RECEIVED_FROM_BANK' => 'warning',
                        'MERCHANT_NOTIFIED' => 'info',
                        'MERCHANT_ACCEPTED' => 'danger',
                        'MERCHANT_DISPUTED' => 'warning',
                        'DISPUTE_WON' => 'success',
                        'DISPUTE_LOST' => 'danger',
                        'REFUND_PROCESSED' => 'info',
                        'BANK_DEBITED' => 'success',
                        default => 'gray',
                    }),
                    Tables\Columns\TextColumn::make('arn')
                    ->label('ARN')
                    ->searchable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options(Chargeback::STATUSES),
                Tables\Filters\Filter::make('chargeback_date')
                    ->form([
                        Forms\Components\DatePicker::make('from_date'),
                        Forms\Components\DatePicker::make('until_date'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['from_date'],
                                fn (Builder $query, $date): Builder => $query->whereDate('chargeback_date', '>=', $date),
                            )
                            ->when(
                                $data['until_date'],
                                fn (Builder $query, $date): Builder => $query->whereDate('chargeback_date', '<=', $date),
                            );
                    }),
                Tables\Filters\SelectFilter::make('reason_code')
                    ->label('Reason')
                    ->options(Chargeback::REASON_CODES),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('chargeback_date', 'desc');
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
            'index' => Pages\ListChargebacks::route('/'),
            'create' => Pages\CreateChargeback::route('/create'),
            'edit' => Pages\EditChargeback::route('/{record}/edit'),
        ];
    }
}
