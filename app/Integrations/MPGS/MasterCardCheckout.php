<?php

namespace App\Integrations\MPGS;

use App\Models\PaymentProviders;
use Illuminate\Support\Facades\Log;

class MasterCardCheckout
{
    private $apiUrl;
    private $apiKey;
    private $apiSecret;

    const TECHPAY_CODE = 'TECHPAY_MPGS';

    public function __construct(PaymentProviders $provider)
    {
        $this->apiUrl = $provider->api_url;
        $this->apiKey = $provider->api_key_id;
        $this->apiSecret = $provider->api_key_secret;
    }

    public function initiateCheckout(float $amount, string $merchant_name, string $order_ID, string $description_of_order, $txn_id, string $return_url, string $currency = 'ZMW')
    {
        $currency = 'ZMW';
        $f_amount = number_format(($amount * 1), 2, '.', '');
        $curl = curl_init();
        $url = config('app.url');
        $endpoint = $this->apiUrl . "/api/rest/version/72/merchant/{$this->apiKey}/session";
        $request = json_encode([
            "apiOperation" => "INITIATE_CHECKOUT",
            "interaction" => [
                "operation" => "PURCHASE",
                "merchant" => [
                    "name" => $merchant_name,
                    "url" => $url
                ],
                "returnUrl" => $return_url
            ],
            "order" => [
                "currency" => $currency,
                "amount" => $f_amount,
                "id" => $order_ID,
                "reference" =>  $txn_id,
                "description" => $description_of_order,
            ],
            'transaction' => [
                'reference' => $txn_id,
            ],
        ]);
        curl_setopt_array($curl, array(
            CURLOPT_URL => $endpoint,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => $request,
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json',
                'Authorization: Basic ' . base64_encode('merchant.' . $this->apiKey . ':' . $this->apiSecret)
            ),
        ));
        Log::info('merchant.' . $this->apiKey . ':' . $this->apiSecret);
        Log::info('Request to Card Processor URL: ' . $endpoint);
        Log::info('Request to Card Processor: ' . $request);

        $response = curl_exec($curl);

        if ($response === false) {
            throw new \Exception('Could not connect to Card Processor: ' . curl_error($curl));
        }

        curl_close($curl);

        $decodedResponse = json_decode($response);
        Log::info("Response from Card Processor: " . $response);

        if ($decodedResponse->result !== 'SUCCESS') {
            Log::error('Could not connect to Card Processor: ' . $response);
            throw new \Exception('Could not connect to Card Processor: ' . $decodedResponse->result);
        }
        return [
            'sessionId' => $decodedResponse->session->id,
            'successIndicator' => $decodedResponse->successIndicator
        ];
    }
}
