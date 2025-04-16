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
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;

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
            'phone_number' => 'required|string|regex:/^\+[1-9]\d{1,14}$/|unique:users',
            'roles' => 'required|array|min:1',
            'roles.*' => 'exists:corporate_roles,id',
            'primary_role' => 'required|exists:corporate_roles,id',
            'message' => 'nullable|string|max:1000',
        ], [
            'phone_number.regex' => 'Please enter a valid international phone number starting with + (e.g., +1234567890)',
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

        try {
            DB::beginTransaction();

            // Create the user with a null password
            $newUser = User::create([
                'first_name' => $request->name,
                'last_name' => $request->name,
                'name' =>  $request->name,
                'email' => $request->email,
                'phone_number' => $request->phone_number,
                'password' => Str::random(14),
                'date_of_birth' => now(),
                'user_type' => 'corporate',
                'company_id' => $company->id,
                'is_active' => true,
            ]);

            // Assign roles
            foreach ($request->roles as $roleId) {
                CorporateUserRole::create([
                    'company_id' => $company->id,
                    'user_id' => $newUser->id,
                    'role_id' => $roleId,
                    'is_primary' => $roleId == $request->primary_role,
                    'assigned_by' => $user->id,
                    'assigned_at' => now(),
                ]);
            }

            // Generate password setup token
            $token = Str::random(64);
            DB::table('password_reset_tokens')->insert([  // Changed from password_resets
                'email' => $newUser->email,
                'token' => Hash::make($token),
                'created_at' => now()
            ]);

            // Generate the setup URL
            $setupUrl = URL::temporarySignedRoute(
                'corporate.setup-password',
                now()->addHours(48),
                [
                    'email' => $newUser->email,
                    'token' => $token
                ]
            );

            // Send invitation email
            $data = [
                'name' => $newUser->name,
                'company' => $company->name,
                'setup_url' => $setupUrl,
                'expiry_hours' => 48
            ];

            $email = new \App\Models\Emails();
            $email->subject = 'Welcome to ' . config('app.name') . ' Corporate Portal';
            $email->from = config('mail.from.address');
            $email->email = $newUser->email;
            $email->message = view('emails.corporate-invite', $data)->render();
            $email->view = 'emails.corporate-invite';
            $email->data = $data;
            $email->status = 'PENDING';
            $email->save();

            DB::commit();

            return redirect()->route('corporate.users.index')
                ->with('success', 'User invited successfully. An invitation email has been sent.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to process user invitation: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Failed to invite user. Please try again.')
                ->withInput();
        }
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

        try {
            DB::beginTransaction();

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

            DB::commit();
            return redirect()->route('corporate.users.index')
                ->with('success', 'User updated successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to update corporate user: ' . $e->getMessage());

            return redirect()->back()
                ->with('error', 'Failed to update user. Please try again.')
                ->withInput();
        }
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

        if (!$user->hasCorporateRole('admin')) {
            return redirect()->back()->with('error', 'You do not have permission to resend invitations.');
        }

        try {
            $inviteUser = User::where('company_id', $company->id)->findOrFail($id);

            // Clear any existing tokens
            DB::table('password_reset_tokens')->where('email', $inviteUser->email)->delete();  // Changed from password_resets

            // Generate new token
            $token = Str::random(64);
            DB::table('password_reset_tokens')->insert([  // Changed from password_resets
                'email' => $inviteUser->email,
                'token' => Hash::make($token),
                'created_at' => now()
            ]);

            // Generate the setup URL
            $setupUrl = URL::temporarySignedRoute(
                'corporate.setup-password',
                now()->addHours(48),
                [
                    'email' => $inviteUser->email,
                    'token' => $token
                ]
            );

            // Send invitation email
            $data = [
                'name' => $inviteUser->name,
                'company' => $company->name,
                'setup_url' => $setupUrl,
                'expiry_hours' => 48
            ];

            $email = new \App\Models\Emails();
            $email->subject = 'Welcome to ' . config('app.name') . ' Corporate Portal';
            $email->from = config('mail.from.address');
            $email->email = $inviteUser->email;
            $email->message = view('emails.corporate-invite', $data)->render();
            $email->view = 'emails.corporate-invite';
            $email->data = $data;
            $email->status = 'PENDING';
            $email->save();

            return redirect()->route('corporate.users.index')
                ->with('success', 'Invitation resent successfully.');

        } catch (\Exception $e) {
            Log::error('Failed to resend invitation: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to resend invitation. Please try again.');
        }
    }

    /**
     * Show the password setup form.
     *
     * @param  string  $email
     * @param  string  $token
     * @return \Illuminate\View\View
     */
    public function showSetupPasswordForm(Request $request, $email, $token)
    {
        return view('corporate.auth.setup-password', [
            'email' => $email,
            'token' => $token
        ]);
    }

    /**
     * Process the password setup.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function setupPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'token' => 'required',
            'password' => 'required|min:8|confirmed',
        ]);

        $tokenData = DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->first();

        if (!$tokenData || !Hash::check($request->token, $tokenData->token)) {
            return back()->withErrors(['email' => 'Invalid token or email.']);
        }

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return back()->withErrors(['email' => 'User not found.']);
        }

        $user->is_email_verified = true;
        $user->password = Hash::make($request->password);
        $user->save();

        DB::table('password_reset_tokens')->where('email', $request->email)->delete();

        return redirect()->route('login')
            ->with('success', 'Password set successfully. You can now login.');
    }
}
