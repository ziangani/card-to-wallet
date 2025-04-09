@extends('corporate.layouts.app')

@section('title', 'Create Bulk Disbursement - ' . config('app.name'))
@section('meta_description', 'Create a new bulk disbursement to multiple recipients')
@section('header_title', 'Create Bulk Disbursement')

@section('content')
    <div class="mb-6">
        <div class="flex items-center mb-2">
            <a href="{{ route('corporate.disbursements.index') }}" class="text-corporate-primary hover:underline">
                <i class="fas fa-arrow-left mr-2"></i> Back to Disbursements
            </a>
        </div>
        <h2 class="text-xl font-bold text-corporate-primary">Create New Bulk Disbursement</h2>
        <p class="text-gray-500">Send payments to multiple recipients at once</p>
    </div>

    <!-- Step Indicator -->
    <div class="bg-white rounded-xl shadow-sm overflow-hidden mb-6">
        <div class="p-6">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <div class="flex items-center">
                        <div class="w-8 h-8 rounded-full bg-corporate-primary text-white flex items-center justify-center text-sm font-medium">1</div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-corporate-primary">Upload File</p>
                            <p class="text-xs text-gray-500">Upload recipient data</p>
                        </div>
                    </div>
                    <div class="h-1 bg-corporate-primary mt-3"></div>
                </div>
                <div class="flex-1">
                    <div class="flex items-center">
                        <div class="w-8 h-8 rounded-full bg-gray-200 text-gray-500 flex items-center justify-center text-sm font-medium">2</div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-gray-500">Validate</p>
                            <p class="text-xs text-gray-500">Review validation results</p>
                        </div>
                    </div>
                    <div class="h-1 bg-gray-200 mt-3"></div>
                </div>
                <div class="flex-1">
                    <div class="flex items-center">
                        <div class="w-8 h-8 rounded-full bg-gray-200 text-gray-500 flex items-center justify-center text-sm font-medium">3</div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-gray-500">Review</p>
                            <p class="text-xs text-gray-500">Confirm details</p>
                        </div>
                    </div>
                    <div class="h-1 bg-gray-200 mt-3"></div>
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
        <!-- Left Column - Upload Form -->
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-corporate-primary mb-4">Upload Recipients File</h3>
                    
                    <form action="{{ route('corporate.disbursements.validate') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                        @csrf
                        
                        <!-- Disbursement Name -->
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Disbursement Name <span class="text-red-500">*</span></label>
                            <input type="text" id="name" name="name" value="{{ old('name') }}" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-corporate-primary focus:border-corporate-primary transition duration-200" placeholder="e.g. April Salaries">
                            @error('name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <!-- Description -->
                        <div>
                            <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Description (Optional)</label>
                            <textarea id="description" name="description" rows="3" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-corporate-primary focus:border-corporate-primary transition duration-200" placeholder="Add a description for this disbursement">{{ old('description') }}</textarea>
                            @error('description')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <!-- File Upload -->
                        <div>
                            <label for="file" class="block text-sm font-medium text-gray-700 mb-1">Upload File <span class="text-red-500">*</span></label>
                            <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-lg">
                                <div class="space-y-1 text-center">
                                    <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48" aria-hidden="true">
                                        <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                    <div class="flex text-sm text-gray-600">
                                        <label for="file" class="relative cursor-pointer bg-white rounded-md font-medium text-corporate-primary hover:text-corporate-primary-dark focus-within:outline-none">
                                            <span>Upload a file</span>
                                            <input id="file" name="file" type="file" class="sr-only" accept=".csv,.xlsx,.xls">
                                        </label>
                                        <p class="pl-1">or drag and drop</p>
                                    </div>
                                    <p class="text-xs text-gray-500">CSV or Excel files up to 10MB</p>
                                </div>
                            </div>
                            <div id="file-name" class="mt-2 text-sm text-gray-500"></div>
                            <div id="file-selected-notice" class="hidden mt-3 p-3 bg-blue-50 text-blue-700 rounded-lg">
                                <p><i class="fas fa-info-circle mr-2"></i> File selected! Click the "Validate File" button below to proceed.</p>
                            </div>
                            @error('file')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <!-- Submit Button -->
                        <div class="flex justify-end">
                            <button id="validate-button" type="submit" class="px-6 py-3 bg-corporate-primary text-white rounded-lg hover:bg-opacity-90 transition">
                                Validate File <i class="fas fa-arrow-right ml-2"></i>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <!-- Right Column - Help & Templates -->
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
                    <div class="mt-4">
                        <a href="{{ route('corporate.wallet.deposit') }}" class="text-corporate-primary hover:underline text-sm">
                            <i class="fas fa-plus-circle mr-1"></i> Add Funds
                        </a>
                    </div>
                </div>
            </div>
            
            <!-- Templates -->
            <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-corporate-primary mb-4">Download Templates</h3>
                    <p class="text-sm text-gray-600 mb-4">Use our templates to ensure your data is formatted correctly.</p>
                    
                    <div class="space-y-3">
                        <a href="{{ route('corporate.disbursements.template', 'csv') }}" class="flex items-center p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition">
                            <div class="w-10 h-10 rounded-full bg-red-100 flex items-center justify-center text-red-500 mr-3">
                                <i class="fas fa-file-csv"></i>
                            </div>
                            <div>
                                <h4 class="font-medium text-gray-900">CSV Template</h4>
                                <p class="text-xs text-gray-500">Download CSV format template</p>
                            </div>
                        </a>
                        
                        <a href="{{ route('corporate.disbursements.template', 'xlsx') }}" class="flex items-center p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition">
                            <div class="w-10 h-10 rounded-full bg-green-100 flex items-center justify-center text-green-500 mr-3">
                                <i class="fas fa-file-excel"></i>
                            </div>
                            <div>
                                <h4 class="font-medium text-gray-900">Excel Template</h4>
                                <p class="text-xs text-gray-500">Download Excel format template</p>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
            
            <!-- Help & Guidelines -->
            <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-corporate-primary mb-4">Guidelines</h3>
                    
                    <div class="space-y-4">
                        <div>
                            <h4 class="font-medium text-gray-900 mb-1">Required Columns</h4>
                            <ul class="list-disc list-inside text-sm text-gray-600 space-y-1">
                                <li><span class="font-medium">wallet_number</span> - Recipient's mobile wallet number</li>
                                <li><span class="font-medium">amount</span> - Amount to send (in {{ $wallet->currency }})</li>
                            </ul>
                        </div>
                        
                        <div>
                            <h4 class="font-medium text-gray-900 mb-1">Optional Columns</h4>
                            <ul class="list-disc list-inside text-sm text-gray-600 space-y-1">
                                <li><span class="font-medium">recipient_name</span> - Name of the recipient</li>
                                <li><span class="font-medium">reference</span> - Your reference for this payment</li>
                                <li><span class="font-medium">provider</span> - Wallet provider code (defaults to auto-detect)</li>
                            </ul>
                        </div>
                        
                        <div>
                            <h4 class="font-medium text-gray-900 mb-1">Supported Wallet Providers</h4>
                            <ul class="list-disc list-inside text-sm text-gray-600 space-y-1">
                                @foreach($walletProviders as $provider)
                                    <li><span class="font-medium">{{ $provider->code }}</span> - {{ $provider->name }}</li>
                                @endforeach
                            </ul>
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

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const fileInput = document.getElementById('file');
        const fileNameDisplay = document.getElementById('file-name');
        const dropZone = fileInput.closest('.border-dashed');
        
        const fileSelectedNotice = document.getElementById('file-selected-notice');
        const validateButton = document.getElementById('validate-button');
        
        // Display file name when selected
        fileInput.addEventListener('change', function() {
            if (this.files.length > 0) {
                const fileName = this.files[0].name;
                const fileSize = (this.files[0].size / 1024 / 1024).toFixed(2);
                fileNameDisplay.textContent = `Selected: ${fileName} (${fileSize} MB)`;
                dropZone.classList.add('border-corporate-primary');
                dropZone.classList.remove('border-gray-300');
                
                // Show the notice and highlight the validate button
                fileSelectedNotice.classList.remove('hidden');
                validateButton.classList.add('animate-pulse');
                validateButton.classList.add('ring-4');
                validateButton.classList.add('ring-corporate-primary');
                validateButton.classList.add('ring-opacity-50');
                
                // Scroll to the validate button
                validateButton.scrollIntoView({ behavior: 'smooth', block: 'center' });
            } else {
                fileNameDisplay.textContent = '';
                fileSelectedNotice.classList.add('hidden');
                dropZone.classList.remove('border-corporate-primary');
                dropZone.classList.add('border-gray-300');
                validateButton.classList.remove('animate-pulse');
                validateButton.classList.remove('ring-4');
                validateButton.classList.remove('ring-corporate-primary');
                validateButton.classList.remove('ring-opacity-50');
            }
        });
        
        // Drag and drop functionality
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
            dropZone.classList.remove('border-gray-300');
        }
        
        function unhighlight() {
            dropZone.classList.remove('border-corporate-primary', 'bg-corporate-primary', 'bg-opacity-5');
            dropZone.classList.add('border-gray-300');
        }
        
        dropZone.addEventListener('drop', handleDrop, false);
        
        function handleDrop(e) {
            const dt = e.dataTransfer;
            const files = dt.files;
            fileInput.files = files;
            
            // Trigger change event
            const event = new Event('change', { bubbles: true });
            fileInput.dispatchEvent(event);
        }
    });
</script>
@endpush
