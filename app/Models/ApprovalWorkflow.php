<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApprovalWorkflow extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'company_id',
        'entity_type',
        'min_approvers',
        'amount_threshold',
        'is_active',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'min_approvers' => 'integer',
        'amount_threshold' => 'decimal:2',
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the company that owns the workflow.
     */
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Get the approval requests for the workflow.
     */
    public function approvalRequests()
    {
        return $this->hasMany(ApprovalRequest::class, 'entity_type', 'entity_type')
            ->where('company_id', $this->company_id);
    }

    /**
     * Check if the workflow is active.
     *
     * @return bool
     */
    public function isActive()
    {
        return $this->is_active;
    }

    /**
     * Check if the workflow requires approval for a given amount.
     *
     * @param  float  $amount
     * @return bool
     */
    public function requiresApproval($amount = null)
    {
        if (!$this->is_active) {
            return false;
        }

        if ($this->amount_threshold && $amount && $amount < $this->amount_threshold) {
            return false;
        }

        return true;
    }

    /**
     * Get the number of required approvers.
     *
     * @return int
     */
    public function getRequiredApprovers()
    {
        return $this->min_approvers;
    }

    /**
     * Scope a query to only include active workflows.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to only include workflows for a specific entity type.
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
     * Scope a query to only include workflows for bulk disbursements.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeForBulkDisbursements($query)
    {
        return $query->where('entity_type', 'bulk_disbursement');
    }

    /**
     * Scope a query to only include workflows for user roles.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeForUserRoles($query)
    {
        return $query->where('entity_type', 'user_role');
    }

    /**
     * Scope a query to only include workflows for rate changes.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeForRateChanges($query)
    {
        return $query->where('entity_type', 'rate_change');
    }

    /**
     * Scope a query to only include workflows for wallet withdrawals.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeForWalletWithdrawals($query)
    {
        return $query->where('entity_type', 'wallet_withdrawal');
    }
}
