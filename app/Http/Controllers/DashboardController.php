<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaction;
use App\Models\Beneficiary;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
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
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $user = Auth::user();
        
        // Get recent transactions
        $recentTransactions = Transaction::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
        
        // Get saved beneficiaries
        $savedBeneficiaries = Beneficiary::where('user_id', $user->id)
            ->orderBy('is_favorite', 'desc')
            ->orderBy('updated_at', 'desc')
            ->limit(4)
            ->get();
        
        // Get transaction statistics
        $totalTransactions = Transaction::where('user_id', $user->id)->count();
        $successfulTransactions = Transaction::where('user_id', $user->id)
            ->where('status', 'completed')
            ->count();
        $totalAmount = Transaction::where('user_id', $user->id)
            ->where('status', 'completed')
            ->sum('amount');
        
        return view('dashboard.index', compact(
            'user', 
            'recentTransactions', 
            'savedBeneficiaries', 
            'totalTransactions', 
            'successfulTransactions', 
            'totalAmount'
        ));
    }
}
