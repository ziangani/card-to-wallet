<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up(): void
    {
        Schema::create('payment_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(\App\Models\Merchants::class, 'merchant_id');
            $table->foreignIdFor(\App\Models\MerchantApis::class, 'merchant_api_id');
            $table->string('reference')->unique();
            $table->string('request_type');
            $table->string('token');
            $table->decimal('amount', 10, 2);
            $table->string('description');
            $table->string('channel')->nullable();
            $table->string('order_number')->nullable();
            $table->string('status')->default(\App\Common\GeneralStatus::STATUS_PENDING);
            $table->string('return_url')->nullable();
            $table->string('callback_url')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_requests');
    }
};
