<?php

namespace App\Common;

use App\Models\AllTransactions;
use App\Models\Merchants;
use App\Models\SettlementReports;
use Illuminate\Support\Facades\DB;

class ImprovedSystemStatsGenerator
{
    private static $sources = ['CYBERSOURCE', 'MPGS'];

    public static function getSummaryBySource($startDate, $endDate)
    {
        return AllTransactions::select(
            'source',
            'txn_currency',
            'card_type',
            'txn_acquirer_id',
            DB::raw('COUNT(*) as total_volume'),
            DB::raw("SUM(CASE WHEN result = 'SUCCESS' THEN 1 ELSE 0 END) as success_volume"),
            DB::raw("SUM(CASE WHEN result = 'FAILURE' THEN 1 ELSE 0 END) as failed_volume"),
            DB::raw('SUM(txn_amount) as total_amount'),
            DB::raw("SUM(CASE WHEN result = 'SUCCESS' THEN txn_amount ELSE 0 END) as success_amount"),
            DB::raw("SUM(CASE WHEN result = 'FAILURE' THEN txn_amount ELSE 0 END) as failed_amount"),
            DB::raw("AVG(CASE WHEN result = 'SUCCESS' THEN txn_amount ELSE NULL END) as avg_success_amount")
        )
            ->where('txn_date', '>=', $startDate)
            ->where('txn_date', '<=', $endDate)
            ->whereIn('txn_type', ['PAYMENT', 'credit card'])
            ->whereNotNull('txn_currency')
            ->whereNotNull('txn_amount')
            ->groupBy('source', 'txn_currency', 'card_type', 'txn_acquirer_id')
            ->orderBy('source')
            ->orderBy('txn_currency')
            ->get();
    }

    public static function getTopMerchants($startDate, $endDate, $limit = 10)
    {
        return AllTransactions::select(
            'merchant',
            'source',
            'txn_currency',
            'txn_acquirer_id',
            'm.name as merchant_name',
            DB::raw('COUNT(*) as total_volume'),
            DB::raw("SUM(CASE WHEN result = 'SUCCESS' THEN 1 ELSE 0 END) as success_volume"),
            DB::raw('SUM(txn_amount) as total_amount'),
            DB::raw("SUM(CASE WHEN result = 'SUCCESS' THEN txn_amount ELSE 0 END) as success_amount"),
            DB::raw("AVG(CASE WHEN result = 'SUCCESS' THEN txn_amount ELSE NULL END) as avg_transaction_value")
        )
            ->leftJoin('merchants as m', 'm.code', '=', 'all_transactions.merchant')
            ->where('txn_date', '>=', $startDate)
            ->where('txn_date', '<=', $endDate)
            ->whereIn('txn_type', ['PAYMENT', 'credit card'])
            ->whereNotNull('txn_currency')
            ->whereNotNull('txn_amount')
            ->groupBy('merchant', 'source', 'txn_currency', 'txn_acquirer_id', 'm.name')
            ->orderBy('success_amount', 'desc')
            ->limit($limit)
            ->get();
    }

    public static function getHourlyDistribution($startDate, $endDate)
    {
        return AllTransactions::select(
            'source',
            'txn_acquirer_id',
            DB::raw('EXTRACT(HOUR FROM txn_date) as hour'),
            DB::raw('COUNT(*) as total_count'),
            DB::raw("SUM(CASE WHEN result = 'SUCCESS' THEN 1 ELSE 0 END) as success_count"),
            DB::raw('SUM(txn_amount) as total_amount')
        )
            ->where('txn_date', '>=', $startDate)
            ->where('txn_date', '<=', $endDate)
            ->whereIn('txn_type', ['PAYMENT', 'credit card'])
            ->whereNotNull('txn_currency')
            ->whereNotNull('txn_amount')
            ->groupBy('source', 'txn_acquirer_id', DB::raw('EXTRACT(HOUR FROM txn_date)'))
            ->orderBy('source')
            ->orderBy('hour')
            ->get();
    }

