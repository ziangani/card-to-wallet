<?php

namespace App\Filament\Resources\Backend;

use App\Common\Helpers;
use App\Filament\Resources\Backend\ApiLogsResource\Pages;
use App\Models\ApiLogs;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Filament\Facades\Filament;

class ApiLogsResource extends Resource
{
    protected static ?string $model = ApiLogs::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup = 'Settings';
    protected static ?int $navigationSort = 100;

    public static function shouldRegisterNavigation(): bool
    {
        return Filament::auth()->user()?->isSysAdmin() ?? false;
    }

    public static function canAccess(): bool
    {
        return Filament::auth()->user()?->isSysAdmin() ?? false;
    }

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
                Tables\Columns\TextColumn::make('created_at')->date('d-m-Y H:i:s')->label('Date')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('request_time')
                    ->state(function (Model $record) {
                        return Helpers::diffInSeconds($record->request_time, $record->response_time);
                    })->label('Time Taken(s)')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('request_status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'COMPLETE', 'SUCCESS' => 'success',
                        'FAILED' => 'danger',
                        default => 'info',
                    })
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('source_ip')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('source_reference')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('request_type')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('request')->limit(20)
                    ->copyable()
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('response')->limit(20)->copyable()
                    ->searchable()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('request_status')
                    ->options([
                        'COMPLETE' => 'Complete',
                        'SUCCESS' => 'Success',
                        'FAILED' => 'Failed',
                    ]),

            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])->persistFiltersInSession()
            ->filtersApplyAction(
                fn(Action $action) => $action
                    ->link()
                    ->label('Save filters to table'),
            )->defaultSort('created_at', 'desc');
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
            'index' => Pages\ListApiLogs::route('/'),
            'create' => Pages\CreateApiLogs::route('/create'),
            'view' => Pages\ViewApiLogs::route('/{record}'),
            'edit' => Pages\EditApiLogs::route('/{record}/edit'),
        ];
    }
}
