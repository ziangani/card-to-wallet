<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Emails extends Model
{
    use HasFactory;

    protected $fillable = [
        'subject',
        'from',
        'email',
        'message',
        'view',
        'response',
        'sent_at',
        'attempts',
        'status',
        'data'
    ];

    protected $casts = [
        'data' => 'array',
        'sent_at' => 'datetime'
    ];
}
