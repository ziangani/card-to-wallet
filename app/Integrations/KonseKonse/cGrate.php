<?php

namespace App\Integrations\KonseKonse;

use App\Common\Helpers;
use DOMDocument;
use Exception;

class cGrate
{


    private string $endpoint = "https://543.cgrate.co.zm/Konik/KonikWs";
    private string $username;
    private string $password;
    private $reference = '';

    public function getEndpoint()
    {
        return $this->endpoint;
    }
    public function getUsername()
    {
        return $this->username;
    }

    public function getPassword()
    {
        return $this->password;
    }
    public static $mobileVouchers = [
        'EF52DTHN2',//Airtel
        'EF52DDRS7',//MTN
        'EF1GHRID1'//Zamtel
    ];

    public static $queryVouchers = [
        'EM3GAQAR2',//DStv-Box office
        'ELOA1SA26',//DStv
        'ELOA1XKZ1',//GOtv
        'ERM2VV456',//Zesco
    ];
    public static $tvSubscriptionsVouchers = [
        'EM3GAQAR2',//DStv-Box office
        'ELOA1SA26',//DStv
        'ELOA1XKZ1',//GOtv
        'EWNHCYE11',//Topstar
    ];

    public static $electricityVouchers = [
        'ERM2VV456',//Zesco
    ];

    public array $serviceList = [
        'getVouchersWithUnits',
        'getAccountBalance',
        'getDistributionChannels'
    ];

    public function __construct($reference, $endpoint = null)
    {
        $this->reference = $reference;
        $this->username = config('cgrate.username');
        $this->password = config('cgrate.password');

        if ($endpoint != null)
            $this->endpoint = $endpoint;
    }


    private function sendBlankRequest($param): array
    {
        $requestBody = <<<REQUEST
            <soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:kon="http://konik.cgrate.com">
               <soapenv:Header>
                     <wsse:Security xmlns:wsse="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-secext-1.0.xsd" soapenv:mustUnderstand="1">
                        <wsse:UsernameToken xmlns:wsu="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-utility-1.0.xsd" wsu:Id="{$this->username}">
                           <wsse:Username>{$this->username}</wsse:Username>
                           <wsse:Password Type="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-username-token-profile-1.0#PasswordText">{$this->password}</wsse:Password>
                        </wsse:UsernameToken>
                     </wsse:Security>
                  </soapenv:Header>
               <soapenv:Body>

                    <ns2:{$param} xmlns:ns2="http://konik.cgrate.com"></ns2:{$param}>

               </soapenv:Body>
          </soapenv:Envelope>
REQUEST;

        $response = $this->sendRequest($requestBody);
        if ($response['errorCode'] != 0)
            throw new Exception($response['errorCode']);

        return $response;
    }

    /*
     * @param string $param - The name of the method to call | validateVoucherPurchase | purchaseVoucher
     * */
    public function processVoucher($param = 'purchaseVoucher', $distributionChannel, $isFixed, $recipient, $serviceProvider, $transactionReference, $voucherType, $voucherValue): array
    {
        $requestBody = <<<REQUEST
            <soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:kon="http://konik.cgrate.com">
               <soapenv:Header>
                     <wsse:Security xmlns:wsse="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-secext-1.0.xsd" soapenv:mustUnderstand="1">
                        <wsse:UsernameToken xmlns:wsu="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-utility-1.0.xsd" wsu:Id="{$this->username}">
                           <wsse:Username>{$this->username}</wsse:Username>
                           <wsse:Password Type="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-username-token-profile-1.0#PasswordText">{$this->password}</wsse:Password>
                        </wsse:UsernameToken>
                     </wsse:Security>
                  </soapenv:Header>
               <soapenv:Body>

                    <ns2:{$param} xmlns:ns2="http://konik.cgrate.com">
                         <Voucher>
                            <distributionChannel>{$distributionChannel}</distributionChannel>
                            <isFixed>{$isFixed}</isFixed>
                            <receipient>{$recipient}</receipient>
                            <serviceProvider>{$serviceProvider}</serviceProvider>
                            <transactionReference>{$transactionReference}</transactionReference>
                            <voucherType>{$voucherType}</voucherType>
                            <voucherValue>{$voucherValue}</voucherValue>
                         </Voucher>
                    </ns2:{$param}>

               </soapenv:Body>
          </soapenv:Envelope>
REQUEST;

        $response = $this->sendRequest($requestBody);
        if ($response['errorCode'] != 0)
            throw new Exception($response['errorCode']);

        return $response;
    }

