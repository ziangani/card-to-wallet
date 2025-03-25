<?php

namespace App\Models;

enum Status: string
{
    case PENDING_APPROVAL = 'PENDING_APPROVAL';
    case APPROVED = 'APPROVED';
    case REJECTED = 'REJECTED';
}

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class CompanyDetail extends Model
{
    use HasFactory, SoftDeletes, LogsActivity;

    public function contact()
    {
        return $this->hasOne(CompanyContact::class, 'company_id');
    }

    public function ownership()
    {
        return $this->hasOne(CompanyOwnership::class, 'company_id');
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->logExcept(['updated_at'])
            ->setDescriptionForEvent(fn(string $eventName) => "Company details have been {$eventName}")
            ->useLogName('company_details');
    }

    protected $fillable = [
        'company_name',
        'trading_name',
        'type_of_ownership',
        'rc_number',
        'tpin',
        'date_registered',
        'nature_of_business',
        'office_address',
        'postal_address',
        'country_of_incorporation',
        'office_telephone',
        'customer_service_telephone',
        'official_email',
        'customer_service_email',
        'official_website',
        'status',
    ];

    protected $casts = [
        'date_registered' => 'date',
        'status' => Status::class,
    ];
}
