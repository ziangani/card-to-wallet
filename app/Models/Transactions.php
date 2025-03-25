<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Transactions extends Model
{
    use HasFactory;

    protected $fillable = [
        'uuid',
        'merchant_reference',
        'status',
        'currency',
        'amount',
        'merchant_id',
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
        'cashout_batch_id',
        'cashout_status',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'provider_payment_confirmation_date' => 'datetime',
        'provider_payment_date' => 'date',
        'merchant_settlement_date' => 'date',
        'last_retry_date' => 'datetime',
        'reversal_date' => 'datetime',
    ];

    public function merchants() : BelongsTo
    {
        return $this->belongsTo(Merchants::class, 'merchant_id', 'id');
    }

    public function logs(): HasMany
    {
        return $this->hasMany(ApiLogs::class, 'reference', 'merchant_reference');
    }

    public function provider() : HasOne
    {
        return $this->hasOne(PaymentProviders::class, 'id', 'payment_providers_id');
    }

    public function request()
    {
        return $this->belongsTo(PaymentRequests::class, 'merchant_reference', 'token');
    }

    public function cashout()
    {
        return $this->belongsTo(CashOuts::class, 'cashout_batch_id', 'batch_id');
    }

    /**
     * Scope a query to only include transactions available for cashout.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeAvailableForCashout($query)
    {
        return $query->where('status', 'COMPLETE')
                    ->where('cashout_status', 'PENDING');
    }

    /**
     * Scope a query to only include transactions that have been initiated for cashout.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeInitiatedForCashout($query)
    {
        return $query->where('status', 'COMPLETE')
                    ->where('cashout_status', 'INITIATED')
                    ->whereNotNull('cashout_batch_id');
    }

    /**
     * Get the available balance for a merchant.
     *
     * @param  int|null  $merchantId
     * @return float
     */
    public static function getAvailableBalance($merchantId = null)
    {
        $query = self::availableForCashout();
        
        if ($merchantId) {
            $query->where('merchant_id', $merchantId);
        }
        
        return $query->sum('amount');
    }
    
    /**
     * Get the actual balance (after charges) for a merchant.
     *
     * @param  int|null  $merchantId
     * @return float
     */
    public static function getActualBalance($merchantId = null)
    {
        $availableBalance = self::getAvailableBalance($merchantId);
        
        if ($merchantId) {
            $merchant = \App\Models\Merchants::find($merchantId);
            if ($merchant) {
                $merchantCode = $merchant->code;
                
                // Get applicable charges
                $charges = \App\Models\Charges::getApplicableCharges('CASHOUT', $merchantCode, null);
                
                $totalCharges = 0;
                foreach ($charges as $charge) {
                    $totalCharges += $charge->calculateCharge($availableBalance);
                }
                
                return $availableBalance - $totalCharges;
            }
        }
        
        return $availableBalance;
    }

    /**
     * Get the count of available transactions for a merchant.
     *
     * @param  int|null  $merchantId
     * @return int
     */
    public static function getAvailableTransactionsCount($merchantId = null)
    {
        $query = self::availableForCashout();
        
        if ($merchantId) {
            $query->where('merchant_id', $merchantId);
        }
        
        return $query->count();
    }

    /**
     * Get the initiated cashout amount for a merchant.
     *
     * @param  int|null  $merchantId
     * @return float
     */
    public static function getInitiatedCashoutAmount($merchantId = null)
    {
        $query = self::initiatedForCashout();
        
        if ($merchantId) {
            $query->where('merchant_id', $merchantId);
        }
        
        return $query->sum('amount');
    }

    /**
     * Get the count of initiated cashout transactions for a merchant.
     *
     * @param  int|null  $merchantId
     * @return int
     */
    public static function getInitiatedTransactionsCount($merchantId = null)
    {
        $query = self::initiatedForCashout();
        
        if ($merchantId) {
            $query->where('merchant_id', $merchantId);
        }
        
        return $query->count();
    }
}
