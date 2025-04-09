<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use App\Models\ApprovalWorkflow;
use App\Models\ApprovalRequest;
use Illuminate\Support\Str;

class ApprovalRequired
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $entityType): Response
    {
        $user = Auth::user();

        // Check if user is authenticated
        if (!$user) {
            return redirect()->route('login');
        }

        // Check if user is a corporate user
        if ($user->user_type !== 'corporate' || !$user->company_id) {
            return redirect()->route('dashboard')->with('error', 'You do not have access to the corporate section.');
        }

        $company = $user->company;

        // Check if the entity type requires approval
        $workflow = ApprovalWorkflow::where('company_id', $company->id)
            ->where('entity_type', $entityType)
            ->where('is_active', true)
            ->first();

        // If no workflow is defined or it's not active, proceed without approval
        if (!$workflow) {
            return $next($request);
        }

        // Check if the amount threshold is defined and if the request amount exceeds it
        $amount = $request->input('amount', 0);
        if ($workflow->amount_threshold && $amount < $workflow->amount_threshold) {
            // Amount is below threshold, proceed without approval
            return $next($request);
        }

        // Store the original request data in the session
        $requestData = $request->all();
        $requestData['_approval_entity_type'] = $entityType;
        $requestData['_approval_requested_by'] = $user->id;
        
        // Generate a unique identifier for this approval request
        $approvalId = Str::uuid();
        session(['approval_request_' . $approvalId => $requestData]);

        // Create an approval request
        $approvalRequest = ApprovalRequest::create([
            'uuid' => Str::uuid(),
            'company_id' => $company->id,
            'entity_type' => $entityType,
            'entity_id' => null, // Will be set after the entity is created
            'requested_by' => $user->id,
            'status' => 'pending',
            'required_approvals' => $workflow->min_approvers,
            'received_approvals' => 0,
            'description' => 'Approval required for ' . $entityType,
            'expires_at' => now()->addDays(7),
        ]);

        // Redirect to the approval pending page
        return redirect()->route('corporate.approvals.pending', ['id' => $approvalRequest->id])
            ->with('approval_id', $approvalId)
            ->with('info', 'This action requires approval. An approval request has been created.');
    }
}
