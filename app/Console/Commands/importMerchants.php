<?php

namespace App\Console\Commands;

use App\Models\Merchants;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class importMerchants extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:import-merchants {--source=cybersource : Source of merchant data (cybersource/absa)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import merchants from cybersource config or ABSA json file';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        try {
            DB::beginTransaction();
            
            if ($this->option('source') === 'absa') {
                $this->importFromAbsa();
            } else {
                $this->importFromCybersource();
            }

            DB::commit();
            $this->info("\nImport completed successfully");
        } catch (\Exception $e) {
            DB::rollBack();
            $this->error($e->getMessage());
        }
    }

    private function importFromCybersource()
    {
        $merchants = config('cybersource_keys');
        foreach ($merchants as $merchant_) {
            $this->info("\nProcessing merchant: " . $merchant_['merchant']);
            
            // Check if merchant exists
            $merchant = Merchants::where('code', $merchant_['mid'])->first();
            if ($merchant) {
                $this->info("Merchant already exists: " . $merchant->name);
                if ($merchant->name != $merchant_['merchant']) {
                    $this->info("Updating merchant name from: " . $merchant->name . " to: " . $merchant_['merchant']);
                    $merchant->name = $merchant_['merchant'];
                    $merchant->save();
                }
            } else {
                $merchant = new Merchants();
                $merchant->company_id = 1;
                $merchant->name = $merchant_['merchant'];
                $merchant->code = $merchant_['mid'];
                $merchant->created_by = 1;
                $merchant->save();
            }
        }
    }

    private function importFromAbsa()
    {
        $jsonPath = storage_path('app/absa_merchants.json');
        if (!file_exists($jsonPath)) {
            throw new \Exception("ABSA merchants file not found at: " . $jsonPath);
        }

        $merchants = json_decode(file_get_contents($jsonPath), true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \Exception("Failed to parse ABSA merchants JSON: " . json_last_error_msg());
        }

        foreach ($merchants as $merchant_) {
            $this->info("\nProcessing merchant: " . $merchant_['Sub Merchant Name']);
            
            // Check if merchant exists by Organization Id
            $merchant = Merchants::where('code', $merchant_['Organization Id'])->first();
            
            if ($merchant) {
                $this->info("Updating existing merchant: " . $merchant->name);
            } else {
                $this->info("Creating new merchant: " . $merchant_['Sub Merchant Name']);
                $merchant = new Merchants();
                $merchant->company_id = 1;
                $merchant->created_by = 1;
                $merchant->code = $merchant_['Organization Id'];
            }

            // Update merchant fields
            $merchant->name = $merchant_['Sub Merchant Name'];
            $merchant->acceptor_point = $merchant_['Acceptor Point'];
            $merchant->bank_merchant_id = $merchant_['Merchant ID'];
            $merchant->environment = $merchant_['status'];
            $merchant->url = $merchant_['URL'];
            $merchant->bank_creation_date = Carbon::createFromFormat('d/m/Y', $merchant_['Creation Date'])->format('Y-m-d');
            
            $merchant->save();
        }
    }
}
