@extends('corporate.layouts.app')

@section('title', 'Approval Workflows')

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
                    <a href="{{ route('corporate.settings.roles') }}" class="flex items-center px-4 py-3 text-dark hover:bg-gray-50 rounded-lg transition-colors">
                        <i class="fas fa-user-tag w-6 text-gray-500"></i>
                        <span>User Roles</span>
                    </a>
                    <a href="{{ route('corporate.settings.approvals') }}" class="flex items-center px-4 py-3 text-dark bg-primary bg-opacity-10 rounded-lg">
                        <i class="fas fa-check-double w-6 text-primary"></i>
                        <span class="font-medium">Approval Workflows</span>
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
        <!-- Approval Workflows -->
<div class="bg-white rounded-xl shadow-sm overflow-hidden">
    <div class="p-6 border-b border-gray-200">
        <h3 class="text-lg font-semibold text-gray-800">Workflow Settings</h3>
        <p class="text-sm text-gray-500">Define approval requirements for different transaction types</p>
    </div>
    
    <form action="{{ route('corporate.settings.update-approvals') }}" method="POST" class="p-6">
        @csrf
        @method('PUT')
        
        <div class="space-y-6">
            <input type="hidden" name="workflows" value="">
            
            <!-- Bulk Disbursement Workflow -->
            <div class="border rounded-lg p-4">
                <div class="flex justify-between items-center mb-4">
                    <h4 class="font-medium text-gray-800">Bulk Disbursement Approvals</h4>
                    <div class="flex items-center">
                        @php
                            $bulkDisbursementWorkflow = $workflows->where('entity_type', 'bulk_disbursement')->first();
                        @endphp
                        <label class="inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="workflows[0][is_active]" value="1" class="sr-only peer" {{ $bulkDisbursementWorkflow && $bulkDisbursementWorkflow->is_active ? 'checked' : '' }}>
                            <div class="relative w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-primary rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-primary"></div>
                            <span class="ml-2 text-sm font-medium text-gray-700">{{ $bulkDisbursementWorkflow && $bulkDisbursementWorkflow->is_active ? 'Enabled' : 'Disabled' }}</span>
                        </label>
                    </div>
                </div>
                
                <input type="hidden" name="workflows[0][id]" value="{{ $bulkDisbursementWorkflow ? $bulkDisbursementWorkflow->id : '' }}">
                <input type="hidden" name="workflows[0][entity_type]" value="bulk_disbursement">
                
                <p class="text-sm text-gray-600 mb-4">Configure approval requirements for bulk disbursements to multiple recipients.</p>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="bulk_min_approvers" class="block text-sm font-medium text-gray-700 mb-1">Minimum Approvers Required</label>
                        <input type="number" id="bulk_min_approvers" name="workflows[0][min_approvers]" value="{{ $bulkDisbursementWorkflow ? $bulkDisbursementWorkflow->min_approvers : 1 }}" min="1" class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-primary focus:border-primary">
                        @error('workflows.0.min_approvers')
                            <p class="mt-1 text-sm text-error">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label for="bulk_amount_threshold" class="block text-sm font-medium text-gray-700 mb-1">Amount Threshold (K)</label>
                        <input type="number" id="bulk_amount_threshold" name="workflows[0][amount_threshold]" value="{{ $bulkDisbursementWorkflow ? $bulkDisbursementWorkflow->amount_threshold : '' }}" min="0" step="0.01" placeholder="No threshold" class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-primary focus:border-primary">
                        <p class="mt-1 text-xs text-gray-500">Leave empty for no threshold (all disbursements require approval)</p>
                        @error('workflows.0.amount_threshold')
                            <p class="mt-1 text-sm text-error">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>
            
            <!-- User Role Workflow -->
            <div class="border rounded-lg p-4">
                <div class="flex justify-between items-center mb-4">
                    <h4 class="font-medium text-gray-800">User Role Changes</h4>
                    <div class="flex items-center">
                        @php
                            $userRoleWorkflow = $workflows->where('entity_type', 'user_role')->first();
                        @endphp
                        <label class="inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="workflows[1][is_active]" value="1" class="sr-only peer" {{ $userRoleWorkflow && $userRoleWorkflow->is_active ? 'checked' : '' }}>
                            <div class="relative w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-primary rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-primary"></div>
                            <span class="ml-2 text-sm font-medium text-gray-700">{{ $userRoleWorkflow && $userRoleWorkflow->is_active ? 'Enabled' : 'Disabled' }}</span>
                        </label>
                    </div>
                </div>
                
                <input type="hidden" name="workflows[1][id]" value="{{ $userRoleWorkflow ? $userRoleWorkflow->id : '' }}">
                <input type="hidden" name="workflows[1][entity_type]" value="user_role">
                
                <p class="text-sm text-gray-600 mb-4">Configure approval requirements for user role changes and permissions.</p>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="role_min_approvers" class="block text-sm font-medium text-gray-700 mb-1">Minimum Approvers Required</label>
                        <input type="number" id="role_min_approvers" name="workflows[1][min_approvers]" value="{{ $userRoleWorkflow ? $userRoleWorkflow->min_approvers : 1 }}" min="1" class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-primary focus:border-primary">
                        @error('workflows.1.min_approvers')
                            <p class="mt-1 text-sm text-error">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label for="role_amount_threshold" class="block text-sm font-medium text-gray-700 mb-1">Amount Threshold</label>
                        <input type="text" id="role_amount_threshold" value="Not Applicable" disabled class="w-full px-3 py-2 border rounded-lg bg-gray-100 text-gray-500">
                        <input type="hidden" name="workflows[1][amount_threshold]" value="">
                    </div>
                </div>
            </div>
            
            <!-- Rate Change Workflow -->
            <div class="border rounded-lg p-4">
                <div class="flex justify-between items-center mb-4">
                    <h4 class="font-medium text-gray-800">Rate Change Approvals</h4>
                    <div class="flex items-center">
                        @php
                            $rateChangeWorkflow = $workflows->where('entity_type', 'rate_change')->first();
                        @endphp
                        <label class="inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="workflows[2][is_active]" value="1" class="sr-only peer" {{ $rateChangeWorkflow && $rateChangeWorkflow->is_active ? 'checked' : '' }}>
                            <div class="relative w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-primary rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-primary"></div>
                            <span class="ml-2 text-sm font-medium text-gray-700">{{ $rateChangeWorkflow && $rateChangeWorkflow->is_active ? 'Enabled' : 'Disabled' }}</span>
                        </label>
                    </div>
                </div>
                
                <input type="hidden" name="workflows[2][id]" value="{{ $rateChangeWorkflow ? $rateChangeWorkflow->id : '' }}">
                <input type="hidden" name="workflows[2][entity_type]" value="rate_change">
                
                <p class="text-sm text-gray-600 mb-4">Configure approval requirements for transaction fee rate changes.</p>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="rate_min_approvers" class="block text-sm font-medium text-gray-700 mb-1">Minimum Approvers Required</label>
                        <input type="number" id="rate_min_approvers" name="workflows[2][min_approvers]" value="{{ $rateChangeWorkflow ? $rateChangeWorkflow->min_approvers : 1 }}" min="1" class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-primary focus:border-primary">
                        @error('workflows.2.min_approvers')
                            <p class="mt-1 text-sm text-error">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label for="rate_amount_threshold" class="block text-sm font-medium text-gray-700 mb-1">Amount Threshold</label>
                        <input type="text" id="rate_amount_threshold" value="Not Applicable" disabled class="w-full px-3 py-2 border rounded-lg bg-gray-100 text-gray-500">
                        <input type="hidden" name="workflows[2][amount_threshold]" value="">
                    </div>
                </div>
            </div>
            
            <!-- Wallet Withdrawal Workflow -->
            <div class="border rounded-lg p-4">
                <div class="flex justify-between items-center mb-4">
                    <h4 class="font-medium text-gray-800">Wallet Withdrawal Approvals</h4>
                    <div class="flex items-center">
                        @php
                            $withdrawalWorkflow = $workflows->where('entity_type', 'wallet_withdrawal')->first();
                        @endphp
                        <label class="inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="workflows[3][is_active]" value="1" class="sr-only peer" {{ $withdrawalWorkflow && $withdrawalWorkflow->is_active ? 'checked' : '' }}>
                            <div class="relative w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-primary rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-primary"></div>
                            <span class="ml-2 text-sm font-medium text-gray-700">{{ $withdrawalWorkflow && $withdrawalWorkflow->is_active ? 'Enabled' : 'Disabled' }}</span>
                        </label>
                    </div>
                </div>
                
                <input type="hidden" name="workflows[3][id]" value="{{ $withdrawalWorkflow ? $withdrawalWorkflow->id : '' }}">
                <input type="hidden" name="workflows[3][entity_type]" value="wallet_withdrawal">
                
                <p class="text-sm text-gray-600 mb-4">Configure approval requirements for wallet withdrawals to bank accounts.</p>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="withdrawal_min_approvers" class="block text-sm font-medium text-gray-700 mb-1">Minimum Approvers Required</label>
                        <input type="number" id="withdrawal_min_approvers" name="workflows[3][min_approvers]" value="{{ $withdrawalWorkflow ? $withdrawalWorkflow->min_approvers : 1 }}" min="1" class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-primary focus:border-primary">
                        @error('workflows.3.min_approvers')
                            <p class="mt-1 text-sm text-error">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label for="withdrawal_amount_threshold" class="block text-sm font-medium text-gray-700 mb-1">Amount Threshold (K)</label>
                        <input type="number" id="withdrawal_amount_threshold" name="workflows[3][amount_threshold]" value="{{ $withdrawalWorkflow ? $withdrawalWorkflow->amount_threshold : '' }}" min="0" step="0.01" placeholder="No threshold" class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-primary focus:border-primary">
                        <p class="mt-1 text-xs text-gray-500">Leave empty for no threshold (all withdrawals require approval)</p>
                        @error('workflows.3.amount_threshold')
                            <p class="mt-1 text-sm text-error">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>
        </div>
        
        <div class="mt-6 flex justify-end">
            <button type="submit" class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-opacity-90">
                <i class="fas fa-save mr-2"></i> Save Workflow Settings
            </button>
        </div>
    </form>
