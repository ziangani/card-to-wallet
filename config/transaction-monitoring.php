<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Transaction Monitoring Settings
    |--------------------------------------------------------------------------
    |
    | Configuration for the transaction monitoring system including thresholds
    | for different currencies and monitoring parameters.
    |
    */

    // Large transaction thresholds by currency
    'large_amount_thresholds' => [
        'ZMW' => 50000,
        'USD' => 5000,
        'EUR' => 4500,
        'GBP' => 4000,
    ],

    // Maximum allowed transactions per card per day
    'same_card_max_daily' => 3,

    // Time window (in minutes) to look for split transactions
    'split_transaction_time_window' => 30,

    // Minimum number of transactions to consider as split
    'split_transaction_min_count' => 3,

    // Email recipients for suspicious activity reports
    'report_recipients' => [
        'EddieMuyeba@techmasters.co.zm',
        'mweemba@techmasters.co.zm',
        'Andrewmbewe@techpay.co.zm',
        'charles@techpay.co.zm',
        'choolwe@techpay.co.zm',
        'chinedukoggu@techmasters.co.zm'
    ],
];
