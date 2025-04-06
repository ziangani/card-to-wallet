@extends('layouts.auth')

@section('title', 'Login - ' . config('app.name'))
@section('meta_description', 'Log in to your account to fund mobile wallets with your card')

@section('header_nav')
<a href="{{ url('/register') }}" class="bg-primary text-white font-medium px-4 py-2 rounded-lg hover:bg-opacity-90 transition duration-300 shadow-button">Register</a>
@endsection

@section('content')
<div class="bg-white rounded-xl shadow-card overflow-hidden">
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
            
            <div class="mt-8 text-sm text-white text-opacity-90">
                Don't have an account? <a href="{{ url('/register') }}" class="text-white underline font-medium hover:text-secondary transition duration-300">Register here</a>
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
                    <input type="text" id="login" name="login" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary transition duration-200" required autofocus>
                    @error('login')
                        <p class="text-error text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <!-- Password -->
                <div>
                    <div class="flex justify-between items-center mb-1">
                        <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                        <a href="{{ route('password.request') }}" class="text-sm text-primary hover:underline transition duration-200">Forgot password?</a>
                    </div>
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
                
                <!-- Remember Me -->
                <div class="flex items-center">
                    <input id="remember" name="remember" type="checkbox" class="h-4 w-4 text-primary focus:ring-primary border-gray-300 rounded transition duration-200">
                    <label for="remember" class="ml-2 block text-sm text-gray-700">
                        Remember me
                    </label>
                </div>
                
                <!-- Submit Button -->
                <div>
                    <button type="submit" class="w-full bg-primary text-white py-3 px-4 rounded-lg hover:bg-opacity-90 transition duration-300 font-medium shadow-button">
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
                    <a href="#" class="w-full inline-flex justify-center py-3 px-4 border border-gray-300 rounded-lg shadow-sm bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 hover:border-secondary transition duration-300">
                        <i class="fab fa-google text-secondary mr-2"></i>
                        Google
                    </a>
                    <a href="#" class="w-full inline-flex justify-center py-3 px-4 border border-gray-300 rounded-lg shadow-sm bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 hover:border-primary transition duration-300">
                        <i class="fab fa-facebook text-primary mr-2"></i>
                        Facebook
                    </a>
                </div>
            </div>
            
            <div class="mt-6 text-center text-sm text-gray-600">
                Don't have an account? <a href="{{ url('/register') }}" class="text-primary hover:underline font-medium transition duration-300">Register here</a>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
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
@endpush
