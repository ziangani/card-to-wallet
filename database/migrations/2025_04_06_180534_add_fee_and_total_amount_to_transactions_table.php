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
        Schema::table('transactions', function (Blueprint $table) {
            if (!Schema::hasColumn('transactions', 'fee_amount')) {
                $table->decimal('fee_amount', 10, 2)->nullable()->after('amount');
            }
            
            if (!Schema::hasColumn('transactions', 'total_amount')) {
                $table->decimal('total_amount', 10, 2)->nullable()->after('fee_amount');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            if (Schema::hasColumn('transactions', 'fee_amount')) {
                $table->dropColumn('fee_amount');
            }
            
            if (Schema::hasColumn('transactions', 'total_amount')) {
                $table->dropColumn('total_amount');
            }
        });
    }
};
