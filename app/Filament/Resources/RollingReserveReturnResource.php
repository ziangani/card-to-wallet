<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RollingReserveReturnResource\Pages;
use App\Models\MerchantReconciliation;
use App\Models\MerchantPayout;
use App\Models\Merchants;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class RollingReserveReturnResource extends Resource
{
    protected static ?string $model = Merchants::class;

    protected static ?string $navigationIcon = 'heroicon-o-banknotes';
    protected static ?string $navigationGroup = 'Finance';
    protected static ?int $navigationSort = 25;
    protected static ?string $navigationLabel = 'Rolling Reserve Summary';
    protected static ?string $modelLabel = 'Merchant Rolling Reserve';
    protected static ?string $pluralModelLabel = 'Merchant Rolling Reserves';
    protected static ?string $slug = 'rolling-reserve-returns';

    protected static bool $shouldRegisterNavigation = false;


    public static function canCreate(): bool
    {
        // Disable creation of new records
        return false;
    }

    public static function canEdit(Model $record): bool
    {
        // Disable editing of records
        return false;
    }

    public static function canDelete(Model $record): bool
    {
        // Disable deletion of records
        return false;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // Empty form since we don't need to create or edit
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('code')
                    ->label('Merchant ID')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('name')
                    ->label('Merchant Name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('total_collected')
                    ->label('Total Reserve Collected')
                    ->getStateUsing(function (Merchants $record): string {
                        $total = MerchantReconciliation::where('merchant_id', $record->code)
                            ->where('status', 'ACTIVE')
                            ->sum('rolling_reserve');
                        return number_format($total, 2);
                    })
                    ->numeric(
                        decimalPlaces: 2,
                        thousandsSeparator: ',',
                    ),
                Tables\Columns\TextColumn::make('total_returned')
                    ->label('Total Reserve Returned')
                    ->getStateUsing(function (Merchants $record): string {
                        $total = MerchantPayout::where('merchant_id', $record->code)
                            ->where('type', 'ROLLING_RESERVE_RETURN')
                            ->where('status', 'COMPLETED')
                            ->sum('amount');
                        return number_format($total, 2);
                    })
                    ->numeric(
                        decimalPlaces: 2,
                        thousandsSeparator: ',',
                    ),
                Tables\Columns\TextColumn::make('pending_return')
                    ->label('Pending Return')
                    ->getStateUsing(function (Merchants $record): string {
                        $total = MerchantReconciliation::where('merchant_id', $record->code)
                            ->where('status', 'ACTIVE')
                            ->where('return_reserve', '>', 0)
                            ->whereNotExists(function ($query) {
                                $query->select(DB::raw(1))
                                    ->from('merchant_payouts')
                                    ->whereColumn('merchant_id', 'merchant_reconciliations.merchant_id')
                                    ->whereColumn('created_at', 'merchant_reconciliations.date')
                                    ->where('type', 'ROLLING_RESERVE_RETURN');
                            })
                            ->sum('return_reserve');
                        return number_format($total, 2);
                    })
                    ->numeric(
                        decimalPlaces: 2,
                        thousandsSeparator: ',',
                    ),
                Tables\Columns\TextColumn::make('net_balance')
                    ->label('Net Balance')
                    ->getStateUsing(function (Merchants $record): string {
                        $collected = MerchantReconciliation::where('merchant_id', $record->code)
                            ->where('status', 'ACTIVE')
                            ->sum('rolling_reserve');

                        $returned = MerchantPayout::where('merchant_id', $record->code)
                            ->where('type', 'ROLLING_RESERVE_RETURN')
                            ->where('status', 'COMPLETED')
                            ->sum('amount');

                        $balance = $collected - $returned;
                        return number_format($balance, 2);
                    })
                    ->numeric(
                        decimalPlaces: 2,
                        thousandsSeparator: ',',
                    )
                    ->color(function (string $state): string {
                        $value = (float) str_replace(',', '', $state);
                        if ($value > 0) return 'success';
                        if ($value < 0) return 'danger';
                        return 'gray';
                    }),
            ])
            ->defaultSort('name', 'asc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'ACTIVE' => 'Active',
                        'DISABLED' => 'Disabled',
                    ])
                    ->attribute('status'),
                Tables\Filters\Filter::make('has_pending_returns')
                    ->label('Has Pending Returns')
                    ->query(function (Builder $query): Builder {
                        return $query->whereHas('reconciliations', function ($query) {
                            $query->where('status', 'ACTIVE')
                                ->where('return_reserve', '>', 0)
                                ->whereNotExists(function ($subQuery) {
                                    $subQuery->select(DB::raw(1))
                                        ->from('merchant_payouts')
                                        ->whereColumn('merchant_id', 'merchant_reconciliations.merchant_id')
                                        ->whereColumn('created_at', 'merchant_reconciliations.date')
                                        ->where('type', 'ROLLING_RESERVE_RETURN');
                                });
                        });
                    }),
            ])
            ->actions([
                Tables\Actions\Action::make('view_details')
                    ->label('View Details')
                    ->icon('heroicon-o-eye')
                    ->color('primary')
                    ->url(fn (Merchants $record): string =>
                        route('filament.backend.resources.rolling-reserve-details.index', ['merchant' => $record->code])
                    ),
            ])
            ->bulkActions([
                // No bulk actions needed
            ]);
    }

    public static function getRelations(): array
    {
        return [
            // No relations needed
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRollingReserveReturns::route('/'),
        ];
    }
}
