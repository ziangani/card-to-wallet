<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MerchantPayout extends Model
{
    protected $guarded = ['id'];
    
    protected $casts = [
        'amount' => 'decimal:2',
        'remittance_fee' => 'decimal:2',
        'initiated_at' => 'datetime',
        'completed_at' => 'datetime',
    ];
    
    /**
     * Get the merchant that owns the payout.
     */
    public function merchant(): BelongsTo
    {
        return $this->belongsTo(Merchants::class, 'merchant_id', 'code');
    }
    
    /**
     * Get the net amount after remittance fee.
     */
    public function getNetAmountAttribute()
    {
        return $this->amount - $this->remittance_fee;
    }
    
    /**
     * Mark the payout as completed.
     */
    public function markAsCompleted($reference = null)
    {
        return $this->update([
            'status' => 'COMPLETED',
            'completed_at' => now(),
            'reference' => $reference ?? $this->reference,
        ]);
    }
    
    /**
     * Mark the payout as failed.
     */
    public function markAsFailed($notes = null)
    {
        return $this->update([
            'status' => 'FAILED',
            'notes' => $notes ?? $this->notes,
        ]);
    }
    
    /**
     * Mark the payout as cancelled.
     */
    public function markAsCancelled($notes = null)
    {
        return $this->update([
            'status' => 'CANCELLED',
            'notes' => $notes ?? $this->notes,
        ]);
    }
    
    /**
     * Scope a query to only include payouts of a specific type.
     */
    public function scopeOfType($query, $type)
    {
        return $query->where('type', $type);
    }
    
    /**
     * Scope a query to only include payouts with a specific status.
     */
    public function scopeWithStatus($query, $status)
    {
        return $query->where('status', $status);
    }
}
