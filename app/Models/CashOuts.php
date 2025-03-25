<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CashOuts extends Model
{
    use HasFactory;

    const STATUS_PENDING = 'PENDING';
    protected $fillable = [
        'batch_id',
        'reference',
        'amount',
        'fee',
        'techpay_charge',
        'third_party_charge',
        'batch_status',
        'transaction_status',
        'merchant_id',
        'bank_id',
        'account_type',
        'account_number',
        'bank_name',
        'branch_code',
        'swift_code',
        'date',
        'comment',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'fee' => 'decimal:2',
        'techpay_charge' => 'decimal:2',
        'third_party_charge' => 'decimal:2',
        'date' => 'date',
    ];

    public function merchant()
    {
        return $this->belongsTo(Merchants::class, 'merchant_id');
    }

    public function cashoutAccount()
    {
        return $this->belongsTo(MerchantCashOutAccounts::class, 'merchant_id', 'merchant_id');
    }

    public function transactions()
    {
        return $this->hasMany(Transactions::class, 'cashout_batch_id', 'batch_id');
    }

    /**
     * Calculate the total amount of transactions in this batch.
     *
     * @return float
     */
    public function calculateTransactionsTotal()
    {
        return $this->transactions()->sum('amount');
    }

    /**
     * Get the company bank account associated with this cashout.
     */
    public function companyBank()
    {
        return $this->belongsTo(CompanyBank::class, 'bank_id');
    }
}
