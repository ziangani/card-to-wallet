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
        Schema::create('settlement_reports', function (Blueprint $table) {
            $table->id();
            $table->string('source');
            $table->date('settlement_date');
            $table->string('merchant');
            $table->string('currency');
            $table->decimal('volume', 10, 2);
            $table->decimal('value', 15, 2);
            $table->decimal('bank_charge', 15, 2)->nullable();
            $table->decimal('our_charge', 15, 2)->nullable();
            $table->decimal('net_settlement', 15, 2)->nullable();
            $table->json('raw_data');
            $table->string('status')->default('PROCESSED');
            $table->unique(['source', 'settlement_date', 'merchant', 'currency']);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('settlement_reports');
    }
};
