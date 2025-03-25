<?php

namespace Database\Seeders;

use App\Models\Charges;
use Illuminate\Database\Seeder;

class DefaultChargesSeeder extends Seeder
{
    public function run(): void
    {
        // ABSA default charges
        Charges::create([
            'channel' => 'ABSA',
            'charge_name' => 'ROLLING_RESERVE',
            'description' => 'Default rolling reserve for ABSA transactions',
            'charge_type' => 'PERCENTAGE',
            'charge_value' => 8.00,
            'is_default' => true,
            'is_active' => true
        ]);

        Charges::create([
            'channel' => 'ABSA',
            'charge_name' => 'BANK_FEE',
            'description' => 'Default bank fee for ABSA transactions',
            'charge_type' => 'PERCENTAGE',
            'charge_value' => 3.00,
            'is_default' => true,
            'is_active' => true
        ]);

        Charges::create([
            'channel' => 'ABSA',
            'charge_name' => 'PLATFORM_FEE',
            'description' => 'Default platform fee for ABSA transactions',
            'charge_type' => 'PERCENTAGE',
            'charge_value' => 1.20,
            'is_default' => true,
            'is_active' => true
        ]);

        Charges::create([
            'channel' => 'ABSA',
            'charge_name' => 'TRANSACTION_FEE',
            'description' => 'Fixed per-transaction fee for ABSA transactions',
            'charge_type' => 'FIXED',
            'charge_value' => 0.20,
            'is_default' => true,
            'is_active' => true
        ]);

        // Mobile Money default charge
        Charges::create([
            'channel' => 'MOBILE_MONEY',
            'charge_name' => 'PROVIDER_FEE',
            'description' => 'Default provider fee for mobile money transactions',
            'charge_type' => 'PERCENTAGE',
            'charge_value' => 2,
            'is_default' => true,
            'is_active' => true
        ]);

        Charges::create([
            'channel' => 'MOBILE_MONEY',
            'charge_name' => 'PLATFORM_FEE',
            'description' => 'Default platform fee for Techpay transactions',
            'charge_type' => 'PERCENTAGE',
            'charge_value' => 0.5,
            'is_default' => true,
            'is_active' => true
        ]);


        // Card default charge
        Charges::create([
            'channel' => 'CARD',
            'charge_name' => 'PROVIDER_FEE',
            'description' => 'Default provider fee for card transactions',
            'charge_type' => 'PERCENTAGE',
            'charge_value' => 2.50,
            'is_default' => true,
            'is_active' => true
        ]);

         // Card default charge
         Charges::create([
            'channel' => 'CARD',
            'charge_name' => 'PLATFORM_FEE',
            'description' => 'Default techpay fee for card transactions',
            'charge_type' => 'PERCENTAGE',
            'charge_value' => 1.0,
            'is_default' => true,
            'is_active' => true
        ]);

     

        //CashOut default charges
        Charges::create([
            'channel' => 'CASHOUT',
            'charge_name' => 'PLATFORM_FEE',
            'description' => 'Default Techpay fee for cashout transactions',
            'charge_type' => 'FIXED',
            'charge_value' => 25.00,
            'is_default' => true,
            'is_active' => true
        ]);
    }
}
