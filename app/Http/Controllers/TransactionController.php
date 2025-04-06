<?php

namespace App\Http\Controllers;

use App\Integrations\MPGS\MasterCardCheckout;
use App\Models\PaymentProviders;
use Illuminate\Http\Request;
use App\Models\Transaction;
use App\Models\WalletProvider;
use App\Models\Beneficiary;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class TransactionController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        // Authentication is handled in routes
    }

    /**
     * Show the transaction initiation form.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function initiate(Request $request)
    {
        $user = Auth::user();
        $walletProviders = WalletProvider::where('is_active', true)->get();
        $beneficiaries = Beneficiary::where('user_id', $user->id)->get();
        $selectedBeneficiary = null;
        $mpgs = PaymentProviders::where('code', MasterCardCheckout::TECHPAY_CODE)->first();
        $mpgs_endpoint = $mpgs->api_url ?? '';
        if ($request->has('beneficiary_id')) {
            $selectedBeneficiary = Beneficiary::where('id', $request->beneficiary_id)
                ->where('user_id', Auth::id())
                ->first();
        }

        return view('transactions.initiate', compact('walletProviders', 'beneficiaries', 'selectedBeneficiary', 'mpgs_endpoint'));
    }

    /**
     * Process the transaction initiation form.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function processInitiate(Request $request)
    {
        $request->validate([
            'wallet_provider_id' => 'required|exists:wallet_providers,id',
            'wallet_number' => 'required|digits:9',
            'amount' => 'required|numeric|min:10',
            'save_beneficiary' => 'nullable|boolean',
        ]);

        // Calculate fees
        $amount = $request->amount;
        $feeAmount = Transaction::calculateFee($amount);
        $totalAmount = $amount + $feeAmount;

        // Store transaction data in session
        $request->session()->put('transaction', [
            'wallet_provider_id' => $request->wallet_provider_id,
            'wallet_number' => $request->wallet_number,
            'amount' => $amount,
            'fee_amount' => $feeAmount,
            'total_amount' => $totalAmount,
            'save_beneficiary' => $request->has('save_beneficiary'),
            'recipient_name' => $request->recipient_name,
        ]);

        return redirect()->route('transactions.confirm');
    }

    /**
     * Show the transaction confirmation page.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Contracts\Support\Renderable|\Illuminate\Http\RedirectResponse
     */
    public function confirm(Request $request)
    {
        if (!$request->session()->has('transaction')) {
            return redirect()->route('transactions.initiate');
        }

        $transaction = $request->session()->get('transaction');
        $walletProvider = WalletProvider::find($transaction['wallet_provider_id']);

        return view('transactions.confirm', compact('transaction', 'walletProvider'));
    }

    /**
     * Process the transaction confirmation.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function processConfirm(Request $request)
    {
        if (!$request->session()->has('transaction')) {
            return redirect()->route('transactions.initiate');
        }

        $transactionData = $request->session()->get('transaction');

        // Create transaction record
        $transaction = Transaction::create([
            'user_id' => Auth::id(),
            'uuid' => substr((string)Str::uuid(), 0, 20),
            'transaction_type' => 'card_to_wallet',
            'wallet_provider_id' => $transactionData['wallet_provider_id'],
            'wallet_number' => $transactionData['wallet_number'],
            'recipient_name' => $transactionData['recipient_name'] ?? 'Unknown',
            'amount' => $transactionData['amount'],
            'fee_amount' => $transactionData['fee_amount'],
            'total_amount' => $transactionData['total_amount'],
            'currency' => 'ZMW',
            'status' => 'pending',
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);
        
        // Create transaction charge records
        Transaction::createTransactionCharges($transaction);

        // Save beneficiary if requested
        if ($transactionData['save_beneficiary'] && !empty($transactionData['recipient_name'])) {
            Beneficiary::updateOrCreate(
                [
                    'user_id' => Auth::id(),
                    'wallet_provider_id' => $transactionData['wallet_provider_id'],
                    'wallet_number' => $transactionData['wallet_number'],
                ],
                [
                    'recipient_name' => $transactionData['recipient_name'],
                ]
            );
        }

        // Store transaction ID in session
        $request->session()->put('transaction_id', $transaction->id);

        // Redirect to payment page
        return redirect()->route('transactions.payment');
    }

    /**
     * Show the payment page.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Contracts\Support\Renderable|\Illuminate\Http\RedirectResponse
     */
    public function payment(Request $request)
    {
        if (!$request->session()->has('transaction_id')) {
            return redirect()->route('transactions.initiate');
        }

        $transaction = Transaction::findOrFail($request->session()->get('transaction_id'));

        // Get MPGS provider for card payments
        $mpgsProvider = \App\Models\PaymentProviders::where('code', \App\Integrations\MPGS\MasterCardCheckout::TECHPAY_CODE)->first();
        $mpgs_endpoint = $mpgsProvider ? $mpgsProvider->api_url : '';

        return view('transactions.payment', compact('transaction', 'mpgs_endpoint'));
    }

    /**
     * Process the payment with MPGS.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function mpgsCheckout(Request $request)
    {
        if (!$request->session()->has('transaction_id')) {
            return response()->json([
                'status' => 'ERROR',
                'statusMessage' => 'Invalid transaction'
            ], 400);
        }

        $transaction = Transaction::findOrFail($request->session()->get('transaction_id'));

        try {
            // Get MPGS provider
            $provider = \App\Models\PaymentProviders::where('code', \App\Integrations\MPGS\MasterCardCheckout::TECHPAY_CODE)->first();

            if (!$provider) {
                return response()->json([
                    'status' => 'ERROR',
                    'statusMessage' => 'Payment provider not found'
                ], 400);
            }

            // Initialize MPGS client
            $client = new \App\Integrations\MPGS\MasterCardCheckout($provider);

            // Generate return URL
            $return_url = route('transactions.mpgs.callback', ['uuid' => $transaction->uuid]);

            // Initiate checkout
            $response = $client->initiateCheckout(
                $transaction->total_amount,
                config('app.name'),
                $transaction->uuid,
                'Card to Wallet Transfer',
                $transaction->id,
                $return_url,
                $transaction->currency
            );

            // Update transaction with MPGS session info
            $transaction->mpgs_order_id = $transaction->uuid;
            $transaction->reference_2 = $response['successIndicator'];
            $transaction->provider_payment_reference = $response['sessionId'];
            $transaction->status = 'payment_initiated';
            $transaction->save();

            // Record transaction status
            $transaction->statuses()->create([
                'status' => 'payment_initiated',
                'notes' => 'Payment initiated with MPGS'
            ]);

            return response()->json([
                'status' => 'SUCCESS',
                'statusMessage' => 'Payment initiated successfully',
                'session' => $response['sessionId']
            ]);

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('MPGS checkout error: ' . $e->getMessage());

            return response()->json([
                'status' => 'ERROR',
                'statusMessage' => 'Could not initiate payment: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Handle MPGS callback.
     *
     * @param \Illuminate\Http\Request $request
     * @param string $uuid
     * @return \Illuminate\Http\RedirectResponse
     */
    public function mpgsCallback(Request $request, $uuid)
    {
        try {
            $resultIndicator = $request->resultIndicator;

            $transaction = Transaction::where('uuid', $uuid)
                ->first();

            if (!$transaction) {
                \Illuminate\Support\Facades\Log::error('MPGS callback error: Transaction not found');
                return redirect()->route('transactions.initiate')->with('error', 'Transaction not found');
            }

            // Verify the success indicator
            if ($resultIndicator === $transaction->reference_2) {
                // Payment successful
                $transaction->status = 'completed';
                $transaction->save();

                // Record transaction status
                $transaction->statuses()->create([
                    'status' => 'completed',
                    'notes' => 'Payment completed successfully'
                ]);

                // Process wallet funding (in a real application)
                // This would call the mobile money provider's API to fund the wallet

                // Clear session data
                $request->session()->forget(['transaction', 'transaction_id']);

                return redirect()->route('transactions.success', $transaction->uuid);
            } else {
                // Payment failed
                $transaction->status = 'payment_failed';
                $transaction->failure_reason = 'Payment was declined by the payment gateway';
                $transaction->save();

                // Record transaction status
                $transaction->statuses()->create([
                    'status' => 'payment_failed',
                    'notes' => 'Payment was declined by the payment gateway'
                ]);

                // Clear session data
                $request->session()->forget(['transaction', 'transaction_id']);

                return redirect()->route('transactions.failure', $transaction->uuid);
            }

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('MPGS callback error: ' . $e->getMessage());
            return redirect()->route('transactions.initiate')->with('error', 'An error occurred while processing your payment');
        }
    }

    /**
     * Process the payment result.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function processPayment(Request $request)
    {
        if (!$request->session()->has('transaction_id')) {
            return redirect()->route('transactions.initiate');
        }

        $transaction = Transaction::findOrFail($request->session()->get('transaction_id'));

        // Simulate payment result (success/failure)
        $paymentSuccess = $request->has('success') ? $request->success : true;

        if ($paymentSuccess) {
            // Update transaction status
            $transaction->status = 'completed';
            $transaction->save();

            // Record transaction status
            $transaction->statuses()->create([
                'status' => 'completed',
                'notes' => 'Payment completed successfully',
            ]);

            // Clear session data
            $request->session()->forget(['transaction', 'transaction_id']);

            return redirect()->route('transactions.success', $transaction->uuid);
        } else {
            // Update transaction status
            $transaction->status = 'payment_failed';
            $transaction->failure_reason = 'Payment was declined by the payment gateway';
            $transaction->save();

            // Record transaction status
            $transaction->statuses()->create([
                'status' => 'payment_failed',
                'notes' => 'Payment was declined by the payment gateway',
            ]);

            // Clear session data
            $request->session()->forget(['transaction', 'transaction_id']);

            return redirect()->route('transactions.failure', $transaction->uuid);
        }
    }

    /**
     * Show the success page.
     *
     * @param string $uuid
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function success($uuid)
    {
        $transaction = Transaction::where('uuid', $uuid)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        return view('transactions.success', compact('transaction'));
    }

    /**
     * Show the failure page.
     *
     * @param string $uuid
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function failure($uuid)
    {
        $transaction = Transaction::where('uuid', $uuid)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        return view('transactions.failure', compact('transaction'));
    }

    /**
     * Show the transaction history page.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function history(Request $request)
    {
        $query = Transaction::where('user_id', Auth::id());

        // Apply filters
        if ($request->has('date_from') && !empty($request->date_from)) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->has('date_to') && !empty($request->date_to)) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        if ($request->has('status') && !empty($request->status)) {
            if ($request->status === 'pending') {
                $query->whereIn('status', ['pending', 'payment_initiated']);
            } elseif ($request->status === 'failed') {
                $query->whereIn('status', ['failed', 'payment_failed']);
            } else {
                $query->where('status', $request->status);
            }
        }

        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('uuid', 'like', "%{$search}%")
                    ->orWhere('recipient_name', 'like', "%{$search}%")
                    ->orWhere('wallet_number', 'like', "%{$search}%");
            });
        }

        // Get transactions with pagination
        $transactions = $query->orderBy('created_at', 'desc')->paginate(10);

        // Get statistics
        $totalTransactions = Transaction::where('user_id', Auth::id())->count();
        $successfulTransactions = Transaction::where('user_id', Auth::id())
            ->where('status', 'completed')
            ->count();
        $totalAmount = Transaction::where('user_id', Auth::id())
            ->where('status', 'completed')
            ->sum('amount');

        return view('transactions.history', compact(
            'transactions',
            'totalTransactions',
            'successfulTransactions',
            'totalAmount'
        ));
    }

    /**
     * Show the transaction details page.
     *
     * @param string $uuid
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function show($uuid)
    {
        $transaction = Transaction::where('uuid', $uuid)
            ->where('user_id', Auth::id())
            ->with(['statuses', 'walletProvider'])
            ->firstOrFail();

        return view('transactions.show', compact('transaction'));
    }

    /**
     * Download transaction receipt.
     *
     * @param string $uuid
     * @return \Illuminate\Http\Response
     */
    public function download($uuid)
    {
        $transaction = Transaction::where('uuid', $uuid)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        // In a real application, this would generate a PDF receipt
        // For now, we'll just redirect back with a message

        return back()->with('success', 'Receipt downloaded successfully');
    }

    /**
     * Retry a failed transaction.
     *
     * @param string $uuid
     * @return \Illuminate\Http\RedirectResponse
     */
    public function retry($uuid)
    {
        $transaction = Transaction::where('uuid', $uuid)
            ->where('user_id', Auth::id())
            ->whereIn('status', ['failed', 'payment_failed'])
            ->firstOrFail();

        // Store transaction data in session
        session([
            'transaction' => [
                'wallet_provider_id' => $transaction->wallet_provider_id,
                'wallet_number' => $transaction->wallet_number,
                'amount' => $transaction->amount,
                'fee_amount' => $transaction->fee_amount,
                'total_amount' => $transaction->total_amount,
                'save_beneficiary' => false,
                'recipient_name' => $transaction->recipient_name,
            ]
        ]);

        return redirect()->route('transactions.confirm');
    }

    /**
     * Export transactions.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function export(Request $request)
    {
        $format = $request->format ?? 'pdf';

        // In a real application, this would generate the export file
        // For now, we'll just redirect back with a message

        return back()->with('success', 'Transactions exported successfully as ' . strtoupper($format));
    }

    /**
     * Process a quick transaction from the dashboard.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function processQuick(Request $request)
    {
        $request->validate([
            'wallet_provider_id' => 'required|exists:wallet_providers,id',
            'wallet_number' => 'required|digits:9',
            'amount' => 'required|numeric|min:10',
            'save_beneficiary' => 'nullable|boolean',
        ]);

        // Calculate fees
        $amount = $request->amount;
        $feeAmount = Transaction::calculateFee($amount);
        $totalAmount = $amount + $feeAmount;

        // Create transaction record
        $transaction = Transaction::create([
            'user_id' => Auth::id(),
            'uuid' => (string)Str::uuid(),
            'transaction_type' => 'card_to_wallet',
            'wallet_provider_id' => $request->wallet_provider_id,
            'wallet_number' => $request->wallet_number,
            'recipient_name' => $request->recipient_name ?? 'Unknown',
            'amount' => $amount,
            'fee_amount' => $feeAmount,
            'total_amount' => $totalAmount,
            'currency' => 'ZMW',
            'status' => 'pending',
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);
        
        // Create transaction charge records
        Transaction::createTransactionCharges($transaction);

        // Save beneficiary if requested
        if ($request->has('save_beneficiary') && !empty($request->recipient_name)) {
            Beneficiary::updateOrCreate(
                [
                    'user_id' => Auth::id(),
                    'wallet_provider_id' => $request->wallet_provider_id,
                    'wallet_number' => $request->wallet_number,
                ],
                [
                    'recipient_name' => $request->recipient_name,
                ]
            );
        }

        // Store transaction ID in session
        $request->session()->put('transaction_id', $transaction->id);

        // Redirect to payment page
        return redirect()->route('transactions.payment');
    }

    /**
     * Process the transaction via AJAX and initiate MPGS checkout.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function processAjax(Request $request)
    {
        try {
            $request->validate([
                'wallet_provider_id' => 'required|exists:wallet_providers,id',
                'wallet_number' => 'required|digits:9',
                'amount' => 'required|numeric|min:10',
//                'save_beneficiary' => 'nullable|boolean',
            ]);

            // Calculate fees
            $amount = $request->amount;
            $feeAmount = Transaction::calculateFee($amount);
            $totalAmount = $amount + $feeAmount;

            // Generate UUID for transaction
            $uuid = (string)Str::uuid();
            $merchantReference = $uuid; // Use UUID as merchant reference

            // Save beneficiary if requested
            if ($request->has('save_beneficiary') && !empty($request->recipient_name)) {
                Beneficiary::updateOrCreate(
                    [
                        'user_id' => Auth::id(),
                        'wallet_provider_id' => $request->wallet_provider_id,
                        'wallet_number' => $request->wallet_number,
                    ],
                    [
                        'recipient_name' => $request->recipient_name,
                    ]
                );
            }

            // Get MPGS provider
            $provider = \App\Models\PaymentProviders::where('code', \App\Integrations\MPGS\MasterCardCheckout::TECHPAY_CODE)->first();

            if (!$provider) {
                return response()->json([
                    'status' => 'ERROR',
                    'statusMessage' => 'Payment provider not found'
                ], 400);
            }

            // Initialize MPGS client
            $client = new \App\Integrations\MPGS\MasterCardCheckout($provider);

            // Generate return URL
            $return_url = route('transactions.mpgs.callback', ['uuid' => $uuid]);

            // Initiate checkout
            $response = $client->initiateCheckout(
                $totalAmount,
                config('app.name'),
                $uuid,
                'Card to Wallet Transfer',
                time(), // Use timestamp as transaction ID
                $return_url,
                'ZMW'
            );

            // Create transaction record
            $transaction = Transaction::create([
                'uuid' => $uuid,
                'merchant_reference' => $merchantReference,
                'status' => 'PENDING',
                'currency' => 'ZMW',
                'amount' => $amount,
                'fee_amount' => $feeAmount,
                'total_amount' => $totalAmount,
                'merchant_code' => 'CARD_TO_WALLET',
                'payment_providers_id' => $provider->id,
                'user_id' => Auth::id(),
                'provider_name' => $provider->name,
                'provider_push_status' => 'SUCCESS',
                'provider_payment_reference' => $response['sessionId'],
                'payment_channel' => 'CARD',
                'reference_1' => $request->wallet_number,
                'reference_2' => $response['successIndicator'],
                'reference_3' => $request->wallet_provider_id,
                'reference_4' => $request->recipient_name ?? 'Unknown',
            ]);
            
            // Create transaction charge records
            Transaction::createTransactionCharges($transaction);

            // Store transaction ID in session
            $request->session()->put('transaction_id', $transaction->id);

            return response()->json([
                'status' => 'SUCCESS',
                'statusMessage' => 'Transaction created and payment initiated',
                'session' => $response['sessionId']
            ]);

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('AJAX transaction error: ' . $e->getMessage());

            return response()->json([
                'status' => 'ERROR',
                'statusMessage' => 'Could not process transaction: ' . $e->getMessage()
            ], 500);
        }
    }
}