</div>

<!-- Approval Process Information -->
<div class="bg-white rounded-xl shadow-sm overflow-hidden mt-6">
    <div class="p-6 border-b border-gray-200">
        <h3 class="text-lg font-semibold text-gray-800">About Approval Workflows</h3>
        <p class="text-sm text-gray-500">How the approval process works</p>
    </div>
    
    <div class="p-6 space-y-4">
        <div class="flex items-start">
            <div class="flex-shrink-0 w-10 h-10 rounded-full bg-primary bg-opacity-10 flex items-center justify-center text-primary mr-4">
                <i class="fas fa-check-double"></i>
            </div>
            <div>
                <h4 class="font-medium text-gray-800">Approval Requirements</h4>
                <p class="text-sm text-gray-600">Each workflow defines how many approvers are required for a specific type of operation. You can set different requirements for different transaction types.</p>
            </div>
        </div>
        
        <div class="flex items-start">
            <div class="flex-shrink-0 w-10 h-10 rounded-full bg-primary bg-opacity-10 flex items-center justify-center text-primary mr-4">
                <i class="fas fa-money-bill-wave"></i>
            </div>
            <div>
                <h4 class="font-medium text-gray-800">Amount Thresholds</h4>
                <p class="text-sm text-gray-600">For monetary transactions, you can set amount thresholds. Transactions below the threshold don't require approval, while those above do. Leave the threshold empty to require approval for all transactions.</p>
            </div>
        </div>
        
        <div class="flex items-start">
            <div class="flex-shrink-0 w-10 h-10 rounded-full bg-primary bg-opacity-10 flex items-center justify-center text-primary mr-4">
                <i class="fas fa-user-check"></i>
            </div>
            <div>
                <h4 class="font-medium text-gray-800">Eligible Approvers</h4>
                <p class="text-sm text-gray-600">Users with the "Approve Requests" permission for the relevant transaction type can approve requests. The same user cannot both initiate and approve a request.</p>
            </div>
        </div>
        
        <div class="flex items-start">
            <div class="flex-shrink-0 w-10 h-10 rounded-full bg-primary bg-opacity-10 flex items-center justify-center text-primary mr-4">
                <i class="fas fa-toggle-on"></i>
            </div>
            <div>
                <h4 class="font-medium text-gray-800">Enabling/Disabling Workflows</h4>
                <p class="text-sm text-gray-600">You can enable or disable each workflow independently. When disabled, operations of that type will proceed without requiring approval.</p>
            </div>
        </div>
    </div>
</div>
@endsection
