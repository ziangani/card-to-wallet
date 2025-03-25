<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        // Add composite indexes for commonly queried columns
        Schema::table('all_transactions', function (Blueprint $table) {
            // Index for date + type + result queries
            $table->index(['txn_date', 'txn_type', 'result'], 'idx_txn_date_type_result');
            
            // Partial indexes for specific transaction types
            DB::statement('CREATE INDEX idx_payment_txns ON all_transactions (txn_date, result, txn_amount) WHERE txn_type IN (\'PAYMENT\', \'credit card\')');
            DB::statement('CREATE INDEX idx_auth_txns ON all_transactions (txn_date, result, txn_amount) WHERE txn_type IN (\'AUTHENTICATION\', \'credit card\')');
        });

        // Create a materialized view for daily transaction stats
        DB::statement('CREATE MATERIALIZED VIEW mv_daily_transaction_stats AS
            SELECT 
                date_trunc(\'day\', txn_date) as stat_date,
                SUM(CASE WHEN txn_type IN (\'PAYMENT\', \'credit card\') AND result = \'SUCCESS\' THEN 1 ELSE 0 END) as payments_success_volume,
                SUM(CASE WHEN txn_type IN (\'PAYMENT\', \'credit card\') AND result = \'FAILURE\' THEN 1 ELSE 0 END) as payments_failed_volume,
                SUM(CASE WHEN txn_type IN (\'PAYMENT\', \'credit card\') AND result = \'SUCCESS\' THEN txn_amount ELSE 0 END) as payments_success_value,
                SUM(CASE WHEN txn_type IN (\'PAYMENT\', \'credit card\') AND result = \'FAILURE\' THEN txn_amount ELSE 0 END) as payments_failed_value,
                SUM(CASE WHEN txn_type IN (\'AUTHENTICATION\', \'credit card\') AND result = \'SUCCESS\' THEN 1 ELSE 0 END) as auth_success_volume,
                SUM(CASE WHEN txn_type IN (\'AUTHENTICATION\', \'credit card\') AND result = \'FAILURE\' THEN 1 ELSE 0 END) as auth_failed_volume,
                SUM(CASE WHEN txn_type IN (\'AUTHENTICATION\', \'credit card\') AND result = \'SUCCESS\' THEN txn_amount ELSE 0 END) as auth_success_value,
                SUM(CASE WHEN txn_type IN (\'AUTHENTICATION\', \'credit card\') AND result = \'FAILURE\' THEN txn_amount ELSE 0 END) as auth_failed_value
            FROM all_transactions
            GROUP BY date_trunc(\'day\', txn_date)
            WITH DATA'
        );

        // Add unique index on stat_date for concurrent refresh
        DB::statement('CREATE UNIQUE INDEX idx_mv_daily_stats_date ON mv_daily_transaction_stats (stat_date)');
    }

    public function down()
    {
        Schema::table('all_transactions', function (Blueprint $table) {
            $table->dropIndex('idx_txn_date_type_result');
        });

        DB::statement('DROP INDEX IF EXISTS idx_payment_txns');
        DB::statement('DROP INDEX IF EXISTS idx_auth_txns');
        DB::statement('DROP MATERIALIZED VIEW IF EXISTS mv_daily_transaction_stats');
    }
};
