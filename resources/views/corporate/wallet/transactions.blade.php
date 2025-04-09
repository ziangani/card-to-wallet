@extends('corporate.layouts.app')

@section('title', 'Wallet Transactions')

@section('content')
<div class="mb-6">
    <div class="flex items-center mb-2">
        <a href="{{ route('corporate.wallet.index') }}" class="text-primary hover:underline">
            <i class="fas fa-arrow-left mr-2"></i> Back to Wallet
        </a>
    </div>
    <div class="flex justify-between items-center">
        <div>
            <h2 class="text-xl font-bold text-gray-800">Wallet Transactions</h2>
            <p class="text-gray-500">View and manage your wallet transaction history</p>
        </div>
        <div>
            <a href="{{ route('corporate.wallet.deposit') }}" class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-opacity-90">
                <i class="fas fa-plus mr-2"></i> Deposit Funds
            </a>
        </div>
    </div>
</div>

<!-- Wallet Summary -->
<div class="bg-white rounded-xl shadow-sm p-6 mb-6">
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div>
            <h3 class="text-sm font-medium text-gray-500 mb-1">Current Balance</h3>
            <p class="text-2xl font-bold text-primary">{{ $wallet->currency }} {{ number_format($wallet->balance, 2) }}</p>
        </div>
        <div>
            <h3 class="text-sm font-medium text-gray-500 mb-1">Total Deposits</h3>
            <p class="text-2xl font-bold text-green-600">{{ $wallet->currency }} {{ number_format($transactionStats['deposits'], 2) }}</p>
        </div>
        <div>
            <h3 class="text-sm font-medium text-gray-500 mb-1">Total Withdrawals</h3>
            <p class="text-2xl font-bold text-red-600">{{ $wallet->currency }} {{ number_format($transactionStats['withdrawals'], 2) }}</p>
        </div>
    </div>
</div>

<!-- Filters -->
<div class="bg-white rounded-xl shadow-sm p-6 mb-6">
    <form action="{{ route('corporate.wallet.transactions') }}" method="GET" class="space-y-4">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label for="type" class="block text-sm font-medium text-gray-700 mb-1">Transaction Type</label>
                <select id="type" name="type" class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-primary focus:border-primary">
                    <option value="all" {{ request('type') == 'all' ? 'selected' : '' }}>All Types</option>
                    <option value="deposit" {{ request('type') == 'deposit' ? 'selected' : '' }}>Deposits</option>
                    <option value="withdrawal" {{ request('type') == 'withdrawal' ? 'selected' : '' }}>Withdrawals</option>
                    <option value="transfer" {{ request('type') == 'transfer' ? 'selected' : '' }}>Transfers</option>
                    <option value="fee" {{ request('type') == 'fee' ? 'selected' : '' }}>Fees</option>
                    <option value="adjustment" {{ request('type') == 'adjustment' ? 'selected' : '' }}>Adjustments</option>
                </select>
            </div>
            
            <div>
                <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                <select id="status" name="status" class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-primary focus:border-primary">
                    <option value="all" {{ request('status') == 'all' ? 'selected' : '' }}>All Statuses</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                    <option value="failed" {{ request('status') == 'failed' ? 'selected' : '' }}>Failed</option>
                    <option value="reversed" {{ request('status') == 'reversed' ? 'selected' : '' }}>Reversed</option>
                </select>
            </div>
            
            <div>
                <label for="min_amount" class="block text-sm font-medium text-gray-700 mb-1">Minimum Amount</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <span class="text-gray-500">{{ $wallet->currency }}</span>
                    </div>
                    <input type="number" id="min_amount" name="min_amount" value="{{ request('min_amount') }}" min="0" step="0.01" class="w-full pl-10 pr-4 py-2 border rounded-lg focus:ring-2 focus:ring-primary focus:border-primary">
                </div>
            </div>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label for="max_amount" class="block text-sm font-medium text-gray-700 mb-1">Maximum Amount</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <span class="text-gray-500">{{ $wallet->currency }}</span>
                    </div>
                    <input type="number" id="max_amount" name="max_amount" value="{{ request('max_amount') }}" min="0" step="0.01" class="w-full pl-10 pr-4 py-2 border rounded-lg focus:ring-2 focus:ring-primary focus:border-primary">
                </div>
            </div>
            
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
            <a href="{{ route('corporate.wallet.transactions') }}" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                Reset
            </a>
            <button type="submit" class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-opacity-90">
                Apply Filters
            </button>
        </div>
    </form>
</div>

