<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\KycDocument;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        // Authentication is handled in routes
    }

    /**
     * Show the user profile page.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $user = Auth::user();
        
        return view('profile.index', compact('user'));
    }

    /**
     * Update the user profile.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request)
    {
        $user = Auth::user();
        
        $request->validate([
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users')->ignore($user->id),
            ],
            'phone_number' => [
                'required',
                'string',
                'max:20',
                Rule::unique('users')->ignore($user->id),
            ],
            'date_of_birth' => 'required|date|before:today',
            'address' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:100',
            'country' => 'nullable|string|max:100',
        ]);
        
        $user->update([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'phone_number' => $request->phone_number,
            'date_of_birth' => $request->date_of_birth,
            'address' => $request->address,
            'city' => $request->city,
            'country' => $request->country,
        ]);
        
        return redirect()->route('profile.index')
            ->with('success', 'Profile updated successfully.');
    }

    /**
     * Show the security settings page.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function security()
    {
        return view('profile.security');
    }

    /**
     * Update the user password.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required|string',
            'password' => 'required|string|min:8|confirmed',
        ]);
        
        $user = Auth::user();
        
        // Check current password
        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors([
                'current_password' => 'The current password is incorrect.',
            ]);
        }
        
        // Update password
        $user->update([
            'password' => Hash::make($request->password),
        ]);
        
        return redirect()->route('profile.security')
            ->with('success', 'Password updated successfully.');
    }

    /**
     * Show the KYC documents page.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function kyc()
    {
        $user = Auth::user();
        $documents = KycDocument::where('user_id', $user->id)->get();
        
        return view('profile.kyc', compact('user', 'documents'));
    }

    /**
     * Upload KYC documents.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function uploadKyc(Request $request)
    {
        $request->validate([
            'document_type' => 'required|string|in:national_id,passport,drivers_license,proof_of_address,selfie',
            'document_number' => 'nullable|string|max:100',
            'expiry_date' => 'nullable|date|after:today',
            'document_file' => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120',
        ]);
        
        $user = Auth::user();
        
        // Store the file
        $path = $request->file('document_file')->store('kyc_documents/' . $user->id, 'public');
        
        // Create document record
        KycDocument::create([
            'user_id' => $user->id,
            'document_type' => $request->document_type,
            'document_number' => $request->document_number,
            'file_path' => $path,
            'status' => 'pending',
            'expiry_date' => $request->expiry_date,
        ]);
        
        return redirect()->route('profile.kyc')
            ->with('success', 'Document uploaded successfully and pending review.');
    }
}
