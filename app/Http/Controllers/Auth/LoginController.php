<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/dashboard';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        // Guest middleware is applied in routes
    }

    /**
     * Get the login username to be used by the controller.
     *
     * @return string
     */
    public function username()
    {
        $login = request()->input('login');
        
        if (filter_var($login, FILTER_VALIDATE_EMAIL)) {
            request()->merge(['email' => $login]);
            return 'email';
        }
        
        request()->merge(['phone_number' => $login]);
        return 'phone_number';
    }

    /**
     * Validate the user login request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return void
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    protected function validateLogin(Request $request)
    {
        // Check if email is provided instead of login
        if ($request->has('email') && !$request->has('login')) {
            $request->merge(['login' => $request->email]);
        }
        
        $request->validate([
            'login' => 'required|string',
            'password' => 'required|string',
        ]);
        
        // Log login attempt for debugging
        \Illuminate\Support\Facades\Log::info('Login attempt for: ' . $request->input('login'));
    }

    /**
     * Get the needed authorization credentials from the request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    protected function credentials(Request $request)
    {
        $field = $this->username();
        $credentials = [
            $field => $request->input('login'),
            'password' => $request->input('password')
        ];
        
        // Log the field being used for authentication
        \Illuminate\Support\Facades\Log::info('Authentication field: ' . $field);
        
        return $credentials;
    }

    /**
     * Attempt to log the user into the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return bool
     */
    protected function attemptLogin(Request $request)
    {
        $credentials = $this->credentials($request);
        
        // Log credentials for debugging (without password)
        $logCredentials = $credentials;
        if (isset($logCredentials['password'])) {
            $logCredentials['password'] = '[HIDDEN]';
        }
        \Illuminate\Support\Facades\Log::info('Login credentials: ' . json_encode($logCredentials));
        
        // Attempt login
        $result = $this->guard()->attempt(
            $credentials, $request->boolean('remember')
        );
        
        // Log result
        \Illuminate\Support\Facades\Log::info('Login attempt result: ' . ($result ? 'success' : 'failure'));
        
        return $result;
    }

    /**
     * The user has been authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  mixed  $user
     * @return mixed
     */
    protected function authenticated(Request $request, $user)
    {
        // Log successful authentication
        \Illuminate\Support\Facades\Log::info('User authenticated: ' . $user->email);
        
        // Check if user is active
        if (!$user->is_active) {
            Auth::logout();
            throw ValidationException::withMessages([
                'login' => ['This account has been deactivated. Please contact support.'],
            ]);
        }

        // Reset login attempts
        $user->login_attempts = 0;
        $user->last_login_at = now();
        $user->save();

        // Redirect based on verification status
        if (!$user->hasVerifiedEmail()) {
            return redirect()->route('verification.notice');
        }

        if (!$user->is_phone_verified) {
            return redirect()->route('verification.phone');
        }
        
        // Redirect based on user type
        if ($user->user_type === 'corporate') {
            return redirect()->route('corporate.dashboard');
        }

        return redirect()->intended($this->redirectPath());
    }

    /**
     * Get the failed login response instance.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    protected function sendFailedLoginResponse(Request $request)
    {
        $login = $request->input('login');
        $field = filter_var($login, FILTER_VALIDATE_EMAIL) ? 'email' : 'phone_number';
        
        // Log failed login attempt
        \Illuminate\Support\Facades\Log::info('Failed login attempt for: ' . $login . ' using field: ' . $field);
        
        // Find the user
        $user = \App\Models\User::where($field, $login)->first();
        
        if ($user) {
            // Log user found
            \Illuminate\Support\Facades\Log::info('User found for failed login: ' . $user->id . ' (is_active: ' . ($user->is_active ? 'true' : 'false') . ', login_attempts: ' . $user->login_attempts . ')');
            
            // Increment login attempts
            $user->login_attempts = $user->login_attempts + 1;
            $user->save();
            
            // Lock account after 5 failed attempts
            if ($user->login_attempts >= 5) {
                $user->is_active = false;
                $user->save();
                
                \Illuminate\Support\Facades\Log::warning('User account locked due to too many failed attempts: ' . $user->email);
                
                throw ValidationException::withMessages([
                    'login' => ['Your account has been locked due to too many failed login attempts. Please contact support.'],
                ]);
            }
        } else {
            // Log user not found
            \Illuminate\Support\Facades\Log::info('No user found for failed login attempt with ' . $field . ': ' . $login);
        }
        
        throw ValidationException::withMessages([
            'login' => [trans('auth.failed')],
        ]);
    }

    /**
     * Log the user out of the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    public function logout(Request $request)
    {
        $this->guard()->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return $request->wantsJson()
            ? response()->json(['message' => 'Successfully logged out.'])
            : redirect()->route('welcome')->with('success', 'Successfully logged out.');
    }
}
