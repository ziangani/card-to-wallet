<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class SettlementSummary extends Model
{
    protected $table = 'settlement_summaries';

    public $timestamps = false;

    protected $casts = [
        'settlement_date' => 'date',
        'debit_value' => 'decimal:2',
        'credit_value' => 'decimal:2',
        'net_settlement' => 'decimal:2'
    ];

    public function getKey()
    {
        return base64_encode($this->merchant . '_' . $this->settlement_date->format('Y-m-d'));
    }

    public static function find($id)
    {
        if (!$id) return null;
        
        try {
            [$merchant, $date] = explode('_', base64_decode($id));
            return static::query()
                ->where('merchant', $merchant)
                ->where('settlement_date', $date)
                ->first();
        } catch (\Exception $e) {
            return null;
        }
    }

    public static function getMerchantNames()
    {
        return DB::table('merchants')
            ->pluck('name', 'code')
            ->toArray();
    }
}
