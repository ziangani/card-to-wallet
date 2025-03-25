<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('merchant_cash_out_accounts', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(\App\Models\Merchants::class, 'merchant_id');
            $table->string('account_type');
            $table->string('account_number');
            $table->string('name_on_account');
            $table->string('mobile_operator')->nullable();
            $table->string('bank_name');
            $table->string('branch_code');
            $table->string('swift_code')->nullable();
            $table->string('beneficiary_name');
            $table->string('beneficiary_id');
            $table->string('country');
            $table->string('province_state');
            $table->string('town_county');
            $table->string('status');
            $table->string('approved_by');
            $table->string('approved_at')->nullable();
            $table->string('comment')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('merchant_cash_out_accounts');
    }
};
