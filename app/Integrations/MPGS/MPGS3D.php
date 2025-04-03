<?php

namespace App\Integrations\MPGS;

use App\Models\PaymentProviders;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

class MPGS3D
{
    private $apiUrl;
    private $apiKey;
    private $apiSecret;
    private $apiVersion = '100'; // Using the latest API version for 3DS support

    const TECHPAY_CODE = 'TECHPAY_MPGS';

    public function __construct(PaymentProviders $provider)
    {
        $this->apiUrl = $provider->api_url;
        $this->apiKey = $provider->api_key_id;
        $this->apiSecret = $provider->api_key_secret;
    }

    /**
     * Generate a unique order ID
     *
     * @return string
     */
    public function generateOrderId()
    {
        return time() . rand(1000, 9999);
    }

    /**
     * Get the API endpoint URL
     *
     * @param string $orderId
     * @param string $transactionId
     * @return string
     */
    private function getEndpoint($orderId, $transactionId = '1')
    {
        return sprintf(
            '%s/api/rest/version/%s/merchant/%s/order/%s/transaction/%s',
            rtrim($this->apiUrl, '/'),
            $this->apiVersion,
            $this->apiKey,
            $orderId,
            $transactionId
        );
    }

    /**
     * Get the API headers
     *
     * @return array
     */
    private function getHeaders()
    {
        return [
            'Content-Type: application/json',
            'Authorization: Basic ' . base64_encode('merchant.' . $this->apiKey . ':' . $this->apiSecret),
            'Accept: application/json',
            'User-Agent: MPGS3D/1.0'
        ];
    }

