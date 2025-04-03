<?php

use App\Models\Merchants;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->string('uuid')->unique();
            $table->string('merchant_reference')->unique();
            $table->string('status')->default('PENDING');
            $table->string('currency')->default('ZMW');
            $table->decimal('amount', 10, 2);
            $table->string('merchant_code');
            $table->string('merchant_settlement_status')->default('PENDING');
            $table->date('merchant_settlement_date')->nullable();
            $table->foreignIdFor(\App\Models\PaymentProviders::class)->nullable();
            $table->foreignIdFor(\App\Models\User::class)->nullable();
            $table->string('provider_name')->nullable();
            $table->string('provider_push_status')->nullable();
            $table->string('provider_external_reference')->nullable();
            $table->string('provider_status_description')->nullable();
            $table->string('provider_payment_reference')->nullable();
            $table->dateTime('provider_payment_confirmation_date')->nullable();
            $table->date('provider_payment_date')->nullable();
            $table->string('payment_channel')->nullable();
            $table->string('callback')->nullable();
            $table->string('reference_1')->nullable();
            $table->string('reference_2')->nullable();
            $table->string('reference_3')->nullable();
            $table->string('reference_4')->nullable();
            $table->integer('retries')->default(0);
            $table->dateTime('last_retry_date')->nullable();
            $table->string('reversal_status')->default('N/A');
            $table->string('reversal_reason')->nullable();
            $table->string('reversal_reference')->nullable();
            $table->dateTime('reversal_date')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
