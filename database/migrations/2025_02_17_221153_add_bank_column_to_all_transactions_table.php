<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement("
            ALTER TABLE all_transactions
            ADD COLUMN bank VARCHAR(255) 
            GENERATED ALWAYS AS (
                CASE 
                    WHEN source = 'MPGS' THEN
                        CASE txn_acquirer_id
                            WHEN 'FRB_S2I' THEN 'FNB'
                            WHEN 'UBAZAM_S2I' THEN 'UBA'
                            ELSE NULL
                        END
                    WHEN source = 'CYBERSOURCE' THEN 'ABSA'
                    ELSE NULL
                END
            ) STORED
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('all_transactions', function (Blueprint $table) {
            $table->dropColumn('bank');
        });
    }
};
