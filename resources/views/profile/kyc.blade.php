@extends('layouts.app')

@section('title', 'KYC Verification - ' . config('app.name'))
@section('meta_description', 'Complete your KYC verification to unlock higher transaction limits')
@section('header_title', 'KYC Verification')

@section('content')
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Sidebar Navigation -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                <div class="p-6 border-b border-gray-100">
                    <h2 class="text-xl font-bold text-dark">Account Settings</h2>
                </div>
                <div class="p-4">
                    <nav class="space-y-1">
                        <a href="{{ route('profile.index') }}" class="flex items-center px-4 py-3 text-dark hover:bg-gray-50 rounded-lg transition-colors">
                            <i class="fas fa-user w-6 text-gray-500"></i>
                            <span>Personal Information</span>
                        </a>
                        <a href="{{ route('profile.security') }}" class="flex items-center px-4 py-3 text-dark hover:bg-gray-50 rounded-lg transition-colors">
                            <i class="fas fa-shield-alt w-6 text-gray-500"></i>
                            <span>Security Settings</span>
                        </a>
                        <a href="{{ route('profile.kyc') }}" class="flex items-center px-4 py-3 text-dark bg-primary bg-opacity-10 rounded-lg">
                            <i class="fas fa-id-card w-6 text-primary"></i>
                            <span class="font-medium">KYC Verification</span>
                            @if(auth()->user()->verification_level === 'basic')
                                <span class="ml-auto inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-warning text-dark">
                                    Required
                                </span>
                            @endif
                        </a>
                    </nav>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="lg:col-span-2">
            <!-- Verification Status -->
            <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                <div class="p-6 border-b border-gray-100">
                    <div class="flex flex-col md:flex-row md:items-center md:justify-between">
                        <div>
                            <h2 class="text-xl font-bold text-dark">Verification Status</h2>
                            <p class="text-gray-600 mt-1">Complete verification to unlock higher transaction limits</p>
                        </div>
                        <div class="mt-4 md:mt-0">
                            @if(auth()->user()->verification_level === 'verified')
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-success text-white">
                                    <i class="fas fa-check-circle mr-1"></i> Verified
                                </span>
                            @else
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-warning text-dark">
                                    <i class="fas fa-exclamation-circle mr-1"></i> Basic
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="p-6">
                    <div class="relative">
                        <div class="overflow-hidden h-2 text-xs flex rounded bg-gray-200">
                            @php
                                // Calculate verification progress
                                $progress = 0;

                                // Email verification - 25%
                                if (auth()->user()->is_email_verified) {
                                    $progress += 25;
                                }

                                // Phone verification - 25%
                                if (auth()->user()->is_phone_verified) {
                                    $progress += 25;
                                }

                                // KYC documents - 50%
                                $kycProgress = 0;
                                
                                // National ID or Passport - 25%
                                if (isset($documents) && ($documents->where('document_type', 'national_id')->where('status', 'approved')->count() > 0 ||
                                    $documents->where('document_type', 'passport')->where('status', 'approved')->count() > 0 ||
                                    $documents->where('document_type', 'drivers_license')->where('status', 'approved')->count() > 0)) {
                                    $kycProgress += 25;
                                }

                                // Proof of address - 25%
                                if (isset($documents) && $documents->where('document_type', 'proof_of_address')->where('status', 'approved')->count() > 0) {
                                    $kycProgress += 25;
                                }

                                $progress += $kycProgress;
                            @endphp

                            <div style="width: {{ $progress }}%" class="shadow-none flex flex-col text-center whitespace-nowrap text-white justify-center {{ $progress < 50 ? 'bg-warning' : ($progress < 100 ? 'bg-primary' : 'bg-success') }}"></div>
                        </div>
                    </div>
                    <div class="mt-2 text-right text-sm text-gray-600">{{ $progress }}% Complete</div>

                    <div class="mt-6 bg-light rounded-lg p-4">
                        <div class="flex items-start">
                            <div class="flex-shrink-0 mt-1">
                                <i class="fas fa-info-circle text-primary"></i>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-dark">Transaction Limits</h3>
                                <div class="mt-1 text-sm text-gray-600">
                                    @if(auth()->user()->verification_level === 'verified')
                                        <p>Your account is fully verified. You can now enjoy higher transaction limits:</p>
                                        <ul class="list-disc pl-5 mt-1 space-y-1">
                                            <li>Up to K5,000 per transaction</li>
                                            <li>Up to K10,000 daily</li>
                                            <li>Up to K50,000 monthly</li>
                                        </ul>
                                    @else
                                        <p>Your account has basic verification. Current transaction limits:</p>
                                        <ul class="list-disc pl-5 mt-1 space-y-1">
                                            <li>Up to K1,000 per transaction</li>
                                            <li>Up to K2,000 daily</li>
                                            <li>Up to K5,000 monthly</li>
                                        </ul>
                                        <p class="mt-2">Complete verification to increase your limits.</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Upload Documents -->
            <div class="bg-white rounded-xl shadow-sm overflow-hidden mt-6">
                <div class="p-6 border-b border-gray-100">
                    <h2 class="text-xl font-bold text-dark">Upload Verification Documents</h2>
                    <p class="text-gray-600 mt-1">Please provide clear, legible documents for faster verification</p>
                </div>
                <div class="p-6">
                    @php
                        $hasPendingDocuments = isset($documents) && $documents->where('status', 'pending')->count() > 0;
                    @endphp

                    @if($hasPendingDocuments)
                        <div class="bg-warning bg-opacity-10 text-warning rounded-lg p-4">
                            <div class="flex items-start">
                                <div class="flex-shrink-0 mt-1">
                                    <i class="fas fa-exclamation-circle"></i>
                                </div>
                                <div class="ml-3">
                                    <h3 class="text-sm font-medium">Document Under Review</h3>
                                    <p class="mt-1 text-sm">
                                        You have documents pending review. You cannot submit new documents until the current review is complete.
                                        Please check back later or contact support if you have any questions.
                                    </p>
                                </div>
                            </div>
                        </div>
                    @else
                    <form action="{{ route('profile.upload-kyc') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="space-y-6">
                            <!-- Document Type -->
                            <div>
                                <label for="document_type" class="block text-sm font-medium text-gray-700 mb-1">Document Type</label>
                                <select name="document_type" id="document_type" 
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-primary focus:border-primary" required>
                                    <option value="">Select Document Type</option>
                                    <option value="national_id">National ID</option>
                                    <option value="passport">Passport</option>
                                    <option value="drivers_license">Driver's License</option>
                                    <option value="proof_of_address">Proof of Address</option>
                                    <option value="selfie">Selfie with ID</option>
                                </select>
                                @error('document_type')
                                    <p class="mt-1 text-sm text-error">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Document Number -->
                            <div id="document_number_container">
                                <label for="document_number" class="block text-sm font-medium text-gray-700 mb-1">Document Number</label>
                                <input type="text" name="document_number" id="document_number" 
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-primary focus:border-primary">
                                @error('document_number')
                                    <p class="mt-1 text-sm text-error">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Expiry Date -->
                            <div id="expiry_date_container">
                                <label for="expiry_date" class="block text-sm font-medium text-gray-700 mb-1">Expiry Date</label>
                                <input type="date" name="expiry_date" id="expiry_date" 
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-primary focus:border-primary">
                                @error('expiry_date')
                                    <p class="mt-1 text-sm text-error">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Document File -->
                            <div>
                                <label for="document_file" class="block text-sm font-medium text-gray-700 mb-1">Upload Document</label>
                                <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-lg">
                                    <div class="space-y-1 text-center">
                                        <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48" aria-hidden="true">
                                            <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                        </svg>
                                        <div class="flex text-sm text-gray-600">
                                            <label for="document_file" class="relative cursor-pointer bg-white rounded-md font-medium text-primary hover:text-primary-dark focus-within:outline-none">
                                                <span>Upload a file</span>
                                                <input id="document_file" name="document_file" type="file" class="sr-only" accept=".jpg,.jpeg,.png,.pdf">
                                            </label>
                                            <p class="pl-1">or drag and drop</p>
                                        </div>
                                        <p class="text-xs text-gray-500">
                                            JPG, PNG, PDF up to 5MB
                                        </p>
                                    </div>
                                </div>
                                <div id="file-name" class="mt-2 text-sm text-gray-600"></div>
                                @error('document_file')
                                    <p class="mt-1 text-sm text-error">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="mt-8 flex justify-end">
                            <button type="submit" class="px-6 py-3 bg-primary text-white rounded-lg hover:bg-opacity-90 transition-colors">
                                Upload Document
                            </button>
                        </div>
                    </form>
                    @endif
                </div>
            </div>

            <!-- Uploaded Documents -->
            <div class="bg-white rounded-xl shadow-sm overflow-hidden mt-6">
                <div class="p-6 border-b border-gray-100">
                    <h2 class="text-xl font-bold text-dark">Uploaded Documents</h2>
                    <p class="text-gray-600 mt-1">Status of your verification documents</p>
                </div>
                <div class="p-6">
                    @if($documents->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Document Type
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Submitted Date
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Status
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Notes
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($documents as $document)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm font-medium text-gray-900">{{ $document->getDocumentTypeDisplayAttribute() }}</div>
                                                @if($document->document_number)
                                                    <div class="text-sm text-gray-500">{{ $document->document_number }}</div>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm text-gray-900">{{ $document->created_at->format('M d, Y') }}</div>
                                                <div class="text-sm text-gray-500">{{ $document->created_at->format('h:i A') }}</div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                @if($document->status === 'approved')
                                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-success bg-opacity-10 text-success">
                                                        Approved
                                                    </span>
                                                @elseif($document->status === 'rejected')
                                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-error bg-opacity-10 text-error">
                                                        Rejected
                                                    </span>
                                                @else
                                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-warning bg-opacity-10 text-warning">
                                                        Pending
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ $document->review_notes ?? 'No notes' }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-8">
                            <div class="text-gray-400 mb-2">
                                <i class="fas fa-file-upload text-4xl"></i>
                            </div>
                            <h3 class="text-lg font-medium text-gray-900">No documents uploaded yet</h3>
                            <p class="mt-1 text-sm text-gray-500">Upload your verification documents to increase your transaction limits.</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Verification Guidelines -->
            <div class="bg-white rounded-xl shadow-sm overflow-hidden mt-6">
                <div class="p-6 border-b border-gray-100">
                    <h2 class="text-xl font-bold text-dark">Verification Guidelines</h2>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="bg-light rounded-lg p-4">
                            <h3 class="font-medium text-dark mb-2">ID Document Requirements</h3>
                            <ul class="text-sm text-gray-600 space-y-2">
                                <li class="flex items-start">
                                    <i class="fas fa-check-circle text-success mt-1 mr-2"></i>
                                    <span>Clear, legible image of the entire document</span>
                                </li>
                                <li class="flex items-start">
                                    <i class="fas fa-check-circle text-success mt-1 mr-2"></i>
                                    <span>All four corners must be visible</span>
                                </li>
                                <li class="flex items-start">
                                    <i class="fas fa-check-circle text-success mt-1 mr-2"></i>
                                    <span>Document must be valid and not expired</span>
                                </li>
                                <li class="flex items-start">
                                    <i class="fas fa-check-circle text-success mt-1 mr-2"></i>
                                    <span>Both sides of the ID if applicable</span>
                                </li>
                            </ul>
                        </div>

                        <div class="bg-light rounded-lg p-4">
                            <h3 class="font-medium text-dark mb-2">Proof of Address Requirements</h3>
                            <ul class="text-sm text-gray-600 space-y-2">
                                <li class="flex items-start">
                                    <i class="fas fa-check-circle text-success mt-1 mr-2"></i>
                                    <span>Utility bill, bank statement, or official letter</span>
                                </li>
                                <li class="flex items-start">
                                    <i class="fas fa-check-circle text-success mt-1 mr-2"></i>
                                    <span>Must be less than 3 months old</span>
                                </li>
                                <li class="flex items-start">
                                    <i class="fas fa-check-circle text-success mt-1 mr-2"></i>
                                    <span>Must show your full name and address</span>
                                </li>
                                <li class="flex items-start">
                                    <i class="fas fa-check-circle text-success mt-1 mr-2"></i>
                                    <span>Clear, legible image of the entire document</span>
                                </li>
                            </ul>
                        </div>
                    </div>

                    <div class="mt-6 bg-light rounded-lg p-4">
                        <div class="flex items-start">
                            <div class="flex-shrink-0 mt-1">
                                <i class="fas fa-info-circle text-primary"></i>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-dark">Verification Process</h3>
                                <p class="mt-1 text-sm text-gray-600">
                                    Document verification typically takes 1-2 business days. You will be notified once your documents have been reviewed. If your documents are rejected, you will be provided with a reason and can resubmit new documents.
                                </p>
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
    document.addEventListener('DOMContentLoaded', function() {
        const documentTypeSelect = document.getElementById('document_type');
        const documentNumberContainer = document.getElementById('document_number_container');
        const expiryDateContainer = document.getElementById('expiry_date_container');
        const fileInput = document.getElementById('document_file');
        const fileNameDisplay = document.getElementById('file-name');

        // Handle document type change
        documentTypeSelect.addEventListener('change', function() {
            const selectedType = this.value;
            
            // Show/hide document number field based on document type
            if (selectedType === 'proof_of_address' || selectedType === 'selfie') {
                documentNumberContainer.style.display = 'none';
                expiryDateContainer.style.display = 'none';
            } else {
                documentNumberContainer.style.display = 'block';
                expiryDateContainer.style.display = 'block';
            }
        });

        // Display selected file name
        fileInput.addEventListener('change', function() {
            if (this.files && this.files[0]) {
                const fileName = this.files[0].name;
                const fileSize = (this.files[0].size / 1024 / 1024).toFixed(2); // Convert to MB
                fileNameDisplay.textContent = `Selected file: ${fileName} (${fileSize} MB)`;
            } else {
                fileNameDisplay.textContent = '';
            }
        });

        // Initialize based on current selection
        if (documentTypeSelect.value) {
            documentTypeSelect.dispatchEvent(new Event('change'));
        } else {
            // Default state
            documentNumberContainer.style.display = 'block';
            expiryDateContainer.style.display = 'block';
        }
    });
</script>
@endpush
