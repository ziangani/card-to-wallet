@extends('corporate.layouts.app')

@section('title', 'Corporate Wallet')

@section('content')
<div class="mb-6">
    <h2 class="text-xl font-bold text-gray-800">Corporate Wallet</h2>
    <p class="text-gray-500">Manage your corporate wallet and transactions</p>
</div>

<!-- Wallet Summary -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
    <!-- Balance Card -->
    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="font-semibold text-gray-800">Wallet Balance</h3>
        </div>
        <div class="p-6">
            <div class="flex items-center mb-4">
                <div class="w-12 h-12 rounded-full bg-primary bg-opacity-10 flex items-center justify-center text-primary mr-4">
                    <i class="fas fa-wallet text-xl"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Available Balance</p>
                    <p class="text-2xl font-bold text-primary">{{ $wallet->currency }} {{ number_format($wallet->balance, 2) }}</p>
                </div>
            </div>

            <div class="flex space-x-2">
                <a href="{{ route('corporate.wallet.deposit') }}" class="flex-1 px-4 py-2 bg-primary text-white rounded-lg hover:bg-opacity-90 text-center">
                    <i class="fas fa-plus mr-2"></i> Deposit
                </a>
                <a href="{{ route('corporate.wallet.transactions') }}" class="flex-1 px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 text-center">
                    <i class="fas fa-history mr-2"></i> Transactions
                </a>
            </div>
        </div>
    </div>

    <!-- Transaction Stats -->
    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="font-semibold text-gray-800">Transaction Summary</h3>
        </div>
        <div class="p-6">
            <div class="space-y-4">
                <div>
                    <p class="text-sm text-gray-500 mb-1">Total Deposits</p>
                    <p class="text-xl font-bold text-green-600">{{ $wallet->currency }} {{ number_format($transactionStats['deposits'], 2) }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500 mb-1">Total Withdrawals</p>
                    <p class="text-xl font-bold text-red-600">{{ $wallet->currency }} {{ number_format($transactionStats['withdrawals'], 2) }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500 mb-1">Total Fees</p>
                    <p class="text-xl font-bold text-yellow-600">{{ $wallet->currency }} {{ number_format($transactionStats['fees'], 2) }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="font-semibold text-gray-800">Quick Actions</h3>
        </div>
        <div class="p-6">
            <div class="space-y-3">
                <a href="{{ route('corporate.wallet.deposit', ['method' => 'card']) }}" class="flex items-center p-3 border border-gray-200 rounded-lg hover:bg-gray-50">
                    <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 mr-3">
                        <i class="fas fa-credit-card"></i>
                    </div>
                    <div>
                        <p class="font-medium text-gray-900">Card Deposit</p>
                        <p class="text-sm text-gray-500">Deposit funds using credit/debit card</p>
                    </div>
                    <i class="fas fa-chevron-right ml-auto text-gray-400"></i>
                </a>

                <a href="{{ route('corporate.wallet.deposit', ['method' => 'bank']) }}" class="flex items-center p-3 border border-gray-200 rounded-lg hover:bg-gray-50">
                    <div class="w-10 h-10 rounded-full bg-green-100 flex items-center justify-center text-green-600 mr-3">
                        <i class="fas fa-university"></i>
                    </div>
                    <div>
                        <p class="font-medium text-gray-900">Bank Transfer</p>
                        <p class="text-sm text-gray-500">Deposit via bank transfer</p>
                    </div>
                    <i class="fas fa-chevron-right ml-auto text-gray-400"></i>
                </a>

                <a href="{{ route('corporate.disbursements.create') }}" class="flex items-center p-3 border border-gray-200 rounded-lg hover:bg-gray-50">
                    <div class="w-10 h-10 rounded-full bg-purple-100 flex items-center justify-center text-purple-600 mr-3">
                        <i class="fas fa-paper-plane"></i>
                    </div>
                    <div>
                        <p class="font-medium text-gray-900">New Disbursement</p>
                        <p class="text-sm text-gray-500">Create a new bulk disbursement</p>
                    </div>
                    <i class="fas fa-chevron-right ml-auto text-gray-400"></i>
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Recent Transactions -->
<div class="bg-white rounded-xl shadow-sm overflow-hidden mb-6">
    <div class="px-6 py-4 border-b border-gray-200">
        <div class="flex justify-between items-center">
            <h3 class="font-semibold text-gray-800">Recent Transactions</h3>
            <a href="{{ route('corporate.wallet.transactions') }}" class="text-primary hover:underline text-sm">
                View All <i class="fas fa-arrow-right ml-1"></i>
            </a>
        </div>
    </div>

    @if($recentTransactions->isEmpty())
        <div class="p-6 text-center">
            <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-gray-100 text-gray-400 mb-4">
                <i class="fas fa-exchange-alt text-2xl"></i>
            </div>
            <h3 class="text-lg font-medium text-gray-900 mb-1">No transactions yet</h3>
            <p class="text-gray-500">Your transaction history will appear here</p>
        </div>
    @else
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50">
                        <th class="px-6 py-3 text-left font-medium text-gray-500">Date</th>
                        <th class="px-6 py-3 text-left font-medium text-gray-500">Reference</th>
                        <th class="px-6 py-3 text-left font-medium text-gray-500">Type</th>
                        <th class="px-6 py-3 text-left font-medium text-gray-500">Description</th>
                        <th class="px-6 py-3 text-right font-medium text-gray-500">Amount</th>
                        <th class="px-6 py-3 text-center font-medium text-gray-500">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($recentTransactions as $transaction)
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
    @endif
</div>

<!-- Wallet Information -->
<div class="bg-white rounded-xl shadow-sm overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-200">
        <h3 class="font-semibold text-gray-800">Wallet Information</h3>
    </div>
    <div class="p-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <h4 class="text-base font-medium text-gray-900 mb-4">About Your Corporate Wallet</h4>
                <ul class="space-y-3 text-sm text-gray-600">
                    <li class="flex">
                        <i class="fas fa-check-circle text-primary mt-1 mr-2"></i>
                        <span>Your corporate wallet allows you to manage funds for bulk disbursements</span>
                    </li>
                    <li class="flex">
                        <i class="fas fa-check-circle text-primary mt-1 mr-2"></i>
                        <span>Deposit funds via card payment, bank transfer, or mobile money</span>
                    </li>
                    <li class="flex">
                        <i class="fas fa-check-circle text-primary mt-1 mr-2"></i>
                        <span>Create bulk disbursements to multiple recipients</span>
                    </li>
                    <li class="flex">
                        <i class="fas fa-check-circle text-primary mt-1 mr-2"></i>
                        <span>Track all transactions with detailed reporting</span>
                    </li>
                </ul>
            </div>

            <div>
                <h4 class="text-base font-medium text-gray-900 mb-4">Wallet Details</h4>
                <div class="bg-gray-50 rounded-lg p-4">
                    <dl class="space-y-3">
                        <div class="flex justify-between">
                            <dt class="text-sm text-gray-500">Company</dt>
                            <dd class="text-sm font-medium text-gray-900">{{ $company->name }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-sm text-gray-500">Currency</dt>
                            <dd class="text-sm font-medium text-gray-900">{{ $wallet->currency }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-sm text-gray-500">Status</dt>
                            <dd class="text-sm font-medium">
                                @if($wallet->status === 'active')
                                    <span class="text-green-600">
                                        <i class="fas fa-circle text-xs mr-1"></i> Active
                                    </span>
                                @else
                                    <span class="text-red-600">
                                        <i class="fas fa-circle text-xs mr-1"></i> {{ ucfirst($wallet->status) }}
                                    </span>
                                @endif
                            </dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-sm text-gray-500">Created</dt>
                            <dd class="text-sm font-medium text-gray-900">{{ $wallet->created_at->format('M d, Y') }}</dd>
                        </div>
                    </dl>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
