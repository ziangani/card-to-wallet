<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransactionCharge extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'transaction_charges';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'transaction_id',
        'charge_id',
        'charge_name',
        'charge_type',
        'charge_value',
        'base_amount',
        'calculated_amount',
        'merchant_id',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'charge_value' => 'decimal:2',
        'base_amount' => 'decimal:2',
        'calculated_amount' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the transaction that owns the charge.
     */
    public function transaction()
    {
        return $this->belongsTo(Transaction::class);
    }

    /**
     * Get the charge that owns the transaction charge.
     */
    public function charge()
    {
        return $this->belongsTo(Charge::class);
    }
}
