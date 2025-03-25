<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\{Schema, DB};

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Drop the existing unique constraint
        Schema::table('settlement_records', function (Blueprint $table) {
            $table->dropUnique('unique_settlement_record');
        });

        // Add a new unique constraint that varies by provider
        DB::statement("
            CREATE UNIQUE INDEX unique_settlement_record ON settlement_records (
                provider,
                merchant_id,
                transaction_reference,
                (CASE 
                    WHEN provider = 'FNB' THEN 'FNB'::text
                    ELSE COALESCE(arn_reference, 'NULL'::text)
                END)
            );
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop the new unique constraint
        Schema::table('settlement_records', function (Blueprint $table) {
            $table->dropIndex('unique_settlement_record');
        });

        // Restore the original unique constraint
        Schema::table('settlement_records', function (Blueprint $table) {
            $table->unique(['provider', 'merchant_id', 'transaction_reference', 'arn_reference'], 'unique_settlement_record');
        });
    }
};
