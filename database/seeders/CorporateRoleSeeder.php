<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CorporateRole;

class CorporateRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            [
                'name' => 'admin',
                'description' => 'Full control of corporate account, users, and transactions. Can manage company settings, invite users, and approve transactions.',
            ],
            [
                'name' => 'approver',
                'description' => 'Can approve transactions and user management actions. Cannot modify company settings or invite new users.',
            ],
            [
                'name' => 'initiator',
                'description' => 'Can initiate transactions but requires approval. Limited access to view company information and transaction history.',
            ],
        ];

        foreach ($roles as $role) {
            CorporateRole::updateOrCreate(
                ['name' => $role['name']],
                ['description' => $role['description']]
            );
        }
    }
}
