# Coding Standards

This document outlines the coding standards for the TechPay project. All contributors should follow these guidelines to maintain consistency and code quality.

## 1. File Structure

- Use UTF-8 encoding
- Use LF (Unix) line endings
- End files with a newline
- Trim trailing whitespace
- PHP files must start with `<?php`
- Namespace must match PSR-4 autoloading standard

## 2. Indentation and Spacing

- Use 4 spaces for PHP files
- Use 2 spaces for YAML files
- No trailing whitespace
- One blank line before return statements
- One blank line between methods

## 3. Naming Conventions

- Classes: PascalCase (e.g., `QueryCollections`)
- Methods: camelCase (e.g., `getStatus`)
- Constants: UPPER_SNAKE_CASE (e.g., `PAYMENT_STATUSES`)
- Variables: camelCase (e.g., `$paymentProvider`)
- Files: Match class names (e.g., `QueryCollections.php`)

## 4. Class Structure

- One class per file
- Namespace declaration first
- Use statements after namespace
- Class properties before methods
- Constructor after properties
- Public methods first, then protected, then private

Example:
```php
<?php

namespace App\Http\Controllers;

use SomeNamespace\SomeClass;

class ExampleController extends Controller
{
    private $property;
    
    public function __construct()
    {
        $this->property = 'value';
    }
    
    public function publicMethod()
    {
        // Implementation
    }
    
    protected function protectedMethod()
    {
        // Implementation
    }
    
    private function privateMethod()
    {
        // Implementation
    }
}
```

## 5. Error Handling

- Use try-catch blocks for external service calls
- Log exceptions appropriately
- Return consistent error responses

Example:
```php
try {
    // External service call
} catch (\Exception $e) {
    Log::error('Error message: ' . $e->getMessage());
    return response()->json([
        'status' => 'ERROR',
        'statusText' => 'Something went wrong'
    ]);
}
```

## 6. Documentation

- Use PHPDoc blocks for classes and methods
- Document parameters and return types
- Include meaningful descriptions

Example:
```php
/**
 * Process the payment transaction
 *
 * @param Request $request The incoming request
 * @param int $merchantId The merchant identifier
 * @return JsonResponse
 */
public function processPayment(Request $request, int $merchantId)
```

## 7. Security

- Validate all input data
- Escape output appropriately
- Use CSRF protection (except for specified routes)
- Follow Laravel security best practices

## 8. Database

- Use Laravel's query builder or Eloquent
- Define relationships in models
- Use migrations for schema changes
- Name tables in snake_case and plural form

## 9. Response Format

All API responses should follow this format:
```php
return response()->json([
    'status' => 'SUCCESS|ERROR',
    'statusText' => 'Message',
    'data' => [] // optional
]);
```

## 10. Git Practices

- Use `.gitattributes` for file handling
- Ignore appropriate files in `.gitignore`
- Use LF line endings consistently
- Write meaningful commit messages

## 11. Configuration

- Use environment variables for configuration
- Store sensitive data in `.env` file
- Include example values in `.env.example`
- Use config files for application settings

## 12. Testing

- Write unit tests for new features
- Maintain existing tests
- Follow arrange-act-assert pattern
- Use meaningful test names

## 13. Dependencies

- Use Composer for PHP dependencies
- Use npm for JavaScript dependencies
- Keep dependencies updated
- Review security advisories

## 14. Performance

- Cache where appropriate
- Optimize database queries
- Use eager loading for relationships
- Follow Laravel performance best practices

## 15. Frontend Standards

### 15.1 Tailwind CSS

#### Configuration
- Keep Tailwind configuration in a dedicated `tailwind.config.js` file
- Define custom colors, fonts, and other theme extensions in the config
- Use semantic color names in the configuration
- Maintain consistent color palette across the application

Example configuration:
```javascript
module.exports = {
  theme: {
    extend: {
      colors: {
        primary: '#3366CC',
        secondary: '#FF9900',
        success: '#28A745',
        warning: '#FFC107',
        error: '#DC3545'
      },
      fontFamily: {
        sans: ['Inter', 'sans-serif'],
      },
      boxShadow: {
        'card': '0 10px 15px -3px rgba(0, 0, 0, 0.05)',
        'button': '0 4px 6px -1px rgba(0, 0, 0, 0.1)'
      }
    }
  }
}
```

#### Class Organization
- Group related Tailwind classes together
- Order classes consistently: layout → spacing → typography → visual styles
- Use meaningful component class names
- Extract repeated patterns into components or @apply directives

Example:
```html
<!-- Good -->
<button class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-primary-dark">

<!-- Better - Using @apply -->
<style>
.btn-primary {
    @apply px-4 py-2 bg-primary text-white rounded-lg hover:bg-primary-dark;
}
</style>
```

#### Responsive Design
- Use mobile-first approach
- Implement responsive designs using Tailwind's breakpoint prefixes
- Keep breakpoint usage consistent across components
- Document custom breakpoints in configuration

Example:
```html
<div class="w-full md:w-1/2 lg:w-1/3">
    <!-- Content -->
</div>
```

### 15.2 JavaScript

#### Organization
- Use ES6+ features
- Organize code into modules
- Keep functions small and focused
- Use consistent naming conventions

Example:
```javascript
// Component initialization
const initializeDropdown = () => {
    // Implementation
};

// Event handlers
const handleSubmit = async (event) => {
    event.preventDefault();
    // Implementation
};
```

#### Form Handling
- Use form validation libraries consistently
- Implement proper error handling
- Show loading states during submissions
- Use data attributes for JavaScript hooks

Example:
```html
<form data-form="signup" class="space-y-4">
    <input 
        type="email" 
        data-input="email"
        class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-primary"
    >
</form>
```

### 15.3 Component Structure

#### Template Organization
- Keep components focused and single-purpose
- Use consistent naming conventions
- Document component props and events
- Implement proper error boundaries

Example:
```html
<!-- Card Component -->
<div class="rounded-lg shadow-card p-4">
    <div class="space-y-2">
        <h3 class="text-lg font-semibold">{{ title }}</h3>
        <p class="text-gray-600">{{ description }}</p>
    </div>
    <!-- Actions -->
    <div class="mt-4 flex justify-end space-x-2">
        <slot name="actions"></slot>
    </div>
</div>
```

### 15.4 Best Practices

- Use semantic HTML elements
- Ensure proper accessibility attributes
- Implement proper loading states
- Maintain consistent spacing and typography
- Follow mobile-first responsive design
- Use proper error handling patterns
- Implement proper form validation
- Keep components modular and reusable

## 16. UI Style Guide & Design System

### 16.1 Design Tokens

#### Colors
- Define semantic color tokens instead of raw values
- Include light/dark mode variants
- Maintain consistent color hierarchy

