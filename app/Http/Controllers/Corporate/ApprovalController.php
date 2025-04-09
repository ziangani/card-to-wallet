<?php

namespace App\Http\Controllers\Corporate;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\ApprovalRequest;
use App\Models\BulkDisbursement;
use App\Models\CorporateUserRole;
use App\Models\CompanyRateAssignment;
use App\Models\CorporateWalletTransaction;

class ApprovalController extends Controller
{
    /**
     * Display a listing of the approval requests.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $company = $user->company;
        
        // Get approval requests with filtering
        $query = ApprovalRequest::where('company_id', $company->id);
        
        // Filter by status
        if ($request->has('status') && $request->status != 'all') {
            $query->where('status', $request->status);
        } else {
            // Default to pending
            $query->where('status', 'pending');
        }
        
        // Filter by entity type
        if ($request->has('entity_type') && $request->entity_type != 'all') {
            $query->where('entity_type', $request->entity_type);
        }
        
        // Filter by date range
        if ($request->has('start_date') && $request->has('end_date')) {
            $query->whereBetween('created_at', [$request->start_date . ' 00:00:00', $request->end_date . ' 23:59:59']);
        }
        
        // Filter by requester
        if ($request->has('requester_id') && $request->requester_id != 'all') {
            $query->where('requested_by', $request->requester_id);
        }
        
        // Order by
        $query->orderBy('created_at', 'desc');
        
        // Paginate
        $approvalRequests = $query->paginate(10);
        
        // Get requesters for filter
        $requesters = ApprovalRequest::where('company_id', $company->id)
            ->select('requested_by')
            ->distinct()
            ->get()
            ->map(function ($request) {
                return $request->requester;
            });
        
        return view('corporate.approvals.index', compact(
            'company',
            'approvalRequests',
            'requesters'
        ));
    }
    
    /**
     * Display the specified approval request.
     *
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function show($id)
    {
        $user = Auth::user();
        $company = $user->company;
        
        // Get the approval request
        $approvalRequest = ApprovalRequest::where('company_id', $company->id)
            ->findOrFail($id);
        
        // Get the entity
        $entity = $approvalRequest->getEntity();
        
        // Get entity details based on entity type
        $entityDetails = $this->getEntityDetails($approvalRequest->entity_type, $entity);
        
        // Get approval actions
        $approvalActions = $approvalRequest->approvalActions()
            ->with('approver')
            ->orderBy('created_at', 'desc')
            ->get();
        
        // Check if the user can approve
        $canApprove = $user->hasCorporateRole('approver') || $user->hasCorporateRole('admin');
        
        // Check if the user has already approved
        $hasApproved = $approvalActions->where('approver_id', $user->id)->where('action', 'approved')->isNotEmpty();
        
        // Check if the user is the requester
        $isRequester = $approvalRequest->requested_by == $user->id;
        
        return view('corporate.approvals.show', compact(
            'company',
            'approvalRequest',
            'entity',
            'entityDetails',
            'approvalActions',
            'canApprove',
            'hasApproved',
            'isRequester'
        ));
    }
    
    /**
     * Approve the specified approval request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function approve(Request $request, $id)
    {
        $user = Auth::user();
        $company = $user->company;
        
        // Get the approval request
        $approvalRequest = ApprovalRequest::where('company_id', $company->id)
            ->findOrFail($id);
        
        // Check if the user can approve
        if (!$user->hasCorporateRole('approver') && !$user->hasCorporateRole('admin')) {
            return redirect()->back()->with('error', 'You do not have permission to approve this request.');
        }
        
        // Check if the request is pending
        if (!$approvalRequest->isPending()) {
            return redirect()->back()->with('error', 'This request is no longer pending approval.');
        }
        
        // Check if the user has already approved
        $hasApproved = $approvalRequest->approvalActions()
            ->where('approver_id', $user->id)
            ->where('action', 'approved')
            ->exists();
        
        if ($hasApproved) {
            return redirect()->back()->with('error', 'You have already approved this request.');
        }
        
        // Approve the request
        $approvalRequest->approve($user->id, $request->comments);
        
        // Process the approved entity
        $this->processApprovedEntity($approvalRequest->entity_type, $approvalRequest->getEntity(), $user->id);
        
        return redirect()->route('corporate.approvals.show', $approvalRequest->id)
            ->with('success', 'Request approved successfully.');
    }
    
    /**
     * Reject the specified approval request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function reject(Request $request, $id)
    {
        $user = Auth::user();
        $company = $user->company;
        
        // Get the approval request
        $approvalRequest = ApprovalRequest::where('company_id', $company->id)
            ->findOrFail($id);
        
        // Check if the user can approve/reject
        if (!$user->hasCorporateRole('approver') && !$user->hasCorporateRole('admin')) {
            return redirect()->back()->with('error', 'You do not have permission to reject this request.');
        }
        
        // Check if the request is pending
        if (!$approvalRequest->isPending()) {
            return redirect()->back()->with('error', 'This request is no longer pending approval.');
        }
        
        // Check if the user has already rejected
        $hasRejected = $approvalRequest->approvalActions()
            ->where('approver_id', $user->id)
            ->where('action', 'rejected')
            ->exists();
        
        if ($hasRejected) {
            return redirect()->back()->with('error', 'You have already rejected this request.');
        }
        
        // Reject the request
        $approvalRequest->reject($user->id, $request->comments);
        
        // Process the rejected entity
        $this->processRejectedEntity($approvalRequest->entity_type, $approvalRequest->getEntity());
        
        return redirect()->route('corporate.approvals.show', $approvalRequest->id)
            ->with('success', 'Request rejected successfully.');
    }
    
    /**
     * Get entity details based on entity type.
     *
     * @param  string  $entityType
     * @param  mixed  $entity
     * @return array
     */
    private function getEntityDetails($entityType, $entity)
    {
        if (!$entity) {
            return [
                'title' => 'Unknown Entity',
                'description' => 'The entity associated with this request could not be found.',
                'details' => [],
            ];
        }
        
        switch ($entityType) {
            case 'bulk_disbursement':
                return [
                    'title' => 'Bulk Disbursement',
                    'description' => $entity->name,
                    'details' => [
                        'Reference' => $entity->reference_number,
                        'Total Amount' => $entity->getFormattedTotalAmount(),
                        'Total Fee' => $entity->getFormattedTotalFee(),
                        'Total with Fee' => $entity->getFormattedTotalWithFee(),
                        'Transaction Count' => $entity->transaction_count,
                        'Status' => $entity->getStatusLabel(),
                        'Created At' => $entity->created_at->format('Y-m-d H:i:s'),
                    ],
                ];
            
            case 'user_role':
                $user = $entity->user;
                $role = $entity->role;
                
                return [
                    'title' => 'User Role Assignment',
                    'description' => 'Role assignment for ' . $user->name,
                    'details' => [
                        'User' => $user->name . ' (' . $user->email . ')',
                        'Role' => $role->getLabel(),
                        'Is Primary' => $entity->is_primary ? 'Yes' : 'No',
                        'Assigned At' => $entity->assigned_at->format('Y-m-d H:i:s'),
                    ],
                ];
            
            case 'rate_change':
                $rateTier = $entity->rateTier;
                
                return [
                    'title' => 'Rate Tier Change',
                    'description' => 'Rate tier change to ' . $rateTier->name,
                    'details' => [
                        'Rate Tier' => $rateTier->name,
                        'Fee Percentage' => $entity->getEffectiveFeePercentage() . '%',
                        'Override Fee' => $entity->hasOverride() ? $entity->override_fee_percentage . '%' : 'None',
                        'Effective From' => $entity->effective_from->format('Y-m-d'),
                        'Effective To' => $entity->effective_to ? $entity->effective_to->format('Y-m-d') : 'Indefinite',
                    ],
                ];
            
            case 'wallet_withdrawal':
                return [
                    'title' => 'Wallet Withdrawal',
                    'description' => 'Withdrawal of ' . $entity->getFormattedAmount(),
                    'details' => [
                        'Reference' => $entity->reference_number,
                        'Amount' => $entity->getFormattedAmount(),
                        'Description' => $entity->description,
                        'Status' => $entity->getStatusLabel(),
                        'Created At' => $entity->created_at->format('Y-m-d H:i:s'),
                    ],
                ];
            
            default:
                return [
                    'title' => 'Unknown Entity Type',
                    'description' => 'The entity type "' . $entityType . '" is not recognized.',
                    'details' => [],
                ];
        }
    }
    
