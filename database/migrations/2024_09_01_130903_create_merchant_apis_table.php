<?php

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
        Schema::create('merchant_apis', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(\App\Models\Merchants::class, 'merchant_id');
            $table->string('api_key');
            $table->string('api_secret');
            $table->string('api_type');
            $table->string('return_url')->nullable();
            $table->string('callback_url')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->string('status')->default(\App\Common\GeneralStatus::STATUS_ACTIVE);
            $table->foreignIdFor(\App\Models\User::class, 'created_by');
            $table->foreignIdFor(\App\Models\User::class, 'updated_by')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('merchant_apis');
    }
};
