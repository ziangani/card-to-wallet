<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify Email - {{ config('app.name') }}</title>
    <meta name="description" content="Verify your email address to access your account">

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

        .auth-gradient {
            background: linear-gradient(135deg, #3366CC 0%, #4D7FD3 50%, #6699FF 100%);
        }
    </style>
</head>
<body class="bg-light">
<!-- Header -->
<header class="bg-white shadow-sm">
    <div class="container mx-auto px-4 py-4 flex justify-between items-center">
        <a href="{{ url('/') }}" class="flex items-center">
            <img src="{{ asset('assets/img/logo.png') }}" alt="Logo" class="h-10">
        </a>
        <nav class="flex items-center space-x-4">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit"
                        class="text-dark hover:text-primary font-medium px-4 py-2 rounded-lg hover:bg-gray-100 transition duration-300">
                    Logout
                </button>
            </form>
        </nav>
    </div>
</header>

<!-- Email Verification Section -->
<section class="py-12">
    <div class="container mx-auto px-4">
        <div class="max-w-md mx-auto">
            <div class="bg-white rounded-xl shadow-md overflow-hidden">
                <div class="auth-gradient text-white p-6 text-center">
                    <div
                        class="w-20 h-20 bg-white bg-opacity-20 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-envelope text-3xl"></i>
                    </div>
                    <h1 class="text-2xl font-bold mb-2">Verify Your Email Address</h1>
                    <p class="text-white text-opacity-90">We've sent a verification link to your email</p>
                </div>

                <div class="p-6">
                    @if(session('status') == 'verification-link-sent')
                        <div class="mb-4 p-4 bg-success bg-opacity-10 text-success rounded-lg">
                            A new verification link has been sent to the email address you provided during registration.
                        </div>
                    @endif

                    <div class="text-gray-600 mb-6">
                        <p class="mb-4">Thanks for signing up! Before getting started, could you verify your email
                            address by clicking on the link we just emailed to you?</p>
                        <p>If you didn't receive the email, we will gladly send you another.</p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <form method="POST" action="{{ route('verification.send') }}">
                            @csrf
                            <button type="submit"
                                    class="w-full bg-primary text-white py-3 px-4 rounded-lg hover:bg-opacity-90 transition duration-300 font-medium">
                                Resend Email
                            </button>
                        </form>

                        <form method="POST" action="{{ route('verification.check') }}">
                            @csrf
                            <button type="submit"
                                    class="w-full bg-secondary text-white py-3 px-4 rounded-lg hover:bg-opacity-90 transition duration-300 font-medium">
                                Already Verified
                            </button>
                        </form>
                    </div>

                    @if(session('error'))
                        <div class="mb-4 p-4 bg-error bg-opacity-10 text-error rounded-lg">
                            {{ session('error') }}
                        </div>
                    @endif

                    <div class="bg-gray-50 p-4 rounded-lg">
                        <div class="flex items-start">
                            <div class="flex-shrink-0">
                                <i class="fas fa-info-circle text-primary mt-1"></i>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-gray-700">Why verify your email?</h3>
                                <div class="mt-2 text-sm text-gray-600">
                                    <p>Email verification helps us:</p>
                                    <ul class="list-disc pl-5 mt-1 space-y-1">
                                        <li>Confirm your identity</li>
                                        <li>Protect your account from unauthorized access</li>
                                        <li>Send important notifications about your transactions</li>
                                        <li>Recover your account if you forget your password</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-8 text-center">
                <p class="text-gray-600 text-sm">
                    Need help? <a href="{{ url('/contact') }}" class="text-primary hover:underline">Contact Support</a>
                </p>
            </div>
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
</body>
</html>
