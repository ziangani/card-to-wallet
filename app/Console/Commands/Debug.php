<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class Debug extends Command
{
    protected $signature = 'debug:settlement';
    protected $description = 'Debug merchant reconciliation data';

    public function handle()
    {
        $this->info('Debugging Merchant Data');
        $this->info('=====================');

        // First check merchant details
        $merchant = DB::selectOne("
            SELECT code, acceptor_point, name 
            FROM merchants 
            WHERE code = 'abz_tech_1205228_usd'
        ");

        if (!$merchant) {
            $this->error("Merchant not found!");
            return;
        }

        $this->info("\nMerchant Details:");
        $this->info("Code: {$merchant->code}");
        $this->info("Acceptor Point: {$merchant->acceptor_point}");
        $this->info("Name: {$merchant->name}");

        // Check settlement records
        $this->info("\nSettlement Records:");
        $settlements = DB::select("
            SELECT 
                transaction_date,
                COUNT(*) as count,
                SUM(original_amount) as total_amount,
                SUM(settlement_amount) as settled_amount
            FROM settlement_records
            WHERE merchant_id = ?
            GROUP BY transaction_date
            ORDER BY transaction_date DESC
        ", [$merchant->acceptor_point]);

        if (count($settlements) > 0) {
            foreach ($settlements as $row) {
                $this->info("\nDate: {$row->transaction_date}");
                $this->info("Count: {$row->count}");
                $this->info("Total Amount: {$row->total_amount}");
                $this->info("Settled Amount: {$row->settled_amount}");
            }
        } else {
            $this->warn("No settlement records found");
        }

        // Check transaction charges
        $this->info("\nTransaction Charges:");
        $charges = DB::select("
            SELECT 
                created_at,
                charge_type,
                base_amount,
                calculated_amount
            FROM transaction_charges
            WHERE merchant_id = ?
            ORDER BY created_at DESC
        ", [$merchant->code]);

        if (count($charges) > 0) {
            foreach ($charges as $row) {
                $this->info("\nDate: {$row->created_at}");
                $this->info("Type: {$row->charge_type}");
                $this->info("Base Amount: {$row->base_amount}");
                $this->info("Calculated Amount: {$row->calculated_amount}");
            }
        } else {
            $this->warn("No transaction charges found");
        }

        $this->info("\nReconciliation Records:");

        $results = DB::select("
            SELECT 
                mr.date,
                mr.transaction_count,
                mr.total_amount as absa_amount,
                mr.platform_fee,
                mr.application_fee,
                mr.rolling_reserve,
                mr.return_reserve,
                mr.refund_amount,
                mr.chargeback_count,
                mr.chargeback_amount,
                mr.chargeback_fees,
                mr.net_processed,
                mr.settled_amount,
                mr.status,
                mr.version
            FROM merchant_reconciliations mr
            WHERE mr.merchant_id = 'abz_tech_1205228_usd'
            ORDER BY mr.date DESC, mr.version DESC
        ");
        
        if (count($results) > 0) {
            $this->info("Found " . count($results) . " reconciliation records:");
            foreach ($results as $row) {
                $this->info("\nDate: {$row->date} (Version {$row->version}, {$row->status})");
                $this->info("Transactions: {$row->transaction_count}");
                $this->info("Absa Amount: {$row->absa_amount}");
                $this->info("Platform Fee: {$row->platform_fee}");
                $this->info("Application Fee: {$row->application_fee}");
                $this->info("Rolling Reserve: {$row->rolling_reserve}");
                $this->info("Return of Reserve: {$row->return_reserve}");
                $this->info("Refunds: {$row->refund_amount}");
                $this->info("Chargebacks: {$row->chargeback_count} count, {$row->chargeback_amount} amount, {$row->chargeback_fees} fees");
                $this->info("Net Processed: {$row->net_processed}");
                $this->info("Settled Amount: {$row->settled_amount}");
            }

            // Show totals
            $totals = DB::selectOne("
                SELECT 
                    COUNT(*) as total_records,
                    SUM(transaction_count) as total_transactions,
                    SUM(total_amount) as total_amount,
                    SUM(platform_fee) as total_platform_fee,
                    SUM(application_fee) as total_application_fee,
                    SUM(rolling_reserve) as total_rolling_reserve,
                    SUM(return_reserve) as total_return_reserve,
                    SUM(refund_amount) as total_refunds,
                    SUM(chargeback_amount) as total_chargebacks,
                    SUM(net_processed) as total_net_processed,
                    SUM(settled_amount) as total_settled
                FROM merchant_reconciliations
                WHERE merchant_id = 'abz_tech_1205228_usd'
                AND status = 'ACTIVE'
            ");

            $this->info("\nTotals (Active Records Only):");
            $this->info("Total Records: {$totals->total_records}");
            $this->info("Total Transactions: {$totals->total_transactions}");
            $this->info("Total Amount: {$totals->total_amount}");
            $this->info("Total Platform Fees: {$totals->total_platform_fee}");
            $this->info("Total Application Fees: {$totals->total_application_fee}");
            $this->info("Total Rolling Reserve: {$totals->total_rolling_reserve}");
            $this->info("Total Return Reserve: {$totals->total_return_reserve}");
            $this->info("Total Refunds: {$totals->total_refunds}");
            $this->info("Total Chargebacks: {$totals->total_chargebacks}");
            $this->info("Total Net Processed: {$totals->total_net_processed}");
            $this->info("Total Settled: {$totals->total_settled}");
        } else {
            $this->error("No reconciliation records found for merchant abz_tech_1205228_usd");
        }
    }
}
