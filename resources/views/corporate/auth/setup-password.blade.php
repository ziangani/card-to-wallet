@extends('layouts.auth')

@section('title', 'Set Up Password - Corporate Portal')

@section('content')
<div class="max-w-md mx-auto">
    <div class="text-center mb-8">
        <h1 class="text-2xl font-bold text-dark mb-2">Set Up Your Password</h1>
        <p class="text-gray-600">Please create a secure password for your corporate account</p>
    </div>

    <div class="bg-white rounded-lg shadow-card p-6">
        <form method="POST" action="{{ route('corporate.setup-password.store') }}" class="space-y-6">
            @csrf
            <input type="hidden" name="email" value="{{ $email }}">
            <input type="hidden" name="token" value="{{ $token }}">

            <div>
                <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
                <input type="text" value="{{ $email }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-gray-100" readonly disabled>
            </div>

            <div>
                <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                <div class="relative">
                    <input type="password"
                           id="password"
                           name="password"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary @error('password') border-error @enderror"
                           required
                           autocomplete="new-password">
                    @error('password')
                        <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                            <i class="fas fa-exclamation-circle text-error"></i>
                        </div>
                    @enderror
                </div>
                @error('password')
                    <p class="mt-1 text-sm text-error">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">Confirm Password</label>
                <input type="password"
                       id="password_confirmation"
                       name="password_confirmation"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary"
                       required
                       autocomplete="new-password">
            </div>

            <div class="pt-2">
                <button type="submit" class="w-full bg-primary hover:bg-corporate-primary-dark text-white font-semibold py-2 px-4 rounded-lg transition-colors shadow-button">
                    Set Password and Continue
                </button>
            </div>
        </form>
    </div>

    <div class="text-center mt-6">
        <p class="text-sm text-gray-600">
            Already have an account?
            <a href="{{ route('login') }}" class="text-primary hover:text-corporate-primary-dark font-semibold">
                Sign in
            </a>
        </p>
    </div>
</div>
@endsection

@push('styles')
<style>
    .auth-gradient {
        background: linear-gradient(135deg, #007751 0%, #005a3d 50%, #007751 100%);
    }
</style>
@endpush
