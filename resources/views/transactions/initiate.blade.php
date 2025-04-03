@extends('layouts.app')

@section('title', 'New Transaction - ' . config('app.name'))
@section('meta_description', 'Send money to a mobile wallet')
@section('header_title', 'New Transaction')

@push('pre-styles')
<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<!-- MPGS Checkout -->
<script src="{{$mpgs_endpoint}}/static/checkout/checkout.min.js" data-error="errorCallback"
        data-cancel="cancelCallback"></script>
<script type="text/javascript">
    function errorCallback(error) {
        console.log(JSON.stringify(error));
        Swal.fire({
            icon: 'error',
            title: 'Payment Error',
            text: JSON.stringify(error),
            confirmButtonColor: '#3366CC'
        });
    }

    function cancelCallback() {
        console.log('Payment cancelled');
        Swal.fire({
            icon: 'warning',
            title: 'Payment Cancelled',
            text: 'Your payment process was cancelled',
            confirmButtonColor: '#3366CC'
        });
    }
</script>
@endpush

@push('styles')
<style>
    .step-item {
        position: relative;
    }
    .step-item:not(:last-child)::after {
        content: '';
        position: absolute;
        top: 1.5rem;
        left: 2.25rem;
        height: calc(100% - 1.5rem);
        width: 1px;
        background-color: #E5E7EB;
    }
    .step-item.active .step-circle {
        background-color: #3366CC;
        color: white;
    }
    .step-item.completed .step-circle {
        background-color: #28A745;
        color: white;
    }
    /* Compact layout styles */
    .compact-card {
        box-shadow: 0 1px 2px 0 rgba(0,0,0,0.05);
        transition: all 0.2s ease;
    }
    .compact-card:hover {
        box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1), 0 2px 4px -1px rgba(0,0,0,0.06);
    }
    .compact-steps {
        font-size: 0.9rem;
    }
    .compact-steps .step-number {
        width: 24px;
        height: 24px;
        font-size: 0.8rem;
    }
</style>
@endpush

