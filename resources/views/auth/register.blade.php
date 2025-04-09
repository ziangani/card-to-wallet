@extends('layouts.auth')

@section('title', 'Register - ' . config('app.name'))
@section('meta_description', 'Create your account to start funding mobile wallets with your card')

@section('header_nav')
<a href="{{ url('/login') }}" class="bg-primary text-white font-medium px-4 py-2 rounded-lg hover:bg-opacity-90 transition duration-300 shadow-button">Login</a>
@endsection

@section('content')
<div class="bg-white rounded-xl shadow-card overflow-hidden">
    <div class="md:flex">
        <!-- Left Side - Image/Info -->
        <div class="md:w-1/3 auth-gradient text-white p-8 flex flex-col justify-between">
            <div>
                <h2 class="text-2xl font-bold mb-4">Join Card-to-Wallet</h2>
                <p class="mb-6">Create your account to start funding mobile wallets instantly with your card.</p>

                <div class="space-y-4 mt-8">
                    <div class="flex items-start mb-5">
                        <div class="flex-shrink-0 w-8 h-8 rounded-full bg-white bg-opacity-20 flex items-center justify-center mr-3">
                            <i class="fas fa-shield-alt text-white"></i>
                        </div>
                        <div class="mb-5">
                            <h3 class="font-semibold">Secure Transactions</h3>
                            <p class="text-sm text-white text-opacity-90">Your card details are never stored</p>
                        </div>
                    </div>

                    <div class="flex items-start mb-5">
                        <div class="flex-shrink-0 w-8 h-8 rounded-full bg-white bg-opacity-20 flex items-center justify-center mr-3">
                            <i class="fas fa-bolt text-white"></i>
                        </div>
                        <div class="mb-5">
                            <h3 class="font-semibold">Instant Transfers</h3>
                            <p class="text-sm text-white text-opacity-90">Fund your wallet in seconds</p>
                        </div>
                    </div>

                    <div class="flex items-start mb-5">
                        <div class="flex-shrink-0 w-8 h-8 rounded-full bg-white bg-opacity-20 flex items-center justify-center mr-3">
                            <i class="fas fa-mobile-alt text-white"></i>
                        </div>
                        <div class="mb-5">
                            <h3 class="font-semibold">Multiple Providers</h3>
                            <p class="text-sm text-white text-opacity-90">Support for all major mobile money services</p>
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
            <h1 class="text-2xl font-bold text-dark mb-6">Create Your Account</h1>

            <form id="registerForm" class="space-y-6">
                @csrf

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
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
                        <input type="email" id="email" name="email" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary transition duration-200" required>
                        @error('email')
                            <p class="text-error text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="phone_number" class="block text-sm font-medium text-gray-700 mb-1">Phone Number</label>
                        <input type="text" id="phone_number" name="phone_number" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary transition duration-200" placeholder="+26097XXXXXXX" required>
                        <p class="text-xs text-gray-500 mt-1 mb-3">Mobile number including the country code.</p>
                        @error('phone_number')
                            <p class="text-error text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Date of Birth and Address -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="date_of_birth" class="block text-sm font-medium text-gray-700 mb-1">Date of Birth</label>
                        <input type="date" id="date_of_birth" name="date_of_birth" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary transition duration-200" placeholder="Select your date of birth" required>
                        @error('date_of_birth')
                            <p class="text-error text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mt-3">
                        <label for="address" class="block text-sm font-medium text-gray-700 mb-1">Address</label>
                        <input type="text" id="address" name="address" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary transition duration-200">
                        @error('address')
                            <p class="text-error text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- City -->
                    <div>
                        <label for="city" class="block text-sm font-medium text-gray-700 mb-1">City</label>
                        <input type="text" id="city" name="city" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary transition duration-200">
                        @error('city')
                            <p class="text-error text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Country -->
                    <div>
                        <label for="country" class="block text-sm font-medium text-gray-700 mb-1">Country</label>
                        <input type="text" id="country" name="country" value="Zambia" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary transition duration-200" readonly>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
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
                <div class="password-strength hidden">
                    <div class="flex space-x-1 mb-1">
                        <div class="h-1 w-1/4 rounded bg-gray-200 strength-indicator"></div>
                        <div class="h-1 w-1/4 rounded bg-gray-200 strength-indicator"></div>
                        <div class="h-1 w-1/4 rounded bg-gray-200 strength-indicator"></div>
                        <div class="h-1 w-1/4 rounded bg-gray-200 strength-indicator"></div>
                    </div>
                    <p class="text-xs text-gray-500 strength-text">Password strength: <span>Weak</span></p>
                </div>

                <!-- Terms and Conditions -->
                <div class="flex items-start mb-5 mt-3">
                    <div class="flex items-center h-5">
                        <input id="terms" name="terms" type="checkbox" class="h-4 w-4 text-primary focus:ring-primary border-gray-300 rounded transition duration-200" required>
                    </div>
                    <div class="ml-3 text-sm">
                        <label for="terms" class="text-gray-700">I agree to the <a href="{{ url('/terms') }}" class="text-primary hover:underline transition duration-200">Terms of Service</a> and <a href="{{ url('/privacy') }}" class="text-primary hover:underline transition duration-200">Privacy Policy</a></label>
                        @error('terms')
                            <p class="text-error text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Submit Button -->
                <div>
                    <button type="submit" class="w-full bg-primary text-white py-3 px-4 rounded-lg hover:bg-opacity-90 transition duration-300 font-medium shadow-button" id="registerButton">
                        Create Account
                    </button>
                </div>
            </form>

            <div class="mt-6 text-center text-sm text-gray-600">
                Already have an account? <a href="{{ url('/login') }}" class="text-primary hover:underline font-medium transition duration-300">Login here</a>
            </div>
            
            <div class="mt-4 text-center">
                <div class="relative">
                    <div class="absolute inset-0 flex items-center">
                        <div class="w-full border-t border-gray-300"></div>
                    </div>
                    <div class="relative flex justify-center text-sm">
                        <span class="px-2 bg-white text-gray-500">Or</span>
                    </div>
                </div>
                <div class="mt-4">
                    <a href="{{ url('/corporate/register') }}" class="inline-flex items-center justify-center w-full px-4 py-3 text-sm font-medium text-primary bg-white border border-primary rounded-lg hover:bg-primary hover:text-white transition duration-300">
                        <i class="fas fa-building mr-2"></i> Register as a Corporate User
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Toggle password visibility
    document.addEventListener('DOMContentLoaded', function() {
        const toggleButtons = document.querySelectorAll('.toggle-password');

        toggleButtons.forEach(button => {
            button.addEventListener('click', function() {
                const input = this.parentElement.querySelector('input');
                const icon = this.querySelector('i');

                if (input.type === 'password') {
                    input.type = 'text';
                    icon.classList.remove('fa-eye');
                    icon.classList.add('fa-eye-slash');
                } else {
                    input.type = 'password';
                    icon.classList.remove('fa-eye-slash');
                    icon.classList.add('fa-eye');
                }
            });
        });

        // Password strength indicator
        const passwordInput = document.getElementById('password');
        const strengthIndicators = document.querySelectorAll('.strength-indicator');
        const strengthText = document.querySelector('.strength-text span');
        const strengthContainer = document.querySelector('.password-strength');

        passwordInput.addEventListener('input', function() {
            const password = this.value;

            if (password.length > 0) {
                strengthContainer.classList.remove('hidden');

                // Reset indicators
                strengthIndicators.forEach(indicator => {
                    indicator.classList.remove('bg-error', 'bg-warning', 'bg-success');
                    indicator.classList.add('bg-gray-200');
                });

                // Calculate strength
                let strength = 0;

                // Length check
                if (password.length >= 8) strength++;

                // Contains lowercase
                if (/[a-z]/.test(password)) strength++;

                // Contains uppercase
                if (/[A-Z]/.test(password)) strength++;

                // Contains number
                if (/[0-9]/.test(password)) strength++;

                // Contains special character
                if (/[^A-Za-z0-9]/.test(password)) strength++;

                // Update indicators
                let color = '';
                let text = '';

                if (strength <= 2) {
                    color = 'bg-error';
                    text = 'Weak';
                } else if (strength <= 4) {
                    color = 'bg-warning';
                    text = 'Medium';
                } else {
                    color = 'bg-success';
                    text = 'Strong';
                }

                for (let i = 0; i < strength && i < 4; i++) {
                    strengthIndicators[i].classList.remove('bg-gray-200');
                    strengthIndicators[i].classList.add(color);
                }

                strengthText.textContent = text;
            } else {
                strengthContainer.classList.add('hidden');
            }
        });

        // Phone number validation
        const phoneInput = document.getElementById('phone_number');

        phoneInput.addEventListener('input', function() {
            // Remove non-numeric characters
            this.value = this.value.replace(/\D/g, '');

            // Limit to 9 digits
            if (this.value.length > 9) {
                this.value = this.value.slice(0, 9);
            }
        });
    });
</script>

<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- SweetAlert2 -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.10.5/dist/sweetalert2.min.css">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.10.5/dist/sweetalert2.all.min.js"></script>

<script>
    $(document).ready(function() {
        $('#registerForm').submit(function(e) {
            e.preventDefault();

            var registerButton = $('#registerButton');
            registerButton.prop('disabled', true).text('Creating Account...');

            $.ajax({
                url: "{{ route('register') }}",
                type: "POST",
                data: $(this).serialize(),
                dataType: 'json', // Expect JSON response
                success: function(response) {
                    registerButton.prop('disabled', false).text('Create Account');
                    if (response.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success!',
                            text: response.message,
                        }).then((result) => {
                            if (result.isConfirmed && response.redirect_url) {
                                window.location.href = response.redirect_url;
                            }
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: response.message,
                        });
                    }
                },
                error: function(xhr, status, error) {
                    registerButton.prop('disabled', false).text('Create Account');
                    var errorMessage = 'Something went wrong. Please try again.';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    }
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: errorMessage,
                    });
                }
            });
        });
    });
</script>
@endpush
