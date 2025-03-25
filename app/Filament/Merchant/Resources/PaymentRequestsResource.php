<?php

namespace App\Filament\Merchant\Resources;

use App\Filament\Merchant\Resources\PaymentRequestsResource\Pages;
use App\Filament\Merchant\Resources\PaymentRequestsResource\RelationManagers;
use App\Models\PaymentRequests;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PaymentRequestsResource extends Resource
{
    protected static ?string $model = PaymentRequests::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
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
            'index' => Pages\ListPaymentRequests::route('/'),
            'create' => Pages\CreatePaymentRequests::route('/create'),
            'edit' => Pages\EditPaymentRequests::route('/{record}/edit'),
            'links' => Pages\PaymentLinks::route('/links'),
        ];
    }
}
