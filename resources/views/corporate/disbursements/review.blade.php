@extends('corporate.layouts.app')

@section('title', 'Review Disbursement - ' . config('app.name'))
@section('meta_description', 'Review and confirm your bulk disbursement')
@section('header_title', 'Review Disbursement')

@section('content')
    <div class="mb-6">
        <div class="flex items-center mb-2">
            <a href="{{ route('corporate.disbursements.show-validation') }}" class="text-corporate-primary hover:underline">
                <i class="fas fa-arrow-left mr-2"></i> Back to Validation
            </a>
        </div>
        <h2 class="text-xl font-bold text-corporate-primary">Review Disbursement</h2>
        <p class="text-gray-500">Confirm details before submitting</p>
    </div>

    <!-- Step Indicator -->
    <div class="bg-white rounded-xl shadow-sm overflow-hidden mb-6">
        <div class="p-6">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <div class="flex items-center">
                        <div class="w-8 h-8 rounded-full bg-corporate-success text-white flex items-center justify-center text-sm font-medium">
                            <i class="fas fa-check"></i>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-corporate-success">Upload File</p>
                            <p class="text-xs text-gray-500">Upload recipient data</p>
                        </div>
                    </div>
                    <div class="h-1 bg-corporate-success mt-3"></div>
                </div>
                <div class="flex-1">
                    <div class="flex items-center">
                        <div class="w-8 h-8 rounded-full bg-corporate-success text-white flex items-center justify-center text-sm font-medium">
                            <i class="fas fa-check"></i>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-corporate-success">Validate</p>
                            <p class="text-xs text-gray-500">Review validation results</p>
                        </div>
                    </div>
                    <div class="h-1 bg-corporate-success mt-3"></div>
                </div>
                <div class="flex-1">
                    <div class="flex items-center">
                        <div class="w-8 h-8 rounded-full bg-corporate-primary text-white flex items-center justify-center text-sm font-medium">3</div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-corporate-primary">Review</p>
                            <p class="text-xs text-gray-500">Confirm details</p>
                        </div>
                    </div>
                    <div class="h-1 bg-corporate-primary mt-3"></div>
                </div>
                <div class="flex-1">
                    <div class="flex items-center">
                        <div class="w-8 h-8 rounded-full bg-gray-200 text-gray-500 flex items-center justify-center text-sm font-medium">4</div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-gray-500">Submit</p>
                            <p class="text-xs text-gray-500">Process disbursement</p>
                        </div>
                    </div>
                    <div class="h-1 bg-gray-200 mt-3"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Left Column - Disbursement Details -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Disbursement Summary -->
            <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-corporate-primary mb-4">Disbursement Summary</h3>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                        <div>
                            <p class="text-sm text-gray-500 mb-1">Name</p>
                            <p class="text-base font-medium text-gray-900">{{ $disbursement->name }}</p>
                        </div>
                        @if($disbursement->description)
                            <div>
                                <p class="text-sm text-gray-500 mb-1">Description</p>
                                <p class="text-base text-gray-900">{{ $disbursement->description }}</p>
                            </div>
                        @endif
                        <div>
                            <p class="text-sm text-gray-500 mb-1">Reference Number</p>
                            <p class="text-base font-medium text-gray-900">{{ $disbursement->reference_number }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 mb-1">Created By</p>
                            <div class="flex items-center">
                                <div class="w-6 h-6 rounded-full bg-corporate-primary text-white flex items-center justify-center text-xs mr-2">
                                    {{ substr(auth()->user()->first_name, 0, 1) . substr(auth()->user()->last_name, 0, 1) }}
                                </div>
                                <p class="text-base font-medium text-gray-900">{{ auth()->user()->name }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
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

                    @if($skippedErrors)
                        <div class="mt-6 bg-corporate-warning bg-opacity-10 text-corporate-warning rounded-lg p-4">
                            <h4 class="font-medium mb-2"><i class="fas fa-exclamation-triangle mr-2"></i> Proceeding with Valid Entries Only</h4>
                            <p class="text-sm">{{ $skippedErrors }} invalid entries have been excluded from this disbursement.</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Recipient Data -->
            <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                <div class="p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold text-corporate-primary">Recipient Data</h3>
                        <div class="flex items-center">
                            <form action="{{ route('corporate.disbursements.review') }}" method="GET" class="flex space-x-2">
                                <select name="provider_filter" class="px-3 py-2 border rounded-lg focus:ring-2 focus:ring-corporate-primary focus:border-corporate-primary text-sm">
                                    <option value="">All Providers</option>
                                    @foreach($walletProviders as $provider)
                                        <option value="{{ $provider->api_code }}" {{ request('provider_filter') == $provider->api_code ? 'selected' : '' }}>{{ $provider->name }}</option>
                                    @endforeach
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
                                    <th class="text-left text-xs font-medium text-gray-500 uppercase tracking-wider py-3">Wallet Number</th>
                                    <th class="text-left text-xs font-medium text-gray-500 uppercase tracking-wider py-3">Provider</th>
                                    <th class="text-left text-xs font-medium text-gray-500 uppercase tracking-wider py-3">Recipient</th>
                                    <th class="text-left text-xs font-medium text-gray-500 uppercase tracking-wider py-3">Amount</th>
                                    <th class="text-left text-xs font-medium text-gray-500 uppercase tracking-wider py-3">Fee</th>
                                    <th class="text-left text-xs font-medium text-gray-500 uppercase tracking-wider py-3">Reference</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                @forelse($items as $item)
                                    <tr>
                                        <td class="whitespace-nowrap">
                                            <div class="text-sm text-gray-900">{{ $item->wallet_number }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                @if($item->walletProvider)
                                                    <img src="{{ asset('assets/img/' . strtolower($item->walletProvider->api_code) . '.png') }}" alt="{{ $item->walletProvider->name }}" class="w-6 h-6 rounded-full mr-2">
                                                    <div class="text-sm text-gray-900">{{ $item->walletProvider->name }}</div>
                                                @else
                                                    <div class="text-sm text-gray-900">Unknown</div>
                                                @endif
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900">{{ $item->recipient_name ?? 'N/A' }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900">{{ $item->currency }} {{ number_format($item->amount, 2) }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900">{{ $item->currency }} {{ number_format($item->fee, 2) }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900">{{ $item->reference ?? 'N/A' }}</div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                                            No items found for this disbursement.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-6">
                        {{ $items->appends(request()->query())->links() }}
                    </div>
                </div>
            </div>

            <!-- Approval Requirements -->
            @if($requiresApproval)
                <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-corporate-primary mb-4">Approval Requirements</h3>

                        <div class="bg-blue-50 p-4 rounded-lg mb-6">
                            <div class="flex items-start">
                                <div class="flex-shrink-0 w-6 h-6 rounded-full bg-blue-100 text-blue-500 flex items-center justify-center text-xs mr-3">
                                    <i class="fas fa-info-circle"></i>
                                </div>
                                <div>
                                    <h4 class="text-sm font-medium text-blue-700">This disbursement requires approval</h4>
                                    <p class="text-sm text-blue-600 mt-1">
                                        Based on your company's approval workflow, this disbursement requires approval from {{ $minApprovers }} {{ Str::plural('approver', $minApprovers) }} before processing.
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <p class="text-sm text-gray-500 mb-1">Minimum Approvers Required</p>
                                <p class="text-base font-medium text-gray-900">{{ $minApprovers }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500 mb-1">Available Approvers</p>
                                <p class="text-base font-medium text-gray-900">{{ $availableApprovers }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500 mb-1">Approval Workflow</p>
                                <p class="text-base font-medium text-gray-900">Bulk Disbursement</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500 mb-1">Approval Expiry</p>
                                <p class="text-base font-medium text-gray-900">48 hours after submission</p>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Action Buttons -->
            <div class="flex justify-between">
                <a href="{{ route('corporate.disbursements.show-validation') }}" class="px-6 py-3 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition">
                    <i class="fas fa-arrow-left mr-2"></i> Back to Validation
                </a>

                <form action="{{ route('corporate.disbursements.submit') }}" method="POST">
                    @csrf
                    <button type="submit" class="px-6 py-3 bg-corporate-primary text-white rounded-lg hover:bg-opacity-90 transition">
                        @if($requiresApproval)
                            Submit for Approval <i class="fas fa-arrow-right ml-2"></i>
                        @else
                            Process Disbursement <i class="fas fa-arrow-right ml-2"></i>
                        @endif
                    </button>
                </form>
            </div>
        </div>

        <!-- Right Column - Summary & Info -->
        <div class="space-y-6">
            <!-- Wallet Balance -->
            <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-corporate-primary mb-4">Wallet Balance</h3>
                    <div class="flex items-center">
                        <div class="w-12 h-12 rounded-full bg-corporate-primary bg-opacity-10 flex items-center justify-center text-corporate-primary mr-4">
                            <i class="fas fa-wallet text-xl"></i>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Available Balance</p>
                            <p class="text-2xl font-bold text-corporate-primary">{{ $wallet->currency }} {{ number_format($wallet->balance, 2) }}</p>
                        </div>
                    </div>

                    <!-- Transaction Summary -->
                    <div class="mt-6 pt-6 border-t border-gray-200">
                        <h4 class="font-medium text-corporate-primary mb-3">Transaction Summary</h4>
                        <div class="space-y-3">
                            <div class="flex justify-between">
                                <span class="text-sm text-gray-500">Total Amount:</span>
                                <span class="text-sm font-medium text-gray-900">{{ $disbursement->currency }} {{ number_format($disbursement->total_amount, 2) }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-sm text-gray-500">Fee ({{ \App\Models\Transaction::getCorporateFeeDescription() }}):</span>
                                <span class="text-sm font-medium text-gray-900">{{ $disbursement->currency }} {{ number_format($disbursement->total_fee, 2) }}</span>
                            </div>
                            <div class="flex justify-between pt-2 border-t border-gray-100">
                                <span class="text-sm font-medium text-gray-700">Total Debit:</span>
                                <span class="text-sm font-bold text-corporate-primary">{{ $disbursement->currency }} {{ number_format($disbursement->total_amount + $disbursement->total_fee, 2) }}</span>
                            </div>
                        </div>
                    </div>

                    @if(($disbursement->total_amount + $disbursement->total_fee) > $wallet->balance)
                        <div class="mt-4 bg-corporate-error bg-opacity-10 text-corporate-error rounded-lg p-3 text-sm">
                            <i class="fas fa-exclamation-circle mr-2"></i> Insufficient balance. Please add funds to your wallet.
                            <div class="mt-2">
                                <a href="{{ route('corporate.wallet.deposit') }}" class="text-corporate-error underline">
                                    Add Funds
                                </a>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Provider Breakdown -->
            <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-corporate-primary mb-4">Provider Breakdown</h3>

                    <div class="space-y-4">
                        @foreach($providerBreakdown as $provider)
                            <div class="bg-gray-50 p-3 rounded-lg">
                                <div class="flex items-center">
                                    <img src="{{ asset('assets/img/' . strtolower($provider['code']) . '.png') }}" alt="{{ $provider['name'] }}" class="w-8 h-8 rounded-full mr-3">
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">{{ $provider['name'] }}</p>
                                        <div class="flex items-center text-xs text-gray-500 mt-1">
                                            <span>{{ number_format($provider['count']) }} {{ Str::plural('recipient', $provider['count']) }}</span>
                                            <span class="mx-2">•</span>
                                            <span>{{ $disbursement->currency }} {{ number_format($provider['amount'], 2) }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Important Information -->
            <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-corporate-primary mb-4">Important Information</h3>

                    <div class="space-y-4">
                        <div>
                            <h4 class="font-medium text-gray-900 mb-1">Processing Time</h4>
                            <p class="text-sm text-gray-600">Transactions are typically processed within 5-15 minutes after approval.</p>
                        </div>

                        <div>
                            <h4 class="font-medium text-gray-900 mb-1">Transaction Notifications</h4>
                            <p class="text-sm text-gray-600">Recipients will receive SMS notifications from their wallet providers.</p>
                        </div>

                        <div class="bg-blue-50 p-3 rounded-lg">
                            <h4 class="font-medium text-blue-700 mb-1">Need Help?</h4>
                            <p class="text-sm text-blue-600">If you encounter any issues, please contact our support team at <a href="mailto:support@cardtowallet.com" class="underline">support@cardtowallet.com</a></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
