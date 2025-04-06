<?php

namespace App\Enums;

enum PaymentChannel: string
{
    case MOBILE_MONEY = 'MOBILE_MONEY';
    case CARD = 'CARD';
    case ABSA = 'ABSA';
    case FNB = 'FNB';
    case UBA = 'UBA';
    case CASHOUT = 'CASHOUT';
    case CARD_TO_WALLET = 'CARD_TO_WALLET';

    public function label(): string
    {
        return match($this) {
            self::MOBILE_MONEY => 'Mobile Money',
            self::CARD => 'Card',
            self::ABSA => 'ABSA',
            self::FNB => 'FNB',
            self::UBA => 'UBA',
            self::CASHOUT => 'Techpay Cash out',
            self::CARD_TO_WALLET => 'Card to Wallet',
        };
    }

    public static function toArray(): array
    {
        return collect(self::cases())->mapWithKeys(fn ($case) => [
            $case->value => $case->label()
        ])->all();
    }
}