```javascript
theme: {
  colors: {
    // Base colors with semantic meaning
    primary: {
      DEFAULT: 'rgb(var(--color-primary) / <alpha-value>)',
      light: 'rgb(var(--color-primary-light) / <alpha-value>)',
      dark: 'rgb(var(--color-primary-dark) / <alpha-value>)',
    },
    surface: {
      DEFAULT: 'rgb(var(--color-surface) / <alpha-value>)',
      raised: 'rgb(var(--color-surface-raised) / <alpha-value>)',
      sunken: 'rgb(var(--color-surface-sunken) / <alpha-value>)',
    },
    text: {
      DEFAULT: 'rgb(var(--color-text) / <alpha-value>)',
      muted: 'rgb(var(--color-text-muted) / <alpha-value>)',
      inverted: 'rgb(var(--color-text-inverted) / <alpha-value>)',
    }
  }
}
```

#### Spacing
- Use consistent spacing scale
- Define relationship between elements
```javascript
spacing: {
  xs: '0.25rem',   // 4px - Minimal spacing
  sm: '0.5rem',    // 8px - Tight spacing
  md: '1rem',      // 16px - Standard spacing
  lg: '1.5rem',    // 24px - Comfortable spacing
  xl: '2rem',      // 32px - Section spacing
  '2xl': '4rem',   // 64px - Major section spacing
}
```

### 16.2 Component Patterns

#### Cards
```html
<!-- Base Card -->
<div class="rounded-lg bg-surface p-4 shadow-sm">
  <!-- Card Header -->
  <div class="flex items-center justify-between mb-4">
    <h3 class="text-lg font-semibold">Card Title</h3>
    <div class="flex gap-2"><!-- Actions --></div>
  </div>
  
  <!-- Card Content -->
  <div class="space-y-4">
    <!-- Content blocks -->
  </div>
  
  <!-- Card Footer -->
  <div class="mt-4 pt-4 border-t border-surface-raised">
    <!-- Footer content -->
  </div>
</div>
```

#### Forms
```html
<!-- Form Group -->
<div class="space-y-2">
  <label class="block text-sm font-medium text-text-muted">
    Field Label
  </label>
  <input 
    type="text"
    class="w-full px-3 py-2 rounded-md border border-surface-raised
           focus:ring-2 focus:ring-primary focus:border-primary"
  >
  <p class="text-sm text-text-muted">Helper text</p>
</div>
```

### 16.3 Layout Guidelines

#### Spacing Hierarchy
- Content sections: `gap-8` or `space-y-8`
- Related elements: `gap-4` or `space-y-4`
- Tight grouping: `gap-2` or `space-y-2`

#### Container Widths
```html
<!-- Main content container -->
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
  <!-- Content -->
</div>

<!-- Narrow content container -->
<div class="max-w-3xl mx-auto">
  <!-- Focused content -->
</div>
```

### 16.4 Typography System

#### Text Styles
```html
<!-- Headings -->
<h1 class="text-4xl font-bold tracking-tight">Page Title</h1>
<h2 class="text-2xl font-semibold">Section Title</h2>
<h3 class="text-xl font-medium">Subsection Title</h3>

<!-- Body Text -->
<p class="text-base text-text">Regular text</p>
<p class="text-sm text-text-muted">Supporting text</p>

<!-- Interactive Text -->
<a class="text-primary hover:text-primary-dark 
          underline-offset-4 hover:underline">Link Text</a>
```

### 16.5 Interactive Elements

#### Buttons
```html
<!-- Primary Button -->
<button class="px-4 py-2 rounded-md bg-primary text-white
               hover:bg-primary-dark focus:ring-2 focus:ring-primary/50
               transition-colors">
  Primary Action
</button>

<!-- Secondary Button -->
<button class="px-4 py-2 rounded-md border border-primary
               text-primary hover:bg-primary/10
               focus:ring-2 focus:ring-primary/50
               transition-colors">
  Secondary Action
</button>
```

### 16.6 Responsive Patterns

#### Adaptive Layouts
```html
<!-- Grid Layout -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
  <!-- Grid items -->
</div>

<!-- Flex Layout -->
<div class="flex flex-col sm:flex-row items-start gap-4">
  <!-- Flex items -->
</div>
```

### 16.7 Animation Guidelines

#### Transitions
```html
<!-- Hover State -->
<div class="transform transition-all duration-300
            hover:scale-105 hover:shadow-lg">
  <!-- Content -->
</div>

<!-- Loading State -->
<div class="animate-pulse bg-surface-raised rounded-md h-32">
  <!-- Loading placeholder -->
</div>
```

### 16.8 Dark Mode Support

#### Color Adaptation
```html
<div class="bg-white dark:bg-gray-800
            text-gray-900 dark:text-gray-100">
  <!-- Content adapts to dark mode -->
</div>
```

### 16.9 Accessibility Guidelines

- Use sufficient color contrast (WCAG AA minimum)
- Implement proper focus states
- Include proper ARIA attributes
- Ensure keyboard navigation
- Provide screen reader context

```html
<!-- Accessible Button -->
<button 
  aria-label="Delete item"
  class="p-2 rounded-full hover:bg-surface-raised
         focus:outline-none focus:ring-2 focus:ring-primary"
>
  <span class="sr-only">Delete</span>
  <!-- Icon -->
</button>
```

## 17. Integration Standards

### 17.1 Directory Structure

- Place all integrations in `app/Integrations/{ProviderName}`
- Use PascalCase for provider directories
- Group related integration files within provider directory
- Include provider-specific interfaces and contracts

Example structure:
```
app/
└── Integrations/
    ├── KonseKonse/
    │   └── cGrate.php
    ├── MPGS/
    │   └── MasterCardCheckout.php
    ├── TechPay/
    │   └── HostedCheckOut.php
    └── Contracts/
        └── PaymentGatewayInterface.php
```

### 17.2 Class Configuration

#### Credentials Management
- Store credentials in `.env`
- Use config files for provider settings
- Follow naming convention: `PROVIDER_NAME_CREDENTIAL`
```php
private string $endpoint = "https://543.cgrate.co.zm/Konik/KonikWs";
private string $username;
private string $password;

public function __construct()
{
    $this->username = config('services.cgrate.username');
    $this->password = config('services.cgrate.password');
}
```

#### Constants and Static Properties
- Define service-specific constants at class level
- Use UPPER_SNAKE_CASE for constants
- Group related constants in arrays
```php
public static $mobileVouchers = [
    'EF52DTHN2', //Airtel
    'EF52DDRS7', //MTN
    'EF1GHRID1'  //Zamtel
];

public static $queryVouchers = [
    'EM3GAQAR2', //DStv-Box office
    'ELOA1SA26', //DStv
    'ELOA1XKZ1', //GOtv
    'ERM2VV456', //Zesco
];
```

### 17.3 API Communication

#### Request Handling
- Use consistent HTTP client implementation
- Implement proper timeout handling
- Include request logging
```php
public function sendMessage(string $businessPhoneNumberId, string $from, string $messageId, string $text)
{
    return Http::withToken($this->graphApiToken)
        ->timeout(30)
        ->post($this->endpoint . "/{$businessPhoneNumberId}/messages", [
            'messaging_product' => 'whatsapp',
            'to' => $from,
            'text' => ['body' => $text],
            'context' => [
                'message_id' => $messageId,
            ],
        ]);
}
```

