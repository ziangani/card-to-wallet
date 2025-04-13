<?php

namespace App\Http\Controllers\Auth;

use App\Common\Helpers;
use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\CorporateRole;
use App\Models\CorporateUserRole;
use App\Models\CorporateWallet;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
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
     * Get a validator for an incoming registration request.
     *
     * @param array $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        $rules = [
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'phone_number' => ['required', 'string', 'max:20'],
            'date_of_birth' => ['nullable', 'date', 'before:today'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'terms' => ['required', 'accepted'],
        ];

        // Add corporate-specific validation rules
        if (isset($data['account_type']) && $data['account_type'] === 'corporate') {
            $rules['company_name'] = ['required', 'string', 'max:255'];
            $rules['registration_number'] = ['required', 'string', 'max:100'];
            $rules['tax_id'] = ['nullable', 'string', 'max:100'];
            $rules['industry'] = ['nullable', 'string', 'max:100'];
            $rules['company_address'] = ['required', 'string', 'max:255'];
            $rules['company_city'] = ['required', 'string', 'max:100'];
            $rules['company_phone'] = ['required', 'string', 'max:20'];
            $rules['company_email'] = ['required', 'string', 'email', 'max:255'];
        }

        return Validator::make($data, $rules);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param array $data
     * @return \App\Models\User
     */
    protected function create(array $data)
    {
        try {

            DB::beginTransaction();
            $user = User::create([
                'first_name' => $data['first_name'],
                'last_name' => $data['last_name'],
                'name' => $data['first_name'] . ' ' . $data['last_name'],
                'email' => $data['email'],
                'phone_number' => $data['phone_number'],
                'date_of_birth' => $data['date_of_birth'] ?? date('Y-m-d'),
                'password' => Hash::make($data['password']),
                'verification_level' => 'basic',
                'is_active' => true,
                'is_email_verified' => false,
                'is_phone_verified' => false,
                'user_type' => isset($data['account_type']) && $data['account_type'] === 'corporate' ? 'corporate' : 'individual',
            ]);

            // Create company record for corporate accounts
            if (isset($data['account_type']) && $data['account_type'] === 'corporate') {
                $company = Company::create([
                    'uuid' => Str::uuid(),
                    'name' => $data['company_name'],
                    'registration_number' => $data['registration_number'],
                    'tax_id' => $data['tax_id'] ?? null,
                    'industry' => $data['industry'] ?? null,
                    'address' => $data['company_address'],
                    'city' => $data['company_city'],
                    'country' => 'Zambia',
                    'phone_number' => $data['company_phone'],
                    'email' => $data['company_email'],
                    'verification_status' => 'pending',
                    'status' => 'active',
                ]);

                // Associate user with company
                $user->company_id = $company->id;
                $user->save();

                // Assign admin role to user
                $adminRole = CorporateRole::where('name', 'admin')->first();
                if ($adminRole) {
                    CorporateUserRole::create([
                        'company_id' => $company->id,
                        'user_id' => $user->id,
                        'role_id' => $adminRole->id,
                        'is_primary' => true,
                        'assigned_by' => $user->id,
                        'assigned_at' => now(),
                    ]);
                }

                // Create corporate wallet
                CorporateWallet::create([
                    'company_id' => $company->id,
                    'balance' => 0,
                    'currency' => 'ZMW',
                    'status' => 'active',
                ]);

                // Create default approval workflows
                $company->createDefaultApprovalWorkflows($company->id);
            }
            return $user;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e; // Rethrow the exception to be handled by the caller
        } finally {
            DB::commit();
        }
    }

    /**
     * Handle a registration request for the application.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    public function showCorporateRegistrationForm()
    {
        return view('corporate.auth.register');
    }

    /**
     * Handle a corporate registration request for the application.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    public function registerCorporate(Request $request)
    {
        try {
            $validator = $this->validator($request->all());

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => $validator->errors()->first(),
                    'errors' => $validator->errors(),
                ], 422);
            }

            $user = $this->create($request->all());
            event(new Registered($user));

            $this->guard()->login($user);

            // Send email verification notification
            Helpers::sendEmailVerificationNotification($user);

            // Mark that verification has been sent
            session()->flash('status', 'verification-link-sent');

            $redirectUrl = route('verification.notice');

            // Check if the request is an AJAX request
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Registration successful. Please verify your email address.',
                    'redirect_url' => $redirectUrl,
                ], 200);
            }

            // For non-AJAX requests, redirect to the verification notice page
            return redirect($redirectUrl);
        } catch (\Exception $e) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Registration failed: ' . $e->getMessage(),
                ], 500);
            }
            
            // For non-AJAX requests, redirect back with error
            return redirect()->back()->withErrors(['error' => 'Registration failed: ' . $e->getMessage()]);
        }
    }

}
