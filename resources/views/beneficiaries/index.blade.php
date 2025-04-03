<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Beneficiaries - {{ config('app.name') }}</title>
    <meta name="description" content="Manage your saved beneficiaries">

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
                <h1 class="text-2xl font-bold text-dark">Beneficiaries</h1>

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

            <!-- Beneficiaries Management -->
            <div class="bg-white rounded-xl shadow-sm overflow-hidden mb-6">
                <div class="p-6">
                    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6">
                        <h2 class="text-xl font-bold text-dark mb-2 md:mb-0">Your Saved Beneficiaries</h2>

                        <button id="add-beneficiary-button" class="inline-flex items-center px-3 py-2 bg-primary text-white rounded text-sm hover:bg-opacity-90 transition">
                            <i class="fas fa-plus mr-1"></i> Add New Beneficiary
                        </button>
                    </div>

                    <!-- Search and Filter -->
                    <div class="mb-6">
                        <div class="relative">
                            <input type="text" id="search-beneficiaries" placeholder="Search beneficiaries..." class="w-full pl-10 pr-4 py-2 border rounded-lg focus:ring-2 focus:ring-primary focus:border-primary">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-search text-gray-400"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Beneficiaries Grid -->
                    @if(count($beneficiaries ?? []) > 0)
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            @foreach($beneficiaries as $beneficiary)
                                <div class="border rounded-lg overflow-hidden hover:shadow-md transition beneficiary-card">
                                    <div class="p-4">
{{--                                        {{dd($beneficiary)}}--}}
                                        <div class="flex items-center mb-3">
                                            <div class="flex-shrink-0 h-10 w-10 rounded-full flex items-center justify-center mr-3">
                                                @if($beneficiary->wallet_provider->api_code === 'airtel')
                                                    <img class="h-10 w-10 rounded-full" src="{{ asset('assets/img/airtel.png') }}" alt="Airtel">
                                                @elseif($beneficiary->wallet_provider->api_code === 'mtn')
                                                    <img class="h-10 w-10 rounded-full" src="{{ asset('assets/img/mtn.jpg') }}" alt="MTN">
                                                @elseif($beneficiary->wallet_provider->api_code === 'zamtel')
                                                    <img class="h-10 w-10 rounded-full" src="{{ asset('assets/img/zamtel.jpg') }}" alt="Zamtel">
                                                @else
                                                    <div class="h-10 w-10 rounded-full bg-primary text-white flex items-center justify-center">
                                                        <i class="fas fa-wallet"></i>
                                                    </div>
                                                @endif
                                            </div>
                                            <div>
                                                <h3 class="font-medium text-dark">{{ $beneficiary->recipient_name }}</h3>
                                                <p class="text-sm text-gray-500">+260{{ $beneficiary->wallet_number }}</p>
                                            </div>
                                            @if($beneficiary->is_favorite)
                                                <div class="ml-auto text-secondary">
                                                    <i class="fas fa-star"></i>
                                                </div>
                                            @endif
                                        </div>

                                        @if($beneficiary->notes)
                                            <p class="text-sm text-gray-600 mb-3">{{ $beneficiary->notes }}</p>
                                        @endif

                                        <div class="flex justify-between items-center">
                                            <span class="text-xs text-gray-500">Last used: {{ $beneficiary->updated_at->diffForHumans() }}</span>

                                            <div class="flex space-x-2">
                                                <a href="{{ route('transactions.initiate', ['beneficiary_id' => $beneficiary->id]) }}" class="text-primary hover:text-primary-dark" title="Send Money">
                                                    <i class="fas fa-paper-plane"></i>
                                                </a>
                                                <button class="text-gray-500 hover:text-dark edit-beneficiary" data-id="{{ $beneficiary->id }}" title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <button class="text-error hover:text-error-dark delete-beneficiary" data-id="{{ $beneficiary->id }}" title="Delete">
                                                    <i class="fas fa-trash-alt"></i>
                                                </button>
                                                <form action="{{ route('beneficiaries.toggle-favorite', $beneficiary->id) }}" method="POST" class="inline">
                                                    @csrf
                                                    <button type="submit" class="{{ $beneficiary->is_favorite ? 'text-secondary' : 'text-gray-400' }} hover:text-secondary" title="{{ $beneficiary->is_favorite ? 'Remove from favorites' : 'Add to favorites' }}">
                                                        <i class="fas {{ $beneficiary->is_favorite ? 'fa-star' : 'fa-star' }}"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-12">
                            <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-gray-100 text-gray-400 mb-4">
                                <i class="fas fa-users text-2xl"></i>
                            </div>
                            <h3 class="text-lg font-medium text-gray-900 mb-1">No beneficiaries found</h3>
                            <p class="text-gray-500 mb-4">You haven't added any beneficiaries yet.</p>
                            <button id="empty-add-beneficiary-button" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary hover:bg-opacity-90 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary">
                                <i class="fas fa-plus mr-2"></i> Add Your First Beneficiary
                            </button>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Benefits of Saving Beneficiaries -->
            <div class="bg-white rounded-xl shadow-sm overflow-hidden p-6">
                <h3 class="text-lg font-bold text-dark mb-4">Benefits of Saving Beneficiaries</h3>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="flex items-start">
                        <div class="flex-shrink-0 h-10 w-10 rounded-full bg-primary bg-opacity-10 text-primary flex items-center justify-center mr-3">
                            <i class="fas fa-bolt"></i>
                        </div>
                        <div>
                            <h4 class="font-medium text-dark">Faster Transactions</h4>
                            <p class="text-sm text-gray-600">Save time by quickly selecting saved recipients instead of entering details each time.</p>
                        </div>
                    </div>

                    <div class="flex items-start">
                        <div class="flex-shrink-0 h-10 w-10 rounded-full bg-success bg-opacity-10 text-success flex items-center justify-center mr-3">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <div>
                            <h4 class="font-medium text-dark">Avoid Errors</h4>
                            <p class="text-sm text-gray-600">Reduce the risk of sending money to the wrong number by using verified beneficiaries.</p>
                        </div>
                    </div>

                    <div class="flex items-start">
                        <div class="flex-shrink-0 h-10 w-10 rounded-full bg-secondary bg-opacity-10 text-secondary flex items-center justify-center mr-3">
                            <i class="fas fa-star"></i>
                        </div>
                        <div>
                            <h4 class="font-medium text-dark">Favorite Recipients</h4>
                            <p class="text-sm text-gray-600">Mark your most frequent recipients as favorites for even quicker access.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Add/Edit Beneficiary Modal -->
    <div id="beneficiary-modal" class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center hidden">
        <div class="bg-white rounded-lg shadow-xl max-w-md w-full mx-4">
            <div class="p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-bold text-dark" id="modal-title">Add New Beneficiary</h3>
                    <button id="close-modal" class="text-gray-400 hover:text-gray-500">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <form id="beneficiary-form" action="{{ route('beneficiaries.store') }}" method="POST">
                    @csrf
                    <input type="hidden" id="beneficiary-id" name="id">

                    <div class="space-y-4">
                        <div>
                            <label for="recipient_name" class="block text-sm font-medium text-gray-700 mb-1">Recipient Name</label>
                            <input type="text" id="recipient_name" name="recipient_name" class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-primary focus:border-primary" required>
                        </div>

                        <div>
                            <label for="wallet_provider_id" class="block text-sm font-medium text-gray-700 mb-1">Mobile Money Provider</label>
                            <select id="wallet_provider_id" name="wallet_provider_id" class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-primary focus:border-primary" required>
                                <option value="">Select Provider</option>
                                @foreach($walletProviders ?? [] as $provider)
                                    <option value="{{ $provider->id }}">{{ $provider->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label for="wallet_number" class="block text-sm font-medium text-gray-700 mb-1">Wallet Number</label>
                            <div class="flex">
                                <span class="inline-flex items-center px-3 py-2 rounded-l-md border border-r-0 border-gray-300 bg-gray-50 text-gray-500">+260</span>
                                <input type="text" id="wallet_number" name="wallet_number" class="flex-1 min-w-0 block w-full px-3 py-2 rounded-none rounded-r-md border focus:ring-primary focus:border-primary" placeholder="9XXXXXXXX" pattern="[0-9]{9}" required>
                            </div>
                            <p class="mt-1 text-xs text-gray-500">Enter 9 digits without the leading zero</p>
                        </div>

                        <div>
                            <label for="notes" class="block text-sm font-medium text-gray-700 mb-1">Notes (Optional)</label>
                            <textarea id="notes" name="notes" rows="2" class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-primary focus:border-primary"></textarea>
                        </div>

                        <div class="flex items-center">
                            <input type="checkbox" id="is_favorite" name="is_favorite" class="h-4 w-4 text-primary focus:ring-primary border-gray-300 rounded">
                            <label for="is_favorite" class="ml-2 block text-sm text-gray-700">Add to favorites</label>
                        </div>
                    </div>

                    <div class="mt-6 flex justify-end space-x-3">
                        <button type="button" id="cancel-modal" class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary">
                            Cancel
                        </button>
                        <button type="submit" class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary hover:bg-opacity-90 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary">
                            <span id="submit-button-text">Save Beneficiary</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div id="delete-modal" class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center hidden">
        <div class="bg-white rounded-lg shadow-xl max-w-md w-full mx-4">
            <div class="p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-bold text-dark">Delete Beneficiary</h3>
                    <button id="close-delete-modal" class="text-gray-400 hover:text-gray-500">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <p class="text-gray-600 mb-4">Are you sure you want to delete this beneficiary? This action cannot be undone.</p>

                <form id="delete-form" action="{{ route('beneficiaries.destroy', 0) }}" method="POST">
                    @csrf
                    @method('DELETE')

                    <div class="flex justify-end space-x-3">
                        <button type="button" id="cancel-delete" class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary">
                            Cancel
                        </button>
                        <button type="submit" class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-error hover:bg-opacity-90 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-error">
                            Delete
                        </button>
                    </div>
                </form>
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

            // Beneficiary Modal
            const beneficiaryModal = document.getElementById('beneficiary-modal');
            const addBeneficiaryButton = document.getElementById('add-beneficiary-button');
            const emptyAddBeneficiaryButton = document.getElementById('empty-add-beneficiary-button');
            const closeModalButton = document.getElementById('close-modal');
            const cancelModalButton = document.getElementById('cancel-modal');
            const beneficiaryForm = document.getElementById('beneficiary-form');
            const modalTitle = document.getElementById('modal-title');
            const submitButtonText = document.getElementById('submit-button-text');

            // Open modal for adding new beneficiary
            if (addBeneficiaryButton) {
                addBeneficiaryButton.addEventListener('click', function() {
                    modalTitle.textContent = 'Add New Beneficiary';
                    submitButtonText.textContent = 'Save Beneficiary';
                    beneficiaryForm.reset();
                    document.getElementById('beneficiary-id').value = '';
                    beneficiaryForm.action = "{{ route('beneficiaries.store') }}";
                    beneficiaryModal.classList.remove('hidden');
                });
            }

            if (emptyAddBeneficiaryButton) {
                emptyAddBeneficiaryButton.addEventListener('click', function() {
                    modalTitle.textContent = 'Add New Beneficiary';
                    submitButtonText.textContent = 'Save Beneficiary';
                    beneficiaryForm.reset();
                    document.getElementById('beneficiary-id').value = '';
                    beneficiaryForm.action = "{{ route('beneficiaries.store') }}";
                    beneficiaryModal.classList.remove('hidden');
                });
            }

            // Close modal
            if (closeModalButton) {
                closeModalButton.addEventListener('click', function() {
                    beneficiaryModal.classList.add('hidden');
                });
            }

            if (cancelModalButton) {
                cancelModalButton.addEventListener('click', function() {
                    beneficiaryModal.classList.add('hidden');
                });
            }

            // Close modal when clicking outside
            beneficiaryModal?.addEventListener('click', function(event) {
                if (event.target === beneficiaryModal) {
                    beneficiaryModal.classList.add('hidden');
                }
            });

            // Edit beneficiary
            const editButtons = document.querySelectorAll('.edit-beneficiary');
            editButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const beneficiaryId = this.getAttribute('data-id');

                    // Here you would typically fetch the beneficiary data from the server
                    // For demonstration, we'll use a placeholder approach
                    // In a real application, you would make an AJAX request to get the data

                    modalTitle.textContent = 'Edit Beneficiary';
                    submitButtonText.textContent = 'Update Beneficiary';
                    document.getElementById('beneficiary-id').value = beneficiaryId;
                    beneficiaryForm.action = "{{ route('beneficiaries.update', '') }}/" + beneficiaryId;

                    // Add method spoofing for PUT request
                    let methodField = beneficiaryForm.querySelector('input[name="_method"]');
                    if (!methodField) {
                        methodField = document.createElement('input');
                        methodField.type = 'hidden';
                        methodField.name = '_method';
                        beneficiaryForm.appendChild(methodField);
                    }
                    methodField.value = 'PUT';

                    beneficiaryModal.classList.remove('hidden');
                });
            });

            // Delete beneficiary
            const deleteModal = document.getElementById('delete-modal');
            const closeDeleteModalButton = document.getElementById('close-delete-modal');
            const cancelDeleteButton = document.getElementById('cancel-delete');
            const deleteForm = document.getElementById('delete-form');
            const deleteButtons = document.querySelectorAll('.delete-beneficiary');

            deleteButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const beneficiaryId = this.getAttribute('data-id');
                    deleteForm.action = "{{ route('beneficiaries.destroy', '') }}/" + beneficiaryId;
                    deleteModal.classList.remove('hidden');
                });
            });

            if (closeDeleteModalButton) {
                closeDeleteModalButton.addEventListener('click', function() {
                    deleteModal.classList.add('hidden');
                });
            }

            if (cancelDeleteButton) {
                cancelDeleteButton.addEventListener('click', function() {
                    deleteModal.classList.add('hidden');
                });
            }

            // Close delete modal when clicking outside
            deleteModal?.addEventListener('click', function(event) {
                if (event.target === deleteModal) {
                    deleteModal.classList.add('hidden');
                }
            });

            // Search functionality
            const searchInput = document.getElementById('search-beneficiaries');
            const beneficiaryCards = document.querySelectorAll('.beneficiary-card');

            if (searchInput) {
                searchInput.addEventListener('input', function() {
                    const searchTerm = this.value.toLowerCase();

                    beneficiaryCards.forEach(card => {
                        const name = card.querySelector('h3').textContent.toLowerCase();
                        const number = card.querySelector('p').textContent.toLowerCase();
                        const notes = card.querySelector('p:nth-child(2)')?.textContent.toLowerCase() || '';

                        if (name.includes(searchTerm) || number.includes(searchTerm) || notes.includes(searchTerm)) {
                            card.style.display = '';
                        } else {
                            card.style.display = 'none';
                        }
                    });
                });
            }
        });
    </script>
</body>
</html>
