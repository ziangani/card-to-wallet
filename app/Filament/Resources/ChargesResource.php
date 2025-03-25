<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ChargesResource\Pages;
use App\Enums\ChargeName;
use App\Enums\ChargeType;
use App\Enums\PaymentChannel;
use App\Models\Charges;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ChargesResource extends Resource
{
    protected static ?string $model = Charges::class;

    protected static ?string $navigationIcon = 'heroicon-o-currency-dollar';
    protected static ?string $label = 'Charges Configuration';

    protected static ?string $navigationGroup = 'Finance';
    protected static bool $shouldRegisterNavigation = false;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('channel')
                    ->options(PaymentChannel::toArray())
                    ->required(),
                Forms\Components\Select::make('charge_name')
                    ->options(ChargeName::toArray())
                    ->required(),
                Forms\Components\TextInput::make('description')
                    ->maxLength(255),
                Forms\Components\Select::make('charge_type')
                    ->options(ChargeType::toArray())
                    ->required(),
                Forms\Components\TextInput::make('charge_value')
                    ->numeric()
                    ->required()
                    ->step('0.01'),
                Forms\Components\TextInput::make('max_amount')
                    ->numeric()
                    ->step('0.01'),
                Forms\Components\TextInput::make('min_amount')
                    ->numeric()
                    ->step('0.01'),
                Forms\Components\Toggle::make('is_default')
                    ->default(true),
                Forms\Components\Select::make('company_id')
                    ->relationship('company', 'company_name')
                    ->searchable()
                    ->preload(),
                Forms\Components\Select::make('merchant_id')
                    ->relationship(
                        'merchant',
                        'name',
                        fn ($query) => $query->select(['code', 'name'])
                    )
                    ->getOptionLabelFromRecordUsing(fn ($record) => "{$record->name} ({$record->code})")
                    ->searchable(['name', 'code'])
                    ->preload(),
                Forms\Components\Toggle::make('is_active')
                    ->default(true),
            ])
            ->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('channel')
                    ->searchable(),
                Tables\Columns\TextColumn::make('charge_name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('charge_type'),
                Tables\Columns\TextColumn::make('charge_value')
                    ->numeric(
                        decimalPlaces: 2,
                        thousandsSeparator: ',',
                    ),
                // Tables\Columns\TextColumn::make('max_amount')
                //     ->numeric(
                //         decimalPlaces: 2,
                //         thousandsSeparator: ',',
                //     ),
                // Tables\Columns\TextColumn::make('min_amount')
                //     ->numeric(
                //         decimalPlaces: 2,
                //         thousandsSeparator: ',',
                //     ),
                Tables\Columns\IconColumn::make('is_default')
                    ->boolean(),
                Tables\Columns\TextColumn::make('company.company_name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('merchant.name')
                    ->searchable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->boolean(),
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
                Tables\Filters\SelectFilter::make('channel')
                    ->options(PaymentChannel::toArray()),
                Tables\Filters\SelectFilter::make('charge_type')
                    ->options(ChargeType::toArray()),
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Active Status'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
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
            'index' => Pages\ListCharges::route('/'),
            'create' => Pages\CreateCharges::route('/create'),
            'edit' => Pages\EditCharges::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes();
    }
}
