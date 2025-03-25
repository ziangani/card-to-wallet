<?php

namespace App\Console\Commands;

use App\Common\Helpers;
use App\Integrations\Airtel\Airtel;
use App\Models\PaymentProviders;
use App\Models\ProviderAccessTokens;
use Illuminate\Console\Command;

class updateAirtelTokens extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:update-airtel-tokens';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch latest api access tokens for Airtel Zambia';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        try {
            $environment = Helpers::getPaymentEnvironment();
            $providers = PaymentProviders::where('name', 'Airtel Zambia')->where('status', 'ACTIVE')->where('environment', $environment)->get();
            $this->info("\n\n: " . count($providers) . " Airtel providers found\n");
            foreach ($providers as $provider) {
                $this->info("Getting token for provider: " . $provider->name);
                $response = Airtel::getTokenDirectly($provider, 'AIRTEL_GET_TOKEN');
                $token = $response->access_token;
                $new_token = new ProviderAccessTokens();
                $new_token->token = $token;
                $new_token->payment_providers_id = $provider->id;
                $new_token->provider_code = $provider->code;
                $new_token->details = json_encode($response);
                $new_token->save();
                $this->info("Token saved for provider: " . $provider->name);
            }
        } catch (\Exception $e) {
            $this->error("An error occurred: " . $e->getMessage());
            Helpers::reportException($e);
        }
    }
}