#### Response Processing
- Implement consistent response format
- Handle API errors uniformly
- Log response data appropriately
```php
protected function processResponse($response): array
{
    try {
        $result = json_decode($response->body(), true);
        
        return [
            'status' => 'SUCCESS',
            'statusText' => 'Successfully processed response',
            'data' => $result
        ];
    } catch (\Exception $e) {
        Log::error('Integration response processing failed', [
            'error' => $e->getMessage(),
            'response' => $response->body()
        ]);
        
        return [
            'status' => 'ERROR',
            'statusText' => 'Failed to process response',
            'error' => $e->getMessage()
        ];
    }
}
```

### 17.4 Error Handling

#### Exception Management
- Create integration-specific exceptions
- Implement proper error logging
- Handle timeouts and connection errors
```php
try {
    $response = $this->makeApiCall();
} catch (ConnectionException $e) {
    Log::error('Integration connection failed', [
        'provider' => 'CGRATE',
        'error' => $e->getMessage()
    ]);
    throw new IntegrationException('Failed to connect to service');
} catch (TimeoutException $e) {
    Log::error('Integration timeout', [
        'provider' => 'CGRATE',
        'error' => $e->getMessage()
    ]);
    throw new IntegrationException('Service timeout');
}
```

### 17.5 Testing

#### Integration Tests
- Create provider-specific test cases
- Mock external API calls
- Test error scenarios
```php
public function test_cgrate_voucher_purchase()
{
    Http::fake([
        'cgrate.co.zm/*' => Http::response([
            'status' => 'success',
            'voucherCode' => 'TEST123'
        ], 200)
    ]);

    $response = $this->cGrateService->purchaseVoucher([
        'amount' => 100,
        'provider' => 'MTN'
    ]);

    $this->assertEquals('SUCCESS', $response['status']);
}
```

### 17.6 Documentation

#### Code Documentation
- Document all public methods
- Include request/response examples
- Document configuration requirements
```php
/**
 * Process voucher purchase or validation
 *
 * @param string $param Either 'purchaseVoucher' or 'validateVoucherPurchase'
 * @param string $distributionChannel The distribution channel code
 * @param bool $isFixed Whether the voucher is fixed value
 * @param string $recipient The recipient identifier
 * @param string $serviceProvider The service provider code
 * @param string $transactionReference Unique transaction reference
 * @param string $voucherType The type of voucher
 * @param float $voucherValue The voucher value
 * @return array
 */
public function processVoucher($param, $distributionChannel, $isFixed, $recipient, $serviceProvider, $transactionReference, $voucherType, $voucherValue): array
```

### 17.7 Security

#### Data Protection
- Encrypt sensitive data
- Use secure communication channels
- Implement proper authentication
```php
protected function encryptPayload(array $payload): string
{
    return encrypt(json_encode($payload));
}

protected function decryptResponse(string $response): array
{
    return json_decode(decrypt($response), true);
}
```

### 17.8 Monitoring

#### Performance Tracking
- Log API response times
- Track success/failure rates
- Monitor integration health
```php
public function makeApiCall()
{
    $startTime = microtime(true);
    
    try {
        $response = $this->client->request();
        
        Helpers::LogPerformance(
            'API_CALL',
            'INTEGRATION',
            $this->providerName,
            null,
            'SUCCESS',
            $response->status(),
            microtime(true) - $startTime
        );
        
        return $response;
    } catch (\Exception $e) {
        Helpers::LogPerformance(
            'API_CALL',
            'INTEGRATION',
            $this->providerName,
            null,
            'ERROR',
            $e->getMessage(),
            microtime(true) - $startTime
        );
        
        throw $e;
    }
}
```

### 17.9 Versioning

#### API Version Management
- Include version in integration path
- Handle multiple API versions
- Document version compatibility
```php
namespace App\Integrations\TechPay\V2;

class HostedCheckOut extends BaseIntegration
{
    protected $apiVersion = 'v2';
    protected $baseUrl = 'https://api.techpay.com/v2/';
}
```

## Route Standards and Best Practices

### 1. Route Organization

#### 1.1 Route Files Structure
```
routes/
├── api.php       # API routes
├── web.php       # Web routes
├── channels.php  # Broadcasting channels
├── console.php   # Console commands
└── botman.php    # Botman routes
```

#### 1.2 Route Grouping
Group related routes using prefixes and middlewares:

```php
// Admin/Backend routes
Route::prefix('backend')->group(function () {
    Route::get('/login', [Authentication::class, 'index']);
    
    Route::group(['middleware' => ['admin']], function () {
        Route::get('home', [DashboardController::class, 'dashboard']);
        
        // Merchants group
        Route::prefix('merchants')->group(function () {
            Route::get('list', [MerchantsController::class, 'index']);
            Route::get('details/{id}', [MerchantsController::class, 'details']);
            Route::get('create', [MerchantsController::class, 'create']);
        });
    });
});

// Merchant portal routes
Route::prefix('merchants')->group(function () {
    Route::group(['middleware' => ['merchant']], function () {
        Route::get('home', [DashboardController::class, 'dashboard']);
        Route::get('reports/payments', [ReportsController::class, 'getTransactions']);
    });
});
```

### 2. Naming Conventions

#### 2.1 URL Structure
- Use kebab-case for URLs
- Use plural for resource collections
- Use singular for single resources
```php
// Good
Route::get('merchant-applications/list', [MerchantApplicationsController::class, 'index']);
Route::get('merchant-application/{id}', [MerchantApplicationsController::class, 'show']);

// Avoid
Route::get('merchantApplications/list', [MerchantApplicationsController::class, 'index']);
```

#### 2.2 Controller Action Naming
```php
// Standard CRUD actions
Route::get('merchants/list', [MerchantsController::class, 'index']);     // List all
Route::get('merchants/create', [MerchantsController::class, 'create']);  // Show create form
Route::post('merchants/save', [MerchantsController::class, 'save']);     // Store new
Route::get('merchants/details/{id}', [MerchantsController::class, 'details']); // Show single
```

### 3. Route Parameters

#### 3.1 Parameter Naming
```php
// Good - Clear parameter names
Route::get('pay/{maid}/bills', [Collections::class, 'getBills']);
Route::get('query/{maid}/receipt/{id}', [QueryCollections::class, 'printReceipt']);

// Avoid - Unclear names
Route::get('pay/{x}/bills', [Collections::class, 'getBills']);
```

#### 3.2 Optional Parameters
```php
Route::get('reports/{type?}', [ReportsController::class, 'index']);
```

### 4. HTTP Methods

#### 4.1 RESTful Convention
```php
Route::get('bills/{accountNumber}', [CollectionsAPI::class, 'queryAccount']);     // Read
Route::post('billers/pay', [CollectionsAPI::class, 'createTransaction']);         // Create
Route::put('merchant/{id}', [MerchantController::class, 'update']);              // Update
Route::delete('merchant/{id}', [MerchantController::class, 'delete']);           // Delete
```

#### 4.2 Multiple Methods
```php
Route::match(['get', 'post'], '/botman', [BotManController::class, 'handle']);
```

### 5. Middleware

