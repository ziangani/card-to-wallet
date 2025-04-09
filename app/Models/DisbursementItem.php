<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DisbursementItem extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'bulk_disbursement_id',
        'transaction_id',
        'wallet_provider_id',
        'wallet_number',
        'recipient_name',
        'amount',
        'fee',
        'currency',
        'status',
        'error_message',
        'reference',
        'row_number',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'amount' => 'decimal:2',
        'fee' => 'decimal:2',
        'row_number' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the bulk disbursement that owns the item.
     */
    public function bulkDisbursement()
    {
        return $this->belongsTo(BulkDisbursement::class, 'bulk_disbursement_id');
    }

    /**
     * Get the transaction associated with the item.
     */
    public function transaction()
    {
        return $this->belongsTo(Transaction::class);
    }

    /**
     * Get the wallet provider associated with the item.
     */
    public function walletProvider()
    {
        return $this->belongsTo(WalletProvider::class);
    }

    /**
     * Check if the item is pending.
     *
     * @return bool
     */
    public function isPending()
    {
        return $this->status === 'pending';
    }

    /**
     * Check if the item is processing.
     *
     * @return bool
     */
    public function isProcessing()
    {
        return $this->status === 'processing';
    }

    /**
     * Check if the item is completed.
     *
     * @return bool
     */
    public function isCompleted()
    {
        return $this->status === 'completed';
    }

    /**
     * Check if the item is failed.
     *
     * @return bool
     */
    public function isFailed()
    {
        return $this->status === 'failed';
    }

    /**
     * Get the total amount with fee.
     *
     * @return float
     */
    public function getTotalWithFee()
    {
        return $this->amount + $this->fee;
    }

    /**
     * Format the wallet number for display.
     *
     * @return string
     */
    public function getFormattedWalletNumber()
    {
        // Clean the number
        $number = preg_replace('/[^0-9]/', '', $this->wallet_number);
        
        // Format based on length
        if (strlen($number) === 12 && substr($number, 0, 3) === '260') {
            // Format as +260 97 123 4567
            return '+' . substr($number, 0, 3) . ' ' . substr($number, 3, 2) . ' ' . substr($number, 5, 3) . ' ' . substr($number, 8);
        }
        
        // Return as is if we can't format it
        return $this->wallet_number;
    }

    /**
     * Scope a query to only include pending items.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope a query to only include processing items.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeProcessing($query)
    {
        return $query->where('status', 'processing');
    }

    /**
     * Scope a query to only include completed items.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    /**
     * Scope a query to only include failed items.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }

    /**
     * Scope a query to only include items for a specific wallet provider.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  int  $providerId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeForProvider($query, $providerId)
    {
        return $query->where('wallet_provider_id', $providerId);
    }

    /**
     * Scope a query to only include items with a specific currency.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  string  $currency
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWithCurrency($query, $currency)
    {
        return $query->where('currency', $currency);
    }
}
