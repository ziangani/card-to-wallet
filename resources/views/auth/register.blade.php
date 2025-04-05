<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - {{ config('app.name') }}</title>
    <meta name="description" content="Create your account to start funding mobile wallets with your card">

    <!-- Favicon -->
    <link rel="icon" href="{{ asset('assets/img/logo.png') }}">

    <!-- Tailwind CSS via CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#5D5FEF', /* ChitChat purple */
                        secondary: '#4F46E5', /* Secondary purple */
                        success: '#28A745',
                        warning: '#FFC107',
                        error: '#DC3545',
                        light: '#F8F9FA',
                        dark: '#343A40',
                    }
                }
            }
        }
    </script>

    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

    <!-- Google Fonts - Inter -->
    <link href="https://fonts.googleapis.com" rel="preconnect">
    <link href="https://fonts.gstatic.com" rel="preconnect" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
        .hero-gradient {
            background: linear-gradient(135deg, #5D5FEF 0%, #4F46E5 50%, #6366F1 100%), url("{{ asset('assets/img/bg-pattern.png') }}");
            background-size: cover;
            background-position: center;
        }
        .feature-card {
            transition: all 0.3s ease;
        }
        .feature-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }
        .payment-button {
            transition: all 0.3s ease;
            animation: pulse 2s infinite;
        }
        .payment-button:hover {
            animation: none;
            transform: scale(1.05);
        }
        @keyframes pulse {
            0% {
                box-shadow: 0 0 0 0 rgba(255, 255, 255, 0.7);
            }
            70% {
                box-shadow: 0 0 0 10px rgba(255, 255, 255, 0);
            }
            100% {
                box-shadow: 0 0 0 0 rgba(255, 255, 255, 0);
            }
        }
        .step-card {
            position: relative;
        }
        .step-card::after {
            content: '';
            position: absolute;
            top: 2rem;
            right: -1rem;
            width: 2rem;
            height: 2px;
            background-color: #5D5FEF;
            display: none;
        }
        @media (min-width: 768px) {
            .step-card:not(:last-child)::after {
                display: block;
            }
        }
    </style>