    public function validateVoucherPurchase($distributionChannel, $isFixed, $receipient, $serviceProvider, $transactionReference, $voucherType, $voucherValue): array
    {
        return $this->processVoucher('validateVoucherPurchase', $distributionChannel, $isFixed, $receipient, $serviceProvider, $transactionReference, $voucherType, $voucherValue);
    }

    public function purchaseVoucher($distributionChannel, $isFixed, $receipient, $serviceProvider, $transactionReference, $voucherType, $voucherValue): array
    {

        $response = $this->processVoucher('purchaseVoucher', $distributionChannel, $isFixed, $receipient, $serviceProvider, $transactionReference, $voucherType, $voucherValue);
        if ($response['errorCode'] != 0)
            throw new Exception($response['errorCode']);

        $dom = new DOMDocument;
        $dom->loadXML($response['raw']);

        $responseCode = $dom->getElementsByTagName('responseCode')->item(0)->nodeValue;
        $responseMessage = $dom->getElementsByTagName('responseMessage')->item(0)->nodeValue;
        $purchaseId = $dom->getElementsByTagName('purchaseId')->item(0)->nodeValue ?? '';
        $voucherSerialNumber = $dom->getElementsByTagName('voucherSerialNumber')->item(0)->nodeValue ?? '';
        return [
            'errorCode' => $responseCode,
            'responseMessage' => $responseMessage,
            'purchaseId' => $purchaseId,
            'voucherSerialNumber' => $voucherSerialNumber
        ];
    }

    //queryTransactionStatus
    public function queryTransactionStatus($transactionReference): array
    {
        $requestBody = <<<REQUEST
            <soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:kon="http://konik.cgrate.com">
               <soapenv:Header>
                     <wsse:Security xmlns:wsse="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-secext-1.0.xsd" soapenv:mustUnderstand="1">
                        <wsse:UsernameToken xmlns:wsu="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-utility-1.0.xsd" wsu:Id="{$this->username}">
                           <wsse:Username>{$this->username}</wsse:Username>
                           <wsse:Password Type="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-username-token-profile-1.0#PasswordText">{$this->password}</wsse:Password>
                        </wsse:UsernameToken>
                     </wsse:Security>
                  </soapenv:Header>
               <soapenv:Body>

                  <ns2:queryTransactionStatus  xmlns:ns2="http://konik.cgrate.com">
                        <transactionReference>{$transactionReference}</transactionReference>
                    </ns2:queryTransactionStatus>
               </soapenv:Body>
          </soapenv:Envelope>
REQUEST;

        $response = $this->sendRequest($requestBody);
        if ($response['errorCode'] != 0)
            throw new Exception($response['errorCode']);

        $dom = new DOMDocument;
        $dom->loadXML($response['raw']);

        $responseCode = $dom->getElementsByTagName('responseCode')->item(0)->nodeValue;
        $responseMessage = $dom->getElementsByTagName('responseMessage')->item(0)->nodeValue;
        return [
            'errorCode' => $responseCode,
            'responseMessage' => $responseMessage,
        ];
    }

    public function getVouchersWithUnits(): array
    {
        return $this->sendBlankRequest('getVouchersWithUnits');
    }

    public function getAccountBalance(): array
    {
        return $this->sendBlankRequest('getAccountBalance');
    }

    public function getDistributionChannels(): array
    {
        return $this->sendBlankRequest('getDistributionChannels');
    }

