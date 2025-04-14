@extends('corporate.layouts.app')

@section('title', 'Disbursement Details')
@section('meta_description', 'View details of your bulk disbursement')
@section('header_title', 'Disbursement Details')

@section('content')
    <div class="mb-6">
        <div class="flex items-center mb-2">
            <a href="{{ route('corporate.disbursements.index') }}" class="text-corporate-primary hover:underline">
                <i class="fas fa-arrow-left mr-2"></i> Back to Disbursements
            </a>
        </div>
        <div class="flex flex-col md:flex-row md:items-center md:justify-between">
            <div>
                <h2 class="text-xl font-bold text-corporate-primary">{{ $disbursement->name }}</h2>
                <p class="text-gray-500">Reference: {{ $disbursement->reference_number }}</p>
            </div>
            <div class="mt-4 md:mt-0 flex space-x-2">
                @if($disbursement->status === 'completed' || $disbursement->status === 'partially_completed')
                    <a href="#" class="inline-flex items-center px-4 py-2 bg-corporate-primary text-white rounded-lg text-sm hover:bg-opacity-90 transition">
                        <i class="fas fa-download mr-2"></i> Download Report
                    </a>
                @endif
                @if($disbursement->status === 'partially_completed' || $disbursement->status === 'failed')
                    <a href="#" class="inline-flex items-center px-4 py-2 border border-corporate-primary text-corporate-primary rounded-lg text-sm hover:bg-corporate-primary hover:text-white transition">
                        <i class="fas fa-redo mr-2"></i> Retry Failed
                    </a>
                @endif
            </div>
        </div>
    </div>

    <!-- Disbursement Summary -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Disbursement Details -->
            <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-corporate-primary mb-4">Disbursement Details</h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <p class="text-sm text-gray-500 mb-1">Status</p>
                            @php
                                $statusClasses = [
                                    'draft' => 'bg-gray-100 text-gray-800',
                                    'pending_approval' => 'bg-corporate-warning bg-opacity-10 text-corporate-warning',
                                    'approved' => 'bg-blue-100 text-blue-800',
                                    'processing' => 'bg-blue-100 text-blue-800',
                                    'completed' => 'bg-corporate-success bg-opacity-10 text-corporate-success',
                                    'partially_completed' => 'bg-corporate-error bg-opacity-10 text-corporate-error',
                                    'failed' => 'bg-red-100 text-red-800',
                                    'cancelled' => 'bg-gray-100 text-gray-800',
                                ];
                                $statusClass = $statusClasses[$disbursement->status] ?? 'bg-gray-100 text-gray-800';
                                $statusLabel = str_replace('_', ' ', ucfirst($disbursement->status));
                            @endphp
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-sm font-medium {{ $statusClass }}">
                                {{ $statusLabel }}
                            </span>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 mb-1">Date Created</p>
                            <p class="text-base font-medium text-gray-900">{{ $disbursement->created_at->format('M d, Y h:i A') }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 mb-1">Created By</p>
                            <div class="flex items-center">
                                @php
                                    $initiator = $disbursement->initiator;
                                    $initials = $initiator ? substr($initiator->first_name, 0, 1) . substr($initiator->last_name, 0, 1) : 'NA';
                                    $name = $initiator ? $initiator->name : 'Unknown';
                                @endphp
                                <div class="w-6 h-6 rounded-full bg-corporate-primary text-white flex items-center justify-center text-xs mr-2">
                                    {{ $initials }}
                                </div>
                                <p class="text-base font-medium text-gray-900">{{ $name }}</p>
                            </div>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 mb-1">Last Updated</p>
                            <p class="text-base font-medium text-gray-900">{{ $disbursement->updated_at->format('M d, Y h:i A') }}</p>
                        </div>
                        <div class="md:col-span-2">
                            <p class="text-sm text-gray-500 mb-1">Description</p>
                            <p class="text-base text-gray-900">{{ $disbursement->description ?? 'No description provided' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Transaction Summary -->
            <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-corporate-primary mb-4">Transaction Summary</h3>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                        <div class="bg-gray-50 rounded-lg p-4">
                            <p class="text-sm text-gray-500 mb-1">Total Recipients</p>
                            <p class="text-xl font-bold text-corporate-primary">{{ number_format($disbursement->transaction_count) }}</p>
                        </div>
                        <div class="bg-gray-50 rounded-lg p-4">
                            <p class="text-sm text-gray-500 mb-1">Total Amount</p>
                            <p class="text-xl font-bold text-corporate-primary">{{ $disbursement->currency }} {{ number_format($disbursement->total_amount, 2) }}</p>
                        </div>
                        <div class="bg-gray-50 rounded-lg p-4">
                            <p class="text-sm text-gray-500 mb-1">Total Fee</p>
                            <p class="text-xl font-bold text-corporate-primary">{{ $disbursement->currency }} {{ number_format($disbursement->total_fee, 2) }}</p>
                        </div>
                    </div>

                    @if($disbursement->status === 'completed' || $disbursement->status === 'partially_completed')
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div class="bg-corporate-success bg-opacity-5 rounded-lg p-4">
                                <p class="text-sm text-gray-500 mb-1">Successful</p>
                                <p class="text-xl font-bold text-corporate-success">{{ number_format($items->where('status', 'completed')->count()) }}</p>
                            </div>
                            <div class="bg-corporate-error bg-opacity-5 rounded-lg p-4">
                                <p class="text-sm text-gray-500 mb-1">Failed</p>
                                <p class="text-xl font-bold text-corporate-error">{{ number_format($items->where('status', 'failed')->count()) }}</p>
                            </div>
                            <div class="bg-corporate-warning bg-opacity-5 rounded-lg p-4">
                                <p class="text-sm text-gray-500 mb-1">Success Rate</p>
                                @php
                                    $successCount = $items->where('status', 'completed')->count();
                                    $totalCount = $items->count();
                                    $successRate = $totalCount > 0 ? ($successCount / $totalCount) * 100 : 0;
                                @endphp
                                <p class="text-xl font-bold text-corporate-warning">{{ number_format($successRate, 1) }}%</p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Approval Information -->
            @if($disbursement->status === 'pending_approval' || $disbursement->status === 'approved')
                <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-corporate-primary mb-4">Approval Information</h3>

                        @if($disbursement->status === 'pending_approval')
                            <div class="bg-corporate-warning bg-opacity-10 text-corporate-warning rounded-lg p-3 text-sm mb-4">
                                <i class="fas fa-exclamation-circle mr-2"></i> This disbursement is pending approval
                            </div>
                        @else
                            <div class="bg-corporate-success bg-opacity-10 text-corporate-success rounded-lg p-3 text-sm mb-4">
                                <i class="fas fa-check-circle mr-2"></i> This disbursement has been approved
                            </div>
                        @endif

                        @if($disbursement->approvalRequest)
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <p class="text-sm text-gray-500 mb-1">Required Approvals</p>
                                    <p class="text-base font-medium text-gray-900">{{ $disbursement->approvalRequest->required_approvals }}</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500 mb-1">Received Approvals</p>
                                    <p class="text-base font-medium text-gray-900">{{ $disbursement->approvalRequest->received_approvals }}</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500 mb-1">Requested By</p>
                                    <p class="text-base font-medium text-gray-900">{{ $disbursement->approvalRequest->requester->name }}</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500 mb-1">Requested On</p>
                                    <p class="text-base font-medium text-gray-900">{{ $disbursement->approvalRequest->created_at->format('M d, Y h:i A') }}</p>
                                </div>
                                @if($disbursement->approvalRequest->completed_at)
                                    <div>
                                        <p class="text-sm text-gray-500 mb-1">Completed On</p>
                                        <p class="text-base font-medium text-gray-900">{{ $disbursement->approvalRequest->completed_at->format('M d, Y h:i A') }}</p>
                                    </div>
                                @endif
                                @if($disbursement->approvalRequest->notes)
                                    <div class="md:col-span-2">
                                        <p class="text-sm text-gray-500 mb-1">Notes</p>
                                        <p class="text-base text-gray-900">{{ $disbursement->approvalRequest->notes }}</p>
                                    </div>
                                @endif
                            </div>

                            @if($disbursement->approvalRequest->actions && $disbursement->approvalRequest->actions->count() > 0)
                                <div class="mt-6">
                                    <h4 class="font-medium text-corporate-primary mb-3">Approval Actions</h4>
                                    <div class="space-y-4">
                                        @foreach($disbursement->approvalRequest->actions as $action)
                                            <div class="flex items-start">
                                                <div class="flex-shrink-0 w-8 h-8 rounded-full bg-corporate-primary text-white flex items-center justify-center text-xs mr-3">
                                                    {{ substr($action->approver->first_name, 0, 1) . substr($action->approver->last_name, 0, 1) }}
                                                </div>
                                                <div>
                                                    <p class="text-sm font-medium text-gray-900">
                                                        {{ $action->approver->name }}
                                                        <span class="text-{{ $action->action === 'approved' ? 'corporate-success' : 'corporate-error' }}">
                                                            {{ ucfirst($action->action) }}
                                                        </span>
                                                    </p>
                                                    <p class="text-xs text-gray-500">{{ $action->created_at->format('M d, Y h:i A') }}</p>
                                                    @if($action->comments)
                                                        <p class="text-sm text-gray-600 mt-1">{{ $action->comments }}</p>
                                                    @endif
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        @endif
                    </div>
                </div>
            @endif
        </div>

        <!-- Right Sidebar -->
        <div class="space-y-6">
            <!-- Status Timeline -->
            <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-corporate-primary mb-4">Status Timeline</h3>

                    <div class="space-y-6">
                        <div class="relative pl-8 pb-6 border-l-2 border-corporate-success">
                            <div class="absolute top-0 left-0 w-6 h-6 -ml-3 rounded-full bg-corporate-success text-white flex items-center justify-center">
                                <i class="fas fa-check text-xs"></i>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-900">Created</p>
                                <p class="text-xs text-gray-500">{{ $disbursement->created_at->format('M d, Y h:i A') }}</p>
                                <p class="text-sm text-gray-600 mt-1">Disbursement created by {{ $disbursement->initiator->name }}</p>
                            </div>
                        </div>

                        @if($disbursement->status === 'pending_approval' || $disbursement->status === 'approved' || $disbursement->status === 'processing' || $disbursement->status === 'completed' || $disbursement->status === 'partially_completed' || $disbursement->status === 'failed')
                            <div class="relative pl-8 pb-6 border-l-2 {{ $disbursement->status === 'pending_approval' ? 'border-corporate-warning' : 'border-corporate-success' }}">
                                <div class="absolute top-0 left-0 w-6 h-6 -ml-3 rounded-full {{ $disbursement->status === 'pending_approval' ? 'bg-corporate-warning' : 'bg-corporate-success' }} text-white flex items-center justify-center">
                                    <i class="fas {{ $disbursement->status === 'pending_approval' ? 'fa-clock' : 'fa-check' }} text-xs"></i>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-900">Submitted for Approval</p>
                                    <p class="text-xs text-gray-500">{{ $disbursement->approvalRequest ? $disbursement->approvalRequest->created_at->format('M d, Y h:i A') : 'N/A' }}</p>
                                    <p class="text-sm text-gray-600 mt-1">
                                        @if($disbursement->status === 'pending_approval')
                                            Waiting for approval
                                        @else
                                            Submitted by {{ $disbursement->initiator->name }}
                                        @endif
                                    </p>
                                </div>
                            </div>
                        @endif

                        @if($disbursement->status === 'approved' || $disbursement->status === 'processing' || $disbursement->status === 'completed' || $disbursement->status === 'partially_completed' || $disbursement->status === 'failed')
                            <div class="relative pl-8 pb-6 border-l-2 border-corporate-success">
                                <div class="absolute top-0 left-0 w-6 h-6 -ml-3 rounded-full bg-corporate-success text-white flex items-center justify-center">
                                    <i class="fas fa-check text-xs"></i>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-900">Approved</p>
                                    <p class="text-xs text-gray-500">{{ $disbursement->approvalRequest && $disbursement->approvalRequest->completed_at ? $disbursement->approvalRequest->completed_at->format('M d, Y h:i A') : 'N/A' }}</p>
                                    <p class="text-sm text-gray-600 mt-1">
                                        Approved by
                                        @if($disbursement->approvalRequest && $disbursement->approvalRequest->actions && $disbursement->approvalRequest->actions->where('action', 'approved')->first())
                                            {{ $disbursement->approvalRequest->actions->where('action', 'approved')->first()->approver->name }}
                                        @else
                                            an approver
                                        @endif
                                    </p>
                                </div>
                            </div>
                        @endif

                        @if($disbursement->status === 'processing' || $disbursement->status === 'completed' || $disbursement->status === 'partially_completed' || $disbursement->status === 'failed')
                            <div class="relative pl-8 pb-6 border-l-2 {{ $disbursement->status === 'processing' ? 'border-corporate-warning' : 'border-corporate-success' }}">
                                <div class="absolute top-0 left-0 w-6 h-6 -ml-3 rounded-full {{ $disbursement->status === 'processing' ? 'bg-corporate-warning' : 'bg-corporate-success' }} text-white flex items-center justify-center">
                                    <i class="fas {{ $disbursement->status === 'processing' ? 'fa-spinner' : 'fa-check' }} text-xs"></i>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-900">Processing</p>
                                    <p class="text-xs text-gray-500">{{ $disbursement->processing_started_at ? $disbursement->processing_started_at->format('M d, Y h:i A') : 'N/A' }}</p>
                                    <p class="text-sm text-gray-600 mt-1">
                                        @if($disbursement->status === 'processing')
                                            Disbursement is being processed
                                        @else
                                            Processing started
                                        @endif
                                    </p>
                                </div>
                            </div>
                        @endif

                        @if($disbursement->status === 'completed' || $disbursement->status === 'partially_completed' || $disbursement->status === 'failed')
                            <div class="relative pl-8 pb-6 border-l-2 border-{{ $disbursement->status === 'completed' ? 'corporate-success' : ($disbursement->status === 'partially_completed' ? 'corporate-warning' : 'corporate-error') }}">
                                <div class="absolute top-0 left-0 w-6 h-6 -ml-3 rounded-full bg-{{ $disbursement->status === 'completed' ? 'corporate-success' : ($disbursement->status === 'partially_completed' ? 'corporate-warning' : 'corporate-error') }} text-white flex items-center justify-center">
                                    <i class="fas {{ $disbursement->status === 'completed' ? 'fa-check' : ($disbursement->status === 'partially_completed' ? 'fa-exclamation' : 'fa-times') }} text-xs"></i>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-900">
                                        {{ $disbursement->status === 'completed' ? 'Completed' : ($disbursement->status === 'partially_completed' ? 'Partially Completed' : 'Failed') }}
                                    </p>
                                    <p class="text-xs text-gray-500">{{ $disbursement->completed_at ? $disbursement->completed_at->format('M d, Y h:i A') : 'N/A' }}</p>
                                    <p class="text-sm text-gray-600 mt-1">
                                        @if($disbursement->status === 'completed')
                                            All transactions completed successfully
                                        @elseif($disbursement->status === 'partially_completed')
                                            Some transactions failed
                                        @else
                                            All transactions failed
                                        @endif
                                    </p>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-corporate-primary mb-4">Actions</h3>

                    <div class="space-y-3">
                        @if($disbursement->status === 'pending_approval' && auth()->user()->hasRole('approver'))
                            <a href="{{ route('corporate.approvals.show', $disbursement->approvalRequest->id) }}" class="flex items-center p-3 bg-corporate-primary bg-opacity-5 rounded-lg hover:bg-opacity-10 transition">
                                <div class="w-10 h-10 rounded-full bg-corporate-primary bg-opacity-10 flex items-center justify-center text-corporate-primary mr-3">
                                    <i class="fas fa-check"></i>
                                </div>
                                <div>
                                    <h4 class="font-medium text-corporate-primary">Approve Disbursement</h4>
                                    <p class="text-xs text-gray-500">Review and approve this disbursement</p>
                                </div>
                            </a>
                        @endif

                        @if($disbursement->status === 'completed' || $disbursement->status === 'partially_completed')
                            <a href="#" class="flex items-center p-3 bg-corporate-primary bg-opacity-5 rounded-lg hover:bg-opacity-10 transition">
                                <div class="w-10 h-10 rounded-full bg-corporate-primary bg-opacity-10 flex items-center justify-center text-corporate-primary mr-3">
                                    <i class="fas fa-download"></i>
                                </div>
                                <div>
                                    <h4 class="font-medium text-corporate-primary">Download Report</h4>
                                    <p class="text-xs text-gray-500">Download detailed transaction report</p>
                                </div>
                            </a>
                        @endif

                        @if($disbursement->status === 'partially_completed' || $disbursement->status === 'failed')
                            <a href="#" class="flex items-center p-3 bg-corporate-primary bg-opacity-5 rounded-lg hover:bg-opacity-10 transition">
                                <div class="w-10 h-10 rounded-full bg-corporate-primary bg-opacity-10 flex items-center justify-center text-corporate-primary mr-3">
                                    <i class="fas fa-redo"></i>
                                </div>
                                <div>
                                    <h4 class="font-medium text-corporate-primary">Retry Failed Transactions</h4>
                                    <p class="text-xs text-gray-500">Attempt to process failed transactions again</p>
                                </div>
                            </a>
                        @endif

                        <a href="#" class="flex items-center p-3 bg-corporate-primary bg-opacity-5 rounded-lg hover:bg-opacity-10 transition">
                            <div class="w-10 h-10 rounded-full bg-corporate-primary bg-opacity-10 flex items-center justify-center text-corporate-primary mr-3">
                                <i class="fas fa-copy"></i>
                            </div>
                            <div>
                                <h4 class="font-medium text-corporate-primary">Duplicate Disbursement</h4>
                                <p class="text-xs text-gray-500">Create a new disbursement with the same recipients</p>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Disbursement Items -->
    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <div class="p-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold text-corporate-primary">Disbursement Items</h3>
                <div class="flex items-center">
                    <form action="{{ route('corporate.disbursements.show', $disbursement->id) }}" method="GET" class="flex space-x-2">
                        <select name="status" class="px-3 py-2 border rounded-lg focus:ring-2 focus:ring-corporate-primary focus:border-corporate-primary text-sm">
                            <option value="">All Statuses</option>
                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="processing" {{ request('status') == 'processing' ? 'selected' : '' }}>Processing</option>
                            <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                            <option value="failed" {{ request('status') == 'failed' ? 'selected' : '' }}>Failed</option>
                        </select>
                        <button type="submit" class="px-3 py-2 bg-corporate-primary text-white rounded-lg text-sm hover:bg-opacity-90">
                            Filter
                        </button>
                    </form>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full corporate-table">
                    <thead>
                        <tr class="bg-gray-50">
                            <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Wallet Number</th>
                            <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Provider</th>
                            <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Recipient</th>
                            <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                            <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fee</th>
                            <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Reference</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($items as $item)
                            <tr>
                                <td class="whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ $item->wallet_number }}</div>
                                </td>
                                <td class="py-3 px-3">
                                    <div class="flex items-center">
                                        @if($item->walletProvider)
                                            <img src="{{ asset('assets/img/' . strtolower($item->walletProvider->api_code) . '.png') }}" alt="{{ $item->walletProvider->name }}" class="w-6 h-6 rounded-full mr-2">
                                            <div class="text-sm text-gray-900">{{ $item->walletProvider->name }}</div>
                                        @else
                                            <div class="text-sm text-gray-900">Unknown</div>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    <div class="text-sm text-gray-900">{{ $item->recipient_name ?? 'N/A' }}</div>
                                </td>
                                <td>
                                    <div class="text-sm font-medium text-gray-900">{{ $item->currency }} {{ number_format($item->amount, 2) }}</div>
                                </td>
                                <td>
                                    <div class="text-sm text-gray-900">{{ $item->currency }} {{ number_format($item->fee, 2) }}</div>
                                </td>
                                <td>
                                    @php
                                        $itemStatusClasses = [
                                            'pending' => 'bg-gray-100 text-gray-800',
                                            'processing' => 'bg-blue-100 text-blue-800',
                                            'completed' => 'bg-corporate-success bg-opacity-10 text-corporate-success',
                                            'failed' => 'bg-corporate-error bg-opacity-10 text-corporate-error',
                                        ];
                                        $itemStatusClass = $itemStatusClasses[$item->status] ?? 'bg-gray-100 text-gray-800';
                                    @endphp
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $itemStatusClass }}">
                                        {{ ucfirst($item->status) }}
                                    </span>
                                </td>
                                <td>
                                    <div class="text-sm text-gray-900">{{ $item->reference }}</div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-4 text-center text-gray-500">
                                    No items found for this disbursement.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="mt-6">
                {{ $items->appends(request()->query())->links() }}
            </div>
        </div>
    </div>
@endsection
