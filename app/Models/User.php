<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Filament\Models\Contracts\FilamentUser;
use Filament\Models\Contracts\HasName;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class User extends Authenticatable implements HasName, FilamentUser
{
    use HasFactory, Notifiable;

    public function merchants(): BelongsTo
    {
        return $this->belongsTo(Merchants::class, 'merchant_id', 'id');
    }

    public function companyDetails(): BelongsTo
    {
        return $this->belongsTo(CompanyDetail::class, 'company_detail_id', 'id');
    }

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
        'surname',
        'mobile',
        'auth_id',
        'auth_password',
        'user_type',
        'merchant_id',
        'company_detail_id'
    ];

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
