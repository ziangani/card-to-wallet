<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MerchantApis extends Model
{
    use HasFactory;

    const KEY_TYPE_DEFAULT = 'DEFAULT';


    public function merchant()
    {
        return $this->belongsTo(Merchants::class, 'merchant_id');
    }
}
