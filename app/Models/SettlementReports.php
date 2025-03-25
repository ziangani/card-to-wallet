<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SettlementReports extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'source',
        'settlement_date',
        'merchant',
        'currency',
        'volume',
        'value',
        'bank_charge',
        'our_charge',
        'net_settlement',
        'raw_data',
        'status',
        'reconciliation_status',
        'file_path',
        'reconciled_at',
        'reconciliation_comment'
    ];

    protected $casts = [
        'settlement_date' => 'date',
        'raw_data' => 'array',
        'reconciled_at' => 'datetime'
    ];

    public function merchants()
    {
        return $this->belongsTo(Merchants::class, 'merchant', 'code');
    }
}
