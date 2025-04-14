@extends('corporate.layouts.app')

@section('title', 'Invite User')

@section('content')
<div class="mb-6">
    <div class="flex items-center mb-2">
        <a href="{{ route('corporate.users.index') }}" class="text-primary hover:underline">
            <i class="fas fa-arrow-left mr-2"></i> Back to Users
        </a>
    </div>
    <h2 class="text-xl font-bold text-gray-800">Invite New User</h2>
    <p class="text-gray-500">Add a new team member to your company</p>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Main Content -->
    <div class="lg:col-span-2">
        <div class="bg-white rounded-xl shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="font-semibold text-gray-800">User Information</h3>
            </div>
            <div class="p-6">
                <form action="{{ route('corporate.users.process-invite') }}" method="POST" class="space-y-6">
                    @csrf

                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Full Name <span class="text-red-600">*</span></label>
                        <input type="text" id="name" name="name" value="{{ old('name') }}" required class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-primary focus:border-primary @error('name') border-red-500 @enderror">
                        @error('name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email Address <span class="text-red-600">*</span></label>
                        <input type="email" id="email" name="email" value="{{ old('email') }}" required class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-primary focus:border-primary @error('email') border-red-500 @enderror">
                        @error('email')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-xs text-gray-500">The user will receive an invitation email at this address</p>
                    </div>

                    <div>
                        <label for="phone_number" class="block text-sm font-medium text-gray-700 mb-1">Phone Number <span class="text-red-600">*</span></label>
                        <input type="text" id="phone_number" name="phone_number" value="{{ old('phone_number') }}" required
                               class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-primary focus:border-primary @error('phone_number') border-red-500 @enderror"
                               placeholder="+1234567890">
                        @error('phone_number')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-xs text-gray-500">International format with country code (e.g., +1234567890)</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Roles <span class="text-red-600">*</span></label>
                        <div class="mt-2 space-y-3">
                            @foreach($roles as $role)
                                <div class="flex items-start">
                                    <div class="flex items-center h-5">
                                        <input id="role_{{ $role->id }}" name="roles[]" type="checkbox" value="{{ $role->id }}" class="h-4 w-4 text-primary focus:ring-primary border-gray-300 rounded" {{ in_array($role->id, old('roles', [])) ? 'checked' : '' }}>
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
                                <option value="{{ $role->id }}" {{ old('primary_role') == $role->id ? 'selected' : '' }}>{{ ucfirst($role->name) }}</option>
                            @endforeach
                        </select>
                        @error('primary_role')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-xs text-gray-500">The primary role determines the user's main function in the system</p>
                    </div>

                    <div>
                        <label for="message" class="block text-sm font-medium text-gray-700 mb-1">Personal Message (Optional)</label>
                        <textarea id="message" name="message" rows="3" class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-primary focus:border-primary @error('message') border-red-500 @enderror">{{ old('message') }}</textarea>
                        @error('message')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-xs text-gray-500">This message will be included in the invitation email</p>
                    </div>

                    <div class="flex justify-end space-x-3 pt-4">
                        <a href="{{ route('corporate.users.index') }}" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                            Cancel
                        </a>
                        <button type="submit" class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-opacity-90">
                            Send Invitation
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Sidebar -->
    <div class="space-y-6">
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

        <!-- Tips -->
        <div class="bg-white rounded-xl shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="font-semibold text-gray-800">Tips</h3>
            </div>
            <div class="p-6">
                <ul class="space-y-3 text-sm text-gray-600">
                    <li class="flex">
                        <i class="fas fa-info-circle text-primary mt-1 mr-2"></i>
                        <span>Assign multiple roles to give users different capabilities</span>
                    </li>
                    <li class="flex">
                        <i class="fas fa-info-circle text-primary mt-1 mr-2"></i>
                        <span>The primary role determines which dashboard the user sees first</span>
                    </li>
                    <li class="flex">
                        <i class="fas fa-info-circle text-primary mt-1 mr-2"></i>
                        <span>Users will receive an email with instructions to set up their account</span>
                    </li>
                    <li class="flex">
                        <i class="fas fa-info-circle text-primary mt-1 mr-2"></i>
                        <span>For security, limit the number of admin users</span>
                    </li>
                </ul>
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

        // Phone number formatting and validation
        const phoneInput = document.getElementById('phone_number');

        phoneInput.addEventListener('input', function(e) {
            let value = e.target.value;

            // Remove all non-numeric characters except +
            value = value.replace(/[^\d+]/g, '');

            // Ensure it starts with +
            if (!value.startsWith('+')) {
                value = '+' + value;
            }

            // Limit the length to 15 characters (ITU-T E.164 standard)
            if (value.length > 15) {
                value = value.substring(0, 15);
            }

            e.target.value = value;
        });

        // Optional: Add a blur event to validate minimum length
        phoneInput.addEventListener('blur', function(e) {
            let value = e.target.value;
            if (value.length < 8) { // Minimum length including +
                phoneInput.classList.add('border-red-500');
            } else {
                phoneInput.classList.remove('border-red-500');
            }
        });
    });
</script>
@endpush
