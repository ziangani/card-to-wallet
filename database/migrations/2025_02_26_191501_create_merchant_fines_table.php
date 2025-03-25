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
        Schema::create('merchant_fines', function (Blueprint $table) {
            $table->id();
            $table->string('merchant_id');
            $table->decimal('amount', 15, 2);
            $table->string('issuer'); // VISA, MASTERCARD, REGULATOR, etc.
            $table->string('reason'); // Reason for the fine
            $table->string('status')->default('PENDING'); // PENDING, PAID, DISPUTED, WAIVED
            $table->string('reference')->nullable(); // Reference number
            $table->date('issued_date');
            $table->date('due_date')->nullable();
            $table->date('paid_date')->nullable();
            $table->string('paid_by')->nullable(); // Who paid the fine (merchant or platform)
            $table->text('notes')->nullable();
            $table->timestamps();
            
            // Foreign keys and indexes
            $table->index(['merchant_id', 'status']);
            $table->index('issuer');
            $table->index('issued_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('merchant_fines');
    }
};
