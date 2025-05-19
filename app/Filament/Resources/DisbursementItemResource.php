<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DisbursementItemResource\Pages;
use App\Models\DisbursementItem;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class DisbursementItemResource extends Resource
{
    protected static ?string $model = DisbursementItem::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    
    protected static ?string $navigationGroup = 'Finance';
    
    protected static ?string $navigationLabel = 'Disbursement Items';
    
    protected static ?int $navigationSort = 4;
    
    // Hide from the main navigation as it will be accessed via BulkDisbursement
    public static function shouldRegisterNavigation(): bool
    {
        return false;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Item Details')
                    ->schema([
                        Forms\Components\TextInput::make('recipient_name')
                            ->required()
                            ->maxLength(255)
                            ->disabled(fn ($record) => $record && $record->status !== 'pending'),
                            
                        Forms\Components\TextInput::make('wallet_number')
                            ->required()
                            ->maxLength(255)
                            ->disabled(fn ($record) => $record && $record->status !== 'pending'),
                            
                        Forms\Components\Select::make('wallet_provider_id')
                            ->relationship('walletProvider', 'name')
                            ->required()
                            ->disabled(fn ($record) => $record && $record->status !== 'pending'),
                            
                        Forms\Components\TextInput::make('amount')
                            ->numeric()
                            ->prefix('ZMW')
                            ->required()
                            ->disabled(fn ($record) => $record && $record->status !== 'pending'),
                            
                        Forms\Components\TextInput::make('fee')
                            ->numeric()
                            ->prefix('ZMW')
                            ->disabled(),
                            
                        Forms\Components\TextInput::make('reference')
                            ->maxLength(255)
                            ->disabled(),
                            
                        Forms\Components\Select::make('status')
                            ->options([
                                'pending' => 'Pending',
                                'processing' => 'Processing',
                                'completed' => 'Completed',
                                'failed' => 'Failed',
                            ])
                            ->disabled(),
                            
                        Forms\Components\Textarea::make('error_message')
                            ->maxLength(1000)
                            ->columnSpanFull()
                            ->disabled(),
                    ])
                    ->columns(2),
                    
                Forms\Components\Section::make('Bulk Disbursement')
                    ->schema([
                        Forms\Components\Select::make('bulk_disbursement_id')
                            ->relationship('bulkDisbursement', 'name')
                            ->disabled(),
                    ])
                    ->collapsed(),
                    
                Forms\Components\Section::make('Transaction')
                    ->schema([
                        Forms\Components\Select::make('transaction_id')
                            ->relationship('transaction', 'reference')
                            ->disabled(),
                    ])
                    ->collapsed(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('row_number')
                    ->label('#')
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('recipient_name')
                    ->searchable()
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('wallet_number')
                    ->searchable()
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('walletProvider.name')
                    ->label('Provider')
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('amount')
                    ->money('ZMW')
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('fee')
                    ->money('ZMW')
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'gray',
                        'processing' => 'info',
                        'completed' => 'success',
                        'failed' => 'danger',
                    })
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('reference')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                    
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                    
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'processing' => 'Processing',
                        'completed' => 'Completed',
                        'failed' => 'Failed',
                    ]),
                    
                Tables\Filters\SelectFilter::make('wallet_provider_id')
                    ->relationship('walletProvider', 'name')
                    ->label('Wallet Provider'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make()
                    ->visible(fn (DisbursementItem $record): bool => $record->status === 'pending'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->visible(fn (): bool => auth()->user()->hasRole('admin')),
                ]),
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
            'index' => Pages\ListDisbursementItems::route('/'),
            'create' => Pages\CreateDisbursementItem::route('/create'),
            'edit' => Pages\EditDisbursementItem::route('/{record}/edit'),
            'view' => Pages\ViewDisbursementItem::route('/{record}'),
        ];
    }
    
    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();
        
        // If bulk_disbursement_id is provided in the request, filter by it
        if (request()->has('bulk_disbursement_id')) {
            $query->where('bulk_disbursement_id', request()->get('bulk_disbursement_id'));
        }
        
        return $query;
    }
}