@section('content')
    <!-- Transaction Container -->
    <div class="bg-white rounded-xl shadow-sm overflow-hidden mb-4">
        <div class="p-4">
            <!-- Header with Navigation -->
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-lg font-bold text-dark">Send Money to Mobile Wallet</h2>
                <div class="flex space-x-2">
                    <a href="{{ route('dashboard') }}" class="inline-flex items-center px-2 py-1 bg-gray-100 hover:bg-gray-200 rounded text-xs text-gray-700 transition">
                        <i class="fas fa-arrow-left mr-1"></i> Dashboard
                    </a>
                    <a href="{{ route('transactions.history') }}" class="inline-flex items-center px-2 py-1 bg-gray-100 hover:bg-gray-200 rounded text-xs text-gray-700 transition">
                        <i class="fas fa-history mr-1"></i> History
                    </a>
                </div>
            </div>

            <!-- Balance and Limit Info - Compact Row -->
            <div class="flex justify-between items-center mb-4 p-2 bg-gray-50 rounded-lg text-sm">
                <div class="flex items-center">
                    <i class="fas fa-wallet text-primary mr-2"></i>
                    <div>
                        <span class="text-gray-500">Balance:</span>
                        <span class="font-bold ml-1">K{{ number_format(auth()->user()->balance ?? 0, 2) }}</span>
                    </div>
                </div>
                <div class="flex items-center">
                    <i class="fas fa-exchange-alt text-primary mr-2"></i>
                    <div>
                        <span class="text-gray-500">Limit:</span>
                        <span class="font-bold ml-1">K{{ number_format(auth()->user()->verification_level === 'verified' ? 5000 : 1000, 2) }}</span>
                        <span class="text-xs text-gray-500 ml-1">({{ auth()->user()->verification_level === 'verified' ? 'Verified' : 'Basic' }})</span>
                    </div>
                </div>
            </div>

            <!-- Steps Progress - Compact -->
            <div class="mb-4 bg-gray-50 p-2 rounded-lg">
                <div class="flex justify-between compact-steps">
                    <div class="flex items-center">
                        <div class="step-number w-6 h-6 rounded-full bg-primary text-white flex items-center justify-center text-xs">1</div>
                        <div class="ml-2">
                            <h3 class="font-medium text-dark text-sm">Recipient Details</h3>
                        </div>
                    </div>
                    <div class="flex items-center">
                        <div class="step-number w-6 h-6 rounded-full bg-gray-200 text-gray-600 flex items-center justify-center text-xs">2</div>
                        <div class="ml-2">
                            <h3 class="font-medium text-gray-600 text-sm">Confirm</h3>
                        </div>
                    </div>
                    <div class="flex items-center">
                        <div class="step-number w-6 h-6 rounded-full bg-gray-200 text-gray-600 flex items-center justify-center text-xs">3</div>
                        <div class="ml-2">
                            <h3 class="font-medium text-gray-600 text-sm">Payment</h3>
                        </div>
                    </div>
                </div>
                <div class="relative mt-2">
                    <div class="overflow-hidden h-1 text-xs flex rounded bg-gray-200">
                        <div style="width: 33.3%" class="shadow-none flex flex-col text-center whitespace-nowrap text-white justify-center bg-primary"></div>
                    </div>
                </div>
            </div>

            <!-- Transaction Form - Streamlined -->
            <form action="{{ route('transactions.confirm') }}" method="POST" id="transaction-form">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Left Column -->
                    <div>

                        <!-- Mobile Number with Provider Detection -->
                        <div class="mb-4">
                            <label for="wallet_number" class="block text-sm font-medium text-gray-700 mb-1">Mobile Number</label>
                            <div class="flex">
                                <span class="inline-flex items-center px-2 text-gray-500 bg-gray-100 border border-r-0 border-gray-300 rounded-l-md">
                                    +260
                                </span>
                                <input type="text" id="mobileNumber" name="wallet_number" value="{{ old('wallet_number', $selectedBeneficiary->wallet_number ?? '') }}" class="w-full px-3 py-2 border border-gray-300 rounded-r-md focus:ring-1 focus:ring-primary focus:border-primary" placeholder="97XXXXXXX" required>
                            </div>

                            <!-- Hidden input for wallet provider -->
                            <input type="hidden" id="wallet_provider_id" name="wallet_provider_id" value="{{ old('wallet_provider_id', $selectedBeneficiary->wallet_provider_id ?? '') }}">

                            <!-- Network Provider Logos - Inline and Smaller -->
                            <div class="flex items-center space-x-2 mt-1">
                                <p class="text-xs text-gray-500">Provider:</p>
                                <div class="p-mode mtn" title="MTN Mobile Money">
                                    <img src="{{ asset('assets/img/mtn.jpg') }}" alt="MTN" class="h-8 w-8 rounded-full object-cover" style="filter: grayscale(1);">
                                </div>
                                <div class="p-mode airtel" title="Airtel Money">
                                    <img src="{{ asset('assets/img/airtel.png') }}" alt="Airtel" class="h-8 w-8 rounded-full object-cover" style="filter: grayscale(1);">
                                </div>
                                <div class="p-mode zamtel" title="Zamtel Kwacha">
                                    <img src="{{ asset('assets/img/zamtel.jpg') }}" alt="Zamtel" class="h-8 w-8 rounded-full object-cover" style="filter: grayscale(1);">
                                </div>
                            </div>
                            @error('wallet_number')
                                <p class="text-error text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Recipient Name -->
                        <div class="mb-4">
                            <label for="recipient_name" class="block text-sm font-medium text-gray-700 mb-1">Recipient Name</label>
                            <input type="text" id="recipient_name" name="recipient_name" value="{{ old('recipient_name', $selectedBeneficiary->recipient_name ?? '') }}" class="w-full px-3 py-2 border rounded-md focus:ring-1 focus:ring-primary focus:border-primary" placeholder="Enter recipient name">
                            @error('recipient_name')
                                <p class="text-error text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Amount -->
                        <div class="mb-4">
                            <label for="amount" class="block text-sm font-medium text-gray-700 mb-1">Amount (ZMW)</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <span class="text-gray-500">K</span>
                                </div>
                                <input type="number" id="amount" name="amount" value="{{ old('amount') }}" min="10" max="{{ auth()->user()->verification_level === 'verified' ? 5000 : 1000 }}" step="0.01" class="w-full pl-8 pr-4 py-2 border rounded-md focus:ring-1 focus:ring-primary focus:border-primary" placeholder="0.00" required>
                            </div>
                            <p class="text-xs text-gray-500 mt-1">
                                Min: K10.00 | Max: K{{ number_format(auth()->user()->verification_level === 'verified' ? 5000 : 1000, 2) }}
                            </p>
                            @error('amount')
                                <p class="text-error text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Right Column -->
                    <div>

                        <!-- Purpose -->
                        <div class="mb-4">
                            <label for="purpose" class="block text-sm font-medium text-gray-700 mb-1">Purpose (Optional)</label>
                            <select id="purpose" name="purpose" class="w-full px-3 py-2 border rounded-md focus:ring-1 focus:ring-primary focus:border-primary">
                                <option value="">Select Purpose</option>
                                <option value="Family Support" {{ old('purpose') == 'Family Support' ? 'selected' : '' }}>Family Support</option>
                                <option value="Business" {{ old('purpose') == 'Business' ? 'selected' : '' }}>Business</option>
                                <option value="Education" {{ old('purpose') == 'Education' ? 'selected' : '' }}>Education</option>
                                <option value="Medical" {{ old('purpose') == 'Medical' ? 'selected' : '' }}>Medical</option>
                                <option value="Utilities" {{ old('purpose') == 'Utilities' ? 'selected' : '' }}>Utilities</option>
                                <option value="Rent" {{ old('purpose') == 'Rent' ? 'selected' : '' }}>Rent</option>
                                <option value="Other" {{ old('purpose') == 'Other' ? 'selected' : '' }}>Other</option>
                            </select>
                            @error('purpose')
                                <p class="text-error text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Notes -->
                        <div class="mb-4">
                            <label for="notes" class="block text-sm font-medium text-gray-700 mb-1">Notes (Optional)</label>
                            <input type="text" id="notes" name="notes" value="{{ old('notes') }}" class="w-full px-3 py-2 border rounded-md focus:ring-1 focus:ring-primary focus:border-primary" placeholder="Add any notes for this transaction">
                            @error('notes')
                                <p class="text-error text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Fee Calculation - Compact -->
                        <div class="p-3 bg-gray-50 rounded-md text-sm">
                            <div class="flex justify-between items-center mb-1">
                                <span class="text-gray-700">Amount:</span>
                                <span class="font-medium" id="display-amount">K0.00</span>
                            </div>
                            <div class="flex justify-between items-center mb-1">
                                <span class="text-gray-700">Fee (4%):</span>
                                <span class="font-medium" id="display-fee">K0.00</span>
                            </div>
                            <div class="border-t border-gray-200 my-1 pt-1 flex justify-between items-center">
                                <span class="text-gray-700 font-medium">Total:</span>
                                <span class="font-bold text-dark" id="display-total">K0.00</span>
                            </div>
                        </div>

                        <!-- Save Beneficiary -->
                        <div class="mt-2">
                            <label class="inline-flex items-center">
                                <input type="checkbox" name="save_beneficiary" class="h-4 w-4 text-primary focus:ring-primary border-gray-300 rounded">
                                <span class="ml-2 text-sm text-gray-700">Save this recipient for future transactions</span>
                            </label>
                        </div>
                    </div>
                </div>

            <!-- Submit Button -->
            <div class="mt-4">
                <button type="button" id="showConfirmModal" class="w-full bg-primary text-white py-2 px-4 rounded-md hover:bg-opacity-90 transition duration-300 font-medium">
                    <i class="fas fa-arrow-right mr-2"></i> Continue to Confirm
                </button>
            </div>
        </form>

        <!-- Confirmation Modal -->
        <div id="confirmationModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center hidden">
            <div class="bg-white rounded-xl shadow-lg max-w-lg w-full mx-4 overflow-hidden">
                <div class="p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-bold text-dark">Confirm Transaction</h3>
                        <button type="button" id="closeConfirmModal" class="text-gray-400 hover:text-gray-600">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>

                    <div class="bg-gray-50 rounded-lg p-4 mb-4">
                        <div class="flex items-center mb-3">
                            <div id="modal-provider-icon" class="w-10 h-10 rounded-full mr-3 flex items-center justify-center">
                                <!-- Provider icon will be inserted here -->
                            </div>
                            <div>
                                <h4 class="font-medium text-dark" id="modal-recipient-name">Recipient Name</h4>
                                <p class="text-sm text-gray-600" id="modal-wallet-number">+260XXXXXXXXX</p>
                            </div>
                        </div>

                        <div class="border-t border-gray-200 pt-3 mt-3">
                            <div class="flex justify-between items-center mb-1">
                                <span class="text-gray-600">Amount:</span>
                                <span class="font-medium" id="modal-amount">K0.00</span>
                            </div>
                            <div class="flex justify-between items-center mb-1">
                                <span class="text-gray-600">Fee (4%):</span>
                                <span class="font-medium" id="modal-fee">K0.00</span>
                            </div>
                            <div class="border-t border-gray-200 my-1 pt-1 flex justify-between items-center">
                                <span class="text-gray-700 font-medium">Total:</span>
                                <span class="font-bold text-dark" id="modal-total">K0.00</span>
                            </div>
                        </div>
                    </div>

                    <div class="bg-primary bg-opacity-5 rounded-lg p-3 mb-4 border border-primary border-opacity-20">
                        <div class="flex items-start">
                            <div class="flex-shrink-0 mt-0.5 text-primary">
                                <i class="fas fa-info-circle"></i>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm text-gray-600">You are about to send money to this mobile wallet. Please verify all details before proceeding.</p>
                            </div>
                        </div>
                    </div>

                    <div class="flex space-x-3">
                        <button type="button" id="cancelConfirmModal" class="flex-1 px-4 py-2 border border-gray-300 rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none">
                            <i class="fas fa-arrow-left mr-2"></i> Edit Details
                        </button>
                        <button type="button" id="proceedToPayment" class="flex-1 px-4 py-2 bg-primary text-white rounded-md hover:bg-opacity-90 focus:outline-none">
                            <i class="fas fa-credit-card mr-2"></i> Proceed to Payment
                        </button>
                    </div>
                </div>
            </div>
        </div>

            <!-- Saved Beneficiaries - Collapsible -->
            <div class="mt-4">
                <div class="flex items-center justify-between cursor-pointer" id="beneficiaries-toggle">
                    <h3 class="text-sm font-medium text-dark">Select from Saved Beneficiaries</h3>
                    <i class="fas fa-chevron-down text-gray-500 text-xs transition-transform" id="beneficiaries-chevron"></i>
                </div>

                <div id="beneficiaries-content" class="mt-2">
                    @if(count($beneficiaries ?? []) > 0)
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-2">
                            @foreach($beneficiaries as $beneficiary)
                                <div class="flex items-center p-2 bg-gray-50 rounded hover:bg-gray-100 transition cursor-pointer beneficiary-card"
                                     data-provider="{{ $beneficiary->wallet_provider_id }}"
                                     data-number="{{ $beneficiary->wallet_number }}"
                                     data-name="{{ $beneficiary->recipient_name }}">
                                    <div class="flex-shrink-0 h-8 w-8">
                                        @if($beneficiary->wallet_provider)
                                            @if($beneficiary->wallet_provider->api_code === 'airtel')
                                                <img class="h-8 w-8 rounded-full" src="{{ asset('assets/img/airtel.png') }}" alt="Airtel">
                                            @elseif($beneficiary->wallet_provider->api_code === 'mtn')
                                                <img class="h-8 w-8 rounded-full" src="{{ asset('assets/img/mtn.jpg') }}" alt="MTN">
                                            @elseif($beneficiary->wallet_provider->api_code === 'zamtel')
                                                <img class="h-8 w-8 rounded-full" src="{{ asset('assets/img/zamtel.jpg') }}" alt="Zamtel">
                                            @else
                                                <div class="h-8 w-8 rounded-full bg-primary text-white flex items-center justify-center text-xs">
                                                    {{ strtoupper(substr($beneficiary->recipient_name, 0, 1)) }}
                                                </div>
                                            @endif
                                        @else
                                            <div class="h-8 w-8 rounded-full bg-primary text-white flex items-center justify-center text-xs">
                                                {{ strtoupper(substr($beneficiary->recipient_name, 0, 1)) }}
                                            </div>
                                        @endif
                                    </div>
                                    <div class="ml-2 overflow-hidden">
                                        <div class="text-xs font-medium text-dark truncate">
                                            {{ $beneficiary->recipient_name }}
                                            @if($beneficiary->is_favorite)
                                                <i class="fas fa-star text-secondary ml-1 text-xs"></i>
                                            @endif
                                        </div>
                                        <div class="text-xs text-gray-500 truncate">
                                            +260{{ $beneficiary->wallet_number }}
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        @if(count($beneficiaries) > 6)
                            <div class="mt-2 text-center">
                                <a href="{{ route('beneficiaries.index') }}" class="text-primary hover:underline text-xs">
                                    View all ({{ count($allBeneficiaries ?? []) }})
                                </a>
                            </div>
                        @endif
                    @else
                        <div class="text-center py-4 bg-gray-50 rounded">
                            <div class="inline-flex items-center justify-center w-10 h-10 rounded-full bg-gray-100 text-gray-400 mb-2">
                                <i class="fas fa-users"></i>
                            </div>
                            <h3 class="text-sm font-medium text-gray-900 mb-1">No saved beneficiaries</h3>
                            <p class="text-xs text-gray-500 mb-2">Save recipients for quick access</p>
                            <a href="{{ route('beneficiaries.index') }}" class="inline-flex items-center px-3 py-1 border border-transparent rounded text-xs font-medium text-white bg-primary hover:bg-opacity-90">
                                <i class="fas fa-plus mr-1"></i> Add Beneficiary
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Amount input and fee calculation
        const amountInput = document.getElementById('amount');
        const displayAmount = document.getElementById('display-amount');
        const displayFee = document.getElementById('display-fee');
        const displayTotal = document.getElementById('display-total');

        // Modal elements
        const showConfirmModal = document.getElementById('showConfirmModal');
        const confirmationModal = document.getElementById('confirmationModal');
        const closeConfirmModal = document.getElementById('closeConfirmModal');
        const cancelConfirmModal = document.getElementById('cancelConfirmModal');
        const proceedToPayment = document.getElementById('proceedToPayment');
        const modalProviderIcon = document.getElementById('modal-provider-icon');
        const modalRecipientName = document.getElementById('modal-recipient-name');
        const modalWalletNumber = document.getElementById('modal-wallet-number');
        const modalAmount = document.getElementById('modal-amount');
        const modalFee = document.getElementById('modal-fee');
        const modalTotal = document.getElementById('modal-total');
        const transactionForm = document.getElementById('transaction-form');

        // Format currency
        function formatCurrency(amount) {
            return 'K' + parseFloat(amount).toFixed(2);
        }

        // Calculate fee and total
        function calculateFee() {
            const amount = parseFloat(amountInput.value) || 0;
            const fee = amount * 0.04; // 4% fee
            const total = amount + fee;

            displayAmount.textContent = formatCurrency(amount);
            displayFee.textContent = formatCurrency(fee);
            displayTotal.textContent = formatCurrency(total);

            return { amount, fee, total };
        }

        if (amountInput) {
            amountInput.addEventListener('input', calculateFee);
            calculateFee(); // Initialize
        }

        // Confirmation modal handling
        if (showConfirmModal) {
            showConfirmModal.addEventListener('click', function() {
                // Validate form
                const mobileNumber = document.getElementById('mobileNumber').value;
                const walletProviderId = document.getElementById('wallet_provider_id').value;
                const recipientName = document.getElementById('recipient_name').value || 'Unknown Recipient';
                const amount = document.getElementById('amount').value;

                // Basic validation
                if (!mobileNumber || mobileNumber.length < 9) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Invalid Input',
                        text: 'Please enter a valid mobile number',
                        confirmButtonColor: '#3366CC'
                    });
                    return;
                }

                if (!walletProviderId) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Invalid Input',
                        text: 'Please select a valid mobile provider',
                        confirmButtonColor: '#3366CC'
                    });
                    return;
                }

                if (!amount || parseFloat(amount) < 10) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Invalid Input',
                        text: 'Please enter a valid amount (minimum K10)',
                        confirmButtonColor: '#3366CC'
                    });
                    return;
                }

                // Calculate fees
                const { amount: amountValue, fee, total } = calculateFee();

                // Update modal content
                modalRecipientName.textContent = recipientName;
                modalWalletNumber.textContent = '+260' + mobileNumber;
                modalAmount.textContent = formatCurrency(amountValue);
                modalFee.textContent = formatCurrency(fee);
                modalTotal.textContent = formatCurrency(total);

                // Set provider icon
                modalProviderIcon.innerHTML = '';
                if (mobileNumber.startsWith('96') || mobileNumber.startsWith('76')) {
                    modalProviderIcon.innerHTML = `<img src="${window.location.origin}/assets/img/mtn.jpg" alt="MTN" class="h-10 w-10 rounded-full object-cover">`;
                } else if (mobileNumber.startsWith('95') || mobileNumber.startsWith('75')) {
                    modalProviderIcon.innerHTML = `<img src="${window.location.origin}/assets/img/zamtel.jpg" alt="Zamtel" class="h-10 w-10 rounded-full object-cover">`;
                } else if (mobileNumber.startsWith('97') || mobileNumber.startsWith('77')) {
                    modalProviderIcon.innerHTML = `<img src="${window.location.origin}/assets/img/airtel.png" alt="Airtel" class="h-10 w-10 rounded-full object-cover">`;
                } else {
                    modalProviderIcon.innerHTML = `<div class="h-10 w-10 rounded-full bg-primary text-white flex items-center justify-center text-lg">
                        <i class="fas fa-mobile-alt"></i>
                    </div>`;
                }

                // Show modal
                confirmationModal.classList.remove('hidden');
            });
        }

        // Close modal
        if (closeConfirmModal) {
            closeConfirmModal.addEventListener('click', function() {
                confirmationModal.classList.add('hidden');
            });
        }

        // Cancel button
        if (cancelConfirmModal) {
            cancelConfirmModal.addEventListener('click', function() {
                confirmationModal.classList.add('hidden');
            });
        }

        // Proceed to payment
        if (proceedToPayment) {
            proceedToPayment.addEventListener('click', function() {
                // Show loading state
                this.disabled = true;
                this.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Processing...';

                // Get form data
                const formData = new FormData(transactionForm);

                // Convert FormData to JSON
                const jsonData = {};
                formData.forEach((value, key) => {
                    jsonData[key] = value;
                });

                // Submit form via AJAX
                fetch('{{ route("transactions.process-ajax") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify(jsonData)
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'SUCCESS') {
                        // Configure MPGS Checkout
                        Checkout.configure({
                            session: {
                                id: data.session
                            }
                        });

                        // Open MPGS Checkout
                        Checkout.showPaymentPage();
                    } else {
                        // Show error
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: data.statusMessage,
                            confirmButtonColor: '#3366CC'
                        });
                        this.disabled = false;
                        this.innerHTML = '<i class="fas fa-credit-card mr-2"></i> Proceed to Payment';
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'An error occurred while processing your payment. Please try again.',
                        confirmButtonColor: '#3366CC'
                    });
                    this.disabled = false;
                    this.innerHTML = '<i class="fas fa-credit-card mr-2"></i> Proceed to Payment';
                });
            });
        }

        // Mobile number validation and provider detection
        $('#mobileNumber').on('input change', function () {
            // Remove non-numeric characters
            this.value = this.value.replace(/\D/g, '');

            // Limit to 9 digits
            if (this.value.length > 9) {
                this.value = this.value.slice(0, 9);
            }

            // Reset all logos to grey
            $('.p-mode img').css('filter', 'grayscale(1)');

            // Set the wallet provider ID based on the number prefix
            const input = this.value;

            if (input.startsWith('96') || input.startsWith('76')) {
                $('.p-mode.mtn img').css('filter', 'grayscale(0)');
                $('#wallet_provider_id').val('2'); // MTN ID
            }
            if (input.startsWith('95') || input.startsWith('75')) {
                $('.p-mode.zamtel img').css('filter', 'grayscale(0)');
                $('#wallet_provider_id').val('3'); // Zamtel ID
            }
            if (input.startsWith('97') || input.startsWith('77')) {
                $('.p-mode.airtel img').css('filter', 'grayscale(0)');
                $('#wallet_provider_id').val('1'); // Airtel ID
            }
        });

        // Trigger the input event to initialize provider detection for pre-filled values
        $('#mobileNumber').trigger('input');

        // Beneficiary selection
        const beneficiaryCards = document.querySelectorAll('.beneficiary-card');
        const walletProviderInput = document.getElementById('wallet_provider_id');
        const mobileNumberInput = document.getElementById('mobileNumber');
        const recipientNameInput = document.getElementById('recipient_name');

        beneficiaryCards.forEach(card => {
            card.addEventListener('click', function() {
                const providerId = this.dataset.provider;
                const number = this.dataset.number;
                const name = this.dataset.name;

                walletProviderInput.value = providerId;
                mobileNumberInput.value = number;
                recipientNameInput.value = name;

                // Trigger input event to update provider logos
                $(mobileNumberInput).trigger('input');

                // Add active class to selected card
                beneficiaryCards.forEach(c => {
                    c.classList.remove('bg-primary', 'bg-opacity-10');
                    c.classList.add('bg-gray-50');
                });

                this.classList.remove('bg-gray-50');
                this.classList.add('bg-primary', 'bg-opacity-10');
            });
        });

        // Toggle beneficiaries section
        const beneficiariesToggle = document.getElementById('beneficiaries-toggle');
        const beneficiariesContent = document.getElementById('beneficiaries-content');
        const beneficiariesChevron = document.getElementById('beneficiaries-chevron');

        // Ensure beneficiaries section is hidden by default
        // beneficiariesContent.classList.add('hidden');

        beneficiariesToggle.addEventListener('click', function() {
            beneficiariesContent.classList.toggle('hidden');
            beneficiariesChevron.classList.toggle('transform');
            beneficiariesChevron.classList.toggle('rotate-180');
        });
    });
</script>
@endpush
