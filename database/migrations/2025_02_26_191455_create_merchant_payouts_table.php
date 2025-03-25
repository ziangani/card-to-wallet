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
        Schema::create('merchant_payouts', function (Blueprint $table) {
            $table->id();
            $table->string('merchant_id');
            $table->decimal('amount', 15, 2);
            $table->string('type'); // SETTLEMENT, ROLLING_RESERVE_RETURN, REFUND, etc.
            $table->string('status')->default('PENDING'); // PENDING, COMPLETED, FAILED, CANCELLED
            $table->string('reference')->nullable(); // Payment reference/transaction ID
            $table->decimal('remittance_fee', 15, 2)->default(0); // Fees for sending the payment
            $table->timestamp('initiated_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->string('initiated_by')->nullable(); // User who initiated the payout
            $table->text('notes')->nullable();
            $table->timestamps();
            
            // Foreign keys and indexes
            $table->index(['merchant_id', 'type', 'status']);
            $table->index('completed_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('merchant_payouts');
    }
};
