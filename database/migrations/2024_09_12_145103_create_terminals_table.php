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
        Schema::create('terminals', function (Blueprint $table) {
            $table->id();
            $table->integer('terminal_id')->unique();
            $table->string('serial_number')->unique();
            $table->foreignIdFor(\App\Models\Merchants::class, 'merchant_id');
            $table->string('type');
            $table->string('model');
            $table->string('manufacturer');
            $table->string('status');
            $table->dateTime('date_activated')->nullable();
            $table->string('activation_code')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('terminals');
    }
};
