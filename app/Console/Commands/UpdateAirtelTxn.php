<?php

namespace App\Console\Commands;

use App\Integrations\Airtel\Airtel;
use App\Integrations\Stubs\transferStatus;
use App\Models\CashOuts;
use App\Models\PaymentProviders;
use App\Models\PaymentRequests;
use App\Models\Transactions;
use Illuminate\Console\Command;

class UpdateAirtelTxn extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:update-airtel-txn';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update Airtel transactions';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $airtel_providers = PaymentProviders::where('name', 'Airtel Zambia')->where('status', 'ACTIVE')->pluck('id')->toArray();
        $pending_txn = Transactions::where('status', 'PENDING')->whereIn('payment_providers_id', $airtel_providers)->where('provider_push_status', transferStatus::STATUS_SUCCESS)->get();
        $this->info("\n\n: " . count($pending_txn) . " Pending payments found\n");
        foreach ($pending_txn as $txn) {
            try {
                $this->info("\n\nGetting enquiry for  txn:" . $txn->id . " mobile: " . $txn->reference_1 . "\n");

                $status = Airtel::getStatus($txn->provider, $txn->uuid);
                $paymentReference = $status->reference;
                $paymentReference2 = $status->secondayReference;
                $message = $status->statusMessage;
                $response = $status->rawResponse;

                $txn->retries = $txn->retries + 1;
                $txn->last_retry_date = now();
                $txn->save();

                $this->info("Transaction enquiry : " . json_encode($response));
                if (in_array($status->status, ['TA', 'TF', '404'])) {
                    $txn->provider_external_reference = $paymentReference2;
                    $txn->provider_payment_reference = $paymentReference;
                    $txn->provider_status_description = $message;
                    $txn->provider_payment_confirmation_date = now();
                    $txn->provider_payment_date = now();
                    $txn->status = 'FAILED';
                    $txn->save();
                    PaymentRequests::where('token', $txn->merchant_reference)->update(['status' => PaymentRequests::STATUS_FAILED]);
                    $this->info("\nTransaction FAILED on MNO side successfully updated.");
                } else if (in_array($status->status, ['TS'])) {
                    $txn->provider_external_reference = $paymentReference2;
                    $txn->provider_payment_reference = $paymentReference;
                    $txn->provider_status_description = $message;
                    $txn->provider_payment_confirmation_date = now();
                    $txn->provider_payment_date = now();
                    $txn->cashout_status = CashOuts::STATUS_PENDING;
                    $txn->status = 'COMPLETE';
                    $txn->save();
                    PaymentRequests::where('token', $txn->merchant_reference)->update(['status' => PaymentRequests::STATUS_SUCCESS]);
                    $this->info("Transaction SUCCESSFUL on MNO side successfully updated.");
                } else {
                    $this->info("Transaction status on MNO side not actionable: " . $status->status);
                }
            } catch (\Exception $ex) {
                $this->error($ex->getMessage());
            }
        }
    }
}
