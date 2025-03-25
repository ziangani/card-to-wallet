<?php

namespace App\Http\Controllers\Frontend;

use App\Common\GeneralStatus;
use App\Common\Helpers;
use App\Http\Controllers\Controller;
use App\Integrations\Airtel\Airtel;
use App\Integrations\KonseKonse\cGrateMomo;
use App\Integrations\MPGS\MasterCardCheckout;
use App\Integrations\Stubs\transferStatus;
use App\Models\PaymentProviders;
use App\Models\PaymentRequests;
use App\Models\Transactions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CheckOutController extends Controller
{
    public function checkout(Request $request)
    {
        $loaded = false;
        $statusMessage = '';
        try {
            $paymentRequest = PaymentRequests::where('token', $request->token)->first();
            $mpgs = PaymentProviders::where('code', MasterCardCheckout::TECHPAY_CODE)->first();
            $mpgs_endpoint = $mpgs->api_url ?? '';
            if (!$paymentRequest) {
                $statusMessage = 'Invalid payment request';
            } elseif ($paymentRequest->status != PaymentRequests::STATUS_PENDING) {
                $statusMessage = 'Payment request has already been processed';
            } else {
                $loaded = true;
            }

        } catch (\Exception $e) {
            $paymentRequest = null;
            $statusMessage = 'An error occurred while processing your request';
        }
        $data = [
            'loaded' => $loaded,
            'statusMessage' => $statusMessage,
            'paymentRequest' => $paymentRequest,
            'token' => $request->token,
            'mpgs_endpoint' => $mpgs_endpoint,
        ];
        return view('checkout.checkout', $data);
    }

    public function processCheckout(Request $request)
    {
        $mobile = $request->mobile;
        $paymentMode = $request->payment_mode;

        //Validate mobile
        if ($paymentMode == 'momo') {
            if (!is_numeric($mobile))
                return response()->json([
                    'status' => 'ERROR',
                    'statusMessage' => 'Invalid Mobile Number',
                ]);
            if (strlen($mobile) != 10)
                return response()->json([
                    'status' => 'ERROR',
                    'statusMessage' => 'Invalid Mobile Number',
                ]);
        }

        $paymentRequest = PaymentRequests::where('token', $request->token)->first();
        if (!$paymentRequest)
            return [
                'status' => 'error',
                'statusMessage' => 'Invalid payment request'
            ];

        if ($paymentRequest->status != PaymentRequests::STATUS_PENDING)
            return [
                'status' => 'error',
                'statusMessage' => 'Payment request has already been processed'
            ];

        try {

            $reference = $paymentRequest->token;
            $app_name = Helpers::getAppName() . ' Limited';
            $description = $paymentRequest->description;
            $requestReference = Helpers::generateUUIDV4();
            $merchant = $paymentRequest->merchant;
            $amount = $paymentRequest->amount;
            $environment = Helpers::getPaymentEnvironment();
            $serviceProvider = Helpers::getServiceProvider($mobile);

            if ($paymentMode !== 'momo') {
                $provider = PaymentProviders::where('code', MasterCardCheckout::TECHPAY_CODE)->first();
                if (!$provider)
                    return response()->json([
                        'status' => 'ERROR',
                        'statusMessage' => 'We could not initiate the payment at this time. MPGS provider not found. NT-001',
                    ]);

                $return_url = route('checkout.mpgs.status', ['token' => $reference]);
                $client = new MasterCardCheckout($provider);
                $response = $client->initiateCheckout($amount, $app_name, $reference, 'Payment for ' . $description, $paymentRequest->id, $return_url, 'ZMW');
                $providerReference = $response['sessionId'];
                $paymentChannel = 'CARD';
                $providerName = $provider->name;
                $push_status = new transferStatus();
                $push_status->status = transferStatus::STATUS_SUCCESS;
                $push_status->reference = $reference;
                $push_status->statusMessage = 'Payment initiated successfully';

            } else {

                $network = Helpers::getNetwork($mobile);
                $wallet = match ($network) {
                    'AIRTEL' => config('airtel.default_wallet'),
                    'MTN', 'ZAMTEL' => config('cgrate.default_wallet'),
                    default => throw new \Exception("Invalid network"),
                };

                $provider = PaymentProviders::where('code', $wallet)
                    ->where('environment', $environment)
                    ->where('status', GeneralStatus::STATUS_ACTIVE)
                    ->first();

                if ($provider == null) {
                    throw new \Exception("Payment Provider not found: " . $wallet);
                }

                $push_status = match ($network) {
                    'AIRTEL' => Airtel::initiateTransfer($provider, $reference, $requestReference, $amount, substr($mobile, 1, 9), ''),
                    'MTN', 'ZAMTEL' => cGrateMomo::initiateTransfer($provider, $reference, $requestReference, $amount, $mobile, ''),
                };
                $providerReference = $push_status->reference;
                $paymentChannel = 'MOBILE_MONEY';
                $providerName = $network;
            }

            $transaction = new Transactions();
            $transaction->merchant_reference = $reference;
            $transaction->amount = $amount;
            $transaction->reference_1 = $mobile;
            $transaction->reference_2 = $paymentMode !== 'momo' ? $response['successIndicator'] : '';
            $transaction->reference_3 = $serviceProvider;
            $transaction->callback = $paymentRequest->callback_url;
            $transaction->uuid = $requestReference;
            $transaction->merchant_code = $merchant->code;
            $transaction->merchant_id = $merchant->id;
            $transaction->provider_name = $providerName;
            $transaction->provider_push_status = $push_status->status;
            $transaction->payment_channel = $paymentChannel;
            $transaction->payment_providers_id = $provider->id;
            $transaction->provider_payment_reference = $providerReference;
            $transaction->save();
            $transaction->save();


            $paymentRequest->status = GeneralStatus::STATUS_INITIATED;
            $paymentRequest->save();

            return [
                'status' => 'SUCCESS',
                'statusMessage' => 'Payment initiated successfully',
                'session' => $providerReference
            ];

        } catch (\Exception $e) {
            Log::info("Could not init payment: " . $e->getMessage());
            return [
                'status' => 'error',
                'statusMessage' => 'An error occurred while processing your request'
            ];
        }
    }

    public function getStatus(Request $request)
    {
        $token = $request->token;
        $paymentRequest = PaymentRequests::where('token', $token)->first();

        if (!$paymentRequest) {
            return [
                'status' => 'error',
                'statusMessage' => 'Invalid payment request'
            ];
        }

        // Get associated transaction
        $transaction = Transactions::where('merchant_reference', $token)->first();

        if (!$transaction) {
            return response()->json([
                'status' => 'ERROR',
                'statusMessage' => 'Transaction not found'
            ]);
        }

        $status = $transaction->status ?? 'PENDING';
        if ($status == 'COMPLETE') {
            $status = 'SUCCESS';
        }

        return response()->json([
            'status' => $status,
            'statusMessage' => 'Payment status retrieved successfully',
            'orderNumber' => $paymentRequest->order_number
        ]);
    }

    public function mpgsStatus(Request $request, $token)
    {
        try {
            $resultIndicator = $request->resultIndicator;

            $transaction = Transactions::where('merchant_reference', $token)
                ->where('payment_channel', 'CARD')
                ->first();

            if (!$transaction) {
                Log::error('MPGS payment status error: Transaction not found');
                return redirect()->route('checkout', ['token' => $token, 'indi' => 1]);
            }

            $paymentRequest = PaymentRequests::where('token', $token)->first();
            if (!$paymentRequest) {
                Log::error('MPGS payment status error: Payment request not found');
                return redirect()->route('checkout', ['token' => $token]);
            }

            // Verify the success indicator
            if ($resultIndicator === $transaction->reference_2) {
                $transaction->status = 'COMPLETE';
                $transaction->save();

                $paymentRequest->status = 'COMPLETE';
                $paymentRequest->save();
            }else{
                $transaction->status = 'FAILED';
                $transaction->reference_2 = $resultIndicator;
                $transaction->save();

                $paymentRequest->status = 'FAILED';
                $paymentRequest->save();
            }
            $url = url("/checkout/{$token}/?indi=1");
            return redirect($url);

        } catch (\Exception $e) {
            Log::error('MPGS payment status error: ' . $e->getMessage());
            return redirect()->route('checkout', ['token' => $token, 'check_status' => 1]);
        }
    }
}
