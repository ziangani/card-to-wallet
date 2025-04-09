<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApprovalRequest extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'uuid',
        'company_id',
        'entity_type',
        'entity_id',
        'requested_by',
        'status',
        'required_approvals',
        'received_approvals',
        'description',
        'expires_at',
        'completed_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'expires_at' => 'datetime',
        'completed_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the company that owns the approval request.
     */
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Get the user who requested the approval.
     */
    public function requester()
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    /**
     * Get the approval actions for the approval request.
     */
    public function approvalActions()
    {
        return $this->hasMany(ApprovalAction::class);
    }

    /**
     * Get the entity that the approval request is for.
     */
    public function getEntity()
    {
        switch ($this->entity_type) {
            case 'bulk_disbursement':
                return BulkDisbursement::find($this->entity_id);
            
            case 'user_role':
                return CorporateUserRole::find($this->entity_id);
            
            case 'rate_change':
                return CompanyRateAssignment::find($this->entity_id);
            
            case 'wallet_withdrawal':
                return CorporateWalletTransaction::find($this->entity_id);
            
            default:
                return null;
        }
    }

    /**
     * Check if the approval request is pending.
     *
     * @return bool
     */
    public function isPending()
    {
        return $this->status === 'pending';
    }

    /**
     * Check if the approval request is approved.
     *
     * @return bool
     */
    public function isApproved()
    {
        return $this->status === 'approved';
    }

    /**
     * Check if the approval request is rejected.
     *
     * @return bool
     */
    public function isRejected()
    {
        return $this->status === 'rejected';
    }

    /**
     * Check if the approval request is cancelled.
     *
     * @return bool
     */
    public function isCancelled()
    {
        return $this->status === 'cancelled';
    }

    /**
     * Check if the approval request is expired.
     *
     * @return bool
     */
    public function isExpired()
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    /**
     * Approve the approval request.
     *
     * @param  int  $approverId
     * @param  string|null  $comments
     * @return bool
     */
    public function approve($approverId, $comments = null)
    {
        // Check if the request is pending
        if (!$this->isPending()) {
            return false;
        }

        // Create an approval action
        $this->approvalActions()->create([
            'approver_id' => $approverId,
            'action' => 'approved',
            'comments' => $comments,
            'ip_address' => request()->ip(),
        ]);

        // Increment the received approvals
        $this->received_approvals++;

        // Check if the required approvals have been received
        if ($this->received_approvals >= $this->required_approvals) {
            $this->status = 'approved';
            $this->completed_at = now();
        }

        return $this->save();
    }

    /**
     * Reject the approval request.
     *
     * @param  int  $approverId
     * @param  string|null  $comments
     * @return bool
     */
    public function reject($approverId, $comments = null)
    {
        // Check if the request is pending
        if (!$this->isPending()) {
            return false;
        }

        // Create an approval action
        $this->approvalActions()->create([
            'approver_id' => $approverId,
            'action' => 'rejected',
            'comments' => $comments,
            'ip_address' => request()->ip(),
        ]);

        // Update the status
        $this->status = 'rejected';
        $this->completed_at = now();

        return $this->save();
    }

    /**
     * Cancel the approval request.
     *
     * @return bool
     */
    public function cancel()
    {
        // Check if the request is pending
        if (!$this->isPending()) {
            return false;
        }

        // Update the status
        $this->status = 'cancelled';
        $this->completed_at = now();

        return $this->save();
    }

    /**
     * Scope a query to only include pending approval requests.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope a query to only include approved approval requests.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    /**
     * Scope a query to only include rejected approval requests.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }

    /**
     * Scope a query to only include cancelled approval requests.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeCancelled($query)
    {
        return $query->where('status', 'cancelled');
    }

    /**
     * Scope a query to only include approval requests for a specific entity type.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  string  $entityType
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeForEntityType($query, $entityType)
    {
        return $query->where('entity_type', $entityType);
    }

    /**
     * Scope a query to only include approval requests for a specific entity.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  string  $entityType
     * @param  int  $entityId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeForEntity($query, $entityType, $entityId)
    {
        return $query->where('entity_type', $entityType)->where('entity_id', $entityId);
    }

    /**
     * Scope a query to only include approval requests requested by a specific user.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  int  $userId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeRequestedBy($query, $userId)
    {
        return $query->where('requested_by', $userId);
    }

    /**
     * Scope a query to only include approval requests that have expired.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeExpired($query)
    {
        return $query->where('expires_at', '<', now())->where('status', 'pending');
    }

    /**
     * Scope a query to only include approval requests that have not expired.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeNotExpired($query)
    {
        return $query->where(function ($query) {
            $query->whereNull('expires_at')->orWhere('expires_at', '>=', now());
        });
    }

    /**
     * Scope a query to only include approval requests created within a specific date range.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  \Illuminate\Support\Carbon  $startDate
     * @param  \Illuminate\Support\Carbon  $endDate
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeCreatedBetween($query, $startDate, $endDate)
    {
        return $query->whereBetween('created_at', [$startDate, $endDate]);
    }

    /**
     * Scope a query to only include approval requests completed within a specific date range.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  \Illuminate\Support\Carbon  $startDate
     * @param  \Illuminate\Support\Carbon  $endDate
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeCompletedBetween($query, $startDate, $endDate)
    {
        return $query->whereBetween('completed_at', [$startDate, $endDate]);
    }
}
