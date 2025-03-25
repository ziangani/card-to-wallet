<?php

namespace App\Http\Controllers\API;

use App\Common\ErrorCodes;
use App\Common\Helpers;
use App\Http\Controllers\Controller;
use App\Integrations\Airtel\Airtel;
use App\Integrations\Stubs\transferStatus;
use App\Models\Merchants;
use App\Models\PaymentProviders;
use App\Models\Transactions;
use Illuminate\Validation\Validator;
use Illuminate\Http\Request;

class MobileMoneyController extends Controller
{
    public function push(Request $request)
    {
        $request_reference = Helpers::generateUUIDV4();
        $environment = config('app.payment_environment');

        try {
            $request_time = date('Y-m-d h:i:s');
            $request_type = 'MOBILE_MONEY_PUSH';

            Helpers::createBasicLog('momo', "Request received, details: " . json_encode($request->all()), $request_reference);
            $validator = validator()->make(request()->all(), [
                'amount' => 'required|numeric|min:1',
                'mobile' => 'required|numeric|digits:10',
                'reference' => 'required|string|min:1|max:25',
                'narration' => 'required|string|min:1|max:25',
                'callback' => 'string|url',
                'wallet' => 'string',
            ]);

            if ($validator->fails()) {
                $response = [
                    'statusCode' => ErrorCodes::ERROR_CODE_INCOMPLETE_REQUEST,
                    'statusDescription' => ErrorCodes::ERROR_MESSAGE_INCOMPLETE_REQUEST,
                    'request_reference' => $request_reference,
                    'missingParams' => json_encode($validator->errors()->messages())
                ];
                Helpers::createBasicLog('momo', "Request validation failed, details: " . json_encode($validator->errors()->messages()), $request_reference);
                Helpers::logApiRequest($request->all(), $response, $request_time, $response_time = date('Y-m-d h:i:s'), '', '', $request_reference, $request->reference, 'FAILED', $request_type);
                return response($response, 400);
            }

            $old_txn = Transactions::where('merchant_reference', $request->reference)->first();
            if ($old_txn != null) {
                $response = [
                    'statusCode' => ErrorCodes::ERROR_CODE_DUPLICATE_REFERENCE,
                    'statusDescription' => ErrorCodes::ERROR_MESSAGE_DUPLICATE_REFERENCE,
                    'request_reference' => $request_reference,
                ];
                Helpers::logApiRequest($request->all(), $response, $request_time, $response_time = date('Y-m-d h:i:s'), '', '', $request_reference, $request->reference, 'FAILED', $request_type);
                Helpers::createBasicLog('momo', "Txn duplicated, old txn: " . json_encode($old_txn), $request_reference);
                return response($response, 400);
            }

            Helpers::createBasicLog('momo', "Request successfully validated", $request_reference);
            $merchant = session()->get('merchant');
            $network = Helpers::getNetwork($request->mobile);
            switch ($network) {
                case 'AIRTEL':
                    $wallet = ($request->wallet != null && $merchant->can_choose_wallet) ? $request->wallet : $merchant->default_airtel_wallet;
                    $provider = PaymentProviders::where('code', $wallet)->where('environment', $environment)->first();
                    if ($provider == null) {
                        $response = [
                            'statusCode' => ErrorCodes::ERROR_CODE_INVALID_WALLET,
                            'statusDescription' => ErrorCodes::ERROR_MESSAGE_INVALID_WALLET,
                            'request_reference' => $request_reference,
                        ];
                        Helpers::logApiRequest($request->all(), $response, $request_time, $response_time = date('Y-m-d h:i:s'), '', '', $request_reference, $request->reference, 'FAILED', $request_type);
                        Helpers::createBasicLog('momo', "Invalid wallet provided $wallet - env: $environment", $request_reference);
                        return response($response, 400);
                    }
                    $push_status = Airtel::initiateTransfer($provider, $request->reference, $request_reference, $request->amount, substr($request->mobile, 1, 9), '');
                    break;
                default:
                    $response = [
                        'statusCode' => ErrorCodes::ERROR_CODE_INVALID_NETWORK,
                        'statusDescription' => ErrorCodes::ERROR_MESSAGE_INVALID_NETWORK,
                        'request_reference' => $request_reference,
                    ];
                    Helpers::logApiRequest($request->all(), $response, $request_time, $response_time = date('Y-m-d h:i:s'), '', '', $request_reference, $request->reference, 'FAILED', $request_type);
                    Helpers::createBasicLog('momo', "Requesting mobile number not supported", $request_reference);
                    return response($response, 400);
            }


            $transaction = new Transactions();
            $transaction->merchant_reference = $request->reference;
            $transaction->amount = $request->amount;
            $transaction->reference_1 = $request->mobile;
            $transaction->reference_2 = $request->narration;
            $transaction->callback = $request->callback;
            $transaction->uuid = $request_reference;
            $transaction->merchant_code = $merchant->code;
            $transaction->merchant_id = $merchant->id;
            $transaction->provider_name = $network;
            $transaction->provider_push_status = $push_status->status;
            $transaction->payment_channel = 'MOBILE_MONEY';
            $transaction->payment_providers_id = $provider->id;
            $transaction->save();


            if ($push_status->status != transferStatus::STATUS_SUCCESS) {
                Helpers::logApiRequest($request->all(), $push_status, $request_time, date('Y-m-d h:i:s'), '', '', $request_reference, $request->reference, 'FAILED', $request_type);
                return response([
                    'statusCode' => ErrorCodes::ERROR_CODE_PROVIDER_ERROR,
                    'statusDescription' => ErrorCodes::ERROR_MESSAGE_PROVIDER_ERROR,
                    'request_reference' => $request_reference,
                ], 200);
            }
            $response = [
                'statusCode' => ErrorCodes::ERROR_CODE_SUCCESS,
                'statusDescription' => ErrorCodes::ERROR_MESSAGE_SUCCESS,
                'request_reference' => $request_reference,
            ];
            Helpers::logApiRequest($request->all(), $response, $request_time, $response_time = date('Y-m-d h:i:s'), '', '', $request_reference, $request->reference, 'SUCCESS', $request_type);
            Helpers::createBasicLog('momo', "Request processed successfully", $request_reference);
            return response($response, 200);

        } catch (\Exception $e) {
            $response = [
                'statusCode' => ErrorCodes::ERROR_CODE_INTERNAL_SERVER_ERROR,
                'statusDescription' => (config('env') == 'production') ? ErrorCodes::ERROR_MESSAGE_INTERNAL_SERVER_ERROR : $e->getMessage(),
                'request_reference' => $request_reference,
            ];
            Helpers::logApiRequest($request->all(), $response, $request_time, $response_time = date('Y-m-d h:i:s'), '', '', $request_reference, $request->reference, 'FAILED', $request_type);
            return response($response, 500);
        }
    }

