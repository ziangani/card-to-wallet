<?php

namespace App\Enums;

enum ChargeName: string
{
    case PROVIDER_FEE = 'PROVIDER_FEE';
    case BANK_FEE = 'BANK_FEE';
    case PLATFORM_FEE = 'PLATFORM_FEE';
    case TRANSACTION_FEE = 'TRANSACTION_FEE';
    case ROLLING_RESERVE = 'ROLLING_RESERVE';
    case TECHPAY_CASHOUT_FEE = 'TECHPAY_CASHOUT_FEE';

    public function label(): string
    {
        return match($this) {
            self::PROVIDER_FEE => 'Provider Fee',
            self::BANK_FEE => 'Bank Fee',
            self::PLATFORM_FEE => 'Platform Fee',
            self::TRANSACTION_FEE => 'Transaction Fee',
            self::ROLLING_RESERVE => 'Rolling Reserve',
            self::TECHPAY_CASHOUT_FEE => 'Techpay Cashout Fee',
        };
    }

    public static function toArray(): array
    {
        return collect(self::cases())->mapWithKeys(fn ($case) => [
            $case->value => $case->label()
        ])->all();
    }
}
