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
        Schema::create('beneficiaries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->unsignedBigInteger('wallet_provider_id');
            $table->string('wallet_number', 50);
            $table->string('recipient_name', 100);
            $table->boolean('is_favorite')->default(false);
            $table->text('notes')->nullable();
            $table->timestamps();
            
            // Add unique constraint to prevent duplicate entries
            $table->unique(['user_id', 'wallet_provider_id', 'wallet_number']);
            
            // Add index for performance
            $table->index('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('beneficiaries');
    }
};
