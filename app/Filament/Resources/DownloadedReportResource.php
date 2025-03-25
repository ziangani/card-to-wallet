<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DownloadedReportResource\Pages;
use App\Models\DownloadedReport;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Storage;
use Filament\Tables\Actions\Action;

class DownloadedReportResource extends Resource
{
    protected static ?string $model = DownloadedReport::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $label = 'Settlement Files';

    protected static ?string $navigationGroup = 'Reports';
    protected static bool $shouldRegisterNavigation = false;


    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('merchant_id')
                    ->label('Merchant')
                    ->searchable()
                    ->sortable()
                    ->tooltip(fn (DownloadedReport $record) => $record->merchant_id),
                Tables\Columns\TextColumn::make('report_name')
                    ->label('Report')
                    ->searchable()
                    ->sortable()
                    ->tooltip(fn (DownloadedReport $record) => $record->report_name),
                Tables\Columns\TextColumn::make('source_system')
                    ->label('Source')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('report_start_time')
                    ->label('Start Date')
                    ->date('M j, Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('report_end_time')
                    ->label('End Date')
                    ->date('M j, Y')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('source_system')
                    ->options([
                        'CYBERSOURCE' => 'CyberSource',
                        // Add other systems as they're implemented
                    ]),
                Tables\Filters\SelectFilter::make('report_type')
                    ->options([
                        'DailyBatchDetails' => 'Daily Batch',
                        'PaymentBatchDetailReport' => 'Payment Batch',
                        'TransactionRequestReport' => 'Transaction Request',
                        // Add other report types as needed
                    ]),
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'completed' => 'Completed',
                        // Add other statuses if needed
                    ]),
            ])
            ->actions([
                Action::make('download')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->label('Download')
                    ->url(fn (DownloadedReport $record) => route('reports.download', $record))
                    ->openUrlInNewTab()
                    ->visible(fn (DownloadedReport $record) => Storage::exists($record->getFullFilePath())),
            ])
            ->defaultSort('report_start_time', 'desc')
            ->bulkActions([])
            ->poll('30s');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListDownloadedReports::route('/'),
        ];
    }
}