#### 5.1 Route Middleware
```php
Route::middleware('admin')->group(function () {
    // Protected admin routes
});

Route::middleware('merchant')->group(function () {
    // Protected merchant routes
});
```

#### 5.2 CSRF Protection
Define exceptions in `VerifyCsrfToken.php`:
```php
protected $except = [
    '/backend/merchants/app/uploadDocuments',
    '/backend/authenticate',
    'pay/*/getAmounts',
    'botman'
];
```

### 6. API Routes

#### 6.1 API Version Prefix
```php
Route::prefix('api/v1')->group(function () {
    Route::get('bills/{accountNumber}', [CollectionsAPI::class, 'queryAccount']);
    Route::get('billers/getServices', [CollectionsAPI::class, 'getProducts']);
});
```

#### 6.2 API Rate Limiting
```php
RateLimiter::for('api', function (Request $request) {
    return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
});
```

## 19. Command Standards

### 19.1 Command Structure

#### Basic Structure
- Place all commands in `app/Console/Commands`
- Use kebab-case for command names
- Follow Laravel's command naming conventions

Example command structure:
```php
namespace App\Console\Commands;

use Illuminate\Console\Command;

class UpdateTransactionStatus extends Command
{
    protected $signature = 'app:update-transaction-status';
    protected $description = 'Updates the status of pending transactions';
    
    public function handle()
    {
        // Command logic here
    }
}
```

#### Command Registration
- Register commands in `routes/console.php`
- Use appropriate scheduling intervals
- Implement proper overlap protection

Example registration:
```php
protected function schedule(Schedule $schedule): void
{
    $schedule->command('app:update-transaction-status')
        ->everyFiveMinutes()
        ->withoutOverlapping()
        ->runInBackground();
}
```

### 19.2 Command Naming

#### File Naming
- Use PascalCase for file names
- Match class name exactly
- End with `.php` extension

Examples:
```
UpdateTransactionStatus.php
CheckExpiredPayments.php
SendGlobalReport.php
```

#### Command Signatures
- Prefix with `app:`
- Use kebab-case
- Include required arguments in signature

Examples:
```php
protected $signature = 'app:check-expired-payments';
protected $signature = 'app:send-report {type} {--date=}';
protected $signature = 'app:settle-bills {merchant?}';
```

### 19.3 Command Implementation

#### Error Handling
- Use try-catch blocks
- Log errors appropriately
- Return meaningful exit codes

Example:
```php
public function handle()
{
    try {
        $result = $this->processTransactions();
        $this->info('Successfully processed transactions');
        return Command::SUCCESS;
    } catch (\Exception $e) {
        $this->error('Failed to process transactions: ' . $e->getMessage());
        Log::error('Command failed', [
            'command' => $this->signature,
            'error' => $e->getMessage()
        ]);
        return Command::FAILURE;
    }
}
```

#### Progress Indication
- Use progress bars for long operations
- Provide meaningful output
- Include success/failure messages

Example:
```php
public function processItems(array $items)
{
    $bar = $this->output->createProgressBar(count($items));
    $bar->start();

    foreach ($items as $item) {
        $this->processItem($item);
        $bar->advance();
    }

    $bar->finish();
    $this->newLine();
}
```

### 19.4 Command Documentation

#### Code Documentation
- Include PHPDoc blocks
- Document arguments and options
- Provide usage examples

Example:
```php
/**
 * Process pending transactions and update their status
 * 
 * @return int
 * @throws \Exception When API connection fails
 */
public function handle()
{
    // Implementation
}
```

#### Help Text
- Provide clear description
- Document all arguments
- Include example usage

Example:
```php
protected $signature = 'app:process-transactions
                       {type : The type of transactions to process}
                       {--date= : Optional date filter}';

protected $help = 'This command processes pending transactions and updates their status.
Usage: php artisan app:process-transactions payment --date=2023-01-01';
```

### 19.5 Testing Commands

#### Test Structure
- Create dedicated test classes
- Test different scenarios
- Mock external dependencies

Example:
```php
public function test_command_processes_transactions()
{
    $this->mock(TransactionService::class)
        ->shouldReceive('processTransactions')
        ->once()
        ->andReturn(true);

    $this->artisan('app:process-transactions')
        ->assertSuccessful()
        ->expectsOutput('Successfully processed transactions');
}
```

### 19.6 Scheduling Standards

#### Schedule Definition
- Group related schedules
- Use appropriate frequencies
- Prevent schedule overlaps

Example in `routes/console.php`:
```php
protected function schedule(Schedule $schedule): void
{
    // Transaction processing
    $schedule->command('app:update-transaction-status')
        ->everyThirtySeconds()
        ->withoutOverlapping()
        ->runInBackground();

    // Daily operations
    $schedule->command('app:check-expired-payments')
        ->dailyAt('03:57')
        ->withoutOverlapping()
        ->runInBackground();

    // Reporting
    $schedule->command('app:send-global-report')
        ->dailyAt('23:57')
        ->withoutOverlapping()
        ->runInBackground();
}
```

### 19.7 Maintenance

#### Logging
- Log command execution
- Track execution time
- Monitor failure rates

Example:
```php
public function handle()
{
    $startTime = microtime(true);
    
    try {
        // Command logic
        
        Log::info('Command completed', [
            'command' => $this->signature,
            'duration' => microtime(true) - $startTime,
            'status' => 'success'
        ]);
    } catch (\Exception $e) {
        Log::error('Command failed', [
            'command' => $this->signature,
            'duration' => microtime(true) - $startTime,
            'error' => $e->getMessage()
        ]);
    }
}
```

#### Performance
- Implement batch processing
- Use queues for long-running tasks
- Monitor memory usage

Example:
```php
public function handle()
{
    Transaction::chunk(100, function ($transactions) {
        foreach ($transactions as $transaction) {
            ProcessTransaction::dispatch($transaction);
        }
    });
}
```

## 20. Helper and Constants Standards

### 20.1 Constants Structure

#### Organization
- Place all constants in `app/Common/Constants.php`
- Group related constants together
- Use descriptive constant names

Example structure:
```php
class Constants
{
    // Status constants
    const PAYMENT_STATUSES = [
        'PENDING' => 'Pending',
        'SUCCESSFUL' => 'Successful',
        'FAILED' => 'Failed',
    ];

    const USER_CLASSES = [
        'ADMIN' => 'Admin',
        'MERCHANT' => 'Merchant',
        'CUSTOMER' => 'Customer',
    ];
}
```

#### Naming Conventions
- Use UPPER_SNAKE_CASE for constant names
- Use descriptive prefixes for grouping
- Keep names clear and unambiguous

Examples:
```php
const TRANSACTION_TYPE_PAYMENT = 'PAYMENT';
const TRANSACTION_TYPE_REFUND = 'REFUND';
const TRANSACTION_STATUS_PENDING = 'PENDING';
```

#### Value Standards
- Use consistent value formats
- Include human-readable labels where needed
- Document special values or formats

Example:
```php
const NOTIFICATION_TYPES = [
    'SMS' => [
        'label' => 'SMS Notification',
        'handler' => SmsNotificationHandler::class
    ],
    'EMAIL' => [
        'label' => 'Email Notification',
        'handler' => EmailNotificationHandler::class
    ]
];
```

