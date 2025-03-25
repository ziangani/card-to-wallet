<?php

namespace App\Http\Controllers\API;

use App\Common\ErrorCodes;
use App\Common\GeneralStatus;
use App\Common\Helpers;
use App\Common\MerchantServices;
use App\Http\Controllers\Controller;
use App\Integrations\Airtel\Airtel;
use App\Integrations\KonseKonse\cGrateMomo;
use App\Integrations\Stubs\transferStatus;
use App\Models\MerchantApis;
use App\Models\PaymentProviders;
use App\Models\PaymentRequests;
use App\Models\Transactions;
use Illuminate\Http\Request;

class HostedCheckOutApiController extends Controller
{


    public function getStatus(Request $request)
    {
        $requestReference = Helpers::generateUUIDV4();

        // Validate request parameters
        $validator = validator()->make($request->all(), [
            'token' => 'required|string',
        ]);

        if ($validator->fails()) {
            return Helpers::generateErrorResponse(101, $validator->errors()->first(), $requestReference);
        }

        $token = $request->token;
        $merchant = $request->attributes->get('merchant');

        // Check if token exists
        $paymentRequest = PaymentRequests::where('token', $token)
            ->where('merchant_id', $merchant->id)
            ->first();

        if (!$paymentRequest) {
            return Helpers::generateErrorResponse(103, "Token not found.", $requestReference);
        }

        // Fetch payment data
        $status = $paymentRequest->payment->status ?? null;
        $internalReference = $paymentRequest->reference;
        $orderNumber = $paymentRequest->order_number;
        $amount = $paymentRequest->amount;

        $paymentMethod = null;
        $payerAccount = null;
        $serviceProvider = null;

        $responseStatus = match ($status) {
            GeneralStatus::STATUS_COMPLETE => 100,
            GeneralStatus::STATUS_EXPIRED => 102,
            default => 101,
        };


        // Return success response
        $data = [
            "status" => $responseStatus,
            "message" => '',
            "token" => $token,
            "orderNumber" => $orderNumber,
            "transactionReference" => $internalReference,
            'amount' => $amount,
            'currency' => 'ZMW',
            'paymentMethod' => $paymentMethod,
            'serviceProvider' => $serviceProvider,
            'account' => $payerAccount,
        ];

        return Helpers::generateSuccessResponse(100, "Successful request", $data, $requestReference);
    }

    public function getToken(Request $request)
    {
        $requestReference = Helpers::generateUUIDV4();
        $time = date('Y-m-d H:i:s');

        // Validate request data
        $validator = validator()->make($request->all(), [
            'orderNumber' => 'required|string',
            'description' => 'required|string|max:255',
            'amount' => 'required|numeric|min:1',
            'returnURL' => 'required|url',
            'callbackURL' => 'sometimes|url',
            'channel' => 'sometimes|IN:POS,BOT,WEB,APP,ATM,USSD,WHATSAPP',
        ]);

        if ($validator->fails()) {
            return Helpers::generateErrorResponse(101, $validator->errors()->first(), $requestReference);
        }

        $orderNumber = $request->orderNumber;
        $description = $request->description;
        $amount = round($request->amount, 2);
        $merchant = $request->attributes->get('merchant');

        // Check return URL and callback URL
        $returnUrl = $request->returnURL ?? $merchant->returnurl;
        $callbackUrl = $request->callbackURL ?? $merchant->callbackurl;

        if (!$returnUrl) {
            return Helpers::generateErrorResponse(104, "Missing Return URL.", $requestReference);
        }

        $token = Helpers::generatePaymentToken();
        $reference = Helpers::generateUUIDV4();

        // Check for duplicate token
        if (PaymentRequests::where('token', $token)->exists()) {
            return Helpers::generateErrorResponse(105, "Request failed. Please try again.", $requestReference);
        }

        // Insert payment request
        $payment_request = new PaymentRequests();
        $payment_request->merchant_id = $merchant->id;
        $payment_request->merchant_api_id = $merchant->id;
        $payment_request->reference = $reference;
        $payment_request->request_type = 'Merchant API Request';
        $payment_request->token = $token;
        $payment_request->amount = $amount;
        $payment_request->description = $description;
        $payment_request->order_number = $orderNumber;
        $payment_request->status = GeneralStatus::STATUS_PENDING;
        $payment_request->return_url = $returnUrl;
        $payment_request->callback_url = $callbackUrl;
        $payment_request->save();

        // Return success response
        $data = [
            "token" => $token,
            "paymentLink" => route('checkout', ['token' => $token]),
        ];
        return Helpers::generateSuccessResponse(100, "Successful request", $data, $requestReference);
    }

