<?php

namespace App\Filament\Resources\Backend;

use App\Filament\Resources\Backend;
use App\Filament\Resources\Backend\PaymentRequestsResource\Pages;
use App\Filament\Resources\Backend\PaymentRequestsResource\RelationManagers;
use App\Models\PaymentRequests;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class PaymentRequestsResource extends Resource
{
    protected static ?string $model = PaymentRequests::class;

    protected static ?string $navigationIcon = 'heroicon-o-banknotes';
//    protected static ?string $navigationGroup = 'Transactions';
    protected static ?string $navigationLabel = 'Payment Requests';
//    protected static bool $shouldRegisterNavigation = false;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('created_at'),
                Tables\Columns\TextColumn::make('merchant.name'),
                Tables\Columns\TextColumn::make('description'),
                Tables\Columns\TextColumn::make('token'),
                Tables\Columns\TextColumn::make('amount'),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'PENDING' => 'info',
                        'SUCCESS' => 'success',
                        'FAILED' => 'danger',
                        default => 'secondary',
                    }),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    //Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])->defaultSort('created_at', 'desc');
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
            'index' => Backend\PaymentRequestsResource\Pages\ListPaymentRequests::route('/'),
//            'create' => Pages\CreatePaymentRequests::route('/create'),
//            'edit' => Pages\EditPaymentRequests::route('/{record}/edit'),
        ];
    }
}
