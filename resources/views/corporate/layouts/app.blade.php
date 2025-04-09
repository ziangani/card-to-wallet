<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ config('app.name') }} - Corporate Portal</title>
    
    <!-- Favicon -->
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}">
    
    <!-- Bootstrap CSS -->
    <link href="{{ asset('assets/css/bootstrap.min.css') }}" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link href="{{ asset('assets/libs/fontawesome/css/all.min.css') }}" rel="stylesheet">
    
    <!-- Custom CSS -->
    <link href="{{ asset('assets/css/app.css') }}" rel="stylesheet">
    
    <!-- Corporate specific CSS -->
    <style>
        :root {
            --corporate-primary: #2C3E50;
            --corporate-secondary: #34495E;
            --corporate-accent: #3498DB;
            --approval-green: #27AE60;
            --pending-amber: #F39C12;
            --rejection-red: #C0392B;
        }
        
        body {
            background-color: #f8f9fa;
        }
        
        .corporate-sidebar {
            background-color: var(--corporate-primary);
            min-height: 100vh;
            color: #fff;
            padding-top: 1rem;
        }
        
        .corporate-sidebar .nav-link {
            color: rgba(255, 255, 255, 0.8);
            padding: 0.75rem 1rem;
            border-radius: 0.25rem;
            margin-bottom: 0.25rem;
        }
        
        .corporate-sidebar .nav-link:hover {
            color: #fff;
            background-color: rgba(255, 255, 255, 0.1);
        }
        
        .corporate-sidebar .nav-link.active {
            color: #fff;
            background-color: var(--corporate-accent);
        }
        
        .corporate-sidebar .nav-link i {
            width: 1.5rem;
            text-align: center;
            margin-right: 0.5rem;
        }
        
        .corporate-header {
            background-color: #fff;
            border-bottom: 1px solid #e9ecef;
            padding: 0.75rem 1.5rem;
        }
        
        .corporate-content {
            padding: 1.5rem;
        }
        
        .corporate-card {
            border-radius: 0.5rem;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
            margin-bottom: 1.5rem;
        }
        
        .corporate-card .card-header {
            background-color: #fff;
            border-bottom: 1px solid rgba(0, 0, 0, 0.125);
            padding: 1rem 1.25rem;
            font-weight: 500;
        }
        
        .status-badge {
            padding: 0.35em 0.65em;
            font-size: 0.75em;
            font-weight: 500;
            border-radius: 0.25rem;
        }
        
        .status-badge.pending {
            background-color: var(--pending-amber);
            color: #fff;
        }
        
        .status-badge.approved {
            background-color: var(--approval-green);
            color: #fff;
        }
        
        .status-badge.rejected {
            background-color: var(--rejection-red);
            color: #fff;
        }
        
        .status-badge.completed {
            background-color: var(--approval-green);
            color: #fff;
        }
        
        .status-badge.failed {
            background-color: var(--rejection-red);
            color: #fff;
        }
        
        .status-badge.processing {
            background-color: var(--corporate-accent);
            color: #fff;
        }
        
        .btn-corporate-primary {
            background-color: var(--corporate-primary);
            border-color: var(--corporate-primary);
            color: #fff;
        }
        
        .btn-corporate-primary:hover {
            background-color: #1a252f;
            border-color: #1a252f;
            color: #fff;
        }
        
        .btn-corporate-accent {
            background-color: var(--corporate-accent);
            border-color: var(--corporate-accent);
            color: #fff;
        }
        
        .btn-corporate-accent:hover {
            background-color: #2980b9;
            border-color: #2980b9;
            color: #fff;
        }
        
        .table-corporate th {
            background-color: #f8f9fa;
            font-weight: 500;
        }
        
        .data-table {
            font-size: 0.875rem;
        }
        
        .data-table th {
            font-weight: 500;
            padding: 0.75rem;
        }
        
        .data-table td {
            padding: 0.75rem;
            vertical-align: middle;
        }
        
        .balance-card {
            background-color: var(--corporate-primary);
            color: #fff;
            border-radius: 0.5rem;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
        }
        
        .balance-card .balance-amount {
            font-size: 2rem;
            font-weight: 500;
            margin-bottom: 0.5rem;
        }
        
        .balance-card .balance-label {
            font-size: 0.875rem;
            opacity: 0.8;
        }
        
        .activity-item {
            padding: 0.75rem 0;
            border-bottom: 1px solid #e9ecef;
        }
        
        .activity-item:last-child {
            border-bottom: none;
        }
        
        .activity-item .activity-icon {
            width: 2.5rem;
            height: 2.5rem;
            border-radius: 50%;
            background-color: #f8f9fa;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 1rem;
        }
        
        .activity-item .activity-content {
            flex: 1;
        }
        
        .activity-item .activity-title {
            font-weight: 500;
            margin-bottom: 0.25rem;
        }
        
        .activity-item .activity-subtitle {
            font-size: 0.875rem;
            color: #6c757d;
        }
        
        .activity-item .activity-time {
            font-size: 0.75rem;
            color: #6c757d;
        }
        
        @media (max-width: 767.98px) {
            .corporate-sidebar {
                position: fixed;
                top: 0;
                left: -250px;
                width: 250px;
                z-index: 1030;
                transition: left 0.3s ease;
            }
            
            .corporate-sidebar.show {
                left: 0;
            }
            
            .corporate-content {
                margin-left: 0 !important;
            }
        }
    </style>
    
    @stack('styles')
