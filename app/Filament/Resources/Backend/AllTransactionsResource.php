<?php

namespace App\Filament\Resources\Backend;

use App\Filament\Resources\Backend\AllTransactionsResource\Pages;
use App\Models\AllTransactions;
use App\Models\Chargeback;
use App\Models\TransactionRefund;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Builder;

class AllTransactionsResource extends Resource
{
    protected static ?string $model = AllTransactions::class;

    protected static ?string $navigationIcon = 'heroicon-o-currency-dollar';
    protected static ?string $label = 'Transactions';
    protected static ?string $navigationGroup = 'Finance';
    protected static bool $shouldRegisterNavigation = false;


    protected static ?int $navigationSort = 5;

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('txn_date')
                    ->label('Date')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('txn_id')
                    ->label('Transaction ID')
                    ->searchable(),
                Tables\Columns\TextColumn::make('order_id')
                    ->label('Merchant Reference')
                    ->searchable(isIndividual: true),
                Tables\Columns\TextColumn::make('merchant')
                    ->label('Merchant ID')
                    ->searchable(isIndividual: true),
                Tables\Columns\TextColumn::make('merchants.name')
                    ->label('Merchant Name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('approval_code')
                    ->label('Approval Code')
                    ->searchable(isIndividual: true),
                Tables\Columns\TextColumn::make('card_prefix')
                    ->label('Card Prefix')
                    ->searchable(isIndividual: true),
                Tables\Columns\TextColumn::make('card_suffix')
                    ->label('Card Suffix')
                    ->searchable(isIndividual: true),
                Tables\Columns\TextColumn::make('txn_amount')
                    ->label('Amount')
                    ->money(fn ($record) => $record->txn_currency)
                    ->sortable(),
                Tables\Columns\TextColumn::make('txn_currency')
                    ->label('Currency'),
                Tables\Columns\TextColumn::make('txn_type')
                    ->label('Type')
                    ->searchable(),
                Tables\Columns\TextColumn::make('result')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'SUCCESS'  => 'success',
                        'FAILURE' => 'danger',
                        default => 'gray',
                    }),

            ])
            ->defaultSort('txn_date', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('result')
                    ->options([
                        'SUCCESS' => 'Success',
                        'FAILURE' => 'Failed',
                    ]),
                Tables\Filters\SelectFilter::make('txn_type')
                    ->label('Transaction Type')
                    ->options([
                        'PAYMENT' => 'Payment',
                        'REFUND' => 'Refund',
                        'VOID' => 'Void',
                    ]),
                Tables\Filters\SelectFilter::make('source')
                    ->label('Source')
                    ->options([
                        'CYBERSOURCE' => 'CyberSource',
                        'MPGS'=> 'Mastercard Payment Gateway Services',
                    ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\Action::make('createChargeback')
                    ->label('Create Chargeback')
                    ->icon('heroicon-o-arrow-uturn-left')
                    ->color('danger')
                    ->form([
                        Forms\Components\TextInput::make('rrn')
                            ->label('RRN(Optional)')
                            ->maxLength(255),
                        Forms\Components\Select::make('reason_code')
                            ->label('Reason')
                            ->options(Chargeback::REASON_CODES)
                            ->required(),
                        Forms\Components\DatePicker::make('chargeback_date')
                            ->label('Chargeback Date')
                            ->required()
                            ->default(now()),
                    ])
                    ->action(function (AllTransactions $record, array $data): void {
                        Chargeback::createFromTransaction(
                            transaction: $record,
                            rrn: $data['rrn'],
                            reason_code: $data['reason_code'],
                            chargeback_date: $data['chargeback_date']
                        );
                    })
                    ->visible(fn (AllTransactions $record): bool =>
                        $record->result === 'SUCCESS' &&
                        ($record->txn_type === 'PAYMENT' || $record->txn_type === 'credit card')
                    )
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title('Chargeback created')
                            ->body('The chargeback has been created successfully.')
                    ),
                Tables\Actions\Action::make('refundTransaction')
                    ->label('Refund Transaction')
                    ->icon('heroicon-o-arrow-path')
                    ->color('warning')
                    ->form(function (AllTransactions $record) {
                        return [
                            Forms\Components\TextInput::make('amount')
                                ->label('Refund Amount')
                                ->numeric()
                                ->default($record->txn_amount)
                                ->required(),
                            Forms\Components\TextInput::make('arn')
                                ->label('ARN (Acquirer Reference Number)')
                                ->maxLength(255)
                                ->nullable(),
                            Forms\Components\TextInput::make('reason')
                                ->label('Reason')
                                ->maxLength(255)
                                ->required(),
                        ];
                    })
                    ->action(function (AllTransactions $record, array $data): void {
                        // Create refund record first
                        $refund = \App\Models\TransactionRefund::createFromTransaction(
                            transaction: $record,
                            amount: $data['amount'] ?? null,
                            reason: $data['reason'],
                            arn: $data['arn'] ?? null,
                            user_id: \Filament\Facades\Filament::auth()->user()->id
                        );

                        // Process the refund via Cybersource API
                        try {
                            // Get merchant credentials
                            $merchants = config('cybersource_keys');
                            $merchant = collect($merchants)->firstWhere('mid', $record->merchant);

                            if (!$merchant) {
                                throw new \Exception("Merchant ID not found in configuration");
                            }

                            // Initialize Cybersource configuration
                            $config = new \App\Integrations\Cybersource\CyberSourceConfiguration(
                                $merchant['key'],
                                $merchant['sharedkey'],
                                $merchant['mid']
                            );

                            // Set up API client
                            $hostConfig = $config->ConnectionHost();
                            $merchantConfig = $config->merchantConfigObject();
                            $apiClient = new \CyberSource\ApiClient($hostConfig, $merchantConfig);

                            // Initialize Refund API
                            $api = new \CyberSource\Api\RefundApi($apiClient);

                            // Create client reference information
                            $clientReferenceInformation = new \CyberSource\Model\Ptsv2paymentsClientReferenceInformation([
                                'code' => $refund->reference_id
                            ]);

                            // Create refund request
                            $refundRequest = new \CyberSource\Model\RefundPaymentRequest([
                                'clientReferenceInformation' => $clientReferenceInformation
                            ]);

                            // Always send amount information to Cybersource
                            // Use provided amount or fall back to original transaction amount
                            $refundAmount = $data['amount'] ?? $record->txn_amount;

                            $amountDetails = new \CyberSource\Model\Ptsv2paymentsOrderInformationAmountDetails([
                                'totalAmount' => $refundAmount,
                                'currency' => $record->txn_currency
                            ]);

                            $orderInformation = new \CyberSource\Model\Ptsv2paymentsOrderInformation([
                                'amountDetails' => $amountDetails
                            ]);

                            $refundRequest['orderInformation'] = $orderInformation;

                            // Update refund status to processing
                            $refund->status = 'PROCESSING';
                            $refund->save();

                            // Process refund
                            $result = $api->refundPayment($refundRequest, $record->txn_id);

                            if (!$result) {
                                throw new \Exception("Refund failed - no response received");
                            }

                            // Parse response
                            $response = json_decode($result[0], true);

                            // Update refund record with response
                            $refund->response_data = $response;
                            $refund->processed_at = now();

                            // Store Cybersource transaction ID
                            if (isset($response['id'])) {
                                $refund->cybersource_id = $response['id'];
                            }

                            if (isset($response['status']) && in_array($response['status'], ['PENDING', 'COMPLETED', 'SUCCEEDED'])) {
                                $refund->status = 'COMPLETED';
                            } else {
                                $refund->status = 'FAILED';
                            }

                            $refund->save();

                        } catch (\Exception $e) {
                            // Update refund record with error
                            $refund->status = 'FAILED';
                            $refund->response_data = ['error' => $e->getMessage()];
                            $refund->processed_at = now();
                            $refund->save();

                            throw $e;
                        }
                    })
                    ->visible(fn (AllTransactions $record): bool =>
                        $record->result === 'SUCCESS' &&
                        $record->source === 'CYBERSOURCE' &&
                        ($record->txn_type === 'PAYMENT' || $record->txn_type === 'credit card') &&
                        !TransactionRefund::hasSuccessfulRefund($record->id)
                    )
                    ->after(function (TransactionRefund $refund) {
                        $status = match ($refund->status) {
                            'COMPLETED' => 'completed',
                            'PENDING' => 'pending',
                            'PROCESSING' => 'processing',
                            'FAILED' => 'failed',
                            default => 'processed',
                        };

                        if ($refund->status === 'COMPLETED' || $refund->status === 'PENDING') {
                            $notification = Notification::make()
                                ->title("Refund {$status}")
                                ->body("The refund of {$refund->amount} {$refund->currency} has been {$status}.")
                                ->success();
                        } elseif ($refund->status === 'PROCESSING') {
                            $notification = Notification::make()
                                ->title("Refund {$status}")
                                ->body("The refund of {$refund->amount} {$refund->currency} has been {$status}.")
                                ->info();
                        } else {
                            // Failed status
                            $errorMessage = isset($refund->response_data['error'])
                                ? $refund->response_data['error']
                                : "Unknown error";

                            $notification = Notification::make()
                                ->title("Refund Failed")
                                ->body("The refund of {$refund->amount} {$refund->currency} has failed: {$errorMessage}")
                                ->danger();
                        }

                        $notification->send();
                    }),
            ])
            ->bulkActions([])
            ->deferLoading()
            ->modifyQueryUsing(fn (Builder $query) => $query->whereNotNull('txn_amount')->whereIn('txn_type', ['PAYMENT', 'credit card']));
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAllTransactions::route('/'),
            'view' => Pages\ViewAllTransactions::route('/{record}'),
        ];
    }
}
