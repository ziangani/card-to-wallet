<?php

namespace App\Console\Commands;

use App\Common\SystemStatsGenerator;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class UpdateStatsCache extends Command
{
    protected $signature = 'stats:update-cache';
    protected $description = 'Updates the system stats cache every 3 minutes for dashboard data';

    public function handle()
    {
        $this->info('Updating system stats cache...');
        
        try {
            // This will automatically cache the results in the cache_records table
            SystemStatsGenerator::getSummary(
                date('Y-m-d 00:00:00'),
                date('Y-m-d 23:59:59')
            );
            
            $this->info('Cache updated successfully.');
        } catch (\Exception $e) {
            Log::error('Failed to update stats cache: ' . $e->getMessage());
            $this->error('Failed to update cache: ' . $e->getMessage());
            return 1;
        }

        return 0;
    }
}
