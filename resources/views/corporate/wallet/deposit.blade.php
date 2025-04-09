
@extends('corporate.layouts.app')

@section('title', 'Deposit Funds - ' . config('app.name'))
@section('meta_description', 'Add funds to your corporate wallet')
@section('header_title', 'Deposit Funds')

@section('content')
    <div class="mb-6">
        <div class="flex items-center mb-2">
            <a href="{{ route('corporate.wallet.index') }}" class="text-corporate-primary hover:underline">
                <i class="fas fa-arrow-left mr-2"></i> Back to Wallet
            </a>
        </div>
        <h2 class="text-xl font-bold text-corporate-primary">Deposit Funds</h2>
        <p class="text-gray-500">Add money to your corporate wallet</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Deposit Methods Tabs -->
            <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                <div class="border-b border-gray-200">
                    <nav class="flex -mb-px">
                        <a href="{{ route('corporate.wallet.deposit', ['method' => 'bank']) }}" class="py-4 px-6 border-b-2 {{ request('method', 'bank') == 'bank' ? 'border-corporate-primary text-corporate-primary' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} font-medium text-sm">
                            <i class="fas fa-university mr-2"></i> Bank Transfer
                        </a>
                        <a href="{{ route('corporate.wallet.deposit', ['method' => 'card']) }}" class="py-4 px-6 border-b-2 {{ request('method') == 'card' ? 'border-corporate-primary text-corporate-primary' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} font-medium text-sm">
                            <i class="fas fa-credit-card mr-2"></i> Card Payment
                        </a>
                        <a href="{{ route('corporate.wallet.deposit', ['method' => 'mobile']) }}" class="py-4 px-6 border-b-2 {{ request('method') == 'mobile' ? 'border-corporate-primary text-corporate-primary' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} font-medium text-sm">
                            <i class="fas fa-mobile-alt mr-2"></i> Mobile Money
                        </a>
                    </nav>
                </div>

                <div class="p-6">
                    <!-- Bank Transfer Method (Default) -->
                    @if(request('method', 'bank') == 'bank')
                        <h3 class="text-lg font-semibold text-corporate-primary mb-4">Bank Transfer Instructions</h3>
                        
                        <div class="bg-corporate-primary bg-opacity-5 rounded-lg p-4 mb-6">
                            <p class="text-sm text-gray-700 mb-4">Please transfer the funds to our bank account using the details below. Once the transfer is complete, fill in the form to notify us about your deposit.</p>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <p class="text-sm text-gray-500 mb-1">Bank Name</p>
                                    <p class="text-base font-medium text-gray-900">First National Bank</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500 mb-1">Account Name</p>
                                    <p class="text-base font-medium text-gray-900">TechPay Limited</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500 mb-1">Account Number</p>
                                    <div class="flex items-center">
                                        <p class="text-base font-medium text-gray-900 mr-2">62345678901</p>
                                        <button type="button" class="text-corporate-primary hover:text-corporate-accent" onclick="copyToClipboard('62345678901')">
                                            <i class="fas fa-copy"></i>
                                        </button>
                                    </div>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500 mb-1">Branch Code</p>
                                    <div class="flex items-center">
                                        <p class="text-base font-medium text-gray-900 mr-2">260001</p>
                                        <button type="button" class="text-corporate-primary hover:text-corporate-accent" onclick="copyToClipboard('260001')">
                                            <i class="fas fa-copy"></i>
                                        </button>
                                    </div>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500 mb-1">Swift Code</p>
                                    <div class="flex items-center">
                                        <p class="text-base font-medium text-gray-900 mr-2">FIRNZMLX</p>
                                        <button type="button" class="text-corporate-primary hover:text-corporate-accent" onclick="copyToClipboard('FIRNZMLX')">
                                            <i class="fas fa-copy"></i>
                                        </button>
                                    </div>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500 mb-1">Reference</p>
                                    <div class="flex items-center">
                                        <p class="text-base font-medium text-gray-900 mr-2">{{ auth()->user()->company->name ?? 'Company Name' }}-{{ auth()->id() }}</p>
                                        <button type="button" class="text-corporate-primary hover:text-corporate-accent" onclick="copyToClipboard('{{ auth()->user()->company->name ?? 'Company Name' }}-{{ auth()->id() }}')">
                                            <i class="fas fa-copy"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <form action="{{ route('corporate.wallet.notify-deposit') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                            @csrf
                            <input type="hidden" name="deposit_method" value="bank">
                            
                            <div>
                                <label for="amount" class="block text-sm font-medium text-gray-700 mb-1">Amount Transferred <span class="text-corporate-error">*</span></label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <span class="text-gray-500">K</span>
                                    </div>
                                    <input type="number" id="amount" name="amount" required min="100" step="0.01" class="w-full pl-8 pr-4 py-2 border rounded-lg focus:ring-2 focus:ring-corporate-primary focus:border-corporate-primary" placeholder="0.00">
                                </div>
                                <p class="text-xs text-gray-500 mt-1">Minimum deposit amount: K100.00</p>
                            </div>
                            
                            <div>
                                <label for="reference" class="block text-sm font-medium text-gray-700 mb-1">Bank Reference Number <span class="text-corporate-error">*</span></label>
                                <input type="text" id="reference" name="reference" required class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-corporate-primary focus:border-corporate-primary" placeholder="Enter the reference number from your bank">
                                <p class="text-xs text-gray-500 mt-1">This is the reference or transaction number provided by your bank</p>
                            </div>
                            
                            <div>
                                <label for="transfer_date" class="block text-sm font-medium text-gray-700 mb-1">Transfer Date <span class="text-corporate-error">*</span></label>
                                <input type="date" id="transfer_date" name="transfer_date" required class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-corporate-primary focus:border-corporate-primary">
                            </div>
                            
                            <div>
                                <label for="proof_of_payment" class="block text-sm font-medium text-gray-700 mb-1">Proof of Payment (Optional)</label>
                                <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-lg">
                                    <div class="space-y-1 text-center">
                                        <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48" aria-hidden="true">
                                            <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                        </svg>
                                        <div class="flex text-sm text-gray-600">
                                            <label for="file-upload" class="relative cursor-pointer bg-white rounded-md font-medium text-corporate-primary hover:text-corporate-accent">
                                                <span>Upload a file</span>
                                                <input id="file-upload" name="proof_of_payment" type="file" class="sr-only" accept=".jpg,.jpeg,.png,.pdf">
                                            </label>
                                            <p class="pl-1">or drag and drop</p>
                                        </div>
                                        <p class="text-xs text-gray-500">PNG, JPG, or PDF up to 5MB</p>
                                    </div>
                                </div>
                                <div id="file-name" class="mt-2 text-sm text-corporate-primary hidden">
                                    <i class="fas fa-file mr-1"></i> <span></span>
                                </div>
                            </div>
                            
                            <div>
                                <label for="notes" class="block text-sm font-medium text-gray-700 mb-1">Additional Notes (Optional)</label>
                                <textarea id="notes" name="notes" rows="3" class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-corporate-primary focus:border-corporate-primary" placeholder="Any additional information about this deposit"></textarea>
                            </div>
                            
                            <div class="flex justify-end space-x-3 pt-4">
                                <a href="{{ route('corporate.wallet.index') }}" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                                    Cancel
                                </a>
                                <button type="submit" class="px-4 py-2 bg-corporate-primary text-white rounded-lg hover:bg-opacity-90">
                                    Notify Deposit
                                </button>
                            </div>
                        </form>
                    @endif

                    <!-- Card Payment Method -->
                    @if(request('method') == 'card')
                        <h3 class="text-lg font-semibold text-corporate-primary mb-4">Card Payment</h3>
                        
                        <form action="{{ route('corporate.wallet.process-card-deposit') }}" method="POST" class="space-y-6">
                            @csrf
                            
                            <div>
                                <label for="card_amount" class="block text-sm font-medium text-gray-700 mb-1">Amount to Deposit <span class="text-corporate-error">*</span></label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <span class="text-gray-500">K</span>
                                    </div>
                                    <input type="number" id="card_amount" name="amount" required min="100" step="0.01" class="w-full pl-8 pr-4 py-2 border rounded-lg focus:ring-2 focus:ring-corporate-primary focus:border-corporate-primary" placeholder="0.00">
                                </div>
                                <p class="text-xs text-gray-500 mt-1">Minimum deposit amount: K100.00</p>
                            </div>
                            
                            <div class="bg-corporate-primary bg-opacity-5 rounded-lg p-4">
                                <h4 class="font-medium text-corporate-primary mb-2">Payment Information</h4>
                                <ul class="text-sm text-gray-600 space-y-1">
                                    <li><i class="fas fa-info-circle text-corporate-primary mr-2"></i> You will be redirected to our secure payment gateway.</li>
                                    <li><i class="fas fa-info-circle text-corporate-primary mr-2"></i> Your wallet will be credited immediately after successful payment.</li>
                                    <li><i class="fas fa-info-circle text-corporate-primary mr-2"></i> A 2% processing fee applies to card deposits.</li>
                                </ul>
                            </div>
                            
                            <div class="flex justify-end space-x-3 pt-4">
                                <a href="{{ route('corporate.wallet.index') }}" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                                    Cancel
                                </a>
                                <button type="submit" class="px-4 py-2 bg-corporate-primary text-white rounded-lg hover:bg-opacity-90">
                                    Proceed to Payment
                                </button>
                            </div>
                        </form>
                    @endif

                    <!-- Mobile Money Method -->
                    @if(request('method') == 'mobile')
                        <h3 class="text-lg font-semibold text-corporate-primary mb-4">Mobile Money Deposit</h3>
                        
                        <div class="bg-corporate-primary bg-opacity-5 rounded-lg p-4 mb-6">
                            <p class="text-sm text-gray-700 mb-4">Follow these steps to deposit funds using mobile money:</p>
                            
                            <ol class="space-y-4 text-sm text-gray-600">
                                <li class="flex">
                                    <div class="flex-shrink-0 w-6 h-6 rounded-full bg-corporate-primary text-white flex items-center justify-center text-xs mr-3">
                                        1
                                    </div>
                                    <div>
                                        <p class="font-medium text-gray-900">Dial the USSD code for your mobile money provider</p>
                                        <p>MTN: *305#, Airtel: *778#, Zamtel: *344#</p>
                                    </div>
                                </li>
                                <li class="flex">
                                    <div class="flex-shrink-0 w-6 h-6 rounded-full bg-corporate-primary text-white flex items-center justify-center text-xs mr-3">
                                        2
                                    </div>
                                    <div>
                                        <p class="font-medium text-gray-900">Select "Pay Bill" or "Make Payment"</p>
                                    </div>
                                </li>
                                <li class="flex">
                                    <div class="flex-shrink-0 w-6 h-6 rounded-full bg-corporate-primary text-white flex items-center justify-center text-xs mr-3">
                                        3
                                    </div>
                                    <div>
                                        <p class="font-medium text-gray-900">Enter the business code</p>
                                        <p>TechPay: 123456</p>
                                    </div>
                                </li>
                                <li class="flex">
                                    <div class="flex-shrink-0 w-6 h-6 rounded-full bg-corporate-primary text-white flex items-center justify-center text-xs mr-3">
                                        4
                                    </div>
                                    <div>
                                        <p class="font-medium text-gray-900">Enter your reference number</p>
                                        <p>{{ auth()->user()->company->name ?? 'Company Name' }}-{{ auth()->id() }}</p>
                                    </div>
                                </li>
                                <li class="flex">
                                    <div class="flex-shrink-0 w-6 h-6 rounded-full bg-corporate-primary text-white flex items-center justify-center text-xs mr-3">
                                        5
                                    </div>
                                    <div>
                                        <p class="font-medium text-gray-900">Enter the amount you wish to deposit</p>
                                    </div>
                                </li>
                                <li class="flex">
                                    <div class="flex-shrink-0 w-6 h-6 rounded-full bg-corporate-primary text-white flex items-center justify-center text-xs mr-3">
                                        6
                                    </div>
                                    <div>
                                        <p class="font-medium text-gray-900">Confirm the payment with your PIN</p>
                                    </div>
                                </li>
                            </ol>
                        </div>
                        
                        <form action="{{ route('corporate.wallet.notify-deposit') }}" method="POST" class="space-y-6">
                            @csrf
                            <input type="hidden" name="deposit_method" value="mobile">
                            
                            <div>
                                <label for="mobile_provider" class="block text-sm font-medium text-gray-700 mb-1">Mobile Money Provider <span class="text-corporate-error">*</span></label>
                                <select id="mobile_provider" name="mobile_provider" required class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-corporate-primary focus:border-corporate-primary">
                                    <option value="">Select Provider</option>
                                    <option value="mtn">MTN Money</option>
                                    <option value="airtel">Airtel Money</option>
                                    <option value="zamtel">Zamtel Money</option>
                                </select>
                            </div>
                            
                            <div>
                                <label for="mobile_number" class="block text-sm font-medium text-gray-700 mb-1">Mobile Number <span class="text-corporate-error">*</span></label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <span class="text-gray-500">+260</span>
                                    </div>
                                    <input type="text" id="mobile_number" name="mobile_number" required pattern="[0-9]{9}" class="w-full pl-14 pr-4 py-2 border rounded-lg focus:ring-2 focus:ring-corporate-primary focus:border-corporate-primary" placeholder="977123456">
                                </div>
                                <p class="text-xs text-gray-500 mt-1">Enter the 9-digit mobile number without the country code</p>
                            </div>
                            
                            <div>
                                <label for="mobile_amount" class="block text-sm font-medium text-gray-700 mb-1">Amount Sent <span class="text-corporate-error">*</span></label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <span class="text-gray-500">K</span>
                                    </div>
                                    <input type="number" id="mobile_amount" name="amount" required min="10" step="0.01" class="w-full pl-8 pr-4 py-2 border rounded-lg focus:ring-2 focus:ring-corporate-primary focus:border-corporate-primary" placeholder="0.00">
                                </div>
                                <p class="text-xs text-gray-500 mt-1">Minimum deposit amount: K10.00</p>
                            </div>
                            
                            <div>
                                <label for="mobile_reference" class="block text-sm font-medium text-gray-700 mb-1">Transaction Reference <span class="text-corporate-error">*</span></label>
                                <input type="text" id="mobile_reference" name="reference" required class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-corporate-primary focus:border-corporate-primary" placeholder="Enter the transaction reference from your mobile money receipt">
                                <p class="text-xs text-gray-500 mt-1">This is the reference number provided in your mobile money confirmation message</p>
                            </div>
                            
                            <div class="flex justify-end space-x-3 pt-4">
                                <a href="{{ route('corporate.wallet.index') }}" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                                    Cancel
                                </a>
                                <button type="submit" class="px-4 py-2 bg-corporate-primary text-white rounded-lg hover:bg-opacity-90">
                                    Notify Deposit
                                </button>
                            </div>
                        </form>
                    @endif
                </div>
            </div>
        </div>

        <!-- Right Sidebar -->
        <div class="space-y-6">
            <!-- Wallet Balance -->
            <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-corporate-primary mb-4">Current Balance</h3>
                    
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <p class="text-sm text-gray-500 mb-1">Available Balance</p>
                            <p class="text-2xl font-bold text-corporate-primary">K 250,000.00</p>
                        </div>
                        <div class="w-12 h-12 rounded-full bg-corporate-primary bg-opacity-10 flex items-center justify-center text-corporate-primary">
                            <i class="fas fa-wallet"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Deposit Information -->
            <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-corporate-primary mb-4">Deposit Information</h3>
                    
                    <div class="space-y-4 text-sm text-gray-600">
                        <div>
                            <h4 class="font-medium text-corporate-primary">Processing Time</h4>
                            <ul class="mt-2 space-y-1">
                                <li><i class="fas fa-check-circle text-corporate-success mr-2"></i> Card payments: Instant</li>
                                <li><i class="fas fa-check-circle text-corporate-success mr-2"></i> Mobile money: Within 30 minutes</li>
                                <li><i class="fas fa-check-circle text-corporate-success mr-2"></i> Bank transfers: 1-2 business days</li>
                            </ul>
                        </div>
                        
                        <div>
                            <h4 class="font-medium text-corporate-primary">Fees</h4>
                            <ul class="mt-2 space-y-1">
                                <li><i class="fas fa-info-circle text-corporate-primary mr-2"></i> Bank transfers: No fee</li>
                                <li><i class="fas fa-info-circle text-corporate-primary mr-2"></i> Card payments: 2% processing fee</li>
                                <li><i class="fas fa-info-circle text-corporate-primary mr-2"></i> Mobile money: Provider charges may apply</li>
                            </ul>
                        </div>
                        
                        <div>
                            <h4 class="font-medium text-corporate-primary">Minimum Deposit</h4>
                            <ul class="mt-2 space-y-1">
                                <li><i class="fas fa-info-circle text-corporate-primary mr-2"></i> Bank transfers: K100.00</li>
                                <li><i class="fas fa-info-circle text-corporate-primary mr-2"></i> Card payments: K100.00</li>
                                <li><i class="fas fa-info-circle text-corporate-primary mr-2"></i> Mobile money: K10.00</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Need Help -->
            <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-corporate-primary mb-4">Need Help?</h3>
                    
                    <p class="text-sm text-gray-600 mb-4">If you have any questions or need assistance with your deposit, please contact our support team:</p>
                    
                    <div class="space-y-3">
                        <div class="flex items-center">
                            <div class="w-10 h-10 rounded-full bg-corporate-primary bg-opacity-10 flex items-center justify-center text-corporate-primary mr-3">
                                <i class="fas fa-phone-alt"></i>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-900">+260 211 123456</p>
                                <p class="text-xs text-gray-500">Monday to Friday, 8:00 AM - 5:00 PM</p>
                            </div>
                        </div>
                        <div class="flex items-center">
                            <div class="w-10 h-10 rounded-full bg-corporate-primary bg-opacity-10 flex items-center justify-center text-corporate-primary mr-3">
                                <i class="fas fa-envelope"></i>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-900">support@techpay.com</p>
                                <p class="text-xs text-gray-500">We'll respond within 24 hours</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    function copyToClipboard(text) {
        navigator.clipboard.writeText(text).then(function() {
            // Show a temporary tooltip or notification
            alert('Copied to clipboard: ' + text);
        }, function(err) {
            console.error('Could not copy text: ', err);
        });
    }

    document.addEventListener('DOMContentLoaded', function() {
        const fileUpload = document.getElementById('file-upload');
        const fileName = document.getElementById('file-name');
        
        if (fileUpload) {
            fileUpload.addEventListener('change', function() {
                if (this.files && this.files[0]) {
                    fileName.querySelector('span').textContent = this.files[0].name;
                    fileName.classList.remove('hidden');
                } else {
                    fileName.classList.add('hidden');
                }
            });
            
            // Handle drag and drop
            const dropZone = document.querySelector('.border-dashed');
            
            ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
                dropZone.addEventListener(eventName, preventDefaults, false);
            });
            
            function preventDefaults(e) {
                e.preventDefault();
                e.stopPropagation();
            }
            
            ['dragenter', 'dragover'].forEach(eventName => {
                dropZone.addEventListener(eventName, highlight, false);
            });
            
            ['dragleave', 'drop'].forEach(eventName => {
                dropZone.addEventListener(eventName, unhighlight, false);
            });
            
            function highlight() {
                dropZone.classList.add('border-corporate-primary', 'bg-corporate-primary', 'bg-opacity-5');
            }
            
            function unhighlight() {
                dropZone.classList.remove('border-corporate-primary', 'bg-corporate-primary', 'bg-opacity-5');
            }
            
            dropZone.addEventListener('drop', handleDrop, false);
            
            function handleDrop(e) {
                const dt = e.dataTransfer;
                const files = dt.files;
                
                if (files && files[0]) {
                    fileUpload.files = files;
                    fileName.querySelector('span').textContent = files[0].name;
                    fileName.classList.remove('hidden');
                }
            }
        }
    });
</script>
@endpush
