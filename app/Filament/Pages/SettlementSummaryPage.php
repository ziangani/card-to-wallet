<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Forms\Components\DatePicker;
use Illuminate\Support\Facades\DB;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Illuminate\Database\Eloquent\Builder;
use App\Models\SettlementReports;
use App\Models\Merchants;
use App\Models\SettlementSummary;
use App\Filament\Exports\SettlementSummaryExporter;
use Filament\Actions\Exports\Enums\ExportFormat;

class SettlementSummaryPage extends Page implements HasTable
{
    use InteractsWithTable;

    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';
    protected static ?string $navigationLabel = 'Settlement Summary';
    protected static ?string $navigationGroup = 'Reports';
    protected static ?int $navigationSort = 1;
    protected static bool $shouldRegisterNavigation = false;
    protected static string $view = 'filament.pages.settlement-summary-page';

    public function table(Table $table): Table
    {
        $baseQuery = SettlementSummary::query()
            ->select([
                'merchant',
                'merchant_name',
                'currency',
                DB::raw('sum(debit_value) as debit_value'),
                DB::raw('sum(credit_value) as credit_value'),
                DB::raw('sum(net_settlement) as net_settlement')
            ])
            ->groupBy('merchant', 'currency', 'merchant_name')
            ->orderByRaw('sum(debit_value) desc');

        // Log the SQL query for debugging
        \Illuminate\Support\Facades\Log::info('Settlement Summary Query:', [
            'sql' => $baseQuery->toSql(),
            'bindings' => $baseQuery->getBindings()
        ]);

        return $table
            ->query(clone $baseQuery)
            ->filters([
                Filter::make('settlement_date')
                    ->form([
                        DatePicker::make('from')
                            ->label('From Date')
                            ->default(null),
                        DatePicker::make('until')
                            ->label('To Date')
                            ->default(null),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('settlement_date', '>=', $date)
                            )
                            ->when(
                                $data['until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('settlement_date', '<=', $date)
                            );
                    }),
                SelectFilter::make('merchant')
                    ->options(fn () => Merchants::pluck('name', 'code')->toArray())
                    ->query(function (Builder $query, $state): Builder {
                        return $query->when(
                            $state,
                            fn (Builder $query, $state): Builder => $query->where('merchant', $state)
                        );
                    }),
                SelectFilter::make('currency')
                    ->options(fn () => SettlementSummary::distinct()->pluck('currency', 'currency')->toArray())
                    ->query(function (Builder $query, $state): Builder {
                        return $query->when(
                            $state,
                            fn (Builder $query, $state): Builder => $query->where('currency', $state)
                        );
                    }),
            ])
            ->defaultSort('debit_value', 'desc')
            ->striped()
            ->columns([
                TextColumn::make('merchant')
                    ->label('Merchant Code')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('merchant_name')
                    ->label('Merchant Name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('currency')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('debit_value')
                    ->label('Debit Value')
                    ->formatStateUsing(fn ($state, $record) => $record->currency . ' ' . number_format($state, 2))
                    ->sortable()
                    ->summarize([
                        \Filament\Tables\Columns\Summarizers\Summarizer::make()
                            ->label('Total')
                            ->using(fn ($records): float => $records->sum('debit_value'))
                            ->formatStateUsing(fn ($state, $records) => $records->first()->currency . ' ' . number_format($state, 2))
                    ]),
                TextColumn::make('credit_value')
                    ->label('Credit Value')
                    ->formatStateUsing(fn ($state, $record) => $record->currency . ' ' . number_format($state, 2))
                    ->sortable()
                    ->summarize([
                        \Filament\Tables\Columns\Summarizers\Summarizer::make()
                            ->label('Total')
                            ->using(fn ($records): float => $records->sum('credit_value'))
                            ->formatStateUsing(fn ($state, $records) => $records->first()->currency . ' ' . number_format($state, 2))
                    ]),
                TextColumn::make('net_settlement')
                    ->label('Net Settlement')
                    ->formatStateUsing(fn ($state, $record) => $record->currency . ' ' . number_format($state, 2))
                    ->sortable()
                    ->summarize([
                        \Filament\Tables\Columns\Summarizers\Summarizer::make()
                            ->label('Total')
                            ->using(fn ($records): float => $records->sum('net_settlement'))
                            ->formatStateUsing(fn ($state, $records) => $records->first()->currency . ' ' . number_format($state, 2))
                    ]),
            ])
            ->paginated(false)
            ->headerActions([
                \Filament\Tables\Actions\ExportAction::make()
                    ->exporter(SettlementSummaryExporter::class)
                    ->formats([ExportFormat::Csv])
                    ->fileName(fn () => 'settlement-summary-' . now()->format('Y-m-d'))
            ])
            ->persistFiltersInSession();
    }
}
