@extends('layouts.app')

@section('title', 'Security Settings - ' . config('app.name'))
@section('meta_description', 'Manage your account security settings and password')
@section('header_title', 'Security Settings')

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
                        <a href="{{ route('profile.security') }}" class="flex items-center px-4 py-3 text-dark bg-primary bg-opacity-10 rounded-lg">
                            <i class="fas fa-shield-alt w-6 text-primary"></i>
                            <span class="font-medium">Security Settings</span>
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
            <!-- Change Password -->
            <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                <div class="p-6 border-b border-gray-100">
                    <h2 class="text-xl font-bold text-dark">Change Password</h2>
                    <p class="text-gray-600 mt-1">Update your password to keep your account secure</p>
                </div>
                <div class="p-6">
                    <form action="{{ route('profile.update-password') }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="space-y-6">
                            <!-- Current Password -->
                            <div>
                                <label for="current_password" class="block text-sm font-medium text-gray-700 mb-1">Current Password</label>
                                <div class="relative">
                                    <input type="password" name="current_password" id="current_password" 
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-primary focus:border-primary" required>
                                    <button type="button" class="toggle-password absolute inset-y-0 right-0 pr-3 flex items-center text-gray-500 cursor-pointer" data-target="current_password">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                                @error('current_password')
                                    <p class="mt-1 text-sm text-error">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- New Password -->
                            <div>
                                <label for="password" class="block text-sm font-medium text-gray-700 mb-1">New Password</label>
                                <div class="relative">
                                    <input type="password" name="password" id="password" 
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-primary focus:border-primary" required>
                                    <button type="button" class="toggle-password absolute inset-y-0 right-0 pr-3 flex items-center text-gray-500 cursor-pointer" data-target="password">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                                @error('password')
                                    <p class="mt-1 text-sm text-error">{{ $message }}</p>
                                @enderror
                                <p class="mt-1 text-sm text-gray-500">Password must be at least 8 characters long</p>
                            </div>

                            <!-- Confirm Password -->
                            <div>
                                <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">Confirm New Password</label>
                                <div class="relative">
                                    <input type="password" name="password_confirmation" id="password_confirmation" 
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-primary focus:border-primary" required>
                                    <button type="button" class="toggle-password absolute inset-y-0 right-0 pr-3 flex items-center text-gray-500 cursor-pointer" data-target="password_confirmation">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="mt-8 flex justify-end">
                            <button type="submit" class="px-6 py-3 bg-primary text-white rounded-lg hover:bg-opacity-90 transition-colors">
                                Update Password
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Security Tips -->
            <div class="bg-white rounded-xl shadow-sm overflow-hidden mt-6">
                <div class="p-6 border-b border-gray-100">
                    <h2 class="text-xl font-bold text-dark">Security Tips</h2>
                </div>
                <div class="p-6">
                    <ul class="space-y-4">
                        <li class="flex items-start">
                            <div class="flex-shrink-0 mt-1">
                                <i class="fas fa-check-circle text-success"></i>
                            </div>
                            <div class="ml-3">
                                <h3 class="font-medium text-dark">Use a strong password</h3>
                                <p class="text-gray-600 mt-1">Create a unique password with a mix of letters, numbers, and symbols.</p>
                            </div>
                        </li>
                        <li class="flex items-start">
                            <div class="flex-shrink-0 mt-1">
                                <i class="fas fa-check-circle text-success"></i>
                            </div>
                            <div class="ml-3">
                                <h3 class="font-medium text-dark">Change passwords regularly</h3>
                                <p class="text-gray-600 mt-1">Update your password every 3-6 months for better security.</p>
                            </div>
                        </li>
                        <li class="flex items-start">
                            <div class="flex-shrink-0 mt-1">
                                <i class="fas fa-check-circle text-success"></i>
                            </div>
                            <div class="ml-3">
                                <h3 class="font-medium text-dark">Don't share your credentials</h3>
                                <p class="text-gray-600 mt-1">Never share your login details or OTP with anyone, including our staff.</p>
                            </div>
                        </li>
                        <li class="flex items-start">
                            <div class="flex-shrink-0 mt-1">
                                <i class="fas fa-check-circle text-success"></i>
                            </div>
                            <div class="ml-3">
                                <h3 class="font-medium text-dark">Be alert to phishing</h3>
                                <p class="text-gray-600 mt-1">We will never ask for your password or OTP via email, SMS, or phone calls.</p>
                            </div>
                        </li>
                        <li class="flex items-start">
                            <div class="flex-shrink-0 mt-1">
                                <i class="fas fa-check-circle text-success"></i>
                            </div>
                            <div class="ml-3">
                                <h3 class="font-medium text-dark">Log out from shared devices</h3>
                                <p class="text-gray-600 mt-1">Always log out when using public or shared computers.</p>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Account Activity -->
            <div class="bg-white rounded-xl shadow-sm overflow-hidden mt-6">
                <div class="p-6 border-b border-gray-100">
                    <h2 class="text-xl font-bold text-dark">Account Activity</h2>
                </div>
                <div class="p-6">
                    <div class="flex items-center justify-between mb-6">
                        <div>
                            <h3 class="font-medium text-dark">Current Session</h3>
                            <p class="text-gray-600 mt-1">This is your current active session</p>
                        </div>
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-success bg-opacity-10 text-success">
                            Active Now
                        </span>
                    </div>

                    <div class="bg-light rounded-lg p-4">
                        <div class="flex items-start">
                            <div class="flex-shrink-0 mt-1">
                                <i class="fas fa-info-circle text-primary"></i>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-dark">Security Notice</h3>
                                <p class="mt-1 text-sm text-gray-600">
                                    If you suspect any unauthorized access to your account, please change your password immediately and contact our support team.
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
        // Toggle password visibility
        const toggleButtons = document.querySelectorAll('.toggle-password');
        
        toggleButtons.forEach(button => {
            button.addEventListener('click', function() {
                const targetId = this.getAttribute('data-target');
                const passwordInput = document.getElementById(targetId);
                
                if (passwordInput.type === 'password') {
                    passwordInput.type = 'text';
                    this.innerHTML = '<i class="fas fa-eye-slash"></i>';
                } else {
                    passwordInput.type = 'password';
                    this.innerHTML = '<i class="fas fa-eye"></i>';
                }
            });
        });
    });
</script>
@endpush
