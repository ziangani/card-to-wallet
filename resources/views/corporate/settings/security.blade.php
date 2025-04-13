@extends('corporate.layouts.app')

@section('title', 'Security Settings')

@section('content')
<div class="mb-6">
    <h2 class="text-xl font-bold text-gray-800">Company Settings</h2>
    <p class="text-gray-500">Manage your company settings and preferences</p>
</div>

<div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
    <!-- Sidebar Navigation -->
    <div class="lg:col-span-1">
        <div class="bg-white rounded-xl shadow-sm overflow-hidden">
            <div class="p-6 border-b border-gray-100">
                <h2 class="text-lg font-bold text-dark">Settings</h2>
            </div>
            <div class="p-4">
                <nav class="space-y-1">
                    <a href="{{ route('corporate.settings.profile') }}" class="flex items-center px-4 py-3 text-dark hover:bg-gray-50 rounded-lg transition-colors">
                        <i class="fas fa-building w-6 text-gray-500"></i>
                        <span>Company Profile</span>
                    </a>
                    <a href="{{ route('corporate.settings.security') }}" class="flex items-center px-4 py-3 text-dark bg-primary bg-opacity-10 rounded-lg">
                        <i class="fas fa-shield-alt w-6 text-primary"></i>
                        <span class="font-medium">Security</span>
                    </a>
                    <a href="{{ route('corporate.settings.roles') }}" class="flex items-center px-4 py-3 text-dark hover:bg-gray-50 rounded-lg transition-colors">
                        <i class="fas fa-user-tag w-6 text-gray-500"></i>
                        <span>User Roles</span>
                    </a>
                    <a href="{{ route('corporate.settings.approvals') }}" class="flex items-center px-4 py-3 text-dark hover:bg-gray-50 rounded-lg transition-colors">
                        <i class="fas fa-check-double w-6 text-gray-500"></i>
                        <span>Approval Workflows</span>
                    </a>
                    <a href="{{ route('corporate.settings.rates') }}" class="flex items-center px-4 py-3 text-dark hover:bg-gray-50 rounded-lg transition-colors">
                        <i class="fas fa-percentage w-6 text-gray-500"></i>
                        <span>Rate Settings</span>
                    </a>
                </nav>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="lg:col-span-3">
        <!-- Change Password Form -->
<div class="bg-white rounded-xl shadow-sm overflow-hidden">
    <div class="p-6 border-b border-gray-200">
        <h3 class="text-lg font-semibold text-gray-800">Change Password</h3>
        <p class="text-sm text-gray-500">Update your account password</p>
    </div>
    
    <form action="{{ route('corporate.settings.update-password') }}" method="POST" class="p-6 space-y-4">
        @csrf
        @method('PUT')
        
        <div>
            <label for="current_password" class="block text-sm font-medium text-gray-700 mb-1">Current Password</label>
            <input type="password" id="current_password" name="current_password" class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-primary focus:border-primary">
            @error('current_password')
                <p class="mt-1 text-sm text-error">{{ $message }}</p>
            @enderror
        </div>
        
        <div>
            <label for="password" class="block text-sm font-medium text-gray-700 mb-1">New Password</label>
            <input type="password" id="password" name="password" class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-primary focus:border-primary">
            @error('password')
                <p class="mt-1 text-sm text-error">{{ $message }}</p>
            @enderror
        </div>
        
        <div>
            <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">Confirm New Password</label>
            <input type="password" id="password_confirmation" name="password_confirmation" class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-primary focus:border-primary">
        </div>
        
        <div class="pt-4">
            <button type="submit" class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-opacity-90">
                <i class="fas fa-key mr-2"></i> Update Password
            </button>
        </div>
    </form>
</div>

