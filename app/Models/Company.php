<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'uuid',
        'name',
        'registration_number',
        'tax_id',
        'industry',
        'address',
        'city',
        'country',
        'phone_number',
        'email',
        'website',
        'verification_status',
        'status'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the users for the company.
     */
    public function users()
    {
        return $this->hasMany(User::class);
    }

    /**
     * Get the corporate wallet for the company.
     */
    public function corporateWallet()
    {
        return $this->hasOne(CorporateWallet::class);
    }

    /**
     * Get the documents for the company.
     */
    public function documents()
    {
        return $this->hasMany(CompanyDocument::class);
    }

    /**
     * Get the bulk disbursements for the company.
     */
    public function bulkDisbursements()
    {
        return $this->hasMany(BulkDisbursement::class);
    }

    /**
     * Get the rate assignment for the company.
     */
    public function rateAssignment()
    {
        return $this->hasOne(CompanyRateAssignment::class)
            ->where(function ($query) {
                $query->whereNull('effective_to')
                    ->orWhere('effective_to', '>=', now());
            })
            ->where('effective_from', '<=', now())
            ->latest('effective_from');
    }

    /**
     * Get all rate assignments for the company.
     */
    public function rateAssignments()
    {
        return $this->hasMany(CompanyRateAssignment::class);
    }

    /**
     * Get the approval workflows for the company.
     */
    public function approvalWorkflows()
    {
        return $this->hasMany(ApprovalWorkflow::class);
    }

    /**
     * Get the approval requests for the company.
     */
    public function approvalRequests()
    {
        return $this->hasMany(ApprovalRequest::class);
    }

    /**
     * Get the corporate user roles for the company.
     */
    public function corporateUserRoles()
    {
        return $this->hasMany(CorporateUserRole::class);
    }

    /**
     * Check if the company is active.
     *
     * @return bool
     */
    public function isActive()
    {
        return $this->status === 'active';
    }

    /**
     * Check if the company is suspended.
     *
     * @return bool
     */
    public function isSuspended()
    {
        return $this->status === 'suspended';
    }

    /**
     * Check if the company is inactive.
     *
     * @return bool
     */
    public function isInactive()
    {
        return $this->status === 'inactive';
    }

    /**
     * Check if the company is verified.
     *
     * @return bool
     */
    public function isVerified()
    {
        return $this->verification_status === 'approved';
    }

    /**
     * Check if the company is pending verification.
     *
     * @return bool
     */
    public function isPendingVerification()
    {
        return $this->verification_status === 'pending';
    }

    /**
     * Check if the company is rejected.
     *
     * @return bool
     */
    public function isRejected()
    {
        return $this->verification_status === 'rejected';
    }

    /**
     * Get the company's current rate tier.
     *
     * @return \App\Models\CorporateRateTier|null
     */
    public function getCurrentRateTier()
    {
        $rateAssignment = $this->rateAssignment;

        if (!$rateAssignment) {
            return null;
        }

        return $rateAssignment->rateTier;
    }

    /**
     * Get the company's current fee percentage.
     *
     * @return float
     */
    public function getCurrentFeePercentage()
    {
        $rateAssignment = $this->rateAssignment;

        if (!$rateAssignment) {
            return 3.5; // Default fee percentage
        }

        return $rateAssignment->getEffectiveFeePercentage();
    }

    /**
     * Get the company's logo URL.
     *
     * @return string|null
     */
    public function getLogoUrl()
    {
        if (!$this->logo_path) {
            return null;
        }

        return asset('storage/' . $this->logo_path);
    }

    /**
     * Scope a query to only include active companies.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope a query to only include suspended companies.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSuspended($query)
    {
        return $query->where('status', 'suspended');
    }

    /**
     * Scope a query to only include inactive companies.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeInactive($query)
    {
        return $query->where('status', 'inactive');
    }

    /**
     * Scope a query to only include verified companies.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeVerified($query)
    {
        return $query->where('verification_status', 'approved');
    }

    /**
     * Scope a query to only include pending verification companies.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopePendingVerification($query)
    {
        return $query->where('verification_status', 'pending');
    }

    /**
     * Scope a query to only include rejected companies.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeRejected($query)
    {
        return $query->where('verification_status', 'rejected');
    }

    /**
     * Create default approval workflows for the company.
     *
     * @param  int  $companyId
     * @return void
     */
    public function createDefaultApprovalWorkflows($companyId)
    {
        $workflowTypes = [
            'bulk_disbursement' => 1,
            'user_role' => 1,
            'rate_change' => 1,
            'wallet_withdrawal' => 1,
        ];

        foreach ($workflowTypes as $type => $minApprovers) {
            ApprovalWorkflow::create([
                'company_id' => $companyId,
                'entity_type' => $type,
                'min_approvers' => $minApprovers,
                'amount_threshold' => null,
                'is_active' => true,
            ]);
        }
    }
}
