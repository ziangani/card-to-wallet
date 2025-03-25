<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Terminals extends Model
{
    use HasFactory;

    protected $fillable = [
        'serial_number',
        'type',
        'model',
        'manufacturer',
        'merchant_id',
        'status',
        'terminal_id',
    ];

    const TYPE_POS = 'POS';
    const TYPE_KIOSK = 'KIOSK';
    const TYPES = [
        self::TYPE_POS => 'Point of Sale',
        self::TYPE_KIOSK => 'Kiosk',
    ];

    const STATUS_UPLOADED = 'UPLOADED';
    const STATUS_ACTIVATED = 'ACTIVATED';

    public static function generateTerminalId()
    {
        while (true) {
            $terminalId = mt_rand(100000, 999999);

            if (!static::where('terminal_id', $terminalId)->exists()) {
                return $terminalId;
            }
        }
    }

    public function merchant(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Merchants::class);
    }
}
