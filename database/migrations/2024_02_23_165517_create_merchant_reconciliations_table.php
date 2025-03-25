<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('merchant_reconciliations', function (Blueprint $table) {
            $table->id();
            $table->string('merchant_id');
            $table->date('date');
            $table->string('status')->default('ACTIVE'); // ACTIVE, SUPERSEDED
            $table->integer('version')->default(1);
            $table->text('reason')->nullable();

            // Transaction details
            $table->integer('transaction_count')->default(0);
            $table->decimal('total_amount', 15, 2)->default(0);
            $table->decimal('platform_fee', 15, 2)->default(0);
            $table->decimal('bank_fee', 15, 2)->default(0);
            $table->decimal('application_fee', 15, 2)->default(0);
            $table->decimal('rolling_reserve', 15, 2)->default(0);
            $table->decimal('return_reserve', 15, 2)->default(0);
            $table->decimal('refund_amount', 15, 2)->default(0);
            $table->integer('chargeback_count')->default(0);
            $table->decimal('chargeback_amount', 15, 2)->default(0);
            $table->decimal('chargeback_fees', 15, 2)->default(0);
            $table->decimal('net_processed', 15, 2)->default(0);
            $table->decimal('settled_amount', 15, 2)->default(0);

            // Metadata
            $table->timestamp('generated_at');
            $table->timestamps();

            // Indexes
            $table->index(['merchant_id', 'date', 'status']);
            $table->index('version');
        });
    }

    public function down()
    {
        Schema::dropIfExists('merchant_reconciliations');
    }
};
