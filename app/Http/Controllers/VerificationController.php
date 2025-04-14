<?php

namespace App\Http\Controllers;

use App\Common\Helpers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class VerificationController extends Controller
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
     * Get the appropriate dashboard route based on user type.
     *
     * @return string
     */
    protected function getDashboardRoute()
    {
        $user = Auth::user();
        
        if ($user && $user->user_type === 'corporate') {
            return 'corporate.dashboard';
        }
        
        return 'dashboard';
    }

    /**
     * Show the phone verification page.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function showPhoneVerification()
    {
        $user = Auth::user();
        
        if ($user->is_phone_verified) {
            return redirect()->route($this->getDashboardRoute())->with('info', 'Your phone number is already verified.');
        }
        
        return view('auth.verify-phone');
    }

    /**
     * Send phone verification code.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function sendPhoneVerificationCode(Request $request)
    {
        $user = Auth::user();
        
        if ($user->is_phone_verified) {
            return redirect()->route($this->getDashboardRoute())->with('info', 'Your phone number is already verified.');
        }
        
        // Generate a random 6-digit code
        $verificationCode = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        
        // Store the code in the session
        session(['phone_verification_code' => $verificationCode]);
        
        // In a real application, this would send an SMS with the code
        // For now, we'll just show it in the flash message for testing
        
        return back()->with('verification_code', $verificationCode)
            ->with('success', 'A verification code has been sent to your phone number.');
    }

    /**
     * Verify phone number.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function verifyPhone(Request $request)
    {
        $request->validate([
            'verification_code' => 'required|string|size:6',
        ]);
        
        $user = Auth::user();
        
        if ($user->is_phone_verified) {
            return redirect()->route($this->getDashboardRoute())->with('info', 'Your phone number is already verified.');
        }
        
        $storedCode = session('phone_verification_code');
        
        if (!$storedCode || $request->verification_code !== $storedCode) {
            return back()->withErrors(['verification_code' => 'The verification code is invalid.']);
        }
        
        // Mark the phone as verified
        $user->is_phone_verified = true;
        $user->save();
        
        // Clear the verification code from the session
        session()->forget('phone_verification_code');
        
        return redirect()->route($this->getDashboardRoute())->with('success', 'Your phone number has been verified successfully.');
    }

    /**
     * Check if email is already verified and redirect accordingly.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function checkEmailVerification(Request $request)
    {
        $user = Auth::user();
        
        if ($user->hasVerifiedEmail()) {
            return redirect()->route($this->getDashboardRoute())->with('success', 'Your email is already verified.');
        }
        
        return back()->with('error', 'Your email is not yet verified. Please check your email for the verification link or request a new one.');
    }

    /**
     * Resend email verification notification.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function resendEmailVerification(Request $request)
    {
        $user = Auth::user();
        
        if ($user->hasVerifiedEmail()) {
            return redirect()->route($this->getDashboardRoute())->with('info', 'Your email is already verified.');
        }
        
        // Use our custom email verification method
        Helpers::sendEmailVerificationNotification($user);
        
        return back()->with('status', 'verification-link-sent');
    }
}
