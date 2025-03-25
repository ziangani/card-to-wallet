<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DownloadedReport extends Model
{
    protected $fillable = [
        'merchant_id',
        'report_type',
        'report_name',
        'report_format',
        'source_system',
        'source_report_id',
        'source_definition_id',
        'source_metadata',
        'frequency',
        'status',
        'status_message',
        'report_meta',
        'report_start_time',
        'report_end_time',
        'timezone',
        'queued_time',
        'completed_time',
        'file_path',
        'file_name',
        'is_processed',
        'notification_sent',
        'processed_at',
        'notification_sent_at',
    ];

    protected $casts = [
        'source_metadata' => 'array',
        'report_meta' => 'array',
        'report_start_time' => 'datetime',
        'report_end_time' => 'datetime',
        'queued_time' => 'datetime',
        'completed_time' => 'datetime',
        'processed_at' => 'datetime',
        'notification_sent_at' => 'datetime',
        'is_processed' => 'boolean',
        'notification_sent' => 'boolean',
    ];

    /**
     * Get the merchant associated with this report.
     */
    public function merchant()
    {
        return $this->belongsTo(Merchants::class, 'merchant_id');
    }

    /**
     * Scope a query to only include unprocessed reports.
     */
    public function scopeUnprocessed($query)
    {
        return $query->where('is_processed', false)
                    ->where('status', 'completed');
    }

    /**
     * Scope a query to only include reports pending notification.
     */
    public function scopePendingNotification($query)
    {
        return $query->where('notification_sent', false)
                    ->where('status', 'completed');
    }

    /**
     * Scope a query to only include reports from a specific source system.
     */
    public function scopeFromSystem($query, string $system)
    {
        return $query->where('source_system', $system);
    }

    /**
     * Scope a query to only include reports of a specific type.
     */
    public function scopeOfType($query, string $type)
    {
        return $query->where('report_type', $type);
    }

    /**
     * Mark the report as processed.
     */
    public function markAsProcessed(): bool
    {
        return $this->update([
            'is_processed' => true,
            'processed_at' => now(),
        ]);
    }

    /**
     * Mark the report as having had its notification sent.
     */
    public function markNotificationSent(): bool
    {
        return $this->update([
            'notification_sent' => true,
            'notification_sent_at' => now(),
        ]);
    }

    /**
     * Store source-specific metadata.
     */
    public function updateSourceMetadata(array $metadata): bool
    {
        return $this->update([
            'source_metadata' => array_merge($this->source_metadata ?? [], $metadata)
        ]);
    }

    /**
     * Update report-specific metadata.
     */
    public function updateReportMeta(array $metadata): bool
    {
        return $this->update([
            'report_meta' => array_merge($this->report_meta ?? [], $metadata)
        ]);
    }

    /**
     * Get the full file path including filename.
     */
    public function getFullFilePath(): ?string
    {
        if (!$this->file_path || !$this->file_name) {
            return null;
        }
        return rtrim($this->file_path, '/') . '/' . $this->file_name;
    }
}
