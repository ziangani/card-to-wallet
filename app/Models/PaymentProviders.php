<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentProviders extends Model
{
    use HasFactory;


    protected $fillable = [
        'name',
        'code',
        'api_key_id',
        'api_key_secret',
        'api_url',
        'api_token',
        'callback_url',
        'environment',
        'details',
    ];
}
