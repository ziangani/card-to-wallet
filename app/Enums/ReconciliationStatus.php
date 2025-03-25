<?php

namespace App\Enums;

enum ReconciliationStatus: string
{
    case ACTIVE = 'ACTIVE';
    case SUPERSEDED = 'SUPERSEDED';

    public function label(): string
    {
        return match($this) {
            self::ACTIVE => 'Active',
            self::SUPERSEDED => 'Superseded',
        };
    }

    public static function toArray(): array
    {
        return collect(self::cases())->mapWithKeys(fn ($case) => [
            $case->value => $case->label()
        ])->all();
    }
}