</head>
<body class="bg-light">
    <!-- Header -->
    <header class="bg-white shadow-sm">
        <div class="container mx-auto px-4 py-4 flex justify-between items-center max-w-5xl">
            <a href="{{ url('/') }}" class="flex items-center">
                <img src="{{ asset('assets/img/logo.png') }}" alt="Logo" class="h-10">
            </a>
            <nav class="flex items-center space-x-2">
                <a href="{{ url('/login') }}" class="text-dark hover:text-primary font-medium px-4 py-2 rounded-lg hover:bg-gray-100 transition duration-300">Login</a>
            </nav>
        </div>
    </header>

    <!-- Registration Form Section -->
    <section class="py-12">
        <div class="container mx-auto px-4">
            <div class="max-w-5xl mx-auto">
                <div class="bg-white rounded-xl shadow-md overflow-hidden">
                    <div class="md:flex">
                        <!-- Left Side - Image/Info -->
                        <div class="md:w-1/3 hero-gradient text-white p-8 flex flex-col justify-between">
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
                                            <p class="text-sm text-white text-opacity-80">Your card details are never stored</p>
                                        </div>
                                    </div>

                                    <div class="flex items-start mb-5">
                                        <div class="flex-shrink-0 w-8 h-8 rounded-full bg-white bg-opacity-20 flex items-center justify-center mr-3">
                                            <i class="fas fa-bolt text-white"></i>
                                        </div>
                                        <div class="mb-5">
                                            <h3 class="font-semibold">Instant Transfers</h3>
                                            <p class="text-sm text-white text-opacity-80">Fund your wallet in seconds</p>
                                        </div>
                                    </div>

                                    <div class="flex items-start mb-5">
                                        <div class="flex-shrink-0 w-8 h-8 rounded-full bg-white bg-opacity-20 flex items-center justify-center mr-3">
                                            <i class="fas fa-mobile-alt text-white"></i>
                                        </div>
                                        <div class="mb-5">
                                            <h3 class="font-semibold">Multiple Providers</h3>
                                            <p class="text-sm text-white text-opacity-80">Support for all major mobile money services</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-8 text-sm text-white text-opacity-80">
                                Already have an account? <a href="{{ url('/login') }}" class="text-white underline">Login here</a>
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
                                        <input type="text" id="first_name" name="first_name" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-primary focus:border-primary" required>
                                        @error('first_name')
                                            <p class="text-error text-sm mt-1">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <!-- Last Name -->
                                    <div>
                                        <label for="last_name" class="block text-sm font-medium text-gray-700 mb-1">Last Name</label>
                                        <input type="text" id="last_name" name="last_name" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-primary focus:border-primary" required>
                                        @error('last_name')
                                            <p class="text-error text-sm mt-1">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Email and Phone Number -->
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div>
                                        <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
                                        <input type="email" id="email" name="email" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-primary focus:border-primary" required>
                                        @error('email')
                                            <p class="text-error text-sm mt-1">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <div>
                                        <label for="phone_number" class="block text-sm font-medium text-gray-700 mb-1">Phone Number</label>
                                        <input type="text" id="phone_number" name="phone_number" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-primary focus:border-primary" placeholder="+26097XXXXXXX" required>
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
                                        <input type="date" id="date_of_birth" name="date_of_birth" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-primary focus:border-primary" placeholder="Select your date of birth" required>
                                        @error('date_of_birth')
                                            <p class="text-error text-sm mt-1">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <div class="mt-3">
                                        <label for="address" class="block text-sm font-medium text-gray-700 mb-1">Address</label>
                                        <input type="text" id="address" name="address" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-primary focus:border-primary">
                                        @error('address')
                                            <p class="text-error text-sm mt-1">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <!-- City -->
                                    <div>
                                        <label for="city" class="block text-sm font-medium text-gray-700 mb-1">City</label>
                                        <input type="text" id="city" name="city" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-primary focus:border-primary">
                                        @error('city')
                                            <p class="text-error text-sm mt-1">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <!-- Country -->
                                    <div>
                                        <label for="country" class="block text-sm font-medium text-gray-700 mb-1">Country</label>
                                        <input type="text" id="country" name="country" value="Zambia" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-primary focus:border-primary" readonly>
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <!-- Password -->
                                    <div>
                                        <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                                        <div class="relative">
                                            <input type="password" id="password" name="password" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-primary focus:border-primary" required>
                                            <button type="button" class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-500 toggle-password">
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
                                            <input type="password" id="password_confirmation" name="password_confirmation" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-primary focus:border-primary" required>
                                            <button type="button" class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-500 toggle-password">
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
                                        <input id="terms" name="terms" type="checkbox" class="h-4 w-4 text-primary focus:ring-primary border-gray-300 rounded" required>
                                    </div>
                                    <div class="ml-3 text-sm">
                                        <label for="terms" class="text-gray-700">I agree to the <a href="{{ url('/terms') }}" class="text-primary hover:underline">Terms of Service</a> and <a href="{{ url('/privacy') }}" class="text-primary hover:underline">Privacy Policy</a></label>
                                        @error('terms')
                                            <p class="text-error text-sm mt-1">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Submit Button -->
                                <div>
                                    <button type="submit" class="w-full bg-primary text-white py-3 px-4 rounded-lg hover:bg-opacity-90 transition duration-300 font-medium" id="registerButton">
                                        Create Account
                                    </button>
                                </div>
                            </form>

                            <div class="mt-6 text-center text-sm text-gray-600">
                                Already have an account? <a href="{{ url('/login') }}" class="text-primary hover:underline font-medium">Login here</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-white py-6 mt-12">
        <div class="container mx-auto px-4">
            <div class="text-center text-gray-500 text-sm">
                <p>&copy; {{ date('Y') }} {{ config('app.name') }}. All Rights Reserved</p>
                <div class="mt-2 space-x-4">
                    <a href="{{ url('/terms') }}" class="text-gray-500 hover:text-primary">Terms of Service</a>
                    <a href="{{ url('/privacy') }}" class="text-gray-500 hover:text-primary">Privacy Policy</a>
                    <a href="{{ url('/contact') }}" class="text-gray-500 hover:text-primary">Contact Us</a>
                </div>
            </div>
        </div>
    </footer>

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
</body>
</html>