    public static function getDailyDistribution($startDate, $endDate)
    {
        return AllTransactions::select(
            'source',
            'txn_acquirer_id',
            DB::raw('DATE(txn_date) as date'),
            DB::raw("SUM(CASE WHEN result = 'SUCCESS' THEN 1 ELSE 0 END) as total_count"),
            DB::raw("SUM(CASE WHEN result = 'SUCCESS' THEN txn_amount ELSE 0 END) as total_amount")
        )
            ->where('txn_date', '>=', $startDate)
            ->where('txn_date', '<=', $endDate)
            ->whereIn('txn_type', ['PAYMENT', 'credit card'])
            ->whereNotNull('txn_currency')
            ->whereNotNull('txn_amount')
            ->where('result', 'SUCCESS')
            ->groupBy('source', 'txn_acquirer_id', DB::raw('DATE(txn_date)'))
            ->orderBy('date')
            ->orderBy('source')
            ->get();
    }

    public static function getCardDistribution($startDate, $endDate)
    {
        return AllTransactions::select(
            'source',
            'txn_acquirer_id',
            'card_type',
            DB::raw('COUNT(DISTINCT card_suffix) as unique_cards'),
            DB::raw('COUNT(*) as total_transactions'),
            DB::raw("SUM(CASE WHEN result = 'SUCCESS' THEN 1 ELSE 0 END) as successful_transactions"),
            DB::raw('SUM(txn_amount) as total_amount')
        )
            ->where('txn_date', '>=', $startDate)
            ->where('txn_date', '<=', $endDate)
            ->whereIn('txn_type', ['PAYMENT', 'credit card'])
            ->whereNotNull('card_type')
            ->groupBy('source', 'txn_acquirer_id', 'card_type')
            ->orderBy('source')
            ->orderBy('total_transactions', 'desc')
            ->get();
    }

    public static function getSettlementStats($startDate, $endDate)
    {
        // Set settlement date to yesterday
        $settlementDate = date('Y-m-d', strtotime('2 days ago'));

        // Get daily settlement stats
        $dailyStats = SettlementReports::select(
            'source',
            'currency',
            DB::raw('SUM(value) as total_value'),
            DB::raw('SUM(credit_value) as total_credit_value'),
            DB::raw('SUM(vat_charge) as total_vat'),
            DB::raw('SUM(rolling_reserve) as total_rolling_reserve'),
            DB::raw('SUM(bank_charge) as total_bank_charge'),
            DB::raw('SUM(bank_settlement) as total_bank_settlement'),
            DB::raw('SUM(our_charge) as total_our_charge'),
            DB::raw('SUM(merchant_settlement) as total_merchant_settlement')
        )
            ->where('settlement_date', $settlementDate)
            ->groupBy('source', 'currency')
            ->get();

        // Get cumulative settlement stats
        $cumulativeStats = SettlementReports::select(
            'source',
            'currency',
            DB::raw('SUM(value) as total_value'),
            DB::raw('SUM(credit_value) as total_credit_value'),
            DB::raw('SUM(vat_charge) as total_vat'),
            DB::raw('SUM(rolling_reserve) as total_rolling_reserve'),
            DB::raw('SUM(bank_charge) as total_bank_charge'),
            DB::raw('SUM(bank_settlement) as total_bank_settlement'),
            DB::raw('SUM(our_charge) as total_our_charge'),
            DB::raw('SUM(merchant_settlement) as total_merchant_settlement')
        )
            ->groupBy('source', 'currency')
            ->get();

        // Get total rolling reserve held
        $rollingReserveHeld = SettlementReports::select(
            'source',
            'currency',
            DB::raw('SUM(rolling_reserve) as total_rolling_reserve')
        )
            ->groupBy('source', 'currency')
            ->get();

        return [
            'daily' => $dailyStats,
            'cumulative' => $cumulativeStats,
            'rolling_reserve_held' => $rollingReserveHeld
        ];
    }

