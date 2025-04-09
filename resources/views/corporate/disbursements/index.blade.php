@extends('corporate.layouts.app')

@section('title', 'Bulk Disbursements')
@section('meta_description', 'Manage your bulk disbursements to mobile wallets')
@section('header_title', 'Bulk Disbursements')

@section('content')
    <div class="mb-6">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between">
            <div class="mb-4 md:mb-0">
                <h2 class="text-2xl font-bold text-gray-800">Manage Disbursements</h2>
                <p class="text-gray-500 mt-1">Create and manage bulk payments to multiple recipients</p>
            </div>
            <div class="flex space-x-3">
                <a href="#" class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary">
                    <i class="fas fa-download mr-2"></i> Export
                </a>
                <a href="{{ route('corporate.disbursements.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-primary hover:bg-primary-dark focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary">
                    <i class="fas fa-plus mr-2"></i> New Disbursement
                </a>
            </div>
        </div>
        
        <!-- Quick Stats -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-6">
            <div class="bg-white rounded-lg shadow-sm p-4 border-l-4 border-primary">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-primary bg-opacity-10 p-2 rounded-md">
                        <i class="fas fa-money-bill-wave text-primary"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-xs font-medium text-gray-500 uppercase">Total</p>
                        <p class="text-lg font-semibold text-gray-800">{{ $disbursements->total() }}</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow-sm p-4 border-l-4 border-green-500">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-green-100 p-2 rounded-md">
                        <i class="fas fa-check-circle text-green-500"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-xs font-medium text-gray-500 uppercase">Completed</p>
                        <p class="text-lg font-semibold text-gray-800">{{ $disbursements->where('status', 'completed')->count() }}</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow-sm p-4 border-l-4 border-yellow-500">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-yellow-100 p-2 rounded-md">
                        <i class="fas fa-clock text-yellow-500"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-xs font-medium text-gray-500 uppercase">Pending</p>
                        <p class="text-lg font-semibold text-gray-800">{{ $disbursements->whereIn('status', ['pending_approval', 'approved', 'processing'])->count() }}</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow-sm p-4 border-l-4 border-red-500">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-red-100 p-2 rounded-md">
                        <i class="fas fa-exclamation-circle text-red-500"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-xs font-medium text-gray-500 uppercase">Failed</p>
                        <p class="text-lg font-semibold text-gray-800">{{ $disbursements->whereIn('status', ['failed', 'partially_completed'])->count() }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-xl shadow-sm overflow-hidden mb-6">
        <div class="p-6 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-800">Filter Disbursements</h3>
                <button type="button" id="toggle-filters" class="text-sm text-primary hover:text-primary-dark flex items-center">
                    <i class="fas fa-sliders-h mr-1"></i> <span id="filter-text">Hide Filters</span>
                </button>
            </div>
        </div>
        
        <div id="filter-container" class="p-6">
            <form action="{{ route('corporate.disbursements.index') }}" method="GET">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
                    <!-- Date Range -->
                    <div>
                        <label for="date_from" class="block text-sm font-medium text-gray-700 mb-1">From Date</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-calendar-alt text-gray-400"></i>
                            </div>
                            <input type="date" id="date_from" name="date_from" value="{{ request('date_from') }}"
                                   class="w-full pl-10 pr-4 py-2 border rounded-lg focus:ring-2 focus:ring-primary focus:border-primary">
                        </div>
                    </div>
    
                    <div>
                        <label for="date_to" class="block text-sm font-medium text-gray-700 mb-1">To Date</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-calendar-alt text-gray-400"></i>
                            </div>
                            <input type="date" id="date_to" name="date_to" value="{{ request('date_to') }}"
                                   class="w-full pl-10 pr-4 py-2 border rounded-lg focus:ring-2 focus:ring-primary focus:border-primary">
                        </div>
                    </div>
    
                    <!-- Status Filter -->
                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-filter text-gray-400"></i>
                            </div>
                            <select id="status" name="status"
                                    class="w-full pl-10 pr-4 py-2 border rounded-lg focus:ring-2 focus:ring-primary focus:border-primary appearance-none">
                                <option value="">All Statuses</option>
                                <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                                <option value="pending_approval" {{ request('status') == 'pending_approval' ? 'selected' : '' }}>Pending Approval</option>
                                <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                                <option value="processing" {{ request('status') == 'processing' ? 'selected' : '' }}>Processing</option>
                                <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                                <option value="partially_completed" {{ request('status') == 'partially_completed' ? 'selected' : '' }}>Partially Completed</option>
                                <option value="failed" {{ request('status') == 'failed' ? 'selected' : '' }}>Failed</option>
                            </select>
                            <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                <i class="fas fa-chevron-down text-gray-400"></i>
                            </div>
                        </div>
                    </div>
    
                    <!-- Search -->
                    <div>
                        <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-search text-gray-400"></i>
                            </div>
                            <input type="text" id="search" name="search" value="{{ request('search') }}"
                                   placeholder="Reference or name"
                                   class="w-full pl-10 pr-4 py-2 border rounded-lg focus:ring-2 focus:ring-primary focus:border-primary">
                        </div>
                    </div>
                </div>
    
                <!-- Filter Buttons -->
                <div class="flex flex-col sm:flex-row sm:justify-end sm:space-x-3">
                    <a href="{{ route('corporate.disbursements.index') }}"
                       class="mb-2 sm:mb-0 px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 flex items-center justify-center">
                        <i class="fas fa-times mr-2"></i> Clear Filters
                    </a>
                    <button type="submit"
                            class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-opacity-90 flex items-center justify-center">
                        <i class="fas fa-search mr-2"></i> Apply Filters
                    </button>
                </div>
            </form>
        </div>
    </div>
    
    <!-- JavaScript for Filter Toggle -->
    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const toggleFilters = document.getElementById('toggle-filters');
            const filterContainer = document.getElementById('filter-container');
            const filterText = document.getElementById('filter-text');
            
            toggleFilters.addEventListener('click', function() {
                if (filterContainer.style.display === 'none') {
                    filterContainer.style.display = 'block';
                    filterText.textContent = 'Hide Filters';
                } else {
                    filterContainer.style.display = 'none';
                    filterText.textContent = 'Show Filters';
                }
            });
        });
    </script>
    @endpush

    <!-- Disbursements Table -->
    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <div class="p-6 border-b border-gray-200">
            <div class="flex justify-between items-center">
                <h3 class="text-lg font-semibold text-gray-800">Disbursement Transactions</h3>
                <span class="px-3 py-1 bg-primary bg-opacity-10 text-primary text-sm font-medium rounded-full">
                    {{ $disbursements->total() }} {{ Str::plural('Disbursement', $disbursements->total()) }}
                </span>
            </div>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50">
                        <th class="px-6 py-3 text-left font-medium text-gray-500">Date</th>
                        <th class="px-6 py-3 text-left font-medium text-gray-500">Reference</th>
                        <th class="px-6 py-3 text-left font-medium text-gray-500">Name</th>
                        <th class="px-6 py-3 text-left font-medium text-gray-500">Amount</th>
                        <th class="px-6 py-3 text-left font-medium text-gray-500">Recipients</th>
                        <th class="px-6 py-3 text-left font-medium text-gray-500">Status</th>
                        <th class="px-6 py-3 text-left font-medium text-gray-500">Created By</th>
                        <th class="px-6 py-3 text-right font-medium text-gray-500">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($disbursements as $disbursement)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $disbursement->created_at->format('M d, Y') }}</div>
                                <div class="text-xs text-gray-500">{{ $disbursement->created_at->format('h:i A') }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="font-medium text-primary">{{ $disbursement->reference_number }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-900 font-medium">{{ $disbursement->name }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm font-medium text-gray-900">{{ $disbursement->currency }} {{ number_format($disbursement->total_amount, 2) }}</div>
                                <div class="text-xs text-gray-500">Fee: {{ $disbursement->currency }} {{ number_format($disbursement->total_fee, 2) }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="inline-flex items-center px-2.5 py-1 bg-gray-100 text-gray-800 rounded-full text-xs font-medium">
                                    <i class="fas fa-users mr-1 text-gray-500"></i> {{ $disbursement->transaction_count }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @php
                                    $statusIcons = [
                                        'draft' => '<i class="fas fa-pencil-alt mr-1"></i>',
                                        'pending_approval' => '<i class="fas fa-clock mr-1"></i>',
                                        'approved' => '<i class="fas fa-check mr-1"></i>',
                                        'processing' => '<i class="fas fa-spinner mr-1"></i>',
                                        'completed' => '<i class="fas fa-check-circle mr-1"></i>',
                                        'partially_completed' => '<i class="fas fa-exclamation-circle mr-1"></i>',
                                        'failed' => '<i class="fas fa-times-circle mr-1"></i>',
                                        'cancelled' => '<i class="fas fa-ban mr-1"></i>',
                                    ];
                                    
                                    $statusClasses = [
                                        'draft' => 'bg-gray-100 text-gray-800',
                                        'pending_approval' => 'bg-yellow-100 text-yellow-800',
                                        'approved' => 'bg-blue-100 text-blue-800',
                                        'processing' => 'bg-blue-100 text-blue-800',
                                        'completed' => 'bg-green-100 text-green-800',
                                        'partially_completed' => 'bg-orange-100 text-orange-800',
                                        'failed' => 'bg-red-100 text-red-800',
                                        'cancelled' => 'bg-gray-100 text-gray-800',
                                    ];
                                    
                                    $statusIcon = $statusIcons[$disbursement->status] ?? '<i class="fas fa-question-circle mr-1"></i>';
                                    $statusClass = $statusClasses[$disbursement->status] ?? 'bg-gray-100 text-gray-800';
                                    $statusLabel = str_replace('_', ' ', ucfirst($disbursement->status));
                                @endphp
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium {{ $statusClass }}">
                                    {!! $statusIcon !!} {{ $statusLabel }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center">
                                    @php
                                        $initiator = $disbursement->initiator;
                                        $initials = $initiator ? substr($initiator->first_name, 0, 1) . substr($initiator->last_name, 0, 1) : 'NA';
                                        $name = $initiator ? $initiator->name : 'Unknown';
                                    @endphp
                                    <div class="w-8 h-8 rounded-full bg-corporate-primary text-white flex items-center justify-center text-xs mr-2">
                                        {{ $initials }}
                                    </div>
                                    <div class="text-sm text-gray-900">{{ $name }}</div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right">
                                <div class="flex justify-end space-x-2">
                                    <a href="{{ route('corporate.disbursements.show', $disbursement->id) }}"
                                       class="inline-flex items-center px-2.5 py-1.5 border border-gray-300 text-xs font-medium rounded text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary">
                                        <i class="fas fa-eye mr-1"></i> Details
                                    </a>
                                    
                                    @if($disbursement->status === 'pending_approval' && auth()->user()->hasRole('approver'))
                                        <a href="{{ route('corporate.approvals.show', $disbursement->approvalRequest->id) }}"
                                           class="inline-flex items-center px-2.5 py-1.5 border border-transparent text-xs font-medium rounded text-white bg-primary hover:bg-opacity-90 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary">
                                            <i class="fas fa-check mr-1"></i> Approve
                                        </a>
                                    @elseif($disbursement->status === 'completed' || $disbursement->status === 'partially_completed')
                                        <a href="#"
                                           class="inline-flex items-center px-2.5 py-1.5 border border-transparent text-xs font-medium rounded text-white bg-corporate-accent hover:bg-opacity-90 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-corporate-accent">
                                            <i class="fas fa-download mr-1"></i> Download
                                        </a>
                                    @elseif($disbursement->status === 'partially_completed' || $disbursement->status === 'failed')
                                        <a href="#"
                                           class="inline-flex items-center px-2.5 py-1.5 border border-transparent text-xs font-medium rounded text-white bg-corporate-warning hover:bg-opacity-90 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-corporate-warning">
                                            <i class="fas fa-redo mr-1"></i> Retry
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-10 text-center">
                                <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-gray-100 text-gray-400 mb-4">
                                    <i class="fas fa-money-bill-wave text-2xl"></i>
                                </div>
                                <h3 class="text-lg font-medium text-gray-900 mb-1">No disbursements found</h3>
                                <p class="text-gray-500 mb-4">Start by creating your first disbursement</p>
                                <a href="{{ route('corporate.disbursements.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-primary hover:bg-opacity-90">
                                    <i class="fas fa-plus mr-2"></i> Create Disbursement
                                </a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    
        <!-- Pagination -->
        @if(!$disbursements->isEmpty())
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $disbursements->appends(request()->query())->links() }}
            </div>
        @endif
    </div>

    <!-- Disbursement Summary -->
    <div class="mt-8">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Detailed Analytics</h3>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <!-- Total Amount Card -->
            <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                <div class="p-4 bg-primary bg-opacity-5 border-b border-primary border-opacity-10">
                    <div class="flex items-center justify-between">
                        <h4 class="text-sm font-medium text-gray-500 uppercase">Total Amount</h4>
                        <div class="w-10 h-10 rounded-lg bg-primary bg-opacity-10 flex items-center justify-center text-primary">
                            <i class="fas fa-dollar-sign"></i>
                        </div>
                    </div>
                </div>
                <div class="p-4">
                    <div class="flex items-center">
                        <p class="text-2xl font-bold text-gray-800">{{ $company->corporateWallet->currency }} {{ number_format($disbursements->sum('total_amount'), 2) }}</p>
                    </div>
                    <div class="mt-2 flex items-center text-xs text-gray-500">
                        <span>Total fees: {{ $company->corporateWallet->currency }} {{ number_format($disbursements->sum('total_fee'), 2) }}</span>
                    </div>
                    <div class="mt-4 pt-3 border-t">
                        <div class="flex justify-between items-center">
                            <span class="text-xs text-gray-500">Avg. transaction</span>
                            <span class="text-sm font-medium text-gray-800">
                                @php
                                    $totalCount = $disbursements->sum('transaction_count');
                                    $avgAmount = $totalCount > 0 ? $disbursements->sum('total_amount') / $totalCount : 0;
                                @endphp
                                {{ $company->corporateWallet->currency }} {{ number_format($avgAmount, 2) }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
    
            <!-- Recipients Card -->
            <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                <div class="p-4 bg-corporate-accent bg-opacity-5 border-b border-corporate-accent border-opacity-10">
                    <div class="flex items-center justify-between">
                        <h4 class="text-sm font-medium text-gray-500 uppercase">Recipients</h4>
                        <div class="w-10 h-10 rounded-lg bg-corporate-accent bg-opacity-10 flex items-center justify-center text-corporate-accent">
                            <i class="fas fa-users"></i>
                        </div>
                    </div>
                </div>
                <div class="p-4">
                    <div class="flex items-center">
                        <p class="text-2xl font-bold text-gray-800">{{ number_format($disbursements->sum('transaction_count')) }}</p>
                    </div>
                    <div class="mt-2 flex items-center text-xs text-gray-500">
                        <span>Across {{ $disbursements->count() }} disbursements</span>
                    </div>
                    <div class="mt-4 pt-3 border-t">
                        <div class="flex justify-between items-center">
                            <span class="text-xs text-gray-500">Avg. per disbursement</span>
                            <span class="text-sm font-medium text-gray-800">
                                @php
                                    $avgRecipients = $disbursements->count() > 0 ? $disbursements->sum('transaction_count') / $disbursements->count() : 0;
                                @endphp
                                {{ number_format($avgRecipients, 0) }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
    
            <!-- Success Rate Card -->
            <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                <div class="p-4 bg-corporate-success bg-opacity-5 border-b border-corporate-success border-opacity-10">
                    <div class="flex items-center justify-between">
                        <h4 class="text-sm font-medium text-gray-500 uppercase">Success Rate</h4>
                        <div class="w-10 h-10 rounded-lg bg-corporate-success bg-opacity-10 flex items-center justify-center text-corporate-success">
                            <i class="fas fa-chart-line"></i>
                        </div>
                    </div>
                </div>
                <div class="p-4">
                    @php
                        $completedCount = $disbursements->where('status', 'completed')->count();
                        $totalProcessed = $disbursements->whereIn('status', ['completed', 'partially_completed', 'failed'])->count();
                        $successRate = $totalProcessed > 0 ? ($completedCount / $totalProcessed) * 100 : 0;
                    @endphp
                    <div class="flex items-center">
                        <p class="text-2xl font-bold text-gray-800">{{ number_format($successRate, 1) }}%</p>
                    </div>
                    <div class="mt-2 flex items-center text-xs text-gray-500">
                        <span>{{ $completedCount }} successful out of {{ $totalProcessed }} processed</span>
                    </div>
                    <div class="mt-4 pt-3 border-t">
                        <div class="flex justify-between items-center">
                            <span class="text-xs text-gray-500">Failed transactions</span>
                            <span class="text-sm font-medium text-gray-800">
                                @php
                                    $failedCount = $disbursements->whereIn('status', ['failed', 'partially_completed'])->count();
                                @endphp
                                {{ $failedCount }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
    
            <!-- Processing Time Card -->
            <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                <div class="p-4 bg-corporate-warning bg-opacity-5 border-b border-corporate-warning border-opacity-10">
                    <div class="flex items-center justify-between">
                        <h4 class="text-sm font-medium text-gray-500 uppercase">Processing Time</h4>
                        <div class="w-10 h-10 rounded-lg bg-corporate-warning bg-opacity-10 flex items-center justify-center text-corporate-warning">
                            <i class="fas fa-clock"></i>
                        </div>
                    </div>
                </div>
                <div class="p-4">
                    <div class="flex items-center">
                        <p class="text-2xl font-bold text-gray-800">~2.5 min</p>
                    </div>
                    <div class="mt-2 flex items-center text-xs text-gray-500">
                        <span>Average processing time per disbursement</span>
                    </div>
                    <div class="mt-4 pt-3 border-t">
                        <div class="flex justify-between items-center">
                            <span class="text-xs text-gray-500">Fastest time</span>
                            <span class="text-sm font-medium text-gray-800">45 sec</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
