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
        Schema::create('downloaded_reports', function (Blueprint $table) {
            $table->id();
            // Core report information
            $table->string('merchant_id');
            $table->string('report_type'); // e.g., 'daily_batch', 'settlement', 'transaction'
            $table->string('report_name');
            $table->string('report_format')->nullable(); // e.g., 'csv', 'json', 'xml'
            
            // Source system information
            $table->string('source_system'); // e.g., 'cybersource', 'mpgs', 'airtel'
            $table->string('source_report_id')->nullable(); // Original report ID from source system
            $table->string('source_definition_id')->nullable(); // Original definition ID from source system
            $table->json('source_metadata')->nullable(); // Store any additional source-specific data
            
            // Report details
            $table->string('frequency')->nullable(); // e.g., 'daily', 'weekly', 'monthly'
            $table->string('status'); // e.g., 'pending', 'completed', 'failed'
            $table->text('status_message')->nullable(); // For storing error messages or additional status info
            $table->json('report_meta')->nullable();
            
            // Timing information
            $table->dateTime('report_start_time');
            $table->dateTime('report_end_time');
            $table->string('timezone')->nullable();
            $table->dateTime('queued_time')->nullable();
            $table->dateTime('completed_time')->nullable();
            
            // File and processing details
            $table->string('file_path')->nullable();
            $table->string('file_name')->nullable();
            $table->boolean('is_processed')->default(false);
            $table->boolean('notification_sent')->default(false);
            $table->timestamp('processed_at')->nullable();
            $table->timestamp('notification_sent_at')->nullable();
            
            $table->timestamps();
            
            // Add indexes for common queries
            $table->index('merchant_id');
            $table->index('source_system');
            $table->index('report_type');
            $table->index('status');
            $table->index(['is_processed', 'notification_sent']);
            $table->index('report_start_time');
            $table->index('report_end_time');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('downloaded_reports');
    }
};
