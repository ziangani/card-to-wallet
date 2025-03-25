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
            $table->string('rrn')->nullable()->change();
            $table->string('approval_code')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('chargebacks', function (Blueprint $table) {
            $table->string('rrn')->nullable(false)->change();
            $table->string('approval_code')->nullable(false)->change();
        });
    }
};
