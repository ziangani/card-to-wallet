<?php

namespace App\Http\Controllers\Frontend;

use App\Common\GeneralStatus;
use App\Common\Helpers;
use App\Http\Controllers\Controller;
use App\Integrations\Airtel\Airtel;
use App\Integrations\KonseKonse\cGrateMomo;
use App\Integrations\MPGS\MasterCardCheckout;
use App\Integrations\Stubs\transferStatus;
use App\Models\Merchants;
use App\Models\PaymentProviders;
use App\Models\PaymentRequests;
use App\Models\Transactions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PaymentLinkController extends Controller
{
    public function merchantPayLink(Request $request)
    {
        $loaded = false;
        $statusMessage = '';
        try {
            $merchant = Merchants::where('code', $request->merchantcode)->first();
            if (!$merchant) {
                $statusMessage = 'Invalid merchant';
            } else {
                $loaded = true;
            }

        } catch (\Exception $e) {
            $merchant = null;
            $statusMessage = 'An error occurred while processing your request';
        }
        $data = [
            'loaded' => $loaded,
            'merchant' => $merchant,
            'statusMessage' => $statusMessage,
        ];
        return view('checkout.merchant_paylink', $data);
    }

    public function makeMerchantToken(Request $request)
    {

        try {
            // Validate request data
            $validator = validator()->make($request->all(), [
                'amount' => 'required|numeric|min:1',
                'paymentNote' => 'required|string|max:255',
                //'email' => 'sometimes|email',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'statusMessage' => $validator->errors()->first(),
                ], 200);
            }

            $amount = round($request->amount, 2);
            $paymentNote = $request->paymentNote;
            $email = $request->email;

            if ($email && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                return response()->json([
                    'status' => 'error',
                    'statusMessage' => 'Invalid email address',
                ], 200);
            }

            $merchant = Merchants::where('code', $request->merchantcode)->first();

            if (!$merchant) {
                return response()->json([
                    'status' => 'error',
                    'statusMessage' => 'Invalid request',
                ], 200);
            }

            $token = substr(Helpers::generateRandomHashM1(), 0, 25);
            $reference = Helpers::generateUUIDV4();

            // Check for duplicate token
            if (PaymentRequests::where('token', $token)->exists()) {
                return response()->json([
                    'status' => 'error',
                    'statusMessage' => 'Request failed. Please try again.',
                ], 200);
            }

            // Insert payment request
            $payment_request = new PaymentRequests();
            $payment_request->merchant_id = $merchant->id;
            $payment_request->merchant_api_id = 0;
            $payment_request->reference = $reference;
            $payment_request->request_type = 'Merchant Pay Link Request';
            $payment_request->token = $token;
            $payment_request->amount = $amount;
            $payment_request->description = $paymentNote;
            $payment_request->status = GeneralStatus::STATUS_PENDING;
            $payment_request->return_url = $merchant->returnurl;
            $payment_request->callback_url = $merchant->callbackurl;
            $payment_request->details = json_encode(['email' => $email]);
            $payment_request->save();

            // Return success response
            return response()->json([
                'status' => 'SUCCESS',
                'url' => route('checkout', ['token' => $token]),
            ]);
        } catch (\Exception $e) {
            Helpers::logError($e);
            return response()->json([
                'status' => 'error',
                'statusMessage' => 'An error occurred while processing your request',
            ], 200);
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

        // Fetch payment data
        $status = $paymentRequest->payment->status ?? 'PENDING';
        $orderNumber = $paymentRequest->order_number;

        if ($status == 'COMPLETE')
            $status = 'SUCCESS';

        // Retur success response
        $data = [
            "status" => $status,
            "statusMessage" => 'As indicated',
            "orderNumber" => $orderNumber,
        ];

        return response()->json($data);
    }
}
