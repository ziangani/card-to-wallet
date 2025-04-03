<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - {{ config('app.name') }}</title>
    <meta name="description" content="Reset your password to regain access to your account">

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
    </style>
</head>
<body class="bg-light">
    <!-- Header -->
    <header class="bg-white shadow-sm">
        <div class="container mx-auto px-4 py-4 flex justify-between items-center">
            <a href="{{ url('/') }}" class="flex items-center">
                <img src="{{ asset('assets/img/logo.png') }}" alt="Logo" class="h-10">
            </a>
            <nav class="flex items-center space-x-2">
                <a href="{{ url('/login') }}" class="text-dark hover:text-primary font-medium px-4 py-2 rounded-lg hover:bg-gray-100 transition duration-300">Login</a>
                <a href="{{ url('/register') }}" class="bg-primary text-white font-medium px-4 py-2 rounded-lg hover:bg-opacity-90 transition duration-300">Register</a>
            </nav>
        </div>
    </header>

    <!-- Password Reset Form Section -->
    <section class="py-12">
        <div class="container mx-auto px-4">
            <div class="max-w-md mx-auto">
                <div class="bg-white rounded-xl shadow-md overflow-hidden">
                    <div class="auth-gradient text-white p-6 text-center">
                        <h1 class="text-2xl font-bold mb-2">Forgot Your Password?</h1>
                        <p class="text-white text-opacity-90">No worries, we'll send you reset instructions</p>
                    </div>
                    
                    <div class="p-6">
                        @if(session('status'))
                            <div class="mb-4 p-4 bg-success bg-opacity-10 text-success rounded-lg">
                                {{ session('status') }}
                            </div>
                        @endif
                        
                        <p class="text-gray-600 mb-6">Enter your email address and we'll send you a link to reset your password.</p>
                        
                        <form action="{{ route('password.email') }}" method="POST" class="space-y-6">
                            @csrf
                            
                            <!-- Email -->
                            <div>
                                <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
                                <input type="email" id="email" name="email" class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-primary focus:border-primary" required autofocus>
                                @error('email')
                                    <p class="text-error text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <!-- Submit Button -->
                            <div>
                                <button type="submit" class="w-full bg-primary text-white py-3 px-4 rounded-lg hover:bg-opacity-90 transition duration-300 font-medium">
                                    Send Reset Link
                                </button>
                            </div>
                        </form>
                        
                        <div class="mt-6 text-center">
                            <a href="{{ url('/login') }}" class="text-primary hover:underline inline-flex items-center">
                                <i class="fas fa-arrow-left mr-2"></i> Back to Login
                            </a>
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
</body>
</html>
