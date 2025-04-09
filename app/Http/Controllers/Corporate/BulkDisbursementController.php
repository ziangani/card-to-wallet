<?php

namespace App\Http\Controllers\Corporate;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use App\Models\BulkDisbursement;
use App\Models\DisbursementItem;
use App\Models\WalletProvider;
use App\Models\ApprovalRequest;
use Illuminate\Support\Str;
use League\Csv\Reader;
use League\Csv\Writer;

class BulkDisbursementController extends Controller
{
    /**
     * Display a listing of the bulk disbursements.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $company = $user->company;

        // Get disbursements with filtering
        $query = BulkDisbursement::where('company_id', $company->id);

        // Filter by status
        if ($request->has('status') && $request->status != 'all') {
            $query->where('status', $request->status);
        }

        // Filter by date range
        if ($request->has('start_date') && $request->has('end_date')) {
            $query->whereBetween('created_at', [$request->start_date . ' 00:00:00', $request->end_date . ' 23:59:59']);
        }

        // Filter by amount range
        if ($request->has('min_amount')) {
            $query->where('total_amount', '>=', $request->min_amount);
        }

        if ($request->has('max_amount')) {
            $query->where('total_amount', '<=', $request->max_amount);
        }

        // Order by
        $query->orderBy('created_at', 'desc');

        // Paginate
        $disbursements = $query->paginate(10);

        return view('corporate.disbursements.index', compact(
            'company',
            'disbursements'
        ));
    }

    /**
     * Show the form for creating a new bulk disbursement.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        $user = Auth::user();
        $company = $user->company;
        $wallet = $company->corporateWallet;
        $walletProviders = WalletProvider::where('is_active', true)->get();

        return view('corporate.disbursements.create', compact(
            'company',
            'wallet',
            'walletProviders'
        ));
    }

    /**
     * Validate the uploaded file.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function validateFile(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'file' => 'required|file|mimes:csv,txt,xlsx|max:10240',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $user = Auth::user();
        $company = $user->company;
        $wallet = $company->corporateWallet;

        // Handle file upload
        $file = $request->file('file');
        $filePath = $file->store('disbursements/' . $company->id, 'public');

        // Create a draft disbursement
        $disbursement = BulkDisbursement::create([
            'uuid' => Str::uuid(),
            'company_id' => $company->id,
            'corporate_wallet_id' => $wallet->id,
            'name' => $request->name,
            'description' => $request->description,
            'file_path' => $filePath,
            'total_amount' => 0, // Will be calculated during validation
            'total_fee' => 0, // Will be calculated during validation
            'transaction_count' => 0, // Will be calculated during validation
            'currency' => $wallet->currency,
            'status' => 'draft',
            'initiated_by' => $user->id,
            'reference_number' => 'BULK-' . strtoupper(Str::random(8)),
        ]);

        // Store the disbursement ID in the session for the next step
        session(['disbursement_id' => $disbursement->id]);

        // Process the file
        $errors = [];
        $validItems = [];
        $totalAmount = 0;
        $totalFee = 0;
        $rowNumber = 0;

        // Get the company's fee percentage
        $feePercentage = $company->getCurrentFeePercentage();

        // Get wallet providers
        $walletProviders = WalletProvider::where('is_active', true)->pluck('id', 'api_code')->toArray();

        // Read the file
        $fileExtension = $file->getClientOriginalExtension();

        if ($fileExtension == 'csv' || $fileExtension == 'txt') {
            // Process CSV file
            $csv = Reader::createFromPath(Storage::disk('public')->path($filePath), 'r');
            $csv->setHeaderOffset(0);

            $headers = $csv->getHeader();
            $expectedHeaders = ['wallet_number', 'amount', 'recipient_name'];

            // Check if the headers are valid
            $missingHeaders = array_diff($expectedHeaders, $headers);
            if (!empty($missingHeaders)) {
                $errors[] = [
                    'row' => 0,
                    'error' => 'Missing required headers: ' . implode(', ', $missingHeaders),
                ];
            } else {
                foreach ($csv->getRecords() as $record) {
                    $rowNumber++;

                    // Validate the record
                    $rowErrors = $this->validateRow($record, $rowNumber, $walletProviders);

                    if (!empty($rowErrors)) {
                        $errors = array_merge($errors, $rowErrors);
                    } else {
                        // Calculate fee
                        $amount = floatval($record['amount']);
                        $fee = $amount * ($feePercentage / 100);

                        // Determine wallet provider
                        $walletNumber = $record['wallet_number'];
                        $walletProviderId = $this->determineWalletProvider($walletNumber, $walletProviders);

                        // Add to valid items
                        $validItems[] = [
                            'bulk_disbursement_id' => $disbursement->id,
                            'wallet_provider_id' => $walletProviderId,
                            'wallet_number' => $walletNumber,
                            'recipient_name' => $record['recipient_name'] ?? '',
                            'amount' => $amount,
                            'fee' => $fee,
                            'currency' => $wallet->currency,
                            'status' => 'pending',
                            'reference' => 'ITEM-' . strtoupper(Str::random(8)),
                            'row_number' => $rowNumber,
                        ];

                        $totalAmount += $amount;
                        $totalFee += $fee;
                    }
                }
            }
        } else if ($fileExtension == 'xlsx') {
            // For XLSX files, we would use a library like PhpSpreadsheet
            // For simplicity, we'll just add an error for now
            $errors[] = [
                'row' => 0,
                'error' => 'XLSX file processing is not implemented in this example.',
            ];
        }

        // Update the disbursement with the calculated totals
        $disbursement->update([
            'total_amount' => $totalAmount,
            'total_fee' => $totalFee,
            'transaction_count' => count($validItems),
        ]);

        // Store the validation results in the session
        session([
            'validation_errors' => $errors,
            'valid_items' => $validItems,
        ]);

        return redirect()->route('corporate.disbursements.show-validation');
    }

    /**
     * Show the validation results.
     *
     * @return \Illuminate\View\View
     */
    public function showValidation()
    {
        $user = Auth::user();
        $company = $user->company;
        $wallet = $company->corporateWallet;
        $walletProviders = WalletProvider::where('is_active', true)->get();

        // Get the disbursement ID from the session
        $disbursementId = session('disbursement_id');

        if (!$disbursementId) {
            return redirect()->route('corporate.disbursements.create')
                ->with('error', 'No disbursement in progress. Please start a new one.');
        }

        // Get the disbursement
        $disbursement = BulkDisbursement::findOrFail($disbursementId);

        // Get the validation results from the session
        $errors = session('validation_errors', []);
        $validItems = session('valid_items', []);

        // Prepare validation results for the view
        $validationResults = [
            'total_rows' => count($validItems) + count($errors),
            'valid_count' => count($validItems),
            'error_count' => count($errors),
            'total_amount' => array_sum(array_column($validItems, 'amount')),
            'total_fee' => array_sum(array_column($validItems, 'fee')),
            'fee_percentage' => $company->getCurrentFeePercentage(),
            'sample_data' => array_slice($validItems, 0, 10), // Show first 10 items
            'error_summary' => $this->summarizeErrors($errors),
        ];

        return view('corporate.disbursements.show-validation', compact(
            'company',
            'wallet',
            'walletProviders',
            'disbursement',
            'validationResults'
        ));
    }

