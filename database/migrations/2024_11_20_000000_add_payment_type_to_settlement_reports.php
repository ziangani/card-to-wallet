<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('settlement_reports', function (Blueprint $table) {
            $table->string('payment_type')->nullable()->after('currency');
        });
    }

    public function down()
    {
        Schema::table('settlement_reports', function (Blueprint $table) {
            $table->dropColumn('payment_type');
        });
    }
};
