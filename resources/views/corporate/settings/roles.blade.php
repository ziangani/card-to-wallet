@extends('corporate.layouts.app')

@section('title', 'Role Management')

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
                    <a href="{{ route('corporate.settings.security') }}" class="flex items-center px-4 py-3 text-dark hover:bg-gray-50 rounded-lg transition-colors">
                        <i class="fas fa-shield-alt w-6 text-gray-500"></i>
                        <span>Security</span>
                    </a>
                    <a href="{{ route('corporate.settings.roles') }}" class="flex items-center px-4 py-3 text-dark bg-primary bg-opacity-10 rounded-lg">
                        <i class="fas fa-user-tag w-6 text-primary"></i>
                        <span class="font-medium">User Roles</span>
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
        <!-- Roles List -->
<div class="bg-white rounded-xl shadow-sm overflow-hidden">
    <div class="p-6 border-b border-gray-200">
        <h3 class="text-lg font-semibold text-gray-800">Available Roles</h3>
        <p class="text-sm text-gray-500">Manage roles and their permissions</p>
    </div>
    
    <form action="{{ route('corporate.settings.update-roles') }}" method="POST" class="p-6">
        @csrf
        @method('PUT')
        
        <div class="space-y-6">
            @foreach($roles as $role)
                <div class="border rounded-lg p-4">
                    <div class="flex justify-between items-center mb-4">
                        <h4 class="font-medium text-gray-800">{{ $role->name }}</h4>
                        <div class="flex items-center">
                            <span class="px-3 py-1 bg-primary bg-opacity-10 text-primary text-sm font-medium rounded-full">
                                {{ $role->users_count ?? 0 }} {{ Str::plural('User', $role->users_count ?? 0) }}
                            </span>
                        </div>
                    </div>
                    
                    <p class="text-sm text-gray-600 mb-4">{{ $role->description }}</p>
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <h5 class="text-sm font-medium text-gray-700 mb-2">Wallet Permissions</h5>
                            <div class="space-y-2">
                                <div class="flex items-center">
                                    <input type="checkbox" id="role_{{ $role->id }}_view_wallet" name="permissions[{{ $role->id }}][view_wallet]" class="rounded text-primary focus:ring-primary" {{ $role->hasPermission('view_wallet') ? 'checked' : '' }}>
                                    <label for="role_{{ $role->id }}_view_wallet" class="ml-2 text-sm text-gray-700">View Wallet</label>
                                </div>
                                <div class="flex items-center">
                                    <input type="checkbox" id="role_{{ $role->id }}_deposit_funds" name="permissions[{{ $role->id }}][deposit_funds]" class="rounded text-primary focus:ring-primary" {{ $role->hasPermission('deposit_funds') ? 'checked' : '' }}>
                                    <label for="role_{{ $role->id }}_deposit_funds" class="ml-2 text-sm text-gray-700">Deposit Funds</label>
                                </div>
                                <div class="flex items-center">
                                    <input type="checkbox" id="role_{{ $role->id }}_withdraw_funds" name="permissions[{{ $role->id }}][withdraw_funds]" class="rounded text-primary focus:ring-primary" {{ $role->hasPermission('withdraw_funds') ? 'checked' : '' }}>
                                    <label for="role_{{ $role->id }}_withdraw_funds" class="ml-2 text-sm text-gray-700">Withdraw Funds</label>
                                </div>
                            </div>
                        </div>
                        
                        <div>
                            <h5 class="text-sm font-medium text-gray-700 mb-2">Disbursement Permissions</h5>
                            <div class="space-y-2">
                                <div class="flex items-center">
                                    <input type="checkbox" id="role_{{ $role->id }}_view_disbursements" name="permissions[{{ $role->id }}][view_disbursements]" class="rounded text-primary focus:ring-primary" {{ $role->hasPermission('view_disbursements') ? 'checked' : '' }}>
                                    <label for="role_{{ $role->id }}_view_disbursements" class="ml-2 text-sm text-gray-700">View Disbursements</label>
                                </div>
                                <div class="flex items-center">
                                    <input type="checkbox" id="role_{{ $role->id }}_create_disbursements" name="permissions[{{ $role->id }}][create_disbursements]" class="rounded text-primary focus:ring-primary" {{ $role->hasPermission('create_disbursements') ? 'checked' : '' }}>
                                    <label for="role_{{ $role->id }}_create_disbursements" class="ml-2 text-sm text-gray-700">Create Disbursements</label>
                                </div>
                                <div class="flex items-center">
                                    <input type="checkbox" id="role_{{ $role->id }}_approve_disbursements" name="permissions[{{ $role->id }}][approve_disbursements]" class="rounded text-primary focus:ring-primary" {{ $role->hasPermission('approve_disbursements') ? 'checked' : '' }}>
                                    <label for="role_{{ $role->id }}_approve_disbursements" class="ml-2 text-sm text-gray-700">Approve Disbursements</label>
                                </div>
                            </div>
                        </div>
                        
                        <div>
                            <h5 class="text-sm font-medium text-gray-700 mb-2">User Management</h5>
                            <div class="space-y-2">
                                <div class="flex items-center">
                                    <input type="checkbox" id="role_{{ $role->id }}_view_users" name="permissions[{{ $role->id }}][view_users]" class="rounded text-primary focus:ring-primary" {{ $role->hasPermission('view_users') ? 'checked' : '' }}>
                                    <label for="role_{{ $role->id }}_view_users" class="ml-2 text-sm text-gray-700">View Users</label>
                                </div>
                                <div class="flex items-center">
                                    <input type="checkbox" id="role_{{ $role->id }}_invite_users" name="permissions[{{ $role->id }}][invite_users]" class="rounded text-primary focus:ring-primary" {{ $role->hasPermission('invite_users') ? 'checked' : '' }}>
                                    <label for="role_{{ $role->id }}_invite_users" class="ml-2 text-sm text-gray-700">Invite Users</label>
                                </div>
                                <div class="flex items-center">
                                    <input type="checkbox" id="role_{{ $role->id }}_manage_roles" name="permissions[{{ $role->id }}][manage_roles]" class="rounded text-primary focus:ring-primary" {{ $role->hasPermission('manage_roles') ? 'checked' : '' }}>
                                    <label for="role_{{ $role->id }}_manage_roles" class="ml-2 text-sm text-gray-700">Manage Roles</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mt-4 grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <h5 class="text-sm font-medium text-gray-700 mb-2">Report Permissions</h5>
                            <div class="space-y-2">
                                <div class="flex items-center">
                                    <input type="checkbox" id="role_{{ $role->id }}_view_reports" name="permissions[{{ $role->id }}][view_reports]" class="rounded text-primary focus:ring-primary" {{ $role->hasPermission('view_reports') ? 'checked' : '' }}>
                                    <label for="role_{{ $role->id }}_view_reports" class="ml-2 text-sm text-gray-700">View Reports</label>
                                </div>
                                <div class="flex items-center">
                                    <input type="checkbox" id="role_{{ $role->id }}_generate_reports" name="permissions[{{ $role->id }}][generate_reports]" class="rounded text-primary focus:ring-primary" {{ $role->hasPermission('generate_reports') ? 'checked' : '' }}>
                                    <label for="role_{{ $role->id }}_generate_reports" class="ml-2 text-sm text-gray-700">Generate Reports</label>
                                </div>
                            </div>
                        </div>
                        
                        <div>
                            <h5 class="text-sm font-medium text-gray-700 mb-2">Settings Permissions</h5>
                            <div class="space-y-2">
                                <div class="flex items-center">
                                    <input type="checkbox" id="role_{{ $role->id }}_view_settings" name="permissions[{{ $role->id }}][view_settings]" class="rounded text-primary focus:ring-primary" {{ $role->hasPermission('view_settings') ? 'checked' : '' }}>
                                    <label for="role_{{ $role->id }}_view_settings" class="ml-2 text-sm text-gray-700">View Settings</label>
                                </div>
                                <div class="flex items-center">
                                    <input type="checkbox" id="role_{{ $role->id }}_update_company" name="permissions[{{ $role->id }}][update_company]" class="rounded text-primary focus:ring-primary" {{ $role->hasPermission('update_company') ? 'checked' : '' }}>
                                    <label for="role_{{ $role->id }}_update_company" class="ml-2 text-sm text-gray-700">Update Company</label>
                                </div>
                                <div class="flex items-center">
                                    <input type="checkbox" id="role_{{ $role->id }}_manage_workflows" name="permissions[{{ $role->id }}][manage_workflows]" class="rounded text-primary focus:ring-primary" {{ $role->hasPermission('manage_workflows') ? 'checked' : '' }}>
                                    <label for="role_{{ $role->id }}_manage_workflows" class="ml-2 text-sm text-gray-700">Manage Workflows</label>
                                </div>
                            </div>
                        </div>
                        
                        <div>
                            <h5 class="text-sm font-medium text-gray-700 mb-2">Approval Permissions</h5>
                            <div class="space-y-2">
                                <div class="flex items-center">
                                    <input type="checkbox" id="role_{{ $role->id }}_view_approvals" name="permissions[{{ $role->id }}][view_approvals]" class="rounded text-primary focus:ring-primary" {{ $role->hasPermission('view_approvals') ? 'checked' : '' }}>
                                    <label for="role_{{ $role->id }}_view_approvals" class="ml-2 text-sm text-gray-700">View Approvals</label>
                                </div>
                                <div class="flex items-center">
                                    <input type="checkbox" id="role_{{ $role->id }}_approve_requests" name="permissions[{{ $role->id }}][approve_requests]" class="rounded text-primary focus:ring-primary" {{ $role->hasPermission('approve_requests') ? 'checked' : '' }}>
                                    <label for="role_{{ $role->id }}_approve_requests" class="ml-2 text-sm text-gray-700">Approve Requests</label>
                                </div>
                                <div class="flex items-center">
                                    <input type="checkbox" id="role_{{ $role->id }}_reject_requests" name="permissions[{{ $role->id }}][reject_requests]" class="rounded text-primary focus:ring-primary" {{ $role->hasPermission('reject_requests') ? 'checked' : '' }}>
                                    <label for="role_{{ $role->id }}_reject_requests" class="ml-2 text-sm text-gray-700">Reject Requests</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        
        <div class="mt-6 flex justify-end">
            <button type="submit" class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-opacity-90">
                <i class="fas fa-save mr-2"></i> Save Role Permissions
            </button>
        </div>
    </form>
