<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ImportAirtel extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:import-airtel';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */


    public function handle()
    {
        try {
            \DB::beginTransaction();
            $this->info('Importing Airtel Zambia');


            $config = config('airtel')['keys'];
            foreach ($config as $key => $row) {

                //prod provider
                $provider = new \App\Models\PaymentProviders();
                $provider->name = 'Airtel Zambia';
                $provider->code = $key;
                $provider->api_key_id = $row['production']['client_id'];
                $provider->api_key_secret = $row['production']['client_secret'];
                $provider->api_url = $row['production']['endpoint'];
                $provider->api_token = null;
                $provider->callback_url = null;
                $provider->environment = 'production';
                $provider->details = json_encode($row['details']);
                $provider->save();

                //sandbox provider
                $provider = new \App\Models\PaymentProviders();
                $provider->name = 'Airtel Zambia';
                $provider->code = $key;
                $provider->api_key_id = $row['sandbox']['client_id'];
                $provider->api_key_secret = $row['sandbox']['client_secret'];
                $provider->api_url = $row['sandbox']['endpoint'];
                $provider->api_token = null;
                $provider->callback_url = null;
                $provider->environment = 'sandbox';
                $provider->details = json_encode($row['details']);
                $provider->save();
            }
            $this->info('Airtel Zambia imported successfully');
            \DB::commit();
        } catch (\Exception $e) {
            \DB::rollBack();
            $this->error('Error importing Airtel Zambia: ' . $e->getMessage());
        }


    }
}
