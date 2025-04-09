<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CorporateRateTier extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'monthly_volume_minimum',
        'fee_percentage',
        'description',
        'is_active',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'monthly_volume_minimum' => 'decimal:2',
        'fee_percentage' => 'decimal:2',
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the company rate assignments for the rate tier.
     */
    public function companyRateAssignments()
    {
        return $this->hasMany(CompanyRateAssignment::class, 'rate_tier_id');
    }

    /**
     * Check if the rate tier is active.
     *
     * @return bool
     */
    public function isActive()
    {
        return $this->is_active;
    }

    /**
     * Get the next tier.
     *
     * @return \App\Models\CorporateRateTier|null
     */
    public function getNextTier()
    {
        return self::where('monthly_volume_minimum', '>', $this->monthly_volume_minimum)
            ->where('is_active', true)
            ->orderBy('monthly_volume_minimum', 'asc')
            ->first();
    }

    /**
     * Get the previous tier.
     *
     * @return \App\Models\CorporateRateTier|null
     */
    public function getPreviousTier()
    {
        return self::where('monthly_volume_minimum', '<', $this->monthly_volume_minimum)
            ->where('is_active', true)
            ->orderBy('monthly_volume_minimum', 'desc')
            ->first();
    }

    /**
     * Check if a company qualifies for this tier based on monthly volume.
     *
     * @param  float  $monthlyVolume
     * @return bool
     */
    public function qualifiesWithVolume($monthlyVolume)
    {
        // Get the next tier
        $nextTier = $this->getNextTier();
        
        // If there's no next tier, qualify if volume is at least the minimum
        if (!$nextTier) {
            return $monthlyVolume >= $this->monthly_volume_minimum;
        }
        
        // Otherwise, qualify if volume is between this tier's minimum and the next tier's minimum
        return $monthlyVolume >= $this->monthly_volume_minimum && $monthlyVolume < $nextTier->monthly_volume_minimum;
    }

    /**
     * Get the appropriate tier for a given monthly volume.
     *
     * @param  float  $monthlyVolume
     * @return \App\Models\CorporateRateTier
     */
    public static function getTierForVolume($monthlyVolume)
    {
        return self::where('monthly_volume_minimum', '<=', $monthlyVolume)
            ->where('is_active', true)
            ->orderBy('monthly_volume_minimum', 'desc')
            ->first();
    }

    /**
     * Scope a query to only include active tiers.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to only include tiers with a minimum volume less than or equal to a given amount.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  float  $volume
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeQualifyingForVolume($query, $volume)
    {
        return $query->where('monthly_volume_minimum', '<=', $volume);
    }

    /**
     * Scope a query to only include tiers with a fee percentage less than or equal to a given amount.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  float  $percentage
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWithMaxFeePercentage($query, $percentage)
    {
        return $query->where('fee_percentage', '<=', $percentage);
    }

    /**
     * Scope a query to only include tiers with a fee percentage greater than or equal to a given amount.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  float  $percentage
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWithMinFeePercentage($query, $percentage)
    {
        return $query->where('fee_percentage', '>=', $percentage);
    }

    /**
     * Scope a query to order tiers by monthly volume minimum in ascending order.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOrderByVolumeAsc($query)
    {
        return $query->orderBy('monthly_volume_minimum', 'asc');
    }

    /**
     * Scope a query to order tiers by monthly volume minimum in descending order.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOrderByVolumeDesc($query)
    {
        return $query->orderBy('monthly_volume_minimum', 'desc');
    }

    /**
     * Scope a query to order tiers by fee percentage in ascending order.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOrderByFeeAsc($query)
    {
        return $query->orderBy('fee_percentage', 'asc');
    }

    /**
     * Scope a query to order tiers by fee percentage in descending order.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOrderByFeeDesc($query)
    {
        return $query->orderBy('fee_percentage', 'desc');
    }
}
