<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', config('app.name'))</title>
    <meta name="description" content="@yield('meta_description', 'Card to Wallet Transfer System')">

    <!-- Favicon -->
    <link rel="icon" href="{{ asset('assets/img/logo.png') }}">

    <!-- Tailwind CSS via CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#007751', /* Zambian green */
                        secondary: '#CE1126', /* Zambian red */
                        accent: '#000000', /* Black */
                        success: '#28A745',
                        warning: '#FFC107',
                        error: '#DC3545',
                        light: '#F8F9FA',
                        dark: '#343A40',
                    },
                    boxShadow: {
                        'card': '0 10px 15px -3px rgba(0, 0, 0, 0.05), 0 4px 6px -2px rgba(0, 0, 0, 0.03)',
                        'button': '0 4px 6px -1px rgba(0, 119, 81, 0.1), 0 2px 4px -1px rgba(0, 119, 81, 0.06)',
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
        .auth-gradient {
            background: linear-gradient(135deg, #007751 0%, #005a3d 50%, #007751 100%);
        }
    </style>
    
    @stack('styles')
</head>
<body class="bg-light">
    <!-- Header -->
    <header class="bg-white shadow-sm">
        <div class="container mx-auto px-4 py-4 flex justify-between items-center max-w-4xl">
            <a href="{{ url('/') }}" class="flex items-center">
                <img src="{{ asset('assets/img/logo.png') }}" alt="Logo" class="h-10">
            </a>
            <nav class="flex items-center space-x-2">
                @yield('header_nav')
            </nav>
        </div>
    </header>

    <!-- Main Content -->
    <section class="py-12">
        <div class="container mx-auto px-4">
            <div class="max-w-4xl mx-auto">
                @yield('content')
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-white py-6 mt-12">
        <div class="container mx-auto px-4">
            <div class="text-center text-gray-500 text-sm">
                <p>&copy; {{ date('Y') }} {{ config('app.name') }}. All Rights Reserved</p>
                <div class="mt-2 space-x-4">
                    <a href="{{ url('/terms') }}" class="text-gray-500 hover:text-primary">Terms of Service</a>
                    <a href="{{ url('/privacy') }}" class="text-gray-500 hover:text-primary">Privacy Policy</a>
                    <a href="{{ url('/contact') }}" class="text-gray-500 hover:text-primary">Contact Us</a>
                </div>
            </div>
        </div>
    </footer>

    @stack('scripts')
</body>
</html>
