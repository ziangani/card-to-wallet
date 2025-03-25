<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('company_details', function (Blueprint $table) {
            $table->id();
            $table->string('company_name');
            $table->string('trading_name')->nullable();
            $table->string('type_of_ownership');
            $table->string('rc_number');
            $table->string('tpin');
            $table->date('date_registered');
            $table->string('nature_of_business');
            $table->text('office_address');
            $table->text('postal_address');
            $table->string('country_of_incorporation');
            $table->string('office_telephone');
            $table->string('customer_service_telephone')->nullable();
            $table->string('official_email');
            $table->string('customer_service_email')->nullable();
            $table->string('official_website')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('company_details');
    }
};
