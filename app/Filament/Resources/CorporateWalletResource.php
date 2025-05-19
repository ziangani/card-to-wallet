<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CorporateWalletResource\Pages;
use App\Filament\Resources\CorporateWalletResource\RelationManagers;
use App\Models\CorporateWallet;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\DB;
use Filament\Notifications\Notification;

class CorporateWalletResource extends Resource
{
    protected static ?string $model = CorporateWallet::class;

    protected static ?string $navigationIcon = 'heroicon-o-wallet';
    
    protected static ?string $navigationGroup = 'Corporate';
    
    protected static ?string $navigationLabel = 'Corporate Wallets';
    
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Wallet Information')
                    ->schema([
                        Forms\Components\Select::make('company_id')
                            ->relationship('company', 'name')
                            ->required()
                            ->searchable(),
                            
                        Forms\Components\TextInput::make('balance')
                            ->numeric()
                            ->prefix('ZMW')
                            ->disabled()
                            ->dehydrated(false),
                            
                        Forms\Components\TextInput::make('currency')
                            ->maxLength(3)
                            ->default('ZMW')
                            ->disabled(),
                            
                        Forms\Components\Select::make('status')
                            ->options([
                                'active' => 'Active',
                                'suspended' => 'Suspended',
                                'inactive' => 'Inactive',
                            ])
                            ->required(),
                    ])
                    ->columns(2),
                    
                Forms\Components\Section::make('Manual Adjustment')
                    ->schema([
                        Forms\Components\TextInput::make('adjustment_amount')
                            ->numeric()
                            ->prefix('ZMW')
                            ->label('Amount')
                            ->helperText('Enter a positive value for deposit, negative for withdrawal')
                            ->dehydrated(false),
                            
                        Forms\Components\TextInput::make('adjustment_description')
                            ->label('Description')
                            ->maxLength(255)
                            ->dehydrated(false),
                            
                        Forms\Components\TextInput::make('adjustment_reference')
                            ->label('Reference')
                            ->maxLength(255)
                            ->dehydrated(false),
                    ])
                    ->columns(3),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('company.name')
                    ->label('Company')
                    ->searchable()
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('balance')
                    ->money('ZMW')
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('currency')
                    ->searchable()
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'active' => 'success',
                        'suspended' => 'danger',
                        'inactive' => 'gray',
                        default => 'gray',
                    })
                    ->sortable(),
                    
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
                        'active' => 'Active',
                        'suspended' => 'Suspended',
                        'inactive' => 'Inactive',
                    ]),
                    
                Tables\Filters\SelectFilter::make('company')
                    ->relationship('company', 'name')
                    ->searchable(),
                    
                Tables\Filters\Filter::make('balance')
                    ->form([
                        Forms\Components\TextInput::make('min_balance')
                            ->numeric()
                            ->label('Minimum Balance'),
                        Forms\Components\TextInput::make('max_balance')
                            ->numeric()
                            ->label('Maximum Balance'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['min_balance'],
                                fn (Builder $query, $amount): Builder => $query->where('balance', '>=', $amount),
                            )
                            ->when(
                                $data['max_balance'],
                                fn (Builder $query, $amount): Builder => $query->where('balance', '<=', $amount),
                            );
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make()
                    ->mutateFormDataUsing(function (array $data, CorporateWallet $record): array {
                        // Handle manual adjustment if provided
                        if (isset($data['adjustment_amount']) && $data['adjustment_amount'] !== null && $data['adjustment_amount'] !== '') {
                            $amount = floatval($data['adjustment_amount']);
                            $description = $data['adjustment_description'] ?? 'Manual adjustment';
                            $reference = $data['adjustment_reference'] ?? 'MANUAL-' . time();
                            
                            DB::beginTransaction();
                            
                            try {
                                if ($amount > 0) {
                                    // Deposit
                                    $record->deposit(
                                        $amount,
                                        $description,
                                        $reference,
                                        1 // Default admin user ID
                                    );
                                } else {
                                    // Withdrawal
                                    $result = $record->withdraw(
                                        abs($amount),
                                        $description,
                                        $reference,
                                        1 // Default admin user ID
                                    );
                                    
                                    if (!$result) {
                                        throw new \Exception('Insufficient balance for withdrawal');
                                    }
                                }
                                
                                DB::commit();
                                
                                Notification::make()
                                    ->title('Wallet adjusted successfully')
                                    ->success()
                                    ->send();
                                    
                            } catch (\Exception $e) {
                                DB::rollBack();
                                
                                Notification::make()
                                    ->title('Error adjusting wallet')
                                    ->body($e->getMessage())
                                    ->danger()
                                    ->send();
                            }
                        }
                        
                        return $data;
                    }),
                Tables\Actions\Action::make('deposit')
                    ->label('Deposit')
                    ->icon('heroicon-o-plus-circle')
                    ->color('success')
                    ->form([
                        Forms\Components\TextInput::make('amount')
                            ->numeric()
                            ->required()
                            ->minValue(0.01)
                            ->prefix('ZMW'),
                            
                        Forms\Components\TextInput::make('description')
                            ->required()
                            ->maxLength(255),
                            
                        Forms\Components\TextInput::make('reference')
                            ->maxLength(255),
                    ])
                    ->action(function (CorporateWallet $record, array $data): void {
                        $record->deposit(
                            $data['amount'],
                            $data['description'],
                            $data['reference'] ?? 'DEPOSIT-' . time(),
                            1 // Default admin user ID
                        );
                        
                        Notification::make()
                            ->title('Deposit successful')
                            ->success()
                            ->send();
                    }),
                Tables\Actions\Action::make('withdraw')
                    ->label('Withdraw')
                    ->icon('heroicon-o-minus-circle')
                    ->color('danger')
                    ->form([
                        Forms\Components\TextInput::make('amount')
                            ->numeric()
                            ->required()
                            ->minValue(0.01)
                            ->prefix('ZMW'),
                            
                        Forms\Components\TextInput::make('description')
                            ->required()
                            ->maxLength(255),
                            
                        Forms\Components\TextInput::make('reference')
                            ->maxLength(255),
                    ])
                    ->action(function (CorporateWallet $record, array $data): void {
                        $result = $record->withdraw(
                            $data['amount'],
                            $data['description'],
                            $data['reference'] ?? 'WITHDRAW-' . time(),
                            1 // Default admin user ID
                        );
                        
                        if ($result) {
                            Notification::make()
                                ->title('Withdrawal successful')
                                ->success()
                                ->send();
                        } else {
                            Notification::make()
                                ->title('Withdrawal failed')
                                ->body('Insufficient balance')
                                ->danger()
                                ->send();
                        }
                    }),
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
            RelationManagers\TransactionsRelationManager::class,
            RelationManagers\BulkDisbursementsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCorporateWallets::route('/'),
            'create' => Pages\CreateCorporateWallet::route('/create'),
            'view' => Pages\ViewCorporateWallet::route('/{record}'),
            'edit' => Pages\EditCorporateWallet::route('/{record}/edit'),
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
