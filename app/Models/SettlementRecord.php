<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SettlementRecord extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'transaction_date' => 'datetime',
        'processing_date' => 'datetime',
        'settlement_date' => 'datetime',
        'processed_at' => 'datetime',
        'original_amount' => 'decimal:2',
        'settlement_amount' => 'decimal:2',
        'commission_amount' => 'decimal:2',
        'net_amount' => 'decimal:2',
        'card_present' => 'boolean',
        'raw_data' => 'json',
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [
        'transaction_date',
        'processing_date',
        'settlement_date',
        'processed_at',
        'created_at',
        'updated_at',
    ];

    /**
     * Scope a query to only include records for a specific provider.
     */
    public function scopeProvider($query, string $provider)
    {
        return $query->where('provider', strtoupper($provider));
    }

    /**
     * Scope a query to only include records with a specific status.
     */
    public function scopeStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope a query to only include records within a date range.
     */
    public function scopeBetweenDates($query, string $startDate, string $endDate)
    {
        return $query->whereBetween('transaction_date', [$startDate, $endDate]);
    }

    /**
     * Get the formatted original amount with currency.
     */
    public function getFormattedOriginalAmountAttribute(): string
    {
        return number_format($this->original_amount, 2) . ' ' . $this->original_currency;
    }

    /**
     * Get the formatted settlement amount with currency.
     */
    public function getFormattedSettlementAmountAttribute(): string
    {
        return $this->settlement_amount 
            ? number_format($this->settlement_amount, 2) . ' ' . $this->settlement_currency
            : 'N/A';
    }

    /**
     * Get the formatted commission amount with currency.
     */
    public function getFormattedCommissionAmountAttribute(): string
    {
        return $this->commission_amount
            ? number_format($this->commission_amount, 2) . ' ' . $this->settlement_currency
            : '0.00 ' . $this->settlement_currency;
    }

    /**
     * Get the formatted net amount with currency.
     */
    public function getFormattedNetAmountAttribute(): string
    {
        return $this->net_amount
            ? number_format($this->net_amount, 2) . ' ' . $this->settlement_currency
            : 'N/A';
    }
}
