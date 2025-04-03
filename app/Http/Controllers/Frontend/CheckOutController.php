<?php

namespace App\Http\Controllers\Frontend;

use App\Common\GeneralStatus;
use App\Common\Helpers;
use App\Http\Controllers\Controller;
use App\Integrations\Airtel\Airtel;
use App\Integrations\KonseKonse\cGrateMomo;
use App\Integrations\MPGS\MasterCardCheckout;
use App\Integrations\MPGS\MPGS3D;
use App\Integrations\Stubs\transferStatus;
use App\Models\PaymentProviders;
use App\Models\PaymentRequests;
use App\Models\Transactions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

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

        if($paymentRequest->amount >= 1000)
            return [
                'status' => 'error',
                'statusMessage' => 'Threshold amount exceeded'
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

    /**
     * Initiate 3DS authentication
     *
     * @param Request $request
     * @param string $token
     * @return \Illuminate\Http\JsonResponse
     */
    public function initiate3DSAuthentication(Request $request, $token)
    {
        try {
            // Validate request
            $request->validate([
                'card_number' => 'required|string|size:16',
                'currency' => 'required|string|size:3',
            ]);

            $cardNumber = $request->input('card_number');
            $currency = $request->input('currency');

            // Get payment request
            $paymentRequest = PaymentRequests::where('token', $token)->first();
            if (!$paymentRequest) {
                return response()->json([
                    'status' => 'ERROR',
                    'statusMessage' => 'Invalid payment request'
                ], 400);
            }

            if ($paymentRequest->status != PaymentRequests::STATUS_PENDING) {
                return response()->json([
                    'status' => 'ERROR',
                    'statusMessage' => 'Payment request has already been processed'
                ], 400);
            }

            // Get MPGS provider
            $provider = PaymentProviders::where('code', MPGS3D::TECHPAY_CODE)->first();
            if (!$provider) {
                return response()->json([
                    'status' => 'ERROR',
                    'statusMessage' => 'We could not initiate the payment at this time. MPGS provider not found.'
                ], 400);
            }

            // Initialize MPGS3D client
            $client = new MPGS3D($provider);
            
            // Initiate authentication
            $response = $client->initiateAuthentication($cardNumber, $currency, $token);
            
            // Store card number in session for later use
            Session::put('card_number', $cardNumber);
            
            // Create a transaction record
            $merchant = $paymentRequest->merchant;
            $amount = $paymentRequest->amount;
            $requestReference = Helpers::generateUUIDV4();
            
            $transaction = new Transactions();
            $transaction->merchant_reference = $token;
            $transaction->amount = $amount;
            $transaction->reference_1 = $cardNumber;
            $transaction->reference_2 = $response['transaction']['id'];
            $transaction->reference_3 = $response['order']['id'];
            $transaction->callback = $paymentRequest->callback_url;
            $transaction->uuid = $requestReference;
            $transaction->merchant_code = $merchant->code;
            $transaction->merchant_id = $merchant->id;
            $transaction->provider_name = $provider->name;
            $transaction->provider_push_status = 'SUCCESS';
            $transaction->payment_channel = 'CARD';
            $transaction->payment_providers_id = $provider->id;
            $transaction->provider_payment_reference = $response['order']['id'];
            $transaction->status = 'PENDING';
            $transaction->save();
            
            // Update payment request status
            $paymentRequest->status = GeneralStatus::STATUS_INITIATED;
            $paymentRequest->save();
            
            return response()->json([
                'status' => 'SUCCESS',
                'statusMessage' => 'Authentication initiated successfully',
                'data' => [
                    'orderId' => $response['order']['id'],
                    'transactionId' => $response['transaction']['id'],
                    'authenticationStatus' => $response['order']['authenticationStatus']
                ]
            ]);
            
        } catch (\Exception $e) {
            Log::error('3DS authentication initiation error: ' . $e->getMessage());
            return response()->json([
                'status' => 'ERROR',
                'statusMessage' => 'Could not initiate 3DS authentication: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Authenticate payer with 3DS
     *
     * @param Request $request
     * @param string $token
     * @return \Illuminate\Http\JsonResponse
     */
    public function authenticate3DSPayer(Request $request, $token)
    {
        try {
            // Validate request
            $request->validate([
                'order_id' => 'required|string',
                'transaction_id' => 'required|string',
                'card_number' => 'required|string|size:16',
                'expiry_month' => 'required|string|size:2',
                'expiry_year' => 'required|string|size:2',
                'amount' => 'required|numeric',
                'currency' => 'required|string|size:3',
                'browser_details' => 'required|array'
            ]);

            // Get payment request
            $paymentRequest = PaymentRequests::where('token', $token)->first();
            if (!$paymentRequest) {
                return response()->json([
                    'status' => 'ERROR',
                    'statusMessage' => 'Invalid payment request'
                ], 400);
            }

            // Get MPGS provider
            $provider = PaymentProviders::where('code', MPGS3D::TECHPAY_CODE)->first();
            if (!$provider) {
                return response()->json([
                    'status' => 'ERROR',
                    'statusMessage' => 'We could not authenticate the payer. MPGS provider not found.'
                ], 400);
            }

            // Prepare card data
            $cardData = [
                'number' => $request->input('card_number'),
                'expiry' => [
                    'month' => $request->input('expiry_month'),
                    'year' => $request->input('expiry_year')
                ]
            ];

            // Store card data in session for callback
            Session::put('card_data', $cardData);
            Session::put('amount', $request->input('amount'));
            Session::put('currency', $request->input('currency'));

            // Initialize MPGS3D client
            $client = new MPGS3D($provider);
            
            // Get callback URL
            $callbackUrl = route('checkout.3ds.callback', ['token' => $token]);
            
            // Authenticate payer
            $response = $client->authenticatePayer(
                $request->input('order_id'),
                $request->input('transaction_id'),
                $cardData,
                $request->input('amount'),
                $request->input('currency'),
                $request->input('browser_details'),
                $callbackUrl
            );
            
            // Check if we have a challenge or frictionless flow
            $hasChallengeFlow = isset($response['authentication']['redirect']['html']);
            
            // For frictionless flow, initiate payment immediately
            if (!$hasChallengeFlow && $response['response']['gatewayRecommendation'] === 'PROCEED') {
                try {
                    $paymentResult = $client->initiatePayment(
                        $request->input('order_id'),
                        $request->input('transaction_id'),
                        $cardData,
                        $request->input('amount'),
                        $request->input('currency')
                    );
                    
                    // Update transaction status
                    $this->updateTransactionStatus(
                        $token, 
                        $request->input('order_id'), 
                        $request->input('transaction_id'), 
                        'COMPLETE'
                    );
                    
                    return response()->json([
                        'status' => 'SUCCESS',
                        'statusMessage' => 'Payment completed successfully (frictionless flow)',
                        'data' => [
                            'paymentResult' => $paymentResult,
                            'requiresChallenge' => false
                        ]
                    ]);
                } catch (\Exception $e) {
                    Log::error('Payment error after frictionless authentication: ' . $e->getMessage());
                    return response()->json([
                        'status' => 'ERROR',
                        'statusMessage' => 'Authentication successful but payment failed: ' . $e->getMessage(),
                        'data' => [
                            'requiresChallenge' => false
                        ]
                    ], 500);
                }
            }
            
            // For challenge flow, return the HTML to render
            return response()->json([
                'status' => 'SUCCESS',
                'statusMessage' => 'Authentication challenge required',
                'data' => [
                    'requiresChallenge' => $hasChallengeFlow,
                    'challengeHtml' => $hasChallengeFlow ? $response['authentication']['redirect']['html'] : null,
                    'orderId' => $request->input('order_id'),
                    'transactionId' => $request->input('transaction_id')
                ]
            ]);
            
        } catch (\Exception $e) {
            Log::error('3DS payer authentication error: ' . $e->getMessage());
            return response()->json([
                'status' => 'ERROR',
                'statusMessage' => 'Could not authenticate payer: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Process 3DS callback
     *
     * @param Request $request
     * @param string $token
     * @return \Illuminate\Http\Response
     */
    public function process3DSCallback(Request $request, $token)
    {
        try {
            // Log the callback data
            Log::info('Received 3DS callback', [
                'method' => $request->method(),
                'params' => $request->all()
            ]);

            // Extract parameters from the response
            $orderId = $request->input('order_id') ?? $request->input('orderId');
            $transactionId = $request->input('transaction_id') ?? $request->input('transactionId');
            $recommendation = $request->input('response_gatewayRecommendation') ?? $request->input('response.gatewayRecommendation') ?? 'PROCEED';
            $result = $request->input('result') ?? 'SUCCESS';
            
            // If we don't have orderId or transactionId, check for alternate formats
            if (!$orderId || !$transactionId) {
                if ($request->has('delegate') && $request->input('delegate') === 'THREEDS') {
                    $orderId = $request->input('order_id');
                    $transactionId = $request->input('transaction_id');
                }
            }
            
            if (!$orderId || !$transactionId) {
                Log::error('Missing parameters in 3DS callback', [
                    'request' => $request->all()
                ]);
                return redirect()->route('checkout', [
                    'token' => $token,
                    'indi' => 1,
                    'status' => 'error',
                    'message' => 'Missing required parameters'
                ]);
            }

            // Log the authentication result
            Log::info('3DS Authentication completed', [
                'orderId' => $orderId,
                'transactionId' => $transactionId,
                'recommendation' => $recommendation,
                'result' => $result
            ]);
            
            // Get payment request
            $paymentRequest = PaymentRequests::where('token', $token)->first();
            if (!$paymentRequest) {
                Log::error('Payment request not found in 3DS callback', [
                    'token' => $token
                ]);
                return redirect()->route('checkout', [
                    'token' => $token,
                    'indi' => 1,
                    'status' => 'error',
                    'message' => 'Payment request not found'
                ]);
            }
            
            // Get transaction
            $transaction = Transactions::where('merchant_reference', $token)
                ->where('payment_channel', 'CARD')
                ->first();
                
            if (!$transaction) {
                Log::error('Transaction not found in 3DS callback', [
                    'token' => $token
                ]);
                return redirect()->route('checkout', [
                    'token' => $token,
                    'indi' => 1,
                    'status' => 'error',
                    'message' => 'Transaction not found'
                ]);
            }
            
            // If authentication was successful and gateway recommends proceeding, update status
            if (($result === 'SUCCESS' || !$result) && ($recommendation === 'PROCEED' || !$recommendation)) {
                // Update transaction status
                $transaction->status = 'COMPLETE';
                $transaction->reference_2 = $transactionId;
                $transaction->reference_3 = $orderId;
                $transaction->save();
                
                // Update payment request status
                $paymentRequest->status = 'COMPLETE';
                $paymentRequest->save();
                
                // Redirect to status page
                return redirect()->route('checkout', [
                    'token' => $token,
                    'indi' => 1
                ]);
            } else {
                // Authentication failed
                Log::warning('3DS Authentication failed or declined', [
                    'orderId' => $orderId,
                    'transactionId' => $transactionId,
                    'result' => $result,
                    'recommendation' => $recommendation
                ]);
                
                // Update transaction status
                $transaction->status = 'FAILED';
                $transaction->reference_2 = $transactionId;
                $transaction->reference_3 = $orderId;
                $transaction->save();
                
                // Update payment request status
                $paymentRequest->status = 'FAILED';
                $paymentRequest->save();
                
                // Redirect to status page
                return redirect()->route('checkout', [
                    'token' => $token,
                    'indi' => 1
                ]);
            }
        } catch (\Exception $e) {
            Log::error('3DS callback error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            // Redirect to status page
            return redirect()->route('checkout', [
                'token' => $token,
                'indi' => 1,
                'status' => 'error',
                'message' => 'An error occurred during 3DS processing'
            ]);
        }
    }

    /**
     * Complete 3DS payment
     *
     * @param Request $request
     * @param string $token
     * @return \Illuminate\Http\JsonResponse
     */
    public function complete3DSPayment(Request $request, $token)
    {
        try {
            // Validate request
            $request->validate([
                'order_id' => 'required|string',
                'transaction_id' => 'required|string'
            ]);

            // Get payment request
            $paymentRequest = PaymentRequests::where('token', $token)->first();
            if (!$paymentRequest) {
                return response()->json([
                    'status' => 'ERROR',
                    'statusMessage' => 'Invalid payment request'
                ], 400);
            }

            // Get card data from session
            $cardData = Session::get('card_data');
            $amount = Session::get('amount');
            $currency = Session::get('currency');
            
            if (!$cardData || !$amount || !$currency) {
                return response()->json([
                    'status' => 'ERROR',
                    'statusMessage' => 'Missing payment data'
                ], 400);
            }
            
            // Get MPGS provider
            $provider = PaymentProviders::where('code', MPGS3D::TECHPAY_CODE)->first();
            if (!$provider) {
                return response()->json([
                    'status' => 'ERROR',
                    'statusMessage' => 'We could not complete the payment. MPGS provider not found.'
                ], 400);
            }

            // Initialize MPGS3D client
            $client = new MPGS3D($provider);
            
            // Initiate payment
            $paymentResult = $client->initiatePayment(
                $request->input('order_id'),
                $request->input('transaction_id'),
                $cardData,
                $amount,
                $currency
            );
            
            // Update transaction status
            $this->updateTransactionStatus(
                $token, 
                $request->input('order_id'), 
                $request->input('transaction_id'), 
                'COMPLETE'
            );
            
            return response()->json([
                'status' => 'SUCCESS',
                'statusMessage' => 'Payment completed successfully',
                'data' => $paymentResult
            ]);
            
        } catch (\Exception $e) {
            Log::error('3DS payment completion error: ' . $e->getMessage());
            return response()->json([
                'status' => 'ERROR',
                'statusMessage' => 'Could not complete payment: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update transaction status
     *
     * @param string $token
     * @param string $orderId
     * @param string $transactionId
     * @param string $status
     * @return void
     */
    private function updateTransactionStatus($token, $orderId, $transactionId, $status)
    {
        try {
            // Get transaction
            $transaction = Transactions::where('merchant_reference', $token)
                ->where('payment_channel', 'CARD')
                ->first();
                
            if ($transaction) {
                $transaction->status = $status;
                $transaction->reference_2 = $transactionId;
                $transaction->reference_3 = $orderId;
                $transaction->save();
                
                // Update payment request status
                $paymentRequest = PaymentRequests::where('token', $token)->first();
                if ($paymentRequest) {
                    $paymentRequest->status = $status;
                    $paymentRequest->save();
                }
            } else {
                Log::warning('Could not update transaction status - transaction not found', [
                    'token' => $token,
                    'orderId' => $orderId,
                    'transactionId' => $transactionId
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Error updating transaction status', [
                'message' => $e->getMessage(),
                'token' => $token,
                'orderId' => $orderId,
                'transactionId' => $transactionId
            ]);
        }
    }
}
