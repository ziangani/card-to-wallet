<?php

namespace App\Console\Commands;

use App\Integrations\Airtel\Airtel;
use App\Integrations\KonseKonse\cGrate;
use App\Integrations\KonseKonse\cGrateMomo;
use App\Integrations\Stubs\transferStatus;
use App\Models\PaymentProviders;
use App\Models\PaymentRequests;
use App\Models\Transactions;
use Illuminate\Console\Command;

class UpdatecGrateTxn extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:update-cgrate-txn';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update cGrate transactions';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $providers = PaymentProviders::where('code', 'CGRATE')->whereIn('status', ['ACTIVE'])->pluck('id')->toArray();
        $pending_txn = Transactions::whereIn('status', ['PENDING'])->whereIn('payment_providers_id', $providers)->where('provider_push_status', transferStatus::STATUS_SUCCESS)->get();
        $this->info("\n\n: " . count($pending_txn) . " Pending payments found\n");
        foreach ($pending_txn as $txn) {
            try {
                $this->info("\n\nGetting enquiry for  txn:" . $txn->id . " mobile: " . $txn->reference_1 . "\n");

                $status = cGrateMomo::getStatus($txn->provider, $txn->merchant_reference);
                $paymentReference = $status->reference;
                $paymentReference2 = $status->secondayReference;
                $message = $status->statusMessage;
                $response = $status->rawResponse;

                $txn->retries = $txn->retries + 1;
                $txn->last_retry_date = now();
                $txn->save();

                $this->info("Transaction enquiry : " . json_encode($response));
                if (in_array($status->status, [transferStatus::STATUS_FAILED])) {
                    //ignore status if transaction is not 3 mins old
                    if ($txn->created_at->diffInMinutes(now()) < 3) {
                        $this->info("Transaction is not 5 mins old. Ignoring status.");
                        continue;
                    }
                    $txn->provider_external_reference = $paymentReference2;
                    $txn->provider_payment_reference = $paymentReference;
                    $txn->provider_status_description = $message;
                    $txn->provider_payment_confirmation_date = now();
                    $txn->provider_payment_date = now();
                    $txn->status = 'FAILED';
                    $txn->save();
                    PaymentRequests::where('token', $txn->merchant_reference)->update(['status' => PaymentRequests::STATUS_FAILED]);
                    $this->info("\nTransaction FAILED on MNO side successfully updated.");
                } else if (in_array($status->status, [transferStatus::STATUS_SUCCESS])) {
                    $txn->provider_external_reference = $paymentReference2;
                    $txn->provider_payment_reference = $paymentReference;
                    $txn->provider_status_description = $message;
                    $txn->provider_payment_confirmation_date = now();
                    $txn->provider_payment_date = now();
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
