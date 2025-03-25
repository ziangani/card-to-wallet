<?php

namespace App\Filament\Merchant\Resources;

use App\Filament\Merchant\Resources\CashOutsResource\Pages;
use App\Filament\Merchant\Resources\CashOutsResource\RelationManagers;
use App\Filament\Merchant\Widgets\CashOutOverview;
use App\Models\CashOuts;
use App\Models\Transactions;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Filament\Actions;

class CashOutsResource extends Resource
{
    protected static ?string $model = CashOuts::class;

    protected static ?string $navigationIcon = 'heroicon-o-banknotes';

    protected static ?string $navigationLabel = 'Cash Outs';

    protected static ?string $modelLabel = 'Cash Out';

    protected static ?string $pluralModelLabel = 'Cash Outs';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('reference')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('amount')
                    ->required()
                    ->numeric()
                    ->prefix('ZMW'),
                Forms\Components\Select::make('bank_id')
                    ->label('Bank Account')
                    ->options(function () {
                        $companyId = Auth::user()->merchant->company_id;
                        return \App\Models\CompanyBank::where('company_id', $companyId)
                            ->pluck('account_name', 'id');
                    })
                    ->preload()
                    ->searchable()
                    ->required()
                    ->default(function () {
                        $companyId = Auth::user()->merchant->company_id;
                        $bank = \App\Models\CompanyBank::where('company_id', $companyId)->first();
                        return $bank ? $bank->id : null;
                    })
                    ->afterStateUpdated(function ($state, callable $set) {
                        if ($state) {
                            $bank = \App\Models\CompanyBank::find($state);
                            if ($bank) {
                                $set('bank_name', $bank->bank_name);
                                $set('account_type', $bank->account_type);
                                $set('account_number', $bank->account_number);
                                $set('branch_code', $bank->bank_sort_code);
                            }
                        }
                    }),
                Forms\Components\Section::make('Bank Account Details')
                    ->description('These details are automatically filled based on the selected bank account')
                    ->schema([
                        Forms\Components\TextInput::make('bank_name')
                            ->disabled()
                            ->dehydrated(false)
                            ->hiddenOn('create'),
                        Forms\Components\TextInput::make('account_type')
                            ->disabled()
                            ->dehydrated(false)
                            ->hiddenOn('create'),
                        Forms\Components\TextInput::make('account_number')
                            ->disabled()
                            ->dehydrated(false)
                            ->hiddenOn('create'),
                        Forms\Components\TextInput::make('branch_code')
                            ->disabled()
                            ->dehydrated(false)
                            ->hiddenOn('create'),
                    ]),
                Forms\Components\DatePicker::make('date')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('batch_id')
                    ->searchable(),
                Tables\Columns\TextColumn::make('reference')
                    ->searchable(),
                Tables\Columns\TextColumn::make('amount')
                    ->money('ZMW')
                    ->sortable(),
                Tables\Columns\TextColumn::make('fee')
                    ->money('ZMW')
                    ->sortable(),
                Tables\Columns\TextColumn::make('techpay_charge')
                    ->money('ZMW')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('third_party_charge')
                    ->money('ZMW')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('batch_status')
                    ->searchable(),
                Tables\Columns\TextColumn::make('transaction_status')
                    ->searchable(),
                Tables\Columns\TextColumn::make('account_type')
                    ->searchable(),
                Tables\Columns\TextColumn::make('account_number')
                    ->searchable(),
                Tables\Columns\TextColumn::make('bank_name')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('branch_code')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('swift_code')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('date')
                    ->date()
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
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
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
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCashOuts::route('/'),
            'create' => Pages\CreateCashOut::route('/create'),
            'edit' => Pages\EditCashOut::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return Transactions::getAvailableTransactionsCount() > 0
            ? Transactions::getAvailableTransactionsCount()
            : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'success';
    }

    public static function getWidgets(): array
    {
        return [
            CashOutOverview::class,
        ];
    }
}
