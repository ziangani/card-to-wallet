<?php

namespace Database\Seeders;

use App\Models\CorporateRole;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CorporateRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        CorporateRole::insert([
            [
                'name' => 'admin',
                'description' => 'Full control of corporate account, users, and transactions',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'approver',
                'description' => 'Can approve transactions and user management actions',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'initiator',
                'description' => 'Can initiate transactions but requires approval',
                'created_at' => now(),
                'updated_at' => now()
            ],
        ]);
    }
}
