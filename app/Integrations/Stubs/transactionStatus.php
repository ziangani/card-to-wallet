<?php

namespace App\Integrations\Stubs;

class transactionStatus
{
    const STATUS_SUCCESS = 'SUCCESS';
    const STATUS_FAILED = 'FAILED';

    public $status;
    public $reference;
    public $secondayReference;
    public $statusMessage;
    public $rawResponse;
}
