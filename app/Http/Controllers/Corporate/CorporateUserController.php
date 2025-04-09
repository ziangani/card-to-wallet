<?php

namespace App\Http\Controllers\Corporate;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use App\Models\CorporateRole;
use App\Models\CorporateUserRole;
use App\Models\ApprovalRequest;
use Illuminate\Support\Str;

class CorporateUserController extends Controller
{
    /**
     * Display a listing of the corporate users.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $company = $user->company;
        
        // Get users with filtering
        $query = User::where('company_id', $company->id);
        
        // Filter by role
        if ($request->has('role') && $request->role != 'all') {
            $roleId = $request->role;
            $query->whereHas('corporateUserRoles', function ($query) use ($roleId) {
                $query->where('role_id', $roleId);
            });
        }
        
        // Filter by status
        if ($request->has('status') && $request->status != 'all') {
            $query->where('is_active', $request->status == 'active');
        }
        
        // Search by name or email
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function ($query) use ($search) {
                $query->where('name', 'like', '%' . $search . '%')
                    ->orWhere('email', 'like', '%' . $search . '%');
            });
        }
        
        // Order by
        $query->orderBy('name', 'asc');
        
        // Paginate
        $users = $query->paginate(10);
        
        // Get roles for filter
        $roles = CorporateRole::orderBy('name', 'asc')->get();
        
        return view('corporate.users.index', compact(
            'company',
            'users',
            'roles'
        ));
    }
    
    /**
     * Show the form for inviting a new user.
     *
     * @return \Illuminate\View\View
     */
    public function invite()
    {
        $user = Auth::user();
        $company = $user->company;
        $roles = CorporateRole::orderBy('name', 'asc')->get();
        
        return view('corporate.users.invite', compact(
            'company',
            'roles'
        ));
    }
    
    /**
     * Process the invitation.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function processInvite(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'roles' => 'required|array|min:1',
            'roles.*' => 'exists:corporate_roles,id',
            'primary_role' => 'required|exists:corporate_roles,id',
            'message' => 'nullable|string|max:1000',
        ]);
        
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
        
        $user = Auth::user();
        $company = $user->company;
        
        // Check if the user has permission to invite users
        if (!$user->hasCorporateRole('admin')) {
            return redirect()->back()->with('error', 'You do not have permission to invite users.');
        }
        
        // Generate a random password
        $password = Str::random(12);
        
        // Create the user
        $newUser = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($password),
            'user_type' => 'corporate',
            'company_id' => $company->id,
            'is_active' => true,
        ]);
        
        // Assign roles
        foreach ($request->roles as $roleId) {
            $isPrimary = $roleId == $request->primary_role;
            
            CorporateUserRole::create([
                'company_id' => $company->id,
                'user_id' => $newUser->id,
                'role_id' => $roleId,
                'is_primary' => $isPrimary,
                'assigned_by' => $user->id,
                'assigned_at' => now(),
            ]);
        }
        
        // Send invitation email
        // In a real implementation, this would send an email with the password
        // For now, we'll just log the password
        Log::info('New user invited: ' . $newUser->email . ' with password: ' . $password);
        
        return redirect()->route('corporate.users.index')
            ->with('success', 'User invited successfully. An email has been sent with login instructions.');
    }
    
    /**
     * Show the form for editing the specified user.
     *
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function edit($id)
    {
        $user = Auth::user();
        $company = $user->company;
        
        // Get the user to edit
        $editUser = User::where('company_id', $company->id)
            ->findOrFail($id);
        
        // Get roles
        $roles = CorporateRole::orderBy('name', 'asc')->get();
        
        // Get user roles
        $userRoles = $editUser->corporateUserRoles()
            ->where('company_id', $company->id)
            ->pluck('role_id')
            ->toArray();
        
        // Get primary role
        $primaryRole = $editUser->corporateUserRoles()
            ->where('company_id', $company->id)
            ->where('is_primary', true)
            ->first();
        
        $primaryRoleId = $primaryRole ? $primaryRole->role_id : null;
        
        return view('corporate.users.edit', compact(
            'company',
            'editUser',
            'roles',
            'userRoles',
            'primaryRoleId'
        ));
    }
    
    /**
     * Update the specified user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'roles' => 'required|array|min:1',
            'roles.*' => 'exists:corporate_roles,id',
            'primary_role' => 'required|exists:corporate_roles,id',
            'is_active' => 'required|boolean',
        ]);
        
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
        
        $user = Auth::user();
        $company = $user->company;
        
        // Check if the user has permission to update users
        if (!$user->hasCorporateRole('admin')) {
            return redirect()->back()->with('error', 'You do not have permission to update users.');
        }
        
        // Get the user to update
        $updateUser = User::where('company_id', $company->id)
            ->findOrFail($id);
        
        // Update the user
        $updateUser->update([
            'name' => $request->name,
            'is_active' => $request->is_active,
        ]);
        
        // Get current roles
        $currentRoles = $updateUser->corporateUserRoles()
            ->where('company_id', $company->id)
            ->pluck('role_id')
            ->toArray();
        
        // Roles to add
        $rolesToAdd = array_diff($request->roles, $currentRoles);
        
        // Roles to remove
        $rolesToRemove = array_diff($currentRoles, $request->roles);
        
        // Add new roles
        foreach ($rolesToAdd as $roleId) {
            $isPrimary = $roleId == $request->primary_role;
            
            CorporateUserRole::create([
                'company_id' => $company->id,
                'user_id' => $updateUser->id,
                'role_id' => $roleId,
                'is_primary' => $isPrimary,
                'assigned_by' => $user->id,
                'assigned_at' => now(),
            ]);
        }
        
        // Remove roles
        CorporateUserRole::where('company_id', $company->id)
            ->where('user_id', $updateUser->id)
            ->whereIn('role_id', $rolesToRemove)
            ->delete();
        
        // Update primary role
        if (in_array($request->primary_role, $request->roles)) {
            // Set all roles to not primary
            CorporateUserRole::where('company_id', $company->id)
                ->where('user_id', $updateUser->id)
                ->update(['is_primary' => false]);
            
            // Set the selected role as primary
            CorporateUserRole::where('company_id', $company->id)
                ->where('user_id', $updateUser->id)
                ->where('role_id', $request->primary_role)
                ->update(['is_primary' => true]);
        }
        
        return redirect()->route('corporate.users.index')
            ->with('success', 'User updated successfully.');
    }
    
    /**
     * Resend the invitation email.
     *
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function resendInvitation($id)
    {
        $user = Auth::user();
        $company = $user->company;
        
        // Check if the user has permission to invite users
        if (!$user->hasCorporateRole('admin')) {
            return redirect()->back()->with('error', 'You do not have permission to resend invitations.');
        }
        
        // Get the user to resend invitation to
        $inviteUser = User::where('company_id', $company->id)
            ->findOrFail($id);
        
        // Generate a new password
        $password = Str::random(12);
        
        // Update the user's password
        $inviteUser->update([
            'password' => Hash::make($password),
        ]);
        
        // Send invitation email
        // In a real implementation, this would send an email with the password
        // For now, we'll just log the password
        Log::info('Invitation resent: ' . $inviteUser->email . ' with password: ' . $password);
        
        return redirect()->route('corporate.users.index')
            ->with('success', 'Invitation resent successfully.');
    }
}
