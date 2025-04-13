@extends('corporate.layouts.app')

@section('title', 'Reports')

@section('content')
<div class="mb-6">
    <h2 class="text-xl font-bold text-gray-800">Reports</h2>
    <p class="text-gray-500">Generate and download transaction reports</p>
</div>

@if(session('error'))
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-6" role="alert">
        <span class="block sm:inline">{{ session('error') }}</span>
    </div>
@endif

<!-- Generate Report Form -->
<div class="bg-white rounded-xl shadow-sm overflow-hidden mb-6">
    <div class="p-6 border-b border-gray-200">
        <h3 class="text-lg font-semibold text-gray-800">Generate New Report</h3>
        <p class="text-sm text-gray-500">Select report parameters</p>
    </div>

    <form action="{{ route('corporate.reports.generate') }}" method="POST" class="p-6 space-y-6">
        @csrf

        <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
            <!-- Report Type -->
            <div>
                <label for="report_type" class="block text-sm font-medium text-gray-700 mb-1">Report Type</label>
                <select id="report_type" name="report_type" class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-primary focus:border-primary">
                    <option value="transactions">Transaction Report</option>
                    <option value="disbursements">Disbursement Report</option>
                    <option value="wallet">Wallet Activity Report</option>
                    <option value="fees">Fee Summary Report</option>
                </select>
                @error('report_type')
                    <p class="mt-1 text-sm text-error">{{ $message }}</p>
                @enderror
            </div>

            <!-- Date Range -->
            <div>
                <label for="date_from" class="block text-sm font-medium text-gray-700 mb-1">From Date</label>
                <input type="date" id="date_from" name="date_from" value="{{ old('date_from', now()->subDays(30)->format('Y-m-d')) }}" class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-primary focus:border-primary">
                @error('date_from')
                    <p class="mt-1 text-sm text-error">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="date_to" class="block text-sm font-medium text-gray-700 mb-1">To Date</label>
                <input type="date" id="date_to" name="date_to" value="{{ old('date_to', now()->format('Y-m-d')) }}" class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-primary focus:border-primary">
                @error('date_to')
                    <p class="mt-1 text-sm text-error">{{ $message }}</p>
                @enderror
            </div>

            <!-- Format -->
            <div>
                <label for="format" class="block text-sm font-medium text-gray-700 mb-1">Format</label>
                <select id="format" name="format" class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-primary focus:border-primary">
                    <option value="csv">CSV</option>
                    <option value="excel">Excel</option>
                    <option value="pdf">PDF</option>
                </select>
                @error('format')
                <p class="mt-1 text-sm text-error">{{ $message }}</p>
                @enderror
            </div>

            <!-- Additional Filters (shown/hidden based on report type) -->
            <div id="transaction_filters" class="space-y-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Status Filter</label>
                <div class="flex flex-wrap gap-4">
                    <label class="inline-flex items-center">
                        <input type="checkbox" name="status[]" value="completed" class="rounded text-primary focus:ring-primary" checked>
                        <span class="ml-2 text-sm text-gray-700">Completed</span>
                    </label>
                    <label class="inline-flex items-center">
                        <input type="checkbox" name="status[]" value="pending" class="rounded text-primary focus:ring-primary" checked>
                        <span class="ml-2 text-sm text-gray-700">Pending</span>
                    </label>
                    <label class="inline-flex items-center">
                        <input type="checkbox" name="status[]" value="failed" class="rounded text-primary focus:ring-primary">
                        <span class="ml-2 text-sm text-gray-700">Failed</span>
                    </label>
                </div>
            </div>
        </div>


        <div class="flex justify-start">
            <button type="submit" class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-opacity-90">
                <i class="fas fa-file-export mr-2"></i> Generate Report
            </button>
        </div>
    </form>
</div>

<!-- Report Descriptions -->
<div class="bg-white rounded-xl shadow-sm overflow-hidden mb-6">
    <div class="p-6 border-b border-gray-200">
        <h3 class="text-lg font-semibold text-gray-800">Report Types</h3>
        <p class="text-sm text-gray-500">Detailed information about available reports</p>
    </div>

    <div class="p-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="bg-blue-50 p-4 rounded-lg">
                <h4 class="font-semibold text-blue-800 mb-2">
                    <i class="fas fa-exchange-alt mr-2"></i> Transaction Report
                </h4>
                <p class="text-sm text-gray-700">{{ $reportDescriptions['transactions'] }}</p>
            </div>

            <div class="bg-green-50 p-4 rounded-lg">
                <h4 class="font-semibold text-green-800 mb-2">
                    <i class="fas fa-money-bill-wave mr-2"></i> Disbursement Report
                </h4>
                <p class="text-sm text-gray-700">{{ $reportDescriptions['disbursements'] }}</p>
            </div>

            <div class="bg-purple-50 p-4 rounded-lg">
                <h4 class="font-semibold text-purple-800 mb-2">
                    <i class="fas fa-wallet mr-2"></i> Wallet Activity Report
                </h4>
                <p class="text-sm text-gray-700">{{ $reportDescriptions['wallet'] }}</p>
            </div>

            <div class="bg-amber-50 p-4 rounded-lg">
                <h4 class="font-semibold text-amber-800 mb-2">
                    <i class="fas fa-receipt mr-2"></i> Fee Summary Report
                </h4>
                <p class="text-sm text-gray-700">{{ $reportDescriptions['fees'] }}</p>
            </div>
        </div>
    </div>
</div>

<!-- Note about reports -->
<div class="bg-white rounded-xl shadow-sm overflow-hidden">
    <div class="p-6 border-b border-gray-200">
        <h3 class="text-lg font-semibold text-gray-800">About Reports</h3>
        <p class="text-sm text-gray-500">Information about the reporting system</p>
    </div>

    <div class="p-6">
        <div class="text-center py-6">
            <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-blue-100 text-blue-500 mb-4">
                <i class="fas fa-info-circle text-2xl"></i>
            </div>
            <h3 class="text-lg font-medium text-gray-900 mb-1">On-Demand Reports</h3>
            <p class="text-gray-500 max-w-2xl mx-auto">
                Reports are generated on-demand and downloaded directly to your device.
                They are not stored on the server for security and privacy reasons.
                If you need to reference a report later, please save it to your local device after downloading.
            </p>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const reportTypeSelect = document.getElementById('report_type');
        const transactionFilters = document.getElementById('transaction_filters');

        // Show/hide filters based on report type
        reportTypeSelect.addEventListener('change', function() {
            if (this.value === 'transactions') {
                transactionFilters.classList.remove('hidden');
            } else {
                transactionFilters.classList.add('hidden');
            }

        });

        // Initialize on page load
        if (reportTypeSelect.value !== 'transactions') {
            transactionFilters.classList.add('hidden');
        }
    });
</script>
@endpush
@endsection