### 20.2 Helper Methods

#### Organization
- Place helpers in `app/Common/Helpers.php`
- Group related functions together
- Keep methods focused and single-purpose

#### Method Standards
- Use descriptive names in camelCase
- Document parameters and return types
- Include validation where appropriate

Example:
```php
/**
 * Validates a Zambian mobile number
 *
 * @param string $mobile The mobile number to validate
 * @return bool Whether the number is valid
 */
public static function isValidZambianMobileNumber($mobile): bool
{
    $zambian_mobile_regex = '/^(?:\+?26)?0[97][567]\d{7}$/';
    return preg_match($zambian_mobile_regex, $mobile);
}
```

#### Response Formatting
- Use consistent return formats
- Handle errors gracefully
- Return typed responses where possible

Example:
```php
/**
 * Builds a standardized API response
 *
 * @param string $statusCode The status code
 * @param string $statusDescription The status description
 * @return array The formatted response
 */
public static function buildApiResponse(string $statusCode, string $statusDescription): array
{
    return [
        'statusCode' => $statusCode,
        'statusDescription' => $statusDescription
    ];
}
```

### 20.3 Helper Categories

#### Validation Helpers
- Input validation methods
- Format verification
- Data type checking

Example:
```php
public static function validateNumberVsNetwork(string $number, string $provider): bool
{
    return match ($provider) {
        'MTN' => str_starts_with($number, '096') || str_starts_with($number, '076'),
        'Airtel' => str_starts_with($number, '097') || str_starts_with($number, '077'),
        'Zamtel' => str_starts_with($number, '095') || str_starts_with($number, '075'),
        default => false
    };
}
```

#### Formatting Helpers
- Data formatting methods
- Text transformation
- Number formatting

Example:
```php
public static function formatAmount(float $amount, string $currency = 'ZMW'): string
{
    return number_format($amount, 2) . ' ' . $currency;
}
```

#### Utility Helpers
- Common utility functions
- Date/time handling
- String manipulation

Example:
```php
public static function getGreetingSalutation(): string
{
    $hour = (int) date('H');
    return match (true) {
        $hour < 12 => 'Good morning',
        $hour < 17 => 'Good afternoon',
        default => 'Good evening'
    };
}
```

### 20.4 Error Handling in Helpers

#### Exception Handling
- Use try-catch blocks appropriately
- Return meaningful defaults
- Log errors when necessary

Example:
```php
public static function getBotFud($userId, $source, $module, $type): array
{
    try {
        return DB::table('bot_user_fuds')
            ->select('system_value', 'friendly_value')
            ->where('user_id', $userId)
            ->where('source', $source)
            ->where('module', $module)
            ->where('type', $type)
            ->distinct()
            ->limit(3)
            ->pluck('system_value', 'friendly_value')
            ->toArray();
    } catch (\Exception $e) {
        Log::error('Error fetching bot FUD', [
            'error' => $e->getMessage(),
            'userId' => $userId
        ]);
        return [];
    }
}
```

### 20.5 Testing Standards

#### Helper Testing
- Test all helper methods
- Include edge cases
- Test error scenarios

Example:
```php
public function test_validates_zambian_mobile_number()
{
    $this->assertTrue(Helpers::isValidZambianMobileNumber('0977123456'));
    $this->assertTrue(Helpers::isValidZambianMobileNumber('0967123456'));
    $this->assertFalse(Helpers::isValidZambianMobileNumber('0990123456'));
}
```

#### Constants Testing
- Verify constant values
- Test constant usage
- Validate array structures

Example:
```php
public function test_payment_statuses_contain_required_states()
{
    $this->assertArrayHasKey('PENDING', Constants::PAYMENT_STATUSES);
    $this->assertArrayHasKey('SUCCESSFUL', Constants::PAYMENT_STATUSES);
    $this->assertArrayHasKey('FAILED', Constants::PAYMENT_STATUSES);
}
```

### 20.6 Documentation

#### Helper Documentation
- Include PHPDoc blocks
- Document parameters and return types
- Provide usage examples

Example:
```php
/**
 * Pluralizes a word based on count
 *
 * @param string $text The word to pluralize
 * @param int $count The count to base pluralization on
 * @return string The pluralized text with count
 *
 * @example
 * pluralize('apple', 1) // returns "1 apple"
 * pluralize('apple', 2) // returns "2 apples"
 */
public static function pluralize(string $text, int $count): string
{
    if (str_ends_with($text, 's')) {
        $text = substr($text, 0, -1);
    }
    return $count . ' ' . $text . ($count === 1 ? '' : 's');
}
```

#### Constants Documentation
- Document constant groups
- Explain value formats
- Include usage context

Example:
```php
/**
 * Payment status constants used throughout the application
 * These statuses represent the various states a payment can be in
 */
const PAYMENT_STATUSES = [
    'PENDING' => 'Pending',     // Initial state when payment is created
    'SUCCESSFUL' => 'Successful', // Payment has been confirmed
    'FAILED' => 'Failed',       // Payment attempt has failed
];
```

## 21. Model Standards

### 21.1 Basic Structure

#### File Organization
- One model per file
- Place all models in `app/Models` directory
- Use singular PascalCase for model names
- Match filename to class name

Example:
```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MerchantApplication extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'class',
        'slug',
        'status',
        'merchant_id',
    ];
}
```

#### Property Declarations
- Declare properties in consistent order:
  1. Traits
  2. Properties (`$fillable`, `$guarded`, `$casts`, etc.)
  3. Relationships
  4. Scopes
  5. Accessors/Mutators
  6. Helper methods

Example:
```php
class Transaction extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = ['reference', 'amount'];
    
    protected $casts = [
        'amount' => 'decimal:2',
        'processed_at' => 'datetime',
    ];

    // Relationships
    public function merchant() {...}
    
    // Scopes
    public function scopePending($query) {...}
    
    // Accessors/Mutators
    public function getFormattedAmountAttribute() {...}
    
    // Helper methods
    public function markAsProcessed() {...}
}
```

### 21.2 Relationships

#### Naming Conventions
- Use descriptive relationship names
- Follow Laravel's naming conventions
- Use proper relationship types

Example:
```php
class Merchant extends Model
{
    // One-to-Many
    public function applications(): HasMany
    {
        return $this->hasMany(MerchantApplication::class);
    }

    // One-to-One
    public function primaryContact(): HasOne
    {
        return $this->hasOne(Contact::class)->where('is_primary', true);
    }

    // Many-to-Many
    public function paymentProviders(): BelongsToMany
    {
        return $this->belongsToMany(PaymentProvider::class);
    }
}
```

#### Type Hinting
- Always use return type hints for relationships
- Import relationship classes
- Use proper PHPDoc blocks

Example:
```php
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MiniStore extends Model
{
    public function items(): HasMany
    {
        return $this->hasMany(MiniStoreItem::class);
    }

    public function merchant(): BelongsTo
    {
        return $this->belongsTo(Merchant::class);
    }
}
```

### 21.3 Property Definitions

