<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class RefreshTransactionStats extends Command
{
    protected $signature = 'stats:refresh-materialized-view';
    protected $description = 'Refreshes the materialized view for transaction statistics';

    public function handle()
    {
        $this->info('Refreshing transaction stats materialized view...');
        
        try {
            // Use non-concurrent refresh since we're doing it hourly
            DB::statement('REFRESH MATERIALIZED VIEW mv_daily_transaction_stats');
            $this->info('Materialized view refreshed successfully.');
        } catch (\Exception $e) {
            Log::error('Failed to refresh materialized view: ' . $e->getMessage());
            $this->error('Failed to refresh materialized view: ' . $e->getMessage());
            return 1;
        }

        return 0;
    }
}
