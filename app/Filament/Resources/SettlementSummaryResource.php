<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SettlementSummaryResource\Pages;
use App\Models\SettlementSummary;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class SettlementSummaryResource extends Resource
{
    protected static ?string $model = SettlementSummary::class;

    protected static ?string $navigationIcon = 'heroicon-o-calculator';

    protected static ?string $navigationGroup = 'Reports';

    protected static ?string $navigationLabel = 'Settlement Summary';

    protected static bool $shouldRegisterNavigation = false;


    protected static ?int $navigationSort = 2;

    public static function getRecordTitle(?Model $record): string
    {
        return $record ? "{$record->merchant} - {$record->settlement_date}" : '';
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('merchant')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('merchant_name')
                    ->searchable()
                    ->sortable()
                    ->label('Merchant Name'),
                Tables\Columns\TextColumn::make('currency')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('debit_value')
                    ->money('ZMW')
                    ->sortable(),
                Tables\Columns\TextColumn::make('credit_value')
                    ->money('ZMW')
                    ->sortable(),
                Tables\Columns\TextColumn::make('net_settlement')
                    ->money('ZMW')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('merchant')
                    ->options(fn () => SettlementSummary::getMerchantNames())
                    ->searchable(),
                Tables\Filters\Filter::make('settlement_date')
                    ->form([
                        Forms\Components\DatePicker::make('from'),
                        Forms\Components\DatePicker::make('until'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        $query = $query
                            ->when(
                                $data['from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('settlement_date', '>=', $date),
                            )
                            ->when(
                                $data['until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('settlement_date', '<=', $date),
                            );

                        if ($data['from'] || $data['until']) {
                            $query->select([
                                'merchant',
                                'merchant_name',
                                'currency',
                                DB::raw('SUM(debit_value) as debit_value'),
                                DB::raw('SUM(credit_value) as credit_value'),
                                DB::raw('SUM(net_settlement) as net_settlement')
                            ])
                            ->groupBy('merchant', 'merchant_name', 'currency')
                            ->orderByDesc(DB::raw('SUM(debit_value)'));
                        }

                        return $query;
                    })
            ])
            ->defaultSort('debit_value', 'desc')
            ->striped()
            ->paginated([10, 25, 50, 100])
            ->headerActions([
                Tables\Actions\Action::make('export')
                    ->label('Export CSV')
                    ->url(fn ($livewire) => route('settlement-summary.export', [
                        'from' => $livewire->tableFilters['settlement_date']['from'] ?? null,
                        'until' => $livewire->tableFilters['settlement_date']['until'] ?? null,
                        'merchant' => $livewire->tableFilters['merchant']['value'] ?? null,
                    ]))
                    ->openUrlInNewTab()
                    ->icon('heroicon-o-arrow-down-tray'),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSettlementSummaries::route('/'),
        ];
    }
}
