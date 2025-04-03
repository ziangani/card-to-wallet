<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Transaction - {{ config('app.name') }}</title>
    <meta name="description" content="Send money to a mobile wallet">

    <!-- Favicon -->
    <link rel="icon" href="{{ asset('assets/img/logo.png') }}">

    <!-- Tailwind CSS via CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#3366CC',
                        secondary: '#FF9900',
                        success: '#28A745',
                        warning: '#FFC107',
                        error: '#DC3545',
                        light: '#F8F9FA',
                        dark: '#343A40',
                    }
                }
            }
        }
    </script>

    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

    <!-- Google Fonts - Inter -->
    <link href="https://fonts.googleapis.com" rel="preconnect">
    <link href="https://fonts.gstatic.com" rel="preconnect" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
        .sidebar {
            width: 280px;
            transition: all 0.3s ease;
        }
        @media (max-width: 1023px) {
            .sidebar {
                width: 0;
                position: fixed;
                z-index: 40;
                overflow: hidden;
            }
            .sidebar.open {
                width: 280px;
            }
        }
        .main-content {
            transition: all 0.3s ease;
        }
        @media (min-width: 1024px) {
            .main-content {
                margin-left: 280px;
            }
        }
        .step-item {
            position: relative;
        }
        .step-item:not(:last-child)::after {
            content: '';
            position: absolute;
            top: 1.5rem;
            left: 2.25rem;
            height: calc(100% - 1.5rem);
            width: 1px;
            background-color: #E5E7EB;
        }
        .step-item.active .step-circle {
            background-color: #3366CC;
            color: white;
        }
        .step-item.completed .step-circle {
            background-color: #28A745;
            color: white;
        }
    </style>