    public function getTransactionStatus(Request $request)
    {
        $request_reference = Helpers::generateUUIDV4();
        try {
            $request_time = date('Y-m-d h:i:s');
            $request_type = 'MERCHANT_MOBILE_MONEY_TXN_ENQUIRY';

            Helpers::createBasicLog('momo', "Request received, details: " . json_encode($request->all()), $request_reference);
            $validator = validator()->make(request()->all(), [
                'reference' => 'required|string|min:1|max:25',
            ]);

            if ($validator->fails()) {
                $response = [
                    'statusCode' => ErrorCodes::ERROR_CODE_INCOMPLETE_REQUEST,
                    'statusDescription' => ErrorCodes::ERROR_MESSAGE_INCOMPLETE_REQUEST,
                    'request_reference' => $request_reference,
                    'missingParams' => json_encode($validator->errors()->messages())
                ];
                Helpers::createBasicLog('momo', "Request validation failed, details: " . json_encode($validator->errors()->messages()), $request_reference);
                Helpers::logApiRequest($request->all(), $response, $request_time, $response_time = date('Y-m-d h:i:s'), '', '', $request_reference, $request->reference, 'FAILED', $request_type);
                return response($response, 400);
            }
            $reference = $request->reference;

            $transaction = Transactions::where('merchant_reference', $reference)->first();
            if ($transaction == null) {
                $response = [
                    'statusCode' => ErrorCodes::ERROR_CODE_TRANSACTION_NOT_FOUND,
                    'statusDescription' => ErrorCodes::ERROR_MESSAGE_TRANSACTION_NOT_FOUND,
                    'request_reference' => $request_reference,
                ];
                Helpers::logApiRequest($request->all(), $response, $request_time, $response_time = date('Y-m-d h:i:s'), '', '', $request_reference, $request->reference, 'FAILED', $request_type);
                Helpers::createBasicLog('momo', "Txn not found $reference", $request_reference);
                return response($response, 404);
            }
            if ($transaction->status == 'COMPLETE') {
                $response = [
                    'statusCode' => ErrorCodes::ERROR_CODE_SUCCESS,
                    'statusDescription' => 'Transaction successfully processed',
                    'request_reference' => $request_reference,
                    'provider_payment_reference' => $transaction->provider_payment_reference,
                ];
                Helpers::logApiRequest($request->all(), $response, $request_time, $response_time = date('Y-m-d h:i:s'), '', '', $request_reference, $request->reference, 'SUCCESS', $request_type);
                return response($response, 200);
            } else if ($transaction->status == 'PENDING') {
                $response = [
                    'statusCode' => ErrorCodes::ERROR_CODE_TRANSACTION_PENDING,
                    'statusDescription' => 'Transaction is still pending',
                    'request_reference' => $request_reference,
                ];
                Helpers::logApiRequest($request->all(), $response, $request_time, $response_time = date('Y-m-d h:i:s'), '', '', $request_reference, $request->reference, 'SUCCESS', $request_type);
                return response($response, 200);
            }else if($transaction->status == 'FAILED'){
                $response = [
                    'statusCode' => ErrorCodes::ERROR_CODE_TRANSACTION_FAILED,
                    'statusDescription' => 'Transaction failed',
                    'request_reference' => $request_reference,
                ];
                Helpers::logApiRequest($request->all(), $response, $request_time, $response_time = date('Y-m-d h:i:s'), '', '', $request_reference, $request->reference, 'SUCCESS', $request_type);
                return response($response, 200);
            } else {
                $response = [
                    'statusCode' => ErrorCodes::ERROR_CODE_TRANSACTION_STATUS_UNKNOWN,
                    'statusDescription' => 'Transaction status unknown - ' . $transaction->status,
                    'request_reference' => $request_reference,
                ];
                Helpers::logApiRequest($request->all(), $response, $request_time, $response_time = date('Y-m-d h:i:s'), '', '', $request_reference, $request->reference, 'SUCCESS', $request_type);
                return response($response, 200);
            }

        } catch (\Exception $e) {
            $response = [
                'statusCode' => ErrorCodes::ERROR_CODE_INTERNAL_SERVER_ERROR,
                'statusDescription' => (config('env') == 'production') ? ErrorCodes::ERROR_MESSAGE_INTERNAL_SERVER_ERROR : $e->getMessage(),
                'request_reference' => $request_reference,
            ];
            Helpers::logApiRequest($request->all(), $response, $request_time, $response_time = date('Y-m-d h:i:s'), '', '', $request_reference, $request->reference, 'FAILED', $request_type);
            return response($response, 500);
        }

    }
}
