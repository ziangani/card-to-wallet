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
                ->sum('amount'),
            'withdrawals' => CorporateWalletTransaction::where('corporate_wallet_id', $wallet->id)
                ->where('transaction_type', 'withdrawal')
                ->sum('amount'),
            'fees' => CorporateWalletTransaction::where('corporate_wallet_id', $wallet->id)
                ->where('transaction_type', 'fee')
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
     * @param  \Illuminate\Http\Request  $request
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
        
        return view('corporate.wallet.transactions', compact(
            'company',
            'wallet',
            'transactions'
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
     * @param  \Illuminate\Http\Request  $request
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
            'amount' => $request->amount,
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
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function processCardDeposit(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'amount' => 'required|numeric|min:1',
            'card_number' => 'required|string',
            'card_expiry' => 'required|string',
            'card_cvv' => 'required|string',
            'card_holder' => 'required|string',
        ]);
        
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
        
        $user = Auth::user();
        $company = $user->company;
        $wallet = $company->corporateWallet;
        
        // Generate a unique reference number
        $reference = 'CARD-' . strtoupper(Str::random(8));
        
        // In a real implementation, this would integrate with a payment gateway
        // For now, we'll simulate a successful deposit
        
        // Update the wallet balance
        $wallet->balance += $request->amount;
        $wallet->save();
        
        // Create a completed deposit transaction
        $transaction = CorporateWalletTransaction::create([
            'uuid' => Str::uuid(),
            'corporate_wallet_id' => $wallet->id,
            'transaction_type' => 'deposit',
            'amount' => $request->amount,
            'balance_after' => $wallet->balance,
            'currency' => $wallet->currency,
            'description' => 'Card deposit',
            'reference_number' => $reference,
            'performed_by' => $user->id,
            'status' => 'completed',
            'related_entity_type' => 'card_deposit',
            'related_entity_id' => null,
        ]);
        
        return redirect()->route('corporate.wallet.index')
            ->with('success', 'Deposit of ' . $wallet->currency . ' ' . number_format($request->amount, 2) . ' completed successfully.');
    }
}
