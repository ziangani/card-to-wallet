<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class CompanyBank extends Model
{
    use HasFactory, LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->logExcept(['updated_at'])
            ->setDescriptionForEvent(fn(string $eventName) => "Company bank details have been {$eventName}")
            ->useLogName('company_bank');
    }

    protected $fillable = [
        'company_id',
        'bank_name',
        'bank_branch',
        'bank_sort_code',
        'account_type',
        'account_number',
        'account_name',
    ];
}
