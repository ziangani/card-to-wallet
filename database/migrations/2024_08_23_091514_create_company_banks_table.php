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
        Schema::create('company_banks', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(\App\Models\CompanyDetail::class, 'company_id');
            $table->string('bank_name');
            $table->string('bank_branch');
            $table->string('bank_sort_code');
            $table->string('account_type');
            $table->string('account_number');
            $table->string('account_name');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('company_banks');
    }
};
