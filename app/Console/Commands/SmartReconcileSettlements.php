<?php

namespace App\Console\Commands;

use App\Models\AllTransactions;
use App\Models\Merchants;
use App\Models\SettlementReports;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class SmartReconcileSettlements extends Command
{
    protected $signature = 'settlements:smart-reconcile 
        {--debug : Show detailed transaction information}
        {--tolerance=0.01 : Value matching tolerance}
        {--volume-tolerance=0 : Volume matching tolerance}
        {--max-days=7 : Maximum days to look back}';

    protected $description = 'Smart reconciliation of settlement reports using settlement schedule analysis';

    private $totalProcessed = 0;
    private $totalSuccess = 0;
    private $totalFailed = 0;

    public function handle()
    {
        $this->info('Starting smart settlement reconciliation...');

        // Get unreconciled settlement reports
        $reports = SettlementReports::whereNull('reconciliation_status')
            ->orWhere('reconciliation_status', '!=', 'RECONCILED')
            ->orderBy('settlement_date')
            ->get();

        foreach ($reports as $report) {
            $this->totalProcessed++;
            $this->info("\nProcessing settlement report for merchant {$report->merchant} on {$report->settlement_date}");
            $this->info("Looking for {$report->volume} transactions totaling {$report->value} {$report->currency}");

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
        // Get settlement date
        $settlementDate = Carbon::parse($report->settlement_date);
        
        // Get transactions for an extended period
        $maxDays = $this->option('max-days');
        $startDate = $settlementDate->copy()->subDays($maxDays)->startOfDay();
        $endDate = $settlementDate->copy()->endOfDay();
        
        $this->info("Searching for transactions from {$startDate} to {$endDate}");

        // Get all transactions within the date range
        $transactions = AllTransactions::where('merchant', $report->merchant)
            ->whereBetween('txn_date', [$startDate, $endDate])
            ->where('order_currency', $report->currency)
            ->where('result', 'SUCCESS')
            ->whereNotNull('txn_amount')
            ->whereNotNull('order_currency')
            ->orderBy('txn_date')
            ->get();

        if ($this->option('debug')) {
            $this->showTransactionBreakdown($transactions);
        }

        // Try different matching strategies
        $matchingTransactions = $this->findMatchingTransactionsWithStrategies($transactions, $report);

        if ($matchingTransactions) {
            $this->generateSettlementFile($report, $matchingTransactions);
            $this->totalSuccess++;
        } else {
            $this->handleFailedMatch($report, $transactions);
            $this->totalFailed++;
        }
    }

    private function showTransactionBreakdown($transactions)
    {
        $this->info("\nFound {$transactions->count()} total transactions");
        
        // Group by date
        $byDate = $transactions->groupBy(function($txn) {
            return Carbon::parse($txn->txn_date)->format('Y-m-d');
        });

        foreach ($byDate as $date => $dayTxns) {
            $this->info("\nDate: {$date}");
            $this->info("Transactions: " . $dayTxns->count());
            $this->info("Total Value: " . number_format($dayTxns->sum('txn_amount'), 2));

            // Show hourly breakdown
            $byHour = $dayTxns->groupBy(function($txn) {
                return Carbon::parse($txn->txn_date)->format('H');
            })->sortKeys();

            foreach ($byHour as $hour => $hourTxns) {
                $this->line(sprintf(
                    "  Hour %02d: %d txns, value: %s",
                    $hour,
                    $hourTxns->count(),
                    number_format($hourTxns->sum('txn_amount'), 2)
                ));
            }
        }
    }

    private function findMatchingTransactionsWithStrategies($transactions, $report)
    {
        if ($transactions->isEmpty()) {
            return null;
        }

        // Strategy 1: Try to find transactions that sum up exactly to the settlement amount
        $match = $this->findExactValueMatch($transactions, $report);
        if ($match) {
            $this->info("Found exact value match");
            return $match;
        }

        // Strategy 2: Try to find transactions within a time window that match
        $match = $this->findTimeWindowMatch($transactions, $report);
        if ($match) {
            $this->info("Found time window match");
            return $match;
        }

        // Strategy 3: Try greedy matching
        $match = $this->findGreedyMatch($transactions, $report);
        if ($match) {
            $this->info("Found greedy match");
            return $match;
        }

        return null;
    }

    private function findExactValueMatch($transactions, $report)
    {
        $targetValue = $report->value;
        $tolerance = $this->option('tolerance');
        
        // Try different combinations starting from each transaction
        foreach ($transactions as $startIdx => $startTxn) {
            $subset = collect([$startTxn]);
            $sum = $startTxn->txn_amount;
            
            for ($i = $startIdx + 1; $i < $transactions->count(); $i++) {
                $txn = $transactions[$i];
                $newSum = $sum + $txn->txn_amount;
                
                // If we've exceeded the target, skip this combination
                if ($newSum > $targetValue + $tolerance) {
                    continue;
                }
                
                $subset->push($txn);
                $sum = $newSum;
                
                // Check if we've found a match
                if (abs($sum - $targetValue) <= $tolerance && 
                    abs($subset->count() - $report->volume) <= $this->option('volume-tolerance')) {
                    return $subset;
                }
            }
        }
        
        return null;
    }

    private function findTimeWindowMatch($transactions, $report)
    {
        // Group transactions by 24-hour windows
        $startDate = Carbon::parse($transactions->min('txn_date'));
        $endDate = Carbon::parse($transactions->max('txn_date'));
        
        for ($date = $startDate; $date <= $endDate; $date->addDay()) {
            $windowStart = $date->copy();
            $windowEnd = $date->copy()->addHours(24);
            
            $windowTxns = $transactions->filter(function($txn) use ($windowStart, $windowEnd) {
                $txnDate = Carbon::parse($txn->txn_date);
                return $txnDate >= $windowStart && $txnDate < $windowEnd;
            });
            
            if ($this->isMatchingSet($windowTxns, $report)) {
                return $windowTxns;
            }
        }
        
        return null;
    }

    private function findGreedyMatch($transactions, $report)
    {
        $targetValue = $report->value;
        $tolerance = $this->option('tolerance');
        $volumeTolerance = $this->option('volume-tolerance');
        
        // Sort transactions by date
        $sortedTxns = $transactions->sortBy('txn_date');
        $bestMatch = null;
        $bestDiff = PHP_FLOAT_MAX;
        
        // Try different window sizes
        for ($size = $report->volume - $volumeTolerance; 
             $size <= $report->volume + $volumeTolerance; 
             $size++) {
            
            // Slide window over transactions
            for ($i = 0; $i <= $sortedTxns->count() - $size; $i++) {
                $window = $sortedTxns->slice($i, $size);
                $sum = $window->sum('txn_amount');
                $diff = abs($sum - $targetValue);
                
                if ($diff < $bestDiff) {
                    $bestDiff = $diff;
                    $bestMatch = $window;
                }
            }
        }
        
        // Only return if within tolerance
        if ($bestDiff <= $tolerance) {
            return $bestMatch;
        }
        
        return null;
    }

    private function isMatchingSet($transactions, $report)
    {
        $tolerance = $this->option('tolerance');
        $volumeTolerance = $this->option('volume-tolerance');
        
        $volumeDiff = abs($transactions->count() - $report->volume);
        $valueDiff = abs($transactions->sum('txn_amount') - $report->value);
        
        return $volumeDiff <= $volumeTolerance && $valueDiff <= $tolerance;
    }

    private function generateSettlementFile($report, $transactions)
    {
        $merchant = Merchants::where('code', $report->merchant)->first();
        $merchantName = $merchant ? $merchant->name : 'Unknown';

        // Create merchant directory if it doesn't exist
        $merchantDir = "settlements/{$report->merchant}";
        Storage::makeDirectory($merchantDir);

        // Generate file name
        $fileName = sprintf(
            '%s_%s_%s_settlement.txt',
            $report->settlement_date,
            $report->merchant,
            str_replace(' ', '_', $merchantName)
        );
        $filePath = "{$merchantDir}/{$fileName}";

        // Generate file content
        $content = $this->generateSettlementFileContent($report, $transactions, $merchantName);

        // Save file
        if (Storage::put($filePath, $content)) {
            $dateRange = Carbon::parse($transactions->min('txn_date'))->format('Y-m-d H:i:s') . 
                        ' to ' . 
                        Carbon::parse($transactions->max('txn_date'))->format('Y-m-d H:i:s');

            $report->update([
                'reconciliation_status' => 'RECONCILED',
                'file_path' => $filePath,
                'reconciled_at' => now(),
                'reconciliation_comment' => "Successfully reconciled. Transactions from: {$dateRange}"
            ]);

            $this->info("Settlement file generated: {$filePath}");
        } else {
            throw new \Exception("Failed to write settlement file");
        }
    }

    private function handleFailedMatch($report, $transactions)
    {
        $comment = [];
        
        // Calculate closest matches
        $byDate = $transactions->groupBy(function($txn) {
            return Carbon::parse($txn->txn_date)->format('Y-m-d');
        });

        foreach ($byDate as $date => $dayTxns) {
            $volumeDiff = abs($dayTxns->count() - $report->volume);
            $valueDiff = abs($dayTxns->sum('txn_amount') - $report->value);
            
            if ($volumeDiff <= 5 || $valueDiff <= 100) {
                $comment[] = sprintf(
                    "Near match on %s: %d txns (diff: %d), value: %s (diff: %s)",
                    $date,
                    $dayTxns->count(),
                    $volumeDiff,
                    number_format($dayTxns->sum('txn_amount'), 2),
                    number_format($valueDiff, 2)
                );
            }
        }

        $report->update([
            'reconciliation_status' => 'FAILED',
            'reconciled_at' => now(),
            'reconciliation_comment' => empty($comment) ? 
                "No matching transactions found" : 
                "Failed to reconcile. " . implode("; ", $comment)
        ]);

        $this->error("Reconciliation failed: " . $report->reconciliation_comment);
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
            "%-20s %-20s %-15s %-15s %-20s %-10s",
            "Transaction ID",
            "Order ID",
            "Amount",
            "Card",
            "Date/Time",
            "Status"
        );
        $content[] = str_repeat('-', 80);

        foreach ($transactions->sortBy('txn_date') as $txn) {
            $content[] = sprintf(
                "%-20s %-20s %-15s %-15s %-20s %-10s",
                $txn->txn_id,
                $txn->order_id,
                number_format($txn->txn_amount, 2),
                "xxxx-" . $txn->card_suffix,
                Carbon::parse($txn->txn_date)->format('Y-m-d H:i:s'),
                $txn->result
            );
        }

        return implode("\n", $content);
    }
}