    function payWithMobileMoney(Request $request)
    {
        $request_time = date('Y-m-d h:i:s');
        $requestReference = Helpers::generateUUIDV4();
        $request_type = 'MOBILE_MONEY_PUSH';
        $request_reference = $requestReference;

        // Define validation rules
        $validator = validator()->make($request->all(), [
            'mobileNumber' => 'required|numeric|digits_between:10,12',
            'token' => 'required|string',
            'reference' => 'sometimes|string',
        ]);

        // Check if validation fails
        if ($validator->fails()) {
            Helpers::createBasicLog('momo', "Request validation failed, details: " . json_encode($validator->errors()->messages()), $request_reference);
            Helpers::logApiRequest($request->all(), $validator->errors(), $request_time, $response_time = date('Y-m-d h:i:s'), '', '', $request_reference, $request->reference, 'FAILED', $request_type);
            return response(Helpers::generateErrorResponse(101, $validator->errors()->first(), $requestReference), 400);
        }

        $token = $request->token;
        $mobile = $request->mobileNumber;
        if (strlen($mobile) == 12) {
            $mobile = substr($mobile, 2);
        }

        $merchant = $request->attributes->get('merchant');
        $paymentRequest = PaymentRequests::where('token', $token)
            ->where('merchant_id', $merchant->id)
            ->first();


        if (!$paymentRequest) {
            return Helpers::generateErrorResponse(103, "Token not found.", $requestReference);
        }

        if ($paymentRequest->status != GeneralStatus::STATUS_PENDING) {
            return Helpers::generateErrorResponse(103, "Token has already been used.", $requestReference);
        }

        $reference = $token;
        $amount = $paymentRequest->amount;
        $environment = Helpers::getPaymentEnvironment();
        $serviceProvider = Helpers::getServiceProvider($mobile);

        try {
            Helpers::createBasicLog('momo', "Request received, details: " . json_encode($request->all()), $request_reference);
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

            $transaction = new Transactions();
            $transaction->merchant_reference = $reference;
            $transaction->amount = $amount;
            $transaction->reference_1 = $mobile;
            $transaction->reference_2 = $request->narration;
            $transaction->reference_3 = $serviceProvider;
            $transaction->callback = $paymentRequest->callback_url;
            $transaction->uuid = $request_reference;
            $transaction->merchant_code = $merchant->code;
            $transaction->merchant_id = $merchant->id;
            $transaction->provider_name = $network;
            $transaction->provider_push_status = $push_status->status;
            $transaction->payment_channel = 'MOBILE_MONEY';
            $transaction->payment_providers_id = $provider->id;
            $transaction->save();

            $paymentRequest->status = GeneralStatus::STATUS_INITIATED;
            $paymentRequest->save();

            if ($push_status->status != transferStatus::STATUS_SUCCESS) {
                Helpers::logApiRequest($request->all(), $push_status, $request_time, date('Y-m-d h:i:s'), '', '', $request_reference, $reference, 'FAILED', $request_type);
                return response(Helpers::generateErrorResponse(103, ErrorCodes::ERROR_MESSAGE_PROVIDER_ERROR, $request_reference), 200);
            }
            $response = Helpers::generateSuccessResponse(100, ErrorCodes::ERROR_MESSAGE_SUCCESS, [], $request_reference);
            Helpers::logApiRequest($request->all(), $response, $request_time, $response_time = date('Y-m-d h:i:s'), '', '', $request_reference, $reference, 'SUCCESS', $request_type);
            Helpers::createBasicLog('momo', "Request processed successfully", $request_reference);
            return response($response, 200);

        } catch (\Exception $e) {
            $response = Helpers::generateErrorResponse(103, (config('env') == 'production') ? 103 : $e->getMessage(), $request_reference);
            Helpers::logApiRequest($request->all(), $response, $request_time, $response_time = date('Y-m-d h:i:s'), '', '', $request_reference, $reference, 'FAILED', $request_type);
            return response($response, 500);
        }
    }

