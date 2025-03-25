<?php

namespace App\Console\Commands;

use App\Integrations\Cybersource\CyberSourceConfiguration;
use CyberSource\ApiClient;
use CyberSource\Model\CheckPayerAuthEnrollmentRequest;
use CyberSource\Model\Ptsv2paymentsConsumerAuthenticationInformation;
use CyberSource\Model\Ptsv2paymentsOrderInformationBillTo;
use CyberSource\Model\Riskv1authenticationsBuyerInformation;
use CyberSource\Model\Riskv1authenticationsDeviceInformation;
use \Exception;
use Illuminate\Console\Command;
use GuzzleHttp\Client;
use CyberSource\ApiException;
use CyberSource\Configuration;
use CyberSource\Model\CreatePaymentRequest;
use CyberSource\Model\Ptsv2paymentsClientReferenceInformation;
use CyberSource\Model\Ptsv2paymentsPaymentInformation;
use CyberSource\Model\Ptsv2paymentsPaymentInformationCard;
use CyberSource\Model\Ptsv2paymentsProcessingInformation;
use CyberSource\Model\Ptsv2paymentsOrderInformation;
use CyberSource\Model\Ptsv2paymentsOrderInformationAmountDetails;
use CyberSource\Model\RiskV1AuthenticationsPost201Response;
use CyberSource\Api\PaymentsApi;
use CyberSource\Api\PayerAuthenticationApi;
use ReflectionClass;

class Test3DSTransaction extends Command
{
    protected $signature = '3ds';
    protected $description = 'Test 3DS transaction with Cyber Source';

    public function handle()
    {
        try {
            $this->info("Authenticating payer...");


//            $merchantId = "testrest";
//            $apiKeyId = "08c94330-f618-42a3-b09d-e1e43be5efda";
//            $secretKey = "yBJxy6LjM2TmcPGu+GaJrHtkke25fPpUX+UY6/L/1tE=";

            $merchantId = "abz_techpay_1199348_usd";
            $apiKeyId = "09d90b1c-fade-458f-a81e-bb4f70f9d6d3";
            $secretKey = "mGmfyVVgL/s9iFciCMr8gPQPVmsGlGcirXsf4p9A6Ds=";


// Usage example:
            try {

                $processor = new CyberSource3DSProcessor($merchantId, $apiKeyId, $secretKey);

                // Step 1: Check 3DS enrollment
                $enrollmentCheck = $processor->check3DSEnrollment(
                       '4000000000002503',  // Card number
                    '12',                // Expiration month
                      '2025',             // Expiration year
                    '100.00',           // Amount
                    'USD'               // Currency
                );

                if (isset($enrollmentCheck['requires_action'])) {
                    echo "\n\n" . json_encode($enrollmentCheck) . "\n\n";
                    // Redirect to ACS URL for 3DS authentication
                    // You'll need to implement a form that posts to the ACS URL with the pareq
                    echo "\nRedirect to: " . url('3ds') . "?pareq=" . $enrollmentCheck['pareq'] . "&acsUrl=" . $enrollmentCheck['acsUrl'] . "&authenticationTransactionId=" . $enrollmentCheck['authenticationTransactionId'];
                    echo "\nRedirect to: " . $enrollmentCheck['acsUrl'];
                    // Store authenticationTransactionId for later use
                    $this->ask('Press enter to continue');

                    // Process payment with authentication results
                    $paymentResult = $processor->processPayment(
                        $enrollmentCheck['authenticationTransactionId'],
                        '4000000000002503',  // Card number
                        '12',                // Expiration month
                        '2025',             // Expiration year
                        '100.00',           // Amount
                        'USD'               // Currency
                    );

                    if (isset($paymentResult['success'])) {
                        echo "\nPayment successful! Transaction ID: " . $paymentResult['transactionId'];
                    } else {
                        echo "\nPayment failed: " . $paymentResult['message'];
                    }

                } elseif (isset($enrollmentCheck['success'])) {
                    // Process payment with authentication results
                    $paymentResult = $processor->processPayment(
                        $enrollmentCheck['authenticationTransactionId'],
                        '4000000000002503',  // Card number
                        '12',                // Expiration month
                        '2025',             // Expiration year
                        '100.00',           // Amount
                        'USD'               // Currency
                    );

                    if (isset($paymentResult['success'])) {
                        echo "\nPayment successful! Transaction ID: " . $paymentResult['transactionId'];
                    } else {
                        echo "\nPayment failed: " . $paymentResult['message'];
                    }
                } else {
                    echo "\nEnrollment check failed: " . $enrollmentCheck['message'];
                }

            } catch (Exception $e) {
                echo "\nError: " . $e->getMessage() . "\n" . $e->getLine() . "\n" . $e->getFile() . "\n";
            }


            $this->info("\nPayer authenticated successfully");
        } catch (\Exception $e) {
            $this->error($e->getMessage());
        }
    }

}