    public function getVoucherDetails($voucherId): array
    {
        $requestBody = <<<REQUEST
                <soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:kon="http://konik.cgrate.com">
                <soapenv:Header>
                        <wsse:Security xmlns:wsse="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-secext-1.0.xsd" soapenv:mustUnderstand="1">
                            <wsse:UsernameToken xmlns:wsu="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-utility-1.0.xsd" wsu:Id="{$this->username}">
                            <wsse:Username>{$this->username}</wsse:Username>
                            <wsse:Password Type="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-username-token-profile-1.0#PasswordText">{$this->password}</wsse:Password>
                            </wsse:UsernameToken>
                        </wsse:Security>
                    </soapenv:Header>
                <soapenv:Body>

                    <ns2:getVoucherDetails  xmlns:ns2="http://konik.cgrate.com">
                            <voucherId>{$voucherId}</voucherId>
                        </ns2:getVoucherDetails>
                </soapenv:Body>
            </soapenv:Envelope>
REQUEST;
        $response = $this->sendRequest($requestBody);
        if ($response['errorCode'] != 0)
            throw new Exception($response['errorCode']);

        dd($response['dom']);
    }

    //getBillCustomerName
    public function getBillCustomerName($serviceProvider, $accountNumber, $is_clean_provider = false): array
    {
        if (!$is_clean_provider) {
            $myProvider = strtoupper($serviceProvider);
            switch ($myProvider) {
                case 'dstv':
                    $serviceProvider = 'DStv';
                    break;
                case 'GOtv':
                    $serviceProvider = 'GOtv';
                    break;
            }
        }
        $requestBody = <<<REQUEST
            <soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:kon="http://konik.cgrate.com">
               <soapenv:Header>
                     <wsse:Security xmlns:wsse="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-secext-1.0.xsd" soapenv:mustUnderstand="1">
                        <wsse:UsernameToken xmlns:wsu="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-utility-1.0.xsd" wsu:Id="{$this->username}">
                           <wsse:Username>{$this->username}</wsse:Username>
                           <wsse:Password Type="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-username-token-profile-1.0#PasswordText">{$this->password}</wsse:Password>
                        </wsse:UsernameToken>
                     </wsse:Security>
                  </soapenv:Header>
               <soapenv:Body>

                  <ns2:getBillCustomerName  xmlns:ns2="http://konik.cgrate.com">
                        <serviceProvider>{$serviceProvider}</serviceProvider>
                        <billPaymentAccountNumber>{$accountNumber}</billPaymentAccountNumber>
                    </ns2:getBillCustomerName>
               </soapenv:Body>
          </soapenv:Envelope>
REQUEST;

        $response = $this->sendRequest($requestBody);
        if ($response['errorCode'] != 0)
            throw new Exception($response['errorCode']);

        $dom = new DOMDocument;
        $dom->loadXML($response['raw']);

        $responseCode = $dom->getElementsByTagName('responseCode')->item(0)->nodeValue;
        $responseMessage = $dom->getElementsByTagName('responseMessage')->item(0)->nodeValue;
        $customerName = $dom->getElementsByTagName('billCustomerName')->item(0)->nodeValue ?? '';
        return [
            'errorCode' => $responseCode,
            'responseMessage' => $responseMessage . ' ' . $serviceProvider . ' ' . $accountNumber,
            'customerName' => $customerName
        ];
    }

