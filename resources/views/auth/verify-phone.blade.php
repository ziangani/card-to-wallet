<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify Phone - {{ config('app.name') }}</title>
    <meta name="description" content="Verify your phone number to access your account">

    <!-- Favicon -->
    <link rel="icon" href="{{ asset('assets/img/logo.png') }}">

    <!-- Tailwind CSS via CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#3366CC',
                        secondary: '#FF9900',
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
        .auth-gradient {
            background: linear-gradient(135deg, #3366CC 0%, #4D7FD3 50%, #6699FF 100%);
        }
        .otp-input {
            width: 3rem;
            height: 3rem;
            font-size: 1.5rem;
            text-align: center;
            border-radius: 0.5rem;
            border: 1px solid #D1D5DB;
        }
        .otp-input:focus {
            outline: none;
            border-color: #3366CC;
            box-shadow: 0 0 0 3px rgba(51, 102, 204, 0.2);
        }
    </style>
</head>
<body class="bg-light">
    <!-- Header -->
    <header class="bg-white shadow-sm">
        <div class="container mx-auto px-4 py-4 flex justify-between items-center">
            <a href="{{ url('/') }}" class="flex items-center">
                <img src="{{ asset('assets/img/logo.png') }}" alt="Logo" class="h-10">
            </a>
            <nav class="flex items-center space-x-4">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="text-dark hover:text-primary font-medium px-4 py-2 rounded-lg hover:bg-gray-100 transition duration-300">Logout</button>
                </form>
            </nav>
        </div>
    </header>

    <!-- Phone Verification Section -->
    <section class="py-12">
        <div class="container mx-auto px-4">
            <div class="max-w-md mx-auto">
                <div class="bg-white rounded-xl shadow-md overflow-hidden">
                    <div class="auth-gradient text-white p-6 text-center">
                        <div class="w-20 h-20 bg-white bg-opacity-20 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-mobile-alt text-3xl"></i>
                        </div>
                        <h1 class="text-2xl font-bold mb-2">Verify Your Phone Number</h1>
                        <p class="text-white text-opacity-90">We've sent a verification code to your phone</p>
                    </div>
                    
                    <div class="p-6">
                        @if(session('status'))
                            <div class="mb-4 p-4 bg-success bg-opacity-10 text-success rounded-lg">
                                {{ session('status') }}
                            </div>
                        @endif
                        
                        @if(session('error'))
                            <div class="mb-4 p-4 bg-error bg-opacity-10 text-error rounded-lg">
                                {{ session('error') }}
                            </div>
                        @endif
                        
                        <div class="text-gray-600 mb-6">
                            <p class="mb-4">We've sent a 6-digit verification code to <strong>+260 {{ substr(auth()->user()->phone_number, -9) }}</strong>. Enter the code below to verify your phone number.</p>
                            <p>The code will expire in <span id="countdown" class="font-semibold">10:00</span> minutes.</p>
                        </div>
                        
                        <form method="POST" action="{{ route('phone.verify') }}" class="space-y-6">
                            @csrf
                            
                            <!-- OTP Input -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-3">Enter Verification Code</label>
                                <div class="flex justify-between gap-2">
                                    <input type="text" name="otp[]" maxlength="1" class="otp-input" required autofocus>
                                    <input type="text" name="otp[]" maxlength="1" class="otp-input" required>
                                    <input type="text" name="otp[]" maxlength="1" class="otp-input" required>
                                    <input type="text" name="otp[]" maxlength="1" class="otp-input" required>
                                    <input type="text" name="otp[]" maxlength="1" class="otp-input" required>
                                    <input type="text" name="otp[]" maxlength="1" class="otp-input" required>
                                </div>
                                <input type="hidden" id="full_otp" name="full_otp">
                                @error('otp')
                                    <p class="text-error text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <!-- Submit Button -->
                            <div>
                                <button type="submit" class="w-full bg-primary text-white py-3 px-4 rounded-lg hover:bg-opacity-90 transition duration-300 font-medium">
                                    Verify Phone Number
                                </button>
                            </div>
                        </form>
                        
                        <div class="mt-6 text-center">
                            <p class="text-gray-600 text-sm mb-4">Didn't receive the code?</p>
                            <form method="POST" action="{{ route('phone.resend') }}">
                                @csrf
                                <button type="submit" id="resend-button" class="text-primary hover:underline font-medium" disabled>
                                    Resend Code <span id="resend-timer">(10:00)</span>
                                </button>
                            </form>
                        </div>
                        
                        <div class="bg-gray-50 p-4 rounded-lg mt-6">
                            <div class="flex items-start">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-info-circle text-primary mt-1"></i>
                                </div>
                                <div class="ml-3">
                                    <h3 class="text-sm font-medium text-gray-700">Why verify your phone?</h3>
                                    <div class="mt-2 text-sm text-gray-600">
                                        <p>Phone verification helps us:</p>
                                        <ul class="list-disc pl-5 mt-1 space-y-1">
                                            <li>Confirm your identity</li>
                                            <li>Protect your account from unauthorized access</li>
                                            <li>Send important transaction notifications</li>
                                            <li>Enable higher transaction limits</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="mt-8 text-center">
                    <p class="text-gray-600 text-sm">
                        Need help? <a href="{{ url('/contact') }}" class="text-primary hover:underline">Contact Support</a>
                    </p>
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
        document.addEventListener('DOMContentLoaded', function() {
            // OTP input handling
            const otpInputs = document.querySelectorAll('.otp-input');
            const fullOtpInput = document.getElementById('full_otp');
            
            // Auto-focus next input
            otpInputs.forEach((input, index) => {
                input.addEventListener('input', function() {
                    if (this.value.length === this.maxLength) {
                        if (index < otpInputs.length - 1) {
                            otpInputs[index + 1].focus();
                        }
                    }
                    
                    // Update hidden full OTP field
                    updateFullOtp();
                });
                
                // Handle backspace
                input.addEventListener('keydown', function(e) {
                    if (e.key === 'Backspace' && this.value.length === 0) {
                        if (index > 0) {
                            otpInputs[index - 1].focus();
                        }
                    }
                });
                
                // Allow only numbers
                input.addEventListener('input', function() {
                    this.value = this.value.replace(/[^0-9]/g, '');
                });
            });
            
            function updateFullOtp() {
                let otp = '';
                otpInputs.forEach(input => {
                    otp += input.value;
                });
                fullOtpInput.value = otp;
            }
            
            // Countdown timer
            let countdownTime = 10 * 60; // 10 minutes in seconds
            const countdownEl = document.getElementById('countdown');
            const resendTimerEl = document.getElementById('resend-timer');
            const resendButton = document.getElementById('resend-button');
            
            function updateCountdown() {
                const minutes = Math.floor(countdownTime / 60);
                const seconds = countdownTime % 60;
                
                // Format time as MM:SS
                const formattedTime = `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
                
                countdownEl.textContent = formattedTime;
                resendTimerEl.textContent = `(${formattedTime})`;
                
                if (countdownTime <= 0) {
                    clearInterval(countdownInterval);
                    resendButton.disabled = false;
                    resendTimerEl.textContent = '';
                } else {
                    countdownTime--;
                }
            }
            
            // Update countdown every second
            updateCountdown();
            const countdownInterval = setInterval(updateCountdown, 1000);
        });
    </script>
</body>
</html>
