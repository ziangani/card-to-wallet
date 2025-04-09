# Corporate Authentication Updates

This document outlines the necessary updates to the authentication system to support corporate accounts in the Card-to-Wallet system.

## Registration Updates

### RegisterController Updates

The `RegisterController` needs to be extended to support corporate registrations:

```php
// Add to validator method
protected function validator(array $data)
{
    $rules = [
        'first_name' => ['required', 'string', 'max:100'],
        'last_name' => ['required', 'string', 'max:100'],
        'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
        'phone_number' => ['required', 'string', 'max:20', 'unique:users'],
        'date_of_birth' => ['required', 'date', 'before:today'],
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

// Update create method
protected function create(array $data)
{
    $user = User::create([
        'first_name' => $data['first_name'],
        'last_name' => $data['last_name'],
        'name' => $data['first_name'] . ' ' . $data['last_name'],
        'email' => $data['email'],
        'phone_number' => $data['phone_number'],
        'date_of_birth' => $data['date_of_birth'],
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
        $this->createDefaultApprovalWorkflows($company->id);
    }
    
    return $user;
}

// Add helper method for creating default approval workflows
protected function createDefaultApprovalWorkflows($companyId)
{
    $workflowTypes = [
        'bulk_disbursement' => 1,
        'user_role' => 1,
        'rate_change' => 1,
        'wallet_withdrawal' => 1,
    ];
    
    foreach ($workflowTypes as $type => $minApprovers) {
        ApprovalWorkflow::create([
            'company_id' => $companyId,
            'entity_type' => $type,
            'min_approvers' => $minApprovers,
            'amount_threshold' => null,
            'is_active' => true,
        ]);
    }
}
```

### Registration View Updates

The registration view (`resources/views/auth/register.blade.php`) needs to be updated to include a toggle for account type and corporate-specific fields:

```html
<!-- Account Type Selection -->
<div class="mb-6">
    <label class="block text-sm font-medium text-gray-700 mb-1">Account Type</label>
    <div class="flex space-x-4">
        <label class="inline-flex items-center">
            <input type="radio" name="account_type" value="individual" class="h-4 w-4 text-primary focus:ring-primary border-gray-300" checked>
            <span class="ml-2">Individual</span>
        </label>
        <label class="inline-flex items-center">
            <input type="radio" name="account_type" value="corporate" class="h-4 w-4 text-primary focus:ring-primary border-gray-300">
            <span class="ml-2">Corporate</span>
        </label>
    </div>
</div>

<!-- Corporate Fields (initially hidden) -->
<div id="corporate-fields" class="hidden space-y-6">
    <h3 class="text-lg font-medium text-gray-900 border-b pb-2">Company Information</h3>
    
    <!-- Company Name and Registration Number -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
            <label for="company_name" class="block text-sm font-medium text-gray-700 mb-1">Company Name</label>
            <input type="text" id="company_name" name="company_name" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary transition duration-200">
        </div>
        <div>
            <label for="registration_number" class="block text-sm font-medium text-gray-700 mb-1">Registration Number</label>
            <input type="text" id="registration_number" name="registration_number" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary transition duration-200">
        </div>
    </div>
    
    <!-- Tax ID and Industry -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
            <label for="tax_id" class="block text-sm font-medium text-gray-700 mb-1">Tax ID (Optional)</label>
            <input type="text" id="tax_id" name="tax_id" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary transition duration-200">
        </div>
        <div>
            <label for="industry" class="block text-sm font-medium text-gray-700 mb-1">Industry (Optional)</label>
            <input type="text" id="industry" name="industry" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary transition duration-200">
        </div>
    </div>
    
    <!-- Company Address and City -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
            <label for="company_address" class="block text-sm font-medium text-gray-700 mb-1">Company Address</label>
            <input type="text" id="company_address" name="company_address" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary transition duration-200">
        </div>
        <div>
            <label for="company_city" class="block text-sm font-medium text-gray-700 mb-1">City</label>
            <input type="text" id="company_city" name="company_city" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary transition duration-200">
        </div>
    </div>
    
    <!-- Company Phone and Email -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
            <label for="company_phone" class="block text-sm font-medium text-gray-700 mb-1">Company Phone</label>
            <input type="text" id="company_phone" name="company_phone" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary transition duration-200">
        </div>
        <div>
            <label for="company_email" class="block text-sm font-medium text-gray-700 mb-1">Company Email</label>
            <input type="email" id="company_email" name="company_email" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary transition duration-200">
        </div>
    </div>
</div>
```

Add JavaScript to toggle corporate fields:

```javascript
// Toggle corporate fields based on account type
document.addEventListener('DOMContentLoaded', function() {
    const accountTypeRadios = document.querySelectorAll('input[name="account_type"]');
    const corporateFields = document.getElementById('corporate-fields');
    
    accountTypeRadios.forEach(radio => {
        radio.addEventListener('change', function() {
            if (this.value === 'corporate') {
                corporateFields.classList.remove('hidden');
            } else {
                corporateFields.classList.add('hidden');
            }
        });
    });
});
```

## Login Updates

### LoginController Updates

The `LoginController` needs to be updated to handle corporate redirects:

```php
// Update authenticated method
protected function authenticated(Request $request, $user)
{
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
```

## Corporate Registration Process

1. User selects "Corporate" account type on registration form
2. Additional company information fields are displayed
3. User fills in personal and company details
4. On submission, the system:
   - Creates a user record with user_type = 'corporate'
   - Creates a company record
   - Associates the user with the company
   - Assigns the admin role to the user
   - Creates a corporate wallet with zero balance
   - Sets up default approval workflows
5. User receives email verification notification
6. After verification, user is redirected to corporate dashboard

## Corporate Login Process

1. User enters email/phone and password
2. System authenticates the user
3. If user_type is 'corporate', redirect to corporate dashboard
4. If user_type is 'individual', redirect to regular dashboard

## Implementation Steps

1. Update RegisterController with corporate account handling
2. Modify registration view to include account type toggle and company fields
3. Update LoginController to redirect based on user type
4. Create necessary database seeders for corporate roles
5. Test the registration and login flows for both individual and corporate accounts
