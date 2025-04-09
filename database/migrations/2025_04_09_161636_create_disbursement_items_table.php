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
        Schema::create('disbursement_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bulk_disbursement_id')->constrained()->cascadeOnDelete();
            $table->foreignId('transaction_id')->nullable()->constrained();
            $table->foreignId('wallet_provider_id')->nullable()->constrained();
            $table->string('wallet_number');
            $table->string('recipient_name')->nullable();
            $table->decimal('amount', 12, 2);
            $table->decimal('fee', 12, 2);
            $table->string('currency', 3)->default('ZMW');
            $table->enum('status', ['pending', 'processing', 'completed', 'failed'])->default('pending');
            $table->text('error_message')->nullable();
            $table->string('reference');
            $table->integer('row_number')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('disbursement_items');
    }
};
