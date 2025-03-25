<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('chargebacks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('transaction_id')->constrained('all_transactions');
            $table->string('tran_reason')->nullable();
            $table->string('approval_code');
            $table->dateTime('orig_time')->nullable();
            $table->string('acquirer_id')->nullable();
            $table->string('term_name')->nullable();
            $table->string('card_acceptor_id')->nullable();
            $table->string('merchant_location')->nullable();
            $table->string('merchant_city')->nullable();
            $table->string('condition_code')->nullable();
            $table->decimal('orig_clear_amount', 10, 2)->nullable();
            $table->string('orig_clear_currency', 3)->nullable();
            $table->decimal('original_amount', 10, 2)->nullable();
            $table->string('original_currency', 3)->nullable();
            $table->string('merchant_title')->nullable();
            $table->string('tran_type_desc')->nullable();
            $table->string('tran_code_desc')->nullable();
            $table->string('pan')->nullable(); // Card number
            $table->string('arn')->nullable(); // Acquirer Reference Number
            $table->string('rrn'); // Reference Retrieval Number
            $table->dateTime('orig_clear_date')->nullable();
            $table->dateTime('orig_settle_schedule')->nullable();
            $table->string('status')->default('pending');
            $table->text('text_message')->nullable();
            $table->json('raw_data')->nullable();
            $table->timestamps();

            // Add indexes for better query performance
            $table->index('approval_code');
            $table->index('rrn');
            $table->index('arn');
            $table->index('card_acceptor_id');
            $table->index('status');
            $table->index('orig_time');
            $table->index('orig_clear_date');
        });
    }

    public function down()
    {
        Schema::dropIfExists('chargebacks');
    }
};