    public static function getWeeklySettlementStats($startDate, $endDate)
    {
        // Get weekly settlement stats for the specified date range
        $weeklyStats = SettlementReports::select(
            'source',
            'currency',
            DB::raw('SUM(value) as total_value'),
            DB::raw('SUM(credit_value) as total_credit_value'),
            DB::raw('SUM(vat_charge) as total_vat'),
            DB::raw('SUM(rolling_reserve) as total_rolling_reserve'),
            DB::raw('SUM(bank_charge) as total_bank_charge'),
            DB::raw('SUM(bank_settlement) as total_bank_settlement'),
            DB::raw('SUM(our_charge) as total_our_charge'),
            DB::raw('SUM(merchant_settlement) as total_merchant_settlement')
        )
            ->where('settlement_date', '>=', $startDate)
            ->where('settlement_date', '<=', $endDate)
            ->groupBy('source', 'currency')
            ->get();

        // Get cumulative settlement stats
        $cumulativeStats = SettlementReports::select(
            'source',
            'currency',
            DB::raw('SUM(value) as total_value'),
            DB::raw('SUM(credit_value) as total_credit_value'),
            DB::raw('SUM(vat_charge) as total_vat'),
            DB::raw('SUM(rolling_reserve) as total_rolling_reserve'),
            DB::raw('SUM(bank_charge) as total_bank_charge'),
            DB::raw('SUM(bank_settlement) as total_bank_settlement'),
            DB::raw('SUM(our_charge) as total_our_charge'),
            DB::raw('SUM(merchant_settlement) as total_merchant_settlement')
        )
            ->groupBy('source', 'currency')
            ->get();

        // Get total rolling reserve held
        $rollingReserveHeld = SettlementReports::select(
            'source',
            'currency',
            DB::raw('SUM(rolling_reserve) as total_rolling_reserve')
        )
            ->groupBy('source', 'currency')
            ->get();

        return [
            'daily' => $weeklyStats, // Using 'daily' key for compatibility with email template
            'cumulative' => $cumulativeStats,
            'rolling_reserve_held' => $rollingReserveHeld
        ];
    }

