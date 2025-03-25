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
        Schema::create('company_ownerships', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(\App\Models\CompanyDetail::class, 'company_id');
            $table->string('salutation');
            $table->string('full_names');
            $table->string('nationality');
            $table->date('date_of_birth');
            $table->string('place_of_birth');
            $table->string('id_type');
            $table->string('identification_number');
            $table->string('country_of_residence');
            $table->text('residential_address');
            $table->string('designation');
            $table->string('mobile');
            $table->string('email');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('company_ownerships');
    }
};
