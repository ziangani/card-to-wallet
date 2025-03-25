<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement("
            CREATE VIEW settlement_summaries AS
            SELECT 
                s.merchant,
                m.name as merchant_name,
                s.currency,
                s.settlement_date,
                s.value as debit_value,
                s.credit_value,
                (s.value - COALESCE(s.credit_value, 0) - COALESCE(s.bank_charge, 0) - COALESCE(s.our_charge, 0)) as net_settlement
            FROM settlement_reports s
            JOIN merchants m ON s.merchant = m.code
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('DROP VIEW IF EXISTS settlement_summaries');
    }
};
