<?php

namespace App\Models;

use App\Enums\ReconciliationStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MerchantReconciliation extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'status' => ReconciliationStatus::class,
        'date' => 'date',
        'transaction_count' => 'integer',
        'total_amount' => 'decimal:2',
        'platform_fee' => 'decimal:2',
        'bank_fee' => 'decimal:2',
        'application_fee' => 'decimal:2',
        'rolling_reserve' => 'decimal:2',
        'return_reserve' => 'decimal:2',
        'refund_amount' => 'decimal:2',
        'chargeback_count' => 'integer',
        'chargeback_amount' => 'decimal:2',
        'chargeback_fees' => 'decimal:2',
        'net_processed' => 'decimal:2',
        'settled_amount' => 'decimal:2',
        'generated_at' => 'datetime',
    ];

    /**
     * Get the merchant that owns the reconciliation.
     */
    public function merchant(): BelongsTo
    {
        return $this->belongsTo(Merchants::class, 'merchant_id', 'code');
    }
    
    /**
     * Get the payouts associated with this reconciliation.
     */
    public function payouts(): HasMany
    {
        return $this->hasMany(MerchantPayout::class, 'merchant_id', 'merchant_id')
            ->whereDate('created_at', $this->date);
    }
    
    /**
     * Get the fines associated with this reconciliation.
     */
    public function fines(): HasMany
    {
        return $this->hasMany(MerchantFine::class, 'merchant_id', 'merchant_id')
            ->whereDate('issued_date', $this->date);
    }

    /**
     * Get the latest active reconciliation for a merchant and date
     */
    public static function getActive(string $merchantId, string $date)
    {
        return static::where([
            'merchant_id' => $merchantId,
            'date' => $date,
            'status' => 'ACTIVE'
        ])->first();
    }

    /**
     * Get reconciliation history for a merchant and date
     */
    public static function getHistory(string $merchantId, string $date)
    {
        return static::where([
            'merchant_id' => $merchantId,
            'date' => $date
        ])->orderBy('version', 'desc')->get();
    }

    /**
     * Mark current reconciliation as superseded
     */
    public function markAsSuperseded(string $reason): bool
    {
        return $this->update([
            'status' => 'SUPERSEDED',
            'reason' => $reason
        ]);
    }

    /**
     * Get the next version number for a merchant and date
     */
    public static function getNextVersion(string $merchantId, string $date): int
    {
        $maxVersion = static::where([
            'merchant_id' => $merchantId,
            'date' => $date
        ])->max('version');

        return ($maxVersion ?? 0) + 1;
    }

    /**
     * Format amount with currency
     */
    private function formatAmount($amount, $currency = 'USD'): string
    {
        return number_format($amount, 2) . ' ' . $currency;
    }

    /**
     * Get formatted amounts for display
     */
    public function getFormattedAmounts(): array
    {
        return [
            'total_amount' => $this->formatAmount($this->total_amount),
            'platform_fee' => $this->formatAmount($this->platform_fee),
            'application_fee' => $this->formatAmount($this->application_fee),
            'rolling_reserve' => $this->formatAmount($this->rolling_reserve),
            'return_reserve' => $this->formatAmount($this->return_reserve),
            'refund_amount' => $this->formatAmount($this->refund_amount),
            'chargeback_amount' => $this->formatAmount($this->chargeback_amount),
            'chargeback_fees' => $this->formatAmount($this->chargeback_fees),
            'net_processed' => $this->formatAmount($this->net_processed),
            'settled_amount' => $this->formatAmount($this->settled_amount),
        ];
    }
    
    /**
     * Create a rolling reserve return payout for this reconciliation.
     */
    public function createRollingReserveReturnPayout($initiatedBy = null)
    {
        if ($this->return_reserve <= 0) {
            return null;
        }
        
        // Calculate remittance fee (example: 1% of amount)
        $remittanceFee = $this->return_reserve * 0.01;
        
        return MerchantPayout::create([
            'merchant_id' => $this->merchant_id,
            'amount' => $this->return_reserve,
            'type' => 'ROLLING_RESERVE_RETURN',
            'status' => 'PENDING',
            'remittance_fee' => $remittanceFee,
            'initiated_at' => now(),
            'initiated_by' => $initiatedBy,
            'notes' => "Rolling reserve return for {$this->date->format('Y-m-d')}",
        ]);
    }
    
    /**
     * Get existing rolling reserve return payout for this reconciliation.
     */
    public function getRollingReserveReturnPayout()
    {
        return MerchantPayout::where('merchant_id', $this->merchant_id)
            ->where('type', 'ROLLING_RESERVE_RETURN')
            ->whereDate('created_at', $this->date)
            ->first();
    }
}
