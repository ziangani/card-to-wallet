<?php

namespace App\Integrations\Stubs;

use App\Models\PaymentProviders;

abstract class Transfers
{
    /**
     * Initiate a transfer
     *
     * @param string $reference
     * @param float $amount
     * @param string $from
     * @param string $to
     * @return string
     */
    abstract public static function initiateTransfer(PaymentProviders $provider, string $reference, string $uuid, float $amount, string $from, string $to): transferStatus;

    /**
     * Get the status of a transfer
     *
     * @param string $external_reference
     * @return array
     */
    abstract public static function getStatus(PaymentProviders $provider, string $external_reference): transactionStatus;
}