<!-- Security Tips -->
<div class="bg-white rounded-xl shadow-sm overflow-hidden mt-6">
    <div class="p-6 border-b border-gray-200">
        <h3 class="text-lg font-semibold text-gray-800">Security Tips</h3>
        <p class="text-sm text-gray-500">Recommendations to keep your account secure</p>
    </div>
    
    <div class="p-6 space-y-4">
        <div class="flex items-start">
            <div class="flex-shrink-0 w-10 h-10 rounded-full bg-primary bg-opacity-10 flex items-center justify-center text-primary mr-4">
                <i class="fas fa-shield-alt"></i>
            </div>
            <div>
                <h4 class="font-medium text-gray-800">Use a strong password</h4>
                <p class="text-sm text-gray-600">Create a unique password that includes a mix of letters, numbers, and special characters. Avoid using easily guessable information like birthdays or names.</p>
            </div>
        </div>
        
        <div class="flex items-start">
            <div class="flex-shrink-0 w-10 h-10 rounded-full bg-primary bg-opacity-10 flex items-center justify-center text-primary mr-4">
                <i class="fas fa-sync-alt"></i>
            </div>
            <div>
                <h4 class="font-medium text-gray-800">Change your password regularly</h4>
                <p class="text-sm text-gray-600">We recommend changing your password every 90 days to maintain account security.</p>
            </div>
        </div>
        
        <div class="flex items-start">
            <div class="flex-shrink-0 w-10 h-10 rounded-full bg-primary bg-opacity-10 flex items-center justify-center text-primary mr-4">
                <i class="fas fa-user-lock"></i>
            </div>
            <div>
                <h4 class="font-medium text-gray-800">Don't share your credentials</h4>
                <p class="text-sm text-gray-600">Never share your login information with others. Each user should have their own account with appropriate permissions.</p>
            </div>
        </div>
        
        <div class="flex items-start">
            <div class="flex-shrink-0 w-10 h-10 rounded-full bg-primary bg-opacity-10 flex items-center justify-center text-primary mr-4">
                <i class="fas fa-sign-out-alt"></i>
            </div>
            <div>
                <h4 class="font-medium text-gray-800">Log out when not in use</h4>
                <p class="text-sm text-gray-600">Always log out of your account when you're finished, especially when using shared or public computers.</p>
            </div>
        </div>
    </div>
</div>

<!-- Recent Account Activity -->
<div class="bg-white rounded-xl shadow-sm overflow-hidden mt-6">
    <div class="p-6 border-b border-gray-200">
        <h3 class="text-lg font-semibold text-gray-800">Recent Account Activity</h3>
        <p class="text-sm text-gray-500">Recent logins and security events</p>
    </div>
    
    <div class="p-6">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50">
                        <th class="px-6 py-3 text-left font-medium text-gray-500">Activity</th>
                        <th class="px-6 py-3 text-left font-medium text-gray-500">IP Address</th>
                        <th class="px-6 py-3 text-left font-medium text-gray-500">Location</th>
                        <th class="px-6 py-3 text-left font-medium text-gray-500">Date & Time</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4">
                            <div class="flex items-center">
                                <i class="fas fa-sign-in-alt text-primary mr-2"></i>
                                <span>Login successful</span>
                            </div>
                        </td>
                        <td class="px-6 py-4">{{ request()->ip() }}</td>
                        <td class="px-6 py-4">Unknown</td>
                        <td class="px-6 py-4">{{ now()->format('M d, Y h:i A') }}</td>
                    </tr>
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4">
                            <div class="flex items-center">
                                <i class="fas fa-key text-primary mr-2"></i>
                                <span>Password changed</span>
                            </div>
                        </td>
                        <td class="px-6 py-4">{{ request()->ip() }}</td>
                        <td class="px-6 py-4">Unknown</td>
                        <td class="px-6 py-4">{{ now()->subDays(5)->format('M d, Y h:i A') }}</td>
                    </tr>
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4">
                            <div class="flex items-center">
                                <i class="fas fa-sign-in-alt text-primary mr-2"></i>
                                <span>Login successful</span>
                            </div>
                        </td>
                        <td class="px-6 py-4">{{ request()->ip() }}</td>
                        <td class="px-6 py-4">Unknown</td>
                        <td class="px-6 py-4">{{ now()->subDays(5)->format('M d, Y h:i A') }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
