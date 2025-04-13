@extends('corporate.layouts.app')

@section('title', 'Validate Disbursement' )
@section('meta_description', 'Validate your bulk disbursement data')
@section('header_title', 'Validate Disbursement')

@section('content')
    <!-- Validation Results Modal -->
    <div id="validationResultsModal" class="fixed inset-0 z-50 overflow-y-auto hidden" aria-labelledby="modal-title"
         role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <!-- Background overlay -->
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>

            <!-- Modal panel -->
            <div
                class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl sm:w-full">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                            <div class="flex justify-between items-center mb-4">
                                <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                                    Validation Results
                                </h3>
                                <div id="modal-error-count"
                                     class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800">
                                    <i class="fas fa-exclamation-circle mr-1"></i> <span>0</span> Errors
                                </div>
                            </div>

                            <div class="mt-2 max-h-96 overflow-y-auto">
                                <div id="error-summary-container" class="mb-6">
                                    <h4 class="text-md font-medium text-gray-800 mb-3">Error Summary</h4>
                                    <div id="error-summary-list" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <!-- Error summary will be populated here -->
                                    </div>
                                </div>

                                <div id="error-details-container">
                                    <h4 class="text-md font-medium text-gray-800 mb-3">Error Details</h4>
                                    <div class="overflow-x-auto">
                                        <table class="min-w-full divide-y divide-gray-200">
                                            <thead class="bg-gray-50">
                                            <tr>
                                                <th scope="col"
                                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                    Row
                                                </th>
                                                <th scope="col"
                                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                    Error
                                                </th>
                                            </tr>
                                            </thead>
                                            <tbody id="error-details-list" class="bg-white divide-y divide-gray-200">
                                            <!-- Error details will be populated here -->
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <a id="download-errors-btn" href="{{ route('corporate.disbursements.download-errors') }}"
                       class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm">
                        <i class="fas fa-download mr-2"></i> Download Error Report
                    </a>
                    <button type="button" id="close-modal-btn"
                            class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                        Close
                    </button>
                </div>
            </div>
        </div>
    </div>
    <div class="mb-6">
        <div class="flex items-center mb-2">
            <a href="{{ route('corporate.disbursements.create') }}" class="text-corporate-primary hover:underline">
                <i class="fas fa-arrow-left mr-2"></i> Back to Upload
            </a>
        </div>
        <h2 class="text-xl font-bold text-corporate-primary">Validate Disbursement Data</h2>
        <p class="text-gray-500">Review validation results before proceeding</p>
    </div>

    <!-- Step Indicator -->
    <div class="bg-white rounded-xl shadow-sm overflow-hidden mb-6">
        <div class="p-6">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <div class="flex items-center">
                        <div
                            class="w-8 h-8 rounded-full bg-gray-200 text-gray-500 flex items-center justify-center text-sm font-medium">
                            1
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-gray-500">Upload File</p>
                            <p class="text-xs text-gray-500">Upload recipient data</p>
                        </div>
                    </div>
                    <div class="h-1 bg-gray-200 mt-3"></div>
                </div>
                <div class="flex-1">
                    <div class="flex items-center">
                        <div
                            class="w-8 h-8 bg-corporate-primary rounded-full text-white flex items-center justify-center text-sm font-medium">
                            2
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-corporate-primary">Validate</p>
                            <p class="text-xs text-gray-500">Review validation results</p>
                        </div>
                    </div>
                    <div class="h-1 bg-corporate-primary  mt-3"></div>
                </div>
                <div class="flex-1">
                    <div class="flex items-center">
                        <div
                            class="w-8 h-8 rounded-full bg-gray-200 text-gray-500 flex items-center justify-center text-sm font-medium">
                            3
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-gray-500">Review</p>
                            <p class="text-xs text-gray-500">Confirm details</p>
                        </div>
                    </div>
                    <div class="h-1 bg-gray-200 mt-3"></div>
                </div>
                <div class="flex-1">
                    <div class="flex items-center">
                        <div
                            class="w-8 h-8 rounded-full bg-gray-200 text-gray-500 flex items-center justify-center text-sm font-medium">
                            4
                        </div>
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

    <!-- Main two-column layout -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main content - Takes up 2/3 of the space -->
        <div class="lg:col-span-2">
            <!-- Validation Results Card -->
            <div class="bg-white rounded-xl shadow-sm overflow-hidden mb-6">
                <div class="p-6">
                    <!-- Validation Stats -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                        <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                            <div class="flex items-center justify-between mb-2">
                                <p class="text-sm font-medium text-gray-500">Total Rows</p>
                                <div class="w-8 h-8 rounded-full bg-gray-200 flex items-center justify-center">
                                    <i class="fas fa-file-alt text-gray-500"></i>
                                </div>
                            </div>
                            <p class="text-xl font-bold text-gray-800">{{ number_format($validationResults['total_rows']) }}</p>
                            <div class="mt-2 text-xs text-gray-500">
                                Total entries in file
                            </div>
                        </div>

                        <div class="bg-green-50 rounded-lg p-4 border border-green-200">
                            <div class="flex items-center justify-between mb-2">
                                <p class="text-sm font-medium text-gray-500">Valid Entries</p>
                                <div class="w-8 h-8 rounded-full bg-green-100 flex items-center justify-center">
                                    <i class="fas fa-check text-green-500"></i>
                                </div>
                            </div>
                            <p class="text-xl font-bold text-green-600">{{ number_format($validationResults['valid_count']) }}</p>
                            <div class="mt-2 text-xs text-gray-500">
                                {{ number_format(($validationResults['valid_count'] / $validationResults['total_rows']) * 100, 1) }}
                                % of total
                            </div>
                        </div>

                        <div class="bg-red-50 rounded-lg p-4 border border-red-200">
                            <div class="flex items-center justify-between mb-2">
                                <p class="text-sm font-medium text-gray-500">Invalid Entries</p>
                                <div class="w-8 h-8 rounded-full bg-red-100 flex items-center justify-center">
                                    <i class="fas fa-times text-red-500"></i>
                                </div>
                            </div>
                            <p class="text-xl font-bold text-red-600">{{ number_format($validationResults['error_count']) }}</p>
                            <div class="mt-2 text-xs text-gray-500">
                                {{ number_format(($validationResults['error_count'] / $validationResults['total_rows']) * 100, 1) }}
                                % of total
                            </div>
                        </div>
                    </div>

                    @if($validationResults['error_count'] > 0)
                        <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-6">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-exclamation-circle text-red-500 text-xl"></i>
                                </div>
                                <div class="ml-3">
                                    <h4 class="text-sm font-medium text-red-800">Validation Errors Found</h4>
                                    <div class="mt-2 text-sm text-red-700">
                                        <p>We found {{ $validationResults['error_count'] }} errors in your file. You can
                                            download the error report to fix these issues.</p>
                                    </div>
                                    <div class="mt-4">
                                        <button id="view-errors-btn" type="button"
                                                class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                                            <i class="fas fa-eye mr-2"></i> View Error Details
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-6">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-check-circle text-green-500 text-xl"></i>
                                </div>
                                <div class="ml-3">
                                    <h4 class="text-sm font-medium text-green-800">Validation Successful</h4>
                                    <div class="mt-2 text-sm text-green-700">
                                        <p>All entries in your file are valid and ready to be processed.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Disbursement Details -->
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-5">
                        <h4 class="font-medium text-blue-800 mb-4 flex items-center">
                            <i class="fas fa-info-circle mr-2"></i> Disbursement Details
                        </h4>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div class="bg-white p-3 rounded-md shadow-sm">
                                <p class="text-xs font-medium text-gray-500 uppercase mb-1">Name</p>
                                <p class="text-base font-medium text-gray-900">{{ $disbursement['name'] }}</p>
                            </div>

                            @if(!empty($disbursement['description']))
                                <div class="bg-white p-3 rounded-md shadow-sm">
                                    <p class="text-xs font-medium text-gray-500 uppercase mb-1">Description</p>
                                    <p class="text-base text-gray-900">{{ $disbursement['description'] }}</p>
                                </div>
                            @endif

                            <div class="bg-white p-3 rounded-md shadow-sm">
                                <p class="text-xs font-medium text-gray-500 uppercase mb-1">File Name</p>
                                <p class="text-base text-gray-900 flex items-center">
                                    <i class="fas fa-file-excel text-green-500 mr-2"></i>
                                    {{ $disbursement['file_name'] }}
                                </p>
                            </div>

                            <div class="bg-white p-3 rounded-md shadow-sm">
                                <p class="text-xs font-medium text-gray-500 uppercase mb-1">Upload Date</p>
                                <p class="text-base text-gray-900 flex items-center">
                                    <i class="fas fa-calendar-alt text-blue-500 mr-2"></i>
                                    {{ \Carbon\Carbon::parse($disbursement['created_at'])->format('M d, Y h:i A') }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sample Data Preview -->
            <div class="bg-white rounded-xl shadow-sm overflow-hidden mb-6">
                <div class="p-6 border-b border-gray-200">
                    <div class="flex justify-between items-center">
                        <h3 class="text-lg font-semibold text-gray-800">Data Preview</h3>
                        <span class="text-sm text-gray-500">
                            Showing {{ count($validationResults['sample_data']) }} of {{ number_format($validationResults['total_rows']) }} rows
                        </span>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                        <tr class="bg-gray-50">
                            <th class="px-6 py-3 text-left font-medium text-gray-500">Row</th>
                            <th class="px-6 py-3 text-left font-medium text-gray-500">Wallet Number</th>
                            <th class="px-6 py-3 text-left font-medium text-gray-500">Provider</th>
                            <th class="px-6 py-3 text-left font-medium text-gray-500">Recipient</th>
                            <th class="px-6 py-3 text-left font-medium text-gray-500">Amount</th>
                        </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                        @forelse($validationResults['sample_data'] as $index => $item)
                            <tr class="hover:bg-gray-50 transition-colors {{ isset($item['is_valid']) && !$item['is_valid'] ? 'bg-red-50' : '' }}">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="font-medium text-gray-900">{{ $item['row_number'] }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="font-medium text-primary">{{ $item['wallet_number'] }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    @if(isset($item['provider']) && $item['provider'])
                                        <div
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                            {{ $item['provider'] }}
                                        </div>
                                    @else
                                        <div
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                            Auto-detect
                                        </div>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    @if(isset($item['recipient_name']) && $item['recipient_name'])
                                        <div class="text-sm text-gray-900">{{ $item['recipient_name'] }}</div>
                                    @else
                                        <div class="text-sm text-gray-500">N/A</div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div
                                        class="text-sm font-medium text-gray-900">{{ $wallet->currency }} {{ number_format($item['amount'], 2) }}</div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-10 text-center">
                                    <div
                                        class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-gray-100 text-gray-400 mb-4">
                                        <i class="fas fa-file-alt text-2xl"></i>
                                    </div>
                                    <h3 class="text-lg font-medium text-gray-900 mb-1">No data available</h3>
                                    <p class="text-gray-500">No data is available for preview</p>
                                </td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
                    <div class="flex items-center text-sm text-gray-500">
                        <i class="fas fa-info-circle mr-2 text-blue-500"></i>
                        <p>This is a preview of your data. The full dataset will be processed when you continue.</p>
                    </div>
                </div>
            </div>

            <!-- Error Summary -->
            @if($validationResults['error_count'] > 0 && !empty($validationResults['error_summary']))
                <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                    <div class="p-6 border-b border-gray-200">
                        <div class="flex justify-between items-center">
                            <h3 class="text-lg font-semibold text-gray-800">Error Summary</h3>
                            <button id="view-error-summary-btn" type="button"
                                    class="inline-flex items-center px-3 py-1.5 border border-red-300 text-sm font-medium rounded-md text-red-700 bg-red-50 hover:bg-red-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                                <i class="fas fa-eye mr-1.5"></i> View Error Details
                            </button>
                        </div>
                    </div>

                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            @foreach($validationResults['error_summary'] as $errorType => $count)
                                <div class="bg-red-50 border border-red-100 p-4 rounded-lg">
                                    <div class="flex items-start">
                                        <div
                                            class="flex-shrink-0 w-8 h-8 rounded-full bg-red-100 text-red-600 flex items-center justify-center mr-3">
                                            <i class="fas fa-exclamation-circle"></i>
                                        </div>
                                        <div>
                                            <p class="text-sm font-medium text-gray-900">{{ $errorType }}</p>
                                            <div class="mt-1 flex items-center">
                                                <span
                                                    class="px-2 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                    {{ $count }} {{ Str::plural('occurrence', $count) }}
                                                </span>
                                                <span class="ml-2 text-xs text-gray-500">
                                                    {{ number_format(($count / $validationResults['error_count']) * 100, 1) }}% of errors
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="mt-6 bg-blue-50 border border-blue-200 rounded-lg p-4">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-info-circle text-blue-500 text-xl"></i>
                                </div>
                                <div class="ml-3">
                                    <h4 class="text-sm font-medium text-blue-800">How to fix these errors</h4>
                                    <div class="mt-2 text-sm text-blue-700">
                                        <p>Download the error report for detailed information about each error. The
                                            report includes row numbers and specific validation failures to help you
                                            correct your data.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Action Buttons -->
        </div>

        <!-- Right sidebar - Takes up 1/3 of the space -->

        <div class="bg-white rounded-xl shadow-sm overflow-hidden lg:col-span-1">
            <div class="p-6 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-800">Next Steps</h3>
            </div>

            <div class="p-6">
                <div class="flex flex-col">
                    <div class="mb-4 md:mb-0">
                        <p class="text-gray-600 mb-2">
                            @if($validationResults['error_count'] > 0)
                                You have {{ $validationResults['error_count'] }} invalid entries. You can either fix the
                                errors and re-upload, or proceed with only the valid entries.
                            @else
                                All entries are valid. You can proceed to review the disbursement details.
                            @endif
                        </p>
                    </div>

                    <div class="flex space-x-3">
                        @if($validationResults['error_count'] > 0)
                            <a href="{{ route('corporate.disbursements.create') }}"
                               class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                                <i class="fas fa-upload mr-2"></i> Re-upload File
                            </a>

                            <form action="{{ route('corporate.disbursements.review') }}" method="GET"
                                  class="inline-block">
                                <input type="hidden" name="skip_errors" value="1">
                                <button type="submit"
                                        class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-primary hover:bg-primary-dark focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary">
                                    Proceed with Valid Entries <i class="fas fa-arrow-right ml-2"></i>
                                </button>
                            </form>
                        @else
                            <a href="{{ route('corporate.disbursements.review') }}"
                               class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-primary hover:bg-primary-dark focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary">
                                Continue to Review <i class="fas fa-arrow-right ml-2"></i>
                            </a>
                        @endif
                    </div>

                    <a href="{{ route('corporate.disbursements.create') }}"
                       class="inline-flex items-center text-primary hover:text-primary-dark mt-3">
                        <i class="fas fa-arrow-left mr-2"></i> Back to Upload
                    </a>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Modal elements
            const modal = document.getElementById('validationResultsModal');
            const closeModalBtn = document.getElementById('close-modal-btn');
            const viewErrorsBtn = document.getElementById('view-errors-btn');
            const viewErrorSummaryBtn = document.getElementById('view-error-summary-btn');
            const errorCountDisplay = document.getElementById('modal-error-count');
            const errorSummaryList = document.getElementById('error-summary-list');
            const errorDetailsList = document.getElementById('error-details-list');

            // Function to open modal
            function openModal() {
                modal.classList.remove('hidden');
                fetchValidationResults();
            }

            // Function to close modal
            function closeModal() {
                modal.classList.add('hidden');
            }

            // Event listeners for opening and closing modal
            if (viewErrorsBtn) {
                viewErrorsBtn.addEventListener('click', openModal);
            }

            if (viewErrorSummaryBtn) {
                viewErrorSummaryBtn.addEventListener('click', openModal);
            }

            closeModalBtn.addEventListener('click', closeModal);

            // Close modal when clicking outside
            modal.addEventListener('click', function (event) {
                if (event.target === modal) {
                    closeModal();
                }
            });

            // Fetch validation results via AJAX
            function fetchValidationResults() {
                fetch('{{ route("corporate.disbursements.get-validation-results") }}')
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Network response was not ok');
                        }
                        return response.json();
                    })
                    .then(data => {
                        // Update error count
                        errorCountDisplay.querySelector('span').textContent = data.error_count;

                        // Update error summary
                        errorSummaryList.innerHTML = '';
                        if (data.error_summary && Object.keys(data.error_summary).length > 0) {
                            Object.entries(data.error_summary).forEach(([errorType, count]) => {
                                const errorSummaryItem = document.createElement('div');
                                errorSummaryItem.className = 'bg-red-50 border border-red-100 p-4 rounded-lg';
                                errorSummaryItem.innerHTML = `
                                <div class="flex items-start">
                                    <div class="flex-shrink-0 w-8 h-8 rounded-full bg-red-100 text-red-600 flex items-center justify-center mr-3">
                                        <i class="fas fa-exclamation-circle"></i>
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">${errorType}</p>
                                        <div class="mt-1 flex items-center">
                                            <span class="px-2 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                ${count} ${count === 1 ? 'occurrence' : 'occurrences'}
                                            </span>
                                            <span class="ml-2 text-xs text-gray-500">
                                                ${((count / data.error_count) * 100).toFixed(1)}% of errors
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            `;
                                errorSummaryList.appendChild(errorSummaryItem);
                            });
                        } else {
                            errorSummaryList.innerHTML = '<p class="text-gray-500">No error summary available</p>';
                        }

                        // Update error details
                        errorDetailsList.innerHTML = '';
                        if (data.errors && data.errors.length > 0) {
                            data.errors.forEach(error => {
                                const row = document.createElement('tr');
                                row.innerHTML = `
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">${error.row}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${error.error}</td>
                            `;
                                errorDetailsList.appendChild(row);
                            });
                        } else {
                            const emptyRow = document.createElement('tr');
                            emptyRow.innerHTML = `
                            <td colspan="2" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">No errors found</td>
                        `;
                            errorDetailsList.appendChild(emptyRow);
                        }
                    })
                    .catch(error => {
                        console.error('Error fetching validation results:', error);
                        errorSummaryList.innerHTML = '<p class="text-red-500">Error loading validation results. Please try again.</p>';
                        errorDetailsList.innerHTML = '<tr><td colspan="2" class="px-6 py-4 whitespace-nowrap text-sm text-red-500 text-center">Error loading validation results</td></tr>';
                    });
            }
        });
    </script>
@endpush
