<?php

namespace App\Console\Commands;

use App\Integrations\KonseKonse\cGrate;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class Debug extends Command
{
    protected $signature = 'debug';
    protected $description = 'Debug merchant reconciliation data';

    public function handle()
    {
        $reference = 'BC'.rand(1000000000, 9999999999);
        $paymentReference = 'l';
        $client = new cGrate($reference);
        $test = $client->queryCustomerPayment($paymentReference);
        print_r($test);
    }
}
