<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'transactions';

    /**
     * Get the variable fee percentage for card to wallet transactions.
     *
     * @return float
     */
    private static function getVariableFeePercentage()
    {
        return 4.0; // 4% variable fee
    }

    /**
     * Get the fixed fee amount for card to wallet transactions.
     *
     * @return float
     */
    private static function getFixedFeeAmount()
    {
        return 7.5; // K7.5 fixed fee
    }

    /**
     * Calculate the total fee for a given amount.
     *
     * @param float $amount
     * @return float Returns the total fee amount
     */
    public static function calculateFee($amount)
    {
        $variableFee = $amount * (self::getVariableFeePercentage() / 100);
        $fixedFee = self::getFixedFeeAmount();
        
        return $variableFee + $fixedFee;
    }

    /**
     * Get the fee description for display purposes.
     *
     * @return string
     */
    public static function getFeeDescription()
    {
        return 'K' . self::getFixedFeeAmount() . ' + ' . self::getVariableFeePercentage() . '%';
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'uuid',
        'transaction_type',
        'wallet_provider_id',
        'wallet_number',
        'recipient_name',
        'amount',
        'fee_amount',
        'total_amount',
        'currency',
        'status',
        'mpgs_order_id',
        'mpgs_result_code',
        'provider_reference',
        'failure_reason',
        'ip_address',
        'user_agent',
        'merchant_reference',
        'merchant_code',
        'merchant_settlement_status',
        'merchant_settlement_date',
        'payment_providers_id',
        'provider_name',
        'provider_push_status',
        'provider_external_reference',
        'provider_status_description',
        'provider_payment_reference',
        'provider_payment_confirmation_date',
        'provider_payment_date',
        'payment_channel',
        'callback',
        'reference_1',
        'reference_2',
        'reference_3',
        'reference_4',
        'retries',
        'last_retry_date',
        'reversal_status',
        'reversal_reason',
        'reversal_reference',
        'reversal_date',
    ];

    public function __get($key)
    {
        if ($key === 'recipient_name') {
            $this->reference_4;
        }
        if ($key === 'wallet_number') {
            $this->reference_1;
        }

        return parent::__get($key);
    }

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the user that owns the transaction.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the wallet provider for the transaction.
     */
    public function walletProvider()
    {
        return $this->belongsTo(WalletProvider::class);
    }

    /**
     * Get the statuses for the transaction.
     */
    public function statuses()
    {
        return $this->hasMany(TransactionStatus::class);
    }

    /**
     * Get the charges for the transaction.
     */
    public function charges()
    {
        return $this->hasMany(TransactionCharge::class);
    }
}
