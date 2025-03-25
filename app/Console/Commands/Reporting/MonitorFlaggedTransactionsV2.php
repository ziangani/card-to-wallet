<?php

namespace App\Console\Commands\Reporting;

use App\Models\AllTransactions;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class MonitorFlaggedTransactionsV2 extends Command
{
    protected $signature = 'reporting:monitor-flagged-transactions-v2';
    protected $description = 'Monitor transactions for exceptions and unusual patterns, generating detailed reports';

    // Load configuration from config file
    private function getConfig($key)
    {
        $config = config('transaction-monitoring');
        if (!$config) {
            $this->error('Transaction monitoring configuration not found!');
            Log::error('Transaction monitoring configuration not found in config/transaction-monitoring.php');
            throw new \Exception('Transaction monitoring configuration not found');
        }

        if (!isset($config[$key])) {
            $this->error("Configuration key '{$key}' not found!");
            Log::error("Missing configuration key: {$key} in transaction-monitoring config");
            throw new \Exception("Configuration key '{$key}' not found");
        }

        return $config[$key];
    }

    private function validateConfig()
    {
        try {
            $requiredKeys = [
                'large_amount_thresholds',
                'same_card_max_daily',
                'split_transaction_time_window',
                'split_transaction_min_count',
                'report_recipients'
            ];

            foreach ($requiredKeys as $key) {
                $this->getConfig($key);
            }

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    private function generateCsvContent($transactions, $type)
    {
        $csvRows = [];

        // CSV Header
        $headers = [
            'Transaction ID',
            'Date/Time',
            'Amount',
            'Currency',
            // Card Details
            'Card Type',
            'Card Prefix',
            'Card Suffix',
            'Payment Method',
            // KYC Details
            'Customer Name',
            'Email',
            'Phone',
            'Address',
            'City',
            'Country',
            // Merchant Details
            'Merchant Name',
            'Merchant Code',
            // Transaction Details
            'Processor',
            'Terminal ID',
            'Approval Code',
            'Commerce Indicator'
        ];

        if ($type === 'ECI 7 Transactions') {
            $headers[] = 'ECI';
        }

        $csvRows[] = implode(',', array_map(fn($header) => '"' . $header . '"', $headers));

        // CSV Data
        foreach ($transactions as $transaction) {
            $customerName = trim($transaction->bill_to_first_name . ' ' . $transaction->bill_to_last_name);
            $row = [
                $transaction->txn_id,
                $transaction->txn_date,
                number_format($transaction->txn_amount, 2),
                $transaction->txn_currency,
                // Card Details
                $transaction->card_type ?? 'N/A',
                $transaction->card_prefix ?? 'N/A',
                $transaction->card_suffix ?? 'N/A',
                $transaction->payment_method ?? 'N/A',
                // KYC Details
                $customerName ?: 'N/A',
                $transaction->bill_to_email ?? 'N/A',
                $transaction->bill_to_phone_number ?? 'N/A',
                $transaction->bill_to_address1 ?? 'N/A',
                $transaction->bill_to_city ?? 'N/A',
                $transaction->bill_to_country ?? 'N/A',
                // Merchant Details
                optional($transaction->merchants)->name ?? 'N/A',
                $transaction->merchant,
                // Transaction Details
                $transaction->processor_name ?? 'N/A',
                $transaction->terminal_id ?? 'N/A',
                $transaction->approval_code ?? 'N/A',
                $transaction->commerce_indicator ?? 'N/A'
            ];

            if ($type === 'ECI 7 Transactions') {
                $row[] = $transaction->eci_raw;
            }

            $csvRows[] = implode(',', array_map(fn($field) => '"' . str_replace('"', '""', $field) . '"', $row));
        }

        return implode("\n", $csvRows);
    }

    public function handle()
    {
        $this->info('Starting transaction exception monitoring...');

        if (!$this->validateConfig()) {
            $this->error('Invalid configuration. Please check config/transaction-monitoring.php');
            return 1;
        }

        $this->info('Configuration validated successfully.');

        $today = Carbon::today();
        $yesterday = Carbon::yesterday();

        $flaggedActivities = [];
        $csvContents = [];

        try {
            $this->info('Checking for ECI 7 transactions...');
            // 1. Check for successful transactions with ECI 7
            $query = AllTransactions::where('source', 'CYBERSOURCE')
                ->where('result', 'SUCCESS')
                ->where('eci_raw', '7')
                ->where('txn_date', '>=', $yesterday)
                ->whereIn('txn_type', ['PAYMENT', 'credit card'])
                ->whereNotNull('txn_currency')
                ->whereNotNull('txn_amount')
                ->whereNotNull('merchant')
                ->select([
                    'txn_id', 'merchant', 'txn_date', 'txn_amount', 'txn_currency',
                    'card_type', 'card_prefix', 'card_suffix', 'payment_method', 'eci_raw',
                    'bill_to_first_name', 'bill_to_last_name', 'bill_to_email', 'bill_to_phone_number',
                    'bill_to_address1', 'bill_to_city', 'bill_to_country',
                    'processor_name', 'terminal_id', 'approval_code', 'commerce_indicator'
                ])
                ->with('merchants');

            $eci7Transactions = $query->get();

            if ($eci7Transactions->count() > 0) {
                $totalAmount = $eci7Transactions->groupBy('txn_currency')
                    ->map(fn($group) => $group->sum('txn_amount'));

                $filename = 'flagged_transactions_eci7_' . Carbon::now()->format('Y-m-d') . '.csv';
                $flaggedActivities[] = [
                    'type' => 'ECI 7 Transactions',
                    'count' => $eci7Transactions->count(),
                    'amounts' => $totalAmount->map(fn($amount, $currency) =>
                        "{$currency} " . number_format($amount, 2)
                    )->values()->toArray(),
                    'filename' => $filename
                ];

                $csvContents[$filename] = $this->generateCsvContent($eci7Transactions, 'ECI 7 Transactions');
            }

            $this->info('Checking for large transactions...');
            // 2. Check for large transactions (by currency)
            $largeTransactions = collect();
            $thresholds = $this->getConfig('large_amount_thresholds');

            foreach ($thresholds as $currency => $threshold) {
                $query = AllTransactions::where('txn_date', '>=', $yesterday)
                    ->where('result', 'SUCCESS')
                    ->where('txn_currency', $currency)
                    ->where('txn_amount', '>=', $threshold)
                    ->whereIn('txn_type', ['PAYMENT', 'credit card'])
                    ->whereNotNull('txn_currency')
                    ->whereNotNull('txn_amount')
                    ->whereNotNull('merchant')
                    ->select([
                        'txn_id', 'merchant', 'txn_date', 'txn_amount', 'txn_currency',
                        'card_type', 'card_prefix', 'card_suffix', 'payment_method',
                        'bill_to_first_name', 'bill_to_last_name', 'bill_to_email', 'bill_to_phone_number',
                        'bill_to_address1', 'bill_to_city', 'bill_to_country',
                        'processor_name', 'terminal_id', 'approval_code', 'commerce_indicator'
                    ])
                    ->orderByDesc('txn_amount')
                    ->with('merchants');

                $transactions = $query->get();
                $largeTransactions = $largeTransactions->concat($transactions);
            }

            if ($largeTransactions->count() > 0) {
                $totalAmount = $largeTransactions->groupBy('txn_currency')
                    ->map(fn($group) => $group->sum('txn_amount'));

                $filename = 'flagged_transactions_large_' . Carbon::now()->format('Y-m-d') . '.csv';
                $flaggedActivities[] = [
                    'type' => 'Large Transactions',
                    'count' => $largeTransactions->count(),
                    'amounts' => $totalAmount->map(fn($amount, $currency) =>
                        "{$currency} " . number_format($amount, 2)
                    )->values()->toArray(),
                    'filename' => $filename
                ];

                $csvContents[$filename] = $this->generateCsvContent($largeTransactions, 'Large Transactions');
            }

            $this->info('Checking for frequent card usage...');
            // 3. Check for frequent card usage
            $frequentCardsQuery = "
                SELECT card_suffix, COUNT(*) as usage_count
                FROM all_transactions
                WHERE card_suffix IS NOT NULL
                AND txn_date >= ?
                AND result = ?
                AND txn_currency IS NOT NULL
                AND txn_amount IS NOT NULL
                AND merchant IS NOT NULL
                AND txn_type IN ('PAYMENT', 'credit card')
                GROUP BY card_suffix
                HAVING COUNT(*) >= ?
            ";

            $frequentCards = DB::select($frequentCardsQuery, [$yesterday, 'SUCCESS', $this->getConfig('same_card_max_daily')]);
            $frequentCardTransactions = collect();

            if (!empty($frequentCards)) {
                foreach ($frequentCards as $card) {
                    $cardTransactions = AllTransactions::where('card_suffix', $card->card_suffix)
                        ->where('txn_date', '>=', $yesterday)
                        ->where('result', 'SUCCESS')
                        ->whereIn('txn_type', ['PAYMENT', 'credit card'])
                        ->whereNotNull('txn_currency')
                        ->whereNotNull('txn_amount')
                        ->whereNotNull('merchant')
                        ->with('merchants')
                        ->select([
                            'txn_id', 'merchant', 'txn_date', 'txn_amount', 'txn_currency',
                            'card_type', 'card_prefix', 'card_suffix', 'payment_method',
                            'bill_to_first_name', 'bill_to_last_name', 'bill_to_email', 'bill_to_phone_number',
                            'bill_to_address1', 'bill_to_city', 'bill_to_country',
                            'processor_name', 'terminal_id', 'approval_code', 'commerce_indicator'
                        ])
                        ->get();

                    $frequentCardTransactions = $frequentCardTransactions->concat($cardTransactions);
                }

                $totalAmount = $frequentCardTransactions->groupBy('txn_currency')
                    ->map(fn($group) => $group->sum('txn_amount'));

                $filename = 'flagged_transactions_frequent_cards_' . Carbon::now()->format('Y-m-d') . '.csv';
                $flaggedActivities[] = [
                    'type' => 'Frequent Card Usage',
                    'count' => $frequentCardTransactions->count(),
                    'cards' => count($frequentCards),
                    'amounts' => $totalAmount->map(fn($amount, $currency) =>
                        "{$currency} " . number_format($amount, 2)
                    )->values()->toArray(),
                    'filename' => $filename
                ];

                $csvContents[$filename] = $this->generateCsvContent($frequentCardTransactions, 'Frequent Card Usage');
            }

            $this->info('Checking for split transactions...');
            // 4. Check for split transactions
            $splitTransactionsQuery = "
                SELECT card_suffix, merchant, COUNT(*) as tx_count, SUM(txn_amount) as total_amount
                FROM all_transactions
                WHERE card_suffix IS NOT NULL
                AND txn_date >= ?
                AND result = ?
                AND txn_currency IS NOT NULL
                AND txn_amount IS NOT NULL
                AND merchant IS NOT NULL
                AND txn_type IN ('PAYMENT', 'credit card')
                GROUP BY card_suffix, merchant
                HAVING COUNT(*) >= ?
            ";

            $potentialSplitTransactions = DB::select($splitTransactionsQuery, [$yesterday, 'SUCCESS', $this->getConfig('split_transaction_min_count')]);
            $splitTransactions = collect();

            foreach ($potentialSplitTransactions as $group) {
                $transactions = AllTransactions::where('card_suffix', $group->card_suffix)
                    ->where('merchant', $group->merchant)
                    ->where('txn_date', '>=', $yesterday)
                    ->where('result', 'SUCCESS')
                    ->whereIn('txn_type', ['PAYMENT', 'credit card'])
                    ->whereNotNull('txn_currency')
                    ->whereNotNull('txn_amount')
                    ->whereNotNull('merchant')
                    ->orderBy('txn_date')
                    ->with('merchants')
                    ->get();

                $firstTx = $transactions->first();
                $windowEnd = Carbon::parse($firstTx->txn_date)->addMinutes($this->getConfig('split_transaction_time_window'));

                $windowTransactions = $transactions->filter(function ($tx) use ($firstTx, $windowEnd) {
                    $txDate = Carbon::parse($tx->txn_date);
                    return $txDate->between(Carbon::parse($firstTx->txn_date), $windowEnd);
                });

                if ($windowTransactions->count() >= $this->getConfig('split_transaction_min_count')) {
                    $splitTransactions = $splitTransactions->concat($windowTransactions);
                }
            }

            if ($splitTransactions->count() > 0) {
                $totalAmount = $splitTransactions->groupBy('txn_currency')
                    ->map(fn($group) => $group->sum('txn_amount'));

                $filename = 'flagged_transactions_split_' . Carbon::now()->format('Y-m-d') . '.csv';
                $flaggedActivities[] = [
                    'type' => 'Split Transactions',
                    'count' => $splitTransactions->count(),
                    'cards' => $splitTransactions->pluck('card_suffix')->unique()->count(),
                    'amounts' => $totalAmount->map(fn($amount, $currency) =>
                        "{$currency} " . number_format($amount, 2)
                    )->values()->toArray(),
                    'filename' => $filename
                ];

                $csvContents[$filename] = $this->generateCsvContent($splitTransactions, 'Split Transactions');
            }

            // Generate and send report if suspicious activities found
            if (!empty($flaggedActivities)) {
                $this->generateAndSendReport($flaggedActivities, $csvContents);
            } else {
                $this->info('No flagged transactions detected');
            }

        } catch (\Exception $e) {
            Log::error('Error in transaction exception monitoring: ' . $e->getMessage());
            $this->error('Error monitoring transactions: ' . $e->getMessage());
        }
    }

    private function generateAndSendReport($flaggedActivities, $csvContents)
    {
        // Get monitoring parameters for the report
        $monitoringParams = [
            'large_amount_thresholds' => $this->getConfig('large_amount_thresholds'),
            'same_card_max_daily' => $this->getConfig('same_card_max_daily'),
            'split_transaction_time_window' => $this->getConfig('split_transaction_time_window'),
            'split_transaction_min_count' => $this->getConfig('split_transaction_min_count')
        ];

        try {
            // Create a timestamp for unique filenames
            $timestamp = Carbon::now()->format('Y-m-d_His');
            $csvPaths = [];
            $zipPath = storage_path('app/flagged_transactions/' . $timestamp . '_transactions.zip');

            // Ensure directory exists
            Storage::makeDirectory('flagged_transactions');

            // Create ZIP archive
            $zip = new \ZipArchive();
            if ($zip->open($zipPath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) === TRUE) {
                // Store CSV files and add to ZIP
                foreach ($csvContents as $filename => $content) {
                    $csvPath = storage_path('app/flagged_transactions/' . $timestamp . '_' . $filename);
                    file_put_contents($csvPath, $content);
                    $csvPaths[] = $csvPath;

                    // Add file to ZIP archive
                    $zip->addFile($csvPath, $filename);
                }
                $zip->close();

                // Send email with ZIP attachment
                Mail::send('emails.flagged-transactions-report-v2', [
                    'activities' => $flaggedActivities,
                    'date' => Carbon::now()->format('Y-m-d H:i:s'),
                    'parameters' => $monitoringParams
                ], function ($message) use ($zipPath) {
                    $message->to($this->getConfig('report_recipients'))
                        ->from('reports@techpay.co.zm', 'TechPay')
                        ->subject('Transaction Exception Report - ' . Carbon::now()->format('Y-m-d'))
                        ->attach($zipPath, [
                            'as' => 'flagged_transactions_' . Carbon::now()->format('Y-m-d') . '.zip',
                            'mime' => 'application/zip'
                        ]);
                });

                // Clean up files
                foreach ($csvPaths as $path) {
                    unlink($path);
                }
                unlink($zipPath);
            } else {
                throw new \Exception('Failed to create ZIP archive');
            }

            $this->info('Exception report sent successfully');
        } catch (\Exception $e) {
            Log::error('Failed to send transaction exception report: ' . $e->getMessage());
            $this->error('Failed to send report: ' . $e->getMessage());
        }
    }
}
