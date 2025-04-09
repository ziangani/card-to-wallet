<?php

namespace App\Http\Controllers\Corporate;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\BulkDisbursement;
use App\Models\ApprovalRequest;

class CorporateController extends Controller
{
    /**
     * Display the corporate dashboard.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $user = Auth::user();
        $company = $user->company;
        $wallet = $company->corporateWallet;
        
        // Get recent disbursements
        $recentDisbursements = BulkDisbursement::where('company_id', $company->id)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
        
        // Get pending approvals
        $pendingApprovals = ApprovalRequest::where('company_id', $company->id)
            ->where('status', 'pending')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
        
        // Get disbursement statistics
        $disbursementStats = [
            'total' => BulkDisbursement::where('company_id', $company->id)->count(),
            'completed' => BulkDisbursement::where('company_id', $company->id)
                ->whereIn('status', ['completed', 'partially_completed'])
                ->count(),
            'pending' => BulkDisbursement::where('company_id', $company->id)
                ->whereIn('status', ['draft', 'pending_approval', 'approved', 'processing'])
                ->count(),
            'failed' => BulkDisbursement::where('company_id', $company->id)
                ->where('status', 'failed')
                ->count(),
        ];
        
        // Get transaction statistics
        $transactionStats = [
            'total_amount' => BulkDisbursement::where('company_id', $company->id)
                ->whereIn('status', ['completed', 'partially_completed'])
                ->sum('total_amount'),
            'total_fee' => BulkDisbursement::where('company_id', $company->id)
                ->whereIn('status', ['completed', 'partially_completed'])
                ->sum('total_fee'),
            'transaction_count' => BulkDisbursement::where('company_id', $company->id)
                ->whereIn('status', ['completed', 'partially_completed'])
                ->sum('transaction_count'),
        ];
        
        // Get user roles
        $userRoles = $user->corporateRoles()->pluck('name')->toArray();
        
        return view('corporate.dashboard.index', compact(
            'company',
            'wallet',
            'recentDisbursements',
            'pendingApprovals',
            'disbursementStats',
            'transactionStats',
            'userRoles'
        ));
    }
}
