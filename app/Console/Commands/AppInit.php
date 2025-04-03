<?php

namespace App\Console\Commands;

use App\Common\GeneralStatus;
use App\Common\Helpers;
use App\Common\UserClasses;
use App\Integrations\KonseKonse\cGrate;
use App\Integrations\MPGS\MasterCardCheckout;
use App\Models\PaymentProviders;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class AppInit extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:app-init';

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

            $user = 'charles@techpay.co.zm';
            if (!User::where('email', $user)->exists()) {
                $pas_user = new \stdClass();
                $pas_user->auth_id = 'Charles';
                $pas_user->first_name = 'Mtonga';
                $pas_user->surname = 'techpay_admin';
                $pas_user->mobile_no = '0964926646';
                $pas_user->email_id = $user;
                $pas_user->user_password = 'password';


                $user = new User();
                $user->uuid = Helpers::generateUUID();
//                $user->auth_id = $pas_user->auth_id;
//                $user->auth_password = Hash::make($pas_user->user_password);
                $user->password = Hash::make($pas_user->user_password);
                $user->first_name = strtoupper($pas_user->first_name);
                $user->last_name = strtoupper($pas_user->surname);
                $user->name = strtoupper($pas_user->first_name . ' ' . $pas_user->surname);
                $user->phone_number = $pas_user->mobile_no;
                $user->email = $pas_user->email_id;
                $user->date_of_birth = now();
//                $user->status = GeneralStatus::STATUS_ACTIVE;
//                $user->user_class = UserClasses::USER_CLASS_ADMIN_USER;
//                $user->changed_one_time_password = 0;
                $user->save();
                $this->info('User successfully added');
            }

            //init cgrate
//            $wallet = config('cgrate.default_wallet');
//            if (!PaymentProviders::where('code', $wallet)->exists()) {
//                $client = new cGrate('init');
//                $provider = new PaymentProviders();
//                $provider->name = 'cGrate';
//                $provider->code = $wallet;
//                $provider->api_key_id = $client->getUsername();
//                $provider->api_key_secret = $client->getPassword();
//                $provider->api_url = $client->getEndpoint();
//                $provider->api_token = null;
//                $provider->callback_url = null;
//                $provider->environment = 'production';
//                $provider->details = json_encode([]);
//                $provider->save();
//                $this->info('cGrate provider successfully added');
//            }
            //init mastercard
            $wallet = MasterCardCheckout::TECHPAY_CODE;
            if (!PaymentProviders::where('name', $wallet)->exists()) {
                $provider = new PaymentProviders();
                $provider->name = 'MasterCard';
                $provider->code = $wallet;
                $provider->api_key_id = '';
                $provider->api_key_secret = '';
                $provider->api_url = 'https://test-gateway.mastercard.com';
                $provider->api_token = null;
                $provider->callback_url = null;
                $provider->environment = 'production';
                $provider->details = json_encode([]);
                $provider->save();
                $this->info('MasterCard provider successfully added');
            }

        } catch (\Exception $e) {
            $this->error('Something went wrong: ' . $e->getMessage());
        }
    }
}
