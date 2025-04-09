@extends('corporate.layouts.app')

@section('title', 'Card Payment')

@section('content')
<div class="mb-6">
    <div class="flex items-center mb-2">
        <a href="{{ route('corporate.wallet.deposit', ['method' => 'card']) }}" class="text-primary hover:underline">
            <i class="fas fa-arrow-left mr-2"></i> Back to Deposit
        </a>
    </div>
    <h2 class="text-xl font-bold text-gray-800">Card Payment</h2>
    <p class="text-gray-500">Complete your deposit with card payment</p>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Main Content -->
    <div class="lg:col-span-2 space-y-6">
        <!-- Payment Details -->
        <div class="bg-white rounded-xl shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="font-semibold text-gray-800">Payment Details</h3>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div>
                        <p class="text-sm text-gray-500 mb-1">Amount</p>
                        <p class="text-xl font-bold text-primary">{{ $transaction->currency }} {{ number_format($transaction->amount, 2) }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 mb-1">Reference</p>
                        <p class="text-base font-medium text-gray-900">{{ $transaction->reference_number }}</p>
                    </div>
                </div>
                
                <div class="bg-gray-50 rounded-lg p-4 mb-6">
                    <div class="flex items-center">
                        <div class="w-10 h-10 rounded-full bg-primary bg-opacity-10 flex items-center justify-center text-primary mr-4">
                            <i class="fas fa-info-circle"></i>
                        </div>
                        <div>
                            <h4 class="font-medium text-gray-900">Secure Payment</h4>
                            <p class="text-sm text-gray-600">You will be redirected to our secure payment gateway to complete this transaction.</p>
                        </div>
                    </div>
                </div>
                
                <!-- MPGS Payment Form -->
                <div id="payment-form" class="border border-gray-200 rounded-lg p-6">
                    <div id="loading" class="text-center py-6">
                        <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-primary mb-4"></div>
                        <p class="text-gray-600">Initializing secure payment...</p>
                    </div>
                    
                    <div id="error-message" class="hidden bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                        <p></p>
                    </div>
                    
                    <!-- MPGS Checkout will be loaded here -->
                    <div id="mpgs-checkout"></div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Sidebar -->
    <div class="space-y-6">
        <!-- Payment Summary -->
        <div class="bg-white rounded-xl shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="font-semibold text-gray-800">Payment Summary</h3>
            </div>
            <div class="p-6">
                <div class="space-y-3">
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-500">Deposit Amount</span>
                        <span class="text-sm font-medium text-gray-900">{{ $transaction->currency }} {{ number_format($transaction->amount, 2) }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-500">Processing Fee</span>
                        <span class="text-sm font-medium text-gray-900">{{ $transaction->currency }} 0.00</span>
                    </div>
                    <div class="border-t border-gray-200 pt-3 mt-3">
                        <div class="flex justify-between">
                            <span class="text-base font-medium text-gray-900">Total</span>
                            <span class="text-base font-bold text-primary">{{ $transaction->currency }} {{ number_format($transaction->amount, 2) }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Secure Payment -->
        <div class="bg-white rounded-xl shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="font-semibold text-gray-800">Secure Payment</h3>
            </div>
            <div class="p-6">
                <ul class="space-y-3 text-sm text-gray-600">
                    <li class="flex">
                        <i class="fas fa-lock text-primary mt-1 mr-2"></i>
                        <span>Your payment information is encrypted and secure</span>
                    </li>
                    <li class="flex">
                        <i class="fas fa-shield-alt text-primary mt-1 mr-2"></i>
                        <span>We use industry-standard security protocols</span>
                    </li>
                    <li class="flex">
                        <i class="fas fa-credit-card text-primary mt-1 mr-2"></i>
                        <span>We accept Visa, Mastercard, and American Express</span>
                    </li>
                    <li class="flex">
                        <i class="fas fa-check-circle text-primary mt-1 mr-2"></i>
                        <span>Your wallet will be credited immediately after successful payment</span>
                    </li>
                </ul>
                
                <div class="flex justify-center mt-6 space-x-4">
                    <img src="https://cdn.jsdelivr.net/gh/stephenhutchings/typicons.font@master/src/svg/credit-card.svg" alt="Visa" class="h-8 opacity-60">
                    <img src="https://cdn.jsdelivr.net/gh/stephenhutchings/typicons.font@master/src/svg/credit-card.svg" alt="Mastercard" class="h-8 opacity-60">
                    <img src="https://cdn.jsdelivr.net/gh/stephenhutchings/typicons.font@master/src/svg/credit-card.svg" alt="American Express" class="h-8 opacity-60">
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const mpgsEndpoint = "{{ $mpgs_endpoint }}";
        const loadingElement = document.getElementById('loading');
        const errorElement = document.getElementById('error-message');
        const errorText = errorElement.querySelector('p');
        
        // Function to show error
        function showError(message) {
            loadingElement.classList.add('hidden');
            errorElement.classList.remove('hidden');
            errorText.textContent = message;
        }
        
        // Initialize MPGS checkout
        function initializeCheckout() {
            // Make AJAX request to get session ID
            fetch("{{ route('corporate.wallet.mpgs-checkout') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'SUCCESS') {
                    loadingElement.classList.add('hidden');
                    
                    // Initialize MPGS Checkout
                    Checkout.configure({
                        merchant: '{{ config('app.name') }}',
                        session: { 
                            id: data.session
                        },
                        interaction: {
                            merchant: {
                                name: '{{ config('app.name') }}',
                                address: {
                                    line1: 'Lusaka',
                                    line2: 'Zambia'            
                                }
                            },
                            displayControl: {
                                billingAddress: 'HIDE',
                                customerEmail: 'HIDE',
                                orderSummary: 'HIDE',
                                shipping: 'HIDE'
                            }
                        }
                    });
                    
                    // Open the payment page
                    Checkout.showPaymentPage();
                } else {
                    showError(data.statusMessage || 'An error occurred while initializing payment.');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showError('An error occurred while initializing payment. Please try again.');
            });
        }
        
        // Load MPGS script
        const script = document.createElement('script');
        script.src = mpgsEndpoint + '/checkout/version/60/checkout.js';
        script.async = true;
        script.onload = initializeCheckout;
        script.onerror = function() {
            showError('Failed to load payment gateway. Please try again later.');
        };
        document.head.appendChild(script);
    });
</script>
@endpush
