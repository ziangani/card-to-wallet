<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class CompanyOwnership extends Model
{
    use HasFactory, LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->logExcept(['updated_at'])
            ->setDescriptionForEvent(fn(string $eventName) => "Company ownership details have been {$eventName}")
            ->useLogName('company_ownership');
    }

    protected $fillable = [
        'company_id',
        'salutation',
        'full_names',
        'nationality',
        'date_of_birth',
        'place_of_birth',
        'id_type',
        'identification_number',
        'country_of_residence',
        'residential_address',
        'designation',
        'mobile',
        'email',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
    ];
}
