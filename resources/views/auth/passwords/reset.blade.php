@extends('layouts.auth')

@section('title', 'Reset Password - ' . config('app.name'))

@section('content')
<div class="bg-white rounded-xl shadow-md overflow-hidden">
    <div class="auth-gradient text-white p-6 text-center">
        <h1 class="text-2xl font-bold mb-2">Reset Your Password</h1>
        <p class="text-white text-opacity-90">Create a new secure password for your account</p>
    </div>
    
    <div class="p-6">
        @if(session('status'))
            <div class="mb-4 p-4 bg-success bg-opacity-10 text-success rounded-lg">
                {{ session('status') }}
            </div>
        @endif
        
        <form action="{{ route('password.update') }}" method="POST" class="space-y-6">
            @csrf
            
            <!-- Password Reset Token -->
            <input type="hidden" name="token" value="{{ $token }}">
            
            <!-- Email -->
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
                <input type="email" id="email" name="email" value="{{ $email ?? old('email') }}" class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-primary focus:border-primary" required autofocus readonly>
                @error('email')
                    <p class="text-error text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
            
            <!-- Password -->
            <div>
                <label for="password" class="block text-sm font-medium text-gray-700 mb-1">New Password</label>
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
            
            <!-- Confirm Password -->
            <div>
                <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">Confirm Password</label>
                <div class="relative">
                    <input type="password" id="password_confirmation" name="password_confirmation" class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-primary focus:border-primary" required>
                    <button type="button" class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-500 toggle-password">
                        <i class="fas fa-eye"></i>
                    </button>
                </div>
            </div>
            
            <!-- Password Requirements -->
            <div class="bg-gray-50 p-4 rounded-lg">
                <p class="text-sm font-medium text-gray-700 mb-2">Password Requirements:</p>
                <ul class="text-sm text-gray-600 space-y-1">
                    <li class="flex items-center">
                        <i class="fas fa-check-circle text-success mr-2"></i>
                        At least 8 characters long
                    </li>
                    <li class="flex items-center">
                        <i class="fas fa-check-circle text-success mr-2"></i>
                        Contains uppercase and lowercase letters
                    </li>
                    <li class="flex items-center">
                        <i class="fas fa-check-circle text-success mr-2"></i>
                        Contains at least one number
                    </li>
                    <li class="flex items-center">
                        <i class="fas fa-check-circle text-success mr-2"></i>
                        Contains at least one special character
                    </li>
                </ul>
            </div>
            
            <!-- Submit Button -->
            <div>
                <button type="submit" class="w-full bg-primary text-white py-3 px-4 rounded-lg hover:bg-opacity-90 transition duration-300 font-medium">
                    Reset Password
                </button>
            </div>
        </form>
    </div>
</div>

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
        
        // Update password requirement checks
        const requirements = document.querySelectorAll('.bg-gray-50 li');
        
        passwordInput.addEventListener('input', function() {
            const password = this.value;
            
            // Check length
            if (password.length >= 8) {
                requirements[0].querySelector('i').classList.add('text-success');
                requirements[0].querySelector('i').classList.remove('text-gray-400');
            } else {
                requirements[0].querySelector('i').classList.remove('text-success');
                requirements[0].querySelector('i').classList.add('text-gray-400');
            }
            
            // Check uppercase and lowercase
            if (/[a-z]/.test(password) && /[A-Z]/.test(password)) {
                requirements[1].querySelector('i').classList.add('text-success');
                requirements[1].querySelector('i').classList.remove('text-gray-400');
            } else {
                requirements[1].querySelector('i').classList.remove('text-success');
                requirements[1].querySelector('i').classList.add('text-gray-400');
            }
            
            // Check number
            if (/[0-9]/.test(password)) {
                requirements[2].querySelector('i').classList.add('text-success');
                requirements[2].querySelector('i').classList.remove('text-gray-400');
            } else {
                requirements[2].querySelector('i').classList.remove('text-success');
                requirements[2].querySelector('i').classList.add('text-gray-400');
            }
            
            // Check special character
            if (/[^A-Za-z0-9]/.test(password)) {
                requirements[3].querySelector('i').classList.add('text-success');
                requirements[3].querySelector('i').classList.remove('text-gray-400');
            } else {
                requirements[3].querySelector('i').classList.remove('text-success');
                requirements[3].querySelector('i').classList.add('text-gray-400');
            }
        });
    });
</script>
@endpush
@endsection
