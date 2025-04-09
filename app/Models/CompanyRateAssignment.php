<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompanyRateAssignment extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'company_id',
        'rate_tier_id',
        'override_fee_percentage',
        'assigned_by',
        'effective_from',
        'effective_to',
        'notes',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'override_fee_percentage' => 'decimal:2',
        'effective_from' => 'datetime',
        'effective_to' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the company that owns the rate assignment.
     */
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Get the rate tier that owns the rate assignment.
     */
    public function rateTier()
    {
        return $this->belongsTo(CorporateRateTier::class, 'rate_tier_id');
    }

    /**
     * Get the user who assigned the rate.
     */
    public function assigner()
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }

    /**
     * Check if the rate assignment is active.
     *
     * @return bool
     */
    public function isActive()
    {
        $now = now();
        
        if ($this->effective_to) {
            return $this->effective_from <= $now && $this->effective_to >= $now;
        }
        
        return $this->effective_from <= $now;
    }

    /**
     * Check if the rate assignment has an override fee percentage.
     *
     * @return bool
     */
    public function hasOverride()
    {
        return $this->override_fee_percentage !== null;
    }

    /**
     * Get the effective fee percentage.
     *
     * @return float
     */
    public function getEffectiveFeePercentage()
    {
        if ($this->hasOverride()) {
            return $this->override_fee_percentage;
        }
        
        return $this->rateTier ? $this->rateTier->fee_percentage : 3.5;
    }

    /**
     * Scope a query to only include active rate assignments.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive($query)
    {
        $now = now();
        
        return $query->where('effective_from', '<=', $now)
            ->where(function ($query) use ($now) {
                $query->whereNull('effective_to')
                    ->orWhere('effective_to', '>=', $now);
            });
    }

    /**
     * Scope a query to only include rate assignments for a specific company.
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
     * Scope a query to only include rate assignments for a specific rate tier.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  int  $rateTierId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeForRateTier($query, $rateTierId)
    {
        return $query->where('rate_tier_id', $rateTierId);
    }

    /**
     * Scope a query to only include rate assignments with an override fee percentage.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWithOverride($query)
    {
        return $query->whereNotNull('override_fee_percentage');
    }

    /**
     * Scope a query to only include rate assignments without an override fee percentage.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWithoutOverride($query)
    {
        return $query->whereNull('override_fee_percentage');
    }

    /**
     * Scope a query to only include rate assignments assigned by a specific user.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  int  $userId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeAssignedBy($query, $userId)
    {
        return $query->where('assigned_by', $userId);
    }

    /**
     * Scope a query to only include rate assignments effective from a specific date.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  \Illuminate\Support\Carbon  $date
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeEffectiveFrom($query, $date)
    {
        return $query->where('effective_from', '>=', $date);
    }

    /**
     * Scope a query to only include rate assignments effective to a specific date.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  \Illuminate\Support\Carbon  $date
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeEffectiveTo($query, $date)
    {
        return $query->where(function ($query) use ($date) {
            $query->whereNull('effective_to')
                ->orWhere('effective_to', '<=', $date);
        });
    }
}