<!-- Transactions List -->
<div class="bg-white rounded-xl shadow-sm overflow-hidden">
    <div class="p-6 border-b border-gray-200">
        <div class="flex justify-between items-center">
            <h3 class="text-lg font-semibold text-gray-800">Transaction History</h3>
            <div class="flex space-x-2">
                <a href="{{ route('corporate.wallet.transactions', array_merge(request()->all(), ['export' => 'csv'])) }}" class="px-3 py-1 border border-gray-300 rounded text-sm text-gray-700 hover:bg-gray-50">
                    <i class="fas fa-file-csv mr-1"></i> CSV
                </a>
                <a href="{{ route('corporate.wallet.transactions', array_merge(request()->all(), ['export' => 'excel'])) }}" class="px-3 py-1 border border-gray-300 rounded text-sm text-gray-700 hover:bg-gray-50">
                    <i class="fas fa-file-excel mr-1"></i> Excel
                </a>
                <a href="{{ route('corporate.wallet.transactions', array_merge(request()->all(), ['export' => 'pdf'])) }}" class="px-3 py-1 border border-gray-300 rounded text-sm text-gray-700 hover:bg-gray-50">
                    <i class="fas fa-file-pdf mr-1"></i> PDF
                </a>
            </div>
        </div>
    </div>
    
    @if($transactions->isEmpty())
        <div class="p-6 text-center">
            <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-gray-100 text-gray-400 mb-4">
                <i class="fas fa-exchange-alt text-2xl"></i>
            </div>
            <h3 class="text-lg font-medium text-gray-900 mb-1">No transactions found</h3>
            <p class="text-gray-500">There are no transactions matching your filters.</p>
        </div>
    @else
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50">
                        <th class="px-6 py-3 text-left font-medium text-gray-500">Date & Time</th>
                        <th class="px-6 py-3 text-left font-medium text-gray-500">Reference</th>
                        <th class="px-6 py-3 text-left font-medium text-gray-500">Type</th>
                        <th class="px-6 py-3 text-left font-medium text-gray-500">Description</th>
                        <th class="px-6 py-3 text-right font-medium text-gray-500">Amount</th>
                        <th class="px-6 py-3 text-right font-medium text-gray-500">Balance</th>
                        <th class="px-6 py-3 text-center font-medium text-gray-500">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($transactions as $transaction)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $transaction->created_at->format('M d, Y') }}</div>
                                <div class="text-xs text-gray-500">{{ $transaction->created_at->format('h:i A') }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="font-medium text-primary">{{ $transaction->reference_number }}</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @php
                                    $typeIcons = [
                                        'deposit' => '<i class="fas fa-arrow-down text-green-600 mr-1"></i>',
                                        'withdrawal' => '<i class="fas fa-arrow-up text-red-600 mr-1"></i>',
                                        'transfer' => '<i class="fas fa-exchange-alt text-blue-600 mr-1"></i>',
                                        'fee' => '<i class="fas fa-receipt text-yellow-600 mr-1"></i>',
                                        'adjustment' => '<i class="fas fa-sliders-h text-purple-600 mr-1"></i>',
                                    ];
                                    $typeIcon = $typeIcons[$transaction->transaction_type] ?? '<i class="fas fa-circle text-gray-600 mr-1"></i>';
                                @endphp
                                <span class="inline-flex items-center">
                                    {!! $typeIcon !!} {{ ucfirst($transaction->transaction_type) }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-900">{{ $transaction->description }}</div>
                                @if($transaction->performed_by)
                                    <div class="text-xs text-gray-500">
                                        By: {{ $transaction->performer->name ?? 'Unknown' }}
                                    </div>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right whitespace-nowrap">
                                @php
                                    $amountClass = in_array($transaction->transaction_type, ['deposit', 'adjustment']) && $transaction->amount > 0 
                                        ? 'text-green-600' 
                                        : (in_array($transaction->transaction_type, ['withdrawal', 'fee']) || $transaction->amount < 0 
                                            ? 'text-red-600' 
                                            : 'text-gray-900');
                                    $amountPrefix = in_array($transaction->transaction_type, ['deposit', 'adjustment']) && $transaction->amount > 0 
                                        ? '+' 
                                        : (in_array($transaction->transaction_type, ['withdrawal', 'fee']) || $transaction->amount < 0 
                                            ? '-' 
                                            : '');
                                    $displayAmount = abs($transaction->amount);
                                @endphp
                                <span class="font-medium {{ $amountClass }}">
                                    {{ $amountPrefix }}{{ $transaction->currency }} {{ number_format($displayAmount, 2) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right whitespace-nowrap">
                                <span class="font-medium text-gray-900">
                                    {{ $transaction->currency }} {{ number_format($transaction->balance_after, 2) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center whitespace-nowrap">
                                @php
                                    $statusClasses = [
                                        'pending' => 'bg-yellow-100 text-yellow-800',
                                        'completed' => 'bg-green-100 text-green-800',
                                        'failed' => 'bg-red-100 text-red-800',
                                        'reversed' => 'bg-gray-100 text-gray-800',
                                    ];
                                    $statusClass = $statusClasses[$transaction->status] ?? 'bg-gray-100 text-gray-800';
                                @endphp
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statusClass }}">
                                    {{ ucfirst($transaction->status) }}
                                </span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $transactions->withQueryString()->links() }}
        </div>
    @endif
</div>
@endsection
