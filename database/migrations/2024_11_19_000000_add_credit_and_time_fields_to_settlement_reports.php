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
            $table->decimal('credit_volume', 10, 2)->default(0)->after('value');
            $table->decimal('credit_value', 15, 2)->default(0)->after('credit_volume');
            $table->timestamp('start_time')->nullable()->after('settlement_date');
            $table->timestamp('end_time')->nullable()->after('start_time');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('settlement_reports', function (Blueprint $table) {
            $table->dropColumn(['credit_volume', 'credit_value', 'start_time', 'end_time']);
        });
    }
};
