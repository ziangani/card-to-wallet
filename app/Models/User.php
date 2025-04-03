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
        'SYSADMIN' => 'SYSADMIN'
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
        'company_detail_id'
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
}
