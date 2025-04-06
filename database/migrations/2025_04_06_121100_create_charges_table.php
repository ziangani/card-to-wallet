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
        Schema::create('charges', function (Blueprint $table) {
            $table->id();
            $table->string('channel');
            $table->string('charge_name');
            $table->string('description')->nullable();
            $table->string('charge_type');
            $table->decimal('charge_value', 10, 2);
            $table->decimal('max_amount', 10, 2)->nullable();
            $table->decimal('min_amount', 10, 2)->nullable();
            $table->boolean('is_default')->default(false);
            $table->string('company_id')->nullable();
            $table->string('merchant_id')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('charges');
    }
};
