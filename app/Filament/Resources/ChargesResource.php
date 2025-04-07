<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ChargesResource\Pages;
use App\Models\Charges;
use App\Enums\PaymentChannel;
use App\Enums\ChargeType;
use App\Enums\ChargeName;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;
use Illuminate\Support\Carbon;

class ChargesResource extends Resource
{
    protected static ?string $model = Charges::class;

    protected static ?string $navigationIcon = 'heroicon-o-currency-dollar';

    protected static ?string $navigationGroup = 'Finance';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Charge Details')
                    ->schema([
                        Forms\Components\Select::make('channel')
                            ->enum(PaymentChannel::class)
                            ->options(PaymentChannel::class)
                            ->required(),
                        Forms\Components\Select::make('charge_name')
                            ->enum(ChargeName::class)
                            ->options(ChargeName::class)
                            ->required(),
                        Forms\Components\Select::make('charge_type')
                            ->enum(ChargeType::class)
                            ->options(ChargeType::class)
                            ->required(),
                        Forms\Components\TextInput::make('charge_value')
                            ->numeric()
                            ->required()
                            ->step(0.01),
                        Forms\Components\TextInput::make('min_amount')
                            ->numeric()
                            ->step(0.01),
                        Forms\Components\TextInput::make('max_amount')
                            ->numeric()
                            ->step(0.01),
                    ])->columns(2),

                Forms\Components\Section::make('Charge Configuration')
                    ->schema([
                        Forms\Components\Toggle::make('is_default')
                            ->label('Is Default Charge'),
                        Forms\Components\Toggle::make('is_active')
                            ->label('Is Active'),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('channel')
                    ->badge()
                    ->formatStateUsing(fn ($state) => $state instanceof PaymentChannel ? $state->value : $state)
                    ->searchable(),
                Tables\Columns\TextColumn::make('charge_name')
                    ->badge()
                    ->formatStateUsing(fn ($state) => $state instanceof ChargeName ? $state->value : $state)
                    ->searchable(),
                Tables\Columns\TextColumn::make('charge_type')
                    ->badge()
                    ->color(fn ($state): string => match ($state instanceof ChargeType ? $state->value : $state) {
                        'PERCENTAGE' => 'info',
                        'FIXED' => 'success',
                        default => 'gray',
                    })
                    ->searchable(),
                Tables\Columns\TextColumn::make('charge_value')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_default')
                    ->boolean()
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->boolean()
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('channel')
                    ->options(PaymentChannel::class),
                SelectFilter::make('charge_type')
                    ->options(ChargeType::class),
                SelectFilter::make('is_active')
                    ->options([
                        '1' => 'Active',
                        '0' => 'Inactive',
                    ]),
                SelectFilter::make('is_default')
                    ->options([
                        '1' => 'Default',
                        '0' => 'Not Default',
                    ]),
                Filter::make('created_at')
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
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];

                        if ($data['created_from'] ?? null) {
                            $indicators['created_from'] = 'From ' . Carbon::parse($data['created_from'])->toFormattedDateString();
                        }

                        if ($data['created_until'] ?? null) {
                            $indicators['created_until'] = 'Until ' . Carbon::parse($data['created_until'])->toFormattedDateString();
                        }

                        return $indicators;
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                // No bulk actions needed
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
            'view' => Pages\ViewCharges::route('/{record}'),
            'edit' => Pages\EditCharges::route('/{record}/edit'),
        ];
    }
}
