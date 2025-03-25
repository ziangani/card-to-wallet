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
        Schema::create('approvals', function (Blueprint $table) {
            $table->id();
            $table->string('reference');
            $table->string('module');
            $table->integer('level');
            $table->string('level_name');
            $table->string('status');
            $table->bigInteger('initiated_by');
            $table->foreignId('actioned_by')->nullable()->constrained('users');
            $table->text('comments')->nullable();
            $table->timestamps();

            // Index for faster lookups
            $table->index(['reference', 'module']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('approvals');
    }
};
