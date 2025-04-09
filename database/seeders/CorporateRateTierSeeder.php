<?php

namespace Database\Seeders;

use App\Models\CorporateRateTier;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CorporateRateTierSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        CorporateRateTier::insert([
            [
                'name' => 'Standard',
                'monthly_volume_minimum' => 0.00,
                'fee_percentage' => 3.50,
                'description' => 'Default rate for corporate accounts',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Silver',
                'monthly_volume_minimum' => 100000.00,
                'fee_percentage' => 3.00,
                'description' => 'Reduced rate for medium volume',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Gold',
                'monthly_volume_minimum' => 500000.00,
                'fee_percentage' => 2.50,
                'description' => 'Preferred rate for high volume',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Platinum',
                'monthly_volume_minimum' => 1000000.00,
                'fee_percentage' => 2.00,
                'description' => 'Premium rate for very high volume',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now()
            ],
        ]);
    }
}
