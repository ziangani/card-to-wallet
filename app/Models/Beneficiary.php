<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Beneficiary extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'beneficiaries';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'wallet_provider_id',
        'wallet_number',
        'recipient_name',
        'is_favorite',
        'notes',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'is_favorite' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the user that owns the beneficiary.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the wallet provider for the beneficiary.
     */
    public function walletProvider()
    {
        return $this->belongsTo(WalletProvider::class);
    }
}