class CyberSource3DSProcessor
{
    private $merchantId;
    private $apiKeyId;
    private $secretKey;
    private $apiClient;

    public function __construct($merchantId, $apiKeyId, $secretKey)
    {
        $this->merchantId = $merchantId;
        $this->apiKeyId = $apiKeyId;
        $this->secretKey = $secretKey;

        $config = new CyberSourceConfiguration($this->apiKeyId, $this->secretKey, $this->merchantId, 'apitest.cybersource.com');
        $config->setMerchantID($this->merchantId);
        $config->setApiKey($this->apiKeyId, $this->secretKey);

        $host_config = $config->ConnectionHost();
        $merchantConfig = $config->merchantConfigObject();
//        $apiClient = new ApiClient($host_config, $merchantConfig);
        $this->apiClient = new ApiClient($host_config, $merchantConfig);
    }

    public function check3DSEnrollment($cardNumber, $expirationMonth, $expirationYear, $amount, $currency)
    {
//        {
//                "orderInformation": {
//                "amountDetails": {
//                    "currency": "USD",
//          "totalAmount": "10.99"
//        },
//        "billTo": {
//                    "address1": "1 Market St",
//          "address2": "Address 2",
//          "administrativeArea": "CA",
//          "country": "US",
//          "locality": "san francisco",
//          "firstName": "John",
//          "lastName": "Doe",
//          "phoneNumber": "4158880000",
//          "email": "test@cybs.com",
//          "postalCode": "94105"
//        }
//      },
//      "paymentInformation": {
//                "card": {
//                    "type": "001",
//          "expirationMonth": "12",
//          "expirationYear": "2025",
//          "number": "4000000000002503"
//        }
//      },
//      "buyerInformation": {
//                "mobilePhone": "1245789632"
//      },
//      "deviceInformation": {
//                "ipAddress": "139.130.4.5",
//        "httpAcceptContent": "test",
//        "httpBrowserLanguage": "en_us",
//        "httpBrowserJavaEnabled": "N",
//        "httpBrowserJavaScriptEnabled": "Y",
//        "httpBrowserColorDepth": "24",
//        "httpBrowserScreenHeight": "100000",
//        "httpBrowserScreenWidth": "100000",
//        "httpBrowserTimeDifference": "300",
//        "userAgentBrowserValue": "GxKnLy8TFDUFxJP1t"
//      },
//      "consumerAuthenticationInformation": {
//                "deviceChannel": "BROWSER",
//        "transactionMode": "eCommerce"
//      }
//    }
        try {
            $payerAuthApi = new PayerAuthenticationApi($this->apiClient);

            // Create the authentication request
            $clientReferenceInformation = new Ptsv2paymentsClientReferenceInformation([
                'code' => uniqid('3DS_TEST_')
            ]);

            $card = new Ptsv2paymentsPaymentInformationCard([
                'number' => $cardNumber,
                'expirationMonth' => $expirationMonth,
                'expirationYear' => $expirationYear,
            ]);

            $paymentInformation = new Ptsv2paymentsPaymentInformation([
                'card' => $card
            ]);

            $amountDetails = new Ptsv2paymentsOrderInformationAmountDetails([
                'totalAmount' => $amount,
                'currency' => $currency
            ]);

            $orderInformation = new Ptsv2paymentsOrderInformation([
                'amountDetails' => $amountDetails
            ]);

            $consumerAuthenticationInformation = new \CyberSource\Model\Riskv1decisionsConsumerAuthenticationInformation([
                "deviceChannel" => "BROWSER",
                "transactionMode" => "eCommerce"
            ]);

            $deviceInformation = new Riskv1authenticationsDeviceInformation([
                "ipAddress" => request()->ip,
                "httpAcceptContent" => 'test',
                "httpBrowserLanguage" => 'en_us',
                "httpBrowserJavaEnabled" => 'N',
                "httpBrowserJavaScriptEnabled" => 'Y',
                "httpBrowserColorDepth" => '24',
                "httpBrowserScreenHeight" => '100000',
                "httpBrowserScreenWidth" => '100000',
                "httpBrowserTimeDifference" => '300',
                "userAgentBrowserValue" => request()->header('User-Agent'),

            ]);
            $checkEnrollmentRequest = new CheckPayerAuthEnrollmentRequest([
                'clientReferenceInformation' => $clientReferenceInformation,
                'paymentInformation' => $paymentInformation,
                'orderInformation' => $orderInformation,
                'consumerAuthenticationInformation' => $consumerAuthenticationInformation,
                'deviceInformation' => $deviceInformation
            ]);


            // Check enrollment
            $response = $payerAuthApi->checkPayerAuthEnrollment($checkEnrollmentRequest);

            return $this->handleEnrollmentResponse($response);

        } catch (ApiException $e) {
            return [
                'error' => true,
                'message' => $e->getMessage(),
                'details' => $e->getResponseBody()
            ];
        }
    }

