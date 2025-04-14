@extends('layouts.auth')

@section('title', 'Corporate Registration - ' . config('app.name'))
@section('meta_description', 'Register your business to start funding mobile wallets with bulk disbursements')

@section('header_nav')
    <a href="{{ url('/login') }}"
       class="bg-primary text-white font-medium px-4 py-2 rounded-lg hover:bg-opacity-90 transition duration-300 shadow-button">Login</a>
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
                            <div
                                class="flex-shrink-0 w-8 h-8 rounded-full bg-white bg-opacity-20 flex items-center justify-center mr-3">
                                <i class="fas fa-users text-white"></i>
                            </div>
                            <div class="mb-5">
                                <h3 class="font-semibold">Multi-User Access</h3>
                                <p class="text-sm text-white text-opacity-90">Add team members with different roles</p>
                            </div>
                        </div>

                        <div class="flex items-start mb-5">
                            <div
                                class="flex-shrink-0 w-8 h-8 rounded-full bg-white bg-opacity-20 flex items-center justify-center mr-3">
                                <i class="fas fa-percentage text-white"></i>
                            </div>
                            <div class="mb-5">
                                <h3 class="font-semibold">Preferential Rates</h3>
                                <p class="text-sm text-white text-opacity-90">Volume-based discounts for businesses</p>
                            </div>
                        </div>

                        <div class="flex items-start mb-5">
                            <div
                                class="flex-shrink-0 w-8 h-8 rounded-full bg-white bg-opacity-20 flex items-center justify-center mr-3">
                                <i class="fas fa-file-invoice text-white"></i>
                            </div>
                            <div class="mb-5">
                                <h3 class="font-semibold">Bulk Disbursements</h3>
                                <p class="text-sm text-white text-opacity-90">Send funds to multiple recipients at
                                    once</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-8 text-sm text-white text-opacity-90">
                    Already have an account? <a href="{{ url('/login') }}"
                                                class="text-white underline font-medium hover:text-secondary transition duration-300">Login
                        here</a>
                </div>
            </div>

            <!-- Right Side - Form -->
            <div class="md:w-2/3 p-8">
                <h1 class="text-2xl font-bold text-dark mb-6">Create Corporate Account</h1>

                <!-- Step Indicator -->
                <div class="mb-8">
                    <div class="flex items-center justify-between">
                        <div class="step active flex flex-col items-center">
                            <div
                                class="w-8 h-8 rounded-full bg-primary text-white flex items-center justify-center mb-1">
                                1
                            </div>
                            <span class="text-xs">Personal Info</span>
                        </div>
                        <div class="h-0.5 flex-1 bg-gray-200 mx-2 step-line"></div>
                        <div class="step flex flex-col items-center">
                            <div
                                class="w-8 h-8 rounded-full bg-gray-200 text-gray-600 flex items-center justify-center mb-1">
                                2
                            </div>
                            <span class="text-xs">Company Info</span>
                        </div>
                        <div class="h-0.5 flex-1 bg-gray-200 mx-2 step-line"></div>
                        <div class="step flex flex-col items-center">
                            <div
                                class="w-8 h-8 rounded-full bg-gray-200 text-gray-600 flex items-center justify-center mb-1">
                                3
                            </div>
                            <span class="text-xs">Documents</span>
                        </div>
                        <div class="h-0.5 flex-1 bg-gray-200 mx-2 step-line"></div>
                        <div class="step flex flex-col items-center">
                            <div
                                class="w-8 h-8 rounded-full bg-gray-200 text-gray-600 flex items-center justify-center mb-1">
                                4
                            </div>
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
                                <label for="first_name" class="block text-sm font-medium text-gray-700 mb-1">First
                                    Name</label>
                                <input type="text" id="first_name" name="first_name"
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary transition duration-200"
                                       required>
                                @error('first_name')
                                <p class="text-error text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Last Name -->
                            <div>
                                <label for="last_name" class="block text-sm font-medium text-gray-700 mb-1">Last
                                    Name</label>
                                <input type="text" id="last_name" name="last_name"
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary transition duration-200"
                                       required>
                                @error('last_name')
                                <p class="text-error text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Email and Phone Number -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                            <div>
                                <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email
                                    Address</label>
                                <input type="email" id="email" name="email"
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary transition duration-200"
                                       required>
                                @error('email')
                                <p class="text-error text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Phone Number -->
                            <div>
                                <label for="phone_number" class="block text-sm font-medium text-gray-700 mb-1">Phone
                                    Number</label>
                                <input type="text" id="phone_number" name="phone_number"
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary transition duration-200"
                                       placeholder="+26097XXXXXXX" required>
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
                                <label for="password"
                                       class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                                <div class="relative">
                                    <input type="password" id="password" name="password"
                                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary transition duration-200"
                                           required>
                                    <button type="button"
                                            class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-500 hover:text-gray-700 toggle-password transition duration-200">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                                @error('password')
                                <p class="text-error text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Confirm Password -->
                            <div>
                                <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">Confirm
                                    Password</label>
                                <div class="relative">
                                    <input type="password" id="password_confirmation" name="password_confirmation"
                                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary transition duration-200"
                                           required>
                                    <button type="button"
                                            class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-500 hover:text-gray-700 toggle-password transition duration-200">
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
                            <button type="button" id="step1Next"
                                    class="w-full bg-primary text-white py-3 px-4 rounded-lg hover:bg-opacity-90 transition duration-300 font-medium shadow-button">
                                Next: Company Information
                            </button>
                        </div>
                    </div>

                    <!-- Step 2: Company Information -->
                    <div id="step-2" class="registration-step hidden">
                        <!-- Company Name and Registration Number -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="company_name" class="block text-sm font-medium text-gray-700 mb-1">Company
                                    Name</label>
                                <input type="text" id="company_name" name="company_name"
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary transition duration-200"
                                       required>
                                @error('company_name')
                                <p class="text-error text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Registration Number -->
                            <div>
                                <label for="registration_number" class="block text-sm font-medium text-gray-700 mb-1">Registration
                                    Number</label>
                                <input type="text" id="registration_number" name="registration_number"
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary transition duration-200"
                                       required>
                                @error('registration_number')
                                <p class="text-error text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Tax ID and Industry -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                            <div>
                                <label for="tax_id" class="block text-sm font-medium text-gray-700 mb-1">Tax ID
                                    (TPIN)</label>
                                <input type="text" id="tax_id" name="tax_id"
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary transition duration-200">
                                @error('tax_id')
                                <p class="text-error text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Industry -->
                            <div>
                                <label for="industry"
                                       class="block text-sm font-medium text-gray-700 mb-1">Industry</label>
                                <select id="industry" name="industry"
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary transition duration-200">
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
                            <label for="company_address" class="block text-sm font-medium text-gray-700 mb-1">Company
                                Address</label>
                            <input type="text" id="company_address" name="company_address"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary transition duration-200"
                                   required>
                            @error('company_address')
                            <p class="text-error text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- City and Country -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                            <div>
                                <label for="company_city"
                                       class="block text-sm font-medium text-gray-700 mb-1">City</label>
                                <input type="text" id="company_city" name="company_city"
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary transition duration-200"
                                       required>
                                @error('company_city')
                                <p class="text-error text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="company_country" class="block text-sm font-medium text-gray-700 mb-1">Country</label>
                                <input type="text" id="company_country" name="company_country" value="Zambia"
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary transition duration-200"
                                       readonly>
                            </div>
                        </div>

                        <!-- Company Phone and Email -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                            <div>
                                <label for="company_phone" class="block text-sm font-medium text-gray-700 mb-1">Company
                                    Phone</label>
                                <input type="text" id="company_phone" name="company_phone"
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary transition duration-200"
                                       required>
                                @error('company_phone')
                                <p class="text-error text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="company_email" class="block text-sm font-medium text-gray-700 mb-1">Company
                                    Email</label>
                                <input type="email" id="company_email" name="company_email"
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary transition duration-200"
                                       required>
                                @error('company_email')
                                <p class="text-error text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Company Website -->
                        <div class="mt-6">
                            <label for="company_website" class="block text-sm font-medium text-gray-700 mb-1">Company
                                Website (Optional)</label>
                            <input type="url" id="company_website" name="company_website"
                                   placeholder="https://example.com"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary transition duration-200">
                            @error('company_website')
                            <p class="text-error text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Navigation Buttons -->
                        <div class="flex justify-between mt-6">
                            <button type="button" id="step2Back"
                                    class="bg-white text-primary border border-primary py-3 px-4 rounded-lg hover:bg-gray-50 transition duration-300 font-medium shadow-sm w-5/12">
                                Back
                            </button>
                            <button type="button" id="step2Next"
                                    class="bg-primary text-white py-3 px-4 rounded-lg hover:bg-opacity-90 transition duration-300 font-medium shadow-button w-5/12">
                                Next: Documents
                            </button>
                        </div>
                    </div>

                    <!-- Step 3: Document Upload -->
                    <div id="step-3" class="registration-step hidden">
                        <p class="text-sm text-gray-600 mb-6">Please upload the following documents to verify your
                            company. Accepted formats: PDF, JPG, PNG (Max 5MB each)</p>

                        <!-- Certificate of Incorporation -->
                        <div class="mb-6">
                            <label for="certificate_of_incorporation"
                                   class="block text-sm font-medium text-gray-700 mb-1">Certificate of
                                Incorporation</label>
                            <div
                                class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-lg">
                                <div class="space-y-1 text-center">
                                    <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none"
                                         viewBox="0 0 48 48" aria-hidden="true">
                                        <path
                                            d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02"
                                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                    <div class="flex text-sm text-gray-600">
                                        <label for="certificate_file"
                                               class="relative cursor-pointer bg-white rounded-md font-medium text-primary hover:text-primary-dark focus-within:outline-none">
                                            <span>Upload a file</span>
                                            <input id="certificate_file" name="certificate_file" type="file"
                                                   class="sr-only" accept=".pdf,.jpg,.jpeg,.png" required>
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
                            <label for="tax_clearance" class="block text-sm font-medium text-gray-700 mb-1">Tax
                                Clearance (Optional)</label>
                            <div
                                class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-lg">
                                <div class="space-y-1 text-center">
                                    <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none"
                                         viewBox="0 0 48 48" aria-hidden="true">
                                        <path
                                            d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02"
                                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                    <div class="flex text-sm text-gray-600">
                                        <label for="tax_clearance_file"
                                               class="relative cursor-pointer bg-white rounded-md font-medium text-primary hover:text-primary-dark focus-within:outline-none">
                                            <span>Upload a file</span>
                                            <input id="tax_clearance_file" name="tax_clearance_file" type="file"
                                                   class="sr-only" accept=".pdf,.jpg,.jpeg,.png">
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
                            <label for="business_license" class="block text-sm font-medium text-gray-700 mb-1">Business
                                License</label>
                            <div
                                class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-lg">
                                <div class="space-y-1 text-center">
                                    <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none"
                                         viewBox="0 0 48 48" aria-hidden="true">
                                        <path
                                            d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02"
                                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                    <div class="flex text-sm text-gray-600">
                                        <label for="business_license_file"
                                               class="relative cursor-pointer bg-white rounded-md font-medium text-primary hover:text-primary-dark focus-within:outline-none">
                                            <span>Upload a file</span>
                                            <input id="business_license_file" name="business_license_file" type="file"
                                                   class="sr-only" accept=".pdf,.jpg,.jpeg,.png" required>
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
                            <label for="director_id" class="block text-sm font-medium text-gray-700 mb-1">Director
                                ID/Passport</label>
                            <div
                                class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-lg">
                                <div class="space-y-1 text-center">
                                    <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none"
                                         viewBox="0 0 48 48" aria-hidden="true">
                                        <path
                                            d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02"
                                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                    <div class="flex text-sm text-gray-600">
                                        <label for="director_id_file"
                                               class="relative cursor-pointer bg-white rounded-md font-medium text-primary hover:text-primary-dark focus-within:outline-none">
                                            <span>Upload a file</span>
                                            <input id="director_id_file" name="director_id_file" type="file"
                                                   class="sr-only" accept=".pdf,.jpg,.jpeg,.png" required>
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
                            <button type="button" id="step3Back"
                                    class="bg-white text-primary border border-primary py-3 px-4 rounded-lg hover:bg-gray-50 transition duration-300 font-medium shadow-sm w-5/12">
                                Back
                            </button>
                            <button type="button" id="step3Next"
                                    class="bg-primary text-white py-3 px-4 rounded-lg hover:bg-opacity-90 transition duration-300 font-medium shadow-button w-5/12">
                                Next: Review
                            </button>
                        </div>
                    </div>

                    <!-- Step 4: Review and Submit -->
                    <div id="step-4" class="registration-step hidden">
                        <p class="text-sm text-gray-600 mb-6">Please review your information before submitting. You can
                            go back to make changes if needed.</p>

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
                                    <input id="terms" name="terms" type="checkbox"
                                           class="h-4 w-4 text-primary border-gray-300 rounded focus:ring-primary"
                                           required>
                                </div>
                                <div class="ml-3 text-sm">
                                    <label for="terms" class="font-medium text-gray-700">I agree to the</label>
                                    <a href="{{ url('/terms') }}" class="text-primary hover:underline" target="_blank">Terms
                                        of Service</a>
                                    <span>and</span>
                                    <a href="{{ url('/privacy') }}" class="text-primary hover:underline"
                                       target="_blank">Privacy Policy</a>
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
                                    <input id="verification" name="verification" type="checkbox"
                                           class="h-4 w-4 text-primary border-gray-300 rounded focus:ring-primary"
                                           required>
                                </div>
                                <div class="ml-3 text-sm">
                                    <label for="verification" class="font-medium text-gray-700">I confirm that all the
                                        information provided is accurate and the documents are authentic.</label>
                                </div>
                            </div>
                            @error('verification')
                            <p class="text-error text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Navigation Buttons -->
                        <div class="flex justify-between mt-6">
                            <button type="button" id="step4Back"
                                    class="bg-white text-primary border border-primary py-3 px-4 rounded-lg hover:bg-gray-50 transition duration-300 font-medium shadow-sm w-5/12">
                                Back
                            </button>
                            <button type="submit" id="submitBtn"
                                    class="bg-primary text-white py-3 px-4 rounded-lg hover:bg-opacity-90 transition duration-300 font-medium shadow-button w-5/12">
                                Complete Registration
                            </button>
                        </div>
                    </div>
                </form>

                <!-- Processing State (Hidden Initially) -->
                <div id="processing-state" class="hidden">
                    <div class="text-center py-10">
                        <div
                            class="inline-block animate-spin rounded-full h-12 w-12 border-t-2 border-b-2 border-primary mb-5"></div>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">Processing Your Registration</h3>
                        <p class="text-gray-600">Please wait while we process your registration. This may take a
                            moment.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Success Message Modal (Initially Hidden) -->
    <div id="success-modal" class="fixed inset-0 z-50 overflow-y-auto hidden" aria-labelledby="modal-title"
         role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div
                class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div
                            class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-green-100 sm:mx-0 sm:h-10 sm:w-10">
                            <svg class="h-6 w-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M5 13l4 4L19 7"/>
                            </svg>
                        </div>
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                            <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                                Registration Successful
                            </h3>
                            <div class="mt-2">
                                <p class="text-sm text-gray-500">
                                    Your corporate account registration has been submitted successfully. Our team will
                                    review your documents and verify your account.
                                </p>
                                <p class="text-sm text-gray-500 mt-2">
                                    You will receive an email notification once your account is verified. This usually
                                    takes 1-2 business days.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <a href="{{ url('/login') }}"
                       class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-primary text-base font-medium text-white hover:bg-opacity-90 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary sm:ml-3 sm:w-auto sm:text-sm">
                        Go to Login
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('scripts')
    {{--    @vite(['resources/js/corporate-register.js'])--}}
    <script type="application/javascript">
        document.addEventListener('DOMContentLoaded', function () {
            // Variables for step navigation
            const steps = document.querySelectorAll('.step');
            const stepContents = document.querySelectorAll('.registration-step');
            let currentStep = 1;

            // File upload handling
            setupFileUpload('certificate_file', 'certificate_file_name');
            setupFileUpload('tax_clearance_file', 'tax_clearance_file_name');
            setupFileUpload('business_license_file', 'business_license_file_name');
            setupFileUpload('director_id_file', 'director_id_file_name');

            function setupFileUpload(inputId, displayId) {
                const input = document.getElementById(inputId);
                const display = document.getElementById(displayId);
                const nameSpan = display.querySelector('span');

                input.addEventListener('change', function () {
                    if (input.files.length > 0) {
                        // Validate file size (max 5MB)
                        if (input.files[0].size > 5 * 1024 * 1024) {
                            alert('File size exceeds 5MB limit. Please select a smaller file.');
                            input.value = '';
                            display.classList.add('hidden');
                            return;
                        }

                        // Validate file type
                        const fileType = input.files[0].type;
                        if (!['application/pdf', 'image/jpeg', 'image/jpg', 'image/png'].includes(fileType)) {
                            alert('Invalid file type. Please upload PDF, JPG, or PNG files only.');
                            input.value = '';
                            display.classList.add('hidden');
                            return;
                        }

                        nameSpan.textContent = input.files[0].name;
                        display.classList.remove('hidden');
                    } else {
                        display.classList.add('hidden');
                    }
                });
            }

            // Password visibility toggle
            document.querySelectorAll('.toggle-password').forEach(button => {
                button.addEventListener('click', function () {
                    const input = this.parentElement.querySelector('input');
                    const type = input.getAttribute('type');

                    if (type === 'password') {
                        input.setAttribute('type', 'text');
                        this.innerHTML = '<i class="fas fa-eye-slash"></i>';
                    } else {
                        input.setAttribute('type', 'password');
                        this.innerHTML = '<i class="fas fa-eye"></i>';
                    }
                });
            });

            // Password strength indicator
            const passwordInput = document.getElementById('password');
            const strengthIndicators = document.querySelectorAll('.strength-indicator');
            const strengthText = document.querySelector('.strength-text span');
            const strengthContainer = document.querySelector('.password-strength');

            passwordInput.addEventListener('input', function () {
                const password = this.value;

                if (password.length > 0) {
                    strengthContainer.classList.remove('hidden');

                    // Reset indicators
                    strengthIndicators.forEach(indicator => {
                        indicator.className = 'h-1 w-1/4 rounded bg-gray-200 strength-indicator';
                    });

                    // Calculate strength
                    let strength = 0;

                    // Length check
                    if (password.length >= 8) strength++;

                    // Contains lowercase
                    if (/[a-z]/.test(password)) strength++;

                    // Contains uppercase
                    if (/[A-Z]/.test(password)) strength++;

                    // Contains number or special char
                    if (/[0-9]/.test(password) || /[^a-zA-Z0-9]/.test(password)) strength++;

                    // Update UI
                    for (let i = 0; i < strength; i++) {
                        strengthIndicators[i].classList.remove('bg-gray-200');

                        if (strength === 1) {
                            strengthIndicators[i].classList.add('bg-red-500');
                            strengthText.textContent = 'Weak';
                            strengthText.className = 'text-red-500';
                        } else if (strength === 2) {
                            strengthIndicators[i].classList.add('bg-yellow-500');
                            strengthText.textContent = 'Fair';
                            strengthText.className = 'text-yellow-500';
                        } else if (strength === 3) {
                            strengthIndicators[i].classList.add('bg-blue-500');
                            strengthText.textContent = 'Good';
                            strengthText.className = 'text-blue-500';
                        } else if (strength === 4) {
                            strengthIndicators[i].classList.add('bg-green-500');
                            strengthText.textContent = 'Strong';
                            strengthText.className = 'text-green-500';
                        }
                    }
                } else {
                    strengthContainer.classList.add('hidden');
                }
            });

            // Step navigation
            function goToStep(step) {
                // Hide all steps
                stepContents.forEach(content => content.classList.add('hidden'));

                // Show the current step
                document.getElementById(`step-${step}`).classList.remove('hidden');

                // Update step indicators
                steps.forEach((s, index) => {
                    const stepNumber = index + 1;
                    const stepCircle = s.querySelector('div:first-child');

                    if (stepNumber < step) {
                        // Completed step
                        stepCircle.classList.remove('bg-gray-200', 'text-gray-600');
                        stepCircle.classList.add('bg-green-500', 'text-white');
                        stepCircle.innerHTML = '<i class="fas fa-check"></i>';
                        s.classList.add('completed');
                    } else if (stepNumber === step) {
                        // Current step
                        stepCircle.classList.remove('bg-gray-200', 'text-gray-600', 'bg-green-500');
                        stepCircle.classList.add('bg-primary', 'text-white');
                        stepCircle.textContent = stepNumber;
                        s.classList.add('active');
                    } else {
                        // Future step
                        stepCircle.classList.remove('bg-primary', 'text-white', 'bg-green-500');
                        stepCircle.classList.add('bg-gray-200', 'text-gray-600');
                        stepCircle.textContent = stepNumber;
                        s.classList.remove('active', 'completed');
                    }
                });

                // Update step lines
                const stepLines = document.querySelectorAll('.step-line');
                stepLines.forEach((line, index) => {
                    if (index < step - 1) {
                        line.classList.remove('bg-gray-200');
                        line.classList.add('bg-green-500');
                    } else {
                        line.classList.remove('bg-green-500');
                        line.classList.add('bg-gray-200');
                    }
                });

                // Update current step
                currentStep = step;
                document.querySelector('input[name="current_step"]').value = currentStep;

                // Scroll to top of form
                document.querySelector('.bg-white.rounded-xl').scrollIntoView({behavior: 'smooth'});
            }

            // Email validation
            function isValidEmail(email) {
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                return emailRegex.test(email);
            }

            // Phone number validation
            function isValidPhone(phone) {
                // Remove any non-digit characters
                const cleanPhone = phone.replace(/\D/g, '');
                // Check if length is between 12 and 14 digits
                return cleanPhone.length >= 12 && cleanPhone.length <= 14;
            }

            // Step 1 Next Button
            document.getElementById('step1Next').addEventListener('click', function () {
                // Validate Step 1
                const firstName = document.getElementById('first_name').value.trim();
                const lastName = document.getElementById('last_name').value.trim();
                const email = document.getElementById('email').value.trim();
                const phone = document.getElementById('phone_number').value.trim();
                const password = document.getElementById('password').value;
                const passwordConfirmation = document.getElementById('password_confirmation').value;

                // Field validation
                if (!firstName || !lastName || !email || !phone || !password || !passwordConfirmation) {
                    alert('Please fill in all required fields');
                    return;
                }

                // Email validation
                if (!isValidEmail(email)) {
                    alert('Please enter a valid email address');
                    return;
                }

                // Phone validation
                if (!isValidPhone(phone)) {
                    alert('Please enter a valid Zambian phone number (e.g., +260971234567)');
                    return;
                }

                // Password validation
                if (password.length < 8) {
                    alert('Password must be at least 8 characters long');
                    return;
                }

                if (password !== passwordConfirmation) {
                    alert('Passwords do not match');
                    return;
                }

                goToStep(2);
            });

            // Step 2 Back Button
            document.getElementById('step2Back').addEventListener('click', function () {
                goToStep(1);
            });

            // Step 2 Next Button
            document.getElementById('step2Next').addEventListener('click', function () {
                // Validate Step 2
                const companyName = document.getElementById('company_name').value.trim();
                const registrationNumber = document.getElementById('registration_number').value.trim();
                const companyAddress = document.getElementById('company_address').value.trim();
                const companyCity = document.getElementById('company_city').value.trim();
                const companyPhone = document.getElementById('company_phone').value.trim();
                const companyEmail = document.getElementById('company_email').value.trim();
                const industry = document.getElementById('industry').value;

                // Field validation
                if (!companyName || !registrationNumber || !companyAddress || !companyCity || !companyPhone || !companyEmail || !industry) {
                    alert('Please fill in all required fields');
                    return;
                }

                // Email validation
                if (!isValidEmail(companyEmail)) {
                    alert('Please enter a valid company email address');
                    return;
                }

                // Phone validation
                if (!isValidPhone(companyPhone)) {
                    alert('Please enter a valid Zambian phone number for the company');
                    return;
                }

                // Website validation (if provided)
                const website = document.getElementById('company_website').value.trim();
                if (website && !website.startsWith('http://') && !website.startsWith('https://')) {
                    alert('Website URL must start with http:// or https://');
                    return;
                }

                goToStep(3);
            });

            // Step 3 Back Button
            document.getElementById('step3Back').addEventListener('click', function () {
                goToStep(2);
            });

            // Step 3 Next Button
            document.getElementById('step3Next').addEventListener('click', function () {
                // Validate Step 3
                const certificateFile = document.getElementById('certificate_file').files.length;
                const businessLicenseFile = document.getElementById('business_license_file').files.length;
                const directorIdFile = document.getElementById('director_id_file').files.length;

                // Required document validation
                if (!certificateFile || !businessLicenseFile || !directorIdFile) {
                    alert('Please upload all required documents');
                    return;
                }

                // Update review section
                updateReviewSection();

                goToStep(4);
            });

            // Step 4 Back Button
            document.getElementById('step4Back').addEventListener('click', function () {
                goToStep(3);
            });

            // Update Review Section
            function updateReviewSection() {
                // Personal Information
                document.getElementById('review-name').textContent =
                    document.getElementById('first_name').value + ' ' + document.getElementById('last_name').value;
                document.getElementById('review-email').textContent = document.getElementById('email').value;
                document.getElementById('review-phone').textContent = document.getElementById('phone_number').value;

                // Company Information
                document.getElementById('review-company-name').textContent = document.getElementById('company_name').value;
                document.getElementById('review-registration-number').textContent = document.getElementById('registration_number').value;
                document.getElementById('review-tax-id').textContent = document.getElementById('tax_id').value || 'Not provided';
                document.getElementById('review-industry').textContent = document.getElementById('industry').options[document.getElementById('industry').selectedIndex].text;
                document.getElementById('review-company-address').textContent = document.getElementById('company_address').value;
                document.getElementById('review-company-city').textContent = document.getElementById('company_city').value;
                document.getElementById('review-company-country').textContent = document.getElementById('company_country').value;
                document.getElementById('review-company-phone').textContent = document.getElementById('company_phone').value;
                document.getElementById('review-company-email').textContent = document.getElementById('company_email').value;
                document.getElementById('review-company-website').textContent = document.getElementById('company_website').value || 'Not provided';

                // Documents
                document.getElementById('review-certificate').textContent =
                    document.getElementById('certificate_file').files.length ? document.getElementById('certificate_file').files[0].name : 'Not uploaded';
                document.getElementById('review-tax-clearance').textContent =
                    document.getElementById('tax_clearance_file').files.length ? document.getElementById('tax_clearance_file').files[0].name : 'Not uploaded';
                document.getElementById('review-business-license').textContent =
                    document.getElementById('business_license_file').files.length ? document.getElementById('business_license_file').files[0].name : 'Not uploaded';
                document.getElementById('review-director-id').textContent =
                    document.getElementById('director_id_file').files.length ? document.getElementById('director_id_file').files[0].name : 'Not uploaded';
            }

            // Form Submission
            const form = document.getElementById('corporateRegisterForm');

            form.addEventListener('submit', function (e) {
                e.preventDefault();

                // Validate final step
                if (!document.getElementById('terms').checked || !document.getElementById('verification').checked) {
                    alert('Please accept the terms and confirm your information');
                    return;
                }

                // Show processing state
                document.getElementById('step-4').classList.add('hidden');
                document.getElementById('processing-state').classList.remove('hidden');

                // Create FormData object
                const formData = new FormData(form);

                // Add file data explicitly to ensure proper upload
                const fileInputs = ['certificate_file', 'tax_clearance_file', 'business_license_file', 'director_id_file'];
                fileInputs.forEach(inputId => {
                    const fileInput = document.getElementById(inputId);
                    if (fileInput.files.length > 0) {
                        formData.set(inputId, fileInput.files[0]);
                    }
                });

                // Send AJAX request
                fetch('/corporate/register', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                    },
                    credentials: 'same-origin',
                })
                    .then(response => {
                        if (!response.ok) {
                            // Handle HTTP errors
                            if (response.status === 422) {
                                // Validation errors
                                return response.json().then(data => {
                                    throw new Error(Object.values(data.errors).flat().join('\n'));
                                });
                            }
                            throw new Error('Something went wrong during registration. Please try again later.');
                        }
                        return response.json();
                    })
                    .then(data => {
                        // Hide processing state
                        document.getElementById('processing-state').classList.add('hidden');

                        if (data.success) {
                            // Show success modal
                            document.getElementById('success-modal').classList.remove('hidden');
                        } else {
                            // Show error message
                            alert(data.message || 'An error occurred during registration. Please try again.');
                            document.getElementById('step-4').classList.remove('hidden');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        // Hide processing state and show form again
                        document.getElementById('processing-state').classList.add('hidden');
                        document.getElementById('step-4').classList.remove('hidden');
                        alert(error.message || 'An error occurred during registration. Please try again.');
                    });
            });

            // Close success modal when clicking outside
            document.getElementById('success-modal').addEventListener('click', function (e) {
                if (e.target === this) {
                    this.classList.add('hidden');
                }
            });
        });
    </script>
@endpush
