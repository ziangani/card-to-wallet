<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Filament\Models\Contracts\FilamentUser;
use Filament\Models\Contracts\HasName;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class User extends Authenticatable implements HasName, FilamentUser, MustVerifyEmail
{
    use HasFactory, Notifiable;

    public static $userTypes = [
        'DATA_ENTRY' => 'DATA_ENTRY',
        'COMPLIANCE' => 'COMPLIANCE',
        'SETTLEMENT' => 'SETTLEMENT',
        'FINANCE' => 'FINANCE',
        'EXCO' => 'EXCO',
        'ACCOUNT_MANAGER' => 'ACCOUNT_MANAGER',
        'MERCHANT' => 'MERCHANT',
        'SYSADMIN' => 'SYSADMIN',
        'individual' => 'individual',
        'corporate' => 'corporate'
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'first_name',
        'last_name',
        'phone_number',
        'date_of_birth',
        'verification_level',
        'is_active',
        'is_email_verified',
        'is_phone_verified',
        'auth_id',
        'auth_password',
        'user_type',
        'merchant_id',
        'company_detail_id',
        'company_id',
        'address',
        'city',
        'country',
        'login_attempts'
    ];

    /**
     * Override the __get method to always return true for is_phone_verified
     * while still allowing the property to be set in the database.
     *
     * @param string $key
     * @return mixed
     */
    public function __get($key)
    {
        if ($key === 'is_phone_verified') {
            return true;
        }
        if ($key === 'is_email_verified') {
            return true;
        }

        return parent::__get($key);
    }

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'last_login_at' => 'datetime',
            'date_of_birth' => 'datetime',
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function canAccessPanel(Panel $panel): bool
    {
        return true;
        return $this->user_type !== 'MERCHANT';
    }

    public function hasRole(string $role): bool
    {
        return $this->user_type === $role;
    }

    public function isSysAdmin(): bool
    {
        return $this->hasRole('SYSADMIN');
    }

    public function getFilamentName(): string
    {
        return $this->getAttributeValue('first_name');
    }

    public static function getUsersByRole($roles): \Illuminate\Database\Eloquent\Collection
    {
        $roles = is_array($roles) ? $roles : [$roles];
        return static::whereIn('user_type', $roles)->get();
    }

    /**
     * Get the company that owns the user.
     */
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Get the corporate user roles for the user.
     */
    public function corporateUserRoles()
    {
        return $this->hasMany(CorporateUserRole::class);
    }

    /**
     * Get the corporate roles for the user.
     */
    public function corporateRoles()
    {
        return $this->belongsToMany(CorporateRole::class, 'corporate_user_roles', 'user_id', 'role_id')
            ->withPivot('company_id', 'is_primary', 'assigned_by', 'assigned_at')
            ->withTimestamps();
    }

    /**
     * Check if the user is a corporate user.
     *
     * @return bool
     */
    public function isCorporate()
    {
        return $this->user_type === 'corporate';
    }

    /**
     * Check if the user is an individual user.
     *
     * @return bool
     */
    public function isIndividual()
    {
        return $this->user_type === 'individual';
    }

    /**
     * Check if the user has a specific corporate role.
     *
     * @param  string  $role
     * @return bool
     */
    public function hasCorporateRole($role)
    {
        return $this->corporateRoles()->where('name', $role)->exists();
    }

    /**
     * Check if the user is a corporate admin.
     *
     * @return bool
     */
    public function isCorporateAdmin()
    {
        return $this->hasCorporateRole('admin');
    }

    /**
     * Check if the user is a corporate approver.
     *
     * @return bool
     */
    public function isCorporateApprover()
    {
        return $this->hasCorporateRole('approver');
    }

    /**
     * Check if the user is a corporate initiator.
     *
     * @return bool
     */
    public function isCorporateInitiator()
    {
        return $this->hasCorporateRole('initiator');
    }
}
