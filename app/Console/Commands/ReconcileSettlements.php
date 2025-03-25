<?php

namespace App\Console\Commands;

use App\Models\AllTransactions;
use App\Models\Merchants;
use App\Models\SettlementReports;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class ReconcileSettlements extends Command
{
    protected $signature = 'settlements:reconcile {--debug : Show detailed transaction information}';
    protected $description = 'Reconcile settlement reports with transactions and generate settlement files';

    private $totalProcessed = 0;
    private $totalSuccess = 0;
    private $totalFailed = 0;

    public function handle()
    {
        $this->info('Starting settlement reconciliation...');

        // Get unreconciled settlement reports
        $reports = SettlementReports::whereNull('reconciliation_status')
            ->orWhere('reconciliation_status', '!=', 'RECONCILED')
            ->get();

        foreach ($reports as $report) {
            $this->totalProcessed++;
            $this->info("\nProcessing settlement report for merchant {$report->merchant} on {$report->settlement_date}");

            try {
                $this->processReport($report);
            } catch (\Exception $e) {
                $this->error("Error processing settlement report: " . $e->getMessage());
                $report->update([
                    'reconciliation_status' => 'ERROR',
                    'reconciled_at' => now(),
                    'reconciliation_comment' => "Error: " . $e->getMessage()
                ]);
                $this->totalFailed++;
            }
        }

        // Show summary
        $this->info("\nReconciliation Summary");
        $this->info("==================");
        $this->info("Total Processed: {$this->totalProcessed}");
        $this->info("Successfully Reconciled: {$this->totalSuccess}");
        $this->info("Failed to Reconcile: {$this->totalFailed}");
        $this->info('Settlement reconciliation completed');
    }

    private function processReport($report)
    {
        // Try different date ranges until we find a match
        $matched = false;
        $matchedTransactions = null;
        $dateRange = null;

        // 1. Try exact date
        $this->info("Trying exact date match: {$report->settlement_date}");
        $transactions = $this->getTransactionsForDate($report, $report->settlement_date);
        if ($this->checkMatch($report, $transactions)) {
            $matched = true;
            $matchedTransactions = $transactions;
            $dateRange = "exact date {$report->settlement_date}";
        }

        // 2. Try previous date
        if (!$matched) {
            $previousDate = Carbon::parse($report->settlement_date)->subDay();
            $this->info("Trying previous date: {$previousDate}");
            $transactions = $this->getTransactionsForDate($report, $previousDate);
            if ($this->checkMatch($report, $transactions)) {
                $matched = true;
                $matchedTransactions = $transactions;
                $dateRange = "previous date {$previousDate}";
            }
        }

        // 3. Try next date
        if (!$matched) {
            $nextDate = Carbon::parse($report->settlement_date)->addDay();
            $this->info("Trying next date: {$nextDate}");
            $transactions = $this->getTransactionsForDate($report, $nextDate);
            if ($this->checkMatch($report, $transactions)) {
                $matched = true;
                $matchedTransactions = $transactions;
                $dateRange = "next date {$nextDate}";
            }
        }

        // 4. Try 3-day range
        if (!$matched) {
            $startDate = Carbon::parse($report->settlement_date)->subDay();
            $endDate = Carbon::parse($report->settlement_date)->addDay();
            $this->info("Trying 3-day range: {$startDate} to {$endDate}");
            $transactions = $this->getTransactionsForDateRange($report, $startDate, $endDate);
            if ($this->checkMatch($report, $transactions)) {
                $matched = true;
                $matchedTransactions = $transactions;
                $dateRange = "date range {$startDate} to {$endDate}";
            }
        }

        if ($matched) {
            // Generate settlement file
            $merchant = Merchants::where('code', $report->merchant)->first();
            $merchantName = $merchant ? $merchant->name : 'Unknown';

            // Create merchant directory if it doesn't exist
            $merchantDir = "settlements/{$report->merchant}";
            Storage::makeDirectory($merchantDir);

            // Generate file name: date_merchantId_merchantName
            $fileName = sprintf(
                '%s_%s_%s_settlement.txt',
                $report->settlement_date,
                $report->merchant,
                str_replace(' ', '_', $merchantName)
            );
            $filePath = "{$merchantDir}/{$fileName}";

            // Generate file content
            $content = $this->generateSettlementFileContent($report, $matchedTransactions, $merchantName);

            // Save file
            if (Storage::put($filePath, $content)) {
                // Update settlement report
                $report->update([
                    'reconciliation_status' => 'RECONCILED',
                    'file_path' => $filePath,
                    'reconciled_at' => now(),
                    'reconciliation_comment' => "Successfully reconciled using {$dateRange}"
                ]);

                $this->info("Settlement file generated: {$filePath}");
                $this->totalSuccess++;
            } else {
                throw new \Exception("Failed to write settlement file");
            }
        } else {
            $report->update([
                'reconciliation_status' => 'FAILED',
                'reconciled_at' => now(),
                'reconciliation_comment' => 'No matching transactions found across all date ranges'
            ]);

            $this->error("Reconciliation failed: No matching transactions found across all date ranges");
            $this->totalFailed++;
        }
    }

    private function getTransactionsForDate($report, $date)
    {
        return AllTransactions::where('merchant', $report->merchant)
            ->whereDate('txn_date', $date)
            ->where('order_currency', $report->currency)
            ->where('result', 'SUCCESS')
            ->whereNotNull('txn_amount')
            ->whereNotNull('order_currency')
            ->get();
    }

    private function getTransactionsForDateRange($report, $startDate, $endDate)
    {
        return AllTransactions::where('merchant', $report->merchant)
            ->whereBetween('txn_date', [$startDate, $endDate])
            ->where('order_currency', $report->currency)
            ->where('result', 'SUCCESS')
            ->whereNotNull('txn_amount')
            ->whereNotNull('order_currency')
            ->get();
    }

    private function checkMatch($report, $transactions)
    {
        $txnVolume = $transactions->count();
        $txnValue = $transactions->sum('txn_amount');

        $this->line("Checking match:");
        $this->line("Settlement - Volume: {$report->volume}, Value: {$report->value}");
        $this->line("Transactions - Volume: {$txnVolume}, Value: {$txnValue}");

        return $txnVolume == $report->volume && abs($txnValue - $report->value) < 0.01;
    }

    private function generateSettlementFileContent($report, $transactions, $merchantName)
    {
        $content = [];

        // Add header
        $content[] = str_repeat('=', 80);
        $content[] = "Settlement Report";
        $content[] = str_repeat('=', 80);
        $content[] = '';
        $content[] = "Merchant ID: {$report->merchant}";
        $content[] = "Merchant Name: {$merchantName}";
        $content[] = "Settlement Date: {$report->settlement_date}";
        $content[] = "Currency: {$report->currency}";
        $content[] = '';

        // Add summary
        $content[] = "Summary";
        $content[] = str_repeat('-', 80);
        $content[] = "Total Transactions: {$report->volume}";
        $content[] = "Total Value: {$report->value}";
        $content[] = "Bank Charge: {$report->bank_charge}";
        $content[] = "Our Charge: {$report->our_charge}";
        $content[] = "Net Settlement: {$report->net_settlement}";
        $content[] = '';

        // Add transaction details
        $content[] = "Transaction Details";
        $content[] = str_repeat('-', 80);
        $content[] = sprintf(
            "%-20s %-20s %-15s %-15s %-10s",
            "Transaction ID",
            "Order ID",
            "Amount",
            "Card",
            "Status"
        );
        $content[] = str_repeat('-', 80);

        foreach ($transactions as $txn) {
            $content[] = sprintf(
                "%-20s %-20s %-15s %-15s %-10s",
                $txn->txn_id,
                $txn->order_id,
                $txn->txn_amount,
                "xxxx-" . $txn->card_suffix,
                $txn->result
            );
        }

        return implode("\n", $content);
    }
}
