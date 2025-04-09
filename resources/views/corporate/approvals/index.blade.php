@extends('corporate.layouts.app')

@section('title', 'Approvals')

@section('content')
<div class="mb-6">
    <h2 class="text-xl font-bold text-gray-800">Pending Approvals</h2>
    <p class="text-gray-500">Review and manage approval requests</p>
</div>

<!-- Filters -->
<div class="bg-white rounded-xl shadow-sm p-6 mb-6">
    <form action="{{ route('corporate.approvals.index') }}" method="GET" class="space-y-4">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                <select id="status" name="status" class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-primary focus:border-primary">
                    <option value="all" {{ request('status') == 'all' ? 'selected' : '' }}>All Statuses</option>
                    <option value="pending" {{ request('status', 'pending') == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                    <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                    <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                </select>
            </div>
            
            <div>
                <label for="entity_type" class="block text-sm font-medium text-gray-700 mb-1">Request Type</label>
                <select id="entity_type" name="entity_type" class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-primary focus:border-primary">
                    <option value="all" {{ request('entity_type') == 'all' ? 'selected' : '' }}>All Types</option>
                    <option value="bulk_disbursement" {{ request('entity_type') == 'bulk_disbursement' ? 'selected' : '' }}>Bulk Disbursement</option>
                    <option value="user_role" {{ request('entity_type') == 'user_role' ? 'selected' : '' }}>User Role</option>
                    <option value="rate_change" {{ request('entity_type') == 'rate_change' ? 'selected' : '' }}>Rate Change</option>
                    <option value="wallet_withdrawal" {{ request('entity_type') == 'wallet_withdrawal' ? 'selected' : '' }}>Wallet Withdrawal</option>
                </select>
            </div>
            
            <div>
                <label for="requester_id" class="block text-sm font-medium text-gray-700 mb-1">Requested By</label>
                <select id="requester_id" name="requester_id" class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-primary focus:border-primary">
                    <option value="all" {{ request('requester_id') == 'all' ? 'selected' : '' }}>All Users</option>
                    @foreach($requesters as $requester)
                        <option value="{{ $requester->id }}" {{ request('requester_id') == $requester->id ? 'selected' : '' }}>{{ $requester->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label for="start_date" class="block text-sm font-medium text-gray-700 mb-1">From Date</label>
                <input type="date" id="start_date" name="start_date" value="{{ request('start_date') }}" class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-primary focus:border-primary">
            </div>
            
            <div>
                <label for="end_date" class="block text-sm font-medium text-gray-700 mb-1">To Date</label>
                <input type="date" id="end_date" name="end_date" value="{{ request('end_date') }}" class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-primary focus:border-primary">
            </div>
        </div>
        
        <div class="flex justify-end space-x-3">
            <a href="{{ route('corporate.approvals.index') }}" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                Reset
            </a>
            <button type="submit" class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-opacity-90">
                Apply Filters
            </button>
        </div>
    </form>
</div>

<!-- Approvals List -->
<div class="bg-white rounded-xl shadow-sm overflow-hidden">
    <div class="p-6 border-b border-gray-200">
        <div class="flex justify-between items-center">
            <h3 class="text-lg font-semibold text-gray-800">Approval Requests</h3>
            <span class="px-3 py-1 bg-primary bg-opacity-10 text-primary text-sm font-medium rounded-full">
                {{ $approvalRequests->total() }} {{ Str::plural('Request', $approvalRequests->total()) }}
            </span>
        </div>
    </div>
    
    @if($approvalRequests->isEmpty())
        <div class="p-6 text-center">
            <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-gray-100 text-gray-400 mb-4">
                <i class="fas fa-check-double text-2xl"></i>
            </div>
            <h3 class="text-lg font-medium text-gray-900 mb-1">No approval requests found</h3>
            <p class="text-gray-500">There are no approval requests matching your filters.</p>
        </div>
    @else
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50">
                        <th class="px-6 py-3 text-left font-medium text-gray-500">Request ID</th>
                        <th class="px-6 py-3 text-left font-medium text-gray-500">Type</th>
                        <th class="px-6 py-3 text-left font-medium text-gray-500">Description</th>
                        <th class="px-6 py-3 text-left font-medium text-gray-500">Requested By</th>
                        <th class="px-6 py-3 text-left font-medium text-gray-500">Date</th>
                        <th class="px-6 py-3 text-left font-medium text-gray-500">Status</th>
                        <th class="px-6 py-3 text-left font-medium text-gray-500">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($approvalRequests as $request)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="font-medium text-primary">{{ substr($request->uuid, 0, 8) }}</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @switch($request->entity_type)
                                    @case('bulk_disbursement')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                            <i class="fas fa-money-bill-wave mr-1"></i> Disbursement
                                        </span>
                                        @break
                                    @case('user_role')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                            <i class="fas fa-user-tag mr-1"></i> User Role
                                        </span>
                                        @break
                                    @case('rate_change')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            <i class="fas fa-percentage mr-1"></i> Rate Change
                                        </span>
                                        @break
                                    @case('wallet_withdrawal')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                            <i class="fas fa-wallet mr-1"></i> Withdrawal
                                        </span>
                                        @break
                                    @default
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                            <i class="fas fa-question-circle mr-1"></i> {{ ucfirst(str_replace('_', ' ', $request->entity_type)) }}
                                        </span>
                                @endswitch
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-900">{{ $request->description }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="w-8 h-8 rounded-full bg-primary text-white flex items-center justify-center text-xs mr-2">
                                        {{ strtoupper(substr($request->requester->name ?? 'U', 0, 1)) }}
                                    </div>
                                    <div>
                                        <div class="text-sm font-medium text-gray-900">{{ $request->requester->name ?? 'Unknown User' }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $request->created_at->format('M d, Y') }}</div>
                                <div class="text-xs text-gray-500">{{ $request->created_at->format('h:i A') }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @php
                                    $statusClasses = [
                                        'pending' => 'bg-yellow-100 text-yellow-800',
                                        'approved' => 'bg-green-100 text-green-800',
                                        'rejected' => 'bg-red-100 text-red-800',
                                        'cancelled' => 'bg-gray-100 text-gray-800',
                                    ];
                                    $statusClass = $statusClasses[$request->status] ?? 'bg-gray-100 text-gray-800';
                                @endphp
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statusClass }}">
                                    {{ ucfirst($request->status) }}
                                </span>
                                @if($request->status === 'pending')
                                    <div class="text-xs text-gray-500 mt-1">
                                        {{ $request->received_approvals }}/{{ $request->required_approvals }} approvals
                                    </div>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <a href="{{ route('corporate.approvals.show', $request->id) }}" class="text-primary hover:text-primary-dark">
                                    View Details
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $approvalRequests->withQueryString()->links() }}
        </div>
    @endif
</div>
@endsection
