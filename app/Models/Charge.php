<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Charge extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'charges';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'channel',
        'charge_name',
        'description',
        'charge_type',
        'charge_value',
        'max_amount',
        'min_amount',
        'is_default',
        'company_id',
        'merchant_id',
        'created_by',
        'updated_by',
        'is_active',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'charge_value' => 'decimal:2',
        'max_amount' => 'decimal:2',
        'min_amount' => 'decimal:2',
        'is_default' => 'boolean',
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the transaction charges for the charge.
     */
    public function transactionCharges()
    {
        return $this->hasMany(TransactionCharge::class);
    }
}
