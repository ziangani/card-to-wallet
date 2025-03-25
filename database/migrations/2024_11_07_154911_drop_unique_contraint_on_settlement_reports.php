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
        Schema::table('settlement_reports', function (Blueprint $table) {
            $table->dropUnique('settlement_reports_source_settlement_date_merchant_currency_uni');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('settlement_reports', function (Blueprint $table) {
            $table->unique(['source', 'settlement_date', 'merchant', 'currency'], 'settlement_reports_source_settlement_date_merchant_currency_uni');
        });
    }
};
