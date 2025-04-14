<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VerifyEmailController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        // Auth middleware is applied in routes
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
     * Mark the authenticated user's email address as verified.
     *
     * @param  \Illuminate\Foundation\Auth\EmailVerificationRequest  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function __invoke(EmailVerificationRequest $request)
    {
        $user = Auth::user();
        
        if ($user->hasVerifiedEmail()) {
            return redirect()->route($this->getDashboardRoute())->with('info', 'Your email is already verified.');
        }

        if ($user->markEmailAsVerified()) {
            event(new Verified($user));
        }

        // Check if phone is verified
        if (!$user->is_phone_verified) {
            return redirect()->route('verification.phone')->with('success', 'Email verified successfully. Please verify your phone number to complete your account setup.');
        }

        return redirect()->route($this->getDashboardRoute())->with('success', 'Email verified successfully.');
    }

    /**
     * Verify email without requiring authentication.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function verifyWithoutAuth(Request $request)
    {
        // Validate the URL signature
        if (!$request->hasValidSignature()) {
            return redirect()->route('login')->with('error', 'Invalid verification link or the link has expired.');
        }

        // Find the user by ID
        $user = User::find($request->route('id'));
        
        if (!$user) {
            return redirect()->route('login')->with('error', 'User not found.');
        }

        // Check if the email hash matches
        if (!hash_equals(sha1($user->email), $request->route('hash'))) {
            return redirect()->route('login')->with('error', 'Invalid verification link.');
        }

        // Check if already verified
        if ($user->hasVerifiedEmail()) {
            return redirect()->route('login')->with('info', 'Your email is already verified. Please log in to access your account.');
        }

        // Mark email as verified
        if ($user->markEmailAsVerified()) {
            event(new Verified($user));
        }

        // Redirect to login page with success message
        return redirect()->route('login')->with('success', 'Email verified successfully. Please log in to access your account.');
    }

    /**
     * Display the email verification notice.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function notice(Request $request)
    {
        $user = Auth::user();
        
        if ($user->hasVerifiedEmail()) {
            return redirect()->route($this->getDashboardRoute());
        }

        return view('auth.verify-email');
    }
}
