<?php

namespace App\Http\Controllers\Corporate;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use App\Models\BulkDisbursement;
use App\Models\CorporateWalletTransaction;
use Illuminate\Support\Str;
use League\Csv\Writer;

class CorporateReportController extends Controller
{
    /**
     * Display the reports index.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $user = Auth::user();
        $company = $user->company;
        
        // Get recent reports
        $reports = []; // In a real implementation, this would be fetched from a reports table
        
        // Report descriptions
        $reportDescriptions = [
            'transactions' => 'Detailed list of all transactions with status, amounts, and timestamps. Use this report to track all transaction activity.',
            'disbursements' => 'Summary of all disbursements including recipient details, amounts, and status. Useful for reconciliation and audit purposes.',
            'wallet' => 'Complete wallet activity showing deposits, withdrawals, and balance changes. Helps track your wallet usage over time.',
            'fees' => 'Breakdown of all fees charged for transactions and services. Provides transparency on costs associated with your account.'
        ];
        
        return view('corporate.reports.index', compact(
            'company',
            'reports',
            'reportDescriptions'
        ));
    }
    
    /**
     * Generate a report.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function generate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'report_type' => 'required|string|in:disbursements,transactions,wallet,fees',
            'date_from' => 'required|date',
            'date_to' => 'required|date|after_or_equal:date_from',
            'format' => 'required|string|in:csv,excel,pdf',
        ]);
        
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
        
        $user = Auth::user();
        $company = $user->company;
        
        // Generate the report based on the type
        switch ($request->report_type) {
            case 'disbursements':
                return $this->generateDisbursementsReport($company, $request->date_from, $request->date_to, $request->format);
            
            case 'transactions':
                return $this->generateTransactionsReport($company, $request->date_from, $request->date_to, $request->format);
            
            case 'wallet':
                return $this->generateWalletReport($company, $request->date_from, $request->date_to, $request->format);
                
            case 'fees':
                return $this->generateFeesReport($company, $request->date_from, $request->date_to, $request->format);
            
            default:
                return redirect()->back()->with('error', 'Invalid report type.');
        }
    }
    
    /**
     * Download a report.
     *
     * @param  int  $id
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function download($id)
    {
        $user = Auth::user();
        $company = $user->company;
        
        // In a real implementation, this would fetch the report from a reports table
        // For now, we'll just return a dummy report
        
        // Create a CSV file
        $csv = Writer::createFromString('');
        $csv->insertOne(['Report ID', 'Date', 'Type', 'Status']);
        $csv->insertOne([$id, now()->format('Y-m-d'), 'Disbursements', 'Completed']);
        
        // Create a temporary file
        $tempFile = tempnam(sys_get_temp_dir(), 'report');
        file_put_contents($tempFile, $csv->getContent());
        
        // Return the file as a download
        return response()->download($tempFile, 'report_' . $id . '.csv')->deleteFileAfterSend(true);
    }
    
    /**
     * Generate a disbursements report.
     *
     * @param  \App\Models\Company  $company
     * @param  string  $startDate
     * @param  string  $endDate
     * @param  string  $format
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    private function generateDisbursementsReport($company, $startDate, $endDate, $format)
    {
        // Get disbursements within the date range
        $disbursements = BulkDisbursement::where('company_id', $company->id)
            ->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
            ->get();
        
        // Create a CSV file
        $csv = Writer::createFromString('');
        $csv->insertOne([
            'ID',
            'Reference',
            'Name',
            'Total Amount',
            'Total Fee',
            'Transaction Count',
            'Status',
            'Created At',
            'Completed At',
        ]);
        
        foreach ($disbursements as $disbursement) {
            $csv->insertOne([
                $disbursement->id,
                $disbursement->reference_number,
                $disbursement->name,
                $disbursement->total_amount,
                $disbursement->total_fee,
                $disbursement->transaction_count,
                $disbursement->status,
                $disbursement->created_at->format('Y-m-d H:i:s'),
                $disbursement->completed_at ? $disbursement->completed_at->format('Y-m-d H:i:s') : '',
            ]);
        }
        
        // Create a temporary file
        $tempFile = tempnam(sys_get_temp_dir(), 'disbursements');
        file_put_contents($tempFile, $csv->getContent());
        
        // Return the file as a download
        return response()->download($tempFile, 'disbursements_' . $startDate . '_to_' . $endDate . '.csv')->deleteFileAfterSend(true);
    }
    
    /**
     * Generate a transactions report.
     *
     * @param  \App\Models\Company  $company
     * @param  string  $startDate
     * @param  string  $endDate
     * @param  string  $format
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    private function generateTransactionsReport($company, $startDate, $endDate, $format)
    {
        // Get the wallet
        $wallet = $company->corporateWallet;
        
        // Get transactions within the date range
        $transactions = CorporateWalletTransaction::where('corporate_wallet_id', $wallet->id)
            ->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
            ->get();
        
        // Create a CSV file
        $csv = Writer::createFromString('');
        $csv->insertOne([
            'ID',
            'Reference',
            'Type',
            'Amount',
            'Balance After',
            'Description',
            'Status',
            'Created At',
        ]);
        
        foreach ($transactions as $transaction) {
            $csv->insertOne([
                $transaction->id,
                $transaction->reference_number,
                $transaction->transaction_type,
                $transaction->amount,
                $transaction->balance_after,
                $transaction->description,
                $transaction->status,
                $transaction->created_at->format('Y-m-d H:i:s'),
            ]);
        }
        
        // Create a temporary file
        $tempFile = tempnam(sys_get_temp_dir(), 'transactions');
        file_put_contents($tempFile, $csv->getContent());
        
        // Return the file as a download
        return response()->download($tempFile, 'transactions_' . $startDate . '_to_' . $endDate . '.csv')->deleteFileAfterSend(true);
    }
    
    /**
     * Generate a wallet report.
     *
     * @param  \App\Models\Company  $company
     * @param  string  $startDate
     * @param  string  $endDate
     * @param  string  $format
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    private function generateWalletReport($company, $startDate, $endDate, $format)
    {
        // Get the wallet
        $wallet = $company->corporateWallet;
        
        // Get transactions within the date range
        $transactions = CorporateWalletTransaction::where('corporate_wallet_id', $wallet->id)
            ->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
            ->get();
        
        // Calculate summary statistics
        $totalDeposits = $transactions->where('transaction_type', 'deposit')->sum('amount');
        $totalWithdrawals = $transactions->where('transaction_type', 'withdrawal')->sum('amount');
        $totalFees = $transactions->where('transaction_type', 'fee')->sum('amount');
        $totalAdjustments = $transactions->where('transaction_type', 'adjustment')->sum('amount');
        $netChange = $totalDeposits - $totalWithdrawals - $totalFees + $totalAdjustments;
        
        // Create a CSV file
        $csv = Writer::createFromString('');
        
        // Add summary section
        $csv->insertOne(['Wallet Report', $startDate . ' to ' . $endDate]);
        $csv->insertOne(['']);
        $csv->insertOne(['Summary']);
        $csv->insertOne(['Total Deposits', $totalDeposits]);
        $csv->insertOne(['Total Withdrawals', $totalWithdrawals]);
        $csv->insertOne(['Total Fees', $totalFees]);
        $csv->insertOne(['Total Adjustments', $totalAdjustments]);
        $csv->insertOne(['Net Change', $netChange]);
        $csv->insertOne(['Current Balance', $wallet->balance]);
        $csv->insertOne(['']);
        
        // Add transactions section
        $csv->insertOne(['Transactions']);
        $csv->insertOne([
            'ID',
            'Reference',
            'Type',
            'Amount',
            'Balance After',
            'Description',
            'Status',
            'Created At',
        ]);
        
        foreach ($transactions as $transaction) {
            $csv->insertOne([
                $transaction->id,
                $transaction->reference_number,
                $transaction->transaction_type,
                $transaction->amount,
                $transaction->balance_after,
                $transaction->description,
                $transaction->status,
                $transaction->created_at->format('Y-m-d H:i:s'),
            ]);
        }
        
        // Create a temporary file
        $tempFile = tempnam(sys_get_temp_dir(), 'wallet');
        file_put_contents($tempFile, $csv->getContent());
        
        // Return the file as a download
        return response()->download($tempFile, 'wallet_' . $startDate . '_to_' . $endDate . '.csv')->deleteFileAfterSend(true);
    }
    
    /**
     * Generate a fees report.
     *
     * @param  \App\Models\Company  $company
     * @param  string  $startDate
     * @param  string  $endDate
     * @param  string  $format
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    private function generateFeesReport($company, $startDate, $endDate, $format)
    {
        // Get the wallet
        $wallet = $company->corporateWallet;
        
        // Get fee transactions within the date range
        $feeTransactions = CorporateWalletTransaction::where('corporate_wallet_id', $wallet->id)
            ->where('transaction_type', 'fee')
            ->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
            ->get();
        
        // Create a CSV file
        $csv = Writer::createFromString('');
        
        // Add header
        $csv->insertOne(['Fees Report', $startDate . ' to ' . $endDate]);
        $csv->insertOne(['']);
        
        // Add summary
        $totalFees = $feeTransactions->sum('amount');
        $csv->insertOne(['Total Fees', $totalFees]);
        $csv->insertOne(['']);
        
        // Add fee breakdown by category
        $csv->insertOne(['Fee Breakdown']);
        
        // Group fees by description/category
        $feesByCategory = $feeTransactions->groupBy('description');
        $csv->insertOne(['Category', 'Count', 'Total Amount']);
        
        foreach ($feesByCategory as $category => $fees) {
            $csv->insertOne([
                $category,
                $fees->count(),
                $fees->sum('amount')
            ]);
        }
        
        $csv->insertOne(['']);
        
        // Add detailed transactions
        $csv->insertOne(['Detailed Fee Transactions']);
        $csv->insertOne([
            'ID',
            'Reference',
            'Description',
            'Amount',
            'Date',
            'Related Transaction'
        ]);
        
        foreach ($feeTransactions as $transaction) {
            $csv->insertOne([
                $transaction->id,
                $transaction->reference_number,
                $transaction->description,
                $transaction->amount,
                $transaction->created_at->format('Y-m-d H:i:s'),
                $transaction->related_transaction_id ?? 'N/A'
            ]);
        }
        
        // Create a temporary file
        $tempFile = tempnam(sys_get_temp_dir(), 'fees');
        file_put_contents($tempFile, $csv->getContent());
        
        // Return the file as a download
        return response()->download($tempFile, 'fees_' . $startDate . '_to_' . $endDate . '.csv')->deleteFileAfterSend(true);
    }
}
