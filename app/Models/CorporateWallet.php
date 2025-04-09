<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CorporateWallet extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'company_id',
        'balance',
        'currency',
        'status',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'balance' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the company that owns the wallet.
     */
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Get the transactions for the wallet.
     */
    public function transactions()
    {
        return $this->hasMany(CorporateWalletTransaction::class);
    }

    /**
     * Get the bulk disbursements for the wallet.
     */
    public function bulkDisbursements()
    {
        return $this->hasMany(BulkDisbursement::class);
    }

    /**
     * Check if the wallet is active.
     *
     * @return bool
     */
    public function isActive()
    {
        return $this->status === 'active';
    }

    /**
     * Check if the wallet is suspended.
     *
     * @return bool
     */
    public function isSuspended()
    {
        return $this->status === 'suspended';
    }

    /**
     * Check if the wallet is inactive.
     *
     * @return bool
     */
    public function isInactive()
    {
        return $this->status === 'inactive';
    }

    /**
     * Check if the wallet has sufficient balance for a given amount.
     *
     * @param  float  $amount
     * @return bool
     */
    public function hasSufficientBalance($amount)
    {
        return $this->balance >= $amount;
    }

    /**
     * Deposit funds into the wallet.
     *
     * @param  float  $amount
     * @param  string  $description
     * @param  string  $reference
     * @param  int|null  $performedBy
     * @return \App\Models\CorporateWalletTransaction
     */
    public function deposit($amount, $description, $reference = null, $performedBy = null)
    {
        // Update the balance
        $this->balance += $amount;
        $this->save();

        // Create a transaction record
        return CorporateWalletTransaction::create([
            'corporate_wallet_id' => $this->id,
            'transaction_type' => 'deposit',
            'amount' => $amount,
            'balance_after' => $this->balance,
            'currency' => $this->currency,
            'description' => $description,
            'reference_number' => $reference,
            'performed_by' => $performedBy,
            'status' => 'completed',
        ]);
    }

    /**
     * Withdraw funds from the wallet.
     *
     * @param  float  $amount
     * @param  string  $description
     * @param  string  $reference
     * @param  int|null  $performedBy
     * @return \App\Models\CorporateWalletTransaction|false
     */
    public function withdraw($amount, $description, $reference = null, $performedBy = null)
    {
        // Check if there's sufficient balance
        if (!$this->hasSufficientBalance($amount)) {
            return false;
        }

        // Update the balance
        $this->balance -= $amount;
        $this->save();

        // Create a transaction record
        return CorporateWalletTransaction::create([
            'corporate_wallet_id' => $this->id,
            'transaction_type' => 'withdrawal',
            'amount' => $amount,
            'balance_after' => $this->balance,
            'currency' => $this->currency,
            'description' => $description,
            'reference_number' => $reference,
            'performed_by' => $performedBy,
            'status' => 'completed',
        ]);
    }

    /**
     * Add a fee to the wallet.
     *
     * @param  float  $amount
     * @param  string  $description
     * @param  string  $reference
     * @param  int|null  $performedBy
     * @return \App\Models\CorporateWalletTransaction
     */
    public function addFee($amount, $description, $reference = null, $performedBy = null)
    {
        // Update the balance
        $this->balance -= $amount;
        $this->save();

        // Create a transaction record
        return CorporateWalletTransaction::create([
            'corporate_wallet_id' => $this->id,
            'transaction_type' => 'fee',
            'amount' => $amount,
            'balance_after' => $this->balance,
            'currency' => $this->currency,
            'description' => $description,
            'reference_number' => $reference,
            'performed_by' => $performedBy,
            'status' => 'completed',
        ]);
    }

    /**
     * Make an adjustment to the wallet.
     *
     * @param  float  $amount
     * @param  string  $description
     * @param  string  $reference
     * @param  int|null  $performedBy
     * @return \App\Models\CorporateWalletTransaction
     */
    public function adjust($amount, $description, $reference = null, $performedBy = null)
    {
        // Update the balance
        $this->balance += $amount;
        $this->save();

        // Create a transaction record
        return CorporateWalletTransaction::create([
            'corporate_wallet_id' => $this->id,
            'transaction_type' => 'adjustment',
            'amount' => $amount,
            'balance_after' => $this->balance,
            'currency' => $this->currency,
            'description' => $description,
            'reference_number' => $reference,
            'performed_by' => $performedBy,
            'status' => 'completed',
        ]);
    }

    /**
     * Get the formatted balance.
     *
     * @return string
     */
    public function getFormattedBalance()
    {
        return number_format($this->balance, 2) . ' ' . $this->currency;
    }

    /**
     * Scope a query to only include active wallets.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope a query to only include suspended wallets.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSuspended($query)
    {
        return $query->where('status', 'suspended');
    }

    /**
     * Scope a query to only include inactive wallets.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeInactive($query)
    {
        return $query->where('status', 'inactive');
    }

    /**
     * Scope a query to only include wallets with a specific currency.
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
     * Scope a query to only include wallets with a balance greater than or equal to a given amount.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  float  $amount
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWithMinimumBalance($query, $amount)
    {
        return $query->where('balance', '>=', $amount);
    }

    /**
     * Scope a query to only include wallets with a balance less than or equal to a given amount.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  float  $amount
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWithMaximumBalance($query, $amount)
    {
        return $query->where('balance', '<=', $amount);
    }
}
