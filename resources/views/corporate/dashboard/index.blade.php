@extends('corporate.layouts.app')

@section('title', 'Corporate Dashboard')

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Current Balance Card -->
    <div class="bg-white rounded-lg shadow-sm p-6">
        <h2 class="text-gray-600 text-sm font-medium mb-2">Current Balance</h2>
        <div class="text-3xl font-bold mb-4">K {{ number_format($balance, 2) }}</div>
        <div class="flex space-x-2">
            <a href="{{ route('corporate.wallet.deposit') }}" class="px-4 py-2 bg-primary text-white text-sm font-medium rounded hover:bg-blue-700 transition-colors">Deposit Funds</a>
            <a href="{{ route('corporate.wallet.transactions') }}" class="px-4 py-2 border border-gray-300 text-gray-700 text-sm font-medium rounded hover:bg-gray-50 transition-colors">View Transactions</a>
        </div>
    </div>
    
    <!-- Monthly Volume Card -->
    <div class="bg-white rounded-lg shadow-sm p-6">
        <div class="flex justify-between items-center mb-2">
            <h2 class="text-gray-600 text-sm font-medium">Monthly Volume</h2>
            <i class="fas fa-chart-line text-primary"></i>
        </div>
        <div class="text-3xl font-bold mb-1">K {{ number_format($monthlyVolume, 2) }}</div>
        <div class="text-gray-500 text-sm">This month</div>
    </div>
    
    <!-- Recipients Card -->
    <div class="bg-white rounded-lg shadow-sm p-6">
        <div class="flex justify-between items-center mb-2">
            <h2 class="text-gray-600 text-sm font-medium">Recipients</h2>
            <i class="fas fa-users text-primary"></i>
        </div>
        <div class="text-3xl font-bold mb-1">{{ number_format($recipientCount) }}</div>
        <div class="text-gray-500 text-sm">This month</div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mt-6">
    <!-- Pending Approvals Card -->
    <div class="bg-white rounded-lg shadow-sm p-6">
        <div class="flex justify-between items-center mb-2">
            <h2 class="text-gray-600 text-sm font-medium">Pending Approvals</h2>
            <i class="fas fa-check-double text-primary"></i>
        </div>
        <div class="text-3xl font-bold mb-1">{{ $pendingApprovalsCount }}</div>
        <div class="text-sm">
            @if($pendingApprovalsCount > 0)
                <a href="{{ route('corporate.approvals.index') }}" class="text-primary hover:underline">View all</a>
            @else
                <span class="text-gray-500">No pending approvals</span>
            @endif
        </div>
    </div>
    
    <!-- Quick Actions Card -->
    <div class="bg-white rounded-lg shadow-sm p-6 lg:col-span-2">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-gray-600 text-sm font-medium">Quick Actions</h2>
            <i class="fas fa-bolt text-primary"></i>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
            <a href="{{ route('corporate.disbursements.create') }}" class="flex items-center justify-center px-4 py-3 bg-primary text-white text-sm font-medium rounded hover:bg-blue-700 transition-colors">
                <i class="fas fa-money-bill-wave mr-2"></i> New Disbursement
            </a>
            <a href="{{ route('corporate.reports.generate') }}" class="flex items-center justify-center px-4 py-3 border border-gray-300 text-gray-700 text-sm font-medium rounded hover:bg-gray-50 transition-colors">
                <i class="fas fa-file-alt mr-2"></i> Generate Report
            </a>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mt-6">
    <!-- Recent Disbursements -->
    <div class="bg-white rounded-lg shadow-sm lg:col-span-2">
        <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center">
            <h2 class="font-medium text-dark">Recent Disbursements</h2>
            <a href="{{ route('corporate.disbursements.index') }}" class="text-primary hover:underline text-sm">View All</a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50">
                        <th class="px-6 py-3 text-left font-medium text-gray-500">Reference</th>
                        <th class="px-6 py-3 text-left font-medium text-gray-500">Date</th>
                        <th class="px-6 py-3 text-left font-medium text-gray-500">Recipients</th>
                        <th class="px-6 py-3 text-left font-medium text-gray-500">Amount</th>
                        <th class="px-6 py-3 text-left font-medium text-gray-500">Status</th>
                        <th class="px-6 py-3 text-left font-medium text-gray-500"></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentDisbursements as $disbursement)
                        <tr class="hover:bg-gray-50 border-b border-gray-100">
                            <td class="px-6 py-4">{{ $disbursement->reference_number }}</td>
                            <td class="px-6 py-4">{{ $disbursement->created_at->format('M d, Y') }}</td>
                            <td class="px-6 py-4">{{ $disbursement->transaction_count }}</td>
                            <td class="px-6 py-4">K {{ number_format($disbursement->total_amount, 2) }}</td>
                            <td class="px-6 py-4">
                                @php
                                    $statusClasses = [
                                        'pending' => 'bg-warning text-dark',
                                        'approved' => 'bg-success text-white',
                                        'rejected' => 'bg-error text-white',
                                        'completed' => 'bg-success text-white',
                                        'failed' => 'bg-error text-white',
                                        'processing' => 'bg-primary text-white',
                                    ];
                                    $status = strtolower($disbursement->status);
                                    $statusClass = $statusClasses[$status] ?? 'bg-gray-200 text-gray-800';
                                @endphp
                                <span class="px-2 py-1 text-xs font-medium rounded-full {{ $statusClass }}">
                                    {{ ucfirst($disbursement->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <a href="{{ route('corporate.disbursements.show', $disbursement->id) }}" class="text-primary hover:text-blue-700">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-4 text-center text-gray-500">No disbursements found</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    
    <!-- Recent Activity -->
    <div class="bg-white rounded-lg shadow-sm">
        <div class="px-6 py-4 border-b border-gray-100">
            <h2 class="font-medium text-dark">Recent Activity</h2>
        </div>
        <div class="divide-y divide-gray-100">
            @foreach($recentActivity as $activity)
                <div class="p-4">
                    <div class="flex">
                        <div class="flex-shrink-0 mr-3">
                            @switch($activity['type'])
                                @case('disbursement_created')
                                    <div class="w-10 h-10 rounded-full bg-primary bg-opacity-10 flex items-center justify-center text-primary">
                                        <i class="fas fa-money-bill-wave"></i>
                                    </div>
                                    @break
                                @case('disbursement_approved')
                                    <div class="w-10 h-10 rounded-full bg-success bg-opacity-10 flex items-center justify-center text-success">
                                        <i class="fas fa-check-circle"></i>
                                    </div>
                                    @break
                                @case('wallet_deposit')
                                    <div class="w-10 h-10 rounded-full bg-primary bg-opacity-10 flex items-center justify-center text-primary">
                                        <i class="fas fa-wallet"></i>
                                    </div>
                                    @break
                                @case('rate_tier_changed')
                                    <div class="w-10 h-10 rounded-full bg-warning bg-opacity-10 flex items-center justify-center text-warning">
                                        <i class="fas fa-chart-line"></i>
                                    </div>
                                    @break
                                @default
                                    <div class="w-10 h-10 rounded-full bg-gray-100 flex items-center justify-center text-gray-500">
                                        <i class="fas fa-bell"></i>
                                    </div>
                            @endswitch
                        </div>
                        <div class="flex-grow">
                            <div class="flex justify-between items-start">
                                <div>
                                    <div class="font-medium text-dark">{{ $activity['description'] }}</div>
                                    <div class="text-sm text-gray-500">{{ $activity['details'] }}</div>
                                </div>
                                <div class="text-xs text-gray-500">{{ $activity['timestamp']->diffForHumans() }}</div>
                            </div>
                            <div class="text-xs text-gray-500 mt-1">By {{ $activity['user'] }}</div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>
@endsection
