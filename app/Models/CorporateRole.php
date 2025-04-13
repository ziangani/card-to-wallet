<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CorporateRole extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'description',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the user roles for the role.
     */
    public function userRoles()
    {
        return $this->hasMany(CorporateUserRole::class, 'role_id');
    }

    /**
     * Get the users with this role.
     */
    public function users()
    {
        return $this->hasManyThrough(
            User::class,
            CorporateUserRole::class,
            'role_id',
            'id',
            'id',
            'user_id'
        );
    }

    /**
     * Check if the role is admin.
     *
     * @return bool
     */
    public function isAdmin()
    {
        return $this->name === 'admin';
    }

    /**
     * Check if the role is approver.
     *
     * @return bool
     */
    public function isApprover()
    {
        return $this->name === 'approver';
    }

    /**
     * Check if the role is initiator.
     *
     * @return bool
     */
    public function isInitiator()
    {
        return $this->name === 'initiator';
    }

    /**
     * Get the role label.
     *
     * @return string
     */
    public function getLabel()
    {
        $labels = [
            'admin' => 'Administrator',
            'approver' => 'Approver',
            'initiator' => 'Initiator',
        ];

        return $labels[$this->name] ?? ucfirst($this->name);
    }

    /**
     * Get the role description.
     *
     * @return string
     */
    public function getDescription()
    {
        if ($this->description) {
            return $this->description;
        }

        $descriptions = [
            'admin' => 'Full control of corporate account, users, and transactions',
            'approver' => 'Can approve transactions and user management actions',
            'initiator' => 'Can initiate transactions but requires approval',
        ];

        return $descriptions[$this->name] ?? '';
    }

    /**
     * Scope a query to only include the admin role.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeAdmin($query)
    {
        return $query->where('name', 'admin');
    }

    /**
     * Scope a query to only include the approver role.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeApprover($query)
    {
        return $query->where('name', 'approver');
    }

    /**
     * Scope a query to only include the initiator role.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeInitiator($query)
    {
        return $query->where('name', 'initiator');
    }

    /**
     * Scope a query to only include roles with a specific name.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  string  $name
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWithName($query, $name)
    {
        return $query->where('name', $name);
    }

    /**
     * Scope a query to order roles by name in ascending order.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOrderByNameAsc($query)
    {
        return $query->orderBy('name', 'asc');
    }

    /**
     * Scope a query to order roles by name in descending order.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOrderByNameDesc($query)
    {
        return $query->orderBy('name', 'desc');
    }

    /**
     * Scope a query to order roles by creation date in ascending order.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOrderByCreatedAsc($query)
    {
        return $query->orderBy('created_at', 'asc');
    }

    /**
     * Scope a query to order roles by creation date in descending order.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOrderByCreatedDesc($query)
    {
        return $query->orderBy('created_at', 'desc');
    }
    
    /**
     * Check if the role has a specific permission.
     *
     * @param string $permission
     * @return bool
     */
    public function hasPermission($permission)
    {
        // Define default permissions for each role
        $rolePermissions = [
            'admin' => [
                'view_wallet', 'deposit_funds', 'withdraw_funds',
                'view_disbursements', 'create_disbursements', 'approve_disbursements',
                'view_users', 'invite_users', 'manage_roles',
                'view_reports', 'generate_reports',
                'view_settings', 'update_company', 'manage_workflows',
                'view_approvals', 'approve_requests', 'reject_requests'
            ],
            'approver' => [
                'view_wallet', 
                'view_disbursements', 'approve_disbursements',
                'view_users',
                'view_reports',
                'view_settings',
                'view_approvals', 'approve_requests', 'reject_requests'
            ],
            'initiator' => [
                'view_wallet', 'deposit_funds',
                'view_disbursements', 'create_disbursements',
                'view_users',
                'view_reports',
                'view_settings',
                'view_approvals'
            ]
        ];
        
        // Get permissions for this role
        $permissions = $rolePermissions[$this->name] ?? [];
        
        // Check if the requested permission is in the list
        return in_array($permission, $permissions);
    }
}
