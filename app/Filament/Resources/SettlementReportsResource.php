<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SettlementReportsResource\Pages;
use App\Filament\Resources\SettlementReportsResource\RelationManagers;
use App\Models\SettlementReports;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Actions\ExportAction;
use Filament\Actions\Exports\Models\Export;
use Filament\Actions\Exports\Enums\ExportFormat;
use App\Filament\Exports\SettlementReportExporter;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Support\Carbon;

class SettlementReportsResource extends Resource
{
    protected static ?string $model = SettlementReports::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $label = 'Settlement';
    protected static ?string $navigationGroup = 'Reports';

    protected static bool $shouldRegisterNavigation = false;


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('merchant')
                    ->searchable()
                    ->label('Merchant ID')
                    ->sortable(),

                Tables\Columns\TextColumn::make('merchants.name')
                    ->label('Merchant')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('settlement_date')
                    ->label('Settlement Date')
                    ->date('Y-m-d')
                    ->searchable()
                    ->sortable(),

//                Tables\Columns\TextColumn::make('start_time')
//                    ->searchable()
//                    ->sortable()
//                    ->dateTime(),
//
//                Tables\Columns\TextColumn::make('end_time')
//                    ->searchable()
//                    ->sortable()
//                    ->dateTime(),

                Tables\Columns\TextColumn::make('currency')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('volume')
                    ->label('Sales Volume')
                    ->searchable()
                    ->numeric(0, '.', ',')
                    ->sortable(),

                Tables\Columns\TextColumn::make('value')
                    ->label('Sales Value')
                    ->searchable()
                    ->numeric(2, '.', ',')
                    ->sortable(),

                Tables\Columns\TextColumn::make('credit_volume')
                    ->searchable()
                    ->numeric(0, '.', ',')
                    ->sortable(),

                Tables\Columns\TextColumn::make('credit_value')
                    ->searchable()
                    ->numeric(2, '.', ',')
                    ->sortable(),

//                Tables\Columns\TextColumn::make('net_settlement')
//                    ->searchable()
//                    ->numeric(2, '.', ',')
//                    ->sortable(),
            ])
            ->filters([
                Filter::make('settlement_date')
                    ->form([
                        Forms\Components\DatePicker::make('from'),
                        Forms\Components\DatePicker::make('until'),
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
                    ->relationship('merchants', 'name')
                    ->searchable()
                    ->preload(),
                SelectFilter::make('currency')
                    ->options(fn () => SettlementReports::distinct()->pluck('currency', 'currency')->toArray())
                    ->searchable()
                    ->preload(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->headerActions([
                Tables\Actions\ExportAction::make()
                    ->exporter(SettlementReportExporter::class)
                    ->formats([ExportFormat::Csv])
                    ->fileName(fn () => 'settlement-reports-' . now()->format('Y-m-d')),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])->defaultSort('settlement_date', 'desc');
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
            'index' => Pages\ListSettlementReports::route('/'),
            'create' => Pages\CreateSettlementReports::route('/create'),
            'edit' => Pages\EditSettlementReports::route('/{record}/edit'),
        ];
    }
}
