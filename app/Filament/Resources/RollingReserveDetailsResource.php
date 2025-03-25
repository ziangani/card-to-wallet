<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RollingReserveDetailsResource\Pages;
use App\Models\MerchantReconciliation;
use App\Models\Merchants;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Route;

class RollingReserveDetailsResource extends Resource
{
    protected static ?string $model = MerchantReconciliation::class;

    protected static ?string $navigationIcon = 'heroicon-o-banknotes';
    protected static ?string $navigationGroup = 'Finance';
    protected static ?int $navigationSort = 26;
    protected static ?string $navigationLabel = 'Rolling Reserve Details';
    protected static ?string $modelLabel = 'Rolling Reserve Detail';
    protected static ?string $pluralModelLabel = 'Rolling Reserve Details';
    protected static ?string $slug = 'rolling-reserve-details';
    protected static bool $shouldRegisterNavigation = false;

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery()
            ->where('status', 'ACTIVE');
            
        // Filter by merchant if provided in the route
        if (Route::current() && Route::current()->hasParameter('merchant')) {
            $merchantId = Route::current()->parameter('merchant');
            $query->where('merchant_id', $merchantId);
        }
        
        return $query;
    }

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
                Tables\Columns\TextColumn::make('merchant_id')
                    ->label('Merchant ID')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('merchant.name')
                    ->label('Merchant Name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('date')
                    ->label('Reconciliation Date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('rolling_reserve')
                    ->label('Reserve Collected')
                    ->numeric(
                        decimalPlaces: 2,
                        thousandsSeparator: ',',
                    )
                    ->sortable(),
                Tables\Columns\TextColumn::make('return_reserve')
                    ->label('Reserve Returned')
                    ->numeric(
                        decimalPlaces: 2,
                        thousandsSeparator: ',',
                    )
                    ->sortable(),
                Tables\Columns\TextColumn::make('originalReserveDate')
                    ->label('Original Reserve Date')
                    ->getStateUsing(function (MerchantReconciliation $record): string {
                        // Calculate the original date (120 days before)
                        return $record->date->copy()->subDays(120)->format('Y-m-d');
                    })
                    ->sortable(false),
                Tables\Columns\TextColumn::make('payoutStatus')
                    ->label('Payout Status')
                    ->getStateUsing(function (MerchantReconciliation $record): string {
                        $payout = $record->getRollingReserveReturnPayout();
                        return $payout ? $payout->status : 'NOT_CREATED';
                    })
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'COMPLETED' => 'success',
                        'PENDING' => 'warning',
                        'FAILED' => 'danger',
                        'CANCELLED' => 'gray',
                        'NOT_CREATED' => 'info',
                        default => 'gray',
                    }),
            ])
            ->defaultSort('date', 'desc')
            ->filters([
                Tables\Filters\Filter::make('date')
                    ->form([
                        Forms\Components\DatePicker::make('from_date')
                            ->label('From Date'),
                        Forms\Components\DatePicker::make('to_date')
                            ->label('To Date'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['from_date'],
                                fn (Builder $query, $date): Builder => $query->whereDate('date', '>=', $date),
                            )
                            ->when(
                                $data['to_date'],
                                fn (Builder $query, $date): Builder => $query->whereDate('date', '<=', $date),
                            );
                    }),
                Tables\Filters\Filter::make('has_return_reserve')
                    ->label('Has Return Reserve')
                    ->query(function (Builder $query): Builder {
                        return $query->where('return_reserve', '>', 0);
                    }),
                Tables\Filters\Filter::make('no_payout')
                    ->label('No Payout Created')
                    ->query(function (Builder $query): Builder {
                        return $query->where('return_reserve', '>', 0)
                            ->whereNotExists(function ($query) {
                                $query->select('id')
                                    ->from('merchant_payouts')
                                    ->whereColumn('merchant_id', 'merchant_reconciliations.merchant_id')
                                    ->whereColumn('created_at', 'merchant_reconciliations.date')
                                    ->where('type', 'ROLLING_RESERVE_RETURN');
                            });
                    }),
            ])
            ->actions([
                Tables\Actions\Action::make('create_payout')
                    ->label('Create Payout')
                    ->icon('heroicon-o-banknotes')
                    ->color('success')
                    ->url(fn (MerchantReconciliation $record): string => 
                        route('filament.backend.resources.merchant-payouts.create', [
                            'merchant_id' => $record->merchant_id,
                            'amount' => $record->return_reserve,
                            'type' => 'ROLLING_RESERVE_RETURN',
                            'notes' => "Rolling reserve return for {$record->date->format('Y-m-d')}",
                        ])
                    )
                    ->openUrlInNewTab()
                    ->visible(function (MerchantReconciliation $record): bool {
                        return $record->return_reserve > 0 && !$record->getRollingReserveReturnPayout();
                    }),
                Tables\Actions\Action::make('view_payout')
                    ->label('View Payout')
                    ->icon('heroicon-o-eye')
                    ->color('info')
                    ->url(function (MerchantReconciliation $record): ?string {
                        $payout = $record->getRollingReserveReturnPayout();
                        if (!$payout) return null;
                        
                        return route('filament.backend.resources.merchant-payouts.edit', ['record' => $payout->id]);
                    })
                    ->openUrlInNewTab()
                    ->visible(function (MerchantReconciliation $record): bool {
                        $payout = $record->getRollingReserveReturnPayout();
                        return $payout !== null;
                    }),
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
            'index' => Pages\ListRollingReserveDetails::route('/'),
        ];
    }
}
