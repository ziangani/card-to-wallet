<?php

namespace App\Filament\Resources\Backend\TransactionsResource\Pages;

use App\Filament\Resources\Backend\TransactionsResource;
use Filament\Infolists\Components\Group;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Pages\ViewRecord;

class ViewTransaction extends ViewRecord
{
    protected static string $resource = TransactionsResource::class;
    protected static ?string $title = 'Transaction Details ';

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist->schema([
            Section::make('Transaction Details')
                ->schema([
                    TextEntry::make('created_at')
                        ->label('Date Created'),

                    TextEntry::make('id')
                        ->label('Transaction Id'),

                    TextEntry::make('status')
                        ->label('Status')
                        ->badge()
                        ->color(fn(string $state): string => match ($state) {
                            'PENDING' => 'info',
                            'COMPLETE' => 'success',
                            'SUCCESS' => 'success',
                            'FAILED' => 'danger',
                        }),

                    TextEntry::make('amount')
                        ->label('Amount'),

                    TextEntry::make('updated_at')
                        ->label('Date Updated'),


                    TextEntry::make('uuid')
                        ->label('UUID'),



                    TextEntry::make('merchant_reference')
                        ->label('Merchant Reference'),

                    TextEntry::make('payment_channel')
                        ->label('Payment Channel')
                    ->default('Unknown'),
//
//                    TextEntry::make('callback')
//                        ->label('Callback')
//                        ->default('Unknown'),

                    TextEntry::make('reference_1')
                        ->label('Reference 1')->default('N/A'),

                    TextEntry::make('reference_2')
                        ->label('Reference 2')->default('N/A'),

                    TextEntry::make('reference_3')
                        ->label('Reference 3')->default('N/A'),

                    TextEntry::make('reference_4')
                        ->label('Reference 4')->default('N/A'),

                    TextEntry::make('reversal_status')
                        ->label('Reversal Status')->default('N/A'),

                    TextEntry::make('reversal_reason')
                        ->label('Reversal Reason')->default('N/A'),

                    TextEntry::make('reversal_reference')
                        ->label('Reversal Reference')->default('N/A'),

                    TextEntry::make('reversal_date')
                        ->label('Reversal Date')->default('N/A'),

                ])->columnSpan(1)->columns(2),

            Group::make([
                Section::make('Provider Details')
                    ->schema([
                        TextEntry::make('provider_name')
                            ->label('Provider Name'),

                        TextEntry::make('provider_push_status')
                            ->label('Provider Push Status')
                            ->badge()
                            ->color(fn(string $state): string => match ($state) {
                                'PENDING' => 'info',
                                'COMPLETE' => 'success',
                                'SUCCESS' => 'success',
                                'FAILED' => 'danger',
                            }),

                        TextEntry::make('retries')
                            ->label('Retries'),

                        TextEntry::make('provider_external_reference')
                            ->label('Provider External Reference')
                            ->default('N/A'),

                        TextEntry::make('provider_status_description')
                            ->label('Provider Status Description')
                            ->default('N/A'),

                        TextEntry::make('provider_payment_reference')
                            ->label('Provider Payment Reference')
                            ->default('N/A'),

                        TextEntry::make('provider_payment_confirmation_date')
                            ->label('Provider Payment Confirmation Date')
                            ->default('N/A'),

                        TextEntry::make('provider_payment_date')
                            ->label('Provider Payment Date')
                            ->default('N/A'),

                    ])->columns(2),
                Section::make('Merchant Details')
                    ->schema([
                        TextEntry::make('merchants.name')
                            ->label('Merchant Name'),
                        TextEntry::make('merchants.code')
                            ->label('Merchant Code'),

                        TextEntry::make('merchant_settlement_status')
                            ->label('Merchant Settlement Status'),

                        TextEntry::make('merchant_settlement_date')
                            ->label('Merchant Settlement Date')
                            ->default('N/A'),

                        TextEntry::make('merchants.created_at')
                            ->label('Date Created'),
                        TextEntry::make('merchants.updated_at')
                            ->label('Date Updated'),

                    ])->columns(2),
            ])->columnSpan(1)->columns(2),


        ]);
    }
}
