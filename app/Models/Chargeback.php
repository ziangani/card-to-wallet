<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Chargeback extends Model
{
    use HasFactory;

    const REASON_CODES = [
        // Fraud/No Authorization
        'fraud_transaction' => 'Fraudulent Transaction',
        'card_not_present_fraud' => 'Card Not Present Fraud',
        'counterfeit_card' => 'Counterfeit Card Used',

        // Processing Errors
        'duplicate_processing' => 'Duplicate Processing',
        'incorrect_amount' => 'Incorrect Amount',
        'late_presentment' => 'Late Presentment',
        'currency_discrepancy' => 'Currency Discrepancy',

        // Consumer Disputes
        'goods_not_received' => 'Goods/Services Not Received',
        'goods_not_as_described' => 'Goods/Services Not as Described',
        'cancelled_recurring' => 'Cancelled Recurring Transaction',
        'credit_not_processed' => 'Credit Not Processed',

        // Technical Issues
        'invalid_authorization' => 'Invalid Authorization',
        'processing_error' => 'Processing Error',
        'system_malfunction' => 'System Malfunction',

        // Documentation
        'documentation_not_received' => 'Required Documentation Not Received',
        'invalid_documentation' => 'Invalid/Missing Documentation'
    ];

    const STATUSES = [
        'RECEIVED_FROM_BANK' => 'Received from Bank',
        'MERCHANT_NOTIFIED' => 'Merchant Notified',
        'MERCHANT_ACCEPTED' => 'Merchant Accepted',
        'MERCHANT_DISPUTED' => 'Merchant Disputed',
        'DISPUTE_WON' => 'Dispute Won',
        'DISPUTE_LOST' => 'Dispute Lost',
        'REFUND_PROCESSED' => 'Refund Processed',
        'BANK_DEBITED' => 'Bank Debited'
    ];

    protected $fillable = [
        'transaction_id',
        'tran_reason',
        'reason_code',
        'approval_code',
        'orig_time',
        'acquirer_id',
        'term_name',
        'card_acceptor_id',
        'merchant_location',
        'merchant_city',
        'condition_code',
        'orig_clear_amount',
        'orig_clear_currency',
        'original_amount',
        'original_currency',
        'merchant_title',
        'tran_type_desc',
        'tran_code_desc',
        'pan',
        'arn',
        'rrn',
        'orig_clear_date',
        'orig_settle_schedule',
        'status',
        'text_message',
        'raw_data',
        'chargeback_date'
    ];

    protected $casts = [
        'orig_time' => 'datetime',
        'orig_clear_date' => 'datetime',
        'orig_settle_schedule' => 'datetime',
        'orig_clear_amount' => 'decimal:2',
        'original_amount' => 'decimal:2',
        'raw_data' => 'array',
        'chargeback_date' => 'datetime'
    ];

    public function originalTransaction(): BelongsTo
    {
        return $this->belongsTo(AllTransactions::class, 'transaction_id');
    }

    public static function findTransactionByApprovalCode(string $approval_code): ?AllTransactions
    {
        return AllTransactions::where('approval_code', $approval_code)
            ->where('result', 'SUCCESS')
            ->whereIn('txn_type', ['PAYMENT', 'credit card'])
            ->first();
    }

    public static function createFromTransaction(AllTransactions $transaction, ?string $rrn = null, ?string $reason_code = null, ?string $chargeback_date = null): self
    {
        return self::create([
            'transaction_id' => $transaction->id,
            'reason_code' => $reason_code,
            'approval_code' => $transaction->approval_code,
            'orig_time' => $transaction->txn_date,
            'acquirer_id' => $transaction->txn_acquirer_id,
            'term_name' => $transaction->terminal_id,
            'card_acceptor_id' => $transaction->merchant,
            'merchant_location' => null, // Can be added if available in transaction data
            'merchant_city' => null, // Can be added if available in transaction data
            'condition_code' => null, // Can be added if available in transaction data
            'orig_clear_amount' => $transaction->txn_amount,
            'orig_clear_currency' => $transaction->txn_currency,
            'original_amount' => $transaction->amount_details_total_amount ?? $transaction->txn_amount,
            'original_currency' => $transaction->amount_details_currency ?? $transaction->txn_currency,
            'merchant_title' => $transaction->merchants?->name,
            'tran_type_desc' => '1442 - Chargeback', // Standard for chargebacks
            'tran_code_desc' => $transaction->txn_type,
            'pan' => $transaction->card_number,
            'arn' => null, // Will be provided by bank/processor
            'rrn' => $rrn,
            'orig_clear_date' => null, // Will be set when cleared
            'orig_settle_schedule' => null, // Will be set when scheduled for settlement
            'status' => 'RECEIVED_FROM_BANK', // Initial status
            'chargeback_date' => $chargeback_date,
            'raw_data' => $transaction->raw_data
        ]);
    }
}
