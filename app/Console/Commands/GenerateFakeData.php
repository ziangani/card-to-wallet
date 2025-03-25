<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Faker\Factory as Faker;
use App\Models\Terminals;
use App\Models\Merchants;
use App\Models\TerminalHeartbeat;
use App\Models\CompanyDetail;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class GenerateFakeData extends Command
{
    protected $signature = 'generate:fake-data {count=10}';
    protected $description = 'Generate fake data for terminals, merchants, and terminal heartbeats';

    public function handle()
    {
        $faker = Faker::create();
        $count = $this->argument('count');

        $this->info("Generating {$count} fake records for each model...");

        try {
            DB::beginTransaction();

            // Get existing companies and users
            $companies = CompanyDetail::all();
            $users = User::all();

            if ($companies->isEmpty()) {
                $this->error("No existing companies found. Please create some companies first.");
                return 1;
            }

            if ($users->isEmpty()) {
                $this->error("No existing users found. Please create some users first.");
                return 1;
            }

            // Generate Merchants
            $createdMerchants = [];
            for ($i = 0; $i < $count; $i++) {
                $company = $faker->randomElement($companies);
                $user = $faker->randomElement($users);
                $merchant = Merchants::create([
                    'company_id' => $company->id,
                    'name' => $company->name ?? $faker->company,
                    'code' => Str::upper(Str::random(6)),
                    'logo' => null,
                    'description' => $faker->sentence,
                    'status' => $faker->randomElement(['ACTIVE', 'DISABLED']),
                    'created_by' => $user->id,
                    'updated_by' => $user->id,
                ]);
                $createdMerchants[] = $merchant;
            }
            $this->info("Generated {$count} fake merchants.");

            // Generate Terminals
            $createdTerminals = [];
            foreach ($createdMerchants as $merchant) {
                $terminalCount = $faker->numberBetween(1, 3);
                for ($i = 0; $i < $terminalCount; $i++) {
                    $terminal = Terminals::create([
                        'terminal_id' => $faker->unique()->numberBetween(10000, 99999),
                        'serial_number' => $faker->unique()->numberBetween(10000, 99999),
                        'merchant_id' => $merchant->id,
                        'type' => $faker->randomElement(['POS', 'mPOS', 'SmartPOS']),
                        'model' => $faker->word,
                        'manufacturer' => $faker->company,
                        'status' => $faker->randomElement(['ACTIVATED', 'UPLOADED']),
                        'date_activated' => $faker->dateTimeThisYear(),
                        'activation_code' => Str::random(8),
                    ]);
                    $createdTerminals[] = $terminal;
                }
            }
            $this->info("Generated " . count($createdTerminals) . " fake terminals.");

            // Generate Terminal Heartbeats
            $heartbeatCount = 0;
            foreach ($createdTerminals as $terminal) {
                $heartbeatCount += $faker->numberBetween(1, 5);
                for ($i = 0; $i < $heartbeatCount; $i++) {
                    TerminalHeartbeat::create([
                        'terminal_id' => $terminal->id,
                        'location' => $faker->city . ', ' . $faker->country,
                        'battery_health' => $faker->numberBetween(0, 100),
                        'transactions_count' => $faker->numberBetween(0, 1000),
                        'misc' => json_encode([
                            'signal_strength' => $faker->numberBetween(0, 5),
                            'software_version' => $faker->semver,
                            'last_update' => $faker->dateTimeThisMonth()->format('Y-m-d H:i:s'),
                        ]),
                    ]);
                }
            }
            $this->info("Generated {$heartbeatCount} fake terminal heartbeats.");

            DB::commit();
            $this->info('Fake data generation completed successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            $this->error('An error occurred while generating fake data: ' . $e->getMessage());
            return 1;
        }
    }
}