    public function purchaseZescVoucher($reference, $recipient, float|int $voucherAmount)
    {

        $requestBody = <<<REQUEST
            <soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:kon="http://konik.cgrate.com">
               <soapenv:Header>
                     <wsse:Security xmlns:wsse="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-secext-1.0.xsd" soapenv:mustUnderstand="1">
                        <wsse:UsernameToken xmlns:wsu="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-utility-1.0.xsd" wsu:Id="{$this->username}">
                           <wsse:Username>{$this->username}</wsse:Username>
                           <wsse:Password Type="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-username-token-profile-1.0#PasswordText">{$this->password}</wsse:Password>
                        </wsse:UsernameToken>
                     </wsse:Security>
                  </soapenv:Header>
               <soapenv:Body>
                    <purchaseZescoVoucher xmlns="http://konik.cgrate.com">
                        <Voucher xmlns="">
                            <distributionChannel></distributionChannel>
                            <isFixed>false</isFixed>
                            <receipient>{$recipient}</receipient>
                            <serviceProvider>Zesco</serviceProvider>
                             <transactionReference>{$reference}</transactionReference>
                            <voucherType>Token</voucherType>
                            <voucherValue>{$voucherAmount}</voucherValue>
                        </Voucher>
                    </purchaseZescoVoucher>
               </soapenv:Body>
          </soapenv:Envelope>
REQUEST;

        $response = $this->sendRequest($requestBody);
        if ($response['errorCode'] != 0)
            throw new Exception($response['errorCode']);

        $dom = new DOMDocument;
        $dom->loadXML($response['raw']);

        $responseCode = $dom->getElementsByTagName('responseCode')->item(0)->nodeValue;
        $responseMessage = $dom->getElementsByTagName('responseMessage')->item(0)->nodeValue;
        $purchaseId = $dom->getElementsByTagName('purchaseId')->item(0)->nodeValue ?? '';
        $voucherSerialNumber = $dom->getElementsByTagName('voucherPinNumber')->item(0)->nodeValue ?? '';
        return [
            'errorCode' => $responseCode,
            'responseMessage' => $responseMessage,
            'purchaseId' => $purchaseId,
            'voucherSerialNumber' => $voucherSerialNumber
        ];
    }

    public function requestMomoPayment($reference, $mobile, float|int $amount, $serviceProvider)
    {

        $requestBody = <<<REQUEST
            <soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:kon="http://konik.cgrate.com">
               <soapenv:Header>
                     <wsse:Security xmlns:wsse="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-secext-1.0.xsd" soapenv:mustUnderstand="1">
                        <wsse:UsernameToken xmlns:wsu="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-utility-1.0.xsd" wsu:Id="{$this->username}">
                           <wsse:Username>{$this->username}</wsse:Username>
                           <wsse:Password Type="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-username-token-profile-1.0#PasswordText">{$this->password}</wsse:Password>
                        </wsse:UsernameToken>
                     </wsse:Security>
                  </soapenv:Header>
               <soapenv:Body>
                  <ns2:processCustomerPayment xmlns:ns2="http://konik.cgrate.com">
                    <transactionAmount>{$amount}</transactionAmount>
                    <customerMobile>{$mobile}</customerMobile>
                    <paymentReference>{$reference}</paymentReference>
                    <issuerName xmlns="">{$serviceProvider}</issuerName>
                </ns2:processCustomerPayment>
               </soapenv:Body>
          </soapenv:Envelope>
REQUEST;
        $response = $this->sendRequest($requestBody, true);
        if ($response['errorCode'] != 0)
            throw new Exception($response['errorCode']);

        return [
            'errorCode' => $response['errorCode'],
            'response' => $response['response'],
        ];
    }

