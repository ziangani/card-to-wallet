<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BulkDisbursement extends Model
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
        'corporate_wallet_id',
        'name',
        'description',
        'file_path',
        'total_amount',
        'total_fee',
        'transaction_count',
        'currency',
        'status',
        'initiated_by',
        'approved_by',
        'approved_at',
        'completed_at',
        'reference_number',
        'notes',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'total_amount' => 'decimal:2',
        'total_fee' => 'decimal:2',
        'transaction_count' => 'integer',
        'approved_at' => 'datetime',
        'completed_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the company that owns the bulk disbursement.
     */
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Get the corporate wallet that owns the bulk disbursement.
     */
    public function corporateWallet()
    {
        return $this->belongsTo(CorporateWallet::class);
    }

    /**
     * Get the disbursement items for the bulk disbursement.
     */
    public function items()
    {
        return $this->hasMany(DisbursementItem::class, 'bulk_disbursement_id');
    }

    /**
     * Get the user who initiated the bulk disbursement.
     */
    public function initiator()
    {
        return $this->belongsTo(User::class, 'initiated_by');
    }

    /**
     * Get the user who approved the bulk disbursement.
     */
    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Get the approval request for the bulk disbursement.
     */
    public function approvalRequest()
    {
        return $this->morphOne(ApprovalRequest::class, 'approvable');
    }

    /**
     * Check if the bulk disbursement is a draft.
     *
     * @return bool
     */
    public function isDraft()
    {
        return $this->status === 'draft';
    }

    /**
     * Check if the bulk disbursement is pending approval.
     *
     * @return bool
     */
    public function isPendingApproval()
    {
        return $this->status === 'pending_approval';
    }

    /**
     * Check if the bulk disbursement is approved.
     *
     * @return bool
     */
    public function isApproved()
    {
        return $this->status === 'approved';
    }

    /**
     * Check if the bulk disbursement is processing.
     *
     * @return bool
     */
    public function isProcessing()
    {
        return $this->status === 'processing';
    }

    /**
     * Check if the bulk disbursement is completed.
     *
     * @return bool
     */
    public function isCompleted()
    {
        return $this->status === 'completed';
    }

    /**
     * Check if the bulk disbursement is partially completed.
     *
     * @return bool
     */
    public function isPartiallyCompleted()
    {
        return $this->status === 'partially_completed';
    }

    /**
     * Check if the bulk disbursement is failed.
     *
     * @return bool
     */
    public function isFailed()
    {
        return $this->status === 'failed';
    }

    /**
     * Check if the bulk disbursement is cancelled.
     *
     * @return bool
     */
    public function isCancelled()
    {
        return $this->status === 'cancelled';
    }

    /**
     * Get the status label.
     *
     * @return string
     */
    public function getStatusLabel()
    {
        $labels = [
            'draft' => 'Draft',
            'pending_approval' => 'Pending Approval',
            'approved' => 'Approved',
            'processing' => 'Processing',
            'completed' => 'Completed',
            'partially_completed' => 'Partially Completed',
            'failed' => 'Failed',
            'cancelled' => 'Cancelled',
        ];

        return $labels[$this->status] ?? ucfirst(str_replace('_', ' ', $this->status));
    }

    /**
     * Get the total amount with fee.
     *
     * @return float
     */
    public function getTotalWithFee()
    {
        return $this->total_amount + $this->total_fee;
    }

    /**
     * Get the formatted total amount.
     *
     * @return string
     */
    public function getFormattedTotalAmount()
    {
        return number_format($this->total_amount, 2) . ' ' . $this->currency;
    }

    /**
     * Get the formatted total fee.
     *
     * @return string
     */
    public function getFormattedTotalFee()
    {
        return number_format($this->total_fee, 2) . ' ' . $this->currency;
    }

    /**
     * Get the formatted total amount with fee.
     *
     * @return string
     */
    public function getFormattedTotalWithFee()
    {
        return number_format($this->getTotalWithFee(), 2) . ' ' . $this->currency;
    }

    /**
     * Get the success rate.
     *
     * @return float
     */
    public function getSuccessRate()
    {
        $completedCount = $this->items()->completed()->count();
        
        if ($this->transaction_count === 0) {
            return 0;
        }
        
        return ($completedCount / $this->transaction_count) * 100;
    }

    /**
     * Get the formatted success rate.
     *
     * @return string
     */
    public function getFormattedSuccessRate()
    {
        return number_format($this->getSuccessRate(), 2) . '%';
    }

    /**
     * Get the failure rate.
     *
     * @return float
     */
    public function getFailureRate()
    {
        $failedCount = $this->items()->failed()->count();
        
        if ($this->transaction_count === 0) {
            return 0;
        }
        
        return ($failedCount / $this->transaction_count) * 100;
    }

    /**
     * Get the formatted failure rate.
     *
     * @return string
     */
    public function getFormattedFailureRate()
    {
        return number_format($this->getFailureRate(), 2) . '%';
    }

    /**
     * Submit the bulk disbursement for approval.
     *
     * @return bool
     */
    public function submitForApproval()
    {
        if (!$this->isDraft()) {
            return false;
        }

        $this->status = 'pending_approval';
        return $this->save();
    }

    /**
     * Approve the bulk disbursement.
     *
     * @param  int  $approverId
     * @return bool
     */
    public function approve($approverId)
    {
        if (!$this->isPendingApproval()) {
            return false;
        }

        $this->status = 'approved';
        $this->approved_by = $approverId;
        $this->approved_at = now();
        return $this->save();
    }

    /**
     * Start processing the bulk disbursement.
     *
     * @return bool
     */
    public function startProcessing()
    {
        if (!$this->isApproved()) {
            return false;
        }

        $this->status = 'processing';
        return $this->save();
    }

    /**
     * Complete the bulk disbursement.
     *
     * @param  bool  $isPartial
     * @return bool
     */
    public function complete($isPartial = false)
    {
        if (!$this->isProcessing()) {
            return false;
        }

        $this->status = $isPartial ? 'partially_completed' : 'completed';
        $this->completed_at = now();
        return $this->save();
    }

    /**
     * Fail the bulk disbursement.
     *
     * @return bool
     */
    public function fail()
    {
        if (!$this->isProcessing()) {
            return false;
        }

        $this->status = 'failed';
        return $this->save();
    }

    /**
     * Cancel the bulk disbursement.
     *
     * @return bool
     */
    public function cancel()
    {
        if (!$this->isDraft() && !$this->isPendingApproval()) {
            return false;
        }

        $this->status = 'cancelled';
        return $this->save();
    }

    /**
     * Scope a query to only include draft bulk disbursements.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeDraft($query)
    {
        return $query->where('status', 'draft');
    }

    /**
     * Scope a query to only include pending approval bulk disbursements.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopePendingApproval($query)
    {
        return $query->where('status', 'pending_approval');
    }

    /**
     * Scope a query to only include approved bulk disbursements.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    /**
     * Scope a query to only include processing bulk disbursements.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeProcessing($query)
    {
        return $query->where('status', 'processing');
    }

    /**
     * Scope a query to only include completed bulk disbursements.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    /**
     * Scope a query to only include partially completed bulk disbursements.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopePartiallyCompleted($query)
    {
        return $query->where('status', 'partially_completed');
    }

    /**
     * Scope a query to only include failed bulk disbursements.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }

    /**
     * Scope a query to only include cancelled bulk disbursements.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeCancelled($query)
    {
        return $query->where('status', 'cancelled');
    }

    /**
     * Scope a query to only include bulk disbursements for a specific company.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  int  $companyId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeForCompany($query, $companyId)
    {
        return $query->where('company_id', $companyId);
    }

    /**
     * Scope a query to only include bulk disbursements initiated by a specific user.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  int  $userId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeInitiatedBy($query, $userId)
    {
        return $query->where('initiated_by', $userId);
    }

    /**
     * Scope a query to only include bulk disbursements approved by a specific user.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  int  $userId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeApprovedBy($query, $userId)
    {
        return $query->where('approved_by', $userId);
    }

    /**
     * Scope a query to only include bulk disbursements with a specific currency.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  string  $currency
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWithCurrency($query, $currency)
    {
        return $query->where('currency', $currency);
    }

    /**
     * Scope a query to only include bulk disbursements created within a specific date range.
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
     * Scope a query to only include bulk disbursements completed within a specific date range.
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
