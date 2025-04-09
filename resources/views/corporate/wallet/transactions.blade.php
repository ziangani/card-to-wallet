@extends('corporate.layouts.app')

@section('title', 'Transaction History - ' . config('app.name'))
@section('meta_description', 'View your corporate wallet transaction history')
@section('header_title', 'Transaction History')

@section('content')
    <div class="mb-6">
        <div class="flex items-center mb-2">
            <a href="{{ route('corporate.wallet.index') }}" class="text-corporate-primary hover:underline">
                <i class="fas fa-arrow-left mr-2"></i> Back to Wallet
            </a>
        </div>
        <h2 class="text-xl font-bold text-corporate-primary">Transaction History</h2>
        <p class="text-gray-500">View and filter your wallet transactions</p>
    </div>

    <div class="bg-white rounded-xl shadow-sm overflow-hidden mb-6">
        <div class="p-6">
            <form action="{{ route('corporate.wallet.transactions') }}" method="GET" class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div>
                        <label for="type" class="block text-sm font-medium text-gray-700 mb-1">Transaction Type</label>
                        <select id="type" name="type" class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-corporate-primary focus:border-corporate-primary">
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
                        <select id="status" name="status" class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-corporate-primary focus:border-corporate-primary">
                            <option value="all" {{ request('status') == 'all' ? 'selected' : '' }}>All Statuses</option>
                            <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="failed" {{ request('status') == 'failed' ? 'selected' : '' }}>Failed</option>
                            <option value="reversed" {{ request('status') == 'reversed' ? 'selected' : '' }}>Reversed</option>
                        </select>
                    </div>
                    
                    <div>
                        <label for="date_from" class="block text-sm font-medium text-gray-700 mb-1">From Date</label>
                        <input type="date" id="date_from" name="date_from" value="{{ request('date_from') }}" class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-corporate-primary focus:border-corporate-primary">
                    </div>
                    
                    <div>
                        <label for="date_to" class="block text-sm font-medium text-gray-700 mb-1">To Date</label>
                        <input type="date" id="date_to" name="date_to" value="{{ request('date_to') }}" class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-corporate-primary focus:border-corporate-primary">
                    </div>
                </div>
                
                <div class="flex items-center space-x-4">
                    <div class="flex-grow">
                        <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                        <input type="text" id="search" name="search" value="{{ request('search') }}" placeholder="Search by reference or description" class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-corporate-primary focus:border-corporate-primary">
                    </div>
                    
                    <div class="pt-6">
                        <button type="submit" class="px-4 py-2 bg-corporate-primary text-white rounded-lg hover:bg-opacity-90">
                            <i class="fas fa-search mr-2"></i> Filter
                        </button>
                        
                        <a href="{{ route('corporate.wallet.transactions') }}" class="ml-2 px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                            <i class="fas fa-times mr-2"></i> Clear
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <div class="p-6 border-b border-gray-200 flex justify-between items-center">
            <h3 class="text-lg font-semibold text-corporate-primary">Transactions</h3>
            
            <div class="flex space-x-2">
                <a href="{{ route('corporate.wallet.transactions', array_merge(request()->all(), ['export' => 'csv'])) }}" class="px-3 py-1.5 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 text-sm">
                    <i class="fas fa-file-csv mr-1"></i> Export CSV
                </a>
                <a href="{{ route('corporate.wallet.transactions', array_merge(request()->all(), ['export' => 'pdf'])) }}" class="px-3 py-1.5 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 text-sm">
                    <i class="fas fa-file-pdf mr-1"></i> Export PDF
                </a>
            </div>
        </div>
        
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Reference</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date & Time</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Balance</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($transactions as $transaction)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-corporate-primary">
                                {{ $transaction->reference_number }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $transaction->created_at->format('M d, Y H:i') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                @switch($transaction->transaction_type)
                                    @case('deposit')
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                            Deposit
                                        </span>
                                        @break
                                    @case('withdrawal')
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                            Withdrawal
                                        </span>
                                        @break
                                    @case('transfer')
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                            Transfer
                                        </span>
                                        @break
                                    @case('fee')
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                            Fee
                                        </span>
                                        @break
                                    @case('adjustment')
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-purple-100 text-purple-800">
                                            Adjustment
                                        </span>
                                        @break
                                    @default
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                            {{ ucfirst($transaction->transaction_type) }}
                                        </span>
                                @endswitch
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500">
                                {{ $transaction->description }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium {{ in_array($transaction->transaction_type, ['deposit', 'adjustment']) && $transaction->amount > 0 ? 'text-green-600' : 'text-red-600' }}">
                                {{ in_array($transaction->transaction_type, ['deposit', 'adjustment']) && $transaction->amount > 0 ? '+' : '-' }} K {{ number_format(abs($transaction->amount), 2) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                K {{ number_format($transaction->balance_after, 2) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                @switch($transaction->status)
                                    @case('completed')
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                            Completed
                                        </span>
                                        @break
                                    @case('pending')
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                            Pending
                                        </span>
                                        @break
                                    @case('failed')
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                            Failed
                                        </span>
                                        @break
                                    @case('reversed')
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                            Reversed
                                        </span>
                                        @break
                                    @default
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                            {{ ucfirst($transaction->status) }}
                                        </span>
                                @endswitch
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-4 text-center text-sm text-gray-500">
                                No transactions found matching your criteria.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $transactions->links() }}
        </div>
    </div>
@endsection
