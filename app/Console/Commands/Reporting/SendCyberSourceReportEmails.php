<?php

namespace App\Console\Commands\Reporting;

use App\Models\DownloadedReport;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use ZipArchive;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Carbon;

class SendCyberSourceReportEmails extends Command
{
    protected $signature = 'reporting:send-cybersource-emails';
    protected $description = 'Send CyberSource PaymentBatchDetailReport files via email grouped by date';

    private $tempPath = 'temp/report_zips';

    private $recipients = [
//        'EddieMuyeba@techmasters.co.zm',
        'settlements@techmasters.co.zm',
        'settlement@techmasters.co.zm',
        'Andrewmbewe@techpay.co.zm',
        'charles@techpay.co.zm',
        'choolwe@techpay.co.zm',
        'kadipa@techpay.co.zm',
        'mutintamachila@techpay.co.zm',
        'chinedukoggu@techmasters.co.zm'
    ];

    public function handle()
    {
        $this->info('Starting to process PaymentBatchDetailReport files...');

        // Get unique unprocessed dates using Eloquent
        $unprocessedDates = DownloadedReport::query()
            ->select('report_start_time')
            ->whereNull('processed_at')
            ->where('status', 'completed')
            ->where('report_type', 'STANDARD')
            ->where('report_name', 'PaymentBatchDetailReport')
            ->distinct()
            ->pluck('report_start_time')
            ->map(function ($date) {
                return Carbon::parse($date)->startOfDay();
            });

        if ($unprocessedDates->isEmpty()) {
            $this->info('No unprocessed PaymentBatchDetailReport files found. Exiting the command.');
            return 0;
        }

        $this->info('Found ' . $unprocessedDates->count() . ' unprocessed date(s). Processing each date...');
        foreach ($unprocessedDates as $date) {
            $this->processReportsForDate($date);
        }

        $this->info('Completed processing all reports. Thank you for your patience!');
        return 0;
    }

    private function processReportsForDate(Carbon $date)
    {
        $this->info("\nProcessing PaymentBatchDetailReport files for date: " . $date->format('Y-m-d'));

        // Get all unprocessed reports for this date
        $reports = DownloadedReport::query()
            ->whereNull('processed_at')
            ->where('status', 'completed')
            ->where('report_type', 'STANDARD')
            ->where('report_name', 'PaymentBatchDetailReport')
            ->whereDate('report_start_time', $date)
            ->get();

        if ($reports->isEmpty()) {
            $this->warn("No PaymentBatchDetailReport files found for date: " . $date->format('Y-m-d'));
            return;
        }

        // Create zip file for this date
        $zipFileName = "cybersource_payment_batch_detail_reports_{$date->format('Y-m-d')}.zip";
        $zipPath = "{$this->tempPath}/{$zipFileName}";

        if (!Storage::exists($this->tempPath)) {
            Storage::makeDirectory($this->tempPath);
            $this->info("Created temporary directory for zip files at: {$this->tempPath}");
        }

        $zip = new ZipArchive();
        $zipFullPath = Storage::path($zipPath);

        if ($zip->open($zipFullPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) === true) {
            $this->info("Creating zip file: {$zipFileName}");
            foreach ($reports as $report) {
                $filePath = $report->getFullFilePath();
                if (Storage::exists($filePath)) {
                    $fileContent = Storage::get($filePath);
                    $zip->addFromString($report->file_name, $fileContent);
                    $this->info("Added file to zip: {$report->file_name}");
                } else {
                    $this->warn("File not found: {$filePath}");
                    continue;
                }
            }
            $zip->close();

            // Send email with zip attachment
            try {
                $dateStr = $date->format('Y-m-d');
                $reportCount = $reports->count();

                Mail::send('emails.cybersource-reports', [
                    'date' => $dateStr,
                    'reportCount' => $reportCount
                ], function ($message) use ($zipFullPath, $dateStr) {
                    $message->to($this->recipients)
//                        ->bcc('mweemba@techmasters.co.zm')
                        ->from('reports@techpay.co.zm', 'TechPay')
                        ->subject("CyberSource Daily Settlement Report - {$dateStr}")
                        ->attach($zipFullPath);
                });

                // Mark reports as processed and notification sent
                foreach ($reports as $report) {
                    $report->markAsProcessed();
                    $report->markNotificationSent();
                }

                $this->info("Successfully sent email with {$reportCount} files for {$dateStr}");
            } catch (\Exception $e) {
                $this->error("Failed to send email for {$dateStr}: " . $e->getMessage());
                Log::error("Failed to send CyberSource PaymentBatchDetailReport email for {$dateStr}: " . $e->getMessage());
            }

            // Cleanup
            Storage::delete($zipPath);
            $this->info("Cleaned up temporary zip file: {$zipFileName}");
        } else {
            $this->error("Failed to create zip file for date: " . $date->format('Y-m-d'));
        }
    }
}
