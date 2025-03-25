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
        Schema::create('company_financials', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(\App\Models\CompanyDetail::class, 'company_id');
            $table->string('description');
            $table->integer('volume');
            $table->decimal('value', 15, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('company_financials');
    }
};
