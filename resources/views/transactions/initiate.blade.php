@extends('layouts.app')

@section('title', 'New Transaction - ' . config('app.name'))
@section('meta_description', 'Send money to a mobile wallet')
@section('header_title', 'New Transaction')

@push('pre-styles')
<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
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
</style>
@endpush

@section('content')
    <!-- Transaction Steps -->
    <div class="bg-white rounded-xl shadow-sm overflow-hidden mb-6">
        <div class="p-6">
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6">
                <h2 class="text-xl font-bold text-dark mb-2 md:mb-0">Send Money to Mobile Wallet</h2>

                <div class="flex space-x-2">
                    <a href="{{ route('dashboard') }}" class="inline-flex items-center px-3 py-1 bg-gray-100 hover:bg-gray-200 rounded text-sm text-gray-700 transition">
                        <i class="fas fa-arrow-left mr-1"></i> Back to Dashboard
                    </a>
                    <a href="{{ route('transactions.history') }}" class="inline-flex items-center px-3 py-1 bg-gray-100 hover:bg-gray-200 rounded text-sm text-gray-700 transition">
                        <i class="fas fa-history mr-1"></i> Transaction History
                    </a>
                </div>
            </div>

            <div class="flex flex-col md:flex-row mb-6">
                <div class="flex-1 flex flex-col md:flex-row items-start md:items-center justify-between p-4 border-b md:border-b-0 md:border-r border-gray-200">
                    <div class="flex items-center mb-2 md:mb-0">
                        <div class="w-10 h-10 rounded-full bg-primary text-white flex items-center justify-center mr-3">
                            <i class="fas fa-wallet"></i>
                        </div>
                        <div>
                            <h3 class="font-medium text-dark">Available Balance</h3>
                            <p class="text-gray-500">Your account balance</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <p class="text-2xl font-bold text-dark">K{{ number_format(auth()->user()->balance ?? 0, 2) }}</p>
                    </div>
                </div>

                <div class="flex-1 flex flex-col md:flex-row items-start md:items-center justify-between p-4">
                    <div class="flex items-center mb-2 md:mb-0">
                        <div class="w-10 h-10 rounded-full bg-light text-primary flex items-center justify-center mr-3">
                            <i class="fas fa-exchange-alt"></i>
                        </div>
                        <div>
                            <h3 class="font-medium text-dark">Transaction Limit</h3>
                            <p class="text-gray-500">Your current limit</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <p class="text-2xl font-bold text-dark">
                            K{{ number_format(auth()->user()->verification_level === 'verified' ? 5000 : 1000, 2) }}
                        </p>
                        <p class="text-xs text-gray-500">
                            {{ auth()->user()->verification_level === 'verified' ? 'Verified Account' : 'Basic Account' }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- Steps Progress -->
            <div class="mb-6">
                <div class="flex flex-col md:flex-row justify-between">
                    <div class="flex-1 flex items-center mb-4 md:mb-0">
                        <div class="w-8 h-8 rounded-full bg-primary text-white flex items-center justify-center">
                            1
                        </div>
                        <div class="ml-3">
                            <h3 class="font-medium text-dark">Recipient Details</h3>
                            <p class="text-sm text-gray-500">Enter mobile wallet details</p>
                        </div>
                    </div>
                    <div class="flex-1 flex items-center mb-4 md:mb-0">
                        <div class="w-8 h-8 rounded-full bg-gray-200 text-gray-600 flex items-center justify-center">
                            2
                        </div>
                        <div class="ml-3">
                            <h3 class="font-medium text-gray-600">Confirm Details</h3>
                            <p class="text-sm text-gray-500">Verify transaction details</p>
                        </div>
                    </div>
                    <div class="flex-1 flex items-center">
                        <div class="w-8 h-8 rounded-full bg-gray-200 text-gray-600 flex items-center justify-center">
                            3
                        </div>
                        <div class="ml-3">
                            <h3 class="font-medium text-gray-600">Make Payment</h3>
                            <p class="text-sm text-gray-500">Complete secure payment</p>
                        </div>
                    </div>
                </div>
                <div class="relative mt-4">
                    <div class="overflow-hidden h-2 text-xs flex rounded bg-gray-200">
                        <div style="width: 33.3%" class="shadow-none flex flex-col text-center whitespace-nowrap text-white justify-center bg-primary"></div>
                    </div>
                </div>
            </div>

            <!-- Transaction Form -->
            <form action="{{ route('transactions.confirm') }}" method="POST" id="transaction-form">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Mobile Number with Provider Detection -->
                    <div>
                        <label for="wallet_number" class="block text-sm font-medium text-gray-700 mb-1">Mobile Number</label>
                        <div class="flex">
                            <span class="inline-flex items-center px-3 text-gray-500 bg-gray-100 border border-r-0 border-gray-300 rounded-l-lg">
                                +260
                            </span>
                            <input type="text" id="mobileNumber" name="wallet_number" value="{{ old('wallet_number', $selectedBeneficiary->wallet_number ?? '') }}" class="w-full px-4 py-3 border border-gray-300 rounded-r-lg focus:ring-2 focus:ring-primary focus:border-primary" placeholder="97XXXXXXX" required>
                        </div>
                        <p class="text-xs text-gray-500 mt-1">Enter 9-digit number without leading zero</p>
                        @error('wallet_number')
                            <p class="text-error text-sm mt-1">{{ $message }}</p>
                        @enderror

                        <!-- Hidden input for wallet provider -->
                        <input type="hidden" id="wallet_provider_id" name="wallet_provider_id" value="{{ old('wallet_provider_id', $selectedBeneficiary->wallet_provider_id ?? '') }}">

                        <!-- Network Provider Logos -->
                        <div class="flex items-center space-x-4 mt-2">
                            <div class="p-mode mtn" title="MTN Mobile Money">
                                <img src="{{ asset('assets/img/mtn.jpg') }}" alt="MTN" class="h-9 w-9 rounded-full object-cover" style="filter: grayscale(1);">
                            </div>
                            <div class="p-mode airtel" title="Airtel Money">
                                <img src="{{ asset('assets/img/airtel.png') }}" alt="Airtel" class="h-9 w-9 rounded-full object-cover" style="filter: grayscale(1);">
                            </div>
                            <div class="p-mode zamtel" title="Zamtel Kwacha">
                                <img src="{{ asset('assets/img/zamtel.jpg') }}" alt="Zamtel" class="h-9 w-9 rounded-full object-cover" style="filter: grayscale(1);">
                            </div>
                        </div>
                    </div>

                    <!-- Amount -->
                    <div>
                        <label for="amount" class="block text-sm font-medium text-gray-700 mb-1">Amount (ZMW)</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <span class="text-gray-500">K</span>
                            </div>
                            <input type="number" id="amount" name="amount" value="{{ old('amount') }}" min="10" max="{{ auth()->user()->verification_level === 'verified' ? 5000 : 1000 }}" step="0.01" class="w-full pl-8 pr-4 py-3 border rounded-lg focus:ring-2 focus:ring-primary focus:border-primary" placeholder="0.00" required>
                        </div>
                        <p class="text-xs text-gray-500 mt-1">
                            Min: K10.00 | Max: K{{ number_format(auth()->user()->verification_level === 'verified' ? 5000 : 1000, 2) }}
                        </p>
                        @error('amount')
                            <p class="text-error text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>


                    <!-- Recipient Name -->
                    <div>
                        <label for="recipient_name" class="block text-sm font-medium text-gray-700 mb-1">Recipient Name</label>
                        <input type="text" id="recipient_name" name="recipient_name" value="{{ old('recipient_name', $selectedBeneficiary->recipient_name ?? '') }}" class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-primary focus:border-primary" placeholder="Enter recipient name">
                        @error('recipient_name')
                            <p class="text-error text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Purpose -->
                    <div>
                        <label for="purpose" class="block text-sm font-medium text-gray-700 mb-1">Purpose (Optional)</label>
                        <select id="purpose" name="purpose" class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-primary focus:border-primary">
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
                            <p class="text-error text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Notes -->
                    <div>
                        <label for="notes" class="block text-sm font-medium text-gray-700 mb-1">Notes (Optional)</label>
                        <input type="text" id="notes" name="notes" value="{{ old('notes') }}" class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-primary focus:border-primary" placeholder="Add any notes for this transaction">
                        @error('notes')
                            <p class="text-error text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Fee Calculation -->
                <div class="mt-6 p-4 bg-light rounded-lg">
                    <div class="flex justify-between items-center mb-2">
                        <span class="text-gray-700">Amount:</span>
                        <span class="font-medium" id="display-amount">K0.00</span>
                    </div>
                    <div class="flex justify-between items-center mb-2">
                        <span class="text-gray-700">Fee (4%):</span>
                        <span class="font-medium" id="display-fee">K0.00</span>
                    </div>
                    <div class="border-t border-gray-200 my-2 pt-2 flex justify-between items-center">
                        <span class="text-gray-700 font-medium">Total:</span>
                        <span class="font-bold text-dark" id="display-total">K0.00</span>
                    </div>
                </div>

                <!-- Save Beneficiary -->
                <div class="mt-4">
                    <label class="inline-flex items-center">
                        <input type="checkbox" name="save_beneficiary" class="h-4 w-4 text-primary focus:ring-primary border-gray-300 rounded">
                        <span class="ml-2 text-gray-700">Save this recipient for future transactions</span>
                    </label>
                </div>

                <!-- Submit Button -->
                <div class="mt-6">
                    <button type="submit" class="w-full bg-primary text-white py-3 px-4 rounded-lg hover:bg-opacity-90 transition duration-300 font-medium">
                        <i class="fas fa-arrow-right mr-2"></i> Continue to Confirm
                    </button>
                </div>
            </form>

            <!-- Saved Beneficiaries -->
            <div class="mt-8">
                <h3 class="text-lg font-medium text-dark mb-4">Select from Saved Beneficiaries</h3>

                @if(count($savedBeneficiaries ?? []) > 0)
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        @foreach($savedBeneficiaries as $beneficiary)
                            <div class="flex items-center p-3 bg-light rounded-lg hover:bg-gray-100 transition cursor-pointer beneficiary-card"
                                 data-provider="{{ $beneficiary->wallet_provider_id }}"
                                 data-number="{{ $beneficiary->wallet_number }}"
                                 data-name="{{ $beneficiary->recipient_name }}">
                                <div class="flex-shrink-0 h-10 w-10">
                                    @if($beneficiary->wallet_provider)
                                        @if($beneficiary->wallet_provider->api_code === 'airtel')
                                            <img class="h-10 w-10 rounded-full" src="{{ asset('assets/img/airtel.png') }}" alt="Airtel">
                                        @elseif($beneficiary->wallet_provider->api_code === 'mtn')
                                            <img class="h-10 w-10 rounded-full" src="{{ asset('assets/img/mtn.jpg') }}" alt="MTN">
                                        @elseif($beneficiary->wallet_provider->api_code === 'zamtel')
                                            <img class="h-10 w-10 rounded-full" src="{{ asset('assets/img/zamtel.jpg') }}" alt="Zamtel">
                                        @else
                                            <div class="h-10 w-10 rounded-full bg-primary text-white flex items-center justify-center">
                                                {{ strtoupper(substr($beneficiary->recipient_name, 0, 1)) }}
                                            </div>
                                        @endif
                                    @else
                                        <div class="h-10 w-10 rounded-full bg-primary text-white flex items-center justify-center">
                                            {{ strtoupper(substr($beneficiary->recipient_name, 0, 1)) }}
                                        </div>
                                    @endif
                                </div>
                                <div class="ml-3">
                                    <div class="text-sm font-medium text-dark">
                                        {{ $beneficiary->recipient_name }}
                                        @if($beneficiary->is_favorite)
                                            <i class="fas fa-star text-secondary ml-1 text-xs"></i>
                                        @endif
                                    </div>
                                    <div class="text-xs text-gray-500">
                                        +260{{ $beneficiary->wallet_number }}
                                    </div>
                                    <div class="text-xs text-gray-500">
                                        {{ $beneficiary->wallet_provider->name ?? 'Unknown Provider' }}
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    @if(count($savedBeneficiaries) > 6)
                        <div class="mt-4 text-center">
                            <a href="{{ route('beneficiaries.index') }}" class="text-primary hover:underline text-sm">
                                View all beneficiaries ({{ count($allBeneficiaries ?? []) }})
                            </a>
                        </div>
                    @endif
                @else
                    <div class="text-center py-8 bg-light rounded-lg">
                        <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-gray-100 text-gray-400 mb-4">
                            <i class="fas fa-users text-2xl"></i>
                        </div>
                        <h3 class="text-lg font-medium text-gray-900 mb-1">No saved beneficiaries</h3>
                        <p class="text-gray-500 mb-4">Save recipients for quick access</p>
                        <a href="{{ route('beneficiaries.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary hover:bg-opacity-90 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary">
                            <i class="fas fa-plus mr-2"></i> Add Beneficiary
                        </a>
                    </div>
                @endif
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
        }

        if (amountInput) {
            amountInput.addEventListener('input', calculateFee);
            calculateFee(); // Initialize
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
                    c.classList.add('bg-light');
                });

                this.classList.remove('bg-light');
                this.classList.add('bg-primary', 'bg-opacity-10');
            });
        });
    });
</script>
@endpush
