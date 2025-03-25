<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class CompanyContact extends Model
{
    use HasFactory, LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->logExcept(['updated_at'])
            ->setDescriptionForEvent(fn(string $eventName) => "Company contact details have been {$eventName}")
            ->useLogName('company_contacts');
    }

    protected $fillable = [
        'company_id',
        'primary_full_name',
        'primary_country',
        'primary_phone_number',
        'primary_email',
        'primary_address',
        'primary_town',
        'primary_designation',
        'secondary_full_name',
        'secondary_country',
        'secondary_phone_number',
        'secondary_email',
        'secondary_address',
        'secondary_town',
        'secondary_designation',
    ];
}
