<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - {{ config('app.name') }}</title>
    <meta name="description" content="Log in to your account to fund mobile wallets with your card">

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
            background: linear-gradient(135deg, #5D5FEF 0%, #4F46E5 50%, #6366F1 100%);
        }
    </style>
</head>
<body class="bg-light">
    <!-- Header -->
    <header class="bg-white shadow-sm">
        <div class="container mx-auto px-4 py-4 flex justify-between items-center max-w-4xl">
            <a href="{{ url('/') }}" class="flex items-center">
                <img src="{{ asset('assets/img/logo.png') }}" alt="Logo" class="h-10">
            </a>
            <nav class="flex items-center space-x-2">
                <a href="{{ url('/register') }}" class="bg-primary text-white font-medium px-4 py-2 rounded-lg hover:bg-opacity-90 transition duration-300">Register</a>
            </nav>
        </div>
    </header>

    <!-- Login Form Section -->
    <section class="py-12">
        <div class="container mx-auto px-4">
            <div class="max-w-4xl mx-auto">
                <div class="bg-white rounded-xl shadow-md overflow-hidden">
                    <div class="md:flex">
                        <!-- Left Side - Image/Info -->
                        <div class="md:w-1/2 auth-gradient text-white p-8 flex flex-col justify-between">
                            <div>
                                <h2 class="text-2xl font-bold mb-4">Welcome Back!</h2>
                                <p class="mb-6">Log in to your account to fund mobile wallets instantly with your card.</p>
                                
                                <div class="mt-8">
                                    <img src="{{ asset('assets/img/woman-with-phone.png') }}" alt="Login Illustration" class="w-full max-w-xs mx-auto" onerror="this.src='https://placehold.co/300x200?text=Card+to+Wallet'">
                                </div>
                            </div>
                            
                            <div class="mt-8 text-sm text-white text-opacity-80">
                                Don't have an account? <a href="{{ url('/register') }}" class="text-white underline">Register here</a>
                            </div>
                        </div>
                        
                        <!-- Right Side - Form -->
                        <div class="md:w-1/2 p-8">
                            <h1 class="text-2xl font-bold text-dark mb-6">Login to Your Account</h1>
                            
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
                            
                            <form action="{{ route('login') }}" method="POST" class="space-y-6">
                                @csrf
                                
                                <!-- Email/Phone -->
                                <div>
                                    <label for="login" class="block text-sm font-medium text-gray-700 mb-1">Email or Phone Number</label>
                                    <input type="text" id="login" name="login" class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-primary focus:border-primary" required autofocus>
                                    @error('login')
                                        <p class="text-error text-sm mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                                
                                <!-- Password -->
                                <div>
                                    <div class="flex justify-between items-center mb-1">
                                        <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                                        <a href="{{ route('password.request') }}" class="text-sm text-primary hover:underline">Forgot password?</a>
                                    </div>
                                    <div class="relative">
                                        <input type="password" id="password" name="password" class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-primary focus:border-primary" required>
                                        <button type="button" class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-500 toggle-password">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </div>
                                    @error('password')
                                        <p class="text-error text-sm mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                                
                                <!-- Remember Me -->
                                <div class="flex items-center">
                                    <input id="remember" name="remember" type="checkbox" class="h-4 w-4 text-primary focus:ring-primary border-gray-300 rounded">
                                    <label for="remember" class="ml-2 block text-sm text-gray-700">
                                        Remember me
                                    </label>
                                </div>
                                
                                <!-- Submit Button -->
                                <div>
                                    <button type="submit" class="w-full bg-primary text-white py-3 px-4 rounded-lg hover:bg-opacity-90 transition duration-300 font-medium">
                                        Log In
                                    </button>
                                </div>
                            </form>
                            
                            <div class="mt-6">
                                <div class="relative">
                                    <div class="absolute inset-0 flex items-center">
                                        <div class="w-full border-t border-gray-300"></div>
                                    </div>
                                    <div class="relative flex justify-center text-sm">
                                        <span class="px-2 bg-white text-gray-500">Or continue with</span>
                                    </div>
                                </div>
                                
                                <div class="mt-6 grid grid-cols-2 gap-3">
                                    <a href="#" class="w-full inline-flex justify-center py-3 px-4 border border-gray-300 rounded-lg shadow-sm bg-white text-sm font-medium text-gray-700 hover:bg-gray-50">
                                        <i class="fab fa-google text-red-500 mr-2"></i>
                                        Google
                                    </a>
                                    <a href="#" class="w-full inline-flex justify-center py-3 px-4 border border-gray-300 rounded-lg shadow-sm bg-white text-sm font-medium text-gray-700 hover:bg-gray-50">
                                        <i class="fab fa-facebook text-blue-600 mr-2"></i>
                                        Facebook
                                    </a>
                                </div>
                            </div>
                            
                            <div class="mt-6 text-center text-sm text-gray-600">
                                Don't have an account? <a href="{{ url('/register') }}" class="text-primary hover:underline font-medium">Register here</a>
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
            const toggleButton = document.querySelector('.toggle-password');
            
            toggleButton.addEventListener('click', function() {
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
    </script>
</body>
</html>
