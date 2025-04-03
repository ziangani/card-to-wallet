<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transaction Successful - {{ config('app.name') }}</title>
    <meta name="description" content="Your transaction has been successfully processed">

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
        @keyframes checkmark {
            0% {
                stroke-dashoffset: 100;
            }
            100% {
                stroke-dashoffset: 0;
            }
        }
        .checkmark {
            stroke-dasharray: 100;
            stroke-dashoffset: 100;
            animation: checkmark 1s ease-in-out forwards;
        }
        .receipt-container {
            background-color: white;
            border-radius: 0.5rem;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            padding: 2rem;
            position: relative;
        }
        .receipt-container::after {
            content: '';
            position: absolute;
            bottom: -10px;
            left: 0;
            right: 0;
            height: 10px;
            background-image: linear-gradient(45deg, white 25%, transparent 25%),
                              linear-gradient(-45deg, white 25%, transparent 25%);
            background-size: 20px 20px;
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
                        <a href="{{ route('transactions.initiate') }}" class="flex items-center px-4 py-3 text-dark hover:bg-primary hover:text-white rounded-lg transition-colors {{ request()->routeIs('transactions.initiate') || request()->routeIs('transactions.confirm') || request()->routeIs('transactions.payment') || request()->routeIs('transactions.success') ? 'bg-primary text-white' : '' }}">
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
                <h1 class="text-2xl font-bold text-dark">Transaction Successful</h1>
                
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
            <div class="max-w-3xl mx-auto">
                <!-- Success Message -->
                <div class="bg-white rounded-xl shadow-sm overflow-hidden mb-6 text-center py-10">
                    <div class="w-24 h-24 mx-auto bg-success bg-opacity-10 rounded-full flex items-center justify-center mb-6">
                        <svg width="50" height="50" viewBox="0 0 50 50" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <circle cx="25" cy="25" r="24" stroke="#28A745" stroke-width="2"/>
                            <path d="M15 25L22 32L35 18" stroke="#28A745" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" class="checkmark"/>
                        </svg>
                    </div>
                    
                    <h2 class="text-2xl font-bold text-dark mb-2">Transaction Successful!</h2>
                    <p class="text-gray-600 mb-6">Your payment has been processed and funds have been sent to the mobile wallet.</p>
                    
                    <div class="flex flex-col md:flex-row justify-center space-y-3 md:space-y-0 md:space-x-3 px-6">
                        <a href="{{ route('transactions.show', $transaction->uuid) }}" class="inline-flex justify-center items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary hover:bg-opacity-90 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary">
                            <i class="fas fa-receipt mr-2"></i> View Receipt
                        </a>
                        <a href="{{ route('dashboard') }}" class="inline-flex justify-center items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary">
                            <i class="fas fa-home mr-2"></i> Back to Dashboard
                        </a>
                        <a href="{{ route('transactions.initiate') }}" class="inline-flex justify-center items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary">
                            <i class="fas fa-plus mr-2"></i> New Transaction
                        </a>
                    </div>
                </div>
                
                <!-- Transaction Receipt -->
                <div class="receipt-container mb-6">
                    <div class="flex justify-between items-start mb-6">
                        <div>
                            <img src="{{ asset('assets/img/logo.png') }}" alt="Logo" class="h-10 mb-2">
                            <h3 class="text-lg font-bold text-dark">Transaction Receipt</h3>
                        </div>
                        <div class="text-right">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-success bg-opacity-10 text-success">
                                Completed
                            </span>
                            <p class="text-sm text-gray-500 mt-1">{{ $transaction->created_at->format('M d, Y h:i A') }}</p>
                        </div>
                    </div>
                    
                    <div class="border-t border-dashed border-gray-200 my-4 pt-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <h4 class="text-xs font-medium text-gray-500 uppercase mb-1">Transaction Reference</h4>
                                <p class="text-dark font-medium">{{ $transaction->uuid }}</p>
                            </div>
                            
                            <div>
                                <h4 class="text-xs font-medium text-gray-500 uppercase mb-1">Payment Method</h4>
                                <p class="text-dark font-medium">Credit/Debit Card</p>
                            </div>
                            
                            <div>
                                <h4 class="text-xs font-medium text-gray-500 uppercase mb-1">Recipient</h4>
                                <div class="flex items-center">
                                    @if($transaction->wallet_provider)
                                        @if($transaction->wallet_provider->api_code === 'airtel')
                                            <img class="h-5 w-5 rounded-full mr-2" src="{{ asset('assets/img/airtel.png') }}" alt="Airtel">
                                        @elseif($transaction->wallet_provider->api_code === 'mtn')
                                            <img class="h-5 w-5 rounded-full mr-2" src="{{ asset('assets/img/mtn.jpg') }}" alt="MTN">
                                        @elseif($transaction->wallet_provider->api_code === 'zamtel')
                                            <img class="h-5 w-5 rounded-full mr-2" src="{{ asset('assets/img/zamtel.jpg') }}" alt="Zamtel">
                                        @endif
                                    @endif
                                    <p class="text-dark font-medium">{{ $transaction->recipient_name ?: 'Unknown' }}</p>
                                </div>
                                <p class="text-sm text-gray-500">+260{{ $transaction->wallet_number }}</p>
                            </div>
                            
                            <div>
                                <h4 class="text-xs font-medium text-gray-500 uppercase mb-1">Provider Reference</h4>
                                <p class="text-dark font-medium">{{ $transaction->provider_reference ?: 'N/A' }}</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="border-t border-dashed border-gray-200 my-4 pt-4">
                        <div class="flex justify-between items-center mb-2">
                            <span class="text-gray-600">Amount:</span>
                            <span class="font-medium">K{{ number_format($transaction->amount, 2) }}</span>
                        </div>
                        <div class="flex justify-between items-center mb-2">
                            <span class="text-gray-600">Fee (4%):</span>
                            <span class="font-medium">K{{ number_format($transaction->fee_amount, 2) }}</span>
                        </div>
                        <div class="border-t border-gray-200 my-2 pt-2 flex justify-between items-center">
                            <span class="text-gray-700 font-medium">Total:</span>
                            <span class="font-bold text-dark text-lg">K{{ number_format($transaction->total_amount, 2) }}</span>
                        </div>
                    </div>
                    
                    <div class="border-t border-dashed border-gray-200 mt-4 pt-4 text-center">
                        <p class="text-sm text-gray-500 mb-2">Thank you for using our service!</p>
                        <p class="text-xs text-gray-400">For any questions, please contact our support team.</p>
                    </div>
                </div>
                
                <!-- What's Next -->
                <div class="bg-white rounded-xl shadow-sm overflow-hidden p-6">
                    <h3 class="text-lg font-bold text-dark mb-4">What's Next?</h3>
                    
                    <div class="space-y-4">
                        <div class="flex items-start">
                            <div class="flex-shrink-0 h-6 w-6 rounded-full bg-primary bg-opacity-10 text-primary flex items-center justify-center mr-3">
                                <i class="fas fa-mobile-alt text-xs"></i>
                            </div>
                            <div>
                                <h4 class="text-sm font-medium text-dark">Check Your Mobile Wallet</h4>
                                <p class="text-sm text-gray-600">The funds should be available in the recipient's mobile wallet immediately.</p>
                            </div>
                        </div>
                        
                        <div class="flex items-start">
                            <div class="flex-shrink-0 h-6 w-6 rounded-full bg-primary bg-opacity-10 text-primary flex items-center justify-center mr-3">
                                <i class="fas fa-envelope text-xs"></i>
                            </div>
                            <div>
                                <h4 class="text-sm font-medium text-dark">Check Your Email</h4>
                                <p class="text-sm text-gray-600">We've sent a receipt to your email address for your records.</p>
                            </div>
                        </div>
                        
                        <div class="flex items-start">
                            <div class="flex-shrink-0 h-6 w-6 rounded-full bg-primary bg-opacity-10 text-primary flex items-center justify-center mr-3">
                                <i class="fas fa-history text-xs"></i>
                            </div>
                            <div>
                                <h4 class="text-sm font-medium text-dark">View Transaction History</h4>
                                <p class="text-sm text-gray-600">You can view this transaction and all your past transactions in your transaction history.</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mt-6 text-center">
                        <a href="{{ route('transactions.history') }}" class="inline-flex justify-center items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary">
                            <i class="fas fa-history mr-2"></i> View Transaction History
                        </a>
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
