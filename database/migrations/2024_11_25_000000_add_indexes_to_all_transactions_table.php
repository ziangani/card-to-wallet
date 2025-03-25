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
        Schema::table('all_transactions', function (Blueprint $table) {
            // Index for date range queries
            $table->index('txn_date');
            
            // Index for filtering by source and type
            $table->index(['source', 'txn_type']);
            
            // Index for MPGS acquirer filtering
            $table->index(['source', 'txn_acquirer_id']);
            
            // Index for currency and card type grouping
            $table->index(['txn_currency', 'card_type']);
            
            // Index for success amount sorting in top merchants
            $table->index(['result', 'txn_amount']);
            
            // Index for merchant lookups
            $table->index('merchant');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('all_transactions', function (Blueprint $table) {
            $table->dropIndex(['txn_date']);
            $table->dropIndex(['source', 'txn_type']);
            $table->dropIndex(['source', 'txn_acquirer_id']);
            $table->dropIndex(['txn_currency', 'card_type']);
            $table->dropIndex(['result', 'txn_amount']);
            $table->dropIndex(['merchant']);
        });
    }
};