#### Fillable Properties
- List all mass-assignable fields
- Group related fields together
- Include comments for non-obvious fields

Example:
```php
protected $fillable = [
    // Basic information
    'name',
    'email',
    'phone',
    
    // Business details
    'merchant_code',
    'industry_id',
    'status',
    
    // Integration settings
    'api_key',
    'webhook_url',
];
```

#### Casts
- Define appropriate casts for attributes
- Use consistent date/time formats
- Cast boolean and numeric fields appropriately

Example:
```php
protected $casts = [
    'email_verified_at' => 'datetime',
    'is_active' => 'boolean',
    'settings' => 'array',
    'amount' => 'decimal:2',
];
```

### 21.4 Scopes and Queries

#### Query Scopes
- Use descriptive scope names
- Keep scopes focused and reusable
- Document complex query logic

Example:
```php
class Transaction extends Model
{
    public function scopeSuccessful($query)
    {
        return $query->where('status', 'SUCCESSFUL');
    }

    public function scopeWithinDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('created_at', [$startDate, $endDate]);
    }
}
```

#### Query Performance
- Use eager loading for relationships
- Index frequently queried columns
- Avoid N+1 query problems

Example:
```php
// In Controller
$transactions = Transaction::with(['merchant', 'paymentProvider'])
    ->successful()
    ->latest()
    ->paginate(20);
```

### 21.5 Accessors and Mutators

#### Naming Conventions
- Use descriptive names
- Follow Laravel's naming convention
- Document complex transformations

Example:
```php
class Transaction extends Model
{
    public function getFormattedAmountAttribute(): string
    {
        return number_format($this->amount, 2) . ' ' . $this->currency;
    }

    public function setPhoneAttribute($value)
    {
        $this->attributes['phone'] = preg_replace('/[^0-9]/', '', $value);
    }
}
```

### 21.6 Model Events

#### Event Handling
- Use model observers for complex logic
- Keep event handlers focused
- Document side effects

Example:
```php
class Transaction extends Model
{
    protected static function booted()
    {
        static::created(function ($transaction) {
            Log::info('New transaction created', [
                'reference' => $transaction->reference
            ]);
        });
    }
}
```

### 21.7 Validation

#### Rules Definition
- Define validation rules in the model
- Use constants for shared rules
- Document complex validation rules

Example:
```php
class Merchant extends Model
{
    public static function validationRules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:merchants,email',
            'phone' => 'required|regex:/^[0-9]{10}$/',
            'merchant_code' => 'required|unique:merchants,merchant_code',
        ];
    }
}
```

### 21.8 Security

#### Mass Assignment Protection
- Always define `$fillable` or `$guarded`
- Never use `$guarded = []`
- Hide sensitive attributes

Example:
```php
class User extends Model
{
    protected $fillable = [
        'name',
        'email',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'api_key',
    ];
}
```

### 21.9 Documentation

#### PHPDoc Blocks
- Document class purpose
- Document non-obvious relationships
- Include usage examples

Example:
```php
/**
 * Represents a merchant application in the system.
 * 
 * @property string $name
 * @property string $status
 * @property int $merchant_id
 * @property-read Merchant $merchant
 * @property-read Collection|PaymentProvider[] $paymentProviders
 */
class MerchantApplication extends Model
{
    // Implementation
}
```

### 21.10 Testing

#### Model Testing
- Test relationships
- Test scopes and queries
- Test accessors and mutators

Example:
```php
class MerchantTest extends TestCase
{
    public function test_it_has_many_applications()
    {
        $merchant = Merchant::factory()
            ->has(MerchantApplication::factory()->count(3))
            ->create();

        $this->assertCount(3, $merchant->applications);
    }
}
```

## 22. Migration Standards

### 22.1 File Naming

#### Convention
- Use timestamp prefix: `YYYY_MM_DD_HHMMSS`
- Use descriptive names in snake_case
- Follow action-based naming:
  - `create_*_table`
  - `add_*_to_*_table`
  - `alter_*_in_*_table`
  - `drop_*_from_*_table`

Examples:
```php
2024_03_16_091849_add_custom_form_fields_to_merchant_applications.php
2023_11_06_161907_create_merchants_table.php
2023_11_15_181630_alter_users_mobile_column.php
```

### 22.2 Class Structure

#### Class Naming
- Use PascalCase
- Match filename (without timestamp)
- Be descriptive and action-oriented

Example:
```php
class AddCustomFormFieldsToMerchantApplications extends Migration
{
    public function up()
    {
        // Implementation
    }

    public function down()
    {
        // Implementation
    }
}
```

### 22.3 Table Creation

#### Standards
- Use proper column types
- Add indexes where needed
- Include timestamps by default
- Use consistent column ordering

Example:
```php
Schema::create('merchants', function (Blueprint $table) {
    // Primary key first
    $table->increments('id');
    
    // Required columns
    $table->string('name');
    $table->string('merchant_code')->unique();
    
    // Optional columns
    $table->string('email')->nullable();
    $table->text('address')->nullable();
    
    // Foreign keys
    $table->foreignId('user_id')->constrained();
    
    // Status and metadata
    $table->string('status')->default('ACTIVE');
    
    // Timestamps last
    $table->timestamps();
});
```

### 22.4 Column Modifications

#### Adding Columns
- Specify column position using `after()`/`first()`
- Include appropriate defaults
- Consider nullable status

Example:
```php
Schema::table('merchant_applications', function (Blueprint $table) {
    $table->boolean('qty_is_enabled')
        ->after('name')
        ->default(false);
    
    $table->string('qty_label')
        ->after('qty_is_enabled')
        ->nullable();
});
```

#### Modifying Columns
- Use `change()` method
- Always implement down() method
- Document changes in comments

Example:
```php
Schema::table('users', function (Blueprint $table) {
    // Converting mobile from integer to string
    $table->string('mobile')->change();
});
```

### 22.5 Foreign Keys

#### Convention
- Use consistent naming pattern
- Include cascade/restrict actions
- Consider index creation

Example:
```php
Schema::table('transactions_breakdowns', function (Blueprint $table) {
    $table->foreign('merchant_application_id')
        ->references('id')
        ->on('merchant_applications')
        ->onDelete('cascade');
});
```

### 22.6 Down Methods

#### Implementation
- Always implement down() method
- Reverse changes in correct order
- Handle complex scenarios

Example:
```php
public function down(): void
{
    Schema::table('transactions', function (Blueprint $table) {
        $table->dropColumn([
            'reference_1',
            'reference_2',
            'reference_3'
        ]);
    });
}
```

### 22.7 Column Types

#### Usage Guidelines
- Use appropriate column types:
  - `string()` for short text
  - `text()` for long content
  - `integer()` for numbers
  - `decimal()` for currency
  - `boolean()` for flags
  - `json()` for structured data
  - `timestamp()` for dates

Example:
```php
Schema::create('merchant_applications', function (Blueprint $table) {
    $table->increments('id');
    $table->string('name', 100);
    $table->text('description');
    $table->decimal('amount', 10, 2);
    $table->boolean('is_active');
    $table->json('custom_form_fields');
    $table->timestamp('processed_at')->nullable();
});
```

