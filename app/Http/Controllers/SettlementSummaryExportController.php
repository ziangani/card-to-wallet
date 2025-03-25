<?php

namespace App\Http\Controllers;

use App\Models\SettlementSummary;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SettlementSummaryExportController extends Controller
{
    public function export(Request $request)
    {
        $query = SettlementSummary::query();

        if ($request->from || $request->until) {
            $query->when(
                $request->from,
                fn ($q) => $q->whereDate('settlement_date', '>=', $request->from)
            )->when(
                $request->until,
                fn ($q) => $q->whereDate('settlement_date', '<=', $request->until)
            );

            $query->select([
                'merchant',
                'merchant_name',
                'currency',
                DB::raw('SUM(debit_value) as debit_value'),
                DB::raw('SUM(credit_value) as credit_value'),
                DB::raw('SUM(net_settlement) as net_settlement')
            ])
            ->groupBy('merchant', 'merchant_name', 'currency')
            ->orderByDesc(DB::raw('SUM(debit_value)'));
        }

        if ($request->merchant) {
            $query->where('merchant', $request->merchant);
        }

        if (!$request->from && !$request->until) {
            $query->select([
                'merchant',
                'merchant_name',
                'currency',
                'debit_value',
                'credit_value',
                'net_settlement'
            ]);
        }

        $data = $query->get();

        $filename = 'settlement-summary-' . now()->format('Y-m-d') . '.csv';
        $handle = fopen('php://temp', 'w+');

        // Add headers
        fputcsv($handle, ['Merchant ID', 'Merchant Name', 'Currency', 'Debit Value', 'Credit Value', 'Net Settlement']);

        // Add data rows
        foreach ($data as $row) {
            fputcsv($handle, [
                $row->merchant,
                $row->merchant_name,
                $row->currency,
                $row->debit_value,
                $row->credit_value,
                $row->net_settlement,
            ]);
        }

        rewind($handle);
        $csv = stream_get_contents($handle);
        fclose($handle);

        return response($csv)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', "attachment; filename=\"$filename\"");
    }
}
