<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

use App\Enums\PaymentChannel;
use App\Enums\ChargeType;
use App\Enums\ChargeName;

class Charges extends Model
{
    protected $casts = [
        'channel' => PaymentChannel::class,
        'charge_type' => ChargeType::class,
        'charge_name' => ChargeName::class,
        'charge_value' => 'decimal:2',
        'max_amount' => 'decimal:2',
        'min_amount' => 'decimal:2',
        'is_default' => 'boolean',
        'is_active' => 'boolean',
    ];

    protected $guarded = ['id'];


    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }


    public function calculateCharge($amount): float
    {
        if (!$this->charge_type instanceof ChargeType) {
            throw new \Exception("Invalid charge type");
        }

        $calculated = match($this->charge_type) {
            ChargeType::FIXED => $this->charge_value,
            ChargeType::PERCENTAGE => $amount * ($this->charge_value / 100),
        };

        if ($this->max_amount) {
            $calculated = min($calculated, $this->max_amount);
        }
        if ($this->min_amount) {
            $calculated = max($calculated, $this->min_amount);
        }

        return round($calculated, 2);
    }

    public static function getApplicableCharges($channel, $merchantCode, $companyId)
    {
        return static::where('channel', $channel)
            ->where('is_active', true)
            ->where(function($query) use ($merchantCode, $companyId) {
                $query->where('merchant_id', $merchantCode)
                     ->orWhere('company_id', $companyId)
                     ->orWhere(function($q) {
                         $q->whereNull('merchant_id')
                           ->whereNull('company_id')
                           ->where('is_default', true);
                     });
            })
            ->orderByRaw("CASE
                WHEN merchant_id = ? THEN 1
                WHEN company_id = ? THEN 2
                ELSE 3
            END", [$merchantCode, $companyId])
            ->get();
    }
}