### 22.8 Indexes

#### Best Practices
- Add indexes for frequently queried columns
- Consider composite indexes
- Index foreign keys

Example:
```php
Schema::create('transactions', function (Blueprint $table) {
    $table->id();
    $table->string('reference')->unique();
    $table->string('status');
    
    // Single column index
    $table->index('status');
    
    // Composite index
    $table->index(['merchant_id', 'created_at']);
});
```

### 22.9 Data Migrations

#### Guidelines
- Use separate migrations for data changes
- Implement transaction wrapping
- Include error handling

Example:
```php
public function up()
{
    DB::transaction(function () {
        $merchants = DB::table('merchants')
            ->where('status', 'ACTIVE')
            ->get();

        foreach ($merchants as $merchant) {
            DB::table('merchant_applications')
                ->where('merchant_id', $merchant->id)
                ->update(['status' => 'ACTIVE']);
        }
    });
}
```

### 22.10 Testing

#### Verification
- Test both up() and down() methods
- Verify column types and constraints
- Check foreign key relationships

Example:
```php
class MigrationTest extends TestCase
{
    public function test_merchants_table_creation()
    {
        // Run the migration
        $this->artisan('migrate');

        // Verify table structure
        $this->assertTrue(Schema::hasTable('merchants'));
        $this->assertTrue(Schema::hasColumn('merchants', 'merchant_code'));
        
        // Test constraints
        $this->assertTrue(
            Schema::getConnection()
                ->getDoctrineColumn('merchants', 'merchant_code')
                ->getNotnull()
        );
    }
}
```

### 22.11 Documentation

#### Comments
- Document complex migrations
- Explain business logic
- Note dependencies

Example:
```php
/**
 * Add custom form fields to merchant applications
 * This enables dynamic form building functionality
 * Depends on: 2024_02_13_120644_add_qty_params_to_merchant_applications
 */
class AddCustomFormFieldsToMerchantApplications extends Migration
{
    public function up()
    {
        Schema::table('merchant_applications', function (Blueprint $table) {
            // JSON field for storing form builder configuration
            $table->json('custom_form_fields')->nullable();
            
            // Separate field for amount configuration
            $table->json('custom_form_amount_field')->nullable();
        });
    }
}
```

## 23. PDF Generation Standards

### 23.1 Configuration

#### DomPDF Setup
- Use configuration file `config/dompdf.php`
- Set consistent paper settings:
  - Default to A4 portrait
  - Use standard margins
- Configure backend renderer in config:
```php
"pdf_backend" => "CPDF"
```

### 23.2 PDF Generation Methods

#### Standard Method
- Use Laravel's PDF facade
- Consistent method chaining
- Standard paper configuration

Example:
```php
$pdf = \PDF::loadView('path.to.view', $data)
    ->setPaper('a4', 'portrait');
return $pdf->stream('document.pdf');
```

### 23.3 Data Structure

#### Standard Data Array
- Use consistent key naming
- Include all required data
- Group related data

Example:
```php
$data = [
    'transaction' => $transaction,
    'payments' => $transaction->breakdown,
    'kyc' => $transaction->kyc,
    'total' => $transaction->amount,
    'app' => $apps,
    'merchant' => $apps->merchant,
];
```

### 23.4 View Templates

#### Organization
- Place PDF templates in dedicated directories:
  - `resources/views/frontend/{module}/receipt.blade.php`
  - `resources/views/pdf/layouts/master.blade.php`
- Use consistent naming convention

#### Structure
```blade
@extends('pdf.layouts.master')

@section('content')
    <div class="receipt-container">
        <!-- Header Section -->
        <div class="header">
            @include('pdf.partials.header')
        </div>

        <!-- Content Sections -->
        <div class="content">
            @include('pdf.partials.transaction-details')
            @include('pdf.partials.payment-breakdown')
        </div>

        <!-- Footer Section -->
        <div class="footer">
            @include('pdf.partials.footer')
        </div>
    </div>
@endsection
```

### 23.5 Error Handling

#### Transaction Validation
- Check transaction status before generation
- Return appropriate error responses
- Log PDF generation failures

Example:
```php
if ($transaction->status != 'COMPLETE') {
    Log::warning("Attempted to generate PDF for incomplete transaction: $reference");
    return redirect()->back()->withError('Transaction not complete');
}
```

### 23.6 Performance

#### Optimization
- Use eager loading for related data
- Implement caching where appropriate
- Optimize template rendering

Example:
```php
$transaction = Transactions::with(['breakdown', 'kyc'])
    ->where('reference', $reference)
    ->firstOrFail();
```

### 23.7 Security

#### Access Control
- Validate user permissions
- Implement rate limiting
- Sanitize input data

Example:
```php
if (!auth()->user()->canGeneratePdf($transaction)) {
    abort(403, 'Unauthorized access to PDF generation');
}
```

### 23.8 Logging

#### Standard Logging
- Log PDF generation attempts
- Include relevant transaction data
- Track performance metrics

Example:
```php
Helpers::LogPerformance(
    'PDF_GENERATION',
    'RECEIPT_PRINTED',
    $request->maid,
    $reference,
    'COLLECTION',
    $user_ip,
    $user_agent,
    $reference
);
```

### 23.9 Response Handling

#### Stream vs Download
- Use `stream()` for viewing
- Use `download()` for saving
- Include proper headers

Example:
```php
// For viewing in browser
return $pdf->stream('invoice.pdf');

// For downloading
return $pdf->download('invoice.pdf');
```

### 23.10 Styling

#### CSS Standards
- Use dedicated PDF stylesheets
- Implement print-specific styles
- Follow consistent formatting

Example:
```css
.receipt-container {
    font-family: 'Arial', sans-serif;
    line-height: 1.6;
    margin: 20mm;
}

.transaction-details {
    border-collapse: collapse;
    width: 100%;
}
```

### 23.11 Testing

#### PDF Generation Tests
- Test template rendering
- Verify data inclusion
- Check file generation

Example:
```php
public function test_pdf_generation()
{
    $transaction = Transaction::factory()->create();
    
    $response = $this->get("/generate-pdf/{$transaction->reference}");
    
    $response->assertStatus(200);
    $response->assertHeader('content-type', 'application/pdf');
}
```

### 23.12 Reusability

#### Component Structure
- Create reusable PDF components
- Standardize header/footer templates
- Use consistent styling

Example:
```php
// PDF Service Class
class PdfService
{
    public function generateReceipt($transaction, $template)
    {
        $data = $this->prepareData($transaction);
        return \PDF::loadView($template, $data)
            ->setPaper('a4', 'portrait');
    }

    protected function prepareData($transaction)
    {
        return [
            'transaction' => $transaction,
            'payments' => $transaction->breakdown,
            'kyc' => $transaction->kyc,
            'total' => $transaction->amount,
            'app' => $transaction->app,
            'merchant' => $transaction->app->merchant,
        ];
    }
}
```

### 23.13 Maintenance

#### Version Control
- Track template changes
- Document PDF structure updates
- Maintain changelog for layouts

