<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CorporateWalletTransaction extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'uuid',
        'corporate_wallet_id',
        'transaction_type',
        'amount',
        'balance_after',
        'currency',
        'description',
        'reference_number',
        'performed_by',
        'status',
        'related_entity_type',
        'related_entity_id',
        'notes'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'amount' => 'decimal:2',
        'balance_after' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'notes' => 'array',
    ];

    /**
     * Get the wallet that owns the transaction.
     */
    public function corporateWallet()
    {
        return $this->belongsTo(CorporateWallet::class);
    }

    /**
     * Get the user who performed the transaction.
     */
    public function performer()
    {
        return $this->belongsTo(User::class, 'performed_by');
    }

    /**
     * Get the related entity.
     */
    public function relatedEntity()
    {
        return $this->morphTo();
    }

    /**
     * Check if the transaction is a deposit.
     *
     * @return bool
     */
    public function isDeposit()
    {
        return $this->transaction_type === 'deposit';
    }

    /**
     * Check if the transaction is a withdrawal.
     *
     * @return bool
     */
    public function isWithdrawal()
    {
        return $this->transaction_type === 'withdrawal';
    }

    /**
     * Check if the transaction is a transfer.
     *
     * @return bool
     */
    public function isTransfer()
    {
        return $this->transaction_type === 'transfer';
    }

    /**
     * Check if the transaction is a fee.
     *
     * @return bool
     */
    public function isFee()
    {
        return $this->transaction_type === 'fee';
    }

    /**
     * Check if the transaction is an adjustment.
     *
     * @return bool
     */
    public function isAdjustment()
    {
        return $this->transaction_type === 'adjustment';
    }

    /**
     * Check if the transaction is completed.
     *
     * @return bool
     */
    public function isCompleted()
    {
        return $this->status === 'completed';
    }

    /**
     * Check if the transaction is pending.
     *
     * @return bool
     */
    public function isPending()
    {
        return $this->status === 'pending';
    }

    /**
     * Check if the transaction is failed.
     *
     * @return bool
     */
    public function isFailed()
    {
        return $this->status === 'failed';
    }

    /**
     * Check if the transaction is reversed.
     *
     * @return bool
     */
    public function isReversed()
    {
        return $this->status === 'reversed';
    }

    /**
     * Get the transaction type label.
     *
     * @return string
     */
    public function getTransactionTypeLabel()
    {
        $labels = [
            'deposit' => 'Deposit',
            'withdrawal' => 'Withdrawal',
            'transfer' => 'Transfer',
            'fee' => 'Fee',
            'adjustment' => 'Adjustment',
        ];

        return $labels[$this->transaction_type] ?? ucfirst($this->transaction_type);
    }

    /**
     * Get the status label.
     *
     * @return string
     */
    public function getStatusLabel()
    {
        $labels = [
            'pending' => 'Pending',
            'completed' => 'Completed',
            'failed' => 'Failed',
            'reversed' => 'Reversed',
        ];

        return $labels[$this->status] ?? ucfirst($this->status);
    }

    /**
     * Get the formatted amount.
     *
     * @return string
     */
    public function getFormattedAmount()
    {
        $prefix = '';

        if ($this->transaction_type === 'withdrawal' || $this->transaction_type === 'fee') {
            $prefix = '-';
        } elseif ($this->transaction_type === 'deposit') {
            $prefix = '+';
        } elseif ($this->transaction_type === 'adjustment') {
            $prefix = $this->amount >= 0 ? '+' : '';
        }

        return $prefix . number_format(abs($this->amount), 2) . ' ' . $this->currency;
    }

    /**
     * Get the formatted balance after.
     *
     * @return string
     */
    public function getFormattedBalanceAfter()
    {
        return number_format($this->balance_after, 2) . ' ' . $this->currency;
    }

    /**
     * Scope a query to only include deposit transactions.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeDeposits($query)
    {
        return $query->where('transaction_type', 'deposit');
    }

    /**
     * Scope a query to only include withdrawal transactions.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWithdrawals($query)
    {
        return $query->where('transaction_type', 'withdrawal');
    }

    /**
     * Scope a query to only include transfer transactions.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeTransfers($query)
    {
        return $query->where('transaction_type', 'transfer');
    }

    /**
     * Scope a query to only include fee transactions.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeFees($query)
    {
        return $query->where('transaction_type', 'fee');
    }

    /**
     * Scope a query to only include adjustment transactions.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeAdjustments($query)
    {
        return $query->where('transaction_type', 'adjustment');
    }

    /**
     * Scope a query to only include completed transactions.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    /**
     * Scope a query to only include pending transactions.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope a query to only include failed transactions.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }

    /**
     * Scope a query to only include reversed transactions.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeReversed($query)
    {
        return $query->where('status', 'reversed');
    }

    /**
     * Scope a query to only include transactions performed by a specific user.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  int  $userId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopePerformedBy($query, $userId)
    {
        return $query->where('performed_by', $userId);
    }

    /**
     * Scope a query to only include transactions with a specific reference number.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  string  $reference
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWithReference($query, $reference)
    {
        return $query->where('reference_number', $reference);
    }

    /**
     * Scope a query to only include transactions with a specific currency.
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
     * Scope a query to only include transactions with an amount greater than or equal to a given amount.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  float  $amount
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWithMinimumAmount($query, $amount)
    {
        return $query->where('amount', '>=', $amount);
    }

    /**
     * Scope a query to only include transactions with an amount less than or equal to a given amount.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  float  $amount
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWithMaximumAmount($query, $amount)
    {
        return $query->where('amount', '<=', $amount);
    }

    /**
     * Scope a query to only include transactions created within a specific date range.
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
}
