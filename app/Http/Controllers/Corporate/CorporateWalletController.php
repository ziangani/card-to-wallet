<?php

namespace App\Http\Controllers\Corporate;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\CorporateWalletTransaction;
use App\Models\CorporateWallet;
use Illuminate\Support\Str;

class CorporateWalletController extends Controller
{
    /**
     * Display the corporate wallet.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $user = Auth::user();
        $company = $user->company;
        $wallet = $company->corporateWallet;

        // Get recent transactions
        $recentTransactions = CorporateWalletTransaction::where('corporate_wallet_id', $wallet->id)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Get transaction statistics
        $transactionStats = [
            'deposits' => CorporateWalletTransaction::where('corporate_wallet_id', $wallet->id)
                ->where('transaction_type', 'deposit')
                ->where('status', 'completed')
                ->sum('amount'),
            'withdrawals' => CorporateWalletTransaction::where('corporate_wallet_id', $wallet->id)
                ->where('transaction_type', 'withdrawal')
                ->where('status', 'completed')
                ->sum('amount'),
            'fees' => CorporateWalletTransaction::where('corporate_wallet_id', $wallet->id)
                ->where('transaction_type', 'fee')
                ->where('status', 'completed')
                ->sum('amount'),
        ];

        return view('corporate.wallet.index', compact(
            'company',
            'wallet',
            'recentTransactions',
            'transactionStats'
        ));
    }

    /**
     * Display the corporate wallet transactions.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\View\View
     */
    public function transactions(Request $request)
    {
        $user = Auth::user();
        $company = $user->company;
        $wallet = $company->corporateWallet;

        // Get transactions with filtering
        $query = CorporateWalletTransaction::where('corporate_wallet_id', $wallet->id);

        // Filter by transaction type
        if ($request->has('type') && $request->type != 'all') {
            $query->where('transaction_type', $request->type);
        }

        // Filter by date range
        if ($request->has('start_date') && $request->has('end_date')) {
            $query->whereBetween('created_at', [$request->start_date . ' 00:00:00', $request->end_date . ' 23:59:59']);
        }

        // Filter by amount range
        if ($request->has('min_amount')) {
            $query->where('amount', '>=', $request->min_amount);
        }

        if ($request->has('max_amount')) {
            $query->where('amount', '<=', $request->max_amount);
        }

        // Filter by status
        if ($request->has('status') && $request->status != 'all') {
            $query->where('status', $request->status);
        }

        // Order by
        $query->orderBy('created_at', 'desc');

        // Paginate
        $transactions = $query->paginate(10);
        
        // Get transaction statistics
        $transactionStats = [
            'deposits' => CorporateWalletTransaction::where('corporate_wallet_id', $wallet->id)
                ->where('transaction_type', 'deposit')
                ->where('status', 'completed')
                ->sum('amount'),
            'withdrawals' => CorporateWalletTransaction::where('corporate_wallet_id', $wallet->id)
                ->where('transaction_type', 'withdrawal')
                ->where('status', 'completed')
                ->sum('amount'),
            'fees' => CorporateWalletTransaction::where('corporate_wallet_id', $wallet->id)
                ->where('transaction_type', 'fee')
                ->where('status', 'completed')
                ->sum('amount'),
        ];

        return view('corporate.wallet.transactions', compact(
            'company',
            'wallet',
            'transactions',
            'transactionStats'
        ));
    }

    /**
     * Display the corporate wallet deposit page.
     *
     * @return \Illuminate\View\View
     */
    public function deposit()
    {
        $user = Auth::user();
        $company = $user->company;
        $wallet = $company->corporateWallet;

        // Generate a unique reference number
        $reference = 'DEP-' . strtoupper(Str::random(8));

        return view('corporate.wallet.deposit', compact(
            'company',
            'wallet',
            'reference'
        ));
    }

