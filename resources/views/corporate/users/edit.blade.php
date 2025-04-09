@extends('corporate.layouts.app')

@section('title', 'Edit User')

@section('content')
<div class="mb-6">
    <div class="flex items-center mb-2">
        <a href="{{ route('corporate.users.index') }}" class="text-primary hover:underline">
            <i class="fas fa-arrow-left mr-2"></i> Back to Users
        </a>
    </div>
    <h2 class="text-xl font-bold text-gray-800">Edit User</h2>
    <p class="text-gray-500">Manage user details and roles</p>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Main Content -->
    <div class="lg:col-span-2">
        <div class="bg-white rounded-xl shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="font-semibold text-gray-800">User Information</h3>
            </div>
            <div class="p-6">
                <form action="{{ route('corporate.users.update', $editUser->id) }}" method="POST" class="space-y-6">
                    @csrf
                    @method('PUT')
                    
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Full Name <span class="text-red-600">*</span></label>
                        <input type="text" id="name" name="name" value="{{ old('name', $editUser->name) }}" required class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-primary focus:border-primary @error('name') border-red-500 @enderror">
                        @error('name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
                        <input type="email" id="email" value="{{ $editUser->email }}" disabled readonly class="w-full px-3 py-2 border border-gray-200 rounded-lg bg-gray-50 text-gray-500">
                        <p class="mt-1 text-xs text-gray-500">Email address cannot be changed</p>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Roles <span class="text-red-600">*</span></label>
                        <div class="mt-2 space-y-3">
                            @foreach($roles as $role)
                                <div class="flex items-start">
                                    <div class="flex items-center h-5">
                                        <input id="role_{{ $role->id }}" name="roles[]" type="checkbox" value="{{ $role->id }}" class="h-4 w-4 text-primary focus:ring-primary border-gray-300 rounded" {{ in_array($role->id, old('roles', $userRoles)) ? 'checked' : '' }}>
                                    </div>
                                    <div class="ml-3 text-sm">
                                        <label for="role_{{ $role->id }}" class="font-medium text-gray-700">{{ ucfirst($role->name) }}</label>
                                        <p class="text-gray-500">{{ $role->description }}</p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        @error('roles')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label for="primary_role" class="block text-sm font-medium text-gray-700 mb-1">Primary Role <span class="text-red-600">*</span></label>
                        <select id="primary_role" name="primary_role" required class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-primary focus:border-primary @error('primary_role') border-red-500 @enderror">
                            <option value="">Select Primary Role</option>
                            @foreach($roles as $role)
                                <option value="{{ $role->id }}" {{ old('primary_role', $primaryRoleId) == $role->id ? 'selected' : '' }}>{{ ucfirst($role->name) }}</option>
                            @endforeach
                        </select>
                        @error('primary_role')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-xs text-gray-500">The primary role determines the user's main function in the system</p>
                    </div>
                    
                    <div>
                        <label for="is_active" class="block text-sm font-medium text-gray-700 mb-1">Account Status <span class="text-red-600">*</span></label>
                        <div class="mt-2">
                            <div class="flex items-center">
                                <input id="is_active_1" name="is_active" type="radio" value="1" class="h-4 w-4 text-primary focus:ring-primary border-gray-300" {{ old('is_active', $editUser->is_active) ? 'checked' : '' }}>
                                <label for="is_active_1" class="ml-3 block text-sm font-medium text-gray-700">
                                    Active
                                </label>
                            </div>
                            <div class="flex items-center mt-2">
                                <input id="is_active_0" name="is_active" type="radio" value="0" class="h-4 w-4 text-primary focus:ring-primary border-gray-300" {{ old('is_active', $editUser->is_active) ? '' : 'checked' }}>
                                <label for="is_active_0" class="ml-3 block text-sm font-medium text-gray-700">
                                    Inactive
                                </label>
                            </div>
                        </div>
                        @error('is_active')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-xs text-gray-500">Inactive users cannot log in to the system</p>
                    </div>
                    
                    <div class="flex justify-end space-x-3 pt-4">
                        <a href="{{ route('corporate.users.index') }}" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                            Cancel
                        </a>
                        <button type="submit" class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-opacity-90">
                            Save Changes
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Sidebar -->
    <div class="space-y-6">
        <!-- User Information -->
        <div class="bg-white rounded-xl shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="font-semibold text-gray-800">User Details</h3>
            </div>
            <div class="p-6">
                <div class="flex items-center mb-6">
                    <div class="w-16 h-16 rounded-full bg-primary text-white flex items-center justify-center text-xl mr-4">
                        {{ strtoupper(substr($editUser->name, 0, 1)) }}
                    </div>
                    <div>
                        <h4 class="text-lg font-medium text-gray-900">{{ $editUser->name }}</h4>
                        <p class="text-sm text-gray-500">{{ $editUser->email }}</p>
                    </div>
                </div>
                
                <div class="space-y-3">
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-500">Member Since</span>
                        <span class="text-sm font-medium text-gray-900">{{ $editUser->created_at->format('M d, Y') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-500">Email Verified</span>
                        <span class="text-sm font-medium text-gray-900">
                            @if($editUser->email_verified_at)
                                <span class="text-green-600">
                                    <i class="fas fa-check-circle mr-1"></i> Yes
                                </span>
                            @else
                                <span class="text-yellow-600">
                                    <i class="fas fa-times-circle mr-1"></i> No
                                </span>
                            @endif
                        </span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-500">Phone Verified</span>
                        <span class="text-sm font-medium text-gray-900">
                            @if($editUser->is_phone_verified)
                                <span class="text-green-600">
                                    <i class="fas fa-check-circle mr-1"></i> Yes
                                </span>
                            @else
                                <span class="text-yellow-600">
                                    <i class="fas fa-times-circle mr-1"></i> No
                                </span>
                            @endif
                        </span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-500">Last Login</span>
                        <span class="text-sm font-medium text-gray-900">
                            {{ $editUser->last_login_at ? $editUser->last_login_at->format('M d, Y h:i A') : 'Never' }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Role Information -->
        <div class="bg-white rounded-xl shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="font-semibold text-gray-800">Role Information</h3>
            </div>
            <div class="p-6">
                <div class="space-y-4">
                    <div>
                        <h4 class="text-base font-medium text-gray-900 mb-2">Admin</h4>
                        <p class="text-sm text-gray-600">Full control of corporate account, users, and transactions. Can manage company settings, invite users, and approve transactions.</p>
                    </div>
                    
                    <div>
                        <h4 class="text-base font-medium text-gray-900 mb-2">Approver</h4>
                        <p class="text-sm text-gray-600">Can approve transactions and user management actions. Cannot modify company settings or invite new users.</p>
                    </div>
                    
                    <div>
                        <h4 class="text-base font-medium text-gray-900 mb-2">Initiator</h4>
                        <p class="text-sm text-gray-600">Can initiate transactions but requires approval. Limited access to view company information and transaction history.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const roleCheckboxes = document.querySelectorAll('input[name="roles[]"]');
        const primaryRoleSelect = document.getElementById('primary_role');
        
        // Update primary role options based on selected roles
        function updatePrimaryRoleOptions() {
            const selectedRoles = Array.from(roleCheckboxes)
                .filter(checkbox => checkbox.checked)
                .map(checkbox => checkbox.value);
            
            // Store current selection
            const currentSelection = primaryRoleSelect.value;
            
            // Clear all options except the placeholder
            while (primaryRoleSelect.options.length > 1) {
                primaryRoleSelect.remove(1);
            }
            
            // Add options for selected roles
            @foreach($roles as $role)
                if (selectedRoles.includes('{{ $role->id }}')) {
                    const option = new Option('{{ ucfirst($role->name) }}', '{{ $role->id }}');
                    primaryRoleSelect.add(option);
                }
            @endforeach
            
            // Restore selection if still valid
            if (selectedRoles.includes(currentSelection)) {
                primaryRoleSelect.value = currentSelection;
            } else {
                primaryRoleSelect.selectedIndex = 0;
            }
        }
        
        // Initialize on page load
        updatePrimaryRoleOptions();
        
        // Update when roles change
        roleCheckboxes.forEach(checkbox => {
            checkbox.addEventListener('change', updatePrimaryRoleOptions);
        });
    });
</script>
@endpush
