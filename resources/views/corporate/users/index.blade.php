@extends('corporate.layouts.app')

@section('title', 'User Management')

@section('content')
<div class="mb-6">
    <div class="flex justify-between items-center">
        <div>
            <h2 class="text-xl font-bold text-gray-800">User Management</h2>
            <p class="text-gray-500">Manage users and their roles within your company</p>
        </div>
        <div>
            <a href="{{ route('corporate.users.invite') }}" class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-opacity-90">
                <i class="fas fa-user-plus mr-2"></i> Invite User
            </a>
        </div>
    </div>
</div>

<!-- Filters -->
<div class="bg-white rounded-xl shadow-sm p-6 mb-6">
    <form action="{{ route('corporate.users.index') }}" method="GET" class="space-y-4">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label for="role" class="block text-sm font-medium text-gray-700 mb-1">Role</label>
                <select id="role" name="role" class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-primary focus:border-primary">
                    <option value="all" {{ request('role') == 'all' ? 'selected' : '' }}>All Roles</option>
                    @foreach($roles as $role)
                        <option value="{{ $role->id }}" {{ request('role') == $role->id ? 'selected' : '' }}>{{ ucfirst($role->name) }}</option>
                    @endforeach
                </select>
            </div>
            
            <div>
                <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                <select id="status" name="status" class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-primary focus:border-primary">
                    <option value="all" {{ request('status') == 'all' ? 'selected' : '' }}>All Statuses</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                </select>
            </div>
            
            <div>
                <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                <input type="text" id="search" name="search" value="{{ request('search') }}" placeholder="Search by name or email" class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-primary focus:border-primary">
            </div>
        </div>
        
        <div class="flex justify-end space-x-3">
            <a href="{{ route('corporate.users.index') }}" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                Reset
            </a>
            <button type="submit" class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-opacity-90">
                Apply Filters
            </button>
        </div>
    </form>
</div>

