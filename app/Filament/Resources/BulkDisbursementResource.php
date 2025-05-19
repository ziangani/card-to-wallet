<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BulkDisbursementResource\Pages;
use App\Filament\Resources\BulkDisbursementResource\RelationManagers;
use App\Models\BulkDisbursement;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Artisan;
use Filament\Notifications\Notification;
use Filament\Support\Enums\IconPosition;

class BulkDisbursementResource extends Resource
{
    protected static ?string $model = BulkDisbursement::class;

    protected static ?string $navigationIcon = 'heroicon-o-banknotes';
    
    protected static ?string $navigationGroup = 'Finance';
    
    protected static ?string $navigationLabel = 'Bulk Disbursements';
    
    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Disbursement Details')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255)
                            ->disabled(fn ($record) => $record && !$record->isDraft()),
                            
                        Forms\Components\TextInput::make('reference_number')
                            ->maxLength(255)
                            ->disabled(),
                            
                        Forms\Components\Textarea::make('description')
                            ->maxLength(1000)
                            ->columnSpanFull()
                            ->disabled(fn ($record) => $record && !$record->isDraft()),
                            
                        Forms\Components\TextInput::make('total_amount')
                            ->numeric()
                            ->prefix('ZMW')
                            ->disabled(),
                            
                        Forms\Components\TextInput::make('total_fee')
                            ->numeric()
                            ->prefix('ZMW')
                            ->disabled(),
                            
                        Forms\Components\TextInput::make('transaction_count')
                            ->numeric()
                            ->disabled(),
                            
                        Forms\Components\Select::make('status')
                            ->options([
                                'draft' => 'Draft',
                                'pending_approval' => 'Pending Approval',
                                'approved' => 'Approved',
                                'processing' => 'Processing',
                                'completed' => 'Completed',
                                'partially_completed' => 'Partially Completed',
                                'failed' => 'Failed',
                                'cancelled' => 'Cancelled',
                            ])
                            ->disabled(),
                            
                        Forms\Components\TextInput::make('currency')
                            ->maxLength(3)
                            ->disabled(),
                    ])
                    ->columns(2),
                    
                Forms\Components\Section::make('Timestamps')
                    ->schema([
                        Forms\Components\DateTimePicker::make('created_at')
                            ->label('Created At')
                            ->disabled(),
                            
                        Forms\Components\DateTimePicker::make('approved_at')
                            ->label('Approved At')
                            ->disabled(),
                            
                        Forms\Components\DateTimePicker::make('completed_at')
                            ->label('Completed At')
                            ->disabled(),
                    ])
                    ->columns(3)
                    ->collapsed(),
                    
                Forms\Components\Section::make('Notes')
                    ->schema([
                        Forms\Components\Textarea::make('notes')
                            ->maxLength(1000)
                            ->columnSpanFull(),
                    ])
                    ->collapsed(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('reference_number')
                    ->searchable()
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->limit(30),
                    
                Tables\Columns\TextColumn::make('company.name')
                    ->label('Company')
                    ->searchable()
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('total_amount')
                    ->money('ZMW')
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('transaction_count')
                    ->label('Items')
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'draft' => 'gray',
                        'pending_approval' => 'warning',
                        'approved' => 'info',
                        'processing' => 'info',
                        'completed' => 'success',
                        'partially_completed' => 'warning',
                        'failed' => 'danger',
                        'cancelled' => 'danger',
                    })
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),
                    
                Tables\Columns\TextColumn::make('approved_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                    
                Tables\Columns\TextColumn::make('completed_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'draft' => 'Draft',
                        'pending_approval' => 'Pending Approval',
                        'approved' => 'Approved',
                        'processing' => 'Processing',
                        'completed' => 'Completed',
                        'partially_completed' => 'Partially Completed',
                        'failed' => 'Failed',
                        'cancelled' => 'Cancelled',
                    ]),
                    
                Tables\Filters\Filter::make('created_at')
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
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make()
                    ->visible(fn (BulkDisbursement $record): bool => $record->isDraft()),
                Tables\Actions\Action::make('process')
                    ->label('Process Now')
                    ->icon('heroicon-o-play')
                    ->color('success')
                    ->visible(fn (BulkDisbursement $record): bool => $record->isApproved())
                    ->action(function (BulkDisbursement $record): void {
                        // Start processing
                        $record->startProcessing();
                        
                        // Run the command to process the disbursement
                        Artisan::call('app:process-bulk-disbursements', [
                            '--disbursement_id' => $record->id
                        ]);
                        
                        Notification::make()
                            ->title('Processing started')
                            ->body("Disbursement #{$record->id} is now being processed.")
                            ->success()
                            ->send();
                    }),
                Tables\Actions\Action::make('view_items')
                    ->label('View Items')
                    ->icon('heroicon-o-list-bullet')
                    ->color('info')
                    ->url(fn (BulkDisbursement $record): string => route('filament.backend.resources.disbursement-items.index', ['bulk_disbursement_id' => $record->id]))
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
            // Will implement relation managers later
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBulkDisbursements::route('/'),
            'create' => Pages\CreateBulkDisbursement::route('/create'),
            'edit' => Pages\EditBulkDisbursement::route('/{record}/edit'),
        ];
    }
    
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
