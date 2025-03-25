<?php

namespace App\Integrations\Stubs;

class transferStatus
{
    const STATUS_SUCCESS = 'SUCCESS';
    const STATUS_FAILED = 'FAILED';

    public $status;
    public $reference;
    public $statusMessage;
}