    private function handleEnrollmentResponse($response_obj)
    {
        $response = json_decode(json_encode(json_decode($response_obj[0])), true);

        if ($response['status'] === 'AUTHENTICATION_SUCCESSFUL') {
            return [
                'success' => true,
                'authenticationTransactionId' => $response['id'],
                'authenticationStatus' => $response['status']
            ];
        } elseif ($response['status'] === 'PENDING_AUTHENTICATION') {

            return [
                'requires_action' => true,
                'acsUrl' => $response['consumerAuthenticationInformation']['acsUrl'],
                'pareq' => $response['consumerAuthenticationInformation']['pareq'],
                'authenticationTransactionId' => $response['id']
            ];
        } else {
            return [
                'error' => true,
                'message' => 'Card not enrolled or authentication failed',
                'status' => $response['status']
            ];
        }
    }

    public function processPayment($authenticationTransactionId, $cardNumber, $expirationMonth, $expirationYear, $amount, $currency)
    {
        try {
            $paymentsApi = new PaymentsApi($this->apiClient);

            // Create the payment request
            $clientReferenceInformation = new Ptsv2paymentsClientReferenceInformation([
                'code' => uniqid('PAYMENT_')
            ]);


            $processingInformation = new Ptsv2paymentsProcessingInformation([
                'commerceIndicator' => 'internet',
                'payerAuthenticationTransaction' => true,
                'actionList' => [
                    'VALIDATE_CONSUMER_AUTHENTICATION'
                ]
            ]);

            $card = new Ptsv2paymentsPaymentInformationCard([
                'number' => $cardNumber,
                'expirationMonth' => $expirationMonth,
                'expirationYear' => $expirationYear
            ]);

            $paymentInformation = new Ptsv2paymentsPaymentInformation([
                'card' => $card
            ]);

            $amountDetails = new Ptsv2paymentsOrderInformationAmountDetails([
                'totalAmount' => $amount,
                'currency' => $currency
            ]);

            $orderInformationBillToArr = [
                "address1" => "1 Market St",
                "address2" => "Address 2",
                "administrativeArea" => "CA",
                "country" => "US",
                "locality" => "san francisco",
                "firstName" => "John",
                "lastName" => "Doe",
                "phoneNumber" => "4158880000",
                "email" => "test@cybs.com",
                "postalCode" => "94105"
            ];
            $orderInformationBillTo = new Ptsv2paymentsOrderInformationBillTo($orderInformationBillToArr);

            $orderInformation = new Ptsv2paymentsOrderInformation([
                'amountDetails' => $amountDetails,
                "billTo" => $orderInformationBillTo
            ]);


            $consumerAuthenticationInformationArr = [
                "authenticationTransactionId" => $authenticationTransactionId
            ];
            $consumerAuthenticationInformation = new Ptsv2paymentsConsumerAuthenticationInformation($consumerAuthenticationInformationArr);


            $billTo = new Riskv1authenticationsBuyerInformation([
                'firstName' => 'John',
                'lastName' => 'Doe',
                'address1' => '1 Market St',
                'locality' => 'san francisco',
                'administrativeArea' => 'CA',
                'postalCode' => '94105',
                'country' => 'US',
                'email' => 'test@techpay.co.zm',
                'phoneNumber' => '4158880000',
            ]);


            $paymentRequest = new CreatePaymentRequest([
                'clientReferenceInformation' => $clientReferenceInformation,
                'processingInformation' => $processingInformation,
                'paymentInformation' => $paymentInformation,
                'orderInformation' => $orderInformation,
                "consumerAuthenticationInformation" => $consumerAuthenticationInformation,

                "billTo" => $billTo
            ]);

            // Process the payment
            $response = $paymentsApi->createPayment($paymentRequest);

            return $this->handlePaymentResponse($response);

        } catch (ApiException $e) {
            return [
                'error' => true,
                'message' => $e->getMessage(),
                'details' => $e->getResponseBody()
            ];
        }
    }

    private function handlePaymentResponse($response_obj)
    {
        $response = json_decode(json_encode(json_decode($response_obj[0])), true);

        if ($response['status'] === 'AUTHORIZED') {
            return [
                'success' => true,
                'transactionId' => $response['id'],
                'authorizationCode' => $response['processorInformation']['approvalCode']
            ];
        } else {
            return [
                'error' => true,
                'message' => 'Payment not authorized',
                'status' => $response['status'],
                'reason' => $response['errorInformation']['reason'] ?? 'Unknown error'
            ];
        }
    }
}


