<?php

namespace App\Common;

use App\Models\AllTransactions;
use App\Models\CacheRecord;
use Illuminate\Support\Facades\DB;

class SystemStatsGenerator
{
    public static function getSummary($startDate, $endDate)
    {
        // For dashboard stats, use the cache table
        if ($startDate === date('Y-m-d 00:00:00') && $endDate === date('Y-m-d 23:59:59')) {
            $stats = CacheRecord::getWithTimestamp('dashboard_stats_today');
            if ($stats['data']) {
                return $stats['data'];
            }
        }

        // Use materialized view for faster queries
        $stats = DB::select("
            SELECT 
                COALESCE(SUM(payments_success_volume), 0) as payments_success_volume,
                COALESCE(SUM(payments_failed_volume), 0) as payments_failed_volume,
                COALESCE(SUM(payments_success_value), 0) as payments_success_value,
                COALESCE(SUM(payments_failed_value), 0) as payments_failed_value,
                COALESCE(SUM(auth_success_volume), 0) as auth_success_volume,
                COALESCE(SUM(auth_failed_volume), 0) as auth_failed_volume,
                COALESCE(SUM(auth_success_value), 0) as auth_success_value,
                COALESCE(SUM(auth_failed_value), 0) as auth_failed_value
            FROM mv_daily_transaction_stats 
            WHERE stat_date >= ?::date AND stat_date <= ?::date
        ", [date('Y-m-d', strtotime($startDate)), date('Y-m-d', strtotime($endDate))])[0];

        $data = [
            [
                'payments' => [
                    'volume' => [
                        'total' => (int)$stats->payments_success_volume + (int)$stats->payments_failed_volume,
                        'failed' => (int)$stats->payments_failed_volume,
                        'success' => (int)$stats->payments_success_volume,
                    ],
                    'value' => [
                        'total' => number_format($stats->payments_success_value + $stats->payments_failed_value, 0),
                        'failed' => number_format($stats->payments_failed_value, 0),
                        'success' => number_format($stats->payments_success_value, 0),
                    ],
                ],
                'authorizations' => [
                    'volume' => [
                        'total' => (int)$stats->auth_success_volume + (int)$stats->auth_failed_volume,
                        'failed' => (int)$stats->auth_failed_volume,
                        'success' => (int)$stats->auth_success_volume,
                    ],
                    'value' => [
                        'total' => number_format($stats->auth_success_value + $stats->auth_failed_value, 0),
                        'failed' => number_format($stats->auth_failed_value, 0),
                        'success' => number_format($stats->auth_success_value, 0),
                    ],
                ],
            ]
        ];

        // Cache today's stats
        if ($startDate === date('Y-m-d 00:00:00') && $endDate === date('Y-m-d 23:59:59')) {
            CacheRecord::put(
                'dashboard_stats_today',
                $data,
                'dashboard_stats',
                'today',
                null,
                'stats_generator'
            );
        }

        return $data;
    }

    public static function getByMerchant($startDate, $endDate, $currency = 'USD')
    {
        // Use the partial index for faster merchant queries
        $providers = AllTransactions::where('status', 'PROCESSED')
            ->where('txn_currency', $currency)
            ->where('txn_date', '>=', $startDate)
            ->where('txn_date', '<=', $endDate)
            ->distinct('merchant')
            ->orderBy('merchant', 'asc')
            ->get(['merchant'])
            ->pluck('merchant')
            ->toArray();

        $data = [];
        foreach ($providers as $merchant) {
            // Get all stats for this merchant in a single query
            $stats = AllTransactions::where('merchant', $merchant)
                ->where('txn_currency', $currency)
                ->where('txn_date', '>=', $startDate)
                ->where('txn_date', '<=', $endDate)
                ->select([
                    DB::raw("SUM(CASE WHEN txn_type IN ('PAYMENT', 'credit card') AND result = 'SUCCESS' THEN 1 ELSE 0 END) as payments_success_volume"),
                    DB::raw("SUM(CASE WHEN txn_type IN ('PAYMENT', 'credit card') AND result = 'FAILURE' THEN 1 ELSE 0 END) as payments_failed_volume"),
                    DB::raw("SUM(CASE WHEN txn_type IN ('PAYMENT', 'credit card') AND result = 'SUCCESS' THEN txn_amount ELSE 0 END) as payments_success_value"),
                    DB::raw("SUM(CASE WHEN txn_type IN ('PAYMENT', 'credit card') AND result = 'FAILURE' THEN txn_amount ELSE 0 END) as payments_failed_value"),
                    DB::raw("SUM(CASE WHEN txn_type IN ('AUTHENTICATION', 'credit card') AND result = 'SUCCESS' THEN 1 ELSE 0 END) as auth_success_volume"),
                    DB::raw("SUM(CASE WHEN txn_type IN ('AUTHENTICATION', 'credit card') AND result = 'FAILURE' THEN 1 ELSE 0 END) as auth_failed_volume"),
                    DB::raw("SUM(CASE WHEN txn_type IN ('AUTHENTICATION', 'credit card') AND result = 'SUCCESS' THEN txn_amount ELSE 0 END) as auth_success_value"),
                    DB::raw("SUM(CASE WHEN txn_type IN ('AUTHENTICATION', 'credit card') AND result = 'FAILURE' THEN txn_amount ELSE 0 END) as auth_failed_value"),
                ])
                ->first();

            $data[] = [
                'name' => $merchant,
                'payments' => [
                    'volume' => [
                        'total' => $stats->payments_success_volume + $stats->payments_failed_volume,
                        'failed' => $stats->payments_failed_volume,
                        'success' => $stats->payments_success_volume,
                    ],
                    'value' => [
                        'total' => number_format($stats->payments_success_value + $stats->payments_failed_value, 2),
                        'failed' => number_format($stats->payments_failed_value, 2),
                        'success' => number_format($stats->payments_success_value, 2),
                    ],
                ],
                'authorizations' => [
                    'volume' => [
                        'total' => $stats->auth_success_volume + $stats->auth_failed_volume,
                        'failed' => $stats->auth_failed_volume,
                        'success' => $stats->auth_success_volume,
                    ],
                    'value' => [
                        'total' => number_format($stats->auth_success_value + $stats->auth_failed_value, 2),
                        'failed' => number_format($stats->auth_failed_value, 2),
                        'success' => number_format($stats->auth_success_value, 2),
                    ],
                ],
            ];
        }

        return $data;
    }

    public static function miscStats($startDate, $endDate)
    {
        // Get all stats in a single query using the partial index
        $stats = AllTransactions::select([
            DB::raw('COUNT(DISTINCT merchant) as merchants_count'),
            DB::raw("SUM(CASE WHEN result = 'SUCCESS' AND txn_type IN ('PAYMENT', 'credit card') THEN 1 ELSE 0 END) as successful_payments_volume"),
            DB::raw("SUM(CASE WHEN result = 'SUCCESS' AND txn_type IN ('PAYMENT', 'credit card') THEN txn_amount ELSE 0 END) as successful_payments_value"),
        ])->first();

        return [
            [
                'value' => '-',
                'volume' => $stats->merchants_count,
                'name' => 'REGISTERED MERCHANTS'
            ],
            [
                'value' => number_format($stats->successful_payments_value, 2),
                'volume' => number_format($stats->successful_payments_volume, 0),
                'name' => 'PAYMENTS'
            ],
        ];
    }
}
