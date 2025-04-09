
@extends('corporate.layouts.app')

@section('title', 'Validate Disbursement' )
@section('meta_description', 'Validate your bulk disbursement data')
@section('header_title', 'Validate Disbursement')

@section('content')
    <div class="mb-6">
        <div class="flex items-center mb-2">
            <a href="{{ route('corporate.disbursements.create') }}" class="text-corporate-primary hover:underline">
                <i class="fas fa-arrow-left mr-2"></i> Back to Upload
            </a>
        </div>
        <h2 class="text-xl font-bold text-corporate-primary">Validate Disbursement Data</h2>
        <p class="text-gray-500">Review and fix any issues with your recipient data</p>
    </div>

   <div class="bg-white rounded-xl shadow-sm overflow-hidden mb-6">
        <div class="p-6">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <div class="flex items-center">
                        <div class="w-8 h-8 rounded-full bg-corporate-primary text-white flex items-center justify-center text-sm font-medium">1</div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-corporate-primary">Upload File</p>
                            <p class="text-xs text-gray-500">Upload recipient data</p>
                        </div>
                    </div>
                    <div class="h-1 bg-corporate-primary mt-3"></div>
                </div>
                <div class="flex-1">
                    <div class="flex items-center">
                        <div class="w-8 h-8 rounded-full bg-gray-200 text-gray-500 flex items-center justify-center text-sm font-medium">2</div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-gray-500">Validate</p>
                            <p class="text-xs text-gray-500">Review validation results</p>
                        </div>
                    </div>
                    <div class="h-1 bg-gray-200 mt-3"></div>
                </div>
                <div class="flex-1">
                    <div class="flex items-center">
                        <div class="w-8 h-8 rounded-full bg-gray-200 text-gray-500 flex items-center justify-center text-sm font-medium">3</div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-gray-500">Review</p>
                            <p class="text-xs text-gray-500">Confirm details</p>
                        </div>
                    </div>
                    <div class="h-1 bg-gray-200 mt-3"></div>
                </div>
                <div class="flex-1">
                    <div class="flex items-center">
                        <div class="w-8 h-8 rounded-full bg-gray-200 text-gray-500 flex items-center justify-center text-sm font-medium">4</div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-gray-500">Submit</p>
                            <p class="text-xs text-gray-500">Process disbursement</p>
                        </div>
                    </div>
                    <div class="h-1 bg-gray-200 mt-3"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Validation Summary -->
    <div class="bg-white rounded-xl shadow-sm overflow-hidden mb-6">
        <div class="p-6">
            <h3 class="text-lg font-semibold text-corporate-primary mb-4">Validation Results</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
                <!-- Total Entries -->
                <div class="bg-gray-50 rounded-lg p-4">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 w-10 h-10 rounded-full bg-corporate-primary bg-opacity-10 flex items-center justify-center text-corporate-primary">
                            <i class="fas fa-list"></i>
                        </div>
                        <div class="ml-3">
                            <h4 class="text-sm font-medium text-gray-500">Total Entries</h4>
                            <p class="text-xl font-bold text-corporate-primary">350</p>
                        </div>
                    </div>
                </div>
                
                <!-- Valid Entries -->
                <div class="bg-gray-50 rounded-lg p-4">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 w-10 h-10 rounded-full bg-corporate-success bg-opacity-10 flex items-center justify-center text-corporate-success">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <div class="ml-3">
                            <h4 class="text-sm font-medium text-gray-500">Valid Entries</h4>
                            <p class="text-xl font-bold text-corporate-success">345</p>
                        </div>
                    </div>
                </div>
                
                <!-- Error Entries -->
                <div class="bg-gray-50 rounded-lg p-4">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 w-10 h-10 rounded-full bg-corporate-error bg-opacity-10 flex items-center justify-center text-corporate-error">
                            <i class="fas fa-exclamation-circle"></i>
                        </div>
                        <div class="ml-3">
                            <h4 class="text-sm font-medium text-gray-500">Errors</h4>
                            <p class="text-xl font-bold text-corporate-error">5</p>
                        </div>
                    </div>
                </div>
                
                <!-- Total Amount -->
                <div class="bg-gray-50 rounded-lg p-4">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 w-10 h-10 rounded-full bg-corporate-accent bg-opacity-10 flex items-center justify-center text-corporate-accent">
                            <i class="fas fa-money-bill-wave"></i>
                        </div>
                        <div class="ml-3">
                            <h4 class="text-sm font-medium text-gray-500">Total Amount</h4>
                            <p class="text-xl font-bold text-corporate-accent">K 175,450.00</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Error Summary -->
            <div class="mb-6">
                <h4 class="font-medium text-corporate-primary mb-2">Error Summary</h4>
                <div class="bg-corporate-error bg-opacity-5 border border-corporate-error border-opacity-20 rounded-lg p-4">
                    <ul class="text-sm text-corporate-error space-y-1">
                        <li><i class="fas fa-times-circle mr-2"></i> Invalid mobile number format (3 entries)</li>
                        <li><i class="fas fa-times-circle mr-2"></i> Amount below minimum threshold (1 entry)</li>
                        <li><i class="fas fa-times-circle mr-2"></i> Invalid provider code (1 entry)</li>
                    </ul>
                </div>
            </div>
            
            <!-- Action Buttons -->
            <div class="flex flex-col md:flex-row md:justify-between md:items-center space-y-4 md:space-y-0">
                <div class="flex space-x-3">
                    <a href="{{ route('corporate.disbursements.download-errors') }}" class="inline-flex items-center px-4 py-2 border border-corporate-primary text-corporate-primary rounded-lg text-sm hover:bg-corporate-primary hover:text-white transition">
                        <i class="fas fa-download mr-2"></i> Download Error Report
                    </a>
                    <a href="{{ route('corporate.disbursements.create') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 text-gray-700 rounded-lg text-sm hover:bg-gray-50 transition">
                        <i class="fas fa-upload mr-2"></i> Upload New File
                    </a>
                </div>
                <div class="flex space-x-3">
                    <a href="{{ route('corporate.disbursements.review', ['skip_errors' => 'true']) }}" class="inline-flex items-center px-4 py-2 border border-corporate-accent text-corporate-accent rounded-lg text-sm hover:bg-corporate-accent hover:text-white transition">
                        <i class="fas fa-forward mr-2"></i> Proceed with Valid Entries
                    </a>
                    <a href="{{ route('corporate.disbursements.review') }}" class="inline-flex items-center px-4 py-2 bg-corporate-primary text-white rounded-lg text-sm hover:bg-opacity-90 transition">
                        <i class="fas fa-check mr-2"></i> Continue to Review
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Error Details -->
    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <div class="p-6">
            <h3 class="text-lg font-semibold text-corporate-primary mb-4">Error Details</h3>
            
            <div class="overflow-x-auto">
                <table class="min-w-full corporate-table">
                    <thead>
                        <tr class="bg-gray-50">
                            <th class="text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Row</th>
                            <th class="text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Mobile Number</th>
                            <th class="text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Provider</th>
                            <th class="text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                            <th class="text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Recipient</th>
                            <th class="text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Error</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        <tr class="bg-corporate-error bg-opacity-5">
                            <td class="whitespace-nowrap">
                                <div class="text-sm text-gray-900">12</div>
                            </td>
                            <td>
                                <div class="text-sm text-corporate-error font-medium">97712345</div>
                            </td>
                            <td>
                                <div class="text-sm text-gray-900">MTN</div>
                            </td>
                            <td>
                                <div class="text-sm text-gray-900">K 500.00</div>
                            </td>
                            <td>
                                <div class="text-sm text-gray-900">John Banda</div>
                            </td>
                            <td>
                                <div class="text-sm text-corporate-error">Invalid mobile number (must be 9 digits)</div>
                            </td>
                        </tr>
                        <tr class="bg-corporate-error bg-opacity-5">
                            <td class="whitespace-nowrap">
                                <div class="text-sm text-gray-900">56</div>
                            </td>
                            <td>
                                <div class="text-sm text-corporate-error font-medium">9771234567</div>
                            </td>
                            <td>
                                <div class="text-sm text-gray-900">MTN</div>
                            </td>
                            <td>
                                <div class="text-sm text-gray-900">K 500.00</div>
                            </td>
                            <td>
                                <div class="text-sm text-gray-900">Mary Phiri</div>
                            </td>
                            <td>
                                <div class="text-sm text-corporate-error">Invalid mobile number (must be 9 digits)</div>
                            </td>
                        </tr>
                        <tr class="bg-corporate-error bg-opacity-5">
                            <td class="whitespace-nowrap">
                                <div class="text-sm text-gray-900">98</div>
                            </td>
                            <td>
                                <div class="text-sm text-corporate-error font-medium">97712A456</div>
                            </td>
                            <td>
                                <div class="text-sm text-gray-900">MTN</div>
                            </td>
                            <td>
                                <div class="text-sm text-gray-900">K 500.00</div>
                            </td>
                            <td>
                                <div class="text-sm text-gray-900">David Mulenga</div>
                            </td>
                            <td>
                                <div class="text-sm text-corporate-error">Invalid mobile number (must contain only digits)</div>
                            </td>
                        </tr>
                        <tr class="bg-corporate-error bg-opacity-5">
                            <td class="whitespace-nowrap">
                                <div class="text-sm text-gray-900">145</div>
                            </td>
                            <td>
                                <div class="text-sm text-gray-900">977123456</div>
                            </td>
                            <td>
                                <div class="text-sm text-gray-900">MTN</div>
                            </td>
                            <td>
                                <div class="text-sm text-corporate-error font-medium">K 5.00</div>
                            </td>
                            <td>
                                <div class="text-sm text-gray-900">Sarah Tembo</div>
                            </td>
                            <td>
                                <div class="text-sm text-corporate-error">Amount below minimum (K10.00)</div>
                            </td>
                        </tr>
                        <tr class="bg-corporate-error bg-opacity-5">
                            <td class="whitespace-nowrap">
                                <div class="text-sm text-gray-900">198</div>
                            </td>
                            <td>
                                <div class="text-sm text-gray-900">977123456</div>
                            </td>
                            <td>
                                <div class="text-sm text-corporate-error font-medium">VODAFONE</div>
                            </td>
                            <td>
                                <div class="text-sm text-gray-900">K 500.00</div>
                            </td>
                            <td>
                                <div class="text-sm text-gray-900">James Zulu</div>
                            </td>
                            <td>
                                <div class="text-sm text-corporate-error">Invalid provider (must be MTN, AIRTEL, or ZAMTEL)</div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            
            <div class="mt-6 text-center">
                <p class="text-sm text-gray-500">Please fix these errors in your file and re-upload, or proceed with only the valid entries.</p>
            </div>
        </div>
    </div>
@endsection
