<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Activity extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'type',
        'description',
        'created_at',
        'updated_at'
    ];

    /**
     * Get the company that owns the activity.
     */
    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}