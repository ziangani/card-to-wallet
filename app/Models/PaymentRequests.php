<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentRequests extends Model
{
    use HasFactory;

    const STATUS_PENDING = 'PENDING';
    const STATUS_SUCCESS = 'SUCCESS';
    const STATUS_FAILED = 'FAILED';
    const STATUS_INITIATED = 'INITIATED';



    public function merchant()
    {
        return $this->belongsTo(Merchants::class, 'merchant_id');
    }

    public function merchantApi()
    {
        return $this->belongsTo(MerchantApis::class, 'merchant_api_id');
    }

    public function payment()
    {
        return $this->hasOne(Transactions::class, 'merchant_reference', 'token');
    }
}
