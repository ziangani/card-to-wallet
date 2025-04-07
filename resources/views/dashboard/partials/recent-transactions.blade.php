<div class="bg-white rounded-xl shadow-sm overflow-hidden">
    <div class="p-6">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6">
            <div>
                <h2 class="text-xl font-bold text-dark mb-1">Recent Transactions</h2>
                <p class="text-gray-600">Your latest 5 transactions</p>
            </div>

            <a href="{{ route('transactions.history') }}" class="text-primary hover:underline mt-2 md:mt-0">
                <i class="fas fa-history mr-1"></i> View all transactions
            </a>
        </div>

        @if(count($recentTransactions ?? []) > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead>
                        <tr>
                            <th class="px-4 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                            <th class="px-4 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Recipient</th>
                            <th class="px-4 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                            <th class="px-4 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-4 py-3 bg-gray-50 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($recentTransactions as $transaction)
                            <tr>
                                <td class="px-4 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ $transaction->created_at->format('M d, Y') }}</div>
                                    <div class="text-xs text-gray-500">{{ $transaction->created_at->format('h:i A') }}</div>
                                </td>
                                <td class="px-4 py-4">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-8 w-8">
                                            @if($transaction->wallet_provider)
                                                @if($transaction->wallet_provider->api_code === 'airtel')
                                                    <img class="h-8 w-8 rounded-full" src="{{ asset('assets/img/airtel.png') }}" alt="Airtel">
                                                @elseif($transaction->wallet_provider->api_code === 'mtn')
                                                    <img class="h-8 w-8 rounded-full" src="{{ asset('assets/img/mtn.jpg') }}" alt="MTN">
                                                @elseif($transaction->wallet_provider->api_code === 'zamtel')
                                                    <img class="h-8 w-8 rounded-full" src="{{ asset('assets/img/zamtel.jpg') }}" alt="Zamtel">
                                                @else
                                                    <div class="h-8 w-8 rounded-full bg-primary text-white flex items-center justify-center">
                                                        <i class="fas fa-wallet"></i>
                                                    </div>
                                                @endif
                                            @else
                                                <div class="h-8 w-8 rounded-full bg-primary text-white flex items-center justify-center">
                                                    <i class="fas fa-wallet"></i>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="ml-3">
                                            <div class="text-sm font-medium text-gray-900">
                                                {{ $transaction->reference_4 ?: 'Unknown' }}
                                            </div>
                                            <div class="text-xs text-gray-500">
                                                +260{{ $transaction->reference_1 }}
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-4">
                                    <div class="text-sm font-medium text-gray-900">K{{ number_format($transaction->amount, 2) }}</div>
                                    <div class="text-xs text-gray-500">Fee: K{{ number_format($transaction->fee_amount, 2) }}</div>
                                </td>
                                <td class="px-4 py-4">
                                    @if($transaction->status === 'completed')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-success bg-opacity-10 text-success">
                                            Completed
                                        </span>
                                    @elseif($transaction->status === 'pending' || $transaction->status === 'payment_initiated')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-warning bg-opacity-10 text-warning">
                                            Pending
                                        </span>
                                    @elseif($transaction->status === 'failed' || $transaction->status === 'payment_failed')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-error bg-opacity-10 text-error">
                                            Failed
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                            {{ ucfirst($transaction->status) }}
                                        </span>
                                    @endif
                                </td>
                                <td class="px-4 py-4 text-right text-sm font-medium">
                                    <a href="{{ route('transactions.show', $transaction->uuid) }}" class="text-primary hover:underline">
                                        Details
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-center py-8">
                <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-gray-100 text-gray-400 mb-4">
                    <i class="fas fa-exchange-alt text-2xl"></i>
                </div>
                <h3 class="text-lg font-medium text-gray-900 mb-1">No transactions yet</h3>
                <p class="text-gray-500">Your recent transactions will appear here</p>
                <div class="mt-4">
                    <a href="{{ route('transactions.initiate') }}" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary hover:bg-opacity-90 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary">
                        <i class="fas fa-plus mr-2"></i> New Transaction
                    </a>
                </div>
            </div>
        @endif
    </div>
</div>
