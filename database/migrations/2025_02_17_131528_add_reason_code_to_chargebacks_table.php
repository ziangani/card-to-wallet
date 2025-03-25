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
        Schema::table('chargebacks', function (Blueprint $table) {
            $table->string('reason_code')->nullable();
            $table->text('tran_reason')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('chargebacks', function (Blueprint $table) {
            $table->dropColumn('reason_code');
            $table->text('tran_reason')->nullable(false)->change();
        });
    }
};