    /**
     * Process the approved entity.
     *
     * @param  string  $entityType
     * @param  mixed  $entity
     * @param  int  $approverId
     * @return void
     */
    private function processApprovedEntity($entityType, $entity, $approverId)
    {
        if (!$entity) {
            return;
        }
        
        switch ($entityType) {
            case 'bulk_disbursement':
                // Approve the bulk disbursement
                $entity->approve($approverId);
                
                // In a real implementation, this would queue a job to process the disbursement
                // For now, we'll just update the status
                $entity->startProcessing();
                break;
            
            case 'user_role':
                // Activate the user role
                $entity->update(['is_active' => true]);
                break;
            
            case 'rate_change':
                // Activate the rate change
                $entity->update(['is_active' => true]);
                break;
            
            case 'wallet_withdrawal':
                // Process the withdrawal
                $wallet = $entity->corporateWallet;
                
                // Check if the wallet has sufficient balance
                if ($wallet->hasSufficientBalance($entity->amount)) {
                    // Update the wallet balance
                    $wallet->balance -= $entity->amount;
                    $wallet->save();
                    
                    // Update the transaction
                    $entity->update([
                        'status' => 'completed',
                        'balance_after' => $wallet->balance,
                    ]);
                } else {
                    // Mark the transaction as failed
                    $entity->update([
                        'status' => 'failed',
                        'description' => $entity->description . ' (Failed: Insufficient balance)',
                    ]);
                }
                break;
        }
    }
    
    /**
     * Process the rejected entity.
     *
     * @param  string  $entityType
     * @param  mixed  $entity
     * @return void
     */
    private function processRejectedEntity($entityType, $entity)
    {
        if (!$entity) {
            return;
        }
        
        switch ($entityType) {
            case 'bulk_disbursement':
                // Cancel the bulk disbursement
                $entity->cancel();
                break;
            
            case 'user_role':
                // Delete the user role
                $entity->delete();
                break;
            
            case 'rate_change':
                // Delete the rate change
                $entity->delete();
                break;
            
            case 'wallet_withdrawal':
                // Mark the withdrawal as failed
                $entity->update([
                    'status' => 'failed',
                    'description' => $entity->description . ' (Rejected)',
                ]);
                break;
        }
    }
}
