<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title }}</title>
    <meta name="description" content="Secure payment processing services">

    <!-- Favicon -->
    <link rel="icon" href="{{ asset('assets/img/logo.png') }}">

    <!-- Tailwind CSS via CDN -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com" rel="preconnect">
    <link href="https://fonts.gstatic.com" rel="preconnect" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Poppins', sans-serif;
        }
        .hero-gradient {
            background: linear-gradient(135deg, #4f46e5 0%, #3b82f6 50%, #0ea5e9 100%);
        }
        .feature-card {
            transition: all 0.3s ease;
        }
        .feature-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }
        .payment-button {
            transition: all 0.3s ease;
            animation: pulse 2s infinite;
        }
        .payment-button:hover {
            animation: none;
            transform: scale(1.05);
        }
        @keyframes pulse {
            0% {
                box-shadow: 0 0 0 0 rgba(255, 255, 255, 0.7);
            }
            70% {
                box-shadow: 0 0 0 10px rgba(255, 255, 255, 0);
            }
            100% {
                box-shadow: 0 0 0 0 rgba(255, 255, 255, 0);
            }
        }
    </style>
</head>
<body class="bg-gray-50">
    <!-- Header -->
    <header class="bg-white shadow-sm">
        <div class="container mx-auto px-4 py-4 flex justify-between items-center">
            <a href="{{ url('/') }}" class="flex items-center">
                <img src="{{ asset('assets/img/logo.png') }}" alt="Logo" class="h-10">
            </a>
            <nav>
{{--                <a href="{{ url('/merchant/login') }}" class="text-gray-600 hover:text-gray-900 font-medium px-4 py-2 rounded-lg hover:bg-gray-100 transition duration-300">Login</a>--}}
                <a href="#" class="text-gray-600 hover:text-gray-900 font-medium px-4 py-2 rounded-lg hover:bg-gray-100 transition duration-300">Login</a>
            </nav>
        </div>
    </header>

    <!-- Hero Section -->
    <section class="py-24 hero-gradient text-white relative overflow-hidden">
        <!-- Background Elements -->
        <div class="absolute top-0 left-0 w-full h-full overflow-hidden opacity-10">
            <div class="absolute top-10 left-10 w-40 h-40 rounded-full bg-white"></div>
            <div class="absolute bottom-10 right-10 w-60 h-60 rounded-full bg-white"></div>
            <div class="absolute top-1/2 left-1/4 w-20 h-20 rounded-full bg-white"></div>
        </div>

        <div class="container mx-auto px-4 max-w-5xl relative z-10">
            <div class="text-center">
                <h1 class="text-5xl md:text-6xl font-bold mb-6 leading-tight">{{ $title }}</h1>
                <p class="text-xl md:text-2xl mb-8 font-light">{{ $subtitle }}</p>
                <p class="text-lg mb-12 opacity-90 max-w-3xl mx-auto">{{ $description }}</p>
                <a href="{{ url($buttonUrl) }}" class="payment-button inline-block bg-white text-indigo-600 font-semibold px-8 py-4 rounded-lg shadow-lg hover:bg-gray-100 transition duration-300 text-lg">
                    <i class="fas fa-credit-card mr-2"></i> {{ $buttonText }}
                </a>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="py-20 bg-white">
        <div class="container mx-auto px-4">
            <div class="text-center mb-16">
                <h2 class="text-3xl font-bold text-gray-800 mb-4">Why Choose Our Payment Solution?</h2>
                <p class="text-gray-600 max-w-2xl mx-auto">Experience the best in payment processing with our secure, reliable, and easy-to-use platform.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- Feature 1 -->
                <div class="feature-card bg-gray-50 rounded-xl p-8 text-center shadow-md">
                    <div class="inline-block p-4 bg-indigo-100 text-indigo-600 rounded-full mb-4">
                        <i class="fas fa-shield-alt text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-semibold mb-3 text-gray-800">Secure Transactions</h3>
                    <p class="text-gray-600">Your payments are protected with industry-leading security protocols and encryption.</p>
                </div>

                <!-- Feature 2 -->
                <div class="feature-card bg-gray-50 rounded-xl p-8 text-center shadow-md">
                    <div class="inline-block p-4 bg-blue-100 text-blue-600 rounded-full mb-4">
                        <i class="fas fa-bolt text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-semibold mb-3 text-gray-800">Fast Processing</h3>
                    <p class="text-gray-600">Experience quick payment processing with real-time transaction updates.</p>
                </div>

                <!-- Feature 3 -->
                <div class="feature-card bg-gray-50 rounded-xl p-8 text-center shadow-md">
                    <div class="inline-block p-4 bg-sky-100 text-sky-600 rounded-full mb-4">
                        <i class="fas fa-globe text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-semibold mb-3 text-gray-800">Multiple Payment Methods</h3>
                    <p class="text-gray-600">Accept payments via credit cards, mobile money, and other popular payment methods.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="py-16 bg-gray-100">
        <div class="container mx-auto px-4 text-center">
            <h2 class="text-3xl font-bold text-gray-800 mb-6">Ready to Get Started?</h2>
            <p class="text-gray-600 max-w-2xl mx-auto mb-8">Join thousands of businesses that trust our payment processing solution.</p>
            <a href="{{ url($buttonUrl) }}" class="inline-block bg-indigo-600 text-white font-semibold px-8 py-4 rounded-lg shadow-lg hover:bg-indigo-700 transition duration-300 text-lg">
                {{ $buttonText }} Now
            </a>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-gray-800 text-white py-12">
        <div class="container mx-auto px-4">
            <div class="flex flex-col md:flex-row justify-between items-center">
                <div class="mb-6 md:mb-0">
                    <img src="{{ asset('assets/img/logo.png') }}" alt="Logo" class="h-10 mb-4">
                    <p class="text-gray-400">Your trusted payment processing solution</p>
                </div>
                <div class="flex space-x-6">
                    <a href="#" class="text-gray-400 hover:text-white transition duration-300">
                        <i class="fab fa-facebook-f text-xl"></i>
                    </a>
                    <a href="#" class="text-gray-400 hover:text-white transition duration-300">
                        <i class="fab fa-twitter text-xl"></i>
                    </a>
                    <a href="#" class="text-gray-400 hover:text-white transition duration-300">
                        <i class="fab fa-instagram text-xl"></i>
                    </a>
                    <a href="#" class="text-gray-400 hover:text-white transition duration-300">
                        <i class="fab fa-linkedin-in text-xl"></i>
                    </a>
                </div>
            </div>
            <hr class="border-gray-700 my-8">
            <div class="text-center">
                <p>&copy; {{ date('Y') }} <strong>{{ config('app.name') }}</strong>. All Rights Reserved</p>
            </div>
        </div>
    </footer>
</body>
</html>
