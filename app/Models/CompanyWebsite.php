<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class CompanyWebsite extends Model
{
    use HasFactory, LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->logExcept(['updated_at'])
            ->setDescriptionForEvent(fn(string $eventName) => "Company website details have been {$eventName}")
            ->useLogName('company_website');
    }

    protected $fillable = [
        'company_id',
        'accept_international_payments',
        'products_services',
        'delivery_days',
        'total_sales_points',
        'secure_platform',
        'security_details',
        'payment_services_request',
        'techpay_services_requested',
        'policies',
    ];

    protected $casts = [
        'accept_international_payments' => 'boolean',
        'delivery_days' => 'integer',
        'total_sales_points' => 'integer',
        'secure_platform' => 'boolean',
        'payment_services_request' => 'json',
        'techpay_services_requested' => 'json',
        'policies' => 'json',
    ];
}