Example:
```php
/**
 * @version 2.0.0
 * @changelog
 * - Added QR code to receipts
 * - Updated header layout
 * - Modified payment breakdown table
 */
```


## 24. Asset Structure and Helpers

### 24.1 Asset Organization

#### Directory Structure
```
public/assets/
├── css/
│   ├── app.css
│   ├── custom/
│   │   ├── forms.css
│   │   ├── tables.css
│   │   └── modals.css
│   └── themes/
│       ├── light.css
│       └── dark.css
├── js/
│   ├── app.js
│   ├── helpers/
│   │   ├── datatables.js
│   │   ├── overlay.js
│   │   └── forms.js
│   └── modules/
│       ├── payments.js
│       └── merchants.js
├── libs/
│   ├── DataTables/
│   ├── jquery/
│   └── plyr/
├── fonts/
├── images/
└── vendor/
```

### 24.2 JavaScript Helpers

#### 24.2.1 Page Preloader
```javascript
// helpers/preloader.js
const PageLoader = {
    show: () => {
        $(".page-loader").show();
    },
    hide: () => {
        $(".page-loader").fadeOut("fast");
    },
    init: () => {
        $(window).on('load', PageLoader.hide);
    }
};

// Usage
PageLoader.init();
```

#### 24.2.2 Overlay/Blocker
```javascript
// helpers/overlay.js
const Overlay = {
    show: (message, selector) => {
        const block = $(selector).parent();
        block.block({
            message: `
                <span class="spinner-border text-purple" role="status"></span>
                <span class="overlay-message">
                    ${message}<span class="animated-dots"></span>
                </span>
            `,
            overlayCSS: {
                opacity: 0.7,
                cursor: 'wait'
            },
            css: {
                border: 0,
                padding: '10px 15px',
                color: '#fff',
                width: 'auto',
                'border-radius': '4px',
                backgroundColor: '#333',
                'font-size': '14px'
            }
        });
        return block;
    },
    hide: (block) => {
        block.unblock();
    }
};

// Usage
const overlay = Overlay.show('Processing...', '.content-area');
// Later...
Overlay.hide(overlay);
```

#### 24.2.3 DataTables Configuration
```javascript
// helpers/datatables.js
const DataTableHelper = {
    defaultConfig: {
        order: [[0, 'desc']],
        pageLength: 25,
        responsive: true,
        language: {
            search: '',
            searchPlaceholder: 'Search...',
            processing: `
                <span class="spinner-border spinner-border-sm" role="status">
                    <span class="visually-hidden">Loading...</span>
                </span>
            `
        }
    },

    init: (selector, customConfig = {}) => {
        const config = { ...DataTableHelper.defaultConfig, ...customConfig };
        return $(selector).DataTable(config);
    }
};

// Usage
DataTableHelper.init('.dt-enabled', {
    serverSide: true,
    ajax: '/api/data'
});
```

### 24.3 Common Utilities

#### 24.3.1 Form Handlers
```javascript
// helpers/forms.js
const FormHelper = {
    validate: (selector) => {
        const form = $(selector);
        const isValid = form[0].checkValidity();
        
        form.find(':input').each(function() {
            if (!this.checkValidity()) {
                $(this).addClass('is-invalid');
            } else {
                $(this).removeClass('is-invalid').addClass('is-valid');
            }
        });
        
        return isValid;
    },

    submit: async (selector, options = {}) => {
        const form = $(selector);
        const overlay = Overlay.show('Processing...', form);
        
        try {
            const response = await $.ajax({
                url: form.attr('action'),
                method: form.attr('method'),
                data: form.serialize(),
                ...options
            });
            
            return response;
        } catch (error) {
            console.error('Form submission error:', error);
            throw error;
        } finally {
            Overlay.hide(overlay);
        }
    }
};
```

#### 24.3.2 Date Picker Configuration
```javascript
// helpers/datepicker.js
const DatePickerHelper = {
    defaultConfig: {
        format: "dd-mm-yyyy",
        todayBtn: "linked",
        autoclose: true,
        todayHighlight: true
    },

    init: (selector, customConfig = {}) => {
        const config = { ...DatePickerHelper.defaultConfig, ...customConfig };
        $(selector).datepicker(config);
    },

    initPeriodPicker: (selector) => {
        DatePickerHelper.init(selector, {
            format: "mm-yyyy",
            minViewMode: 1,
            maxViewMode: 2
        });
    }
};
```

### 24.4 Global Event Handlers

```javascript
// helpers/events.js
const GlobalEvents = {
    init: () => {
        // Block UI on specific clicks
        $('.block-on-click').on('click', function(e) {
            Overlay.show('Processing...', '.page');
        });

        // Initialize all tooltips
        $('[data-bs-toggle="tooltip"]').tooltip();

        // Handle session timeout
        let sessionTimeout;
        $(document).on('mousemove keypress', () => {
            clearTimeout(sessionTimeout);
            sessionTimeout = setTimeout(checkSession, 300000); // 5 minutes
        });
    }
};
```

### 24.5 Application Initialization

```javascript
// app.js
$(document).ready(() => {
    // Initialize global handlers
    GlobalEvents.init();
    
    // Initialize DataTables
    DataTableHelper.init('.dt-enabled');
    
    // Initialize Date pickers
    DatePickerHelper.init('.dp-enabled');
    DatePickerHelper.initPeriodPicker('.dp-period');
    
    // Initialize page loader
    PageLoader.init();
});
```

### 24.6 CSS Organization

#### 24.6.1 Custom Components
```css
/* custom/components.css */
.page-loader {
    position: fixed;
    width: 100%;
    height: 100%;
    background: rgba(255, 255, 255, 0.9);
    z-index: 9999;
    display: flex;
    align-items: center;
    justify-content: center;
}

.animated-dots::after {
    content: '...';
    animation: dots 1.5s steps(4, end) infinite;
}

@keyframes dots {
    0%, 20% { content: ''; }
    40% { content: '.'; }
    60% { content: '..'; }
    80% { content: '...'; }
}
```

### 24.7 Asset Loading

```php
// In your blade layout
<!DOCTYPE html>
<html>
<head>
    <!-- Core CSS -->
    @vite(['resources/css/app.css'])
    
    <!-- Third-party Libraries -->
    <link href="{{ asset('assets/libs/DataTables/datatables.min.css') }}" rel="stylesheet">
    
    <!-- Custom CSS -->
    <link href="{{ asset('assets/css/custom/components.css') }}" rel="stylesheet">
</head>
<body>
    <!-- Page Loader -->
    <div class="page-loader">
        <div class="spinner-border text-primary" role="status">
            <span class="visually-hidden">Loading...</span>
        </div>
    </div>

    <!-- Content -->
    <div class="content-area">
        @yield('content')
    </div>

    <!-- Scripts -->
    @vite(['resources/js/app.js'])
    <script src="{{ asset('assets/js/helpers/overlay.js') }}"></script>
    <script src="{{ asset('assets/js/helpers/datatables.js') }}"></script>
</body>
</html>
```

## Enforcement

- Code reviews should verify these standards
- Use automated tools where possible (PHP_CodeSniffer, Laravel Pint)
- CI/CD pipeline should include style checks
