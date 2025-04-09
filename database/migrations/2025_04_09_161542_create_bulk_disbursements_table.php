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
        Schema::create('bulk_disbursements', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('company_id')->constrained();
            $table->foreignId('corporate_wallet_id')->constrained();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('file_path')->nullable();
            $table->decimal('total_amount', 15, 2);
            $table->decimal('total_fee', 15, 2);
            $table->integer('transaction_count');
            $table->string('currency', 3)->default('ZMW');
            $table->enum('status', [
                'draft',
                'pending_approval',
                'approved',
                'processing',
                'completed',
                'partially_completed',
                'failed',
                'cancelled'
            ])->default('draft');
            $table->foreignId('initiated_by')->constrained('users');
            $table->foreignId('approved_by')->nullable()->constrained('users');
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->string('reference_number')->unique();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bulk_disbursements');
    }
};
