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
        Schema::create('company_contacts', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(\App\Models\CompanyDetail::class, 'company_id');
            $table->string('primary_full_name');
            $table->string('primary_country');
            $table->string('primary_phone_number');
            $table->string('primary_email');
            $table->text('primary_address');
            $table->string('primary_town');
            $table->string('primary_designation');
            $table->string('secondary_full_name')->nullable();
            $table->string('secondary_country')->nullable();
            $table->string('secondary_phone_number')->nullable();
            $table->string('secondary_email')->nullable();
            $table->text('secondary_address')->nullable();
            $table->string('secondary_town')->nullable();
            $table->string('secondary_designation')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('company_contacts');
    }
};