    /**
     * Initiate 3DS authentication
     *
     * @param string $cardNumber
     * @param string $currency
     * @param string|null $orderId
     * @return array
     * @throws \Exception
     */
    public function initiateAuthentication($cardNumber, $currency, $orderId = null)
    {
        try {
            Log::info('Initiating 3DS authentication', [
                'cardNumber' => substr($cardNumber, -4),
                'currency' => $currency
            ]);
            
            $orderId = $orderId ?: $this->generateOrderId();
            $endpoint = $this->getEndpoint($orderId);
            
            $payload = [
                'apiOperation' => 'INITIATE_AUTHENTICATION',
                'authentication' => [
                    'channel' => 'PAYER_BROWSER'
                ],
                'order' => [
                    'currency' => $currency
                ],
                'sourceOfFunds' => [
                    'provided' => [
                        'card' => [
                            'number' => $cardNumber
                        ]
                    ]
                ]
            ];
            
            Log::debug('API Request', [
                'url' => $endpoint,
                'method' => 'PUT',
                'payload' => $payload
            ]);
            
            $ch = curl_init($endpoint);
            curl_setopt_array($ch, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_CUSTOMREQUEST => 'PUT',
                CURLOPT_POSTFIELDS => json_encode($payload),
                CURLOPT_HTTPHEADER => $this->getHeaders(),
                CURLOPT_SSL_VERIFYPEER => false, // Disable SSL verification for test gateway
                CURLOPT_SSL_VERIFYHOST => 0, // Disable host verification for test gateway
                CURLOPT_VERBOSE => true,
                CURLOPT_HEADER => true,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_CONNECTTIMEOUT => 10
            ]);

            // Create a temporary file handle for CURL to write verbose info
            $verbose = fopen('php://temp', 'w+');
            curl_setopt($ch, CURLOPT_STDERR, $verbose);
            
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            
            if ($error = curl_error($ch)) {
                // Get verbose debug information
                rewind($verbose);
                $verboseLog = stream_get_contents($verbose);
                fclose($verbose);
                
                Log::error('CURL Error', [
                    'error' => $error,
                    'endpoint' => $endpoint,
                    'verbose' => $verboseLog,
                    'info' => curl_getinfo($ch)
                ]);
                throw new \Exception("CURL Error: " . $error);
            }
            
            // Parse headers and body
            $headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
            $headers = substr($response, 0, $headerSize);
            $body = substr($response, $headerSize);
            
            curl_close($ch);
            fclose($verbose);
            
            Log::debug('Response Headers', ['headers' => $headers]);
            
            $data = json_decode($body, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                Log::error('JSON Decode Error', [
                    'error' => json_last_error_msg(),
                    'body' => $body
                ]);
                throw new \Exception('Invalid JSON response: ' . json_last_error_msg());
            }
            
            Log::debug('API Response', [
                'url' => $endpoint,
                'httpCode' => $httpCode,
                'response' => $data
            ]);
            
            if ($httpCode >= 200 && $httpCode < 300) {
                if (isset($data['result']) && $data['result'] === 'SUCCESS') {
                    if (isset($data['order']['authenticationStatus']) && 
                        $data['order']['authenticationStatus'] === 'AUTHENTICATION_AVAILABLE') {
                        Log::info('Authentication initiated successfully', [
                            'orderId' => $orderId,
                            'transactionId' => $data['transaction']['id'] ?? '1'
                        ]);
                        return $data;
                    } else {
                        throw new \Exception('Authentication not available for this card');
                    }
                } else {
                    throw new \Exception(
                        isset($data['error']['explanation']) 
                            ? $data['error']['explanation'] 
                            : 'Authentication initiation failed'
                    );
                }
            } else {
                throw new \Exception(
                    isset($data['error']['explanation']) 
                        ? $data['error']['explanation'] 
                        : 'HTTP Error: ' . $httpCode
                );
            }
            
        } catch (\Exception $e) {
            Log::error('Failed to initiate authentication', ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    /**
     * Authenticate the payer
     *
     * @param string $orderId
     * @param string $transactionId
     * @param array $cardData
     * @param float $amount
     * @param string $currency
     * @param array $browserData
     * @param string $redirectUrl
     * @return array
     * @throws \Exception
     */
    public function authenticatePayer($orderId, $transactionId, $cardData, $amount, $currency, $browserData, $redirectUrl)
    {
        try {
            Log::info('Authenticating payer', [
                'orderId' => $orderId,
                'transactionId' => $transactionId,
                'cardNumber' => substr($cardData['number'], -4),
                'currency' => $currency
            ]);

            $endpoint = $this->getEndpoint($orderId, $transactionId);
            
            $payload = [
                'apiOperation' => 'AUTHENTICATE_PAYER',
                'sourceOfFunds' => [
                    'provided' => [
                        'card' => [
                            'number' => $cardData['number'],
                            'expiry' => [
                                'month' => $cardData['expiry']['month'],
                                'year' => $cardData['expiry']['year']
                            ]
                        ]
                    ]
                ],
                'authentication' => [
                    'redirectResponseUrl' => $redirectUrl
                ],
                'order' => [
                    'amount' => strval($amount),
                    'currency' => $currency,
                    'custom' => [
                        'orderId' => $orderId,
                        'transactionId' => $transactionId
                    ]
                ],
                'device' => [
                    'browser' => 'MOZILLA',
                    'browserDetails' => [
                        '3DSecureChallengeWindowSize' => 'FULL_SCREEN',
                        'acceptHeaders' => 'application/json',
                        'colorDepth' => $browserData['colorDepth'] ?? 24,
                        'javaEnabled' => $browserData['javaEnabled'] ?? true,
                        'language' => $browserData['language'] ?? 'en-US',
                        'screenHeight' => $browserData['screenHeight'] ?? 640,
                        'screenWidth' => $browserData['screenWidth'] ?? 480,
                        'timeZone' => $browserData['timeZone'] ?? 273
                    ],
                    'ipAddress' => $browserData['ipAddress'] ?? '127.0.0.1'
                ]
            ];

            Log::debug('API Request', [
                'url' => $endpoint,
                'method' => 'PUT',
                'payload' => $payload
            ]);
            
            $ch = curl_init($endpoint);
            curl_setopt_array($ch, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_CUSTOMREQUEST => 'PUT',
                CURLOPT_POSTFIELDS => json_encode($payload),
                CURLOPT_HTTPHEADER => $this->getHeaders(),
                CURLOPT_SSL_VERIFYPEER => false, // Disable SSL verification for test gateway
                CURLOPT_SSL_VERIFYHOST => 0, // Disable host verification for test gateway
                CURLOPT_VERBOSE => true,
                CURLOPT_HEADER => true,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_CONNECTTIMEOUT => 10
            ]);

            // Create a temporary file handle for CURL to write verbose info
            $verbose = fopen('php://temp', 'w+');
            curl_setopt($ch, CURLOPT_STDERR, $verbose);
            
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            
            if ($error = curl_error($ch)) {
                // Get verbose debug information
                rewind($verbose);
                $verboseLog = stream_get_contents($verbose);
                fclose($verbose);
                
                Log::error('CURL Error', [
                    'error' => $error,
                    'endpoint' => $endpoint,
                    'verbose' => $verboseLog,
                    'info' => curl_getinfo($ch)
                ]);
                throw new \Exception("CURL Error: " . $error);
            }
            
            // Parse headers and body
            $headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
            $headers = substr($response, 0, $headerSize);
            $body = substr($response, $headerSize);
            
            curl_close($ch);
            fclose($verbose);
            
            Log::debug('Response Headers', ['headers' => $headers]);
            
            $data = json_decode($body, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                Log::error('JSON Decode Error', [
                    'error' => json_last_error_msg(),
                    'body' => $body
                ]);
                throw new \Exception('Invalid JSON response: ' . json_last_error_msg());
            }
            
            Log::debug('API Response', [
                'url' => $endpoint,
                'httpCode' => $httpCode,
                'response' => $data
            ]);
            
            if ($httpCode >= 200 && $httpCode < 300) {
                $recommendation = $data['response']['gatewayRecommendation'] ?? 'UNKNOWN';
                Log::info('Authentication request processed', [
                    'orderId' => $orderId,
                    'transactionId' => $transactionId,
                    'recommendation' => $recommendation
                ]);

                if ($recommendation === 'PROCEED') {
                    if (isset($data['authentication']['redirect']['html'])) {
                        Log::info('Challenge flow initiated', ['orderId' => $orderId]);
                    } else {
                        Log::info('Frictionless flow completed', ['orderId' => $orderId]);
                        // For frictionless flow, we'll initiate payment in the controller
                    }
                } else if ($recommendation === 'DO_NOT_PROCEED') {
                    throw new \Exception('Authentication declined');
                }
                
                return $data;
            } else {
                throw new \Exception(
                    isset($data['error']['explanation']) 
                        ? $data['error']['explanation'] 
                        : 'Authentication failed with HTTP ' . $httpCode
                );
            }
            
        } catch (\Exception $e) {
            Log::error('Failed to authenticate payer', ['error' => $e->getMessage()]);
            throw $e;
        }
    }
    
    /**
     * Initiate payment after successful authentication
     *
     * @param string $orderId
     * @param string $transactionId
     * @param array $cardData
     * @param float $amount
     * @param string $currency
     * @return array
     * @throws \Exception
     */
    public function initiatePayment($orderId, $transactionId, $cardData, $amount, $currency)
    {
        try {
            Log::info('Initiating payment', [
                'orderId' => $orderId,
                'transactionId' => $transactionId,
                'cardNumber' => substr($cardData['number'], -4),
                'amount' => $amount,
                'currency' => $currency
            ]);
            
            // Create a new transaction ID for the payment
            $paymentTransactionId = $transactionId . '_pay';
            $endpoint = $this->getEndpoint($orderId, $paymentTransactionId);
            
            $payload = [
                'apiOperation' => 'PAY',
                'order' => [
                    'amount' => strval($amount),
                    'currency' => $currency
                ],
                'sourceOfFunds' => [
                    'provided' => [
                        'card' => [
                            'number' => $cardData['number'],
                            'expiry' => [
                                'month' => $cardData['expiry']['month'],
                                'year' => $cardData['expiry']['year']
                            ]
                        ]
                    ],
                    'type' => 'CARD'
                ],
                'transaction' => [
                    'source' => 'INTERNET'
                ],
                'authentication' => [
                    'transactionId' => $transactionId
                ]
            ];
            
            Log::debug('API Request', [
                'url' => $endpoint,
                'method' => 'PUT',
                'payload' => $payload
            ]);
            
            $ch = curl_init($endpoint);
            curl_setopt_array($ch, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_CUSTOMREQUEST => 'PUT',
                CURLOPT_POSTFIELDS => json_encode($payload),
                CURLOPT_HTTPHEADER => $this->getHeaders(),
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_SSL_VERIFYHOST => 0,
                CURLOPT_VERBOSE => true,
                CURLOPT_HEADER => true,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_CONNECTTIMEOUT => 10
            ]);
            
            $verbose = fopen('php://temp', 'w+');
            curl_setopt($ch, CURLOPT_STDERR, $verbose);
            
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            
            if ($error = curl_error($ch)) {
                rewind($verbose);
                $verboseLog = stream_get_contents($verbose);
                fclose($verbose);
                
                Log::error('CURL Error in payment', [
                    'error' => $error,
                    'endpoint' => $endpoint,
                    'verbose' => $verboseLog,
                    'info' => curl_getinfo($ch)
                ]);
                throw new \Exception("CURL Error in payment: " . $error);
            }
            
            $headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
            $headers = substr($response, 0, $headerSize);
            $body = substr($response, $headerSize);
            
            curl_close($ch);
            fclose($verbose);
            
            Log::debug('Payment Response Headers', ['headers' => $headers]);
            
            $data = json_decode($body, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                Log::error('JSON Decode Error in payment', [
                    'error' => json_last_error_msg(),
                    'body' => $body
                ]);
                throw new \Exception('Invalid JSON response in payment: ' . json_last_error_msg());
            }
            
            Log::debug('API Response', [
                'url' => $endpoint,
                'httpCode' => $httpCode,
                'response' => $data
            ]);
            
            if ($httpCode >= 200 && $httpCode < 300) {
                if (isset($data['result']) && $data['result'] === 'SUCCESS') {
                    Log::info('Payment successful', [
                        'orderId' => $orderId,
                        'transactionId' => $paymentTransactionId,
                        'amount' => $amount,
                        'currency' => $currency
                    ]);
                    return $data;
                } else {
                    throw new \Exception(
                        isset($data['response']['gatewayCode']) 
                            ? 'Payment failed: ' . $data['response']['gatewayCode']
                            : 'Payment failed'
                    );
                }
            } else {
                throw new \Exception(
                    isset($data['error']['explanation']) 
                        ? $data['error']['explanation'] 
                        : 'Payment failed with HTTP ' . $httpCode
                );
            }
            
        } catch (\Exception $e) {
            Log::error('Failed to initiate payment', ['error' => $e->getMessage()]);
            throw $e;
        }
    }
}
