<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CorporateRateTier;

class CorporateRateTierSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $rateTiers = [
            [
                'name' => 'Standard',
                'monthly_volume_minimum' => 0.00,
                'fee_percentage' => 3.50,
                'description' => 'Default rate for corporate accounts',
                'is_active' => true,
            ],
            [
                'name' => 'Silver',
                'monthly_volume_minimum' => 100000.00,
                'fee_percentage' => 3.00,
                'description' => 'Reduced rate for medium volume',
                'is_active' => true,
            ],
            [
                'name' => 'Gold',
                'monthly_volume_minimum' => 500000.00,
                'fee_percentage' => 2.50,
                'description' => 'Preferred rate for high volume',
                'is_active' => true,
            ],
            [
                'name' => 'Platinum',
                'monthly_volume_minimum' => 1000000.00,
                'fee_percentage' => 2.00,
                'description' => 'Premium rate for very high volume',
                'is_active' => true,
            ],
        ];

        foreach ($rateTiers as $tier) {
            CorporateRateTier::updateOrCreate(
                ['name' => $tier['name']],
                [
                    'monthly_volume_minimum' => $tier['monthly_volume_minimum'],
                    'fee_percentage' => $tier['fee_percentage'],
                    'description' => $tier['description'],
                    'is_active' => $tier['is_active'],
                ]
            );
        }
    }
}
