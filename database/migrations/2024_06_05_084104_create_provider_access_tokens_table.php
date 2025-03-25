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
        Schema::create('provider_access_tokens', function (Blueprint $table) {
            $table->id();
            $table->string('token');
            $table->string('provider_code');
            $table->json('details');
            $table->integer('usage')->default(0);
            $table->foreignIdFor(\App\Models\PaymentProviders::class);
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('provider_access_tokens');
    }
};
