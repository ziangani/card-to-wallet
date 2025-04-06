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
        Schema::create('transaction_charges', function (Blueprint $table) {
            $table->id();
            $table->foreignId('transaction_id')->constrained('transactions')->onDelete('cascade');
            $table->foreignId('charge_id')->nullable()->constrained('charges')->onDelete('set null');
            $table->string('charge_name');
            $table->string('charge_type');
            $table->decimal('charge_value', 10, 2);
            $table->decimal('base_amount', 10, 2);
            $table->decimal('calculated_amount', 10, 2);
            $table->string('merchant_id')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transaction_charges');
    }
};