</div>

<!-- Role Information -->
<div class="bg-white rounded-xl shadow-sm overflow-hidden mt-6">
    <div class="p-6 border-b border-gray-200">
        <h3 class="text-lg font-semibold text-gray-800">Role Information</h3>
        <p class="text-sm text-gray-500">Description of each role and its purpose</p>
    </div>
    
    <div class="p-6 space-y-4">
        <div class="flex items-start">
            <div class="flex-shrink-0 w-10 h-10 rounded-full bg-primary bg-opacity-10 flex items-center justify-center text-primary mr-4">
                <i class="fas fa-user-shield"></i>
            </div>
            <div>
                <h4 class="font-medium text-gray-800">Administrator</h4>
                <p class="text-sm text-gray-600">Administrators have full access to all features and settings. They can manage users, roles, company settings, and perform all operations.</p>
            </div>
        </div>
        
        <div class="flex items-start">
            <div class="flex-shrink-0 w-10 h-10 rounded-full bg-primary bg-opacity-10 flex items-center justify-center text-primary mr-4">
                <i class="fas fa-user-tie"></i>
            </div>
            <div>
                <h4 class="font-medium text-gray-800">Manager</h4>
                <p class="text-sm text-gray-600">Managers can view all data and approve transactions. They have access to reports and can manage disbursements, but cannot change company settings.</p>
            </div>
        </div>
        
        <div class="flex items-start">
            <div class="flex-shrink-0 w-10 h-10 rounded-full bg-primary bg-opacity-10 flex items-center justify-center text-primary mr-4">
                <i class="fas fa-user-cog"></i>
            </div>
            <div>
                <h4 class="font-medium text-gray-800">Operator</h4>
                <p class="text-sm text-gray-600">Operators can create disbursements and view transactions. They have limited access to reports and cannot approve high-value transactions.</p>
            </div>
        </div>
        
        <div class="flex items-start">
            <div class="flex-shrink-0 w-10 h-10 rounded-full bg-primary bg-opacity-10 flex items-center justify-center text-primary mr-4">
                <i class="fas fa-user"></i>
            </div>
            <div>
                <h4 class="font-medium text-gray-800">Viewer</h4>
                <p class="text-sm text-gray-600">Viewers have read-only access to the system. They can view transactions, reports, and disbursements but cannot create or modify any data.</p>
            </div>
        </div>
    </div>
</div>
@endsection
