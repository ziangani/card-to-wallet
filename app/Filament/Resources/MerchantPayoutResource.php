<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MerchantPayoutResource\Pages;
use App\Filament\Resources\MerchantPayoutResource\RelationManagers;
use App\Models\MerchantPayout;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class MerchantPayoutResource extends Resource
{
    protected static ?string $model = MerchantPayout::class;

    protected static ?string $navigationIcon = 'heroicon-o-banknotes';
    protected static ?string $navigationGroup = 'Finance';
    protected static ?int $navigationSort = 20;
    protected static ?string $navigationLabel = 'Merchant Payouts';
    protected static ?string $modelLabel = 'Payout';
    protected static ?string $pluralModelLabel = 'Payouts';

    protected static bool $shouldRegisterNavigation = false;


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('merchant_id')
                    ->relationship('merchant', 'name')
                    ->required(),
                Forms\Components\TextInput::make('amount')
                    ->required()
                    ->numeric(),
                Forms\Components\Select::make('type')
                    ->required()
                    ->options([
                        'SETTLEMENT' => 'Settlement',
                        'ROLLING_RESERVE_RETURN' => 'Rolling Reserve Return',
                        'REFUND' => 'Refund',
                        'ADJUSTMENT' => 'Adjustment',
                        'OTHER' => 'Other',
                    ]),
                Forms\Components\Select::make('status')
                    ->required()
                    ->options([
                        'PENDING' => 'Pending',
                        'COMPLETED' => 'Completed',
                        'FAILED' => 'Failed',
                        'CANCELLED' => 'Cancelled',
                    ])
                    ->default('PENDING'),
                Forms\Components\TextInput::make('reference')
                    ->maxLength(255),
                Forms\Components\TextInput::make('remittance_fee')
                    ->required()
                    ->numeric()
                    ->default(0),
                Forms\Components\DateTimePicker::make('initiated_at'),
                Forms\Components\DateTimePicker::make('completed_at'),
                Forms\Components\TextInput::make('initiated_by')
                    ->maxLength(255),
                Forms\Components\Textarea::make('notes')
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('merchant.name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('amount')
                    ->numeric(
                        decimalPlaces: 2,
                        thousandsSeparator: ',',
                    )
                    ->sortable(),
                Tables\Columns\TextColumn::make('type')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'SETTLEMENT' => 'success',
                        'ROLLING_RESERVE_RETURN' => 'info',
                        'REFUND' => 'warning',
                        'ADJUSTMENT' => 'danger',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'PENDING' => 'warning',
                        'COMPLETED' => 'success',
                        'FAILED' => 'danger',
                        'CANCELLED' => 'gray',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('remittance_fee')
                    ->numeric(
                        decimalPlaces: 2,
                        thousandsSeparator: ',',
                    )
                    ->sortable(),
                Tables\Columns\TextColumn::make('initiated_at')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('completed_at')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('reference')
                    ->searchable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('initiated_by')
                    ->searchable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->options([
                        'SETTLEMENT' => 'Settlement',
                        'ROLLING_RESERVE_RETURN' => 'Rolling Reserve Return',
                        'REFUND' => 'Refund',
                        'ADJUSTMENT' => 'Adjustment',
                        'OTHER' => 'Other',
                    ]),
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'PENDING' => 'Pending',
                        'COMPLETED' => 'Completed',
                        'FAILED' => 'Failed',
                        'CANCELLED' => 'Cancelled',
                    ]),
                Tables\Filters\Filter::make('initiated_at')
                    ->form([
                        Forms\Components\DatePicker::make('initiated_from'),
                        Forms\Components\DatePicker::make('initiated_until'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['initiated_from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('initiated_at', '>=', $date),
                            )
                            ->when(
                                $data['initiated_until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('initiated_at', '<=', $date),
                            );
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('mark_completed')
                    ->label('Mark as Completed')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->form([
                        Forms\Components\TextInput::make('reference')
                            ->label('Payment Reference')
                            ->required(),
                    ])
                    ->action(function (MerchantPayout $record, array $data): void {
                        $record->markAsCompleted($data['reference']);
                    })
                    ->visible(fn (MerchantPayout $record): bool => $record->status === 'PENDING'),
                Tables\Actions\Action::make('mark_failed')
                    ->label('Mark as Failed')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->form([
                        Forms\Components\Textarea::make('notes')
                            ->label('Failure Reason')
                            ->required(),
                    ])
                    ->action(function (MerchantPayout $record, array $data): void {
                        $record->markAsFailed($data['notes']);
                    })
                    ->visible(fn (MerchantPayout $record): bool => $record->status === 'PENDING'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
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
            'index' => Pages\ListMerchantPayouts::route('/'),
            'create' => Pages\CreateMerchantPayout::route('/create'),
            'edit' => Pages\EditMerchantPayout::route('/{record}/edit'),
        ];
    }
}