    /**
     * Process a deposit notification.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function notifyDeposit(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'amount' => 'required|numeric|min:1',
            'reference' => 'required|string',
            'payment_method' => 'required|string',
            'payment_date' => 'required|date',
            'proof_of_payment' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $user = Auth::user();
        $company = $user->company;
        $wallet = $company->corporateWallet;

        // Calculate the 4% fee
        $depositAmount = $request->amount;
        $feeAmount = $depositAmount * 0.04; // 4% fee
        $totalAmount = $depositAmount + $feeAmount;

        // Handle file upload if provided
        $filePath = null;
        if ($request->hasFile('proof_of_payment')) {
            $file = $request->file('proof_of_payment');
            $filePath = $file->store('deposits/' . $company->id, 'public');
        }

        // Create a pending deposit transaction
        $transaction = CorporateWalletTransaction::create([
            'uuid' => Str::uuid(),
            'corporate_wallet_id' => $wallet->id,
            'transaction_type' => 'deposit',
            'amount' => $depositAmount,
            'balance_after' => $wallet->balance, // Will be updated when approved
            'currency' => $wallet->currency,
            'description' => 'Manual deposit via ' . $request->payment_method,
            'reference_number' => $request->reference,
            'performed_by' => $user->id,
            'status' => 'pending',
            'related_entity_type' => 'deposit_notification',
            'related_entity_id' => null,
        ]);

        // Store additional information in the notes field
        $notes = [
            'payment_method' => $request->payment_method,
            'payment_date' => $request->payment_date,
            'proof_of_payment' => $filePath,
            'notes' => $request->notes,
            'fee_amount' => $feeAmount,
            'total_amount' => $totalAmount,
        ];

        // Update the transaction with the notes
        $transaction->update([
            'notes' => json_encode($notes),
        ]);

        return redirect()->route('corporate.wallet.index')
            ->with('success', 'Deposit notification submitted successfully. Your deposit will be processed once confirmed.');
    }

    /**
     * Process a card deposit.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function processCardDeposit(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'amount' => 'required|numeric|min:100',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $user = Auth::user();
        $company = $user->company;
        $wallet = $company->corporateWallet;

        // Calculate the 4% fee
        $depositAmount = $request->amount;
        $feeAmount = $depositAmount * 0.04; // 4% fee
        $totalAmount = $depositAmount + $feeAmount;

        // Generate a unique reference number and UUID
        $reference = 'CARD-' . strtoupper(Str::random(8));
        $uuid = (string)Str::uuid();

        try {
            // Get MPGS provider
            $provider = \App\Models\PaymentProviders::where('code', \App\Integrations\MPGS\MasterCardCheckout::TECHPAY_CODE)->first();

            if (!$provider) {
                return redirect()->back()->with('error', 'Payment provider not configured.');
            }

            // Initialize MPGS client
            $client = new \App\Integrations\MPGS\MasterCardCheckout($provider);

            // Generate return URL
            $return_url = route('corporate.wallet.card-callback', ['uuid' => $uuid]);

            // Create a pending deposit transaction
            $transaction = CorporateWalletTransaction::create([
                'uuid' => $uuid,
                'corporate_wallet_id' => $wallet->id,
                'transaction_type' => 'deposit',
                'amount' => $depositAmount,
                'balance_after' => $wallet->balance, // Will be updated when payment is completed
                'currency' => $wallet->currency,
                'description' => 'Card deposit',
                'reference_number' => $reference,
                'performed_by' => $user->id,
                'status' => 'pending',
                'related_entity_type' => 'card_deposit',
                'related_entity_id' => null,
                'notes' => json_encode([
                    'fee_amount' => $feeAmount,
                    'total_amount' => $totalAmount
                ]),
            ]);

            // Store transaction ID in session
            $request->session()->put('corporate_transaction_id', $transaction->id);

            // Redirect to payment page
            return redirect()->route('corporate.wallet.card-payment');

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Card deposit error: ' . $e->getMessage());

            return redirect()->back()->with('error', 'An error occurred while processing your payment: ' . $e->getMessage());
        }
    }

    /**
     * Show the card payment page.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function cardPayment(Request $request)
    {
        if (!$request->session()->has('corporate_transaction_id')) {
            return redirect()->route('corporate.wallet.deposit', ['method' => 'card'])
                ->with('error', 'No transaction in progress.');
        }

        $transaction = CorporateWalletTransaction::findOrFail($request->session()->get('corporate_transaction_id'));

        // Get MPGS provider for card payments
        $mpgsProvider = \App\Models\PaymentProviders::where('code', \App\Integrations\MPGS\MasterCardCheckout::TECHPAY_CODE)->first();
        $mpgs_endpoint = $mpgsProvider ? $mpgsProvider->api_url : '';

        return view('corporate.wallet.card-payment', compact('transaction', 'mpgs_endpoint'));
    }

    /**
     * Process the MPGS checkout.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function mpgsCheckout(Request $request)
    {
        if (!$request->session()->has('corporate_transaction_id')) {
            return response()->json([
                'status' => 'ERROR',
                'statusMessage' => 'Invalid transaction'
            ], 400);
        }

        $transaction = CorporateWalletTransaction::findOrFail($request->session()->get('corporate_transaction_id'));

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
            $return_url = route('corporate.wallet.card-callback', ['uuid' => $transaction->uuid]);

            // Calculate fee and total amount
            $depositAmount = $transaction->amount;
            $feeAmount = $depositAmount * 0.04; // 4% fee
            $totalAmount = $depositAmount + $feeAmount;

            // Initiate checkout with total amount (including fee)
            $response = $client->initiateCheckout(
                $totalAmount, // Changed from $transaction->amount to $totalAmount
                config('app.name'),
                $transaction->uuid,
                'Corporate Wallet Deposit',
                $transaction->id,
                $return_url,
                $transaction->currency
            );

            // Update transaction with MPGS session info
            $transaction->update([
                'reference_number' => $transaction->uuid,
                'notes' => json_encode([
                    'success_indicator' => $response['successIndicator'],
                    'session_id' => $response['sessionId'],
                    'payment_status' => 'initiated',
                    'fee_amount' => $feeAmount,
                    'total_amount' => $totalAmount
                ]),
                'status' => 'pending',
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
    public function cardCallback(Request $request, $uuid)
    {
        try {
            $resultIndicator = $request->resultIndicator;

            $transaction = CorporateWalletTransaction::where('uuid', $uuid)
                ->first();

            if (!$transaction) {
                \Illuminate\Support\Facades\Log::error('MPGS callback error: Transaction not found');
                return redirect()->route('corporate.wallet.deposit', ['method' => 'card'])
                    ->with('error', 'Transaction not found');
            }

            // Get the notes with success indicator
            $notes = json_decode($transaction->notes, true);
            $successIndicator = $notes['success_indicator'] ?? null;

            // Verify the success indicator
            if ($resultIndicator === $successIndicator) {
                // Payment successful
                $wallet = $transaction->corporateWallet;

                // Get transaction notes
                $notes = json_decode($transaction->notes, true);
                $feeAmount = $notes['fee_amount'] ?? ($transaction->amount * 0.04);

                // Create a fee transaction
                $feeTransaction = CorporateWalletTransaction::create([
                    'uuid' => Str::uuid(),
                    'corporate_wallet_id' => $wallet->id,
                    'transaction_type' => 'fee',
                    'amount' => $feeAmount,
                    'balance_after' => $wallet->balance, // Will be updated below
                    'currency' => $wallet->currency,
                    'description' => 'Deposit fee (4%)',
                    'reference_number' => $transaction->reference_number . '-FEE',
                    'performed_by' => $transaction->performed_by,
                    'status' => 'completed',
                    'related_entity_type' => 'deposit_fee',
                    'related_entity_id' => $transaction->id,
                ]);

                // Update wallet balance (deposit amount minus fee)
                $wallet->balance += $transaction->amount - $feeAmount;
                $wallet->save();

                // Update transaction
                $transaction->update([
                    'status' => 'completed',
                    'balance_after' => $wallet->balance
                ]);

                // Update fee transaction balance_after
                $feeTransaction->update([
                    'balance_after' => $wallet->balance
                ]);

                // Clear session data
                $request->session()->forget('corporate_transaction_id');

                return redirect()->route('corporate.wallet.index')
                    ->with('success', 'Deposit of ' . $transaction->currency . ' ' . number_format($transaction->amount, 2) . ' completed successfully.');
            } else {
                // Payment failed
                $transaction->update([
                    'status' => 'failed',
                    'description' => $transaction->description . ' (Failed: Payment was declined)'
                ]);

                // Clear session data
                $request->session()->forget('corporate_transaction_id');

                return redirect()->route('corporate.wallet.deposit', ['method' => 'card'])
                    ->with('error', 'Payment was declined by the payment gateway.');
            }

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('MPGS callback error: ' . $e->getMessage());
            return redirect()->route('corporate.wallet.deposit', ['method' => 'card'])
                ->with('error', 'An error occurred while processing your payment');
        }
    }
}
