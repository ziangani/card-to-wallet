@extends('layouts.app')

@section('title', 'Support - ' . config('app.name'))
@section('meta_description', 'Get help and support for your card-to-wallet transfers')
@section('header_title', 'Help & Support')

@section('content')
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Sidebar -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                <div class="p-6 border-b border-gray-100">
                    <h2 class="text-xl font-bold text-dark">Support Options</h2>
                </div>
                <div class="p-4">
                    <nav class="space-y-1">
                        <a href="{{ route('support') }}" class="flex items-center px-4 py-3 text-dark bg-primary bg-opacity-10 rounded-lg">
                            <i class="fas fa-headset w-6 text-primary"></i>
                            <span class="font-medium">Contact Support</span>
                        </a>
                        <a href="{{ route('faq') }}" class="flex items-center px-4 py-3 text-dark hover:bg-gray-50 rounded-lg transition-colors">
                            <i class="fas fa-question-circle w-6 text-gray-500"></i>
                            <span>Frequently Asked Questions</span>
                        </a>
                    </nav>
                </div>
            </div>

            <!-- Contact Information -->
            <div class="bg-white rounded-xl shadow-sm overflow-hidden mt-6">
                <div class="p-6 border-b border-gray-100">
                    <h2 class="text-xl font-bold text-dark">Contact Information</h2>
                </div>
                <div class="p-6">
                    <ul class="space-y-4">
                        <li class="flex items-start">
                            <div class="flex-shrink-0 mt-1">
                                <i class="fas fa-phone text-primary"></i>
                            </div>
                            <div class="ml-3">
                                <h3 class="font-medium text-dark">Phone Support</h3>
                                <p class="text-gray-600 mt-1">+260 97 1234567</p>
                                <p class="text-sm text-gray-500 mt-1">Monday to Friday, 8:00 AM - 5:00 PM</p>
                            </div>
                        </li>
                        <li class="flex items-start">
                            <div class="flex-shrink-0 mt-1">
                                <i class="fas fa-envelope text-primary"></i>
                            </div>
                            <div class="ml-3">
                                <h3 class="font-medium text-dark">Email Support</h3>
                                <p class="text-gray-600 mt-1">support@cardtowallet.com</p>
                                <p class="text-sm text-gray-500 mt-1">We aim to respond within 24 hours</p>
                            </div>
                        </li>
                        <li class="flex items-start">
                            <div class="flex-shrink-0 mt-1">
                                <i class="fas fa-map-marker-alt text-primary"></i>
                            </div>
                            <div class="ml-3">
                                <h3 class="font-medium text-dark">Office Address</h3>
                                <p class="text-gray-600 mt-1">123 Cairo Road, Lusaka, Zambia</p>
                                <p class="text-sm text-gray-500 mt-1">Visit us during business hours</p>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                <div class="p-6 border-b border-gray-100">
                    <h2 class="text-xl font-bold text-dark">Contact Support</h2>
                    <p class="text-gray-600 mt-1">Fill out the form below to get in touch with our support team</p>
                </div>
                <div class="p-6">
                    <form action="{{ route('support.submit') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="space-y-6">
                            <!-- Subject -->
                            <div>
                                <label for="subject" class="block text-sm font-medium text-gray-700 mb-1">Subject</label>
                                <input type="text" name="subject" id="subject" value="{{ old('subject') }}" 
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-primary focus:border-primary" required>
                                @error('subject')
                                    <p class="mt-1 text-sm text-error">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Transaction ID (Optional) -->
                            <div>
                                <label for="transaction_id" class="block text-sm font-medium text-gray-700 mb-1">
                                    Transaction ID (Optional)
                                </label>
                                <input type="text" name="transaction_id" id="transaction_id" value="{{ old('transaction_id') }}" 
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-primary focus:border-primary">
                                <p class="mt-1 text-sm text-gray-500">If your inquiry is about a specific transaction, please provide the ID</p>
                                @error('transaction_id')
                                    <p class="mt-1 text-sm text-error">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Message -->
                            <div>
                                <label for="message" class="block text-sm font-medium text-gray-700 mb-1">Message</label>
                                <textarea name="message" id="message" rows="6" 
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-primary focus:border-primary" required>{{ old('message') }}</textarea>
                                @error('message')
                                    <p class="mt-1 text-sm text-error">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Attachment (Optional) -->
                            <div>
                                <label for="attachment" class="block text-sm font-medium text-gray-700 mb-1">
                                    Attachment (Optional)
                                </label>
                                <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-lg">
                                    <div class="space-y-1 text-center">
                                        <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48" aria-hidden="true">
                                            <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                        </svg>
                                        <div class="flex text-sm text-gray-600">
                                            <label for="attachment" class="relative cursor-pointer bg-white rounded-md font-medium text-primary hover:text-primary-dark focus-within:outline-none">
                                                <span>Upload a file</span>
                                                <input id="attachment" name="attachment" type="file" class="sr-only" accept=".jpg,.jpeg,.png,.pdf">
                                            </label>
                                            <p class="pl-1">or drag and drop</p>
                                        </div>
                                        <p class="text-xs text-gray-500">
                                            JPG, PNG, PDF up to 5MB
                                        </p>
                                    </div>
                                </div>
                                <div id="file-name" class="mt-2 text-sm text-gray-600"></div>
                                @error('attachment')
                                    <p class="mt-1 text-sm text-error">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="mt-8 flex justify-end">
                            <button type="submit" class="px-6 py-3 bg-primary text-white rounded-lg hover:bg-opacity-90 transition-colors">
                                Submit Request
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Common Issues -->
            <div class="bg-white rounded-xl shadow-sm overflow-hidden mt-6">
                <div class="p-6 border-b border-gray-100">
                    <h2 class="text-xl font-bold text-dark">Common Issues</h2>
                </div>
                <div class="p-6">
                    <div class="space-y-4">
                        <div class="bg-light rounded-lg p-4">
                            <h3 class="font-medium text-dark">Transaction Failed</h3>
                            <p class="text-gray-600 mt-1">If your transaction failed, please check your transaction history for details. Most failed transactions are automatically refunded within 24-48 hours.</p>
                        </div>
                        <div class="bg-light rounded-lg p-4">
                            <h3 class="font-medium text-dark">Wallet Not Credited</h3>
                            <p class="text-gray-600 mt-1">If your card was charged but the wallet was not credited, please provide the transaction ID when contacting support. Our team will investigate and resolve the issue.</p>
                        </div>
                        <div class="bg-light rounded-lg p-4">
                            <h3 class="font-medium text-dark">Verification Issues</h3>
                            <p class="text-gray-600 mt-1">For KYC verification issues, please ensure your documents are clear, valid, and match the information provided during registration.</p>
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
        const fileInput = document.getElementById('attachment');
        const fileNameDisplay = document.getElementById('file-name');

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
    });
</script>
@endpush