    public function generateTokenAndSendMobileMoneyRequest(Request $request): array
    {
        $requestReference = Helpers::generateUUIDV4();
        $time = date('Y-m-d H:i:s');

        // Validate request data
        $validator = validator()->make($request->all(), [
            'orderNumber' => 'required|string',
            'description' => 'required|string|max:255',
            'amount' => 'required|numeric|min:1',
            'returnURL' => 'required|url',
            'callbackURL' => 'sometimes|url',
            'channel' => 'sometimes|IN:POS,BOT,WEB,APP,ATM,USSD,WHATSAPP',
            'mobileNumber' => 'required|numeric|digits_between:10,12',
        ]);

        if ($validator->fails()) {
            return Helpers::generateErrorResponse(101, $validator->errors()->first(), $requestReference);
        }

        $orderNumber = $request->orderNumber;
        $description = $request->description;
        $amount = round($request->amount, 2);
        $merchant = $request->attributes->get('merchant');
        $mobile = $request->mobileNumber;
        if (strlen($mobile) == 12) {
            $mobile = substr($mobile, 2);
        }

        // Check return URL and callback URL
        $returnUrl = $request->returnURL ?? $merchant->returnurl;
        $callbackUrl = $request->callbackURL ?? $merchant->callbackurl;

        if (!$returnUrl) {
            return Helpers::generateErrorResponse(104, "Missing Return URL.", $requestReference);
        }

        $token = Helpers::generatePaymentToken();
        $reference = Helpers::generateUUIDV4();

        // Check for duplicate token
        if (PaymentRequests::where('token', $token)->exists()) {
            return Helpers::generateErrorResponse(105, "Request failed. Please try again.", $requestReference);
        }

        // Insert payment request
        $payment_request = new PaymentRequests();
        $payment_request->merchant_id = $merchant->id;
        $payment_request->merchant_api_id = $merchant->id;
        $payment_request->reference = $reference;
        $payment_request->request_type = 'Merchant API Request';
        $payment_request->token = $token;
        $payment_request->amount = $amount;
        $payment_request->description = $description;
        $payment_request->order_number = $orderNumber;
        $payment_request->status = GeneralStatus::STATUS_PENDING;
        $payment_request->return_url = $returnUrl;
        $payment_request->callback_url = $callbackUrl;
        $payment_request->save();

        // Send mobile money request
        $environment = Helpers::getPaymentEnvironment();
        $serviceProvider = Helpers::getServiceProvider($mobile);
        $network = Helpers::getNetwork($mobile);

        try {
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
                'AIRTEL' => Airtel::initiateTransfer($provider, $token, $requestReference, $amount, substr($mobile, 1, 9), ''),
                'MTN', 'ZAMTEL' => cGrateMomo::initiateTransfer($provider, $reference, $requestReference, $amount, $mobile, ''),
            };

            $transaction = new Transactions();
            $transaction->merchant_reference = $token;
            $transaction->amount = $amount;
            $transaction->reference_1 = $mobile;
            $transaction->reference_2 = $request->narration;
            $transaction->reference_3 = $serviceProvider;
            $transaction->callback = $payment_request->callback_url;
            $transaction->uuid = $requestReference;
            $transaction->merchant_code = $merchant->code;
            $transaction->merchant_id = $merchant->id;
            $transaction->provider_name = $network;
            $transaction->provider_push_status = $push_status->status;
            $transaction->payment_channel = 'MOBILE_MONEY';
            $transaction->payment_providers_id = $provider->id;
            $transaction->save();

            $payment_request->status = GeneralStatus::STATUS_INITIATED;
            $payment_request->save();

            if ($push_status->status != transferStatus::STATUS_SUCCESS) {
                return Helpers::generateErrorResponse(103, ErrorCodes::ERROR_MESSAGE_PROVIDER_ERROR, $requestReference);
            }

            $data = [
                "token" => $token,
                "paymentLink" => route('checkout', ['token' => $token]),
            ];
            return Helpers::generateSuccessResponse(100, "Successful request", $data, $requestReference);

        } catch (\Exception $e) {
            return Helpers::generateErrorResponse(103, (config('env') == 'production') ? 103 : $e->getMessage(), $requestReference);
        }
    }

    public function history(Request $request)
    {
        $requestReference = Helpers::generateUUIDV4();
        $currentDate = date('Y-m-d H:i:s');
        $sixMonthsAgo = date('Y-m-d H:i:s', strtotime('-6 months', strtotime($currentDate)));
        $merchant = $request->attributes->get('merchant');
        $businessId = $merchant->id;

        try {
            // Fetch transactions for the specific merchant within the last 6 months
            $transactions = Transactions::query()
                ->where('transactions.created_at', '>=', $sixMonthsAgo)
                ->where('transactions.merchant_id', $businessId)
                ->orderBy('transactions.id', 'desc')
                ->get([
                    'transactions.created_at as transactiondate',
                    'transactions.merchant_reference as ordernumber',
                    'transactions.currency',
                    'transactions.amount',
                    'transactions.status as paymentstatus',
                    'transactions.reference_1 as payer',
                    'transactions.reference_2 as narration',
                    'transactions.reference_3 as paymentmode',
                    'transactions.reference_4',
//                    'payment_requests.description',
//                    'payment_requests.channel as paymentmode',
                ]);

            if ($transactions->isEmpty()) {
                return response(
                    [
                        'responsecode' => 101,
                        'count' => 0,
                        'responsemessage' => "No records available",
                        'data' => null,
                        'requestReference' => $requestReference
                    ]);
            }

            return response([
                'responsecode' => 100,
                'count' => count($transactions),
                'responsemessage' => "Success",
                'data' => $transactions,
                'requestReference' => $requestReference
            ]);
        } catch (\Exception $e) {
            Helpers::reportException($e);
            return response([
                'responsecode' => 103,
                'count' => 0,
                'responsemessage' => "An error occurred while fetching transactions",
                'requestReference' => $requestReference
            ]);
        }
    }
}
