@extends('layouts.app')

@section('title', 'My Profile - ' . config('app.name'))
@section('meta_description', 'Manage your personal information and account settings')
@section('header_title', 'My Profile')

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
                        <a href="{{ route('profile.index') }}" class="flex items-center px-4 py-3 text-dark bg-primary bg-opacity-10 rounded-lg">
                            <i class="fas fa-user w-6 text-primary"></i>
                            <span class="font-medium">Personal Information</span>
                        </a>
                        <a href="{{ route('profile.security') }}" class="flex items-center px-4 py-3 text-dark hover:bg-gray-50 rounded-lg transition-colors">
                            <i class="fas fa-shield-alt w-6 text-gray-500"></i>
                            <span>Security Settings</span>
                        </a>
                        <a href="{{ route('profile.kyc') }}" class="flex items-center px-4 py-3 text-dark hover:bg-gray-50 rounded-lg transition-colors">
                            <i class="fas fa-id-card w-6 text-gray-500"></i>
                            <span>KYC Verification</span>
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
            <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                <div class="p-6 border-b border-gray-100">
                    <h2 class="text-xl font-bold text-dark">Personal Information</h2>
                    <p class="text-gray-600 mt-1">Update your personal details and contact information</p>
                </div>
                <div class="p-6">
                    <form action="{{ route('profile.update') }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- First Name -->
                            <div>
                                <label for="first_name" class="block text-sm font-medium text-gray-700 mb-1">First Name</label>
                                <input type="text" name="first_name" id="first_name" value="{{ old('first_name', $user->first_name) }}" 
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-primary focus:border-primary" required>
                                @error('first_name')
                                    <p class="mt-1 text-sm text-error">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Last Name -->
                            <div>
                                <label for="last_name" class="block text-sm font-medium text-gray-700 mb-1">Last Name</label>
                                <input type="text" name="last_name" id="last_name" value="{{ old('last_name', $user->last_name) }}" 
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-primary focus:border-primary" required>
                                @error('last_name')
                                    <p class="mt-1 text-sm text-error">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Email -->
                            <div>
                                <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
                                <div class="relative">
                                    <input type="email" id="email" value="{{ $user->email }}" 
                                        class="w-full px-4 py-2 border border-gray-200 bg-gray-50 rounded-lg" readonly>
                                    @if($user->is_email_verified)
                                        <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                                            <span class="text-success" title="Verified"><i class="fas fa-check-circle"></i></span>
                                        </div>
                                    @endif
                                </div>
                                <p class="mt-1 text-xs text-gray-500">Email address cannot be changed</p>
                            </div>

                            <!-- Phone Number -->
                            <div>
                                <label for="phone_number" class="block text-sm font-medium text-gray-700 mb-1">Phone Number</label>
                                <div class="relative">
                                    <input type="text" id="phone_number" value="{{ $user->phone_number }}" 
                                        class="w-full px-4 py-2 border border-gray-200 bg-gray-50 rounded-lg" readonly>
                                    @if($user->is_phone_verified)
                                        <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                                            <span class="text-success" title="Verified"><i class="fas fa-check-circle"></i></span>
                                        </div>
                                    @endif
                                </div>
                                <p class="mt-1 text-xs text-gray-500">Phone number cannot be changed</p>
                            </div>

                            <!-- Date of Birth -->
                            <div>
                                <label for="date_of_birth" class="block text-sm font-medium text-gray-700 mb-1">Date of Birth</label>
                                <input type="date" name="date_of_birth" id="date_of_birth" value="{{ old('date_of_birth', $user->date_of_birth ? $user->date_of_birth->format('Y-m-d') : '') }}" 
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-primary focus:border-primary" required>
                                @error('date_of_birth')
                                    <p class="mt-1 text-sm text-error">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Address -->
                            <div class="md:col-span-2">
                                <label for="address" class="block text-sm font-medium text-gray-700 mb-1">Address</label>
                                <input type="text" name="address" id="address" value="{{ old('address', $user->address) }}" 
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-primary focus:border-primary">
                                @error('address')
                                    <p class="mt-1 text-sm text-error">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- City -->
                            <div>
                                <label for="city" class="block text-sm font-medium text-gray-700 mb-1">City</label>
                                <input type="text" name="city" id="city" value="{{ old('city', $user->city) }}" 
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-primary focus:border-primary">
                                @error('city')
                                    <p class="mt-1 text-sm text-error">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Country -->
                            <div>
                                <label for="country" class="block text-sm font-medium text-gray-700 mb-1">Country</label>
                                <input type="text" name="country" id="country" value="{{ old('country', $user->country ?? 'Zambia') }}" 
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-primary focus:border-primary">
                                @error('country')
                                    <p class="mt-1 text-sm text-error">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="mt-8 flex justify-end">
                            <button type="submit" class="px-6 py-3 bg-primary text-white rounded-lg hover:bg-opacity-90 transition-colors">
                                Save Changes
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Account Status -->
            <div class="bg-white rounded-xl shadow-sm overflow-hidden mt-6">
                <div class="p-6 border-b border-gray-100">
                    <h2 class="text-xl font-bold text-dark">Account Status</h2>
                </div>
                <div class="p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="font-medium text-dark">Verification Level</h3>
                            <p class="text-gray-600 mt-1">
                                @if(auth()->user()->verification_level === 'verified')
                                    Your account is fully verified
                                @else
                                    Basic verification - limited transaction amounts
                                @endif
                            </p>
                        </div>
                        <div>
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

                    <div class="mt-6 border-t border-gray-100 pt-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="font-medium text-dark">Account Created</h3>
                                <p class="text-gray-600 mt-1">{{ $user->created_at->format('F j, Y') }}</p>
                            </div>
                        </div>
                    </div>

                    @if(auth()->user()->verification_level !== 'verified')
                        <div class="mt-6 bg-light rounded-lg p-4">
                            <div class="flex items-start">
                                <div class="flex-shrink-0 mt-1">
                                    <i class="fas fa-info-circle text-primary"></i>
                                </div>
                                <div class="ml-3">
                                    <h3 class="text-sm font-medium text-dark">Complete Your Verification</h3>
                                    <p class="mt-1 text-sm text-gray-600">
                                        To enjoy higher transaction limits and full access to all features, please complete your KYC verification.
                                    </p>
                                    <div class="mt-3">
                                        <a href="{{ route('profile.kyc') }}" class="inline-flex items-center px-4 py-2 bg-primary text-white rounded-lg hover:bg-opacity-90 transition-colors">
                                            <i class="fas fa-id-card mr-2"></i> Complete KYC
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
