<?php

namespace Database\Seeders;

use App\Models\Charges;
use Illuminate\Database\Seeder;

class DefaultChargesSeeder extends Seeder
{
    public function run(): void
    {

        // Card to Wallet default charges
        Charges::create([
            'channel' => 'CARD_TO_WALLET',
            'charge_name' => 'PLATFORM_FEE',
            'description' => 'Commission for card to wallet transactions',
            'charge_type' => 'PERCENTAGE',
            'charge_value' => 1.00,
            'is_default' => true,
            'is_active' => true
        ]);

        Charges::create([
            'channel' => 'CARD_TO_WALLET',
            'charge_name' => 'BANK_FEE',
            'description' => 'Bank fee for card to wallet transactions',
            'charge_type' => 'PERCENTAGE',
            'charge_value' => 3.00,
            'is_default' => true,
            'is_active' => true
        ]);

        Charges::create([
            'channel' => 'CARD_TO_WALLET',
            'charge_name' => 'PROVIDER_FEE',
            'description' => 'Interchange fee for card to wallet transactions',
            'charge_type' => 'FIXED',
            'charge_value' => 7.50,
            'is_default' => true,
            'is_active' => true
        ]);

        Charges::create([
            'channel' => 'CARD_TO_WALLET',
            'charge_name' => 'TRANSACTION_FEE',
            'description' => 'Fixed commission for card to wallet transactions',
            'charge_type' => 'FIXED',
            'charge_value' => 2.50,
            'is_default' => true,
            'is_active' => true
        ]);

        // Corporate Deposit default charges
        Charges::create([
            'channel' => 'CORPORATE_DEPOSIT',
            'charge_name' => 'PLATFORM_FEE',
            'description' => 'Fee for corporate wallet deposits',
            'charge_type' => 'PERCENTAGE',
            'charge_value' => 4.00,
            'is_default' => true,
            'is_active' => true
        ]);
    }
}
