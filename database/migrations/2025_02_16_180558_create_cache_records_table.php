<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('cache_records', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->string('type')->index()->comment('Type of cached data e.g. dashboard_stats, system_config');
            $table->string('subtype')->nullable()->index()->comment('Subtype for more specific categorization e.g. today, overall');
            $table->json('value');
            $table->integer('expiration')->nullable();
            $table->string('created_by')->nullable()->comment('User or system process that created the cache');
            $table->timestamps();

            // Composite index for efficient lookups
            $table->index(['type', 'subtype']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('cache_records');
    }
};
