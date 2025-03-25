<?php

namespace App\Console\Commands\Reporting;

use App\Models\AllTransactions;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class MonitorSuspiciousTransactions extends Command
{
    protected $signature = 'reporting:monitor-suspicious-transactions';
    protected $description = 'Monitor transactions for suspicious patterns and generate alerts';

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

    public function handle()
    {
        $this->info('Starting suspicious transaction monitoring...');
        
        if (!$this->validateConfig()) {
            $this->error('Invalid configuration. Please check config/transaction-monitoring.php');
            return 1;
        }

        $this->info('Configuration validated successfully.');
        
        $today = Carbon::today();
        $yesterday = Carbon::yesterday();
        
        $suspiciousActivities = [];

        try {
            $this->info('Checking for ECI 7 transactions...');
            // 1. Check for successful transactions with ECI 7
            // Order matches idx_all_txn_eci7 index
            $query = AllTransactions::where('source', 'CYBERSOURCE')
                ->where('result', 'SUCCESS')
                ->where('eci_raw', '07')
                ->where('txn_date', '>=', $yesterday)
                ->whereIn('txn_type', ['PAYMENT', 'credit card'])
                ->whereNotNull('txn_currency')
                ->whereNotNull('txn_amount')
                ->whereNotNull('merchant')
                ->select(['txn_id', 'merchant', 'txn_date', 'txn_amount', 'txn_currency', 'card_suffix', 'eci_raw'])
                ->with('merchants');
            
            $this->info('ECI 7 Query: ' . $query->toSql());
            $this->info('Parameters: ' . json_encode([
                'yesterday' => $yesterday->toDateTimeString(),
            ]));
            
            $eci7Transactions = $query->get();

            if ($eci7Transactions->count() > 0) {
                // Limit to 10 most recent transactions
                $suspiciousActivities[] = [
                    'type' => 'ECI 7 Transactions',
                    'description' => sprintf(
                        'Successful transactions with ECI 7 detected (showing %d of %d)',
                        min(10, $eci7Transactions->count()),
                        $eci7Transactions->count()
                    ),
                    'transactions' => $eci7Transactions->take(5)
                ];
            }

            $this->info('Checking for large transactions...');
            // 2. Check for large transactions (by currency)
            $largeTransactions = collect();
            $thresholds = $this->getConfig('large_amount_thresholds');
            
            foreach ($thresholds as $currency => $threshold) {
                // Order matches idx_all_txn_large_amount index
                $query = AllTransactions::where('txn_date', '>=', $yesterday)
                    ->where('result', 'SUCCESS')
                    ->where('txn_currency', $currency)
                    ->where('txn_amount', '>=', $threshold)
                    ->whereIn('txn_type', ['PAYMENT', 'credit card'])
                    ->whereNotNull('txn_currency')
                    ->whereNotNull('txn_amount')
                    ->whereNotNull('merchant')
                    ->select(['txn_id', 'merchant', 'txn_date', 'txn_amount', 'txn_currency', 'card_suffix'])
                    ->orderByDesc('txn_amount')
                    ->with('merchants');
                
                $this->info("Large transactions query for {$currency}: " . $query->toSql());
                $this->info('Parameters: ' . json_encode([
                    'yesterday' => $yesterday->toDateTimeString(),
                    'currency' => $currency,
                    'threshold' => $threshold
                ]));
                
                $transactions = $query->get();
                
                $largeTransactions = $largeTransactions->concat($transactions);
            }

            if ($largeTransactions->count() > 0) {
                // Limit to 10 most recent transactions
                $suspiciousActivities[] = [
                    'type' => 'Large Transactions',
                    'description' => sprintf(
                        'Large value transactions detected across multiple currencies (showing %d of %d)',
                        min(10, $largeTransactions->count()),
                        $largeTransactions->count()
                    ),
                    'transactions' => $largeTransactions->take(5)
                ];
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
            $this->info('Frequent cards query: ' . $frequentCardsQuery);
            $this->info('Parameters: ' . json_encode([
                'yesterday' => $yesterday->toDateTimeString(),
                'min_count' => $this->getConfig('same_card_max_daily')
            ]));
            $frequentCards = DB::select($frequentCardsQuery, [$yesterday, 'SUCCESS', $this->getConfig('same_card_max_daily')]);

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
                        ->select(['txn_id', 'merchant', 'txn_date', 'txn_amount', 'txn_currency', 'card_suffix'])
                        ->latest('txn_date')
                        ->take(5)
                        ->get();

                    $suspiciousActivities[] = [
                        'type' => 'Frequent Card Usage',
                        'description' => sprintf(
                            "Card ending in %s used %d times in 24 hours (showing %d most recent)",
                            $card->card_suffix,
                            $card->usage_count,
                            min(10, $cardTransactions->count())
                        ),
                        'transactions' => $cardTransactions
                    ];
                }
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
            $this->info('Split transactions query: ' . $splitTransactionsQuery);
            $this->info('Parameters: ' . json_encode([
                'yesterday' => $yesterday->toDateTimeString(),
                'min_count' => $this->getConfig('split_transaction_min_count')
            ]));
            $potentialSplitTransactions = DB::select($splitTransactionsQuery, [$yesterday, 'SUCCESS', $this->getConfig('split_transaction_min_count')]);
            $this->info('Initial split transaction groups found: ' . count($potentialSplitTransactions));

            foreach ($potentialSplitTransactions as $group) {
                // Get detailed transactions within time window
                $this->info("Analyzing transactions for card ending in {$group->card_suffix} with total amount {$group->total_amount} and count {$group->tx_count}...");
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

                // Check if transactions occurred within the time window
                $firstTx = $transactions->first();
                $windowEnd = Carbon::parse($firstTx->txn_date)->addMinutes($this->getConfig('split_transaction_time_window'));
                
                $windowTransactions = $transactions->filter(function ($tx) use ($firstTx, $windowEnd) {
                    $txDate = Carbon::parse($tx->txn_date);
                    return $txDate->between(Carbon::parse($firstTx->txn_date), $windowEnd);
                });

                $windowCount = $windowTransactions->count();
                if ($windowCount >= $this->getConfig('split_transaction_min_count')) {
                    $this->info("Found split transaction pattern: {$windowCount} transactions in {$this->getConfig('split_transaction_time_window')} minutes");
                    // Take only the 10 most recent transactions if there are more
                    $limitedTransactions = $windowTransactions->take(5);
                    $suspiciousActivities[] = [
                        'type' => 'Split Transactions',
                        'description' => sprintf(
                            "Multiple transactions for card ending in %s within %d minutes (showing %d of %d)",
                            $group->card_suffix,
                            $this->getConfig('split_transaction_time_window'),
                            $limitedTransactions->count(),
                            $windowTransactions->count()
                        ),
                        'transactions' => $limitedTransactions
                    ];
                }
            }

            // Generate and send report if suspicious activities found
            if (!empty($suspiciousActivities)) {
                $this->generateAndSendReport($suspiciousActivities);
            } else {
                $this->info('No suspicious activities detected');
            }

        } catch (\Exception $e) {
            Log::error('Error in suspicious transaction monitoring: ' . $e->getMessage());
            $this->error('Error monitoring transactions: ' . $e->getMessage());
        }
    }

    private function generateAndSendReport($suspiciousActivities)
    {
        // Get monitoring parameters for the report
        $monitoringParams = [
            'large_amount_thresholds' => $this->getConfig('large_amount_thresholds'),
            'same_card_max_daily' => $this->getConfig('same_card_max_daily'),
            'split_transaction_time_window' => $this->getConfig('split_transaction_time_window'),
            'split_transaction_min_count' => $this->getConfig('split_transaction_min_count')
        ];

        try {
            Mail::send('emails.suspicious-transactions-report', [
                'activities' => $suspiciousActivities,
                'date' => Carbon::now()->format('Y-m-d H:i:s'),
                'parameters' => $monitoringParams
            ], function ($message) {
                $message->to($this->getConfig('report_recipients'))
                    ->from('reports@techpay.co.zm', 'TechPay')
                    ->subject('Suspicious Transaction Activity Report - ' . Carbon::now()->format('Y-m-d'));
            });

            $this->info('Suspicious activity report sent successfully');
        } catch (\Exception $e) {
            Log::error('Failed to send suspicious transactions report: ' . $e->getMessage());
            $this->error('Failed to send report: ' . $e->getMessage());
        }
    }
}
