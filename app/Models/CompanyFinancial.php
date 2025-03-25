<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompanyFinancial extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'description',
        'volume',
        'value',
    ];

    protected $casts = [
        'volume' => 'integer',
        'value' => 'decimal:2',
    ];
}
