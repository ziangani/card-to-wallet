<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CompanyDetailsResource\Pages;
use App\Models\CompanyDetail;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\BadgeColumn;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CompanyDetailsResource extends Resource
{
    protected static ?string $model = CompanyDetail::class;

    protected static ?string $navigationLabel = 'Company Profiles';

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $modelLabel = 'Company Detail';

    protected static ?string $pluralModelLabel = 'Company Profiles';

//    protected static ?string $navigationGroup = 'Settings';
    protected static bool $shouldRegisterNavigation = false;


    protected static ?int $navigationSort = 1;

    public static function form(Forms\Form $form): Forms\Form
    {
        return $form
            ->schema([
                Forms\Components\Card::make()
                    ->schema([
                        Forms\Components\TextInput::make('company_name')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('trading_name')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('type_of_ownership')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('rc_number')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('tpin')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\DatePicker::make('date_registered')
                            ->required(),
                        Forms\Components\TextInput::make('nature_of_business')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\Textarea::make('office_address')
                            ->required()
                            ->maxLength(65535),
                        Forms\Components\Textarea::make('postal_address')
                            ->required()
                            ->maxLength(65535),
                        Forms\Components\TextInput::make('country_of_incorporation')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('office_telephone')
                            ->required()
                            ->tel()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('customer_service_telephone')
                            ->tel()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('official_email')
                            ->required()
                            ->email()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('customer_service_email')
                            ->email()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('official_website')
                            ->url()
                            ->maxLength(255),
                        Forms\Components\Select::make('status')
                            ->options([
                                'PENDING_APPROVAL' => 'Pending Approval',
                                'APPROVED' => 'Approved',
                                'REJECTED' => 'Rejected',
                            ])
                            ->required()
                            ->default('PENDING_APPROVAL'),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->columns([

                Tables\Columns\TextColumn::make('date_registered')
                    ->date('d-M-Y')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('company_name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('rc_number')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('type_of_ownership')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('official_email')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('office_telephone')
                    ->searchable()
                    ->sortable(),
                    BadgeColumn::make('status')
                    ->colors([
                        'warning' => fn ($state) => $state === 'PENDING_APPROVAL',
                        'success' => fn ($state) => $state === 'APPROVED',
                        'danger' => fn ($state) => $state === 'REJECTED',
                    ])
                    ->searchable()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
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
            'index' => Pages\ListCompanyDetails::route('/'),
            'create' => Pages\CreateCompanyDetails::route('/create'),
            'view' => Pages\ViewCompanyDetails::route('/{record}'),
            'edit' => Pages\EditCompanyDetails::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return self::$model::query()->count();
    }
}
