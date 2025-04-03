<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment - {{ config('app.name') }}</title>
    <meta name="description" content="Complete your mobile wallet transaction payment">

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
        .payment-card {
            background: linear-gradient(135deg, #e6e6e6 0%, #ffffff 100%);
            border-radius: 16px;
            box-shadow: 0 4px 30px rgba(0, 0, 0, 0.1);
            backdrop-filter: blur(5px);
            -webkit-backdrop-filter: blur(5px);
            border: 1px solid rgba(255, 255, 255, 0.3);
        }
        .payment-logo {
            filter: grayscale(100%);
            opacity: 0.7;
            transition: all 0.3s ease;
        }
        .payment-logo:hover {
            filter: grayscale(0%);
            opacity: 1;
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
                        <a href="{{ route('transactions.initiate') }}" class="flex items-center px-4 py-3 text-dark hover:bg-primary hover:text-white rounded-lg transition-colors {{ request()->routeIs('transactions.initiate') || request()->routeIs('transactions.confirm') || request()->routeIs('transactions.payment') ? 'bg-primary text-white' : '' }}">
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
                <h1 class="text-2xl font-bold text-dark">Complete Payment</h1>
                
                <div class="flex items-center space-x-4">
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
                        <h2 class="text-xl font-bold text-dark mb-2 md:mb-0">Complete Your Payment</h2>
                        
                        <div class="flex space-x-2">
                            <a href="{{ route('transactions.confirm', $transaction->id) }}" class="inline-flex items-center px-3 py-1 bg-gray-100 hover:bg-gray-200 rounded text-sm text-gray-700 transition">
                                <i class="fas fa-arrow-left mr-1"></i> Back to Confirmation
                            </a>
                        </div>
                    </div>
                    
                    <!-- Steps Progress -->
                    <div class="mb-6">
                        <div class="flex flex-col md:flex-row justify-between">
                            <div class="flex-1 flex items-center mb-4 md:mb-0">
                                <div class="w-8 h-8 rounded-full bg-success text-white flex items-center justify-center">
                                    <i class="fas fa-check"></i>
                                </div>
                                <div class="ml-3">
                                    <h3 class="font-medium text-dark">Recipient Details</h3>
                                    <p class="text-sm text-gray-500">Enter mobile wallet details</p>
                                </div>
                            </div>
                            <div class="flex-1 flex items-center mb-4 md:mb-0">
                                <div class="w-8 h-8 rounded-full bg-success text-white flex items-center justify-center">
                                    <i class="fas fa-check"></i>
                                </div>
                                <div class="ml-3">
                                    <h3 class="font-medium text-dark">Confirm Details</h3>
                                    <p class="text-sm text-gray-500">Verify transaction details</p>
                                </div>
                            </div>
                            <div class="flex-1 flex items-center">
                                <div class="w-8 h-8 rounded-full bg-primary text-white flex items-center justify-center">
                                    3
                                </div>
                                <div class="ml-3">
                                    <h3 class="font-medium text-dark">Make Payment</h3>
                                    <p class="text-sm text-gray-500">Complete secure payment</p>
                                </div>
                            </div>
                        </div>
                        <div class="relative mt-4">
                            <div class="overflow-hidden h-2 text-xs flex rounded bg-gray-200">
                                <div style="width: 100%" class="shadow-none flex flex-col text-center whitespace-nowrap text-white justify-center bg-primary"></div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Transaction Summary -->
                    <div class="bg-light rounded-lg p-6 mb-6">
                        <h3 class="text-lg font-semibold text-dark mb-4">Transaction Summary</h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <h4 class="text-sm font-medium text-gray-500 mb-1">Recipient</h4>
                                <div class="flex items-center">
                                    @if($transaction->wallet_provider)
                                        @if($transaction->wallet_provider->api_code === 'airtel')
                                            <img class="h-6 w-6 rounded-full mr-2" src="{{ asset('assets/img/airtel.png') }}" alt="Airtel">
                                        @elseif($transaction->wallet_provider->api_code === 'mtn')
                                            <img class="h-6 w-6 rounded-full mr-2" src="{{ asset('assets/img/mtn.jpg') }}" alt="MTN">
                                        @elseif($transaction->wallet_provider->api_code === 'zamtel')
                                            <img class="h-6 w-6 rounded-full mr-2" src="{{ asset('assets/img/zamtel.jpg') }}" alt="Zamtel">
                                        @else
                                            <div class="h-6 w-6 rounded-full bg-primary text-white flex items-center justify-center mr-2 text-xs">
                                                <i class="fas fa-wallet"></i>
                                            </div>
                                        @endif
                                    @else
                                        <div class="h-6 w-6 rounded-full bg-primary text-white flex items-center justify-center mr-2 text-xs">
                                            <i class="fas fa-wallet"></i>
                                        </div>
                                    @endif
                                    <p class="text-dark">{{ $transaction->recipient_name ?: 'Unknown' }} (+260{{ $transaction->wallet_number }})</p>
                                </div>
                            </div>
                            
                            <div>
                                <h4 class="text-sm font-medium text-gray-500 mb-1">Transaction Reference</h4>
                                <p class="text-dark">{{ $transaction->uuid }}</p>
                            </div>
                            
                            <div>
                                <h4 class="text-sm font-medium text-gray-500 mb-1">Amount</h4>
                                <p class="text-dark">K{{ number_format($transaction->amount, 2) }}</p>
                            </div>
                            
                            <div>
                                <h4 class="text-sm font-medium text-gray-500 mb-1">Fee</h4>
                                <p class="text-dark">K{{ number_format($transaction->fee_amount, 2) }}</p>
                            </div>
                            
                            <div class="md:col-span-2">
                                <h4 class="text-sm font-medium text-gray-500 mb-1">Total Amount</h4>
                                <p class="text-xl font-bold text-primary">K{{ number_format($transaction->total_amount, 2) }}</p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Payment Options -->
                    <div class="mb-6">
                        <h3 class="text-lg font-semibold text-dark mb-4">Select Payment Method</h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- MPGS Hosted Checkout -->
                            <div class="payment-card p-6 cursor-pointer hover:shadow-lg transition-shadow" id="mpgs-option">
                                <div class="flex items-center justify-between mb-4">
                                    <h4 class="font-medium text-dark">Card Payment</h4>
                                    <div class="flex space-x-2">
                                        <img src="{{ asset('assets/img/visa.png') }}" alt="Visa" class="h-6 payment-logo" onerror="this.src='https://via.placeholder.com/60x40?text=Visa'">
                                        <img src="{{ asset('assets/img/mastercard.png') }}" alt="Mastercard" class="h-6 payment-logo" onerror="this.src='https://via.placeholder.com/60x40?text=Mastercard'">
                                    </div>
                                </div>
                                <p class="text-gray-600 text-sm mb-4">Pay securely with your credit or debit card through our secure payment gateway.</p>
                                <div class="flex items-center">
                                    <div class="w-5 h-5 rounded-full border-2 border-primary flex items-center justify-center mr-2">
                                        <div class="w-3 h-3 rounded-full bg-primary"></div>
                                    </div>
                                    <span class="text-primary font-medium">Selected</span>
                                </div>
                            </div>
                            
                            <!-- Other Payment Method (Disabled) -->
                            <div class="payment-card p-6 bg-gray-100 opacity-60 cursor-not-allowed">
                                <div class="flex items-center justify-between mb-4">
                                    <h4 class="font-medium text-gray-500">Mobile Money</h4>
                                    <div class="flex space-x-2">
                                        <img src="{{ asset('assets/img/airtel.png') }}" alt="Airtel" class="h-6 payment-logo" onerror="this.src='https://via.placeholder.com/40x40?text=Airtel'">
                                        <img src="{{ asset('assets/img/mtn.jpg') }}" alt="MTN" class="h-6 payment-logo" onerror="this.src='https://via.placeholder.com/40x40?text=MTN'">
                                    </div>
                                </div>
                                <p class="text-gray-500 text-sm mb-4">Pay using your mobile money account. (Coming soon)</p>
                                <div class="flex items-center">
                                    <div class="w-5 h-5 rounded-full border-2 border-gray-400 mr-2"></div>
                                    <span class="text-gray-500">Not available</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Security Information -->
                    <div class="bg-gray-50 rounded-lg p-4 mb-6">
                        <div class="flex items-start">
                            <div class="flex-shrink-0 mt-0.5">
                                <i class="fas fa-shield-alt text-primary"></i>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-dark">Secure Payment</h3>
                                <p class="mt-1 text-sm text-gray-600">Your payment is processed securely with industry-standard encryption. We do not store your card details.</p>
                                <div class="mt-2 flex items-center space-x-3">
                                    <img src="{{ asset('assets/img/pci-dss.png') }}" alt="PCI DSS" class="h-8" onerror="this.src='https://via.placeholder.com/80x30?text=PCI+DSS'">
                                    <img src="{{ asset('assets/img/ssl.png') }}" alt="SSL" class="h-8" onerror="this.src='https://via.placeholder.com/80x30?text=SSL'">
                                    <img src="{{ asset('assets/img/3ds.png') }}" alt="3D Secure" class="h-8" onerror="this.src='https://via.placeholder.com/80x30?text=3D+Secure'">
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Action Buttons -->
                    <div class="flex flex-col md:flex-row space-y-3 md:space-y-0 md:space-x-3">
                        <a href="{{ route('transactions.confirm', $transaction->id) }}" class="flex-1 inline-flex justify-center items-center px-4 py-3 border border-gray-300 shadow-sm text-base font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary">
                            <i class="fas fa-arrow-left mr-2"></i> Back
                        </a>
                        
                        <form action="{{ route('transactions.mpgs.checkout') }}" method="POST" class="flex-1">
                            @csrf
                            <input type="hidden" name="transaction_id" value="{{ $transaction->id }}">
                            <button type="submit" class="w-full inline-flex justify-center items-center px-4 py-3 border border-transparent shadow-sm text-base font-medium rounded-md text-white bg-primary hover:bg-opacity-90 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary">
                                <i class="fas fa-lock mr-2"></i> Proceed to Secure Checkout
                            </button>
                        </form>
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
            });
        });
    </script>
</body>
</html>
