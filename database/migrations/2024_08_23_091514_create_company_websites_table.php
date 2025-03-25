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
        Schema::create('company_websites', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(\App\Models\CompanyDetail::class, 'company_id');
            $table->boolean('accept_international_payments');
            $table->text('products_services');
            $table->integer('delivery_days');
            $table->integer('total_sales_points');
            $table->boolean('secure_platform');
            $table->text('security_details');
            $table->json('payment_services_request');
            $table->json('techpay_services_requested');
            $table->json('policies');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('company_websites');
    }
};
