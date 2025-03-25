<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AllTransactions extends Model
{
    public function merchants(): BelongsTo
    {
        return $this->belongsTo(Merchants::class, 'merchant', 'code');
    }

    use HasFactory;

    protected $fillable = [
        'txn_id',
        'source',
        'merchant',
        'result',
        'order_currency',
        'txn_date',
        'order_id',
        'card_number',
        'card_expiry_month',
        'card_expiry_year',
        'txn_amount',
        'txn_currency',
        'txn_type',
        'txn_acquirer_id',
        'response_acquirer_code',
        'submit_time_utc',
        'application_name',
        'reason_code',
        'r_code',
        'r_flag',
        'reconciliation_id',
        'r_message',
        'return_code',
        'client_reference_code',
        'eci_raw',
        'bill_to_address1',
        'bill_to_state',
        'bill_to_city',
        'bill_to_country',
        'bill_to_postal_code',
        'bill_to_email',
        'bill_to_phone_number',
        'bill_to_first_name',
        'bill_to_last_name',
        'amount_details_total_amount',
        'amount_details_currency',
        'payment_type',
        'payment_method',
        'card_suffix',
        'card_prefix',
        'card_type',
        'commerce_indicator',
        'processor_name',
        'approval_code',
        'terminal_id',
        'raw_data',
        'status'
    ];

    protected $casts = [
        'txn_date' => 'datetime',
        'submit_time_utc' => 'datetime',
        'raw_data' => 'array'
    ];
}
