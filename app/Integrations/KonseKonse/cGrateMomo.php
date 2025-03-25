<?php

namespace App\Integrations\KonseKonse;

use App\Common\Helpers;
use App\Integrations\Stubs\transactionStatus;
use App\Integrations\Stubs\Transfers;
use App\Integrations\Stubs\transferStatus;
use App\Models\PaymentProviders;
use GuzzleHttp\Client;

class cGrateMomo extends Transfers
{
    public static function initiateTransfer(PaymentProviders $provider, string $reference, string $uuid, float $amount, string $from, string $to): transferStatus
    {
        try {
            $client = new cGrate($reference);

            //test service first
            $test = $client->getAccountBalance();
            if ($test['errorCode'] != 0)
                throw new \Exception('Service provider could not be contacted at this time: ' . $test['response']);

            $serviceProvider = Helpers::getServiceProvider($from);
            $mobile = (strlen($from) == 10) ? '26' . $from : $from;
            $r = $client->requestMomoPayment($reference, $mobile, $amount, $serviceProvider);

            if ($r['errorCode'] != 0)
                throw new \Exception($r['response']);

            $status = new transferStatus();
            $status->status = transferStatus::STATUS_SUCCESS;
            $status->reference = $reference;
            $status->statusMessage = $r['response'];
            return $status;
        } catch (\Exception $e) {
            throw new \Exception('The Mobile Money system could not be contacted at this time: ' . $e->getMessage());
        }

    }

    public static function getStatus(PaymentProviders $provider, string $external_reference): transactionStatus
    {
        try {
            $client = new cGrate($external_reference);
            $result = $client->queryCustomerPayment($external_reference);

            $status = new transactionStatus();
            $status->status = ($result['errorCode'] == 0) ? transactionStatus::STATUS_SUCCESS : transactionStatus::STATUS_FAILED;
            $status->reference = $result['paymentId'] ?? '';
            $status->secondayReference = '';
            $status->statusMessage = $result['response'];
            $status->rawResponse = $result;
            return $status;
        } catch (\Exception $e) {
            throw new \Exception('The Mobile Money system could not be contacted at this time: ' . $e->getMessage());
        }
    }
}
