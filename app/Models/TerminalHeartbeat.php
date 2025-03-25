<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TerminalHeartbeat extends Model
{
    use HasFactory;

    protected $fillable = [
        'terminal_id',
        'location',
        'battery_health',
        'transactions_count',
        'misc'
    ];

    public function terminal()
    {
        return $this->belongsTo(Terminals::class, 'terminal_id');
    }
}
