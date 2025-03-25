<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\{DB, Schema};

class CreateChargeConfigurationsTable extends Migration
{
    public function up(): void
    {
        Schema::create('charges', function (Blueprint $table) {
            $table->id();
            $table->string('channel', 20); // MOBILE_MONEY, CARD, etc
            $table->string('charge_name', 50); // PROVIDER_MDR, PLATFORM_FEE, BROKER_FEE, ROLLING_RESERVE
            $table->string('description', 255)->nullable();
            $table->string('charge_type', 20); // FIXED, PERCENTAGE
            $table->decimal('charge_value', 10, 2);
            $table->decimal('max_amount', 10, 2)->nullable();
            $table->decimal('min_amount', 10, 2)->nullable();
            $table->boolean('is_default')->default(true);
            // Either company_id for company-wide charges or merchant_id for merchant-specific overrides
            $table->unsignedBigInteger('company_id')->nullable();
            $table->string('merchant_id')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->foreign('company_id')->references('id')->on('company_details')->onDelete('cascade');
            $table->foreign('merchant_id')->references('code')->on('merchants')->onDelete('cascade');
            $table->foreign('created_by')->references('id')->on('users');
            $table->foreign('updated_by')->references('id')->on('users');
        });

        // Add check constraint using raw SQL
        DB::statement('ALTER TABLE charges ADD CONSTRAINT check_merchant_company CHECK (
            (company_id IS NOT NULL AND merchant_id IS NULL) OR 
            (company_id IS NULL AND merchant_id IS NOT NULL) OR 
            (company_id IS NULL AND merchant_id IS NULL)
        )');

        Schema::create('transaction_charges', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('transaction_id')->nullable();
            $table->unsignedBigInteger('settlement_id')->nullable();
            $table->unsignedBigInteger('charge_id');
            $table->string('charge_name', 50);
            $table->string('charge_type', 20);
            $table->decimal('charge_value', 10, 2);
            $table->decimal('base_amount', 20, 2);
            $table->decimal('calculated_amount', 20, 2);
            $table->string('merchant_id')->nullable();
            $table->timestamps();

            $table->foreign('transaction_id')->references('id')->on('transactions')->onDelete('cascade');
            $table->foreign('settlement_id')->references('id')->on('settlement_records')->onDelete('cascade');
            $table->foreign('charge_id')->references('id')->on('charges');
            $table->foreign('merchant_id')->references('code')->on('merchants')->onDelete('cascade');
        });

        // Add check constraint using raw SQL
        DB::statement('ALTER TABLE transaction_charges ADD CONSTRAINT check_transaction_settlement CHECK (
            (transaction_id IS NOT NULL AND settlement_id IS NULL) OR 
            (transaction_id IS NULL AND settlement_id IS NOT NULL)
        )');

        Schema::table('transactions', function (Blueprint $table) {
            $table->string('charges_status')->default('PENDING');
            $table->index('charges_status');
        });

        Schema::table('settlement_records', function (Blueprint $table) {
            $table->string('charges_status')->default('PENDING');
            $table->index('charges_status');
        });
    }

    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropIndex(['charges_status']);
            $table->dropColumn('charges_status');
        });

        Schema::table('settlement_records', function (Blueprint $table) {
            $table->dropIndex(['charges_status']);
            $table->dropColumn('charges_status');
        });

        Schema::dropIfExists('transaction_charges');
        Schema::dropIfExists('charges');
    }
}
