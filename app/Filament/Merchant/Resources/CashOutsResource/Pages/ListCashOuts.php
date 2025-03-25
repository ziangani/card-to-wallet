<?php

namespace App\Filament\Merchant\Resources\CashOutsResource\Pages;

use App\Filament\Merchant\Resources\CashOutsResource;
use App\Filament\Merchant\Widgets\CashOutOverview;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use App\Models\Transactions;
use Illuminate\Support\Facades\Auth;
use Filament\Forms;
use Filament\Forms\Components\TextInput;

class ListCashOuts extends ListRecords
{
    protected static string $resource = CashOutsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('initiateCashOut')
                ->label('Initiate Cash Out')
                ->form([
                    Forms\Components\Section::make('Balance Information')
                        ->schema([
                            Forms\Components\Placeholder::make('availableBalance')
                                ->label('Available Balance')
                                ->content(function () {
                                    $merchantId = Auth::user()->merchant_id;
                                    return 'ZMW ' . number_format(Transactions::getAvailableBalance($merchantId), 2);
                                })->inlineLabel(),
                            Forms\Components\Placeholder::make('actualBalance')
                                ->label('Actual Balance (after charges)')
                                ->content(function () {
                                    $merchantId = Auth::user()->merchant_id;
                                    return 'ZMW ' . number_format(Transactions::getActualBalance($merchantId), 2);
                                })->inlineLabel(),
                        ]),
                    Forms\Components\Section::make('Cash-Out Details')
                        ->schema([
                            TextInput::make('amount')
                                ->label('Cash-Out Amount')
                                ->numeric()
                                ->required()
                                ->prefix('ZMW'),
                            Forms\Components\Textarea::make('comment')
                                ->label('Comment')
                                ->placeholder('Add a comment for this cash-out'),
                        ]),
                ])
                ->modalHeading('Initiate Cash Out')
                ->modalWidth('lg')
                ->action(function (array $data): void {
                    // Get merchant ID from authenticated user
                    $merchantId = Auth::user()->merchant_id;


                    // Get actual balance
                    $actualBalance = Transactions::getActualBalance($merchantId);
                    // Validate that the cash-out amount doesn't exceed the actual balance
                    if ($data['amount'] > $actualBalance) {
                        // Display an error message
                        \Filament\Notifications\Notification::make()
                            ->title('Cash-out amount exceeds actual balance')
                            ->danger()
                            ->send();
                        return;
                    }
                    // Process the cash-out request
                    try {
                        // Start a database transaction
                        \Illuminate\Support\Facades\DB::beginTransaction();

                        // Generate a unique batch ID
                        $batchId = 'CO-' . uniqid();

                        // Create a new CashOuts record
                        $cashOut = new \App\Models\CashOuts();
                        $cashOut->batch_id = $batchId;
                        $cashOut->reference = 'CO-' . time();
                        $cashOut->amount = $data['amount'];
                        $cashOut->merchant_id = $merchantId;

                        // Calculate fees and charges
                        $merchant = \App\Models\Merchants::find($merchantId);
                        $merchantCode = $merchant ? $merchant->code : null;
                        $charges = \App\Models\Charges::getApplicableCharges('CASHOUT', $merchantCode, null);

                        $techpayCharge = 0;
                        $thirdPartyCharge = 0;

                        foreach ($charges as $charge) {
                            $chargeAmount = $charge->calculateCharge($data['amount']);

                            if ($charge->charge_type === 'TECHPAY') {
                                $techpayCharge += $chargeAmount;
                            } else {
                                $thirdPartyCharge += $chargeAmount;
                            }
                        }

                        $totalFee = $techpayCharge + $thirdPartyCharge;

                        $cashOut->fee = $totalFee;
                        $cashOut->techpay_charge = $techpayCharge;
                        $cashOut->third_party_charge = $thirdPartyCharge;
                        $cashOut->batch_status = 'INITIATED';
                        $cashOut->transaction_status = 'PENDING';
                        $cashOut->date = now();

                        // Save the comment if provided
                        if (!empty($data['comment'])) {
                            $cashOut->comment = $data['comment'];
                        }

                        $cashOut->save();

                        // Update the relevant transactions
                        $availableTransactions = Transactions::availableForCashout()
                            ->where('merchant_id', $merchantId)
                            ->get();

                        $remainingAmount = $data['amount'];

                        foreach ($availableTransactions as $transaction) {
                            if ($remainingAmount <= 0) {
                                break;
                            }

                            $transaction->cashout_batch_id = $batchId;
                            $transaction->cashout_status = 'INITIATED';
                            $transaction->save();

                            $remainingAmount -= $transaction->amount;
                        }

                        // Commit the transaction
                        \Illuminate\Support\Facades\DB::commit();

                        // Display a success message
                        \Filament\Notifications\Notification::make()
                            ->title('Cash-out initiated successfully')
                            ->success()
                            ->send();

                    } catch (\Exception $e) {
                        // Rollback the transaction in case of an error
                        \Illuminate\Support\Facades\DB::rollBack();

                        // Display an error message
                        \Filament\Notifications\Notification::make()
                            ->title('Error initiating cash-out')
                            ->body($e->getMessage())
                            ->danger()
                            ->send();
                    }
                }),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            CashOutOverview::class,
        ];
    }
}
