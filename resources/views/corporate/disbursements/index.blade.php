@extends('corporate.layouts.app')

@section('title', 'Bulk Disbursements - ' . config('app.name'))
@section('meta_description', 'Manage your bulk disbursements to mobile wallets')
@section('header_title', 'Bulk Disbursements')

@section('content')
    <div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between">
        <div>
            <h2 class="text-xl font-bold text-corporate-primary">Manage Disbursements</h2>
            <p class="text-gray-500">Create and manage bulk payments to multiple recipients</p>
        </div>
        <div class="mt-4 md:mt-0">
            <a href="{{ route('corporate.disbursements.create') }}" class="inline-flex items-center px-4 py-2 bg-corporate-primary text-white rounded-lg text-sm hover:bg-opacity-90 transition">
                <i class="fas fa-plus mr-2"></i> New Disbursement
            </a>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-xl shadow-sm overflow-hidden mb-6">
        <div class="p-6">
            <h3 class="text-lg font-semibold text-corporate-primary mb-4">Filter Disbursements</h3>
            <form action="{{ route('corporate.disbursements.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <!-- Date Range -->
                <div>
                    <label for="date_from" class="block text-sm font-medium text-gray-700 mb-1">From Date</label>
                    <input type="date" id="date_from" name="date_from" value="{{ request('date_from') }}" class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-corporate-primary focus:border-corporate-primary">
                </div>

                <div>
                    <label for="date_to" class="block text-sm font-medium text-gray-700 mb-1">To Date</label>
                    <input type="date" id="date_to" name="date_to" value="{{ request('date_to') }}" class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-corporate-primary focus:border-corporate-primary">
                </div>

                <!-- Status Filter -->
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                    <select id="status" name="status" class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-corporate-primary focus:border-corporate-primary">
                        <option value="">All Statuses</option>
                        <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                        <option value="pending_approval" {{ request('status') == 'pending_approval' ? 'selected' : '' }}>Pending Approval</option>
                        <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                        <option value="processing" {{ request('status') == 'processing' ? 'selected' : '' }}>Processing</option>
                        <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                        <option value="partially_completed" {{ request('status') == 'partially_completed' ? 'selected' : '' }}>Partially Completed</option>
                        <option value="failed" {{ request('status') == 'failed' ? 'selected' : '' }}>Failed</option>
                    </select>
                </div>

                <!-- Search -->
                <div>
                    <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                    <div class="relative">
                        <input type="text" id="search" name="search" value="{{ request('search') }}" placeholder="Reference or name" class="w-full pl-10 pr-4 py-2 border rounded-lg focus:ring-2 focus:ring-corporate-primary focus:border-corporate-primary">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-search text-gray-400"></i>
                        </div>
                    </div>
                </div>

                <!-- Filter Button -->
                <div class="md:col-span-4 flex justify-end space-x-2">
                    <a href="{{ route('corporate.disbursements.index') }}" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                        Clear Filters
                    </a>
                    <button type="submit" class="px-4 py-2 bg-corporate-primary text-white rounded-lg hover:bg-opacity-90">
                        Apply Filters
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Disbursements Table -->
    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <div class="p-6">
            <div class="overflow-x-auto">
                <table class="min-w-full corporate-table">
                    <thead>
                        <tr class="bg-gray-50">
                            <th class="text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                            <th class="text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Reference</th>
                            <th class="text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                            <th class="text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                            <th class="text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Recipients</th>
                            <th class="text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Created By</th>
                            <th class="text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        <!-- Example data - would be replaced with actual data from the controller -->
                        <tr>
                            <td class="whitespace-nowrap">
                                <div class="text-sm text-gray-900">Apr 8, 2025</div>
                                <div class="text-xs text-gray-500">10:15 AM</div>
                            </td>
                            <td class="whitespace-nowrap">
                                <div class="text-sm text-gray-900">BD-25040801</div>
                            </td>
                            <td>
                                <div class="text-sm text-gray-900">April Salaries</div>
                            </td>
                            <td>
                                <div class="text-sm font-medium text-gray-900">K 25,400.00</div>
                                <div class="text-xs text-gray-500">Fee: K 635.00</div>
                            </td>
                            <td>
                                <div class="text-sm text-gray-900">45</div>
                            </td>
                            <td>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-corporate-warning bg-opacity-10 text-corporate-warning">
                                    Pending Approval
                                </span>
                            </td>
                            <td>
                                <div class="flex items-center">
                                    <div class="w-6 h-6 rounded-full bg-corporate-primary text-white flex items-center justify-center text-xs mr-2">
                                        JD
                                    </div>
                                    <div class="text-sm text-gray-900">John Doe</div>
                                </div>
                            </td>
                            <td class="text-right text-sm font-medium">
                                <a href="{{ route('corporate.disbursements.show', 'BD-25040801') }}" class="text-corporate-accent hover:underline mr-3">
                                    Details
                                </a>
                                <a href="#" class="text-corporate-primary hover:underline">
                                    Approve
                                </a>
                            </td>
                        </tr>
                        <tr>
                            <td class="whitespace-nowrap">
                                <div class="text-sm text-gray-900">Apr 5, 2025</div>
                                <div class="text-xs text-gray-500">2:30 PM</div>
                            </td>
                            <td class="whitespace-nowrap">
                                <div class="text-sm text-gray-900">BD-25040502</div>
                            </td>
                            <td>
                                <div class="text-sm text-gray-900">Vendor Payments</div>
                            </td>
                            <td>
                                <div class="text-sm font-medium text-gray-900">K 156,750.00</div>
                                <div class="text-xs text-gray-500">Fee: K 3,918.75</div>
                            </td>
                            <td>
                                <div class="text-sm text-gray-900">312</div>
                            </td>
                            <td>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-corporate-success bg-opacity-10 text-corporate-success">
                                    Completed
                                </span>
                            </td>
                            <td>
                                <div class="flex items-center">
                                    <div class="w-6 h-6 rounded-full bg-corporate-primary text-white flex items-center justify-center text-xs mr-2">
                                        JS
                                    </div>
                                    <div class="text-sm text-gray-900">Jane Smith</div>
                                </div>
                            </td>
                            <td class="text-right text-sm font-medium">
                                <a href="{{ route('corporate.disbursements.show', 'BD-25040502') }}" class="text-corporate-accent hover:underline mr-3">
                                    Details
                                </a>
                                <a href="#" class="text-corporate-primary hover:underline">
                                    Download
                                </a>
                            </td>
                        </tr>
                        <tr>
                            <td class="whitespace-nowrap">
                                <div class="text-sm text-gray-900">Apr 1, 2025</div>
                                <div class="text-xs text-gray-500">9:45 AM</div>
                            </td>
                            <td class="whitespace-nowrap">
                                <div class="text-sm text-gray-900">BD-25040101</div>
                            </td>
                            <td>
                                <div class="text-sm text-gray-900">Commission Payouts</div>
                            </td>
                            <td>
                                <div class="text-sm font-medium text-gray-900">K 78,500.00</div>
                                <div class="text-xs text-gray-500">Fee: K 1,962.50</div>
                            </td>
                            <td>
                                <div class="text-sm text-gray-900">157</div>
                            </td>
                            <td>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-corporate-success bg-opacity-10 text-corporate-success">
                                    Completed
                                </span>
                            </td>
                            <td>
                                <div class="flex items-center">
                                    <div class="w-6 h-6 rounded-full bg-corporate-primary text-white flex items-center justify-center text-xs mr-2">
                                        JD
                                    </div>
                                    <div class="text-sm text-gray-900">John Doe</div>
                                </div>
                            </td>
                            <td class="text-right text-sm font-medium">
                                <a href="{{ route('corporate.disbursements.show', 'BD-25040101') }}" class="text-corporate-accent hover:underline mr-3">
                                    Details
                                </a>
                                <a href="#" class="text-corporate-primary hover:underline">
                                    Download
                                </a>
                            </td>
                        </tr>
                        <tr>
                            <td class="whitespace-nowrap">
                                <div class="text-sm text-gray-900">Mar 28, 2025</div>
                                <div class="text-xs text-gray-500">3:15 PM</div>
                            </td>
                            <td class="whitespace-nowrap">
                                <div class="text-sm text-gray-900">BD-25032801</div>
                            </td>
                            <td>
                                <div class="text-sm text-gray-900">Contractor Payments</div>
                            </td>
                            <td>
                                <div class="text-sm font-medium text-gray-900">K 42,800.00</div>
                                <div class="text-xs text-gray-500">Fee: K 1,070.00</div>
                            </td>
                            <td>
                                <div class="text-sm text-gray-900">85</div>
                            </td>
                            <td>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-corporate-error bg-opacity-10 text-corporate-error">
                                    Partially Completed
                                </span>
                            </td>
                            <td>
                                <div class="flex items-center">
                                    <div class="w-6 h-6 rounded-full bg-corporate-primary text-white flex items-center justify-center text-xs mr-2">
                                        JS
                                    </div>
                                    <div class="text-sm text-gray-900">Jane Smith</div>
                                </div>
                            </td>
                            <td class="text-right text-sm font-medium">
                                <a href="{{ route('corporate.disbursements.show', 'BD-25032801') }}" class="text-corporate-accent hover:underline mr-3">
                                    Details
                                </a>
                                <a href="#" class="text-corporate-primary hover:underline">
                                    Retry Failed
                                </a>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="mt-6">
                <nav class="flex items-center justify-between">
                    <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                        <div>
                            <p class="text-sm text-gray-700">
                                Showing <span class="font-medium">1</span> to <span class="font-medium">4</span> of <span class="font-medium">12</span> results
                            </p>
                        </div>
                        <div>
                            <ul class="flex space-x-2">
                                <li>
                                    <a href="#" class="px-3 py-2 rounded-md border border-gray-300 text-sm font-medium text-gray-700 hover:bg-gray-50">Previous</a>
                                </li>
                                <li>
                                    <a href="#" class="px-3 py-2 rounded-md bg-corporate-primary text-white text-sm font-medium hover:bg-opacity-90">1</a>
                                </li>
                                <li>
                                    <a href="#" class="px-3 py-2 rounded-md border border-gray-300 text-sm font-medium text-gray-700 hover:bg-gray-50">2</a>
                                </li>
                                <li>
                                    <a href="#" class="px-3 py-2 rounded-md border border-gray-300 text-sm font-medium text-gray-700 hover:bg-gray-50">3</a>
                                </li>
                                <li>
                                    <a href="#" class="px-3 py-2 rounded-md border border-gray-300 text-sm font-medium text-gray-700 hover:bg-gray-50">Next</a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </nav>
            </div>
        </div>
    </div>

    <!-- Disbursement Summary -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mt-6">
        <!-- Total Disbursements -->
        <div class="bg-white rounded-xl shadow-sm p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0 w-12 h-12 rounded-full bg-corporate-primary bg-opacity-10 flex items-center justify-center text-corporate-primary">
                    <i class="fas fa-money-bill-wave"></i>
                </div>
                <div class="ml-4">
                    <h3 class="text-sm font-medium text-gray-500">Total Disbursements</h3>
                    <p class="text-2xl font-bold text-corporate-primary">12</p>
                </div>
            </div>
        </div>

        <!-- Total Amount -->
        <div class="bg-white rounded-xl shadow-sm p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0 w-12 h-12 rounded-full bg-corporate-success bg-opacity-10 flex items-center justify-center text-corporate-success">
                    <i class="fas fa-dollar-sign"></i>
                </div>
                <div class="ml-4">
                    <h3 class="text-sm font-medium text-gray-500">Total Amount</h3>
                    <p class="text-2xl font-bold text-corporate-primary">K 450,000.00</p>
                </div>
            </div>
        </div>

        <!-- Total Recipients -->
        <div class="bg-white rounded-xl shadow-sm p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0 w-12 h-12 rounded-full bg-corporate-accent bg-opacity-10 flex items-center justify-center text-corporate-accent">
                    <i class="fas fa-users"></i>
                </div>
                <div class="ml-4">
                    <h3 class="text-sm font-medium text-gray-500">Total Recipients</h3>
                    <p class="text-2xl font-bold text-corporate-primary">1,245</p>
                </div>
            </div>
        </div>

        <!-- Success Rate -->
        <div class="bg-white rounded-xl shadow-sm p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0 w-12 h-12 rounded-full bg-corporate-warning bg-opacity-10 flex items-center justify-center text-corporate-warning">
                    <i class="fas fa-chart-line"></i>
                </div>
                <div class="ml-4">
                    <h3 class="text-sm font-medium text-gray-500">Success Rate</h3>
                    <p class="text-2xl font-bold text-corporate-primary">98.5%</p>
                </div>
            </div>
        </div>
    </div>
@endsection
