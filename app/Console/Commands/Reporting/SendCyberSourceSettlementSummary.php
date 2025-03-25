<?php

namespace App\Console\Commands\Reporting;

use App\Models\DownloadedReport;
use App\Models\SettlementReports;
use App\Models\Merchants;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use ZipArchive;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;

class SendCyberSourceSettlementSummary extends Command
{
    protected $signature = 'reporting:send-cybersource-settlement-summary';
    protected $description = 'Send CyberSource Settlement Summary and related files on Monday and Thursday';

    private $tempPath = 'temp/report_zips';

    private $recipients = [
        'EddieMuyeba@techmasters.co.zm',
        'Andrewmbewe@techpay.co.zm',
        'charles@techpay.co.zm',
        'choolwe@techpay.co.zm',
        'kadipa@techpay.co.zm',
        'mutintamachila@techpay.co.zm',
        'chinedukoggu@techmasters.co.zm'
    ];

    public function handle()
    {
        $this->info('Starting to process Settlement Summary...');

        // Calculate date range
        $endDate = now()->startOfDay();

        // If it's Thursday, get data for Monday-Wednesday (for Friday settlement)
        // If it's Monday, get data for Thursday-Sunday (for Tuesday settlement)
        if ($endDate->dayOfWeek === 4) { // Thursday
            $startDate = $endDate->copy()->startOfWeek()->startOfDay(); // Start from Monday
            $endDate = $endDate->copy()->subDay()->endOfDay(); // End on Wednesday
        } else { // Monday
            $startDate = $endDate->copy()->subDays(4)->startOfDay(); // Start from previous Thursday
            $endDate = $endDate->copy()->subDay()->endOfDay(); // End on Sunday
        }

        // Get settlement summary
        $summaryData = DB::table('settlement_reports as s')
            ->join('merchants as m', 's.merchant', '=', 'm.code')
            ->select(
                DB::raw('sum(value) as debit_value'),
                DB::raw('sum(credit_value) as credit_value'),
                DB::raw('sum(net_settlement) as net_settlement'),
                's.merchant',
                'm.name as merchant_name',
                's.currency'
            )
            ->whereBetween('settlement_date', [$startDate, $endDate])
            ->groupBy('merchant', 'currency', 'm.name')
            ->orderByRaw('sum(value) desc')
            ->get();

        // Get payment detail reports for the period
        $reports = DownloadedReport::query()
            ->whereNull('processed_at')
            ->where('status', 'completed')
            ->where('report_type', 'STANDARD')
            ->where('report_name', 'PaymentBatchDetailReport')
            ->whereBetween('report_start_time', [$startDate, $endDate])
            ->get();

        if ($reports->isEmpty() && $summaryData->isEmpty()) {
            $this->warn('No data found for the period. Exiting.');
            return 0;
        }

        // Create zip file for payment detail reports
        $zipFileName = "cybersource_payment_detail_reports_{$startDate->format('Y-m-d')}_{$endDate->format('Y-m-d')}.zip";
        $zipPath = "{$this->tempPath}/{$zipFileName}";

        if (!Storage::exists($this->tempPath)) {
            Storage::makeDirectory($this->tempPath);
        }

        $zipFullPath = null;
        if ($reports->isNotEmpty()) {
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
            } else {
                $this->error("Failed to create zip file");
                return 1;
            }
        }

        // Generate CSV file from summary data
        $csvFileName = "settlement_summary_{$startDate->format('Y-m-d')}_{$endDate->format('Y-m-d')}.csv";
        $csvPath = "{$this->tempPath}/{$csvFileName}";
        $csvFullPath = Storage::path($csvPath);

        $csvHandle = fopen($csvFullPath, 'w');
        // Add CSV headers
        fputcsv($csvHandle, ['Merchant Code', 'Merchant Name', 'Currency', 'Debit Value', 'Credit Value', 'Net Settlement']);

        // Add data rows
        foreach ($summaryData as $row) {
            fputcsv($csvHandle, [
                $row->merchant,
                $row->merchant_name,
                $row->currency,
                $row->debit_value,
                $row->credit_value,
                $row->net_settlement
            ]);
        }
        fclose($csvHandle);

        // Send email
        try {
            Mail::send('emails.cybersource-settlement-summary', [
                'startDate' => $startDate->format('Y-m-d'),
                'endDate' => $endDate->format('Y-m-d'),
                'summaryData' => $summaryData,
                'reportCount' => $reports->count()
            ], function ($message) use ($zipFullPath, $csvFullPath, $startDate, $endDate) {
                $message->to($this->recipients)
                    ->from('reports@techpay.co.zm', 'TechPay')
                    ->bcc('mweemba@techmasters.co.zm')
                    ->subject("CyberSource Settlement Summary Report - {$startDate->format('Y-m-d')} to {$endDate->format('Y-m-d')}");

                // Attach the CSV file
                $message->attach($csvFullPath, [
                    'as' => "settlement_summary_{$startDate->format('Y-m-d')}_{$endDate->format('Y-m-d')}.csv",
                    'mime' => 'text/csv'
                ]);

                if ($zipFullPath) {
                    $message->attach($zipFullPath);
                }
            });

            $this->info('Successfully sent settlement summary email');
        } catch (\Exception $e) {
            $this->error("Failed to send email: " . $e->getMessage());
            Log::error("Failed to send CyberSource Settlement Summary email: " . $e->getMessage());
            return 1;
        }

        // Cleanup
        if ($zipFullPath) {
            Storage::delete($zipPath);
            $this->info("Cleaned up temporary zip file: {$zipFileName}");
        }
        Storage::delete($csvPath);
        $this->info("Cleaned up temporary CSV file: {$csvFileName}");

        return 0;
    }
}
