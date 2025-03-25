<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\{Schema, DB};

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('settlement_records', function (Blueprint $table) {
            $table->id();
            
            // File Information
            $table->string('provider', 10); // UBA, ABSA, FNB
            $table->string('file_name');
            $table->string('batch_id', 100)->nullable();
            
            // Merchant Information
            $table->string('parent_merchant_id', 100)->nullable();
            $table->string('outlet_id', 100)->nullable();
            $table->string('merchant_id', 100);
            $table->string('merchant_name')->nullable();
            $table->string('merchant_location')->nullable();
            $table->string('terminal_id', 50)->nullable();
            
            // Transaction Details
            $table->timestamp('transaction_date');
            $table->timestamp('processing_date')->nullable();
            $table->timestamp('settlement_date')->nullable();
            $table->string('transaction_type', 50);
            $table->string('transaction_reference', 100);
            $table->string('authorization_code', 50)->nullable();
            $table->string('card_number', 50)->nullable();
            $table->string('card_scheme', 20)->nullable();
            
            // Amount Information
            $table->decimal('original_amount', 20, 2);
            $table->string('original_currency', 10);
            $table->decimal('settlement_amount', 20, 2)->nullable();
            $table->string('settlement_currency', 10)->nullable();
            $table->decimal('commission_amount', 20, 2)->default(0.00);
            $table->decimal('net_amount', 20, 2)->nullable();
            
            // Additional Information
            $table->boolean('card_present')->nullable();
            $table->string('transaction_source', 50)->nullable();
            $table->string('approval_code', 50)->nullable();
            $table->string('arn_reference', 100)->nullable();
            $table->string('remittance_number', 50)->nullable();
            $table->string('merchant_account_number', 100)->nullable();
            $table->string('merchant_account_bank_code', 20)->nullable();
            
            // Status and Processing
            $table->string('status', 20)->default('pending');
            $table->text('error_message')->nullable();
            $table->timestamp('processed_at')->nullable();
            
            // Original Row Data
            $table->jsonb('raw_data'); // Store original CSV row as JSON
            
            // Timestamps
            $table->timestamps();
            
            // Indexes
            $table->unique(['provider', 'merchant_id', 'transaction_reference', 'arn_reference'], 'unique_settlement_record');
            $table->index('merchant_id');
            $table->index('parent_merchant_id');
            $table->index('outlet_id');
            $table->index(['transaction_date', 'processing_date', 'settlement_date']);
            $table->index('file_name');
            $table->index('status');
            $table->index('card_scheme');
        });

        // Add check constraints
        DB::statement("ALTER TABLE settlement_records ADD CONSTRAINT check_provider CHECK (provider IN ('UBA', 'ABSA', 'FNB'))");
        DB::statement("ALTER TABLE settlement_records ADD CONSTRAINT check_status CHECK (status IN ('pending', 'processed', 'failed', 'duplicate'))");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('settlement_records');
    }
};
