<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CorporateUserRole extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'company_id',
        'user_id',
        'role_id',
        'is_primary',
        'assigned_by',
        'assigned_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_primary' => 'boolean',
        'assigned_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the company that owns the user role.
     */
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Get the user that owns the user role.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the role that owns the user role.
     */
    public function role()
    {
        return $this->belongsTo(CorporateRole::class, 'role_id');
    }

    /**
     * Get the user who assigned the role.
     */
    public function assigner()
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }

    /**
     * Check if the user role is primary.
     *
     * @return bool
     */
    public function isPrimary()
    {
        return $this->is_primary;
    }

    /**
     * Check if the user role is for an admin.
     *
     * @return bool
     */
    public function isAdmin()
    {
        return $this->role && $this->role->isAdmin();
    }

    /**
     * Check if the user role is for an approver.
     *
     * @return bool
     */
    public function isApprover()
    {
        return $this->role && $this->role->isApprover();
    }

    /**
     * Check if the user role is for an initiator.
     *
     * @return bool
     */
    public function isInitiator()
    {
        return $this->role && $this->role->isInitiator();
    }

    /**
     * Make the user role primary.
     *
     * @return bool
     */
    public function makePrimary()
    {
        // First, remove primary flag from all other roles for this user in this company
        self::where('company_id', $this->company_id)
            ->where('user_id', $this->user_id)
            ->where('id', '!=', $this->id)
            ->update(['is_primary' => false]);
        
        // Then, set this role as primary
        $this->is_primary = true;
        
        return $this->save();
    }

    /**
     * Scope a query to only include primary user roles.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopePrimary($query)
    {
        return $query->where('is_primary', true);
    }

    /**
     * Scope a query to only include user roles for a specific company.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  int  $companyId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeForCompany($query, $companyId)
    {
        return $query->where('company_id', $companyId);
    }

    /**
     * Scope a query to only include user roles for a specific user.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  int  $userId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope a query to only include user roles for a specific role.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  int  $roleId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeForRole($query, $roleId)
    {
        return $query->where('role_id', $roleId);
    }

    /**
     * Scope a query to only include user roles assigned by a specific user.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  int  $userId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeAssignedBy($query, $userId)
    {
        return $query->where('assigned_by', $userId);
    }

    /**
     * Scope a query to only include user roles assigned within a specific date range.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  \Illuminate\Support\Carbon  $startDate
     * @param  \Illuminate\Support\Carbon  $endDate
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeAssignedBetween($query, $startDate, $endDate)
    {
        return $query->whereBetween('assigned_at', [$startDate, $endDate]);
    }

    /**
     * Scope a query to only include admin user roles.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeAdmins($query)
    {
        return $query->whereHas('role', function ($query) {
            $query->where('name', 'admin');
        });
    }

    /**
     * Scope a query to only include approver user roles.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeApprovers($query)
    {
        return $query->whereHas('role', function ($query) {
            $query->where('name', 'approver');
        });
    }

    /**
     * Scope a query to only include initiator user roles.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeInitiators($query)
    {
        return $query->whereHas('role', function ($query) {
            $query->where('name', 'initiator');
        });
    }
}