</head>
<body>
    <div class="d-flex">
        <!-- Sidebar -->
        <div class="corporate-sidebar" style="width: 250px;">
            <div class="px-3 mb-4">
                <h5 class="mb-0">{{ config('app.name') }}</h5>
                <div class="small">Corporate Portal</div>
            </div>
            
            <div class="px-3 mb-3">
                <div class="small text-uppercase opacity-75 mb-2">Main Menu</div>
                <ul class="nav flex-column">
                    <li class="nav-item">
                        <a href="{{ route('corporate.dashboard') }}" class="nav-link {{ request()->routeIs('corporate.dashboard') ? 'active' : '' }}">
                            <i class="fas fa-tachometer-alt"></i> Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('corporate.wallet.index') }}" class="nav-link {{ request()->routeIs('corporate.wallet.*') ? 'active' : '' }}">
                            <i class="fas fa-wallet"></i> Wallet
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('corporate.disbursements.index') }}" class="nav-link {{ request()->routeIs('corporate.disbursements.*') ? 'active' : '' }}">
                            <i class="fas fa-money-bill-wave"></i> Disbursements
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('corporate.approvals.index') }}" class="nav-link {{ request()->routeIs('corporate.approvals.*') ? 'active' : '' }}">
                            <i class="fas fa-check-double"></i> Approvals
                            @if(isset($pendingApprovalsCount) && $pendingApprovalsCount > 0)
                                <span class="badge bg-danger ms-2">{{ $pendingApprovalsCount }}</span>
                            @endif
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('corporate.reports.index') }}" class="nav-link {{ request()->routeIs('corporate.reports.*') ? 'active' : '' }}">
                            <i class="fas fa-chart-bar"></i> Reports
                        </a>
                    </li>
                </ul>
            </div>
            
            <div class="px-3 mb-3">
                <div class="small text-uppercase opacity-75 mb-2">Administration</div>
                <ul class="nav flex-column">
                    <li class="nav-item">
                        <a href="{{ route('corporate.users.index') }}" class="nav-link {{ request()->routeIs('corporate.users.*') ? 'active' : '' }}">
                            <i class="fas fa-users"></i> Users
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('corporate.settings.profile') }}" class="nav-link {{ request()->routeIs('corporate.settings.*') ? 'active' : '' }}">
                            <i class="fas fa-cog"></i> Settings
                        </a>
                    </li>
                </ul>
            </div>
        </div>
        
        <!-- Main Content -->
        <div class="flex-grow-1" style="margin-left: 250px;">
            <!-- Header -->
            <div class="corporate-header d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="mb-0">@yield('title', 'Dashboard')</h5>
                </div>
                
                <div class="d-flex align-items-center">
                    <div class="dropdown me-3">
                        <a href="#" class="text-dark position-relative" id="notificationsDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-bell"></i>
                            @if(isset($pendingApprovalsCount) && $pendingApprovalsCount > 0)
                                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                    {{ $pendingApprovalsCount }}
                                </span>
                            @endif
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="notificationsDropdown">
                            <li><h6 class="dropdown-header">Notifications</h6></li>
                            @if(isset($pendingApprovalsCount) && $pendingApprovalsCount > 0)
                                <li>
                                    <a class="dropdown-item" href="{{ route('corporate.approvals.index') }}">
                                        <i class="fas fa-check-double me-2 text-primary"></i>
                                        {{ $pendingApprovalsCount }} pending approval(s)
                                    </a>
                                </li>
                            @else
                                <li><span class="dropdown-item">No new notifications</span></li>
                            @endif
                        </ul>
                    </div>
                    
                    <div class="dropdown">
                        <a href="#" class="d-flex align-items-center text-dark text-decoration-none dropdown-toggle" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                            <div class="me-2">
                                <div class="fw-bold">{{ Auth::user()->name }}</div>
                                <div class="small text-muted">{{ Auth::user()->company->name ?? 'Corporate User' }}</div>
                            </div>
                            <div class="rounded-circle bg-secondary text-white d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                            </div>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                            <li><h6 class="dropdown-header">{{ Auth::user()->name }}</h6></li>
                            <li><a class="dropdown-item" href="{{ route('corporate.settings.profile') }}"><i class="fas fa-user me-2"></i> Profile</a></li>
                            <li><a class="dropdown-item" href="{{ route('corporate.settings.security') }}"><i class="fas fa-lock me-2"></i> Security</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="dropdown-item"><i class="fas fa-sign-out-alt me-2"></i> Logout</button>
                                </form>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
            
            <!-- Content -->
            <div class="corporate-content">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif
                
                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif
                
                @if(session('warning'))
                    <div class="alert alert-warning alert-dismissible fade show" role="alert">
                        {{ session('warning') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif
                
                @yield('content')
            </div>
        </div>
    </div>
    
    <!-- Bootstrap JS -->
    <script src="{{ asset('assets/libs/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    
    <!-- jQuery -->
    <script src="{{ asset('assets/libs/jquery/jquery.min.js') }}"></script>
    
    <!-- Custom JS -->
    <script>
        // Mobile sidebar toggle
        document.addEventListener('DOMContentLoaded', function() {
            const toggleSidebar = document.querySelector('.toggle-sidebar');
            const sidebar = document.querySelector('.corporate-sidebar');
            
            if (toggleSidebar && sidebar) {
                toggleSidebar.addEventListener('click', function() {
                    sidebar.classList.toggle('show');
                });
            }
        });
    </script>
    
    @stack('scripts')
</body>
</html>
