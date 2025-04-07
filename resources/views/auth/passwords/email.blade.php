@extends('layouts.auth')

@section('title', 'Reset Password - ' . config('app.name'))

@section('content')
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
@endsection
