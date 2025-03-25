<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('all_transactions', function (Blueprint $table) {
            $table->id();
            $table->string('txn_id');
            $table->string('source')->nullable();
            $table->string('merchant')->nullable();
            $table->string('result')->nullable();
            $table->string('order_currency')->nullable();
            $table->timestamp('txn_date')->nullable();
            $table->string('order_id')->nullable();
            $table->string('card_number')->nullable();
            $table->string('card_expiry_month')->nullable();
            $table->string('card_expiry_year')->nullable();
            $table->decimal('txn_amount', 15, 2)->nullable();
            $table->string('txn_currency')->nullable();
            $table->string('txn_type')->nullable();
            $table->string('txn_acquirer_id')->nullable();
            $table->string('response_acquirer_code')->nullable();
            $table->timestamp('submit_time_utc')->nullable();
            $table->string('application_name')->nullable();
            $table->string('reason_code')->nullable();
            $table->string('r_code')->nullable();
            $table->string('r_flag')->nullable();
            $table->string('reconciliation_id')->nullable();
            $table->string('r_message')->nullable();
            $table->string('return_code')->nullable();
            $table->string('client_reference_code')->nullable();
            $table->string('eci_raw')->nullable();
            $table->string('bill_to_address1')->nullable();
            $table->string('bill_to_state')->nullable();
            $table->string('bill_to_city')->nullable();
            $table->string('bill_to_country')->nullable();
            $table->string('bill_to_postal_code')->nullable();
            $table->string('bill_to_email')->nullable();
            $table->string('bill_to_phone_number')->nullable();
            $table->string('bill_to_first_name')->nullable();
            $table->string('bill_to_last_name')->nullable();
            $table->decimal('amount_details_total_amount', 15, 2)->nullable();
            $table->string('amount_details_currency')->nullable();
            $table->string('payment_type')->nullable();
            $table->string('payment_method')->nullable();
            $table->string('card_suffix')->nullable();
            $table->string('card_prefix')->nullable();
            $table->string('card_type')->nullable();
            $table->string('commerce_indicator')->nullable();
            $table->string('commerce_indicator_label')->nullable();
            $table->string('processor_name')->nullable();
            $table->string('approval_code')->nullable();
            $table->string('terminal_id')->nullable();
            $table->json('raw_data')->nullable();
            $table->string('status')->default('PROCESSED');
            $table->unique(['txn_id','order_id', 'source', 'card_number','result', 'txn_type','txn_date', 'txn_amount', 'response_acquirer_code']);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('all_transactions');
    }
};
