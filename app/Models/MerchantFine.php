<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MerchantFine extends Model
{
    protected $guarded = ['id'];
    
    protected $casts = [
        'amount' => 'decimal:2',
        'issued_date' => 'date',
        'due_date' => 'date',
        'paid_date' => 'date',
    ];
    
    /**
     * Get the merchant that owns the fine.
     */
    public function merchant(): BelongsTo
    {
        return $this->belongsTo(Merchants::class, 'merchant_id', 'code');
    }
    
    /**
     * Mark the fine as paid.
     */
    public function markAsPaid($paidBy = null, $paidDate = null)
    {
        return $this->update([
            'status' => 'PAID',
            'paid_date' => $paidDate ?? now(),
            'paid_by' => $paidBy ?? $this->paid_by,
        ]);
    }
    
    /**
     * Mark the fine as disputed.
     */
    public function markAsDisputed($notes = null)
    {
        return $this->update([
            'status' => 'DISPUTED',
            'notes' => $notes ?? $this->notes,
        ]);
    }
    
    /**
     * Mark the fine as waived.
     */
    public function markAsWaived($notes = null)
    {
        return $this->update([
            'status' => 'WAIVED',
            'notes' => $notes ?? $this->notes,
        ]);
    }
    
    /**
     * Scope a query to only include fines from a specific issuer.
     */
    public function scopeWithIssuer($query, $issuer)
    {
        return $query->where('issuer', $issuer);
    }
    
    /**
     * Scope a query to only include fines with a specific status.
     */
    public function scopeWithStatus($query, $status)
    {
        return $query->where('status', $status);
    }
    
    /**
     * Scope a query to only include overdue fines.
     */
    public function scopeOverdue($query)
    {
        return $query->where('status', 'PENDING')
            ->whereNotNull('due_date')
            ->where('due_date', '<', now());
    }
}
