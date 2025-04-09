@extends('corporate.layouts.app')

@section('title', 'Deposit Funds')

@section('content')
<div class="mb-6">
    <div class="flex items-center mb-2">
        <a href="{{ route('corporate.wallet.index') }}" class="text-primary hover:underline">
            <i class="fas fa-arrow-left mr-2"></i> Back to Wallet
        </a>
    </div>
    <h2 class="text-xl font-bold text-gray-800">Deposit Funds</h2>
    <p class="text-gray-500">Add funds to your corporate wallet</p>
</div>

<!-- Wallet Summary -->
<div class="bg-white rounded-xl shadow-sm p-6 mb-6">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
            <h3 class="text-sm font-medium text-gray-500 mb-1">Current Balance</h3>
            <p class="text-2xl font-bold text-primary">{{ $wallet->currency }} {{ number_format($wallet->balance, 2) }}</p>
        </div>
        <div>
            <h3 class="text-sm font-medium text-gray-500 mb-1">Wallet ID</h3>
            <p class="text-base font-medium text-gray-900">{{ $wallet->id }}</p>
        </div>
    </div>
</div>

<!-- Deposit Methods -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Card Deposit -->
    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="font-semibold text-gray-800">Card Deposit</h3>
        </div>
        <div class="p-6">
            <div class="flex items-center mb-4">
                <div class="w-12 h-12 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 mr-4">
                    <i class="fas fa-credit-card text-xl"></i>
                </div>
                <div>
                    <h4 class="text-lg font-medium text-gray-900">Credit/Debit Card</h4>
                    <p class="text-sm text-gray-500">Instant deposit using your card</p>
                </div>
            </div>
            
            <form action="{{ route('corporate.wallet.process-card-deposit') }}" method="POST" class="space-y-4">
                @csrf
                
                <div>
                    <label for="amount" class="block text-sm font-medium text-gray-700 mb-1">Amount ({{ $wallet->currency }})</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <span class="text-gray-500">{{ $wallet->currency }}</span>
                        </div>
                        <input type="number" id="amount" name="amount" min="100" step="0.01" class="w-full pl-12 pr-4 py-2 border rounded-lg focus:ring-2 focus:ring-primary focus:border-primary @error('amount') border-red-500 @enderror" required>
                    </div>
                    @error('amount')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-xs text-gray-500">Minimum deposit amount: {{ $wallet->currency }} 100.00</p>
                </div>
                
                <div class="bg-gray-50 rounded-lg p-4 mb-2">
                    <div class="flex items-center">
                        <div class="w-8 h-8 rounded-full bg-primary bg-opacity-10 flex items-center justify-center text-primary mr-3">
                            <i class="fas fa-info-circle"></i>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">You will be redirected to our secure payment gateway to complete this transaction.</p>
                        </div>
                    </div>
                </div>
                
                <div>
                    <button type="submit" class="w-full px-4 py-2 bg-primary text-white rounded-lg hover:bg-opacity-90">
                        Continue to Payment
                    </button>
                </div>
            </form>
        </div>
    </div>
    
    <!-- Bank Transfer -->
    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="font-semibold text-gray-800">Bank Transfer</h3>
        </div>
        <div class="p-6">
            <div class="flex items-center mb-4">
                <div class="w-12 h-12 rounded-full bg-green-100 flex items-center justify-center text-green-600 mr-4">
                    <i class="fas fa-university text-xl"></i>
                </div>
                <div>
                    <h4 class="text-lg font-medium text-gray-900">Bank Transfer</h4>
                    <p class="text-sm text-gray-500">Transfer from your bank account</p>
                </div>
            </div>
            
            <div class="bg-gray-50 rounded-lg p-4 mb-4">
                <h5 class="font-medium text-gray-900 mb-2">Bank Account Details</h5>
                <div class="space-y-2">
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-500">Bank Name</span>
                        <span class="text-sm font-medium text-gray-900">First National Bank</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-500">Account Name</span>
                        <span class="text-sm font-medium text-gray-900">TechPay Ltd</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-500">Account Number</span>
                        <span class="text-sm font-medium text-gray-900">1234567890</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-500">Branch Code</span>
                        <span class="text-sm font-medium text-gray-900">250655</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-500">Reference</span>
                        <span class="text-sm font-medium text-gray-900">{{ $company->name }} - {{ $reference }}</span>
                    </div>
                </div>
            </div>
            
            <form action="{{ route('corporate.wallet.notify-deposit') }}" method="POST" class="space-y-4" enctype="multipart/form-data">
                @csrf
                
                <input type="hidden" name="reference" value="{{ $reference }}">
                <input type="hidden" name="payment_method" value="bank_transfer">
                
                <div>
                    <label for="bank_amount" class="block text-sm font-medium text-gray-700 mb-1">Amount ({{ $wallet->currency }})</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <span class="text-gray-500">{{ $wallet->currency }}</span>
                        </div>
                        <input type="number" id="bank_amount" name="amount" min="1" step="0.01" class="w-full pl-12 pr-4 py-2 border rounded-lg focus:ring-2 focus:ring-primary focus:border-primary @error('amount') border-red-500 @enderror" required>
                    </div>
                    @error('amount')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                
                <div>
                    <label for="payment_date" class="block text-sm font-medium text-gray-700 mb-1">Payment Date</label>
                    <input type="date" id="payment_date" name="payment_date" class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-primary focus:border-primary @error('payment_date') border-red-500 @enderror" required>
                    @error('payment_date')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                
                <div>
                    <label for="proof_of_payment" class="block text-sm font-medium text-gray-700 mb-1">Proof of Payment (Optional)</label>
                    <input type="file" id="proof_of_payment" name="proof_of_payment" class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-primary focus:border-primary @error('proof_of_payment') border-red-500 @enderror">
                    @error('proof_of_payment')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-xs text-gray-500">Accepted formats: PDF, JPG, JPEG, PNG (max 2MB)</p>
                </div>
                
                <div>
                    <label for="notes" class="block text-sm font-medium text-gray-700 mb-1">Notes (Optional)</label>
                    <textarea id="notes" name="notes" rows="2" class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-primary focus:border-primary @error('notes') border-red-500 @enderror"></textarea>
                    @error('notes')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                
                <div>
                    <button type="submit" class="w-full px-4 py-2 bg-primary text-white rounded-lg hover:bg-opacity-90">
                        Notify of Payment
                    </button>
                </div>
            </form>
        </div>
    </div>
    
    <!-- Mobile Money -->
    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="font-semibold text-gray-800">Mobile Money</h3>
        </div>
        <div class="p-6">
            <div class="flex items-center mb-4">
                <div class="w-12 h-12 rounded-full bg-purple-100 flex items-center justify-center text-purple-600 mr-4">
                    <i class="fas fa-mobile-alt text-xl"></i>
                </div>
                <div>
                    <h4 class="text-lg font-medium text-gray-900">Mobile Money</h4>
                    <p class="text-sm text-gray-500">Deposit using mobile money</p>
                </div>
            </div>
            
            <div class="bg-gray-50 rounded-lg p-4 mb-4">
                <h5 class="font-medium text-gray-900 mb-2">Mobile Money Details</h5>
                <div class="space-y-2">
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-500">MTN Mobile Money</span>
                        <span class="text-sm font-medium text-gray-900">0977 123 456</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-500">Airtel Money</span>
                        <span class="text-sm font-medium text-gray-900">0966 123 456</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-500">Account Name</span>
                        <span class="text-sm font-medium text-gray-900">TechPay Ltd</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-500">Reference</span>
                        <span class="text-sm font-medium text-gray-900">{{ $company->name }} - {{ $reference }}</span>
                    </div>
                </div>
            </div>
            
            <form action="{{ route('corporate.wallet.notify-deposit') }}" method="POST" class="space-y-4" enctype="multipart/form-data">
                @csrf
                
                <input type="hidden" name="reference" value="{{ $reference }}">
                <input type="hidden" name="payment_method" value="mobile_money">
                
                <div>
                    <label for="mobile_amount" class="block text-sm font-medium text-gray-700 mb-1">Amount ({{ $wallet->currency }})</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <span class="text-gray-500">{{ $wallet->currency }}</span>
                        </div>
                        <input type="number" id="mobile_amount" name="amount" min="1" step="0.01" class="w-full pl-12 pr-4 py-2 border rounded-lg focus:ring-2 focus:ring-primary focus:border-primary @error('amount') border-red-500 @enderror" required>
                    </div>
                    @error('amount')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                
                <div>
                    <label for="mobile_payment_date" class="block text-sm font-medium text-gray-700 mb-1">Payment Date</label>
                    <input type="date" id="mobile_payment_date" name="payment_date" class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-primary focus:border-primary @error('payment_date') border-red-500 @enderror" required>
                    @error('payment_date')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                
                <div>
                    <label for="mobile_proof_of_payment" class="block text-sm font-medium text-gray-700 mb-1">Proof of Payment (Optional)</label>
                    <input type="file" id="mobile_proof_of_payment" name="proof_of_payment" class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-primary focus:border-primary @error('proof_of_payment') border-red-500 @enderror">
                    @error('proof_of_payment')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-xs text-gray-500">Accepted formats: PDF, JPG, JPEG, PNG (max 2MB)</p>
                </div>
                
                <div>
                    <label for="mobile_notes" class="block text-sm font-medium text-gray-700 mb-1">Notes (Optional)</label>
                    <textarea id="mobile_notes" name="notes" rows="2" class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-primary focus:border-primary @error('notes') border-red-500 @enderror"></textarea>
                    @error('notes')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                
                <div>
                    <button type="submit" class="w-full px-4 py-2 bg-primary text-white rounded-lg hover:bg-opacity-90">
                        Notify of Payment
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
