@extends('layouts.auth')

@section('title', 'Corporate Registration - ' . config('app.name'))
@section('meta_description', 'Register your business to start funding mobile wallets with bulk disbursements')

@section('header_nav')
    <a href="{{ url('/login') }}" class="bg-primary text-white font-medium px-4 py-2 rounded-lg hover:bg-opacity-90 transition duration-300 shadow-button">Login</a>
@endsection

@section('content')
    <style>
        .bg-corporate-primary {
            background: linear-gradient(135deg, #007751 0%, #005a3d 50%, #007751 100%);
        }
    </style>
    <div class="mb-6">
        <a href="{{ url('/register') }}" class="inline-flex items-center text-primary hover:underline">
            <i class="fas fa-arrow-left mr-2"></i> Back to Individual Registration
        </a>
    </div>

    <div class="bg-white rounded-xl shadow-card overflow-hidden">
        <div class="md:flex">
            <!-- Left Side - Info -->
            <div class="md:w-1/3 bg-corporate-primary text-white p-8 flex flex-col justify-between">
                <div>
                    <h2 class="text-2xl font-bold mb-4">Corporate Registration</h2>
                    <p class="mb-6">Create a corporate account to access bulk disbursements and preferential rates.</p>

                    <div class="space-y-4 mt-8">
                        <div class="flex items-start mb-5">
                            <div class="flex-shrink-0 w-8 h-8 rounded-full bg-white bg-opacity-20 flex items-center justify-center mr-3">
                                <i class="fas fa-users text-white"></i>
                            </div>
                            <div class="mb-5">
                                <h3 class="font-semibold">Multi-User Access</h3>
                                <p class="text-sm text-white text-opacity-90">Add team members with different roles</p>
                            </div>
                        </div>

                        <div class="flex items-start mb-5">
                            <div class="flex-shrink-0 w-8 h-8 rounded-full bg-white bg-opacity-20 flex items-center justify-center mr-3">
                                <i class="fas fa-percentage text-white"></i>
                            </div>
                            <div class="mb-5">
                                <h3 class="font-semibold">Preferential Rates</h3>
                                <p class="text-sm text-white text-opacity-90">Volume-based discounts for businesses</p>
                            </div>
                        </div>

                        <div class="flex items-start mb-5">
                            <div class="flex-shrink-0 w-8 h-8 rounded-full bg-white bg-opacity-20 flex items-center justify-center mr-3">
                                <i class="fas fa-file-invoice text-white"></i>
                            </div>
                            <div class="mb-5">
                                <h3 class="font-semibold">Bulk Disbursements</h3>
                                <p class="text-sm text-white text-opacity-90">Send funds to multiple recipients at once</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-8 text-sm text-white text-opacity-90">
                    Already have an account? <a href="{{ url('/login') }}" class="text-white underline font-medium hover:text-secondary transition duration-300">Login here</a>
                </div>
            </div>

            <!-- Right Side - Form -->
            <div class="md:w-2/3 p-8">
                <h1 class="text-2xl font-bold text-dark mb-6">Create Corporate Account</h1>

                <!-- Step Indicator -->
                <div class="mb-8">
                    <div class="flex items-center justify-between">
                        <div class="step active flex flex-col items-center">
                            <div class="w-8 h-8 rounded-full bg-primary text-white flex items-center justify-center mb-1">1</div>
                            <span class="text-xs">Personal Info</span>
                        </div>
                        <div class="h-0.5 flex-1 bg-gray-200 mx-2 step-line"></div>
                        <div class="step flex flex-col items-center">
                            <div class="w-8 h-8 rounded-full bg-gray-200 text-gray-600 flex items-center justify-center mb-1">2</div>
                            <span class="text-xs">Company Info</span>
                        </div>
                        <div class="h-0.5 flex-1 bg-gray-200 mx-2 step-line"></div>
                        <div class="step flex flex-col items-center">
                            <div class="w-8 h-8 rounded-full bg-gray-200 text-gray-600 flex items-center justify-center mb-1">3</div>
                            <span class="text-xs">Documents</span>
                        </div>
                        <div class="h-0.5 flex-1 bg-gray-200 mx-2 step-line"></div>
                        <div class="step flex flex-col items-center">
                            <div class="w-8 h-8 rounded-full bg-gray-200 text-gray-600 flex items-center justify-center mb-1">4</div>
                            <span class="text-xs">Review</span>
                        </div>
                    </div>
                </div>

                <!-- Corporate Registration Form -->
                <form id="corporateRegisterForm" class="space-y-6">
                    @csrf
                    <input type="hidden" name="account_type" value="corporate">
                    <input type="hidden" name="user_type" value="corporate">
                    <input type="hidden" name="current_step" value="1">

                    <!-- Step 1: Personal Information -->
                    <div id="step-1" class="registration-step">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- First Name -->
                            <div>
                                <label for="first_name" class="block text-sm font-medium text-gray-700 mb-1">First Name</label>
                                <input type="text" id="first_name" name="first_name" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary transition duration-200" required>
                                @error('first_name')
                                <p class="text-error text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Last Name -->
                            <div>
                                <label for="last_name" class="block text-sm font-medium text-gray-700 mb-1">Last Name</label>
                                <input type="text" id="last_name" name="last_name" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary transition duration-200" required>
                                @error('last_name')
                                <p class="text-error text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Email and Phone Number -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                            <div>
                                <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
                                <input type="email" id="email" name="email" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary transition duration-200" required>
                                @error('email')
                                <p class="text-error text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Phone Number -->
                            <div>
                                <label for="phone_number" class="block text-sm font-medium text-gray-700 mb-1">Phone Number</label>
                                <input type="text" id="phone_number" name="phone_number" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary transition duration-200" placeholder="+26097XXXXXXX" required>
                                <p class="text-xs text-gray-500 mt-1">Mobile number including the country code.</p>
                                @error('phone_number')
                                <p class="text-error text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Password Fields -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                            <!-- Password -->
                            <div>
                                <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                                <div class="relative">
                                    <input type="password" id="password" name="password" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary transition duration-200" required>
                                    <button type="button" class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-500 hover:text-gray-700 toggle-password transition duration-200">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                                @error('password')
                                <p class="text-error text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Confirm Password -->
                            <div>
                                <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">Confirm Password</label>
                                <div class="relative">
                                    <input type="password" id="password_confirmation" name="password_confirmation" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary transition duration-200" required>
                                    <button type="button" class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-500 hover:text-gray-700 toggle-password transition duration-200">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Password Strength Indicator -->
                        <div class="password-strength hidden mt-6">
                            <div class="flex space-x-1 mb-1">
                                <div class="h-1 w-1/4 rounded bg-gray-200 strength-indicator"></div>
                                <div class="h-1 w-1/4 rounded bg-gray-200 strength-indicator"></div>
                                <div class="h-1 w-1/4 rounded bg-gray-200 strength-indicator"></div>
                                <div class="h-1 w-1/4 rounded bg-gray-200 strength-indicator"></div>
                            </div>
                            <p class="text-xs text-gray-500 strength-text">Password strength: <span>Weak</span></p>
                        </div>

                        <!-- Next Button -->
                        <div class="mt-6">
                            <button type="button" id="step1Next" class="w-full bg-primary text-white py-3 px-4 rounded-lg hover:bg-opacity-90 transition duration-300 font-medium shadow-button">
                                Next: Company Information
                            </button>
                        </div>
                    </div>

                    <!-- Step 2: Company Information -->
                    <div id="step-2" class="registration-step hidden">
                        <!-- Company Name and Registration Number -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="company_name" class="block text-sm font-medium text-gray-700 mb-1">Company Name</label>
                                <input type="text" id="company_name" name="company_name" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary transition duration-200" required>
                                @error('company_name')
                                <p class="text-error text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Registration Number -->
                            <div>
                                <label for="registration_number" class="block text-sm font-medium text-gray-700 mb-1">Registration Number</label>
                                <input type="text" id="registration_number" name="registration_number" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary transition duration-200" required>
                                @error('registration_number')
                                <p class="text-error text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Tax ID and Industry -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                            <div>
                                <label for="tax_id" class="block text-sm font-medium text-gray-700 mb-1">Tax ID (TPIN)</label>
                                <input type="text" id="tax_id" name="tax_id" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary transition duration-200">
                                @error('tax_id')
                                <p class="text-error text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Industry -->
                            <div>
                                <label for="industry" class="block text-sm font-medium text-gray-700 mb-1">Industry</label>
                                <select id="industry" name="industry" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary transition duration-200">
                                    <option value="">Select Industry</option>
                                    <option value="Agriculture">Agriculture</option>
                                    <option value="Banking & Finance">Banking & Finance</option>
                                    <option value="Construction">Construction</option>
                                    <option value="Education">Education</option>
                                    <option value="Energy">Energy</option>
                                    <option value="Healthcare">Healthcare</option>
                                    <option value="Hospitality">Hospitality</option>
                                    <option value="Information Technology">Information Technology</option>
                                    <option value="Manufacturing">Manufacturing</option>
                                    <option value="Mining">Mining</option>
                                    <option value="Retail">Retail</option>
                                    <option value="Telecommunications">Telecommunications</option>
                                    <option value="Transportation">Transportation</option>
                                    <option value="Other">Other</option>
                                </select>
                                @error('industry')
                                <p class="text-error text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Company Address -->
                        <div class="mt-6">
                            <label for="company_address" class="block text-sm font-medium text-gray-700 mb-1">Company Address</label>
                            <input type="text" id="company_address" name="company_address" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary transition duration-200" required>
                            @error('company_address')
                            <p class="text-error text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- City and Country -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                            <div>
                                <label for="company_city" class="block text-sm font-medium text-gray-700 mb-1">City</label>
                                <input type="text" id="company_city" name="company_city" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary transition duration-200" required>
                                @error('company_city')
                                <p class="text-error text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="company_country" class="block text-sm font-medium text-gray-700 mb-1">Country</label>
                                <input type="text" id="company_country" name="company_country" value="Zambia" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary transition duration-200" readonly>
                            </div>
                        </div>

                        <!-- Company Phone and Email -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                            <div>
                                <label for="company_phone" class="block text-sm font-medium text-gray-700 mb-1">Company Phone</label>
                                <input type="text" id="company_phone" name="company_phone" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary transition duration-200" required>
                                @error('company_phone')
                                <p class="text-error text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="company_email" class="block text-sm font-medium text-gray-700 mb-1">Company Email</label>
                                <input type="email" id="company_email" name="company_email" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary transition duration-200" required>
                                @error('company_email')
                                <p class="text-error text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Company Website -->
                        <div class="mt-6">
                            <label for="company_website" class="block text-sm font-medium text-gray-700 mb-1">Company Website (Optional)</label>
                            <input type="url" id="company_website" name="company_website" placeholder="https://example.com" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary transition duration-200">
                            @error('company_website')
                            <p class="text-error text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Navigation Buttons -->
                        <div class="flex justify-between mt-6">
                            <button type="button" id="step2Back" class="bg-white text-primary border border-primary py-3 px-4 rounded-lg hover:bg-gray-50 transition duration-300 font-medium shadow-sm w-5/12">
                                Back
                            </button>
                            <button type="button" id="step2Next" class="bg-primary text-white py-3 px-4 rounded-lg hover:bg-opacity-90 transition duration-300 font-medium shadow-button w-5/12">
                                Next: Documents
                            </button>
                        </div>
                    </div>

                    <!-- Step 3: Document Upload -->
                    <div id="step-3" class="registration-step hidden">
                        <p class="text-sm text-gray-600 mb-6">Please upload the following documents to verify your company. Accepted formats: PDF, JPG, PNG (Max 5MB each)</p>

                        <!-- Certificate of Incorporation -->
                        <div class="mb-6">
                            <label for="certificate_of_incorporation" class="block text-sm font-medium text-gray-700 mb-1">Certificate of Incorporation</label>
                            <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-lg">
                                <div class="space-y-1 text-center">
                                    <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48" aria-hidden="true">
                                        <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                    <div class="flex text-sm text-gray-600">
                                        <label for="certificate_file" class="relative cursor-pointer bg-white rounded-md font-medium text-primary hover:text-primary-dark focus-within:outline-none">
                                            <span>Upload a file</span>
                                            <input id="certificate_file" name="certificate_file" type="file" class="sr-only" accept=".pdf,.jpg,.jpeg,.png" required>
                                        </label>
                                        <p class="pl-1">or drag and drop</p>
                                    </div>
                                    <p class="text-xs text-gray-500">PDF, JPG, PNG up to 5MB</p>
                                </div>
                            </div>
                            <div id="certificate_file_name" class="mt-2 text-sm text-gray-500 hidden">
                                Selected file: <span class="font-medium"></span>
                            </div>
                            @error('certificate_file')
                            <p class="text-error text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Tax Clearance -->
                        <div class="mb-6">
                            <label for="tax_clearance" class="block text-sm font-medium text-gray-700 mb-1">Tax Clearance (Optional)</label>
                            <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-lg">
                                <div class="space-y-1 text-center">
                                    <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48" aria-hidden="true">
                                        <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                    <div class="flex text-sm text-gray-600">
                                        <label for="tax_clearance_file" class="relative cursor-pointer bg-white rounded-md font-medium text-primary hover:text-primary-dark focus-within:outline-none">
                                            <span>Upload a file</span>
                                            <input id="tax_clearance_file" name="tax_clearance_file" type="file" class="sr-only" accept=".pdf,.jpg,.jpeg,.png">
                                        </label>
                                        <p class="pl-1">or drag and drop</p>
                                    </div>
                                    <p class="text-xs text-gray-500">PDF, JPG, PNG up to 5MB</p>
                                </div>
                            </div>
                            <div id="tax_clearance_file_name" class="mt-2 text-sm text-gray-500 hidden">
                                Selected file: <span class="font-medium"></span>
                            </div>
                            @error('tax_clearance_file')
                            <p class="text-error text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Business License -->
                        <div class="mb-6">
                            <label for="business_license" class="block text-sm font-medium text-gray-700 mb-1">Business License</label>
                            <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-lg">
                                <div class="space-y-1 text-center">
                                    <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48" aria-hidden="true">
                                        <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                    <div class="flex text-sm text-gray-600">
                                        <label for="business_license_file" class="relative cursor-pointer bg-white rounded-md font-medium text-primary hover:text-primary-dark focus-within:outline-none">
                                            <span>Upload a file</span>
                                            <input id="business_license_file" name="business_license_file" type="file" class="sr-only" accept=".pdf,.jpg,.jpeg,.png" required>
                                        </label>
                                        <p class="pl-1">or drag and drop</p>
                                    </div>
                                    <p class="text-xs text-gray-500">PDF, JPG, PNG up to 5MB</p>
                                </div>
                            </div>
                            <div id="business_license_file_name" class="mt-2 text-sm text-gray-500 hidden">
                                Selected file: <span class="font-medium"></span>
                            </div>
                            @error('business_license_file')
                            <p class="text-error text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Director ID -->
                        <div class="mb-6">
                            <label for="director_id" class="block text-sm font-medium text-gray-700 mb-1">Director ID/Passport</label>
                            <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-lg">
                                <div class="space-y-1 text-center">
                                    <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48" aria-hidden="true">
                                        <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                    <div class="flex text-sm text-gray-600">
                                        <label for="director_id_file" class="relative cursor-pointer bg-white rounded-md font-medium text-primary hover:text-primary-dark focus-within:outline-none">
                                            <span>Upload a file</span>
                                            <input id="director_id_file" name="director_id_file" type="file" class="sr-only" accept=".pdf,.jpg,.jpeg,.png" required>
                                        </label>
                                        <p class="pl-1">or drag and drop</p>
                                    </div>
                                    <p class="text-xs text-gray-500">PDF, JPG, PNG up to 5MB</p>
                                </div>
                            </div>
                            <div id="director_id_file_name" class="mt-2 text-sm text-gray-500 hidden">
                                Selected file: <span class="font-medium"></span>
                            </div>
                            @error('director_id_file')
                            <p class="text-error text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Navigation Buttons -->
                        <div class="flex justify-between mt-6">
                            <button type="button" id="step3Back" class="bg-white text-primary border border-primary py-3 px-4 rounded-lg hover:bg-gray-50 transition duration-300 font-medium shadow-sm w-5/12">
                                Back
                            </button>
                            <button type="button" id="step3Next" class="bg-primary text-white py-3 px-4 rounded-lg hover:bg-opacity-90 transition duration-300 font-medium shadow-button w-5/12">
                                Next: Review
                            </button>
                        </div>
                    </div>

                    <!-- Step 4: Review and Submit -->
                    <div id="step-4" class="registration-step hidden">
                        <p class="text-sm text-gray-600 mb-6">Please review your information before submitting. You can go back to make changes if needed.</p>

                        <!-- Personal Information Review -->
                        <div class="mb-6">
                            <h3 class="text-md font-semibold text-gray-800 mb-3 border-b pb-2">Personal Information</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <p class="text-sm text-gray-600">Name</p>
                                    <p class="text-sm font-medium" id="review-name"></p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-600">Email</p>
                                    <p class="text-sm font-medium" id="review-email"></p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-600">Phone</p>
                                    <p class="text-sm font-medium" id="review-phone"></p>
                                </div>
                            </div>
                        </div>

                        <!-- Company Information Review -->
                        <div class="mb-6">
                            <h3 class="text-md font-semibold text-gray-800 mb-3 border-b pb-2">Company Information</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <p class="text-sm text-gray-600">Company Name</p>
                                    <p class="text-sm font-medium" id="review-company-name"></p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-600">Registration Number</p>
                                    <p class="text-sm font-medium" id="review-registration-number"></p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-600">Tax ID</p>
                                    <p class="text-sm font-medium" id="review-tax-id"></p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-600">Industry</p>
                                    <p class="text-sm font-medium" id="review-industry"></p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-600">Address</p>
                                    <p class="text-sm font-medium" id="review-company-address"></p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-600">City</p>
                                    <p class="text-sm font-medium" id="review-company-city"></p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-600">Country</p>
                                    <p class="text-sm font-medium" id="review-company-country"></p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-600">Company Phone</p>
                                    <p class="text-sm font-medium" id="review-company-phone"></p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-600">Company Email</p>
                                    <p class="text-sm font-medium" id="review-company-email"></p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-600">Website</p>
                                    <p class="text-sm font-medium" id="review-company-website"></p>
                                </div>
                            </div>
                        </div>

                        <!-- Documents Review -->
                        <div class="mb-6">
                            <h3 class="text-md font-semibold text-gray-800 mb-3 border-b pb-2">Uploaded Documents</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <p class="text-sm text-gray-600">Certificate of Incorporation</p>
                                    <p class="text-sm font-medium" id="review-certificate"></p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-600">Tax Clearance</p>
                                    <p class="text-sm font-medium" id="review-tax-clearance"></p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-600">Business License</p>
                                    <p class="text-sm font-medium" id="review-business-license"></p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-600">Director ID/Passport</p>
                                    <p class="text-sm font-medium" id="review-director-id"></p>
                                </div>
                            </div>
                        </div>

                        <!-- Terms and Conditions -->
                        <div class="mt-6">
                            <div class="flex items-start">
                                <div class="flex items-center h-5">
                                    <input id="terms" name="terms" type="checkbox" class="h-4 w-4 text-primary border-gray-300 rounded focus:ring-primary" required>
                                </div>
                                <div class="ml-3 text-sm">
                                    <label for="terms" class="font-medium text-gray-700">I agree to the</label>
                                    <a href="{{ url('/terms') }}" class="text-primary hover:underline" target="_blank">Terms of Service</a>
                                    <span>and</span>
                                    <a href="{{ url('/privacy') }}" class="text-primary hover:underline" target="_blank">Privacy Policy</a>
                                </div>
                            </div>
                            @error('terms')
                            <p class="text-error text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Information Verification -->
                        <div class="mt-4">
                            <div class="flex items-start">
                                <div class="flex items-center h-5">
                                    <input id="verification" name="verification" type="checkbox" class="h-4 w-4 text-primary border-gray-300 rounded focus:ring-primary" required>
                                </div>
                                <div class="ml-3 text-sm">
                                    <label for="verification" class="font-medium text-gray-700">I confirm that all the information provided is accurate and the documents are authentic.</label>
                                </div>
                            </div>
                            @error('verification')
                            <p class="text-error text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Navigation Buttons -->
                        <div class="flex justify-between mt-6">
                            <button type="button" id="step4Back" class="bg-white text-primary border border-primary py-3 px-4 rounded-lg hover:bg-gray-50 transition duration-300 font-medium shadow-sm w-5/12">
                                Back
                            </button>
                            <button type="submit" id="submitBtn" class="bg-primary text-white py-3 px-4 rounded-lg hover:bg-opacity-90 transition duration-300 font-medium shadow-button w-5/12">
                                Complete Registration
                            </button>
                        </div>
                    </div>
                </form>

                <!-- Processing State (Hidden Initially) -->
                <div id="processing-state" class="hidden">
                    <div class="text-center py-10">
                        <div class="inline-block animate-spin rounded-full h-12 w-12 border-t-2 border-b-2 border-primary mb-5"></div>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">Processing Your Registration</h3>
                        <p class="text-gray-600">Please wait while we process your registration. This may take a moment.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Success Message Modal (Initially Hidden) -->
    <div id="success-modal" class="fixed inset-0 z-50 overflow-y-auto hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-green-100 sm:mx-0 sm:h-10 sm:w-10">
                            <svg class="h-6 w-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                        </div>
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                            <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                                Registration Successful
                            </h3>
                            <div class="mt-2">
                                <p class="text-sm text-gray-500">
                                    Your corporate account registration has been submitted successfully. Our team will review your documents and verify your account.
                                </p>
                                <p class="text-sm text-gray-500 mt-2">
                                    You will receive an email notification once your account is verified. This usually takes 1-2 business days.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <a href="{{ url('/login') }}" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-primary text-base font-medium text-white hover:bg-opacity-90 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary sm:ml-3 sm:w-auto sm:text-sm">
                        Go to Login
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('scripts')
    @vite(['resources/js/corporate-register.js'])
@endpush
