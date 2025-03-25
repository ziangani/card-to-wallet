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
        Schema::table('settlement_records', function (Blueprint $table) {
            $table->string('legal_name')->nullable()->after('merchant_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('settlement_records', function (Blueprint $table) {
            $table->dropColumn('legal_name');
        });
    }
};
