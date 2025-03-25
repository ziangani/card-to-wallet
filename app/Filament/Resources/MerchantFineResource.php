<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MerchantFineResource\Pages;
use App\Filament\Resources\MerchantFineResource\RelationManagers;
use App\Models\MerchantFine;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class MerchantFineResource extends Resource
{
    protected static ?string $model = MerchantFine::class;

    protected static ?string $navigationIcon = 'heroicon-o-exclamation-triangle';
    protected static ?string $navigationGroup = 'Finance';
    protected static ?int $navigationSort = 30;
    protected static ?string $navigationLabel = 'Merchant Fines';
    protected static ?string $modelLabel = 'Fine';
    protected static ?string $pluralModelLabel = 'Fines';
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
                Forms\Components\Select::make('issuer')
                    ->required()
                    ->options([
                        'VISA' => 'Visa',
                        'MASTERCARD' => 'Mastercard',
                        'REGULATOR' => 'Regulator',
                        'OTHER' => 'Other',
                    ]),
                Forms\Components\TextInput::make('reason')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Select::make('status')
                    ->required()
                    ->options([
                        'PENDING' => 'Pending',
                        'PAID' => 'Paid',
                        'DISPUTED' => 'Disputed',
                        'WAIVED' => 'Waived',
                    ])
                    ->default('PENDING'),
                Forms\Components\TextInput::make('reference')
                    ->maxLength(255),
                Forms\Components\DatePicker::make('issued_date')
                    ->required(),
                Forms\Components\DatePicker::make('due_date'),
                Forms\Components\DatePicker::make('paid_date'),
                Forms\Components\Select::make('paid_by')
                    ->options([
                        'MERCHANT' => 'Merchant',
                        'PLATFORM' => 'Platform',
                    ]),
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
                Tables\Columns\TextColumn::make('issuer')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'VISA' => 'info',
                        'MASTERCARD' => 'warning',
                        'REGULATOR' => 'danger',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('reason')
                    ->searchable()
                    ->limit(30),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'PENDING' => 'warning',
                        'PAID' => 'success',
                        'DISPUTED' => 'info',
                        'WAIVED' => 'gray',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('issued_date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('due_date')
                    ->date()
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('paid_date')
                    ->date()
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('paid_by')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'MERCHANT' => 'success',
                        'PLATFORM' => 'danger',
                        default => 'gray',
                    })
                    ->toggleable(),
                Tables\Columns\TextColumn::make('reference')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
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
                Tables\Filters\SelectFilter::make('issuer')
                    ->options([
                        'VISA' => 'Visa',
                        'MASTERCARD' => 'Mastercard',
                        'REGULATOR' => 'Regulator',
                        'OTHER' => 'Other',
                    ]),
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'PENDING' => 'Pending',
                        'PAID' => 'Paid',
                        'DISPUTED' => 'Disputed',
                        'WAIVED' => 'Waived',
                    ]),
                Tables\Filters\Filter::make('issued_date')
                    ->form([
                        Forms\Components\DatePicker::make('issued_from'),
                        Forms\Components\DatePicker::make('issued_until'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['issued_from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('issued_date', '>=', $date),
                            )
                            ->when(
                                $data['issued_until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('issued_date', '<=', $date),
                            );
                    }),
                Tables\Filters\Filter::make('overdue')
                    ->query(fn (Builder $query): Builder => $query->where('status', 'PENDING')
                        ->whereNotNull('due_date')
                        ->where('due_date', '<', now())),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('mark_paid')
                    ->label('Mark as Paid')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->form([
                        Forms\Components\Select::make('paid_by')
                            ->label('Paid By')
                            ->options([
                                'MERCHANT' => 'Merchant',
                                'PLATFORM' => 'Platform',
                            ])
                            ->required(),
                        Forms\Components\DatePicker::make('paid_date')
                            ->label('Payment Date')
                            ->default(now())
                            ->required(),
                        Forms\Components\TextInput::make('reference')
                            ->label('Payment Reference'),
                    ])
                    ->action(function (MerchantFine $record, array $data): void {
                        $record->markAsPaid($data['paid_by'], $data['paid_date']);
                        if (isset($data['reference']) && !empty($data['reference'])) {
                            $record->update(['reference' => $data['reference']]);
                        }
                    })
                    ->visible(fn (MerchantFine $record): bool => $record->status === 'PENDING'),
                Tables\Actions\Action::make('mark_disputed')
                    ->label('Mark as Disputed')
                    ->icon('heroicon-o-chat-bubble-left')
                    ->color('info')
                    ->form([
                        Forms\Components\Textarea::make('notes')
                            ->label('Dispute Reason')
                            ->required(),
                    ])
                    ->action(function (MerchantFine $record, array $data): void {
                        $record->markAsDisputed($data['notes']);
                    })
                    ->visible(fn (MerchantFine $record): bool => $record->status === 'PENDING'),
                Tables\Actions\Action::make('mark_waived')
                    ->label('Mark as Waived')
                    ->icon('heroicon-o-x-mark')
                    ->color('gray')
                    ->form([
                        Forms\Components\Textarea::make('notes')
                            ->label('Waiver Reason')
                            ->required(),
                    ])
                    ->action(function (MerchantFine $record, array $data): void {
                        $record->markAsWaived($data['notes']);
                    })
                    ->visible(fn (MerchantFine $record): bool => $record->status === 'PENDING'),
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
            'index' => Pages\ListMerchantFines::route('/'),
            'create' => Pages\CreateMerchantFine::route('/create'),
            'edit' => Pages\EditMerchantFine::route('/{record}/edit'),
        ];
    }
}