    /**
     * Summarize validation errors by type.
     *
     * @param  array  $errors
     * @return array
     */
    private function summarizeErrors($errors)
    {
        $summary = [];

        foreach ($errors as $error) {
            $errorMessage = $error['error'];
            if (!isset($summary[$errorMessage])) {
                $summary[$errorMessage] = 0;
            }
            $summary[$errorMessage]++;
        }

        return $summary;
    }

    /**
     * Show the review page.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function review(Request $request)
    {
        $user = Auth::user();
        $company = $user->company;
        $wallet = $company->corporateWallet;
        $walletProviders = WalletProvider::where('is_active', true)->get();

        // Get the disbursement ID from the session
        $disbursementId = session('disbursement_id');

        if (!$disbursementId) {
            return redirect()->route('corporate.disbursements.create')
                ->with('error', 'No disbursement in progress. Please start a new one.');
        }

        // Get the disbursement
        $disbursement = BulkDisbursement::findOrFail($disbursementId);

        // Get the valid items from the session
        $validItems = session('valid_items', []);

        // Check if we're skipping errors
        $skippedErrors = 0;
        if ($request->has('skip_errors')) {
            $errors = session('validation_errors', []);
            $skippedErrors = count($errors);
        }

        // Check if the wallet has sufficient balance
        $totalWithFee = $disbursement->total_amount + $disbursement->total_fee;
        $hasSufficientBalance = $wallet->balance >= $totalWithFee;

        // Get the fee percentage
        $feePercentage = $company->getCurrentFeePercentage();

        // Check if approval is required
        $requiresApproval = true; // Default to requiring approval
        $minApprovers = 1; // Default minimum approvers

        // Get the approval workflow for bulk disbursements
        $approvalWorkflow = $company->approvalWorkflows()
            ->where('entity_type', 'bulk_disbursement')
            ->where('is_active', true)
            ->first();

        if ($approvalWorkflow) {
            // Check if amount threshold is set and if the disbursement amount is below it
            if ($approvalWorkflow->amount_threshold && $totalWithFee < $approvalWorkflow->amount_threshold) {
                $requiresApproval = false;
            }

            $minApprovers = $approvalWorkflow->min_approvers;
        }

        // Count available approvers
        $availableApprovers = $company->users()
            ->whereHas('corporateRoles', function($query) {
                $query->where('name', 'approver')->orWhere('name', 'admin');
            })
            ->where('id', '!=', $user->id) // Exclude current user
            ->count();

        // Group items by provider for the breakdown
        $providerBreakdown = [];
        foreach ($validItems as $item) {
            $providerId = $item['wallet_provider_id'];
            $provider = WalletProvider::find($providerId);

            if ($provider) {
                $code = $provider->api_code;
                $name = $provider->name;

                if (!isset($providerBreakdown[$code])) {
                    $providerBreakdown[$code] = [
                        'code' => $code,
                        'name' => $name,
                        'count' => 0,
                        'amount' => 0,
                    ];
                }

                $providerBreakdown[$code]['count']++;
                $providerBreakdown[$code]['amount'] += $item['amount'];
            }
        }

        // Convert to indexed array for the view
        $providerBreakdown = array_values($providerBreakdown);

        // Paginate the items for the view
        $perPage = 10;
        $page = $request->input('page', 1);
        $offset = ($page - 1) * $perPage;
        $itemsForPage = array_slice($validItems, $offset, $perPage);
        $items = new \Illuminate\Pagination\LengthAwarePaginator(
            $itemsForPage,
            count($validItems),
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        // Convert array items to objects for consistency in the view
        foreach ($items as $key => $item) {
            $items[$key] = (object) $item;
            $items[$key]->walletProvider = WalletProvider::find($item['wallet_provider_id']);
        }

        return view('corporate.disbursements.review', compact(
            'company',
            'wallet',
            'walletProviders',
            'disbursement',
            'items',
            'skippedErrors',
            'hasSufficientBalance',
            'feePercentage',
            'requiresApproval',
            'minApprovers',
            'availableApprovers',
            'providerBreakdown'
        ));
    }

    /**
     * Submit the disbursement.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function submit(Request $request)
    {
        $user = Auth::user();
        $company = $user->company;
        $wallet = $company->corporateWallet;

        // Get the disbursement ID from the session
        $disbursementId = session('disbursement_id');

        if (!$disbursementId) {
            return redirect()->route('corporate.disbursements.create')
                ->with('error', 'No disbursement in progress. Please start a new one.');
        }

        // Get the disbursement
        $disbursement = BulkDisbursement::findOrFail($disbursementId);

        // Get the valid items from the session
        $validItems = session('valid_items', []);

        // Check if the wallet has sufficient balance
        $totalWithFee = $disbursement->getTotalWithFee();
        if (!$wallet->hasSufficientBalance($totalWithFee)) {
            return redirect()->route('corporate.disbursements.review')
                ->with('error', 'Insufficient wallet balance. Please deposit funds and try again.');
        }

        // Create the disbursement items
        foreach ($validItems as $item) {
            DisbursementItem::create($item);
        }

        // Submit the disbursement for approval
        $disbursement->submitForApproval();

        // Create an approval request
        $approvalRequest = ApprovalRequest::create([
            'uuid' => Str::uuid(),
            'company_id' => $company->id,
            'entity_type' => 'bulk_disbursement',
            'entity_id' => $disbursement->id,
            'requested_by' => $user->id,
            'status' => 'pending',
            'required_approvals' => 1, // This would be determined by the approval workflow
            'received_approvals' => 0,
            'description' => 'Bulk disbursement: ' . $disbursement->name,
            'expires_at' => now()->addDays(7),
        ]);

        // Clear the session data
        session()->forget(['disbursement_id', 'validation_errors', 'valid_items']);

        return redirect()->route('corporate.disbursements.success')
            ->with('disbursement_id', $disbursement->id);
    }

    /**
     * Show the success page.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function success(Request $request)
    {
        $user = Auth::user();
        $company = $user->company;
        $wallet = $company->corporateWallet;

        // Get the disbursement ID from the session or flash data
        $disbursementId = $request->session()->get('disbursement_id', $request->session()->get('_old_input.disbursement_id'));

        if (!$disbursementId) {
            return redirect()->route('corporate.disbursements.index');
        }

        // Get the disbursement
        $disbursement = BulkDisbursement::findOrFail($disbursementId);

        // Get approval workflow details
        $minApprovers = 1; // Default value

        // Get the approval workflow for bulk disbursements
        $approvalWorkflow = $company->approvalWorkflows()
            ->where('entity_type', 'bulk_disbursement')
            ->where('is_active', true)
            ->first();

        if ($approvalWorkflow) {
            $minApprovers = $approvalWorkflow->min_approvers;
        }

        // Get the fee percentage
        $feePercentage = $company->getCurrentFeePercentage();

        return view('corporate.disbursements.success', compact(
            'company',
            'wallet',
            'disbursement',
            'minApprovers',
            'feePercentage'
        ));
    }

    /**
     * Display the specified bulk disbursement.
     *
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function show($id)
    {
        $user = Auth::user();
        $company = $user->company;

        // Get the disbursement
        $disbursement = BulkDisbursement::where('company_id', $company->id)
            ->findOrFail($id);

        // Get the disbursement items
        $items = $disbursement->items()->paginate(10);

        return view('corporate.disbursements.show', compact(
            'company',
            'disbursement',
            'items'
        ));
    }

    /**
     * Download the error report.
     *
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function downloadErrors()
    {
        // Get the validation errors from the session
        $errors = session('validation_errors', []);

        // Create a CSV file
        $csv = Writer::createFromString('');
        $csv->insertOne(['Row', 'Error']);

        foreach ($errors as $error) {
            $csv->insertOne([$error['row'], $error['error']]);
        }

        // Create a temporary file
        $tempFile = tempnam(sys_get_temp_dir(), 'errors');
        file_put_contents($tempFile, $csv->getContent());

        // Return the file as a download
        return response()->download($tempFile, 'validation_errors.csv')->deleteFileAfterSend(true);
    }

    /**
     * Download a template file.
     *
     * @param  string  $format
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function downloadTemplate($format)
    {
        if ($format == 'csv') {
            // Create a CSV template
            $csv = Writer::createFromString('');
            $csv->insertOne(['wallet_number', 'amount', 'recipient_name']);
            $csv->insertOne(['260971234567', '100.00', 'John Doe']);
            $csv->insertOne(['260961234567', '200.00', 'Jane Smith']);

            // Create a temporary file
            $tempFile = tempnam(sys_get_temp_dir(), 'template');
            file_put_contents($tempFile, $csv->getContent());

            // Return the file as a download
            return response()->download($tempFile, 'disbursement_template.csv')->deleteFileAfterSend(true);
        } else if ($format == 'xlsx') {
            // For XLSX files, we would use a library like PhpSpreadsheet
            // For simplicity, we'll just return an error for now
            return redirect()->back()->with('error', 'XLSX template is not available yet.');
        }

        return redirect()->back()->with('error', 'Invalid template format.');
    }

    /**
     * Validate a row from the CSV file.
     *
     * @param  array  $row
     * @param  int  $rowNumber
     * @param  array  $walletProviders
     * @return array
     */
    private function validateRow($row, $rowNumber, $walletProviders)
    {
        $errors = [];

        // Check if the required fields are present
        if (!isset($row['wallet_number']) || empty($row['wallet_number'])) {
            $errors[] = [
                'row' => $rowNumber,
                'error' => 'Wallet number is required.',
            ];
        } else {
            // Validate the wallet number format
            $walletNumber = $row['wallet_number'];
            if (!$this->isValidWalletNumber($walletNumber)) {
                $errors[] = [
                    'row' => $rowNumber,
                    'error' => 'Invalid wallet number format.',
                ];
            } else {
                // Check if the wallet provider is supported
                $walletProviderId = $this->determineWalletProvider($walletNumber, $walletProviders);
                if (!$walletProviderId) {
                    $errors[] = [
                        'row' => $rowNumber,
                        'error' => 'Unsupported wallet provider.',
                    ];
                }
            }
        }

        if (!isset($row['amount']) || empty($row['amount'])) {
            $errors[] = [
                'row' => $rowNumber,
                'error' => 'Amount is required.',
            ];
        } else {
            // Validate the amount
            $amount = $row['amount'];
            if (!is_numeric($amount) || $amount <= 0) {
                $errors[] = [
                    'row' => $rowNumber,
                    'error' => 'Amount must be a positive number.',
                ];
            }
        }

        return $errors;
    }

