<?php

namespace App\Integrations\Airtel;

use App\Common\Helpers;
use App\Integrations\Stubs\transactionStatus;
use App\Integrations\Stubs\Transfers;
use App\Integrations\Stubs\transferStatus;
use App\Models\PaymentProviders;
use App\Models\ProviderAccessTokens;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class Airtel extends Transfers
{

    private static $lastRequest = null;
    private static $lastRequestHeader = null;
    private static $lastRequestBody = null;
    private static $lastResponse = null;


    public static function getToken(PaymentProviders $provider, $reference)
    {
        $token = ProviderAccessTokens::where('payment_providers_id', $provider->id)->orderBy('id', 'desc')->first();
        if($token){
            $token->usage = $token->usage + 1;
            $token->save();
        }
        //if the token is older than 2.5 minutes, get a new one
        if ($token && strtotime($token->created_at) < strtotime('-2 minutes')) {
            $response = self::getTokenDirectly($provider, $reference);
            $token = new ProviderAccessTokens();
            $token->token = $response->access_token;
            $token->payment_providers_id = $provider->id;
            $token->provider_code = $provider->code;
            $token->details = json_encode($response);
            $token->save();
        }
        return $token;
    }

    public static function getTokenDirectly($provider, $reference): \stdClass
    {
        $client_secret = $provider->api_key_secret;
        $client_id = $provider->api_key_id;
        $endpoint = $provider->api_url . 'auth/oauth2/token';

        $body = [
            "client_secret" => $client_secret,
            "client_id" => $client_id,
            "grant_type" => "client_credentials"
        ];
        $request_time = date('Y-m-d h:i:s');
        $result = self::sendRequest($body, $endpoint);


        Helpers::logApiRequest(self::$lastRequest, $body, $request_time, date('Y-m-d h:i:s'), $body, $result['response'], $reference, '', 'SUCCESS', 'AIRTEL_GET_TOKEN');
        Helpers::createBasicLog('momo', "Airtel token request : " . json_encode($body), $reference);
        Helpers::createBasicLog('momo', "Airtel token response : " . $result['response'], $reference);


        if ($result['errorCode'] != 0) {
            throw new \Exception('The Airtel system could not be contacted at this time: Auth error.');
        }

        try {
            $response = json_decode($result['response']);
        } catch (\Exception $e) {
            throw new \Exception('The Airtel system could not be contacted at this time: No Json');
        }

        if (!isset($response->access_token)) {
            throw new \Exception('The Airtel system could not be contacted at this time: No Tkn');
        }
        return $response;
    }


    public static function initiateTransfer(PaymentProviders $provider, string $reference, string $uuid, float $amount, string $from, string $to): transferStatus
    {
        try {

            $token = self::getToken($provider, $reference);

            $headers = array(
                'Content-Type' => 'application/json',
                'X-Country' => 'ZM',
                'X-Currency' => 'ZMW',
                'Authorization' => 'Bearer  ' . $token->token,
            );

            $endpoint = $provider->api_url . 'merchant/v1/payments/';
            $client = new Client(['defaults' => [
                'verify' => false
            ]]);

            $request_body = array(
                "reference" => $reference,
                "subscriber" => [
                    "country" => "ZM",
                    "currency" => "ZMW",
                    "msisdn" => $from
                ],
                "transaction" => [
                    "amount" => number_format($amount, 2, '.', ''),
                    "country" => "ZM",
                    "currency" => "ZMW",
                    "id" => $uuid
                ]
            );

            self::$lastRequest =
                [
                    'headers' => $headers,
                    'json' => $request_body
                ];
            Helpers::createBasicLog('momo', "Airtel transaction push request : " . json_encode(self::$lastRequest), $uuid);
            $request_time = date('Y-m-d h:i:s');

            $response = $client->request('POST', $endpoint, array(
                    'headers' => $headers,
                    'json' => $request_body,
                )
            );
            $body = $response->getBody()->getContents();
            Helpers::logApiRequest(self::$lastRequest, $body, $request_time, date('Y-m-d h:i:s'), $body, $body, $reference, $uuid, 'SUCCESS', 'AIRTEL_PUSH_REQUEST');
            Helpers::createBasicLog('momo', "Airtel transaction push response : " . json_encode($body), $uuid);

            $result = json_decode($body);
            if (!isset($result->status)) {
                throw new \Exception('The Airtel system could not be contacted at this time: No Status');
            }
            self::$lastResponse = $result;
            $status = new transferStatus();
            $status->status = ($result->status->code == '200') ? transferStatus::STATUS_SUCCESS : transferStatus::STATUS_FAILED;
            $status->reference = $result->status->response_code ?? '';
            $status->statusMessage = $result->status->message ?? '';
            return $status;
        } catch (\Exception $e) {
            Helpers::reportException($e);
            throw new \Exception('The Airtel system could not be contacted at this time: Payment Error');
        }
    }

    private static function sendRequest($soap_request, $url): array
    {
        $soap_do = curl_init();
        curl_setopt($soap_do, CURLOPT_URL, $url);
        curl_setopt($soap_do, CURLOPT_CONNECTTIMEOUT, 60);
        curl_setopt($soap_do, CURLOPT_TIMEOUT, 60);
        curl_setopt($soap_do, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($soap_do, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($soap_do, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($soap_do, CURLOPT_POST, true);
        curl_setopt($soap_do, CURLOPT_POSTFIELDS, $soap_request);
        curl_setopt($soap_do, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);

        $response = curl_exec($soap_do);
        $errorCode = curl_errno($soap_do);

        if ($errorCode != 0) {
            $response = 'Sorry, we are unable to connect to Airtel. Please try again after a few minutes. [I-' . $errorCode . '] ' . curl_error($soap_do);
        }

        curl_close($soap_do);

        self::$lastRequest = $soap_request;
        self::$lastRequestHeader = null;
        self::$lastRequestBody = $soap_request;
        self::$lastResponse = $response;

        return array('errorCode' => $errorCode, 'response' => $response);
    }

    /**
     * @throws GuzzleException
     * @throws \Exception
     */
    public static function getStatus(PaymentProviders $provider, string $external_reference): transactionStatus
    {

        $token = self::getToken($provider, $external_reference);

        $headers = array(
            'Content-Type' => 'application/json',
            'X-Country' => 'ZM',
            'X-Currency' => 'ZMW',
            'Authorization' => 'Bearer  ' . $token->token,
        );
        $endpoint = $provider->api_url . 'standard/v1/payments/' . $external_reference;
        $client = new Client(['defaults' => [
            'verify' => false
        ]]);

        try {
            $request_time = date('Y-m-d h:i:s');
            $request = [
                'headers' => $headers,
                'endpoint' => $endpoint,
            ];
            $response = $client->request('GET', $endpoint, array(
                    'headers' => $headers,
                )
            );
            $body = $response->getBody()->getContents();

            Helpers::logApiRequest($request, $body, $request_time,  date('Y-m-d h:i:s'), $provider, '', $external_reference, '', 'SUCCESS', 'AIRTEL_STATUS_INQUIRY');
            Helpers::createBasicLog('momo', "Airtel Status response : " . $body, $external_reference);
            $result = json_decode($body);
            if (!isset($result->status)) {
                throw new \Exception('The Airtel system could not be contacted at this time: No Status');
            }
            $status = new transactionStatus();
            $status->status = $result->data->transaction->status ?? $result->status->code;
            $status->reference = $result->data->transaction->airtel_money_id ?? '';
            $status->secondayReference = $result->status->response_code ?? '';
            $status->statusMessage = $result->data->transaction->message ?? $result->status->message;
            $status->rawResponse = $result;
            return $status;

        } catch (\Exception $e) {
            throw new \Exception('Could not reach Airtel: ' . $e->getMessage());
        }
    }

    public static function getLastRequest()
    {
        return self::$lastRequest;
    }

    public static function getLastResponse()
    {
        return self::$lastResponse;
    }

    public static function getLastRequestHeader()
    {
        return self::$lastRequestHeader;
    }

    public static function getLastRequestBody()
    {
        return self::$lastRequestBody;
    }

}
