<?php

namespace App\Models\Traits;

use App\Models\Approval;
use Illuminate\Database\Eloquent\Casts\Attribute;

trait HasApprovals
{
    abstract public static function getApprovalLevels(): array;

    public function approvals()
    {
        return $this->hasMany(Approval::class, 'reference', 'reference')
            ->where('module', static::getModuleName());
    }

    public function currentApproval()
    {
        return $this->approvals()->latest()->first();
    }

    public function latestApprovalAtLevel(int $level)
    {
        return $this->approvals()
            ->where('level', $level)
            ->latest()
            ->first();
    }

    public function isAtFinalLevel(): bool
    {
        return $this->approval_level === array_key_last(static::getApprovalLevels());
    }

    public function getNextLevel(): ?int
    {
        $nextLevel = $this->approval_level + 1;
        return array_key_exists($nextLevel, static::getApprovalLevels()) ? $nextLevel : null;
    }

    public function getCurrentLevelNameAttribute(): string
    {
        return static::getApprovalLevels()[$this->approval_level] ?? 'UNKNOWN';
    }

    public function needsClarity(): bool
    {
        $currentApproval = $this->currentApproval();
        return $currentApproval && $currentApproval->needsClarity();
    }

    public function isApproved(): bool
    {
        return $this->status === 'APPROVED' && $this->isAtFinalLevel();
    }

    public function isRejected(): bool
    {
        $currentApproval = $this->currentApproval();
        return $currentApproval && $currentApproval->isRejected();
    }

    public static function canUserReviewLevel($user, int $level): bool
    {
        if ($user->isSysAdmin()) {
            return true;
        }

        return $user->hasRole(static::getApprovalLevels()[$level]);
    }

    public static function canUserInitiate($user): bool
    {
        return $user->isSysAdmin() || $user->hasRole(static::getApprovalLevels()[0]);
    }

    public static function getUserApprovalLevel($user): ?int
    {
        if ($user->isSysAdmin()) {
            return null; // Sysadmin can access all levels
        }

        return array_search($user->user_type, static::getApprovalLevels());
    }

    protected static function getModuleName(): string
    {
        return strtolower(class_basename(static::class));
    }
}
