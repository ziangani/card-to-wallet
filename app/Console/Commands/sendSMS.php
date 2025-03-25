<?php

namespace App\Console\Commands;

use App\Models\SmsNotifications;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class sendSMS extends Command
{

    protected $signature = 'sendsms';
    protected $description = 'Command description';


    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        try {
            $pendingSMSs = SmsNotifications::where('status', 'PENDING')->orderBy('id', 'desc')->get();
            $this->info(count($pendingSMSs) . ' pending SMSs found.');

            foreach ($pendingSMSs as $sms) {

                $number = (double)$sms->mobile;

                if (strlen($number) == 10) {
                    $number = '26' . $number;
                    $this->info($sms->mobile . ' updated to: ' . $number);
                }
                if (strlen($number) == 9) {
                    $number = '260' . $number;
                    $this->info($sms->mobile . ' updated to: ' . $number);
                }
                $response = $this->sendSMS($sms);
                $status = json_encode($response);

                $sms->status = 'COMPLETE';
                $sms->sent_at = now();
                $sms->response = json_encode($response);
                $sms->save();
                $this->info('SMS Successfully sent, status: ' . $status);

            }
        } catch (\Exception $ex) {
            $this->error('Something went wrong: ' . $ex->getMessage() . ' ' . $ex->getFile());
            Log::error('Error sending SMS: ' . $ex->getMessage());
            $sms->status = 'FAILED';
            $sms->sent_at = now();
            $sms->save();
        }
    }

    public
    function sendSMS(SMSNotifications $sms)
    {
        $url = 'https://notifications.techpay.co.zm/api/v1/messaging/send';
        $curl = curl_init();
        $reference = $sms->id . '-' . time();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => 'recipient=' . $sms->mobile . '&sender='. $sms->sender . '&channel=SMS&reference=' . $reference . '&message=' . $sms->message . '&source=VOTINGSYSTEM',
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/x-www-form-urlencoded'
            ),
        ));
        $response = curl_exec($curl);
        if (curl_errno($curl) != 0) {
            curl_close($curl);
            throw new \Exception('Error sending SMS: ' . curl_error($curl));
        }
        curl_close($curl);

        $response = json_decode($response);
        if (!isset($response->statusCode))
            throw new \Exception('Invalid SMS response: ' . $response->statusDescription);

        if ($response->statusCode != 0)
            throw new \Exception('Error sending SMS: ' . $response->statusDescription);

        return $response;
    }


}
