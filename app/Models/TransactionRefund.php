<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TransactionRefund extends Model
{
    use HasFactory;

    const STATUSES = [
        'PENDING' => 'Pending',
        'PROCESSING' => 'Processing',
        'COMPLETED' => 'Completed',
        'FAILED' => 'Failed'
    ];

    protected $fillable = [
        'transaction_id',
        'amount',
        'currency',
        'status',
        'reason',
        'reference_id',
        'arn',
        'cybersource_id',
        'user_id',
        'response_data',
        'processed_at'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'processed_at' => 'datetime',
        'response_data' => 'array'
    ];

    public function originalTransaction(): BelongsTo
    {
        return $this->belongsTo(AllTransactions::class, 'transaction_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public static function createFromTransaction(AllTransactions $transaction, ?float $amount = null, ?string $reason = null, ?string $arn = null, ?int $user_id = null): self
    {
        return self::create([
            'transaction_id' => $transaction->id,
            'amount' => $amount ?? $transaction->txn_amount,
            'currency' => $transaction->txn_currency,
            'status' => 'PENDING',
            'reason' => $reason,
            'arn' => $arn,
            'user_id' => $user_id,
            'reference_id' => 'Techpay-refund-' . uniqid(),
            'response_data' => null,
            'processed_at' => null
        ]);
    }
    
    /**
     * Check if a transaction has already been successfully refunded
     *
     * @param int $transactionId
     * @return bool
     */
    public static function hasSuccessfulRefund(int $transactionId): bool
    {
        return self::where('transaction_id', $transactionId)
            ->whereIn('status', ['COMPLETED', 'PENDING'])
            ->exists();
    }
}