<!-- Users List -->
<div class="bg-white rounded-xl shadow-sm overflow-hidden">
    <div class="p-6 border-b border-gray-200">
        <div class="flex justify-between items-center">
            <h3 class="text-lg font-semibold text-gray-800">Company Users</h3>
            <span class="px-3 py-1 bg-primary bg-opacity-10 text-primary text-sm font-medium rounded-full">
                {{ $users->total() }} {{ Str::plural('User', $users->total()) }}
            </span>
        </div>
    </div>
    
    @if($users->isEmpty())
        <div class="p-6 text-center">
            <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-gray-100 text-gray-400 mb-4">
                <i class="fas fa-users text-2xl"></i>
            </div>
            <h3 class="text-lg font-medium text-gray-900 mb-1">No users found</h3>
            <p class="text-gray-500">There are no users matching your filters.</p>
        </div>
    @else
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50">
                        <th class="px-6 py-3 text-left font-medium text-gray-500">User</th>
                        <th class="px-6 py-3 text-left font-medium text-gray-500">Email</th>
                        <th class="px-6 py-3 text-left font-medium text-gray-500">Roles</th>
                        <th class="px-6 py-3 text-left font-medium text-gray-500">Status</th>
                        <th class="px-6 py-3 text-left font-medium text-gray-500">Joined</th>
                        <th class="px-6 py-3 text-left font-medium text-gray-500">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($users as $user)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 rounded-full bg-primary text-white flex items-center justify-center text-sm mr-3">
                                        {{ strtoupper(substr($user->name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <div class="font-medium text-gray-900">{{ $user->name }}</div>
                                        @php
                                            $primaryRole = $user->corporateUserRoles()
                                                ->where('company_id', $company->id)
                                                ->where('is_primary', true)
                                                ->first();
                                        @endphp
                                        @if($primaryRole)
                                            <div class="text-xs text-gray-500">{{ ucfirst($primaryRole->role->name) }}</div>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-gray-900">{{ $user->email }}</div>
                                @if($user->email_verified_at)
                                    <div class="text-xs text-green-600">
                                        <i class="fas fa-check-circle mr-1"></i> Verified
                                    </div>
                                @else
                                    <div class="text-xs text-yellow-600">
                                        <i class="fas fa-exclamation-circle mr-1"></i> Unverified
                                    </div>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex flex-wrap gap-1">
                                    @foreach($user->corporateUserRoles()->where('company_id', $company->id)->get() as $userRole)
                                        @php
                                            $roleColors = [
                                                'admin' => 'bg-purple-100 text-purple-800',
                                                'approver' => 'bg-blue-100 text-blue-800',
                                                'initiator' => 'bg-green-100 text-green-800',
                                            ];
                                            $roleColor = $roleColors[$userRole->role->name] ?? 'bg-gray-100 text-gray-800';
                                        @endphp
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $roleColor }}">
                                            {{ ucfirst($userRole->role->name) }}
                                            @if($userRole->is_primary)
                                                <i class="fas fa-star ml-1 text-yellow-500"></i>
                                            @endif
                                        </span>
                                    @endforeach
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($user->is_active)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        <i class="fas fa-circle text-xs mr-1"></i> Active
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                        <i class="fas fa-circle text-xs mr-1"></i> Inactive
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $user->created_at->format('M d, Y') }}</div>
                                <div class="text-xs text-gray-500">{{ $user->created_at->diffForHumans() }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex space-x-3">
                                    <a href="{{ route('corporate.users.edit', $user->id) }}" class="text-primary hover:text-primary-dark">
                                        <i class="fas fa-edit"></i>
                                        <span class="sr-only">Edit</span>
                                    </a>
                                    
                                    @if(Auth::id() !== $user->id)
                                        @if($user->is_active)
                                            <form action="{{ route('corporate.users.update', $user->id) }}" method="POST" class="inline">
                                                @csrf
                                                @method('PUT')
                                                <input type="hidden" name="name" value="{{ $user->name }}">
                                                <input type="hidden" name="is_active" value="0">
                                                <input type="hidden" name="roles" value="{{ json_encode($user->corporateUserRoles()->where('company_id', $company->id)->pluck('role_id')->toArray()) }}">
                                                <input type="hidden" name="primary_role" value="{{ $primaryRole ? $primaryRole->role_id : '' }}">
                                                
                                                <button type="submit" class="text-yellow-600 hover:text-yellow-800" onclick="return confirm('Are you sure you want to deactivate this user?')">
                                                    <i class="fas fa-user-slash"></i>
                                                    <span class="sr-only">Deactivate</span>
                                                </button>
                                            </form>
                                        @else
                                            <form action="{{ route('corporate.users.update', $user->id) }}" method="POST" class="inline">
                                                @csrf
                                                @method('PUT')
                                                <input type="hidden" name="name" value="{{ $user->name }}">
                                                <input type="hidden" name="is_active" value="1">
                                                <input type="hidden" name="roles" value="{{ json_encode($user->corporateUserRoles()->where('company_id', $company->id)->pluck('role_id')->toArray()) }}">
                                                <input type="hidden" name="primary_role" value="{{ $primaryRole ? $primaryRole->role_id : '' }}">
                                                
                                                <button type="submit" class="text-green-600 hover:text-green-800">
                                                    <i class="fas fa-user-check"></i>
                                                    <span class="sr-only">Activate</span>
                                                </button>
                                            </form>
                                        @endif
                                        
                                        <a href="{{ route('corporate.users.resend-invitation', $user->id) }}" class="text-blue-600 hover:text-blue-800" onclick="return confirm('Are you sure you want to resend the invitation?')">
                                            <i class="fas fa-paper-plane"></i>
                                            <span class="sr-only">Resend Invitation</span>
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $users->withQueryString()->links() }}
        </div>
    @endif
</div>
@endsection
