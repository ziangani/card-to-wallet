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
        Schema::create('companies', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('name');
            $table->string('registration_number');
            $table->string('tax_id')->nullable();
            $table->string('industry')->nullable();
            $table->text('address');
            $table->string('city');
            $table->string('country')->default('Zambia');
            $table->string('postal_code')->nullable();
            $table->string('phone_number');
            $table->string('email');
            $table->string('website')->nullable();
            $table->string('logo_path')->nullable();
            $table->enum('verification_status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->enum('status', ['active', 'suspended', 'inactive'])->default('active');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('companies');
    }
};
