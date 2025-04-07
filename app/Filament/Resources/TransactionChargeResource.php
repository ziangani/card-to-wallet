<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TransactionChargeResource\Pages;
use App\Models\TransactionCharge;
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

class TransactionChargeResource extends Resource
{
    protected static ?string $model = TransactionCharge::class;

    protected static ?string $navigationIcon = 'heroicon-o-receipt-percent';

    protected static ?string $navigationGroup = 'Finance';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Transaction Charge Details')
                    ->schema([
                        Forms\Components\Select::make('transaction_id')
                            ->relationship('transaction', 'uuid')
                            ->searchable()
                            ->preload()
                            ->disabled(),
                        Forms\Components\TextInput::make('charge_id')
                            ->disabled(),
                        Forms\Components\TextInput::make('charge_name')
                            ->disabled(),
                        Forms\Components\TextInput::make('charge_type')
                            ->disabled(),
                        Forms\Components\TextInput::make('charge_value')
                            ->disabled()
                            ->numeric(),
                        Forms\Components\TextInput::make('base_amount')
                            ->disabled()
                            ->numeric()
                            ->prefix('ZMW'),
                        Forms\Components\TextInput::make('calculated_amount')
                            ->disabled()
                            ->numeric()
                            ->prefix('ZMW'),
                        Forms\Components\TextInput::make('merchant_id')
                            ->disabled(),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('transaction.uuid')
                    ->label('Transaction ID')
                    ->searchable(),
                Tables\Columns\TextColumn::make('charge_name')
                    ->badge()
                    ->searchable(),
                Tables\Columns\TextColumn::make('charge_type')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'PERCENTAGE' => 'info',
                        'FIXED' => 'success',
                        default => 'gray',
                    })
                    ->searchable(),
                Tables\Columns\TextColumn::make('charge_value')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('base_amount')
                    ->money('ZMW')
                    ->sortable(),
                Tables\Columns\TextColumn::make('calculated_amount')
                    ->money('ZMW')
                    ->sortable(),
                Tables\Columns\TextColumn::make('merchant_id')
                    ->searchable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('charge_type')
                    ->options([
                        'PERCENTAGE' => 'Percentage',
                        'FIXED' => 'Fixed',
                    ]),
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
            'index' => Pages\ListTransactionCharges::route('/'),
            'view' => Pages\ViewTransactionCharge::route('/{record}'),
        ];
    }    
}
