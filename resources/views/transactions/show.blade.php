<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transaction Details - {{ config('app.name') }}</title>
    <meta name="description" content="View transaction details">

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
        .timeline-container {
            position: relative;
            padding-left: 2rem;
        }
        .timeline-container::before {
            content: '';
            position: absolute;
            top: 0;
            bottom: 0;
            left: 0.75rem;
            width: 2px;
            background-color: #e5e7eb;
        }
        .timeline-item {
            position: relative;
            padding-bottom: 1.5rem;
        }
        .timeline-item:last-child {
            padding-bottom: 0;
        }
        .timeline-marker {
            position: absolute;
            top: 0;
            left: -2rem;
            width: 1.5rem;
            height: 1.5rem;
            border-radius: 50%;
            background-color: #f3f4f6;
            border: 2px solid #e5e7eb;
            z-index: 10;
        }
        .timeline-marker.active {
            background-color: #3366CC;
            border-color: #3366CC;
        }
        .timeline-marker.success {
            background-color: #28A745;
            border-color: #28A745;
        }
        .timeline-marker.error {
            background-color: #DC3545;
            border-color: #DC3545;
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
                        <a href="{{ route('transactions.history') }}" class="flex items-center px-4 py-3 text-dark hover:bg-primary hover:text-white rounded-lg transition-colors {{ request()->routeIs('transactions.history') || request()->routeIs('transactions.show') ? 'bg-primary text-white' : '' }}">
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
                <h1 class="text-2xl font-bold text-dark">Transaction Details</h1>

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
            <div class="mb-6">
                <a href="{{ route('transactions.history') }}" class="inline-flex items-center text-primary hover:underline">
                    <i class="fas fa-arrow-left mr-2"></i> Back to Transaction History
                </a>
            </div>

            <div class="max-w-4xl mx-auto">
                <!-- Transaction Status -->
                <div class="bg-white rounded-xl shadow-sm overflow-hidden mb-6 p-6">
                    <div class="flex flex-col md:flex-row items-start md:items-center justify-between mb-6">
                        <div class="flex items-center mb-4 md:mb-0">
                            <div class="flex-shrink-0 mr-4">
                                @if($transaction->status === 'COMPLETED')
                                    <div class="w-16 h-16 rounded-full bg-success bg-opacity-10 flex items-center justify-center text-success">
                                        <i class="fas fa-check-circle text-2xl"></i>
                                    </div>
                                @elseif($transaction->status === 'pending' || $transaction->status === 'payment_initiated')
                                    <div class="w-16 h-16 rounded-full bg-warning bg-opacity-10 flex items-center justify-center text-warning">
                                        <i class="fas fa-clock text-2xl"></i>
                                    </div>
                                @elseif($transaction->status === 'failed' || $transaction->status === 'payment_failed')
                                    <div class="w-16 h-16 rounded-full bg-error bg-opacity-10 flex items-center justify-center text-error">
                                        <i class="fas fa-times-circle text-2xl"></i>
                                    </div>
                                @else
                                    <div class="w-16 h-16 rounded-full bg-gray-100 flex items-center justify-center text-gray-500">
                                        <i class="fas fa-question-circle text-2xl"></i>
                                    </div>
                                @endif
                            </div>
                            <div>
                                <h2 class="text-xl font-bold text-dark">
                                    @if($transaction->status === 'COMPLETED')
                                        Transaction Successful
                                    @elseif($transaction->status === 'pending' || $transaction->status === 'payment_initiated')
                                        Transaction Pending
                                    @elseif($transaction->status === 'failed' || $transaction->status === 'payment_failed')
                                        Transaction Failed
                                    @else
                                        {{ ucfirst($transaction->status) }}
                                    @endif
                                </h2>
                                <p class="text-gray-500">{{ $transaction->created_at->format('M d, Y h:i A') }}</p>
                            </div>
                        </div>

                        <div>
                            @if($transaction->status === 'COMPLETED')
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-success bg-opacity-10 text-success">
                                    Completed
                                </span>
                            @elseif($transaction->status === 'pending' || $transaction->status === 'payment_initiated')
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-warning bg-opacity-10 text-warning">
                                    Pending
                                </span>
                            @elseif($transaction->status === 'failed' || $transaction->status === 'payment_failed')
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-error bg-opacity-10 text-error">
                                    Failed
                                </span>
                            @else
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                    {{ ucfirst($transaction->status) }}
                                </span>
                            @endif
                        </div>
                    </div>

                    @if($transaction->status === 'failed' || $transaction->status === 'payment_failed')
                        <div class="bg-error bg-opacity-10 text-error rounded-lg p-4 mb-6">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-exclamation-circle mt-0.5"></i>
                                </div>
                                <div class="ml-3">
                                    <h3 class="text-sm font-medium">Transaction Failed</h3>
                                    <p class="mt-1 text-sm">{{ $transaction->failure_reason ?: 'Your payment could not be processed. Please try again or use a different payment method.' }}</p>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Transaction Receipt -->
                    <div class="receipt-container">
                        <div class="flex justify-between items-start mb-6">
                            <div>
                                <img src="{{ asset('assets/img/logo.png') }}" alt="Logo" class="h-10 mb-2">
                                <h3 class="text-lg font-bold text-dark">Transaction Receipt</h3>
                            </div>
                            <div class="text-right">
                                <p class="text-sm text-gray-500">Reference: {{ $transaction->uuid }}</p>
                            </div>
                        </div>

                        <div class="border-t border-dashed border-gray-200 my-4 pt-4">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <h4 class="text-xs font-medium text-gray-500 uppercase mb-1">Transaction Type</h4>
                                    <p class="text-dark font-medium">Card to Wallet Transfer</p>
                                </div>

                                <div>
                                    <h4 class="text-xs font-medium text-gray-500 uppercase mb-1">Date & Time</h4>
                                    <p class="text-dark font-medium">{{ $transaction->created_at->format('M d, Y h:i A') }}</p>
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
                                        <p class="text-dark font-medium">{{ $transaction->reference_4 ?: 'Unknown' }}</p>
                                    </div>
                                    <p class="text-sm text-gray-500">+260{{ $transaction->reference_1 }}</p>
                                </div>

                                <div>
                                    <h4 class="text-xs font-medium text-gray-500 uppercase mb-1">Payment Method</h4>
                                    <p class="text-dark font-medium">Credit/Debit Card</p>
                                </div>

                                @if($transaction->purpose)
                                    <div>
                                        <h4 class="text-xs font-medium text-gray-500 uppercase mb-1">Purpose</h4>
                                        <p class="text-dark">{{ $transaction->purpose }}</p>
                                    </div>
                                @endif

                                @if($transaction->notes)
                                    <div>
                                        <h4 class="text-xs font-medium text-gray-500 uppercase mb-1">Notes</h4>
                                        <p class="text-dark">{{ $transaction->notes }}</p>
                                    </div>
                                @endif

                                @if($transaction->provider_reference)
                                    <div>
                                        <h4 class="text-xs font-medium text-gray-500 uppercase mb-1">Provider Reference</h4>
                                        <p class="text-dark font-medium">{{ $transaction->provider_reference }}</p>
                                    </div>
                                @endif

                                @if($transaction->mpgs_order_id)
                                    <div>
                                        <h4 class="text-xs font-medium text-gray-500 uppercase mb-1">MPGS Order ID</h4>
                                        <p class="text-dark font-medium">{{ $transaction->mpgs_order_id }}</p>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div class="border-t border-dashed border-gray-200 my-4 pt-4">
                            <div class="flex justify-between items-center mb-2">
                                <span class="text-gray-600">Amount Funded:</span>
                                <span class="font-medium">K{{ number_format($transaction->amount, 2) }}</span>
                            </div>

                            <!-- Fee Breakdown -->
                            <div class="mb-2">
                                <div class="flex justify-between items-center mb-1">
                                    <span class="text-gray-600">Fees:</span>
                                    <span class="font-medium">K{{ number_format($transaction->fee_amount, 2) }}</span>
                                </div>

                                <!-- Simplified Fee Details -->
                                <div class="pl-4 text-sm">
                                    <div class="flex justify-between items-center text-gray-500">
                                        <span>Bank Fee</span>
                                        <span>K{{ number_format($transaction->getTransactionVariableFee(), 2) }}</span>
                                    </div>
                                    <div class="flex justify-between items-center text-gray-500">
                                        <span>Deposit Fee</span>
                                        <span>K{{ number_format($transaction->getTransactionFixedFee(), 2) }}</span>
                                    </div>
                                </div>
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

                    <!-- Action Buttons -->
                    <div class="mt-6 flex flex-col md:flex-row space-y-3 md:space-y-0 md:space-x-3">
                        @if($transaction->status === 'COMPLETED')
                            <a href="{{ route('transactions.download', $transaction->uuid) }}" class="inline-flex justify-center items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary hover:bg-opacity-90 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary">
                                <i class="fas fa-download mr-2"></i> Download Receipt
                            </a>

                            <button type="button" id="email-receipt-btn" class="inline-flex justify-center items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary">
                                <i class="fas fa-envelope mr-2"></i> Email Receipt
                            </button>
                        @else
                            <button type="button" disabled class="inline-flex justify-center items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-gray-400 cursor-not-allowed">
                                <i class="fas fa-download mr-2"></i> Download Receipt
                            </button>

                            <button type="button" disabled class="inline-flex justify-center items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-500 bg-gray-100 cursor-not-allowed">
                                <i class="fas fa-envelope mr-2"></i> Email Receipt
                            </button>
                        @endif

                        @if($transaction->status === 'failed' || $transaction->status === 'payment_failed')
                            <a href="{{ route('transactions.retry', $transaction->uuid) }}" class="inline-flex justify-center items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-secondary hover:bg-opacity-90 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-secondary">
                                <i class="fas fa-redo mr-2"></i> Retry Transaction
                            </a>
                        @endif
                    </div>
                </div>

                <!-- Transaction Timeline -->
                <div class="bg-white rounded-xl shadow-sm overflow-hidden p-6 mb-6">
                    <h3 class="text-lg font-bold text-dark mb-4">Transaction Timeline</h3>

                    <div class="timeline-container">
                        @foreach($transaction->statuses ?? [] as $status)
                            <div class="timeline-item">
                                <div class="timeline-marker {{ $status->status === 'COMPLETED' ? 'success' : ($status->status === 'failed' || $status->status === 'payment_failed' ? 'error' : 'active') }}"></div>
                                <div class="ml-2">
                                    <h4 class="font-medium text-dark">
                                        @if($status->status === 'pending')
                                            Transaction Initiated
                                        @elseif($status->status === 'payment_initiated')
                                            Payment Initiated
                                        @elseif($status->status === 'payment_COMPLETED')
                                            Payment Completed
                                        @elseif($status->status === 'payment_failed')
                                            Payment Failed
                                        @elseif($status->status === 'funding_initiated')
                                            Wallet Funding Initiated
                                        @elseif($status->status === 'COMPLETED')
                                            Transaction Completed
                                        @elseif($status->status === 'failed')
                                            Transaction Failed
                                        @else
                                            {{ ucfirst($status->status) }}
                                        @endif
                                    </h4>
                                    <p class="text-sm text-gray-500">{{ $status->created_at->format('M d, Y h:i A') }}</p>
                                    @if($status->notes)
                                        <p class="text-sm text-gray-600 mt-1">{{ $status->notes }}</p>
                                    @endif
                                </div>
                            </div>
                        @endforeach

                        @if(empty($transaction->statuses))
                            <div class="timeline-item">
                                <div class="timeline-marker {{ $transaction->status === 'COMPLETED' ? 'success' : ($transaction->status === 'failed' || $transaction->status === 'payment_failed' ? 'error' : 'active') }}"></div>
                                <div class="ml-2">
                                    <h4 class="font-medium text-dark">
                                        @if($transaction->status === 'pending')
                                            Transaction Initiated
                                        @elseif($transaction->status === 'payment_initiated')
                                            Payment Initiated
                                        @elseif($transaction->status === 'payment_completed')
                                            Payment Completed
                                        @elseif($transaction->status === 'payment_failed')
                                            Payment Failed
                                        @elseif($transaction->status === 'funding_initiated')
                                            Wallet Funding Initiated
                                        @elseif($transaction->status === 'COMPLETED')
                                            Transaction Completed
                                        @elseif($transaction->status === 'failed')
                                            Transaction Failed
                                        @else
                                            {{ ucfirst($transaction->status) }}
                                        @endif
                                    </h4>
                                    <p class="text-sm text-gray-500">{{ $transaction->created_at->format('M d, Y h:i A') }}</p>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Need Help? -->
                <div class="bg-white rounded-xl shadow-sm overflow-hidden p-6">
                    <h3 class="text-lg font-bold text-dark mb-4">Need Help?</h3>

                    <div class="space-y-4">
                        <div class="flex items-start">
                            <div class="flex-shrink-0 h-6 w-6 rounded-full bg-primary bg-opacity-10 text-primary flex items-center justify-center mr-3">
                                <i class="fas fa-question-circle text-xs"></i>
                            </div>
                            <div>
                                <h4 class="text-sm font-medium text-dark">Have Questions?</h4>
                                <p class="text-sm text-gray-600">If you have any questions about this transaction, our support team is here to help.</p>
                            </div>
                        </div>

                        <div class="flex items-start">
                            <div class="flex-shrink-0 h-6 w-6 rounded-full bg-primary bg-opacity-10 text-primary flex items-center justify-center mr-3">
                                <i class="fas fa-exclamation-circle text-xs"></i>
                            </div>
                            <div>
                                <h4 class="text-sm font-medium text-dark">Report an Issue</h4>
                                <p class="text-sm text-gray-600">If you believe there's an issue with this transaction, please let us know.</p>
                            </div>
                        </div>
                    </div>

                    <div class="mt-6">
                        <a href="{{ route('support') }}" class="inline-flex justify-center items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary">
                            <i class="fas fa-headset mr-2"></i> Contact Support
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Email Receipt Modal -->
    <div id="email-receipt-modal" class="fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center z-50 hidden">
        <div class="bg-white rounded-lg shadow-xl max-w-md w-full mx-4">
            <div class="p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-bold text-dark">Email Receipt</h3>
                    <button type="button" id="close-modal" class="text-gray-400 hover:text-gray-500">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                
                <div id="email-form-container">
                    <p class="text-gray-600 mb-4">Enter your email address to receive a copy of this transaction receipt.</p>
                    
                    <form id="email-receipt-form">
                        <div class="mb-4">
                            <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
                            <input type="email" id="email" name="email" value="{{ auth()->user()->email }}" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary" required>
                        </div>
                        
                        <div class="flex justify-end space-x-3">
                            <button type="button" id="cancel-email" class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary">
                                Cancel
                            </button>
                            <button type="submit" class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary hover:bg-opacity-90 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary">
                                Send Receipt
                            </button>
                        </div>
                    </form>
                </div>
                
                <div id="email-success" class="hidden">
                    <div class="text-center py-6">
                        <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-success bg-opacity-10 text-success mb-4">
                            <i class="fas fa-check text-xl"></i>
                        </div>
                        <h3 class="text-lg font-medium text-dark mb-2">Receipt Sent!</h3>
                        <p class="text-gray-500" id="success-message"></p>
                        <button type="button" id="close-success" class="mt-6 px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary hover:bg-opacity-90 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary">
                            Close
                        </button>
                    </div>
                </div>
                
                <div id="email-error" class="hidden">
                    <div class="text-center py-6">
                        <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-error bg-opacity-10 text-error mb-4">
                            <i class="fas fa-exclamation-triangle text-xl"></i>
                        </div>
                        <h3 class="text-lg font-medium text-dark mb-2">Error</h3>
                        <p class="text-gray-500" id="error-message">An error occurred while sending the receipt.</p>
                        <button type="button" id="try-again" class="mt-6 px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary hover:bg-opacity-90 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary">
                            Try Again
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

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

            // Email Receipt Modal
            const emailReceiptBtn = document.getElementById('email-receipt-btn');
            const emailReceiptModal = document.getElementById('email-receipt-modal');
            const closeModalBtn = document.getElementById('close-modal');
            const cancelEmailBtn = document.getElementById('cancel-email');
            const emailForm = document.getElementById('email-receipt-form');
            const emailFormContainer = document.getElementById('email-form-container');
            const emailSuccess = document.getElementById('email-success');
            const emailError = document.getElementById('email-error');
            const closeSuccessBtn = document.getElementById('close-success');
            const tryAgainBtn = document.getElementById('try-again');
            const successMessage = document.getElementById('success-message');
            const errorMessage = document.getElementById('error-message');

            // Open modal
            if (emailReceiptBtn) {
                emailReceiptBtn.addEventListener('click', function() {
                    emailReceiptModal.classList.remove('hidden');
                    // Reset form state
                    emailFormContainer.classList.remove('hidden');
                    emailSuccess.classList.add('hidden');
                    emailError.classList.add('hidden');
                });
            }

            // Close modal functions
            const closeModal = function() {
                emailReceiptModal.classList.add('hidden');
            };

            if (closeModalBtn) closeModalBtn.addEventListener('click', closeModal);
            if (cancelEmailBtn) cancelEmailBtn.addEventListener('click', closeModal);
            if (closeSuccessBtn) closeSuccessBtn.addEventListener('click', closeModal);

            // Try again button
            if (tryAgainBtn) {
                tryAgainBtn.addEventListener('click', function() {
                    emailFormContainer.classList.remove('hidden');
                    emailError.classList.add('hidden');
                });
            }

            // Form submission
            if (emailForm) {
                emailForm.addEventListener('submit', function(e) {
                    e.preventDefault();
                    
                    const email = document.getElementById('email').value;
                    const formData = new FormData();
                    formData.append('email', email);
                    formData.append('_token', '{{ csrf_token() }}');
                    
                    // Show loading state
                    const submitBtn = emailForm.querySelector('button[type="submit"]');
                    const originalBtnText = submitBtn.innerHTML;
                    submitBtn.disabled = true;
                    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Sending...';
                    
                    fetch('{{ route('transactions.email-receipt', $transaction->uuid) }}', {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        // Reset button state
                        submitBtn.disabled = false;
                        submitBtn.innerHTML = originalBtnText;
                        
                        if (data.success) {
                            // Show success message
                            emailFormContainer.classList.add('hidden');
                            emailSuccess.classList.remove('hidden');
                            successMessage.textContent = data.message;
                        } else {
                            // Show error message
                            emailFormContainer.classList.add('hidden');
                            emailError.classList.remove('hidden');
                            errorMessage.textContent = data.message || 'An error occurred while sending the receipt.';
                        }
                    })
                    .catch(error => {
                        // Reset button state
                        submitBtn.disabled = false;
                        submitBtn.innerHTML = originalBtnText;
                        
                        // Show error message
                        emailFormContainer.classList.add('hidden');
                        emailError.classList.remove('hidden');
                        errorMessage.textContent = 'An error occurred while sending the receipt.';
                        console.error('Error:', error);
                    });
                });
            }

            // Close modal when clicking outside
            emailReceiptModal.addEventListener('click', function(event) {
                if (event.target === emailReceiptModal) {
                    closeModal();
                }
            });
        });
    </script>
</body>
</html>