    private function sendRequest($requestBody, $async = false): array
    {
        $request_time = date('Y-m-d H:i:s');
        $curlHandle = curl_init();
        $endpoint = $this->endpoint;
        $content_length = strlen($requestBody);

        $headers = [
            "Content-Type: text/xml; charset=\"utf-8\"",
            "Content-length: $content_length"
        ];

        curl_setopt($curlHandle, CURLOPT_URL, $endpoint);
        curl_setopt($curlHandle, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($curlHandle, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curlHandle, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curlHandle, CURLOPT_POSTFIELDS, $requestBody);

        if ($async) {
            // Fork a child process to handle the request asynchronously
            if (function_exists('pcntl_fork')) {
                $pid = pcntl_fork();
                if ($pid == -1) {
                    // Fork failed
                    return [
                        'errorCode' => 'Fork failed',
                        'response' => null
                    ];
                } elseif ($pid) {
                    // Parent process: immediately return and continue execution
                    return [
                        'errorCode' => 0,
                        'response' => 'Request sent asynchronously',
                        'raw' => null
                    ];
                } else {
                    // Child process: perform the cURL request
                    $response = curl_exec($curlHandle);
                    if (curl_errno($curlHandle)) {
                        curl_close($curlHandle);
                        exit(1); // Exit child process
                    }

                    curl_close($curlHandle);

                    $dom = new DOMDocument();
                    if (!@$dom->loadXML($response)) {
                        Helpers::logApiRequest($requestBody, $response, $request_time, date('Y-m-d H:i:s'), '', '', '', $this->reference, 'cGrate', 'FAILED', '');
                        exit(1); // Exit child process
                    }

                    if ($dom->getElementsByTagName('Fault')->length > 0) {
                        Helpers::logApiRequest($requestBody, $response, $request_time, date('Y-m-d H:i:s'), '', '', '', $this->reference, 'cGrate', 'FAILED', '');
                        exit(1); // Exit child process
                    }
                    Helpers::logApiRequest($requestBody, $response, $request_time, date('Y-m-d H:i:s'), '', '', '', $this->reference, 'cGrate', 'SUCCESS', '');
                    exit(0); // Exit child process
                }
            } else {
                throw new Exception('pcntl_fork() not available');
            }
        } else {
            // Synchronous request (normal behavior)
            try {
                $response = curl_exec($curlHandle);
                if (curl_errno($curlHandle)) {
                    throw new Exception(curl_error($curlHandle));
                }
            } catch (Exception $e) {
                return [
                    'errorCode' => $e->getMessage(),
                    'response' => null
                ];
            }

            curl_close($curlHandle);

            $dom = new DOMDocument();
            if (!@$dom->loadXML($response)) {
                Helpers::logApiRequest($requestBody, $response, $request_time, date('Y-m-d H:i:s'), '', '', '', $this->reference, 'cGrate', 'FAILED', '');
                return [
                    'errorCode' => 'Invalid XML response',
                    'response' => null
                ];
            }

            if ($dom->getElementsByTagName('Fault')->length > 0) {
                Helpers::logApiRequest($requestBody, $response, $request_time, date('Y-m-d H:i:s'), '', '', '', $this->reference, 'cGrate', 'FAILED', '');
                return [
                    'errorCode' => 'SOAP Fault: ' . $dom->getElementsByTagName('Fault')->item(0)->nodeValue,
                    'response' => null
                ];
            }
            Helpers::logApiRequest($requestBody, $response, $request_time, date('Y-m-d H:i:s'), '', '', '', $this->reference, 'cGrate', 'SUCCESS', '');
            return [
                'errorCode' => 0,
                'request' => $requestBody,
                'response' => $dom,
                'raw' => $response,

            ];
        }
    }

    public function queryCustomerPayment($reference): array
    {

        $requestBody = <<<REQUEST
            <soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:kon="http://konik.cgrate.com">
               <soapenv:Header>
                     <wsse:Security xmlns:wsse="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-secext-1.0.xsd" soapenv:mustUnderstand="1">
                        <wsse:UsernameToken xmlns:wsu="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-utility-1.0.xsd" wsu:Id="{$this->username}">
                           <wsse:Username>{$this->username}</wsse:Username>
                           <wsse:Password Type="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-username-token-profile-1.0#PasswordText">{$this->password}</wsse:Password>
                        </wsse:UsernameToken>
                     </wsse:Security>
                  </soapenv:Header>
               <soapenv:Body>
                <kon:queryCustomerPayment>
                    <paymentReference>{$reference}</paymentReference>
                </kon:queryCustomerPayment>
               </soapenv:Body>
          </soapenv:Envelope>
REQUEST;

        $response = $this->sendRequest($requestBody);

        if ($response['errorCode'] != 0)
            throw new Exception($response['errorCode']);

        $dom = new DOMDocument;
        $dom->loadXML($response['raw']);
        $responseCode = $dom->getElementsByTagName('responseCode')->item(0)->nodeValue;
        $responseMessage = $dom->getElementsByTagName('responseMessage')->item(0)->nodeValue;
        $paymentId = $dom->getElementsByTagName('paymentID')->item(0)->nodeValue ?? '';
        return [
            'errorCode' => $responseCode,
            'response' => $responseMessage,
            'paymentId' => $paymentId
        ];
    }
}



