<?php

namespace App\Console\Commands;

use App\Models\MerchantReconciliation;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class GenerateRollingReserveReturns extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'rolling-reserve:generate-returns 
                            {--days=120 : Days to hold reserve}
                            {--merchant= : Specific merchant ID or all if not specified}
                            {--date= : Specific date to process (defaults to days ago from today)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate pending payouts for rolling reserves due to be returned';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $daysToHold = $this->option('days');
        $merchantId = $this->option('merchant');
        $specificDate = $this->option('date');
        
        // Determine the target date
        if ($specificDate) {
            $date = Carbon::parse($specificDate);
        } else {
            $date = Carbon::now()->subDays($daysToHold);
        }
        
        $this->info("Generating rolling reserve returns for {$date->format('Y-m-d')}");
        
        // Build query for active reconciliations from the target date
        $query = MerchantReconciliation::where('status', 'ACTIVE')
            ->whereDate('date', $date)
            ->where('rolling_reserve', '>', 0);
            
        // Filter by merchant if specified
        if ($merchantId) {
            $query->where('merchant_id', $merchantId);
            $this->info("Processing for merchant: {$merchantId}");
        }
        
        // Get reconciliations
        $reconciliations = $query->get();
        
        if ($reconciliations->isEmpty()) {
            $this->warn("No eligible reconciliations found for {$date->format('Y-m-d')}");
            return;
        }
        
        $this->info("Found {$reconciliations->count()} reconciliations with rolling reserves");
        
        $count = 0;
        $totalAmount = 0;
        
        foreach ($reconciliations as $reconciliation) {
            // Check if a payout already exists
            $existingPayout = $reconciliation->getRollingReserveReturnPayout();
            
            if ($existingPayout) {
                $this->info("Payout already exists for {$reconciliation->merchant_id} on {$reconciliation->date->format('Y-m-d')} with status: {$existingPayout->status}");
                continue;
            }
            
            // Create new payout
            $payout = $reconciliation->createRollingReserveReturnPayout('system');
            
            if ($payout) {
                $count++;
                $totalAmount += $payout->amount;
                $this->info("Created payout of {$payout->amount} for {$reconciliation->merchant_id}");
                
                // Log the creation
                Log::info("Created rolling reserve return payout", [
                    'merchant_id' => $reconciliation->merchant_id,
                    'date' => $reconciliation->date->format('Y-m-d'),
                    'amount' => $payout->amount,
                    'payout_id' => $payout->id
                ]);
            } else {
                $this->warn("Failed to create payout for {$reconciliation->merchant_id}");
            }
        }
        
        $this->info("Created {$count} pending rolling reserve return payouts totaling {$totalAmount}");
    }
}
