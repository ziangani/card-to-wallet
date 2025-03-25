<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TransactionCharges extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'charge_value' => 'decimal:2',
        'base_amount' => 'decimal:2',
        'calculated_amount' => 'decimal:2',
    ];

    public function transaction(): BelongsTo
    {
        return $this->belongsTo(Transactions::class, 'transaction_id');
    }

    public function settlement(): BelongsTo
    {
        return $this->belongsTo(SettlementRecord::class, 'settlement_id');
    }

    public function charge(): BelongsTo
    {
        return $this->belongsTo(Charges::class, 'charge_id');
    }

    public function merchant(): BelongsTo
    {
        return $this->belongsTo(Merchants::class, 'merchant_id', 'code');
    }
}
