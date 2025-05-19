<?php

namespace App\Filament\Resources\CompanyResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Artisan;

class BulkDisbursementsRelationManager extends RelationManager
{
    protected static string $relationship = 'bulkDisbursements';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Textarea::make('description')
                    ->maxLength(1000),
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
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                Tables\Columns\TextColumn::make('reference_number')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->limit(30),
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
                        default => 'gray',
                    })
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),
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
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make()
                    ->visible(fn ($record): bool => $record->isDraft()),
                Tables\Actions\Action::make('process')
                    ->label('Process Now')
                    ->icon('heroicon-o-play')
                    ->color('success')
                    ->visible(fn ($record): bool => $record->isApproved())
                    ->action(function ($record): void {
                        // Start processing
                        $record->startProcessing();
                        
                        // Run the command to process the disbursement
                        Artisan::call('app:process-bulk-disbursements', [
                            '--disbursement_id' => $record->id
                        ]);
                        
                        \Filament\Notifications\Notification::make()
                            ->title('Processing started')
                            ->body("Disbursement #{$record->id} is now being processed.")
                            ->success()
                            ->send();
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->visible(fn (): bool => false), // Disable bulk delete
                ]),
            ]);
    }
}
