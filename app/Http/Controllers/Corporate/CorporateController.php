<?php

namespace App\Http\Controllers\Corporate;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\BulkDisbursement;
use App\Models\ApprovalRequest;
use App\Models\RateTier;
use App\Models\Activity;

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

        // Add this line to get the balance
        $balance = $wallet->balance;

        // Calculate monthly volume from completed/partially completed disbursements
        $monthlyVolume = BulkDisbursement::where('company_id', $company->id)
            ->whereIn('status', ['completed', 'partially_completed'])
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('total_amount');

        // Calculate recipient count for this month
        $recipientCount = BulkDisbursement::where('company_id', $company->id)
            ->whereIn('status', ['completed', 'partially_completed'])
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('transaction_count');

        // Get recent disbursements
        $recentDisbursements = BulkDisbursement::where('company_id', $company->id)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Get rate assignment and next tier
        $rateAssignment = $company->rateAssignment;
        $nextTier = null;
        if ($rateAssignment) {
            $nextTier = RateTier::where('monthly_volume_minimum', '>', $rateAssignment->rateTier->monthly_volume_minimum)
                ->orderBy('monthly_volume_minimum', 'asc')
                ->first();
        }

        // Get recent activity
        $recentActivity = Activity::where('company_id', $company->id)
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get()
            ->map(function ($activity) {
                return [
                    'type' => $activity->type,
                    'description' => $activity->description,
                    'created_at' => $activity->created_at,
                    // Add other needed activity fields
                ];
            });

        // Get pending approvals count
        $pendingApprovalsCount = ApprovalRequest::where('company_id', $company->id)
            ->where('status', 'pending')
            ->count();

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
            'balance',
            'monthlyVolume',
            'recipientCount',
            'recentDisbursements',
            'pendingApprovals',
            'pendingApprovalsCount',
            'disbursementStats',
            'transactionStats',
            'userRoles',
            'recentActivity',
            'rateAssignment',
            'nextTier'
        ));
    }
}
