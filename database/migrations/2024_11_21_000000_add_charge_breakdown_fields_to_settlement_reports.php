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
            $table->decimal('vat_charge', 15, 2)->default(0)->after('credit_value');
            $table->decimal('rolling_reserve', 15, 2)->default(0)->after('vat_charge');
            $table->decimal('bank_settlement', 15, 2)->default(0)->after('bank_charge');
            $table->decimal('merchant_settlement', 15, 2)->default(0)->after('our_charge');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('settlement_reports', function (Blueprint $table) {
            $table->dropColumn(['vat_charge', 'rolling_reserve', 'bank_settlement', 'merchant_settlement']);
        });
    }
};
