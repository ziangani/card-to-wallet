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
        Schema::create('corporate_wallet_transactions', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('corporate_wallet_id')->constrained()->cascadeOnDelete();
            $table->enum('transaction_type', ['deposit', 'withdrawal', 'transfer', 'fee', 'adjustment']);
            $table->decimal('amount', 15, 2);
            $table->decimal('balance_after', 15, 2);
            $table->string('currency', 3)->default('ZMW');
            $table->text('description')->nullable();
            $table->string('reference_number')->nullable();
            $table->foreignId('performed_by')->nullable()->constrained('users');
            $table->enum('status', ['pending', 'completed', 'failed', 'reversed'])->default('completed');
            $table->string('related_entity_type')->nullable();
            $table->unsignedBigInteger('related_entity_id')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['related_entity_type', 'related_entity_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('corporate_wallet_transactions');
    }
};
