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
        Schema::create('transaction_refunds', function (Blueprint $table) {
            $table->id();
            $table->foreignId('transaction_id')->constrained('all_transactions');
            $table->decimal('amount', 10, 2)->nullable();
            $table->string('currency', 3)->nullable();
            $table->string('status');
            $table->string('reason')->nullable();
            $table->string('reference_id')->nullable();
            $table->string('arn')->nullable();
            $table->string('cybersource_id')->nullable();
            $table->json('response_data')->nullable();
            $table->timestamp('processed_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transaction_refunds');
    }
};
