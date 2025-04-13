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
     * Calculate the total fee for a given amount.
     *
     * @param float $amount
     * @return float Returns the total fee amount
     */
    public static function calculateFee($amount)
    {
        $totalFee = 0;
        
        // Get all charges for CARD_TO_WALLET channel
        $charges = Charges::where('channel', 'CARD_TO_WALLET')
            ->where('is_active', true)
            ->get();
            
        foreach ($charges as $charge) {
            // Only apply FIXED charges, as percentage charges are collected at deposit time
            if ($charge->charge_type == 'FIXED') {
                $totalFee += $charge->charge_value;
            }
        }
        
        return $totalFee;
    }
    
    /**
     * Get the variable fee percentage for card to wallet transactions.
     *
     * @return float
     */
    public static function getVariableFeePercentage()
    {
        $percentageTotal = 0;
        
        // Get all percentage charges for CARD_TO_WALLET channel
        $charges = Charges::where('channel', 'CARD_TO_WALLET')
            ->where('charge_type', 'PERCENTAGE')
            ->where('is_active', true)
            ->get();
            
        foreach ($charges as $charge) {
            $percentageTotal += $charge->charge_value;
        }
        
        return $percentageTotal;
    }

    /**
     * Get the fixed fee amount for card to wallet transactions.
     *
     * @return float
     */
    public static function getFixedFeeAmount()
    {
        $fixedTotal = 0;
        
        // Get all fixed charges for CARD_TO_WALLET channel
        $charges = Charges::where('channel', 'CARD_TO_WALLET')
            ->where('charge_type', 'FIXED')
            ->where('is_active', true)
            ->get();
            
        foreach ($charges as $charge) {
            $fixedTotal += $charge->charge_value;
        }
        
        return $fixedTotal;
    }
    
    /**
     * Get the variable fee amount for this transaction.
     *
     * @return float
     */
    public function getTransactionVariableFee()
    {
        $variableFee = 0;
        
        foreach ($this->charges as $charge) {
            if ($charge->charge_type === 'PERCENTAGE') {
                $variableFee += $charge->calculated_amount;
            }
        }
        
        return $variableFee;
    }
    
    /**
     * Get the fixed fee amount for this transaction.
     *
     * @return float
     */
    public function getTransactionFixedFee()
    {
        $fixedFee = 0;
        
        foreach ($this->charges as $charge) {
            if ($charge->charge_type === 'FIXED') {
                $fixedFee += $charge->calculated_amount;
            }
        }
        
        return $fixedFee;
    }

    /**
     * Create transaction charge records for a transaction.
     *
     * @param Transaction $transaction
     * @return void
     */
    public static function createTransactionCharges(Transaction $transaction)
    {
        // Get all charges for CARD_TO_WALLET channel
        $charges = Charges::where('channel', 'CARD_TO_WALLET')
            ->where('is_active', true)
            ->get();

        foreach ($charges as $charge) {
            $calculatedAmount = 0;
            
            // Calculate amount based on charge type
            if ($charge->charge_type == 'PERCENTAGE') {
                $calculatedAmount = $transaction->amount * ($charge->charge_value / 100);
            } else { // FIXED
                $calculatedAmount = $charge->charge_value;
            }
            
            // Create transaction charge record
            TransactionCharge::create([
                'transaction_id' => $transaction->id,
                'charge_id' => $charge->id,
                'charge_name' => $charge->charge_name,
                'charge_type' => $charge->charge_type,
                'charge_value' => $charge->charge_value,
                'base_amount' => $transaction->amount,
                'calculated_amount' => $calculatedAmount,
                'merchant_id' => $transaction->merchant_code ?? null,
            ]);
        }
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
     * Calculate the fee for corporate transactions (only fixed fee).
     *
     * @param float $amount
     * @return float Returns the total fee amount (only fixed fees)
     */
    public static function calculateCorporateFee($amount)
    {
        $totalFee = 0;
        
        // Get all fixed charges for CARD_TO_WALLET channel
        $charges = Charges::where('channel', 'CARD_TO_WALLET')
            ->where('charge_type', 'FIXED')
            ->where('is_active', true)
            ->get();
            
        foreach ($charges as $charge) {
            $totalFee += $charge->charge_value;
        }
        
        return $totalFee;
    }
    
    /**
     * Get the fee description for corporate transactions (only fixed fee).
     *
     * @return string
     */
    public static function getCorporateFeeDescription()
    {
        return 'K' . self::getFixedFeeAmount() . ' fixed fee';
    }
    
    /**
     * Create transaction charge records for a corporate transaction.
     * Only includes fixed fees, as percentage fees are collected at deposit time.
     *
     * @param Transaction $transaction
     * @return void
     */
    public static function createCorporateTransactionCharges(Transaction $transaction)
    {
        // Get all fixed charges for CARD_TO_WALLET channel
        $charges = Charges::where('channel', 'CARD_TO_WALLET')
            ->where('charge_type', 'FIXED')
            ->where('is_active', true)
            ->get();

        foreach ($charges as $charge) {
            // Create transaction charge record (only for fixed fees)
            TransactionCharge::create([
                'transaction_id' => $transaction->id,
                'charge_id' => $charge->id,
                'charge_name' => $charge->charge_name,
                'charge_type' => $charge->charge_type,
                'charge_value' => $charge->charge_value,
                'base_amount' => $transaction->amount,
                'calculated_amount' => $charge->charge_value,
                'merchant_id' => $transaction->merchant_code ?? null,
            ]);
        }
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