    /**
     * Check if a wallet number is valid.
     *
     * @param  string  $walletNumber
     * @return bool
     */
    private function isValidWalletNumber($walletNumber)
    {
        // Remove any non-numeric characters
        $walletNumber = preg_replace('/[^0-9]/', '', $walletNumber);

        // Check if the wallet number is valid
        // For simplicity, we'll just check if it's a 12-digit number starting with 260
        return strlen($walletNumber) == 12 && substr($walletNumber, 0, 3) == '260';
    }

    /**
     * Determine the wallet provider for a wallet number.
     *
     * @param  string  $walletNumber
     * @param  array  $walletProviders
     * @return int|null
     */
    private function determineWalletProvider($walletNumber, $walletProviders)
    {
        // Remove any non-numeric characters
        $walletNumber = preg_replace('/[^0-9]/', '', $walletNumber);

        // Check the prefix to determine the provider
        // For simplicity, we'll just check the first 5 digits
        $prefix = substr($walletNumber, 0, 5);

        // Map prefixes to provider codes
        $prefixMap = [
            '26097' => 'AIRTEL', // Airtel
            '26096' => 'MTN',    // MTN
            '26095' => 'ZAMTEL', // Zamtel
        ];

        if (isset($prefixMap[$prefix]) && isset($walletProviders[$prefixMap[$prefix]])) {
            return $walletProviders[$prefixMap[$prefix]];
        }

        return null;
    }
}
