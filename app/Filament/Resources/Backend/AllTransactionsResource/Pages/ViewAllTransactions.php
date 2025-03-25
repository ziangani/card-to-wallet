<?php

namespace App\Filament\Resources\Backend\AllTransactionsResource\Pages;

use App\Filament\Resources\Backend\AllTransactionsResource;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\Grid;

class ViewAllTransactions extends ViewRecord
{
    protected static string $resource = AllTransactionsResource::class;

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make('Transaction Details')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                TextEntry::make('txn_id')
                                    ->label('Transaction ID'),
                                TextEntry::make('source'),
                                TextEntry::make('merchant'),
                                TextEntry::make('txn_type')
                                    ->label('Type'),
                                TextEntry::make('status')
                                    ->badge()
                                    ->color(fn (string $state): string => match ($state) {
                                        'success' | 'PROCESSED' => 'success',
                                        'failed' => 'danger',
                                        'pending' => 'warning',
                                        default => 'gray',
                                    }),
                                TextEntry::make('txn_date')
                                    ->dateTime(),
                                TextEntry::make('order_id'),
                                TextEntry::make('terminal_id'),
                                TextEntry::make('client_reference_code'),
                            ]),
                    ]),

                Section::make('Card Information')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                TextEntry::make('card_number'),
                                TextEntry::make('card_type'),
                                TextEntry::make('card_expiry_month'),
                                TextEntry::make('card_expiry_year'),
                                TextEntry::make('card_suffix'),
                                TextEntry::make('card_prefix'),
                                TextEntry::make('commerce_indicator'),
                            ]),
                    ]),

                Section::make('Amount Details')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                TextEntry::make('txn_amount')
                                    ->money(fn ($record) => $record->txn_currency),
                                TextEntry::make('txn_currency'),
                                TextEntry::make('order_currency'),
                                TextEntry::make('amount_details_total_amount')
                                    ->money(fn ($record) => $record->amount_details_currency),
                                TextEntry::make('amount_details_currency'),
                            ]),
                    ]),

                Section::make('Billing Information')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                TextEntry::make('bill_to_first_name'),
                                TextEntry::make('bill_to_last_name'),
                                TextEntry::make('bill_to_email'),
                                TextEntry::make('bill_to_phone_number'),
                                TextEntry::make('bill_to_address1'),
                                TextEntry::make('bill_to_city'),
                                TextEntry::make('bill_to_state'),
                                TextEntry::make('bill_to_country'),
                                TextEntry::make('bill_to_postal_code'),
                            ]),
                    ]),

                Section::make('Response Details')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                TextEntry::make('response_acquirer_code'),
                                TextEntry::make('reason_code'),
                                TextEntry::make('r_code'),
                                TextEntry::make('r_flag'),
                                TextEntry::make('r_message'),
                                TextEntry::make('return_code'),
                                TextEntry::make('reconciliation_id'),
                                TextEntry::make('approval_code'),
                                TextEntry::make('processor_name'),
                            ]),
                    ]),

                Section::make('Raw Data')
                    ->schema([
                        TextEntry::make('raw_data')
                            ->columnSpanFull()
                            ->formatStateUsing(fn ($state) => json_encode($state, JSON_PRETTY_PRINT))
                            ->prose(),
                    ])
                    ->collapsible(),
            ]);
    }
}
