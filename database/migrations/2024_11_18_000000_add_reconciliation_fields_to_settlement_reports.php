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
            $table->string('reconciliation_status')->nullable()->after('status');
            $table->string('file_path')->nullable()->after('reconciliation_status');
            $table->timestamp('reconciled_at')->nullable()->after('file_path');
            $table->text('reconciliation_comment')->nullable()->after('reconciled_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('settlement_reports', function (Blueprint $table) {
            $table->dropColumn('reconciliation_status');
            $table->dropColumn('file_path');
            $table->dropColumn('reconciled_at');
            $table->dropColumn('reconciliation_comment');
        });
    }
};
