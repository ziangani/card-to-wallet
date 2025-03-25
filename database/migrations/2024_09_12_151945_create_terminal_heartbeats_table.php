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
        Schema::create('terminal_heartbeats', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('terminal_id');
            $table->string('location');
            $table->integer('battery_health');
            $table->integer('transactions_count');
            $table->json('misc')->nullable();
            $table->timestamps();

            $table->foreign('terminal_id')->references('id')->on('terminals')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('terminal_heartbeats');
    }
};
