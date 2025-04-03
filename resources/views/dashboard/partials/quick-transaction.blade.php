<!-- Quick Transaction Button -->
<div class="bg-white rounded-xl shadow-sm overflow-hidden mb-6 p-6">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between">
        <div>
            <h2 class="text-xl font-bold text-dark mb-1">Quick Transaction</h2>
            <p class="text-gray-600">Send money to a mobile wallet instantly</p>
        </div>
        
        <div class="mt-4 md:mt-0 flex space-x-3">
            <button type="button" id="open-quick-transaction-modal" class="bg-primary text-white py-2 px-4 rounded-lg hover:bg-opacity-90 transition duration-300 font-medium">
                <i class="fas fa-paper-plane mr-2"></i> Quick Send
            </button>
            
            <a href="{{ route('transactions.initiate') }}" class="text-primary hover:underline flex items-center">
                <i class="fas fa-external-link-alt mr-1"></i> Advanced options
            </a>
        </div>
    </div>
</div>

<!-- Quick Transaction Modal -->
<div id="quick-transaction-modal" class="fixed inset-0 z-50 hidden overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen p-4">
        <!-- Modal Backdrop -->
        <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity" id="modal-backdrop"></div>
        
        <!-- Modal Content -->
        <div class="relative bg-white rounded-xl shadow-xl max-w-2xl w-full mx-auto z-10">
            <div class="p-6">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h2 class="text-xl font-bold text-dark mb-1">Quick Transaction</h2>
                        <p class="text-gray-600">Send money to a mobile wallet instantly</p>
                    </div>
                    
                    <button type="button" id="close-quick-transaction-modal" class="text-gray-500 hover:text-gray-700">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
        
                <form action="{{ route('transactions.quick') }}" method="POST" id="quick-transaction-form">
                    @csrf
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Mobile Provider -->
                        <div>
                            <label for="wallet_provider_id" class="block text-sm font-medium text-gray-700 mb-1">Mobile Provider</label>
                            <select id="wallet_provider_id" name="wallet_provider_id" class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-primary focus:border-primary" required>
                                <option value="">Select Provider</option>
                                @foreach($walletProviders ?? [] as $provider)
                                    <option value="{{ $provider->id }}" data-code="{{ $provider->api_code }}">{{ $provider->name }}</option>
                                @endforeach
                            </select>
                            @error('wallet_provider_id')
                                <p class="text-error text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <!-- Mobile Number -->
                        <div>
                            <label for="wallet_number" class="block text-sm font-medium text-gray-700 mb-1">Mobile Number</label>
                            <div class="flex">
                                <span class="inline-flex items-center px-3 text-gray-500 bg-gray-100 border border-r-0 border-gray-300 rounded-l-lg">
                                    +260
                                </span>
                                <input type="text" id="wallet_number" name="wallet_number" class="w-full px-4 py-3 border border-gray-300 rounded-r-lg focus:ring-2 focus:ring-primary focus:border-primary" placeholder="97XXXXXXX" required>
                            </div>
                            <p class="text-xs text-gray-500 mt-1">Enter 9-digit number without leading zero</p>
                            @error('wallet_number')
                                <p class="text-error text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <!-- Amount -->
                        <div>
                            <label for="amount" class="block text-sm font-medium text-gray-700 mb-1">Amount (ZMW)</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <span class="text-gray-500">K</span>
                                </div>
                                <input type="number" id="amount" name="amount" min="10" step="0.01" class="w-full pl-8 pr-4 py-3 border rounded-lg focus:ring-2 focus:ring-primary focus:border-primary" placeholder="0.00" required>
                            </div>
                            <p class="text-xs text-gray-500 mt-1">Minimum: K10.00</p>
                            @error('amount')
                                <p class="text-error text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <!-- Recipient Name -->
                        <div>
                            <label for="recipient_name" class="block text-sm font-medium text-gray-700 mb-1">Recipient Name (Optional)</label>
                            <input type="text" id="recipient_name" name="recipient_name" class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-primary focus:border-primary" placeholder="Enter recipient name">
                            @error('recipient_name')
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
                            <i class="fas fa-paper-plane mr-2"></i> Send Money
                        </button>
                    </div>
                </form>
                
                <!-- Beneficiary Dropdown -->
                <div class="mt-4">
                    <div class="flex items-center justify-between">
                        <h3 class="text-sm font-medium text-gray-700">Recent Recipients</h3>
                        <a href="{{ route('beneficiaries.index') }}" class="text-primary hover:underline text-sm">View All</a>
                    </div>
                    
                    <div class="mt-2 flex flex-wrap gap-2">
                        @forelse($recentBeneficiaries ?? [] as $beneficiary)
                            <button type="button" 
                                    class="inline-flex items-center px-3 py-1 bg-gray-100 hover:bg-gray-200 rounded-full text-sm text-gray-700 transition beneficiary-chip"
                                    data-provider="{{ $beneficiary->wallet_provider_id }}"
                                    data-number="{{ $beneficiary->wallet_number }}"
                                    data-name="{{ $beneficiary->recipient_name }}">
                                <span class="w-4 h-4 rounded-full bg-primary text-white flex items-center justify-center mr-1 text-xs">
                                    {{ strtoupper(substr($beneficiary->recipient_name, 0, 1)) }}
                                </span>
                                {{ $beneficiary->recipient_name }}
                            </button>
                        @empty
                            <p class="text-sm text-gray-500">No recent recipients</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Modal elements
        const modal = document.getElementById('quick-transaction-modal');
        const modalBackdrop = document.getElementById('modal-backdrop');
        const openModalButton = document.getElementById('open-quick-transaction-modal');
        const closeModalButton = document.getElementById('close-quick-transaction-modal');
        
        // Form elements
        const amountInput = document.getElementById('amount');
        const displayAmount = document.getElementById('display-amount');
        const displayFee = document.getElementById('display-fee');
        const displayTotal = document.getElementById('display-total');
        const walletNumberInput = document.getElementById('wallet_number');
        const beneficiaryChips = document.querySelectorAll('.beneficiary-chip');
        const walletProviderSelect = document.getElementById('wallet_provider_id');
        
        // Modal functions
        function openModal() {
            modal.classList.remove('hidden');
            document.body.classList.add('overflow-hidden');
        }
        
        function closeModal() {
            modal.classList.add('hidden');
            document.body.classList.remove('overflow-hidden');
        }
        
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
        
        // Phone number validation
        function validatePhoneNumber() {
            // Remove non-numeric characters
            this.value = this.value.replace(/\D/g, '');
            
            // Limit to 9 digits
            if (this.value.length > 9) {
                this.value = this.value.slice(0, 9);
            }
        }
        
        // Fill form with beneficiary data
        function fillBeneficiaryData() {
            const providerId = this.dataset.provider;
            const number = this.dataset.number;
            const name = this.dataset.name;
            
            walletProviderSelect.value = providerId;
            walletNumberInput.value = number;
            document.getElementById('recipient_name').value = name;
            
            // Add active class to selected chip
            beneficiaryChips.forEach(chip => {
                chip.classList.remove('bg-primary', 'text-white');
                chip.classList.add('bg-gray-100', 'text-gray-700');
            });
            
            this.classList.remove('bg-gray-100', 'text-gray-700');
            this.classList.add('bg-primary', 'text-white');
        }
        
        // Event listeners for modal
        openModalButton.addEventListener('click', openModal);
        closeModalButton.addEventListener('click', closeModal);
        modalBackdrop.addEventListener('click', closeModal);
        
        // Close modal when pressing Escape key
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape' && !modal.classList.contains('hidden')) {
                closeModal();
            }
        });
        
        // Event listeners for form
        amountInput.addEventListener('input', calculateFee);
        walletNumberInput.addEventListener('input', validatePhoneNumber);
        
        beneficiaryChips.forEach(chip => {
            chip.addEventListener('click', fillBeneficiaryData);
        });
        
        // Initialize
        calculateFee();
    });
</script>
