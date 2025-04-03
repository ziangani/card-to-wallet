<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transaction History - {{ config('app.name') }}</title>
    <meta name="description" content="View your transaction history">

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
                <h1 class="text-2xl font-bold text-dark">Transaction History</h1>

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

            <!-- Transaction History -->
            <div class="bg-white rounded-xl shadow-sm overflow-hidden mb-6">
                <div class="p-6">
                    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6">
                        <h2 class="text-xl font-bold text-dark mb-2 md:mb-0">Your Transaction History</h2>

                        <div class="flex space-x-2">
                            <a href="{{ route('transactions.initiate') }}" class="inline-flex items-center px-3 py-2 bg-primary text-white rounded text-sm hover:bg-opacity-90 transition">
                                <i class="fas fa-plus mr-1"></i> New Transaction
                            </a>
                        </div>
                    </div>

                    <!-- Filters -->
                    <div class="mb-6 bg-light rounded-lg p-4">
                        <form action="{{ route('transactions.history') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                            <!-- Date Range -->
                            <div>
                                <label for="date_from" class="block text-sm font-medium text-gray-700 mb-1">From Date</label>
                                <input type="date" id="date_from" name="date_from" value="{{ request('date_from') }}" class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-primary focus:border-primary">
                            </div>

                            <div>
                                <label for="date_to" class="block text-sm font-medium text-gray-700 mb-1">To Date</label>
                                <input type="date" id="date_to" name="date_to" value="{{ request('date_to') }}" class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-primary focus:border-primary">
                            </div>

                            <!-- Status Filter -->
                            <div>
                                <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                                <select id="status" name="status" class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-primary focus:border-primary">
                                    <option value="">All Statuses</option>
                                    <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="failed" {{ request('status') == 'failed' ? 'selected' : '' }}>Failed</option>
                                </select>
                            </div>

                            <!-- Search -->
                            <div>
                                <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                                <div class="relative">
                                    <input type="text" id="search" name="search" value="{{ request('search') }}" placeholder="Reference or recipient" class="w-full pl-10 pr-4 py-2 border rounded-lg focus:ring-2 focus:ring-primary focus:border-primary">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <i class="fas fa-search text-gray-400"></i>
                                    </div>
                                </div>
                            </div>

                            <!-- Filter Button -->
                            <div class="md:col-span-4 flex justify-end space-x-2">
                                <a href="{{ route('transactions.history') }}" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                                    Clear Filters
                                </a>
                                <button type="submit" class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-opacity-90">
                                    Apply Filters
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- Transactions Table -->
                    @if(count($transactions ?? []) > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead>
                                    <tr>
                                        <th class="px-4 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                        <th class="px-4 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Reference</th>
                                        <th class="px-4 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Recipient</th>
                                        <th class="px-4 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                                        <th class="px-4 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                        <th class="px-4 py-3 bg-gray-50 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($transactions as $transaction)
                                        <tr>
                                            <td class="px-4 py-4 whitespace-nowrap">
                                                <div class="text-sm text-gray-900">{{ $transaction->created_at->format('M d, Y') }}</div>
                                                <div class="text-xs text-gray-500">{{ $transaction->created_at->format('h:i A') }}</div>
                                            </td>
                                            <td class="px-4 py-4 whitespace-nowrap">
                                                <div class="text-sm text-gray-900">{{ Str::limit($transaction->uuid, 8) }}</div>
                                            </td>
                                            <td class="px-4 py-4">
                                                <div class="flex items-center">
                                                    <div class="flex-shrink-0 h-8 w-8">
                                                        @if($transaction->wallet_provider)
                                                            @if($transaction->wallet_provider->api_code === 'airtel')
                                                                <img class="h-8 w-8 rounded-full" src="{{ asset('assets/img/airtel.png') }}" alt="Airtel">
                                                            @elseif($transaction->wallet_provider->api_code === 'mtn')
                                                                <img class="h-8 w-8 rounded-full" src="{{ asset('assets/img/mtn.jpg') }}" alt="MTN">
                                                            @elseif($transaction->wallet_provider->api_code === 'zamtel')
                                                                <img class="h-8 w-8 rounded-full" src="{{ asset('assets/img/zamtel.jpg') }}" alt="Zamtel">
                                                            @else
                                                                <div class="h-8 w-8 rounded-full bg-primary text-white flex items-center justify-center">
                                                                    <i class="fas fa-wallet"></i>
                                                                </div>
                                                            @endif
                                                        @else
                                                            <div class="h-8 w-8 rounded-full bg-primary text-white flex items-center justify-center">
                                                                <i class="fas fa-wallet"></i>
                                                            </div>
                                                        @endif
                                                    </div>
                                                    <div class="ml-3">
                                                        <div class="text-sm font-medium text-gray-900">
                                                            {{ $transaction->reference_4 ?: 'Unknown' }}
                                                        </div>
                                                        <div class="text-xs text-gray-500">
                                                            +260{{ $transaction->reference_1 }}
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-4 py-4">
                                <div class="text-sm font-medium text-gray-900">K{{ number_format($transaction->amount, 2) }}</div>
                                <div class="text-xs text-gray-500">Fee: K{{ number_format(Transaction::calculateFee($transaction->amount), 2) }}</div>
                                            </td>
                                            <td class="px-4 py-4">
                                                @if($transaction->status === 'completed')
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-success bg-opacity-10 text-success">
                                                        Completed
                                                    </span>
                                                @elseif($transaction->status === 'pending' || $transaction->status === 'payment_initiated')
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-warning bg-opacity-10 text-warning">
                                                        Pending
                                                    </span>
                                                @elseif($transaction->status === 'failed' || $transaction->status === 'payment_failed')
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-error bg-opacity-10 text-error">
                                                        Failed
                                                    </span>
                                                @else
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                                        {{ ucfirst($transaction->status) }}
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="px-4 py-4 text-right text-sm font-medium">
                                                <a href="{{ route('transactions.show', $transaction->uuid) }}" class="text-primary hover:underline">
                                                    Details
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="mt-6">
                            {{ $transactions->withQueryString()->links() }}
                        </div>
                    @else
                        <div class="text-center py-12">
                            <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-gray-100 text-gray-400 mb-4">
                                <i class="fas fa-history text-2xl"></i>
                            </div>
                            <h3 class="text-lg font-medium text-gray-900 mb-1">No transactions found</h3>
                            <p class="text-gray-500 mb-4">You haven't made any transactions yet or no transactions match your filters.</p>
                            <a href="{{ route('transactions.initiate') }}" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary hover:bg-opacity-90 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary">
                                <i class="fas fa-plus mr-2"></i> New Transaction
                            </a>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Transaction Summary -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                <!-- Total Transactions -->
                <div class="bg-white rounded-xl shadow-sm p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 w-12 h-12 rounded-full bg-primary bg-opacity-10 flex items-center justify-center text-primary">
                            <i class="fas fa-exchange-alt"></i>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-sm font-medium text-gray-500">Total Transactions</h3>
                            <p class="text-2xl font-bold text-dark">{{ $totalTransactions ?? 0 }}</p>
                        </div>
                    </div>
                </div>

                <!-- Total Amount -->
                <div class="bg-white rounded-xl shadow-sm p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 w-12 h-12 rounded-full bg-success bg-opacity-10 flex items-center justify-center text-success">
                            <i class="fas fa-money-bill-wave"></i>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-sm font-medium text-gray-500">Total Amount</h3>
                            <p class="text-2xl font-bold text-dark">K{{ number_format($totalAmount ?? 0, 2) }}</p>
                        </div>
                    </div>
                </div>

                <!-- Successful Transactions -->
                <div class="bg-white rounded-xl shadow-sm p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 w-12 h-12 rounded-full bg-secondary bg-opacity-10 flex items-center justify-center text-secondary">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-sm font-medium text-gray-500">Successful Transactions</h3>
                            <p class="text-2xl font-bold text-dark">{{ $successfulTransactions ?? 0 }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Export Options -->
            <div class="bg-white rounded-xl shadow-sm p-6 mb-6">
                <h3 class="text-lg font-bold text-dark mb-4">Export Transactions</h3>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <a href="{{ route('transactions.export', ['format' => 'pdf']) }}" class="flex items-center p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition">
                        <div class="w-10 h-10 rounded-full bg-error bg-opacity-10 flex items-center justify-center text-error mr-3">
                            <i class="fas fa-file-pdf"></i>
                        </div>
                        <div>
                            <h4 class="font-medium text-dark">PDF Export</h4>
                            <p class="text-xs text-gray-500">Download as PDF document</p>
                        </div>
                    </a>

                    <a href="{{ route('transactions.export', ['format' => 'csv']) }}" class="flex items-center p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition">
                        <div class="w-10 h-10 rounded-full bg-success bg-opacity-10 flex items-center justify-center text-success mr-3">
                            <i class="fas fa-file-csv"></i>
                        </div>
                        <div>
                            <h4 class="font-medium text-dark">CSV Export</h4>
                            <p class="text-xs text-gray-500">Download as CSV spreadsheet</p>
                        </div>
                    </a>

                    <a href="{{ route('transactions.export', ['format' => 'excel']) }}" class="flex items-center p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition">
                        <div class="w-10 h-10 rounded-full bg-primary bg-opacity-10 flex items-center justify-center text-primary mr-3">
                            <i class="fas fa-file-excel"></i>
                        </div>
                        <div>
                            <h4 class="font-medium text-dark">Excel Export</h4>
                            <p class="text-xs text-gray-500">Download as Excel spreadsheet</p>
                        </div>
                    </a>
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

            // Date range validation
            const dateFromInput = document.getElementById('date_from');
            const dateToInput = document.getElementById('date_to');

            if (dateFromInput && dateToInput) {
                dateFromInput.addEventListener('change', function() {
                    if (dateToInput.value && this.value > dateToInput.value) {
                        dateToInput.value = this.value;
                    }
                });

                dateToInput.addEventListener('change', function() {
                    if (dateFromInput.value && this.value < dateFromInput.value) {
                        dateFromInput.value = this.value;
                    }
                });
            }
        });
    </script>
</body>
</html>
