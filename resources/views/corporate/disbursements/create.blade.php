@extends('corporate.layouts.app')

@section('title', 'Create Bulk Disbursement - ' . config('app.name'))
@section('meta_description', 'Create a new bulk disbursement to multiple mobile wallets')
@section('header_title', 'Create Bulk Disbursement')

@section('content')
    <div class="mb-6">
        <div class="flex items-center mb-2">
            <a href="{{ route('corporate.disbursements.index') }}" class="text-corporate-primary hover:underline">
                <i class="fas fa-arrow-left mr-2"></i> Back to Disbursements
            </a>
        </div>
        <h2 class="text-xl font-bold text-corporate-primary">Create New Bulk Disbursement</h2>
        <p class="text-gray-500">Send payments to multiple recipients in one transaction</p>
    </div>

    <!-- Step Indicator -->
    <div class="bg-white rounded-xl shadow-sm overflow-hidden mb-6">
        <div class="p-6">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <div class="flex items-center">
                        <div class="w-10 h-10 rounded-full bg-corporate-primary text-white flex items-center justify-center font-semibold">1</div>
                        <div class="ml-3">
                            <h3 class="font-medium text-corporate-primary">Upload File</h3>
                            <p class="text-xs text-gray-500">Prepare and upload recipient data</p>
                        </div>
                    </div>
                    <div class="h-1 bg-corporate-primary mt-3"></div>
                </div>
                <div class="flex-1">
                    <div class="flex items-center">
                        <div class="w-10 h-10 rounded-full bg-gray-200 text-gray-500 flex items-center justify-center font-semibold">2</div>
                        <div class="ml-3">
                            <h3 class="font-medium text-gray-500">Validate</h3>
                            <p class="text-xs text-gray-500">Review and fix any errors</p>
                        </div>
                    </div>
                    <div class="h-1 bg-gray-200 mt-3"></div>
                </div>
                <div class="flex-1">
                    <div class="flex items-center">
                        <div class="w-10 h-10 rounded-full bg-gray-200 text-gray-500 flex items-center justify-center font-semibold">3</div>
                        <div class="ml-3">
                            <h3 class="font-medium text-gray-500">Review</h3>
                            <p class="text-xs text-gray-500">Confirm disbursement details</p>
                        </div>
                    </div>
                    <div class="h-1 bg-gray-200 mt-3"></div>
                </div>
                <div class="flex-1">
                    <div class="flex items-center">
                        <div class="w-10 h-10 rounded-full bg-gray-200 text-gray-500 flex items-center justify-center font-semibold">4</div>
                        <div class="ml-3">
                            <h3 class="font-medium text-gray-500">Submit</h3>
                            <p class="text-xs text-gray-500">Process the disbursement</p>
                        </div>
                    </div>
                    <div class="h-1 bg-gray-200 mt-3"></div>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Form -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-corporate-primary mb-4">Disbursement Information</h3>
                    
                    <form action="{{ route('corporate.disbursements.validate') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                        @csrf
                        
                        <!-- Disbursement Name -->
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Disbursement Name <span class="text-corporate-error">*</span></label>
                            <input type="text" id="name" name="name" required class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-corporate-primary focus:border-corporate-primary" placeholder="e.g. April Salaries">
                            <p class="text-xs text-gray-500 mt-1">Give your disbursement a descriptive name for easy reference</p>
                        </div>
                        
                        <!-- Description -->
                        <div>
                            <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Description (Optional)</label>
                            <textarea id="description" name="description" rows="3" class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-corporate-primary focus:border-corporate-primary" placeholder="Add any additional notes or details about this disbursement"></textarea>
                        </div>
                        
                        <!-- File Upload -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Recipient Data File <span class="text-corporate-error">*</span></label>
                            <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-lg">
                                <div class="space-y-1 text-center">
                                    <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48" aria-hidden="true">
                                        <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                    <div class="flex text-sm text-gray-600">
                                        <label for="file-upload" class="relative cursor-pointer bg-white rounded-md font-medium text-corporate-primary hover:text-corporate-accent">
                                            <span>Upload a file</span>
                                            <input id="file-upload" name="file" type="file" class="sr-only" accept=".csv,.xlsx">
                                        </label>
                                        <p class="pl-1">or drag and drop</p>
                                    </div>
                                    <p class="text-xs text-gray-500">CSV or Excel file up to 10MB</p>
                                </div>
                            </div>
                            <div id="file-name" class="mt-2 text-sm text-corporate-primary hidden">
                                <i class="fas fa-file-excel mr-1"></i> <span></span>
                            </div>
                            <p class="text-xs text-gray-500 mt-1">Upload a file containing recipient details and payment amounts</p>
                        </div>
                        
                        <div class="flex justify-end space-x-3 pt-4">
                            <a href="{{ route('corporate.disbursements.index') }}" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                                Cancel
                            </a>
                            <button type="submit" class="px-4 py-2 bg-corporate-primary text-white rounded-lg hover:bg-opacity-90">
                                Continue to Validation
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Right Sidebar -->
        <div class="space-y-6">
            <!-- Template Download -->
            <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-corporate-primary mb-4">Download Template</h3>
                    <p class="text-sm text-gray-600 mb-4">Use our template to ensure your data is formatted correctly. The template includes sample data and column headers.</p>
                    
                    <div class="space-y-3">
                        <a href="{{ route('corporate.disbursements.template', ['format' => 'csv']) }}" class="flex items-center p-3 bg-corporate-primary bg-opacity-5 rounded-lg hover:bg-opacity-10 transition">
                            <div class="w-10 h-10 rounded-full bg-corporate-primary bg-opacity-10 flex items-center justify-center text-corporate-primary mr-3">
                                <i class="fas fa-file-csv"></i>
                            </div>
                            <div>
                                <h4 class="font-medium text-corporate-primary">CSV Template</h4>
                                <p class="text-xs text-gray-500">Download as CSV file</p>
                            </div>
                        </a>
                        <a href="{{ route('corporate.disbursements.template', ['format' => 'xlsx']) }}" class="flex items-center p-3 bg-corporate-primary bg-opacity-5 rounded-lg hover:bg-opacity-10 transition">
                            <div class="w-10 h-10 rounded-full bg-corporate-primary bg-opacity-10 flex items-center justify-center text-corporate-primary mr-3">
                                <i class="fas fa-file-excel"></i>
                            </div>
                            <div>
                                <h4 class="font-medium text-corporate-primary">Excel Template</h4>
                                <p class="text-xs text-gray-500">Download as Excel file</p>
                            </div>
                        </a>
                    </div>
                </div>
            </div>

            <!-- File Format Instructions -->
            <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-corporate-primary mb-4">File Format Instructions</h3>
                    
                    <div class="space-y-4">
                        <div>
                            <h4 class="font-medium text-corporate-primary">Required Columns</h4>
                            <ul class="mt-2 text-sm text-gray-600 list-disc list-inside space-y-1">
                                <li><strong>mobile_number</strong> - 9 digits without country code (e.g. 977123456)</li>
                                <li><strong>amount</strong> - Payment amount in ZMW (e.g. 500.00)</li>
                                <li><strong>provider</strong> - Mobile provider code (MTN, AIRTEL, ZAMTEL)</li>
                            </ul>
                        </div>
                        
                        <div>
                            <h4 class="font-medium text-corporate-primary">Optional Columns</h4>
                            <ul class="mt-2 text-sm text-gray-600 list-disc list-inside space-y-1">
                                <li><strong>recipient_name</strong> - Name of the recipient</li>
                                <li><strong>reference</strong> - Your reference for this payment</li>
                                <li><strong>description</strong> - Additional payment details</li>
                            </ul>
                        </div>
                        
                        <div>
                            <h4 class="font-medium text-corporate-primary">Validation Rules</h4>
                            <ul class="mt-2 text-sm text-gray-600 list-disc list-inside space-y-1">
                                <li>Mobile numbers must be 9 digits</li>
                                <li>Amount must be between K10 and K50,000 per transaction</li>
                                <li>Provider must be one of: MTN, AIRTEL, ZAMTEL</li>
                                <li>Maximum 1,000 recipients per file</li>
                            </ul>
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
        const fileUpload = document.getElementById('file-upload');
        const fileName = document.getElementById('file-name');
        
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
    });
</script>
@endpush
