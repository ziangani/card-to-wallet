<?php

namespace App\Filament\Resources\Backend;

use App\Common\GeneralStatus;
use App\Filament\Resources\Backend;
use App\Filament\Resources\Backend\MerchantsResource\Pages;
use App\Filament\Resources\Backend\MerchantsResource\RelationManagers;
use App\Models\Merchants;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class MerchantsResource extends Resource
{
    protected static ?string $model = Merchants::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-office-2';
    protected static ?string $navigationLabel = 'Merchants';
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Merchant Information')
                    ->schema([
                        Forms\Components\BelongsToSelect::make('company_id')
                            ->relationship('company', 'company_name')
                            ->required(),
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('code')
                            ->required()
                            ->prefix('FN')
                            ->unique(ignoreRecord: true)
                            ->maxLength(255),
                        Forms\Components\TextInput::make('description')
                            ->maxLength(255),
                        Forms\Components\Select::make('primary_channel')
                            ->options(Merchants::CHANNELS)
                            ->nullable(),

                    ])->columns(2)->columnSpan(1),

                Forms\Components\Section::make('Misc Details')
                    ->schema([
                        Forms\Components\Select::make('status')
                            ->options(
                                Merchants::STATUSES
                            )->required()->default(Merchants::STATUS_ACTIVE),
                        Forms\Components\FileUpload::make('logo')
                            ->image()
//                            ->required(),
                    ])->columnSpan(1)->columns(1),
            ])->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('created_at')
                    ->date('Y-m-d'),
                Tables\Columns\TextColumn::make('code')
                ->searchable(),
                Tables\Columns\TextColumn::make('name')
                ->searchable(),
                Tables\Columns\TextColumn::make('company.company_name')
                    ->label('Company'),
                Tables\Columns\TextColumn::make('primary_channel')
                    ->badge(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        GeneralStatus::STATUS_ACTIVE => 'success',
                        GeneralStatus::STATUS_DISABLED => 'danger',
                        default => 'warning',
                    }),
                Tables\Columns\TextColumn::make('updated_at'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])->defaultSort('created_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\ApisRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Backend\MerchantsResource\Pages\ListMerchants::route('/'),
            'create' => Backend\MerchantsResource\Pages\CreateMerchants::route('/create'),
            'view' => Backend\MerchantsResource\Pages\ViewMerchants::route('/{record}'),
            'edit' => Backend\MerchantsResource\Pages\EditMerchants::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        /** @var class-string<Model> $modelClass */
        $modelClass = static::$model;

        return (string)$modelClass::where('status', Merchants::STATUS_ACTIVE)->count();
    }
}