</head>
<body class="bg-light">
    <!-- Mobile Header -->
    <header class="lg:hidden bg-white shadow-sm sticky top-0 z-30">
        <div class="container mx-auto px-4 py-4 flex justify-between items-center">
            <button id="sidebar-toggle" class="text-dark hover:text-primary p-2">
                <i class="fas fa-bars text-xl"></i>
            </button>
            <a href="{{ url('/dashboard') }}" class="flex items-center">
                <img src="{{ asset('assets/img/logo.png') }}" alt="Logo" class="h-10">
            </a>
            <div class="relative">
                <button id="mobile-user-menu-button" class="flex items-center focus:outline-none">
                    <div class="w-10 h-10 rounded-full bg-primary text-white flex items-center justify-center">
                        {{ strtoupper(substr(auth()->user()->first_name, 0, 1) . substr(auth()->user()->last_name, 0, 1)) }}
                    </div>
                </button>
                <!-- Mobile User Dropdown Menu -->
                <div id="mobile-user-menu" class="hidden absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg py-2 z-50">
                    <a href="{{ route('profile.index') }}" class="block px-4 py-2 text-gray-800 hover:bg-gray-100">
                        <i class="fas fa-user mr-2"></i> Profile
                    </a>
                    <a href="{{ route('profile.security') }}" class="block px-4 py-2 text-gray-800 hover:bg-gray-100">
                        <i class="fas fa-shield-alt mr-2"></i> Security
                    </a>
                    <div class="border-t border-gray-200 my-1"></div>
                    <form method="POST" action="{{ route('logout') }}" class="block">
                        @csrf
                        <button type="submit" class="w-full text-left px-4 py-2 text-gray-800 hover:bg-gray-100">
                            <i class="fas fa-sign-out-alt mr-2"></i> Logout
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </header>

    <!-- Sidebar -->
    <aside id="sidebar" class="sidebar bg-white shadow-md h-screen fixed top-0 left-0 overflow-y-auto z-40">
        <div class="p-4 border-b">
            <a href="{{ url('/dashboard') }}" class="flex items-center justify-center">
                <img src="{{ asset('assets/img/logo.png') }}" alt="Logo" class="h-12">
            </a>
        </div>
        
        <div class="p-4">
            <div class="flex items-center space-x-3 mb-6">
                <div class="w-12 h-12 rounded-full bg-primary text-white flex items-center justify-center text-lg font-semibold">
                    {{ strtoupper(substr(auth()->user()->first_name, 0, 1) . substr(auth()->user()->last_name, 0, 1)) }}
                </div>
                <div>
                    <h3 class="font-semibold text-dark">{{ auth()->user()->first_name }} {{ auth()->user()->last_name }}</h3>
                    <p class="text-sm text-gray-500">{{ auth()->user()->email }}</p>
                </div>
            </div>
            
            <nav class="mt-6">
                <ul class="space-y-1">
                    <li>
                        <a href="{{ route('dashboard') }}" class="flex items-center px-4 py-3 text-dark hover:bg-primary hover:text-white rounded-lg transition-colors {{ request()->routeIs('dashboard') ? 'bg-primary text-white' : '' }}">
                            <i class="fas fa-home w-6"></i>
                            <span>Dashboard</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('transactions.initiate') }}" class="flex items-center px-4 py-3 text-dark hover:bg-primary hover:text-white rounded-lg transition-colors {{ request()->routeIs('transactions.initiate') ? 'bg-primary text-white' : '' }}">
                            <i class="fas fa-exchange-alt w-6"></i>
                            <span>New Transaction</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('transactions.history') }}" class="flex items-center px-4 py-3 text-dark hover:bg-primary hover:text-white rounded-lg transition-colors {{ request()->routeIs('transactions.history') ? 'bg-primary text-white' : '' }}">
                            <i class="fas fa-history w-6"></i>
                            <span>Transaction History</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('beneficiaries.index') }}" class="flex items-center px-4 py-3 text-dark hover:bg-primary hover:text-white rounded-lg transition-colors {{ request()->routeIs('beneficiaries.index') ? 'bg-primary text-white' : '' }}">
                            <i class="fas fa-users w-6"></i>
                            <span>Beneficiaries</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('profile.index') }}" class="flex items-center px-4 py-3 text-dark hover:bg-primary hover:text-white rounded-lg transition-colors {{ request()->routeIs('profile.index') ? 'bg-primary text-white' : '' }}">
                            <i class="fas fa-user w-6"></i>
                            <span>My Profile</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('profile.kyc') }}" class="flex items-center px-4 py-3 text-dark hover:bg-primary hover:text-white rounded-lg transition-colors {{ request()->routeIs('profile.kyc') ? 'bg-primary text-white' : '' }}">
                            <i class="fas fa-id-card w-6"></i>
                            <span>KYC Verification</span>
                            @if(auth()->user()->verification_level === 'basic')
                                <span class="ml-auto inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-warning text-dark">
                                    Basic
                                </span>
                            @endif
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('support') }}" class="flex items-center px-4 py-3 text-dark hover:bg-primary hover:text-white rounded-lg transition-colors {{ request()->routeIs('support') ? 'bg-primary text-white' : '' }}">
                            <i class="fas fa-question-circle w-6"></i>
                            <span>Help & Support</span>
                        </a>
                    </li>
                </ul>
            </nav>
        </div>
        
        <div class="p-4 mt-auto border-t">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="flex items-center px-4 py-3 text-dark hover:bg-gray-100 rounded-lg transition-colors w-full">
                    <i class="fas fa-sign-out-alt w-6"></i>
                    <span>Logout</span>
                </button>
            </form>
        </div>
    </aside>

    <!-- Main Content -->
    <main class="main-content min-h-screen bg-light pb-12">
        <!-- Desktop Header -->
        <header class="hidden lg:block bg-white shadow-sm sticky top-0 z-20">
            <div class="container mx-auto px-6 py-4 flex justify-between items-center">
                <h1 class="text-2xl font-bold text-dark">New Transaction</h1>
                
                <div class="flex items-center space-x-4">
                    <div class="relative">
                        <button id="notifications-button" class="text-gray-600 hover:text-primary p-2 relative">
                            <i class="fas fa-bell text-xl"></i>
                            <span class="absolute top-0 right-0 inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-white transform translate-x-1/2 -translate-y-1/2 bg-error rounded-full">2</span>
                        </button>
                        <!-- Notifications Dropdown -->
                        <div id="notifications-dropdown" class="hidden absolute right-0 mt-2 w-80 bg-white rounded-lg shadow-lg py-2 z-50">
                            <div class="px-4 py-2 border-b border-gray-200">
                                <h3 class="font-semibold text-dark">Notifications</h3>
                            </div>
                            <div class="max-h-64 overflow-y-auto">
                                <a href="#" class="block px-4 py-3 hover:bg-gray-50 border-b border-gray-100">
                                    <div class="flex items-start">
                                        <div class="flex-shrink-0 w-8 h-8 rounded-full bg-success bg-opacity-20 flex items-center justify-center text-success">
                                            <i class="fas fa-check-circle"></i>
                                        </div>
                                        <div class="ml-3">
                                            <p class="text-sm font-medium text-dark">Transaction Successful</p>
                                            <p class="text-xs text-gray-500">Your transfer of K500 to MTN wallet was successful.</p>
                                            <p class="text-xs text-gray-400 mt-1">2 hours ago</p>
                                        </div>
                                    </div>
                                </a>
                                <a href="#" class="block px-4 py-3 hover:bg-gray-50">
                                    <div class="flex items-start">
                                        <div class="flex-shrink-0 w-8 h-8 rounded-full bg-warning bg-opacity-20 flex items-center justify-center text-warning">
                                            <i class="fas fa-exclamation-circle"></i>
                                        </div>
                                        <div class="ml-3">
                                            <p class="text-sm font-medium text-dark">Verification Reminder</p>
                                            <p class="text-xs text-gray-500">Complete your KYC verification to increase your transaction limits.</p>
                                            <p class="text-xs text-gray-400 mt-1">1 day ago</p>
                                        </div>
                                    </div>
                                </a>
                            </div>
                            <div class="px-4 py-2 border-t border-gray-200 text-center">
                                <a href="#" class="text-sm text-primary hover:underline">View All Notifications</a>
                            </div>
                        </div>
                    </div>
                    
                    <div class="relative">
                        <button id="user-menu-button" class="flex items-center space-x-3 focus:outline-none">
                            <div class="w-10 h-10 rounded-full bg-primary text-white flex items-center justify-center">
                                {{ strtoupper(substr(auth()->user()->first_name, 0, 1) . substr(auth()->user()->last_name, 0, 1)) }}
                            </div>
                            <div class="hidden md:block text-left">
                                <h3 class="font-medium text-dark">{{ auth()->user()->first_name }} {{ auth()->user()->last_name }}</h3>
                                <p class="text-xs text-gray-500">{{ auth()->user()->verification_level === 'verified' ? 'Verified Account' : 'Basic Account' }}</p>
                            </div>
                            <i class="fas fa-chevron-down text-gray-400"></i>
                        </button>
                        <!-- User Dropdown Menu -->
                        <div id="user-menu" class="hidden absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg py-2 z-50">
                            <a href="{{ route('profile.index') }}" class="block px-4 py-2 text-gray-800 hover:bg-gray-100">
                                <i class="fas fa-user mr-2"></i> Profile
                            </a>
                            <a href="{{ route('profile.security') }}" class="block px-4 py-2 text-gray-800 hover:bg-gray-100">
                                <i class="fas fa-shield-alt mr-2"></i> Security
                            </a>
                            <div class="border-t border-gray-200 my-1"></div>
                            <form method="POST" action="{{ route('logout') }}" class="block">
                                @csrf
                                <button type="submit" class="w-full text-left px-4 py-2 text-gray-800 hover:bg-gray-100">
                                    <i class="fas fa-sign-out-alt mr-2"></i> Logout
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <!-- Page Content -->
        <div class="container mx-auto px-4 py-6">
            @if(session('success'))
                <div class="mb-6 p-4 bg-success bg-opacity-10 text-success rounded-lg flex items-start">
                    <i class="fas fa-check-circle mt-1 mr-3"></i>
                    <div>
                        <h3 class="font-semibold">Success</h3>
                        <p>{{ session('success') }}</p>
                    </div>
                </div>
            @endif

            @if(session('error'))
                <div class="mb-6 p-4 bg-error bg-opacity-10 text-error rounded-lg flex items-start">
                    <i class="fas fa-exclamation-circle mt-1 mr-3"></i>
                    <div>
                        <h3 class="font-semibold">Error</h3>
                        <p>{{ session('error') }}</p>
                    </div>
                </div>
            @endif

            <!-- Transaction Steps -->
            <div class="bg-white rounded-xl shadow-sm overflow-hidden mb-6">
                <div class="p-6">
                    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6">
                        <h2 class="text-xl font-bold text-dark mb-2 md:mb-0">Send Money to Mobile Wallet</h2>
                        
                        <div class="flex space-x-2">
                            <a href="{{ route('dashboard') }}" class="inline-flex items-center px-3 py-1 bg-gray-100 hover:bg-gray-200 rounded text-sm text-gray-700 transition">
                                <i class="fas fa-arrow-left mr-1"></i> Back to Dashboard
                            </a>
                            <a href="{{ route('transactions.history') }}" class="inline-flex items-center px-3 py-1 bg-gray-100 hover:bg-gray-200 rounded text-sm text-gray-700 transition">
                                <i class="fas fa-history mr-1"></i> Transaction History
                            </a>
                        </div>
                    </div>
                    
                    <div class="flex flex-col md:flex-row mb-6">
                        <div class="flex-1 flex flex-col md:flex-row items-start md:items-center justify-between p-4 border-b md:border-b-0 md:border-r border-gray-200">
                            <div class="flex items-center mb-2 md:mb-0">
                                <div class="w-10 h-10 rounded-full bg-primary text-white flex items-center justify-center mr-3">
                                    <i class="fas fa-wallet"></i>
                                </div>
                                <div>
                                    <h3 class="font-medium text-dark">Available Balance</h3>
                                    <p class="text-gray-500">Your account balance</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="text-2xl font-bold text-dark">K{{ number_format(auth()->user()->balance ?? 0, 2) }}</p>
                            </div>
                        </div>
                        
                        <div class="flex-1 flex flex-col md:flex-row items-start md:items-center justify-between p-4">
                            <div class="flex items-center mb-2 md:mb-0">
                                <div class="w-10 h-10 rounded-full bg-light text-primary flex items-center justify-center mr-3">
                                    <i class="fas fa-exchange-alt"></i>
                                </div>
                                <div>
                                    <h3 class="font-medium text-dark">Transaction Limit</h3>
                                    <p class="text-gray-500">Your current limit</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="text-2xl font-bold text-dark">
                                    K{{ number_format(auth()->user()->verification_level === 'verified' ? 5000 : 1000, 2) }}
                                </p>
                                <p class="text-xs text-gray-500">
                                    {{ auth()->user()->verification_level === 'verified' ? 'Verified Account' : 'Basic Account' }}
                                </p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Steps Progress -->
                    <div class="mb-6">
                        <div class="flex flex-col md:flex-row justify-between">
                            <div class="flex-1 flex items-center mb-4 md:mb-0">
                                <div class="w-8 h-8 rounded-full bg-primary text-white flex items-center justify-center">
                                    1
                                </div>
                                <div class="ml-3">
                                    <h3 class="font-medium text-dark">Recipient Details</h3>
                                    <p class="text-sm text-gray-500">Enter mobile wallet details</p>
                                </div>
                            </div>
                            <div class="flex-1 flex items-center mb-4 md:mb-0">
                                <div class="w-8 h-8 rounded-full bg-gray-200 text-gray-600 flex items-center justify-center">
                                    2
                                </div>
                                <div class="ml-3">
                                    <h3 class="font-medium text-gray-600">Confirm Details</h3>
                                    <p class="text-sm text-gray-500">Verify transaction details</p>
                                </div>
                            </div>
                            <div class="flex-1 flex items-center">
                                <div class="w-8 h-8 rounded-full bg-gray-200 text-gray-600 flex items-center justify-center">
                                    3
                                </div>
                                <div class="ml-3">
                                    <h3 class="font-medium text-gray-600">Make Payment</h3>
                                    <p class="text-sm text-gray-500">Complete secure payment</p>
                                </div>
                            </div>
                        </div>
                        <div class="relative mt-4">
                            <div class="overflow-hidden h-2 text-xs flex rounded bg-gray-200">
                                <div style="width: 33.3%" class="shadow-none flex flex-col text-center whitespace-nowrap text-white justify-center bg-primary"></div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Transaction Form -->
                    <form action="{{ route('transactions.confirm') }}" method="POST" id="transaction-form">
                        @csrf
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Mobile Provider -->
                            <div>
                                <label for="wallet_provider_id" class="block text-sm font-medium text-gray-700 mb-1">Mobile Provider</label>
                                <select id="wallet_provider_id" name="wallet_provider_id" class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-primary focus:border-primary" required>
                                    <option value="">Select Provider</option>
                                    @foreach($walletProviders ?? [] as $provider)
                                        <option value="{{ $provider->id }}" data-code="{{ $provider->api_code }}" {{ old('wallet_provider_id', $selectedBeneficiary->wallet_provider_id ?? '') == $provider->id ? 'selected' : '' }}>
                                            {{ $provider->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('wallet_provider_id')
                                    <p class="text-error text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <!-- Mobile Number -->
                            <div>
                                <label for="wallet_number" class="block text-sm font-medium text-gray-700 mb-1">Mobile Number</label>
                                <div class="flex">
                                    <span class="inline-flex items-center px-3 text-gray-500 bg-gray-100 border border-r-0 border-gray-300 rounded-l-lg">
                                        +260
                                    </span>
                                    <input type="text" id="wallet_number" name="wallet_number" value="{{ old('wallet_number', $selectedBeneficiary->wallet_number ?? '') }}" class="w-full px-4 py-3 border border-gray-300 rounded-r-lg focus:ring-2 focus:ring-primary focus:border-primary" placeholder="97XXXXXXX" required>
                                </div>
                                <p class="text-xs text-gray-500 mt-1">Enter 9-digit number without leading zero</p>
                                @error('wallet_number')
                                    <p class="text-error text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <!-- Amount -->
                            <div>
                                <label for="amount" class="block text-sm font-medium text-gray-700 mb-1">Amount (ZMW)</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <span class="text-gray-500">K</span>
                                    </div>
                                    <input type="number" id="amount" name="amount" value="{{ old('amount') }}" min="10" max="{{ auth()->user()->verification_level === 'verified' ? 5000 : 1000 }}" step="0.01" class="w-full pl-8 pr-4 py-3 border rounded-lg focus:ring-2 focus:ring-primary focus:border-primary" placeholder="0.00" required>
                                </div>
                                <p class="text-xs text-gray-500 mt-1">
                                    Min: K10.00 | Max: K{{ number_format(auth()->user()->verification_level === 'verified' ? 5000 : 1000, 2) }}
                                </p>
                                @error('amount')
                                    <p class="text-error text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <!-- Recipient Name -->
                            <div>
                                <label for="recipient_name" class="block text-sm font-medium text-gray-700 mb-1">Recipient Name</label>
                                <input type="text" id="recipient_name" name="recipient_name" value="{{ old('recipient_name', $selectedBeneficiary->recipient_name ?? '') }}" class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-primary focus:border-primary" placeholder="Enter recipient name">
                                @error('recipient_name')
                                    <p class="text-error text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <!-- Purpose -->
                            <div>
                                <label for="purpose" class="block text-sm font-medium text-gray-700 mb-1">Purpose (Optional)</label>
                                <select id="purpose" name="purpose" class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-primary focus:border-primary">
                                    <option value="">Select Purpose</option>
                                    <option value="Family Support" {{ old('purpose') == 'Family Support' ? 'selected' : '' }}>Family Support</option>
                                    <option value="Business" {{ old('purpose') == 'Business' ? 'selected' : '' }}>Business</option>
                                    <option value="Education" {{ old('purpose') == 'Education' ? 'selected' : '' }}>Education</option>
                                    <option value="Medical" {{ old('purpose') == 'Medical' ? 'selected' : '' }}>Medical</option>
                                    <option value="Utilities" {{ old('purpose') == 'Utilities' ? 'selected' : '' }}>Utilities</option>
                                    <option value="Rent" {{ old('purpose') == 'Rent' ? 'selected' : '' }}>Rent</option>
                                    <option value="Other" {{ old('purpose') == 'Other' ? 'selected' : '' }}>Other</option>
                                </select>
                                @error('purpose')
                                    <p class="text-error text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <!-- Notes -->
                            <div>
                                <label for="notes" class="block text-sm font-medium text-gray-700 mb-1">Notes (Optional)</label>
                                <input type="text" id="notes" name="notes" value="{{ old('notes') }}" class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-primary focus:border-primary" placeholder="Add any notes for this transaction">
                                @error('notes')
                                    <p class="text-error text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                        
                        <!-- Fee Calculation -->
                        <div class="mt-6 p-4 bg-light rounded-lg">
                            <div class="flex justify-between items-center mb-2">
                                <span class="text-gray-700">Amount:</span>
                                <span class="font-medium" id="display-amount">K0.00</span>
                            </div>
                            <div class="flex justify-between items-center mb-2">
                                <span class="text-gray-700">Fee (4%):</span>
                                <span class="font-medium" id="display-fee">K0.00</span>
                            </div>
                            <div class="border-t border-gray-200 my-2 pt-2 flex justify-between items-center">
                                <span class="text-gray-700 font-medium">Total:</span>
                                <span class="font-bold text-dark" id="display-total">K0.00</span>
                            </div>
                        </div>
                        
                        <!-- Save Beneficiary -->
                        <div class="mt-4">
                            <label class="inline-flex items-center">
                                <input type="checkbox" name="save_beneficiary" class="h-4 w-4 text-primary focus:ring-primary border-gray-300 rounded">
                                <span class="ml-2 text-gray-700">Save this recipient for future transactions</span>
                            </label>
                        </div>
                        
                        <!-- Submit Button -->
                        <div class="mt-6">
                            <button type="submit" class="w-full bg-primary text-white py-3 px-4 rounded-lg hover:bg-opacity-90 transition duration-300 font-medium">
                                <i class="fas fa-arrow-right mr-2"></i> Continue to Confirm
                            </button>
                        </div>
                    </form>
                    
                    <!-- Saved Beneficiaries -->
                    <div class="mt-8">
                        <h3 class="text-lg font-medium text-dark mb-4">Select from Saved Beneficiaries</h3>
                        
                        @if(count($savedBeneficiaries ?? []) > 0)
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                @foreach($savedBeneficiaries as $beneficiary)
                                    <div class="flex items-center p-3 bg-light rounded-lg hover:bg-gray-100 transition cursor-pointer beneficiary-card" 
                                         data-provider="{{ $beneficiary->wallet_provider_id }}"
                                         data-number="{{ $beneficiary->wallet_number }}"
                                         data-name="{{ $beneficiary->recipient_name }}">
                                        <div class="flex-shrink-0 h-10 w-10">
                                            @if($beneficiary->wallet_provider)
                                                @if($beneficiary->wallet_provider->api_code === 'airtel')
                                                    <img class="h-10 w-10 rounded-full" src="{{ asset('assets/img/airtel.png') }}" alt="Airtel">
                                                @elseif($beneficiary->wallet_provider->api_code === 'mtn')
                                                    <img class="h-10 w-10 rounded-full" src="{{ asset('assets/img/mtn.jpg') }}" alt="MTN">
                                                @elseif($beneficiary->wallet_provider->api_code === 'zamtel')
                                                    <img class="h-10 w-10 rounded-full" src="{{ asset('assets/img/zamtel.jpg') }}" alt="Zamtel">
                                                @else
                                                    <div class="h-10 w-10 rounded-full bg-primary text-white flex items-center justify-center">
                                                        {{ strtoupper(substr($beneficiary->recipient_name, 0, 1)) }}
                                                    </div>
                                                @endif
                                            @else
                                                <div class="h-10 w-10 rounded-full bg-primary text-white flex items-center justify-center">
                                                    {{ strtoupper(substr($beneficiary->recipient_name, 0, 1)) }}
                                                </div>
                                            @endif
                                        </div>
                                        <div class="ml-3">
                                            <div class="text-sm font-medium text-dark">
                                                {{ $beneficiary->recipient_name }}
                                                @if($beneficiary->is_favorite)
                                                    <i class="fas fa-star text-secondary ml-1 text-xs"></i>
                                                @endif
                                            </div>
                                            <div class="text-xs text-gray-500">
                                                +260{{ $beneficiary->wallet_number }}
                                            </div>
                                            <div class="text-xs text-gray-500">
                                                {{ $beneficiary->wallet_provider->name ?? 'Unknown Provider' }}
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            
                            @if(count($savedBeneficiaries) > 6)
                                <div class="mt-4 text-center">
                                    <a href="{{ route('beneficiaries.index') }}" class="text-primary hover:underline text-sm">
                                        View all beneficiaries ({{ count($allBeneficiaries ?? []) }})
                                    </a>
                                </div>
                            @endif
                        @else
                            <div class="text-center py-8 bg-light rounded-lg">
                                <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-gray-100 text-gray-400 mb-4">
                                    <i class="fas fa-users text-2xl"></i>
                                </div>
                                <h3 class="text-lg font-medium text-gray-900 mb-1">No saved beneficiaries</h3>
                                <p class="text-gray-500 mb-4">Save recipients for quick access</p>
                                <a href="{{ route('beneficiaries.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary hover:bg-opacity-90 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary">
                                    <i class="fas fa-plus mr-2"></i> Add Beneficiary
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Mobile sidebar toggle
            const sidebarToggle = document.getElementById('sidebar-toggle');
            const sidebar = document.getElementById('sidebar');
            
            if (sidebarToggle) {
                sidebarToggle.addEventListener('click', function() {
                    sidebar.classList.toggle('open');
                });
            }
            
            // Close sidebar when clicking outside
            document.addEventListener('click', function(event) {
                if (sidebar.classList.contains('open') && 
                    !sidebar.contains(event.target) && 
                    event.target !== sidebarToggle) {
                    sidebar.classList.remove('open');
                }
            });
            
            // User menu dropdown
            const userMenuButton = document.getElementById('user-menu-button');
            const userMenu = document.getElementById('user-menu');
            
            if (userMenuButton) {
                userMenuButton.addEventListener('click', function() {
                    userMenu.classList.toggle('hidden');
                });
            }
            
            // Mobile user menu dropdown
            const mobileUserMenuButton = document.getElementById('mobile-user-menu-button');
            const mobileUserMenu = document.getElementById('mobile-user-menu');
            
            if (mobileUserMenuButton) {
                mobileUserMenuButton.addEventListener('click', function() {
                    mobileUserMenu.classList.toggle('hidden');
                });
            }
            
            // Notifications dropdown
            const notificationsButton = document.getElementById('notifications-button');
            const notificationsDropdown = document.getElementById('notifications-dropdown');
            
            if (notificationsButton) {
                notificationsButton.addEventListener('click', function() {
                    notificationsDropdown.classList.toggle('hidden');
                });
            }
            
            // Close dropdowns when clicking outside
            document.addEventListener('click', function(event) {
                if (userMenu && !userMenu.classList.contains('hidden') && 
                    !userMenu.contains(event.target) && 
                    !userMenuButton.contains(event.target)) {
                    userMenu.classList.add('hidden');
                }
                
                if (mobileUserMenu && !mobileUserMenu.classList.contains('hidden') && 
                    !mobileUserMenu.contains(event.target) && 
                    !mobileUserMenuButton.contains(event.target)) {
                    mobileUserMenu.classList.add('hidden');
                }
                
                if (notificationsDropdown && !notificationsDropdown.classList.contains('hidden') && 
                    !notificationsDropdown.contains(event.target) && 
                    !notificationsButton.contains(event.target)) {
                    notificationsDropdown.classList.add('hidden');
                }
            });
            
            // Amount input and fee calculation
            const amountInput = document.getElementById('amount');
            const displayAmount = document.getElementById('display-amount');
            const displayFee = document.getElementById('display-fee');
            const displayTotal = document.getElementById('display-total');
            
            // Format currency
            function formatCurrency(amount) {
                return 'K' + parseFloat(amount).toFixed(2);
            }
            
            // Calculate fee and total
            function calculateFee() {
                const amount = parseFloat(amountInput.value) || 0;
                const fee = amount * 0.04; // 4% fee
                const total = amount + fee;
                
                displayAmount.textContent = formatCurrency(amount);
                displayFee.textContent = formatCurrency(fee);
                displayTotal.textContent = formatCurrency(total);
            }
            
            if (amountInput) {
                amountInput.addEventListener('input', calculateFee);
                calculateFee(); // Initialize
            }
            
            // Phone number validation
            const walletNumberInput = document.getElementById('wallet_number');
            
            if (walletNumberInput) {
                walletNumberInput.addEventListener('input', function() {
                    // Remove non-numeric characters
                    this.value = this.value.replace(/\D/g, '');
                    
                    // Limit to 9 digits
                    if (this.value.length > 9) {
                        this.value = this.value.slice(0, 9);
                    }
                });
            }
            
            // Beneficiary selection
            const beneficiaryCards = document.querySelectorAll('.beneficiary-card');
            const walletProviderSelect = document.getElementById('wallet_provider_id');
            const recipientNameInput = document.getElementById('recipient_name');
            
            beneficiaryCards.forEach(card => {
                card.addEventListener('click', function() {
                    const providerId = this.dataset.provider;
                    const number = this.dataset.number;
                    const name = this.dataset.name;
                    
                    walletProviderSelect.value = providerId;
                    walletNumberInput.value = number;
                    recipientNameInput.value = name;
                    
                    // Add active class to selected card
                    beneficiaryCards.forEach(c => {
                        c.classList.remove('bg-primary', 'bg-opacity-10');
                        c.classList.add('bg-light');
                    });
                    
                    this.classList.remove('bg-light');
                    this.classList.add('bg-primary', 'bg-opacity-10');
                });
            });
        });
    </script>
</body>
</html>
