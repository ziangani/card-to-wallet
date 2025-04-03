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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->default(\Illuminate\Support\Facades\DB::raw('gen_random_uuid()'));
            $table->string('name', 100)->nullable(false);
            $table->string('first_name', 100)->nullable(false);
            $table->string('last_name', 100)->nullable(false);
            $table->string('email', 255)->unique()->nullable(false);
            $table->string('phone_number', 20)->unique()->nullable(false);
            $table->string('password', 255)->nullable(false);
            $table->date('date_of_birth')->nullable(false);
            $table->text('address')->nullable();
            $table->string('city', 100)->nullable();
            $table->string('country', 100)->default('Zambia');
            $table->string('verification_level', 20)->default('basic')->check('verification_level IN (\'basic\', \'verified\')');
            $table->boolean('is_active')->default(true);
            $table->boolean('is_email_verified')->default(false);
            $table->boolean('is_phone_verified')->default(false);
            $table->integer('login_attempts')->default(0);
            $table->timestamp('last_login_date')->nullable();
            $table->timestampTz('last_login_at')->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->rememberToken();
            $table->timestamps();
                });


        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
