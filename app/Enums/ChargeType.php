<?php

namespace App\Enums;

enum ChargeType: string
{
    case FIXED = 'FIXED';
    case PERCENTAGE = 'PERCENTAGE';

    public function label(): string
    {
        return match($this) {
            self::FIXED => 'Fixed',
            self::PERCENTAGE => 'Percentage',
        };
    }

    public static function toArray(): array
    {
        return collect(self::cases())->mapWithKeys(fn ($case) => [
            $case->value => $case->label()
        ])->all();
    }
}
