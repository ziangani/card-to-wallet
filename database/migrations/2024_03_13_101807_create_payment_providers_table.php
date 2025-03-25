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
        Schema::create('payment_providers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('status')->default(\App\Common\GeneralStatus::STATUS_ACTIVE);
            $table->string('code');

            $table->string('api_key_id')->nullable();
            $table->string('api_key_secret')->nullable();

            $table->string('api_url')->nullable();
            $table->string('api_token')->nullable();
            $table->string('callback_url')->nullable();
            $table->enum('environment', ['sandbox', 'production']);
            $table->json('details')->nullable();

            $table->unique(['code', 'environment']);
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_providers');
    }
};
