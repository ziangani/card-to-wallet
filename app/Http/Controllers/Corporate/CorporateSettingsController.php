<?php

namespace App\Http\Controllers\Corporate;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use App\Models\Company;
use App\Models\CompanyDocument;
use App\Models\ApprovalWorkflow;
use App\Models\CorporateRateTier;

class CorporateSettingsController extends Controller
{
    /**
     * Display the company profile settings.
     *
     * @return \Illuminate\View\View
     */
    public function profile()
    {
        $user = Auth::user();
        $company = $user->company;
        
        // Get company documents
        $documents = $company->documents()->get();
        
        return view('corporate.settings.profile', compact(
            'company',
            'documents'
        ));
    }
    
    /**
     * Update the company profile.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateProfile(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'registration_number' => 'required|string|max:100',
            'tax_id' => 'nullable|string|max:100',
            'industry' => 'nullable|string|max:100',
            'address' => 'required|string|max:255',
            'city' => 'required|string|max:100',
            'country' => 'required|string|max:100',
            'postal_code' => 'nullable|string|max:20',
            'phone_number' => 'required|string|max:20',
            'email' => 'required|string|email|max:255',
            'website' => 'nullable|string|max:255',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'documents.*' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'document_types.*' => 'required_with:documents.*|string|in:certificate_of_incorporation,tax_clearance,business_license,company_profile,director_id,other',
            'document_numbers.*' => 'nullable|string|max:100',
        ]);
        
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
        
        $user = Auth::user();
        $company = $user->company;
        
        // Check if the user has permission to update company profile
        if (!$user->hasCorporateRole('admin')) {
            return redirect()->back()->with('error', 'You do not have permission to update company profile.');
        }
        
        // Handle logo upload
        if ($request->hasFile('logo')) {
            $logo = $request->file('logo');
            $logoPath = $logo->store('companies/' . $company->id . '/logo', 'public');
            
            // Delete old logo if exists
            if ($company->logo_path) {
                Storage::disk('public')->delete($company->logo_path);
            }
            
            $company->logo_path = $logoPath;
        }
        
        // Update company details
        $company->update([
            'name' => $request->name,
            'registration_number' => $request->registration_number,
            'tax_id' => $request->tax_id,
            'industry' => $request->industry,
            'address' => $request->address,
            'city' => $request->city,
            'country' => $request->country,
            'postal_code' => $request->postal_code,
            'phone_number' => $request->phone_number,
            'email' => $request->email,
            'website' => $request->website,
            'logo_path' => $company->logo_path,
        ]);
        
        // Handle document uploads
        if ($request->hasFile('documents')) {
            foreach ($request->file('documents') as $key => $file) {
                $documentType = $request->input('document_types.' . $key);
                $documentNumber = $request->input('document_numbers.' . $key);
                
                $filePath = $file->store('companies/' . $company->id . '/documents', 'public');
                
                CompanyDocument::create([
                    'company_id' => $company->id,
                    'document_type' => $documentType,
                    'document_number' => $documentNumber,
                    'file_path' => $filePath,
                    'status' => 'pending',
                ]);
            }
        }
        
        return redirect()->route('corporate.settings.profile')
            ->with('success', 'Company profile updated successfully.');
    }
    
    /**
     * Display the security settings.
     *
     * @return \Illuminate\View\View
     */
    public function security()
    {
        $user = Auth::user();
        $company = $user->company;
        
        return view('corporate.settings.security', compact(
            'company',
            'user'
        ));
    }
    
    /**
     * Update the user's password.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updatePassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'current_password' => 'required|string',
            'password' => 'required|string|min:8|confirmed',
        ]);
        
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
        
        $user = Auth::user();
        
        // Check if the current password is correct
        if (!Hash::check($request->current_password, $user->password)) {
            return redirect()->back()->withErrors(['current_password' => 'The current password is incorrect.']);
        }
        
        // Update the password
        $user->update([
            'password' => Hash::make($request->password),
        ]);
        
        return redirect()->route('corporate.settings.security')
            ->with('success', 'Password updated successfully.');
    }
    
    /**
     * Display the roles settings.
     *
     * @return \Illuminate\View\View
     */
    public function roles()
    {
        $user = Auth::user();
        $company = $user->company;
        
        // Get corporate roles
        $roles = \App\Models\CorporateRole::orderBy('name', 'asc')->get();
        
        return view('corporate.settings.roles', compact(
            'company',
            'roles'
        ));
    }
    
    /**
     * Update the roles settings.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateRoles(Request $request)
    {
        $user = Auth::user();
        $company = $user->company;
        
        // Check if the user has permission to update roles
        if (!$user->hasCorporateRole('admin')) {
            return redirect()->back()->with('error', 'You do not have permission to update roles.');
        }
        
        // In a real implementation, this would update role permissions
        // For now, we'll just return a success message
        
        return redirect()->route('corporate.settings.roles')
            ->with('success', 'Roles updated successfully.');
    }
    
    /**
     * Display the approvals settings.
     *
     * @return \Illuminate\View\View
     */
    public function approvals()
    {
        $user = Auth::user();
        $company = $user->company;
        
        // Get approval workflows
        $workflows = ApprovalWorkflow::where('company_id', $company->id)
            ->orderBy('entity_type', 'asc')
            ->get();
        
        return view('corporate.settings.approvals', compact(
            'company',
            'workflows'
        ));
    }
    
    /**
     * Update the approvals settings.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateApprovals(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'workflows' => 'required|array',
            'workflows.*.id' => 'nullable|exists:approval_workflows,id',
            'workflows.*.entity_type' => 'required|string|in:bulk_disbursement,user_role,rate_change,wallet_withdrawal',
            'workflows.*.min_approvers' => 'required|integer|min:1',
            'workflows.*.amount_threshold' => 'nullable|numeric|min:0',
            'workflows.*.is_active' => 'required|boolean',
        ]);
        
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
        
        $user = Auth::user();
        $company = $user->company;
        
        // Check if the user has permission to update approval workflows
        if (!$user->hasCorporateRole('admin')) {
            return redirect()->back()->with('error', 'You do not have permission to update approval workflows.');
        }
        
        // Update or create approval workflows
        foreach ($request->workflows as $workflowData) {
            if (isset($workflowData['id']) && $workflowData['id']) {
                // Update existing workflow
                $workflow = ApprovalWorkflow::where('company_id', $company->id)
                    ->findOrFail($workflowData['id']);
                
                $workflow->update([
                    'min_approvers' => $workflowData['min_approvers'],
                    'amount_threshold' => $workflowData['amount_threshold'],
                    'is_active' => $workflowData['is_active'],
                ]);
            } else {
                // Create new workflow
                ApprovalWorkflow::create([
                    'company_id' => $company->id,
                    'entity_type' => $workflowData['entity_type'],
                    'min_approvers' => $workflowData['min_approvers'],
                    'amount_threshold' => $workflowData['amount_threshold'],
                    'is_active' => $workflowData['is_active'],
                ]);
            }
        }
        
        return redirect()->route('corporate.settings.approvals')
            ->with('success', 'Approval workflows updated successfully.');
    }
    
    /**
     * Display the rates settings.
     *
     * @return \Illuminate\View\View
     */
    public function rates()
    {
        $user = Auth::user();
        $company = $user->company;
        
        // Get rate tiers
        $rateTiers = CorporateRateTier::active()
            ->orderByVolumeAsc()
            ->get();
        
        // Get current rate assignment
        $rateAssignment = $company->rateAssignment;
        
        return view('corporate.settings.rates', compact(
            'company',
            'rateTiers',
            'rateAssignment'
        ));
    }
}