    public static function getCumulativeStats($startDate, $endDate)
    {
        $dailyStats = [];
        $inceptionStats = [];

        // Get daily stats for each source
        foreach (self::$sources as $source) {
            if ($source === 'MPGS') {
                // Get stats for MPGS with different banks
                $bankStats = AllTransactions::select(
                    'bank',
                    DB::raw('COUNT(*) as total_transactions'),
                    DB::raw("SUM(CASE WHEN result = 'SUCCESS' THEN 1 ELSE 0 END) as successful_transactions"),
                    DB::raw('SUM(txn_amount) as total_amount'),
                    DB::raw("SUM(CASE WHEN result = 'SUCCESS' THEN txn_amount ELSE 0 END) as successful_amount"),
                    DB::raw('COUNT(DISTINCT merchant) as total_merchants')
                )
                    ->where('source', $source)
                    ->where('txn_date', '>=', $startDate)
                    ->where('txn_date', '<=', $endDate)
                    ->whereIn('txn_type', ['PAYMENT', 'credit card'])
                    ->whereNotNull('txn_currency')
                    ->whereNotNull('txn_amount')
                    ->whereNotNull('bank')
                    ->groupBy('bank')
                    ->get();

                foreach ($bankStats as $stat) {
                    $dailyStats[] = (object)[
                        'source' => $source,
                        'acquirer' => $stat->bank,
                        'txn_acquirer_id' => null,
                        'total_transactions' => $stat->total_transactions ?? 0,
                        'successful_transactions' => $stat->successful_transactions ?? 0,
                        'total_amount' => $stat->total_amount ?? 0,
                        'successful_amount' => $stat->successful_amount ?? 0,
                        'total_merchants' => $stat->total_merchants ?? 0
                    ];
                }
            } else {
                // Get stats for non-MPGS sources
                $stats = AllTransactions::select(
                    DB::raw('COUNT(*) as total_transactions'),
                    DB::raw("SUM(CASE WHEN result = 'SUCCESS' THEN 1 ELSE 0 END) as successful_transactions"),
                    DB::raw('SUM(txn_amount) as total_amount'),
                    DB::raw("SUM(CASE WHEN result = 'SUCCESS' THEN txn_amount ELSE 0 END) as successful_amount"),
                    DB::raw('COUNT(DISTINCT merchant) as total_merchants')
                )
                    ->where('source', $source)
                    ->where('txn_date', '>=', $startDate)
                    ->where('txn_date', '<=', $endDate)
                    ->whereIn('txn_type', ['PAYMENT', 'credit card'])
                    ->whereNotNull('txn_currency')
                    ->whereNotNull('txn_amount')
                    ->first();

                $dailyStats[] = (object)[
                    'source' => $source,
                    'acquirer' => null,
                    'txn_acquirer_id' => null,
                    'total_transactions' => $stats->total_transactions ?? 0,
                    'successful_transactions' => $stats->successful_transactions ?? 0,
                    'total_amount' => $stats->total_amount ?? 0,
                    'successful_amount' => $stats->successful_amount ?? 0,
                    'total_merchants' => $stats->total_merchants ?? 0
                ];
            }
        }

        // Get inception stats for each source
        foreach (self::$sources as $source) {
            if ($source === 'MPGS') {
                // Get inception stats for MPGS with different banks
                $bankStats = AllTransactions::select(
                    'bank',
                    DB::raw('COUNT(*) as total_transactions'),
                    DB::raw("SUM(CASE WHEN result = 'SUCCESS' THEN 1 ELSE 0 END) as successful_transactions"),
                    DB::raw('SUM(txn_amount) as total_amount'),
                    DB::raw("SUM(CASE WHEN result = 'SUCCESS' THEN txn_amount ELSE 0 END) as successful_amount"),
                    DB::raw('COUNT(DISTINCT merchant) as total_merchants')
                )
                    ->where('source', $source)
                    ->whereIn('txn_type', ['PAYMENT', 'credit card'])
                    ->whereNotNull('txn_currency')
                    ->whereNotNull('txn_amount')
                    ->whereNotNull('bank')
                    ->groupBy('bank')
                    ->get();

                foreach ($bankStats as $stat) {
                    $inceptionStats[] = (object)[
                        'source' => $source,
                        'acquirer' => $stat->bank,
                        'txn_acquirer_id' => null,
                        'total_transactions' => $stat->total_transactions ?? 0,
                        'successful_transactions' => $stat->successful_transactions ?? 0,
                        'total_amount' => $stat->total_amount ?? 0,
                        'successful_amount' => $stat->successful_amount ?? 0,
                        'total_merchants' => $stat->total_merchants ?? 0
                    ];
                }
            } else {
                // Get inception stats for non-MPGS sources
                $stats = AllTransactions::select(
                    DB::raw('COUNT(*) as total_transactions'),
                    DB::raw("SUM(CASE WHEN result = 'SUCCESS' THEN 1 ELSE 0 END) as successful_transactions"),
                    DB::raw('SUM(txn_amount) as total_amount'),
                    DB::raw("SUM(CASE WHEN result = 'SUCCESS' THEN txn_amount ELSE 0 END) as successful_amount"),
                    DB::raw('COUNT(DISTINCT merchant) as total_merchants')
                )
                    ->where('source', $source)
                    ->whereIn('txn_type', ['PAYMENT', 'credit card'])
                    ->whereNotNull('txn_currency')
                    ->whereNotNull('txn_amount')
                    ->first();

                $inceptionStats[] = (object)[
                    'source' => $source,
                    'acquirer' => null,
                    'txn_acquirer_id' => null,
                    'total_transactions' => $stats->total_transactions ?? 0,
                    'successful_transactions' => $stats->successful_transactions ?? 0,
                    'total_amount' => $stats->total_amount ?? 0,
                    'successful_amount' => $stats->successful_amount ?? 0,
                    'total_merchants' => $stats->total_merchants ?? 0
                ];
            }
        }

        // Get daily totals
        $dailyTotals = AllTransactions::select(
            DB::raw('COUNT(*) as total_transactions'),
            DB::raw("SUM(CASE WHEN result = 'SUCCESS' THEN 1 ELSE 0 END) as successful_transactions"),
            DB::raw('SUM(txn_amount) as total_amount'),
            DB::raw("SUM(CASE WHEN result = 'SUCCESS' THEN txn_amount ELSE 0 END) as successful_amount"),
            DB::raw('COUNT(DISTINCT merchant) as total_merchants')
        )
            ->where('txn_date', '>=', $startDate)
            ->where('txn_date', '<=', $endDate)
            ->whereIn('txn_type', ['PAYMENT', 'credit card'])
            ->whereNotNull('txn_currency')
            ->whereNotNull('txn_amount')
            ->first();

        // Get inception totals
        $inceptionTotals = AllTransactions::select(
            DB::raw('COUNT(*) as total_transactions'),
            DB::raw("SUM(CASE WHEN result = 'SUCCESS' THEN 1 ELSE 0 END) as successful_transactions"),
            DB::raw('SUM(txn_amount) as total_amount'),
            DB::raw("SUM(CASE WHEN result = 'SUCCESS' THEN txn_amount ELSE 0 END) as successful_amount"),
            DB::raw('COUNT(DISTINCT merchant) as total_merchants')
        )
            ->whereIn('txn_type', ['PAYMENT', 'credit card'])
            ->whereNotNull('txn_currency')
            ->whereNotNull('txn_amount')
            ->first();

        // Get detailed stats by currency
        $currencyStats = AllTransactions::select(
            'source',
            'bank',
            'txn_currency',
            DB::raw('COUNT(*) as total_transactions'),
            DB::raw("SUM(CASE WHEN result = 'SUCCESS' THEN 1 ELSE 0 END) as successful_transactions"),
            DB::raw('SUM(txn_amount) as total_amount'),
            DB::raw("SUM(CASE WHEN result = 'SUCCESS' THEN txn_amount ELSE 0 END) as successful_amount"),
            DB::raw("AVG(CASE WHEN result = 'SUCCESS' THEN txn_amount ELSE NULL END) as avg_transaction_value")
        )
            ->where('txn_date', '>=', $startDate)
            ->where('txn_date', '<=', $endDate)
            ->whereIn('txn_type', ['PAYMENT', 'credit card'])
            ->whereNotNull('txn_currency')
            ->whereNotNull('txn_amount')
            ->groupBy('source', 'bank', 'txn_currency')
            ->orderBy('source')
            ->orderBy('bank')
            ->orderBy('txn_currency')
            ->get()
            ->map(function ($stat) {
                return (object)[
                    'source' => $stat->source,
                    'bank' => $stat->bank,
                    'txn_currency' => $stat->txn_currency,
                    'total_transactions' => $stat->total_transactions,
                    'successful_transactions' => $stat->successful_transactions,
                    'total_amount' => $stat->total_amount,
                    'successful_amount' => $stat->successful_amount,
                    'avg_transaction_value' => $stat->avg_transaction_value
                ];
            });

        // Add grand totals by currency only
        $grandTotals = $currencyStats->groupBy('txn_currency')->map(function ($group) {
            return (object)[
                'source' => 'TOTAL',
                'bank' => null,
                'txn_currency' => $group->first()->txn_currency,
                'total_transactions' => $group->sum('total_transactions'),
                'successful_transactions' => $group->sum('successful_transactions'),
                'total_amount' => $group->sum('total_amount'),
                'successful_amount' => $group->sum('successful_amount'),
                'is_total' => true
            ];
        });

        return [
            'daily' => [
                'by_source' => collect($dailyStats),
                'totals' => $dailyTotals,
                'by_currency' => $currencyStats->concat($grandTotals)
            ],
            'inception' => [
                'by_source' => collect($inceptionStats),
                'totals' => $inceptionTotals
            ],
            'currency_distribution' => $currencyStats
        ];
    }
}
