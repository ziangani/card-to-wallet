@extends('layouts.app')

@section('title', 'New Transaction - ' . config('app.name'))
@section('meta_description', 'Send money to a mobile wallet')
@section('header_title', 'New Transaction')

@push('pre-styles')
<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<!-- MPGS Checkout -->
<script src="{{$mpgs_endpoint}}/static/checkout/checkout.min.js" data-error="errorCallback" data-cancel="cancelCallback"></script>
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
    /* Card styles */
    .transaction-card {
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        transition: all 0.2s ease;
    }

    /* Form section styles */
    .form-section {
        background-color: #F9FAFB;
        border-radius: 8px;
        padding: 1.25rem;
        margin-bottom: 1.5rem;
    }

    .form-section-title {
        font-size: 1rem;
        font-weight: 600;
        color: #111827;
        margin-bottom: 1rem;
        display: flex;
        align-items: center;
    }

    .form-section-title i {
        margin-right: 0.5rem;
        color: #3366CC;
    }

    /* Beneficiary cards */
    .beneficiary-card {
        transition: all 0.2s ease;
    }

    .beneficiary-card:hover {
        transform: translateY(-2px);
    }

    .beneficiary-card.selected {
        border-color: #3366CC;
        background-color: rgba(51, 102, 204, 0.1);
    }

    /* Provider selection */
    .provider-option {
        position: relative;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .provider-option:hover img {
        transform: scale(1.05);
    }

    .provider-option.active img {
        border-color: #3366CC;
        filter: grayscale(0) !important;
    }

    .provider-option:not(.active) img {
        filter: grayscale(1);
        opacity: 0.7;
    }

    /* Form controls */
    input:focus, select:focus, textarea:focus {
        box-shadow: 0 0 0 2px rgba(51, 102, 204, 0.2);
    }

    /* Responsive adjustments */
    @media (max-width: 640px) {
        .beneficiary-grid {
            grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
        }
    }
</style>
@endpush

@section('content')
    <div class="container mx-auto px-4 py-6">
        <!-- Page Header -->
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 mb-1">Send Money to Mobile Wallet</h1>
                <p class="text-gray-600">Transfer funds directly to any mobile wallet in Zambia</p>
            </div>
            <div class="flex space-x-2 mt-2 md:mt-0">
                <a href="{{ route('dashboard') }}" class="inline-flex items-center px-3 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                    <i class="fas fa-arrow-left mr-2"></i> Dashboard
                </a>
                <a href="{{ route('transactions.history') }}" class="inline-flex items-center px-3 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                    <i class="fas fa-history mr-2"></i> History
                </a>
            </div>
        </div>

        <!-- Account Info Card -->
        <div class="bg-white rounded-xl shadow-sm p-4 mb-6 flex flex-col md:flex-row justify-between items-start md:items-center">
            <div class="flex items-center mb-3 md:mb-0">
                <div class="w-10 h-10 rounded-full bg-primary bg-opacity-10 flex items-center justify-center mr-3">
                    <i class="fas fa-wallet text-primary"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Available Balance</p>
                    <p class="text-xl font-bold text-gray-900">K{{ number_format(auth()->user()->balance ?? 0, 2) }}</p>
                </div>
            </div>
            <div class="flex items-center">
                <div class="w-10 h-10 rounded-full bg-primary bg-opacity-10 flex items-center justify-center mr-3">
                    <i class="fas fa-exchange-alt text-primary"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Transaction Limit</p>
                    <p class="text-xl font-bold text-gray-900">K{{ number_format(auth()->user()->verification_level === 'verified' ? 5000 : 1000, 2) }}</p>
                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium {{ auth()->user()->verification_level === 'verified' ? 'bg-green-100 text-green-800' : 'bg-blue-100 text-blue-800' }}">
                        {{ auth()->user()->verification_level === 'verified' ? 'Verified Account' : 'Basic Account' }}
                    </span>
                </div>
            </div>
        </div>
<!-- Main Transaction Card -->
<div class="bg-white rounded-xl shadow-sm overflow-hidden">

            <!-- Transaction Form -->
            <form action="{{ route('transactions.confirm') }}" method="POST" id="transaction-form" class="p-6">
                @csrf

                <!-- Saved Beneficiaries Section -->
                <div class="bg-gray-50 rounded-lg p-4 mb-6">
                    <div class="flex items-center justify-between mb-3">
                        <h3 class="text-base font-medium text-gray-900">Select a Saved Recipient</h3>
                    </div>

                    @if(count($beneficiaries ?? []) > 0)
                        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-3">
                            @foreach($beneficiaries as $beneficiary)
                                <div class="bg-white border border-gray-200 rounded-lg p-3 text-center hover:border-primary hover:bg-blue-50 transition cursor-pointer beneficiary-card"
                                     data-provider="{{ $beneficiary->wallet_provider_id }}"
                                     data-number="{{ $beneficiary->wallet_number }}"
                                     data-name="{{ $beneficiary->recipient_name }}">
                                    <div class="flex flex-col items-center">
                                        <div class="w-12 h-12 mb-2">
                                            @if($beneficiary->wallet_provider)
                                                @if($beneficiary->wallet_provider->api_code === 'airtel')
                                                    <img class="w-full h-full rounded-full object-cover" src="{{ asset('assets/img/airtel.png') }}" alt="Airtel">
                                                @elseif($beneficiary->wallet_provider->api_code === 'mtn')
                                                    <img class="w-full h-full rounded-full object-cover" src="{{ asset('assets/img/mtn.jpg') }}" alt="MTN">
                                                @elseif($beneficiary->wallet_provider->api_code === 'zamtel')
                                                    <img class="w-full h-full rounded-full object-cover" src="{{ asset('assets/img/zamtel.jpg') }}" alt="Zamtel">
                                                @else
                                                    <div class="w-full h-full rounded-full bg-primary text-white flex items-center justify-center text-lg">
                                                        {{ strtoupper(substr($beneficiary->recipient_name, 0, 1)) }}
                                                    </div>
                                                @endif
                                            @else
                                                <div class="w-full h-full rounded-full bg-primary text-white flex items-center justify-center text-lg">
                                                    {{ strtoupper(substr($beneficiary->recipient_name, 0, 1)) }}
                                                </div>
                                            @endif
                                        </div>
                                        <p class="text-sm font-medium text-gray-900 truncate max-w-full">{{ $beneficiary->recipient_name }}</p>
                                        <p class="text-xs text-gray-500">+260{{ $beneficiary->wallet_number }}</p>
                                    </div>
                                </div>
                            @endforeach

                            <!-- Add New Recipient Card -->
                            <div class="bg-white border border-dashed border-gray-300 rounded-lg p-3 text-center hover:border-primary hover:bg-blue-50 transition cursor-pointer new-beneficiary">
                                <div class="flex flex-col items-center">
                                    <div class="w-12 h-12 mb-2 rounded-full bg-gray-100 flex items-center justify-center">
                                        <i class="fas fa-plus text-gray-400"></i>
                                    </div>
                                    <p class="text-sm font-medium text-gray-900">New Recipient</p>
                                    <p class="text-xs text-gray-500">Enter details below</p>
                                </div>
                            </div>
                        </div>

                        @if(count($beneficiaries) > 8)
                            <div class="mt-3 text-center">
                                <a href="{{ route('beneficiaries.index') }}" class="text-primary hover:underline text-sm">
                                    View all beneficiaries ({{ count($allBeneficiaries ?? []) }})
                                </a>
                            </div>
                        @endif
                    @else
                        <div class="text-center py-6 bg-white rounded-lg border border-gray-200">
                            <div class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-gray-100 text-gray-400 mb-3">
                                <i class="fas fa-users"></i>
                            </div>
                            <h3 class="text-base font-medium text-gray-900 mb-1">No saved recipients</h3>
                            <p class="text-sm text-gray-500 mb-3">Save recipients for quick access in future transactions</p>
                            <a href="{{ route('beneficiaries.index') }}" class="inline-flex items-center px-3 py-2 border border-transparent rounded-md text-sm font-medium text-white bg-primary hover:bg-opacity-90">
                                <i class="fas fa-plus mr-1"></i> Add Recipient
                            </a>
                        </div>
                    @endif
                </div>

                <!-- Recipient Details Section -->
                <div class="bg-white border border-gray-200 rounded-lg p-6 mb-6 shadow-sm">
                    <div class="flex items-center mb-5">
                        <div class="w-10 h-10 rounded-full bg-primary bg-opacity-10 flex items-center justify-center mr-3">
                            <i class="fas fa-user text-primary"></i>
                        </div>
                        <h3 class="text-lg font-medium text-gray-900">Recipient Details</h3>
                    </div>

                    <!-- Maximized Space Layout -->
                    <div class="space-y-3">
                        <!-- Mobile Number and Provider Row -->
                        <div>
                            <label for="wallet_number" class="block text-sm font-medium text-gray-700 mb-1">Mobile Number <span class="text-red-500">*</span></label>
                            <div class="flex items-center space-x-3">
                                <div class="flex flex-grow">
                                    <span class="inline-flex items-center px-3 text-gray-500 bg-gray-100 border border-r-0 border-gray-300 rounded-l-md">
                                        +260
                                    </span>
                                    <input type="text" id="mobileNumber" name="wallet_number" value="{{ old('wallet_number', $selectedBeneficiary->wallet_number ?? '') }}" class="w-full px-3 py-2 border border-gray-300 rounded-r-md focus:ring-2 focus:ring-primary focus:border-primary" placeholder="97XXXXXXX" required>
                                </div>
                                
                                <!-- Provider Selection -->
                                <div class="flex items-center space-x-2">
                                    <div class="provider-option mtn cursor-pointer bg-white border border-gray-200 rounded-lg p-1 text-center hover:border-primary hover:bg-blue-50 transition" title="MTN Mobile Money" data-provider="2">
                                        <img src="{{ asset('assets/img/mtn.jpg') }}" alt="MTN" class="h-8 w-8 rounded-full object-cover border-2 border-transparent" style="filter: grayscale(1);">
                                    </div>
                                    <div class="provider-option airtel cursor-pointer bg-white border border-gray-200 rounded-lg p-1 text-center hover:border-primary hover:bg-blue-50 transition" title="Airtel Money" data-provider="1">
                                        <img src="{{ asset('assets/img/airtel.png') }}" alt="Airtel" class="h-8 w-8 rounded-full object-cover border-2 border-transparent" style="filter: grayscale(1);">
                                    </div>
                                    <div class="provider-option zamtel cursor-pointer bg-white border border-gray-200 rounded-lg p-1 text-center hover:border-primary hover:bg-blue-50 transition" title="Zamtel Kwacha" data-provider="3">
                                        <img src="{{ asset('assets/img/zamtel.jpg') }}" alt="Zamtel" class="h-8 w-8 rounded-full object-cover border-2 border-transparent" style="filter: grayscale(1);">
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Hidden input for wallet provider -->
                            <input type="hidden" id="wallet_provider_id" name="wallet_provider_id" value="{{ old('wallet_provider_id', $selectedBeneficiary->wallet_provider_id ?? '') }}">
                            @error('wallet_number')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Recipient Name and Amount Row - Side by Side -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                            <!-- Recipient Name -->
                            <div>
                                <label for="recipient_name" class="block text-sm font-medium text-gray-700 mb-1">Recipient Name</label>
                                <input type="text" id="recipient_name" name="recipient_name" value="{{ old('recipient_name', $selectedBeneficiary->recipient_name ?? '') }}" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-primary focus:border-primary" placeholder="Enter recipient name">
                                @error('recipient_name')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Amount -->
                            <div class="mb-3">
                                <label for="amount" class="block text-sm font-medium text-gray-700 mb-1">Amount (ZMW) <span class="text-red-500">*</span></label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <span class="text-gray-500">K</span>
                                    </div>
                                    <input type="number" id="amount" name="amount" value="{{ old('amount') }}" min="10" max="{{ auth()->user()->verification_level === 'verified' ? 5000 : 1000 }}" step="0.01" class="w-full pl-8 pr-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-primary focus:border-primary" placeholder="0.00" required>
                                </div>
                                <div class="flex items-center mt-1">
                                    <i class="fas fa-info-circle text-gray-400 mr-1"></i>
                                    <p class="text-xs text-gray-500">
                                        Min: K10.00 | Max: K{{ number_format(auth()->user()->verification_level === 'verified' ? 5000 : 1000, 2) }}
                                    </p>
                                </div>
                                @error('amount')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Right Column -->
                   <div class="flex space-x-4">
    <!-- Purpose -->
    <div class="mb-5 flex-1">
        <label for="purpose" class="block text-sm font-medium text-gray-700 mb-2">Purpose (Optional)</label>
        <div class="relative">
            <select id="purpose" name="purpose" class="w-full px-3 py-3 border border-gray-300 rounded-md focus:ring-2 focus:ring-primary focus:border-primary appearance-none">
                <option value="">Select Purpose</option>
                <option value="Family Support" {{ old('purpose') == 'Family Support' ? 'selected' : '' }}>Family Support</option>
                <option value="Business" {{ old('purpose') == 'Business' ? 'selected' : '' }}>Business</option>
                <option value="Education" {{ old('purpose') == 'Education' ? 'selected' : '' }}>Education</option>
                <option value="Medical" {{ old('purpose') == 'Medical' ? 'selected' : '' }}>Medical</option>
                <option value="Utilities" {{ old('purpose') == 'Utilities' ? 'selected' : '' }}>Utilities</option>
                <option value="Rent" {{ old('purpose') == 'Rent' ? 'selected' : '' }}>Rent</option>
                <option value="Other" {{ old('purpose') == 'Other' ? 'selected' : '' }}>Other</option>
            </select>
            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-700">
                <i class="fas fa-chevron-down text-gray-400"></i>
            </div>
        </div>
        @error('purpose')
            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
        @enderror
    </div>

    <!-- Notes -->
    <div class="mb-5 flex-1">
        <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">Notes (Optional)</label>
        <input id="notes" name="notes" rows="3" class="w-full px-3 py-3 border border-gray-300 rounded-md focus:ring-2 focus:ring-primary focus:border-primary" placeholder="Add any notes for this transaction">{{ old('notes') }}</input>
        @error('notes')
            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
        @enderror
    </div>
</div>
                </div>

                <!-- Save Beneficiary -->
                <div class="mt-6 mb-6">
                    <div class="bg-blue-50 border border-blue-100 rounded-lg p-4">
                        <label class="flex items-center">
                            <input type="checkbox" name="save_beneficiary" class="h-5 w-5 text-primary focus:ring-primary border-gray-300 rounded">
                            <span class="ml-2 text-sm text-gray-700 font-medium">Save this recipient for future transactions</span>
                        </label>
                    </div>
                </div>

                <!-- Transaction Summary -->
                <div class="bg-white border border-gray-200 rounded-lg p-6 mb-6 shadow-sm">
                    <div class="flex items-center mb-5">
                        <div class="w-10 h-10 rounded-full bg-primary bg-opacity-10 flex items-center justify-center mr-3">
                            <i class="fas fa-receipt text-primary"></i>
                        </div>
                        <h3 class="text-lg font-medium text-gray-900">Transaction Summary</h3>
                    </div>

                    <div class="bg-gray-50 rounded-lg p-5">
                        <div class="flex justify-between items-center mb-3">
                            <span class="text-gray-700">Amount:</span>
                            <span class="font-medium text-gray-900" id="display-amount">K0.00</span>
                        </div>
                        <div class="flex justify-between items-center mb-3">
                            <span class="text-gray-700">Fee (K7.5 + 4%):</span>
                            <span class="font-medium text-gray-900" id="display-fee">K0.00</span>
                        </div>
                        <div class="border-t border-gray-200 my-3 pt-3 flex justify-between items-center">
                            <span class="text-gray-900 font-medium">Total:</span>
                            <span class="font-bold text-primary text-xl" id="display-total">K0.00</span>
                        </div>
                    </div>
                </div>

                <!-- Action Button -->
                <div class="mt-6">
                    <button type="button" id="showConfirmModal" class="w-full bg-primary text-white py-3 px-4 rounded-lg hover:bg-opacity-90 transition duration-300 font-medium text-base flex items-center justify-center">
                        <i class="fas fa-check-circle mr-2"></i> Review and Confirm
                    </button>
                </div>
        </form>

        <!-- Confirmation Modal -->
        <div id="confirmationModal" class="fixed inset-0 bg-black bg-opacity-60 z-50 flex items-center justify-center hidden">
            <div class="bg-white rounded-xl shadow-xl max-w-lg w-full mx-4 overflow-hidden">
                <div class="bg-primary bg-opacity-10 p-4 border-b border-primary border-opacity-20">
                    <div class="flex justify-between items-center">
                        <h3 class="text-lg font-bold text-gray-900 flex items-center">
                            <i class="fas fa-check-circle text-primary mr-2"></i>
                            Confirm Transaction
                        </h3>
                        <button type="button" id="closeConfirmModal" class="text-gray-500 hover:text-gray-700 focus:outline-none">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>

                <div class="p-6">
                    <!-- Recipient Info -->
                    <div class="flex items-center p-4 bg-gray-50 rounded-lg mb-5">
                        <div id="modal-provider-icon" class="w-14 h-14 rounded-full mr-4 flex items-center justify-center">
                            <!-- Provider icon will be inserted here -->
                        </div>
                        <div>
                            <h4 class="font-medium text-gray-900 text-lg" id="modal-recipient-name">Recipient Name</h4>
                            <p class="text-gray-600" id="modal-wallet-number">+260XXXXXXXXX</p>
                        </div>
                    </div>

                    <!-- Transaction Details -->
                    <div class="bg-white border border-gray-200 rounded-lg p-4 mb-5">
                        <div class="flex justify-between items-center mb-3">
                            <span class="text-gray-600">Amount:</span>
                            <span class="font-medium text-gray-900" id="modal-amount">K0.00</span>
                        </div>
                        <div class="flex justify-between items-center mb-3">
                            <span class="text-gray-600">Fee (K7.5 + 4%):</span>
                            <span class="font-medium text-gray-900" id="modal-fee">K0.00</span>
                        </div>
                        <div class="border-t border-gray-200 my-2 pt-3 flex justify-between items-center">
                            <span class="text-gray-900 font-medium">Total:</span>
                            <span class="font-bold text-primary text-xl" id="modal-total">K0.00</span>
                        </div>
                    </div>

                    <!-- Info Alert -->
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-5">
                        <div class="flex">
                            <div class="flex-shrink-0 text-blue-500">
                                <i class="fas fa-info-circle"></i>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm text-blue-700">
                                    You are about to send money to this mobile wallet. Please verify all details before proceeding to payment.
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="flex flex-col sm:flex-row space-y-3 sm:space-y-0 sm:space-x-3">
                        <button type="button" id="cancelConfirmModal" class="sm:flex-1 px-4 py-3 border border-gray-300 rounded-lg text-gray-700 bg-white hover:bg-gray-50 focus:outline-none font-medium">
                            <i class="fas fa-arrow-left mr-2"></i> Edit Details
                        </button>
                        <button type="button" id="proceedToPayment" class="sm:flex-1 px-4 py-3 bg-primary text-white rounded-lg hover:bg-opacity-90 focus:outline-none font-medium">
                            <i class="fas fa-credit-card mr-2"></i> Proceed to Payment
                        </button>
                    </div>
                </div>
            </div>
        </div>

            <!-- No duplicate beneficiaries section needed as it's already at the top -->
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
            const variableFee = amount * 0.04; // 4% fee
            const fixedFee = 7.5; // K7.5 fixed fee
            const totalFee = variableFee + fixedFee;
            const total = amount + totalFee;

            displayAmount.textContent = formatCurrency(amount);
            displayFee.textContent = formatCurrency(totalFee);
            displayTotal.textContent = formatCurrency(total);

            return { amount, variableFee, fixedFee, totalFee, total };
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
                const { amount: amountValue, totalFee, total } = calculateFee();

                // Update modal content
                modalRecipientName.textContent = recipientName;
                modalWalletNumber.textContent = '+260' + mobileNumber;
                modalAmount.textContent = formatCurrency(amountValue);
                modalFee.textContent = formatCurrency(totalFee);
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

            // Reset all provider options
            $('.provider-option').removeClass('active');
            $('.provider-option').removeClass('border-primary').addClass('border-gray-200');
            $('.provider-option').removeClass('bg-blue-50');
            $('.provider-option img').css('filter', 'grayscale(1)').css('border-color', 'transparent');

            // Set the wallet provider ID based on the number prefix
            const input = this.value;

            if (input.startsWith('96') || input.startsWith('76')) {
                $('.provider-option.mtn').addClass('active border-primary bg-blue-50').removeClass('border-gray-200');
                $('.provider-option.mtn img').css('filter', 'grayscale(0)').css('border-color', '#3366CC');
                $('#wallet_provider_id').val('2'); // MTN ID
            } else if (input.startsWith('95') || input.startsWith('75')) {
                $('.provider-option.zamtel').addClass('active border-primary bg-blue-50').removeClass('border-gray-200');
                $('.provider-option.zamtel img').css('filter', 'grayscale(0)').css('border-color', '#3366CC');
                $('#wallet_provider_id').val('3'); // Zamtel ID
            } else if (input.startsWith('97') || input.startsWith('77')) {
                $('.provider-option.airtel').addClass('active border-primary bg-blue-50').removeClass('border-gray-200');
                $('.provider-option.airtel img').css('filter', 'grayscale(0)').css('border-color', '#3366CC');
                $('#wallet_provider_id').val('1'); // Airtel ID
            }
        });

        // Provider option click handling
        $('.provider-option').on('click', function() {
            // Reset all provider options
            $('.provider-option').removeClass('active');
            $('.provider-option').removeClass('border-primary').addClass('border-gray-200');
            $('.provider-option').removeClass('bg-blue-50');
            $('.provider-option img').css('filter', 'grayscale(1)').css('border-color', 'transparent');

            // Activate selected provider
            $(this).addClass('active border-primary bg-blue-50').removeClass('border-gray-200');
            $(this).find('img').css('filter', 'grayscale(0)').css('border-color', '#3366CC');

            // Set provider ID
            $('#wallet_provider_id').val($(this).data('provider'));
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
                    c.classList.remove('border-primary', 'bg-blue-50');
                    c.classList.add('border-gray-200');
                });

                this.classList.remove('border-gray-200');
                this.classList.add('border-primary', 'bg-blue-50');
            });
        });

        // New beneficiary card handling
        const newBeneficiaryCard = document.querySelector('.new-beneficiary');
        if (newBeneficiaryCard) {
            newBeneficiaryCard.addEventListener('click', function() {
                // Clear form fields
                mobileNumberInput.value = '';
                recipientNameInput.value = '';
                walletProviderInput.value = '';

                // Reset provider selection
                $('.provider-option').removeClass('active');
                $('.provider-option').removeClass('border-primary').addClass('border-gray-200');
                $('.provider-option').removeClass('bg-blue-50');
                $('.provider-option img').css('filter', 'grayscale(1)').css('border-color', 'transparent');

                // Remove active class from all beneficiary cards
                beneficiaryCards.forEach(c => {
                    c.classList.remove('border-primary', 'bg-blue-50');
                    c.classList.add('border-gray-200');
                });

                // Focus on mobile number input
                mobileNumberInput.focus();
            });
        }
    });
</script>
@endpush
