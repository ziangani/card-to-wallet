<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentServices extends Model
{
    use HasFactory;
    const STATUS_ENABLED = 'ENABLED';
}
