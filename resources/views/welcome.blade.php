<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title }}</title>
    <meta name="description" content="Fund your mobile wallet instantly with secure card payments">

    <!-- Favicon -->
    <link rel="icon" href="{{ asset('assets/img/logo.png') }}">

    <!-- Tailwind CSS via CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#5D5FEF', /* ChitChat purple */
                        secondary: '#4F46E5', /* Secondary purple */
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
            scroll-behavior: smooth;
        }
        .hero-gradient {
            background: linear-gradient(135deg, #5D5FEF 0%, #4F46E5 50%, #6366F1 100%);
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
        .step-card {
            position: relative;
        }
        .step-card::after {
            content: '';
            position: absolute;
            top: 2rem;
            right: -1rem;
            width: 2rem;
            height: 2px;
            background-color: #5D5FEF;
            display: none;
        }
        @media (min-width: 768px) {
            .step-card:not(:last-child)::after {
                display: block;
            }
        }
        .faq-item {
            border-bottom: 1px solid #e5e7eb;
            transition: all 0.3s ease;
        }
        .faq-item:hover {
            background-color: #f9fafb;
        }
        .mno-logo {
            transition: all 0.3s ease;
            filter: grayscale(0.5);
            opacity: 0.9;
        }
        .mno-logo:hover {
            filter: grayscale(0);
            opacity: 1;
            transform: scale(1.05);
        }
        .scroll-indicator {
            animation: bounce 2s infinite;
        }
        @keyframes bounce {
            0%, 20%, 50%, 80%, 100% {
                transform: translateY(0);
            }
            40% {
                transform: translateY(-10px);
            }
            60% {
                transform: translateY(-5px);
            }
        }
        .benefit-card {
            transition: all 0.3s ease;
            border-bottom: 3px solid transparent;
        }
        .benefit-card:hover {
            border-bottom: 3px solid #5D5FEF;
            transform: translateY(-3px);
        }
        .faq-toggle {
            transition: transform 0.3s ease;
        }
        .faq-toggle.active {
            transform: rotate(180deg);
        }
        .faq-answer {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.3s ease;
        }
        .faq-answer.active {
            max-height: 200px;
        }
        .skip-link {
            position: absolute;
            top: -40px;
            left: 0;
            background: #5D5FEF;
            color: white;
            padding: 8px;
            z-index: 100;
            transition: top 0.3s;
        }
        .skip-link:focus {
            top: 0;
        }
    </style>
</head>
<body class="bg-light">
    <!-- Accessibility Skip Link -->
    <a href="#main-content" class="skip-link">Skip to main content</a>

    <!-- Header -->
    <header class="bg-white shadow-sm sticky top-0 z-30">
        <div class="container mx-auto px-4 py-4 flex justify-between items-center max-w-6xl">
            <a href="{{ url('/') }}" class="flex items-center" aria-label="Home">
                <img src="{{ asset('assets/img/logo.png') }}" alt="Card to Wallet Logo" class="h-10">
            </a>
            <nav class="flex items-center space-x-2">
                <a href="{{ url('/login') }}" class="text-dark hover:text-primary font-medium px-4 py-2 rounded-lg hover:bg-gray-100 transition duration-300">Login</a>
                <a href="{{ url('/register') }}" class="bg-primary text-white font-medium px-4 py-2 rounded-lg hover:bg-opacity-90 transition duration-300">Register</a>
            </nav>
        </div>
    </header>

    <!-- Hero Section -->
    <section class="py-20 hero-gradient text-white relative overflow-hidden" id="main-content">
        <div class="container mx-auto px-4 max-w-6xl relative z-10">
            <div class="flex flex-col md:flex-row items-center justify-between">
                <div class="md:w-1/2 text-left mb-10 md:mb-0">
                    <h1 class="text-4xl md:text-5xl font-bold mb-6 leading-tight">{{ $title }}</h1>
                    <p class="text-xl mb-6 font-light">{{ $subtitle }}</p>
                    <p class="text-lg mb-8 opacity-90">{{ $description }}</p>
                    <a href="{{ url($buttonUrl) }}" class="inline-block bg-white text-primary font-semibold px-8 py-4 rounded-lg shadow-md hover:bg-gray-100 transition duration-300 text-lg payment-button">
                        <i class="fas fa-credit-card mr-2"></i> {{ $buttonText }}
                    </a>
                </div>
                <div class="md:w-1/2 flex justify-center">
                    <img src="{{ asset('assets/img/woman-with-phone.png') }}" alt="Person using mobile wallet" class="max-w-full h-auto rounded-lg" onerror="this.src='https://via.placeholder.com/500x400?text=Mobile+Wallet';this.onerror='';">
                </div>
            </div>

            <!-- Scroll indicator -->
            <div class="absolute bottom-5 left-1/2 transform -translate-x-1/2 text-white text-center scroll-indicator">
                <p class="text-sm mb-2">Scroll to learn more</p>
                <i class="fas fa-chevron-down"></i>
            </div>
        </div>
    </section>

    <!-- Supported Mobile Networks Section -->
    <section class="py-8 bg-white">
        <div class="container mx-auto px-4 max-w-6xl">
            <div class="text-center mb-6">
                <h2 class="text-2xl font-bold text-dark mb-2">Supported Mobile Networks</h2>
                <p class="text-gray-600 max-w-2xl mx-auto">Send money instantly to any of these mobile wallet providers</p>
            </div>

            <div class="flex flex-wrap justify-center items-center gap-6 md:gap-12">
                <!-- MTN -->
                <div class="text-center">
                    <div class="w-20 h-20 mx-auto mb-2 p-2 bg-white rounded-full shadow-sm hover:shadow-md transition-all">
                        <img src="{{ asset('assets/img/mtn.jpg') }}" alt="MTN Mobile Money" class="w-full h-full object-contain rounded-full mno-logo">
                    </div>
                    <p class="font-medium text-gray-800">MTN Mobile Money</p>
                </div>

                <!-- Airtel -->
                <div class="text-center">
                    <div class="w-20 h-20 mx-auto mb-2 p-2 bg-white rounded-full shadow-sm hover:shadow-md transition-all">
                        <img src="{{ asset('assets/img/airtel.png') }}" alt="Airtel Money" class="w-full h-full object-contain rounded-full mno-logo">
                    </div>
                    <p class="font-medium text-gray-800">Airtel Money</p>
                </div>

                <!-- Zamtel -->
                <div class="text-center">
                    <div class="w-20 h-20 mx-auto mb-2 p-2 bg-white rounded-full shadow-sm hover:shadow-md transition-all">
                        <img src="{{ asset('assets/img/zamtel.jpg') }}" alt="Zamtel Kwacha" class="w-full h-full object-contain rounded-full mno-logo">
                    </div>
                    <p class="font-medium text-gray-800">Zamtel Kwacha</p>
                </div>
            </div>

            <!-- Trust indicators -->
            <div class="mt-8 text-center">
                <div class="inline-flex items-center justify-center bg-gray-100 rounded-full px-4 py-2 text-sm text-gray-700">
                    <i class="fas fa-shield-alt text-primary mr-2"></i>
                    <span>Secure & Regulated Financial Service</span>
                </div>
            </div>
        </div>
    </section>

    <!-- How It Works Section -->
    <section class="py-12 bg-light" id="how-it-works">
        <div class="container mx-auto px-4 max-w-6xl">
            <div class="text-center mb-8">
                <h2 class="text-2xl font-bold text-dark mb-3">How It Works</h2>
                <p class="text-gray-600 max-w-2xl mx-auto">Fund your mobile wallet in three simple steps</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Step 1 -->
                <div class="bg-white rounded-lg p-5 text-center shadow-sm hover:shadow-md transition-shadow duration-300 step-card relative">
                    <div class="absolute -top-3 -left-3 w-8 h-8 rounded-full bg-primary text-white flex items-center justify-center font-bold text-base">1</div>
                    <div class="inline-block p-3 bg-indigo-100 text-primary rounded-full mb-3">
                        <i class="fas fa-mobile-alt text-xl"></i>
                    </div>
                    <h3 class="text-base font-semibold mb-2 text-dark">Enter Mobile Details</h3>
                    <p class="text-gray-600 text-sm">Enter your mobile number and the amount you want to transfer.</p>
                </div>

                <!-- Step 2 -->
                <div class="bg-white rounded-lg p-5 text-center shadow-sm hover:shadow-md transition-shadow duration-300 step-card relative">
                    <div class="absolute -top-3 -left-3 w-8 h-8 rounded-full bg-primary text-white flex items-center justify-center font-bold text-base">2</div>
                    <div class="inline-block p-3 bg-indigo-100 text-primary rounded-full mb-3">
                        <i class="fas fa-credit-card text-xl"></i>
                    </div>
                    <h3 class="text-base font-semibold mb-2 text-dark">Make Secure Payment</h3>
                    <p class="text-gray-600 text-sm">Complete your payment using your Visa or Mastercard securely.</p>
                </div>

                <!-- Step 3 -->
                <div class="bg-white rounded-lg p-5 text-center shadow-sm hover:shadow-md transition-shadow duration-300 step-card relative">
                    <div class="absolute -top-3 -left-3 w-8 h-8 rounded-full bg-primary text-white flex items-center justify-center font-bold text-base">3</div>
                    <div class="inline-block p-3 bg-indigo-100 text-primary rounded-full mb-3">
                        <i class="fas fa-check-circle text-xl"></i>
                    </div>
                    <h3 class="text-base font-semibold mb-2 text-dark">Receive Funds Instantly</h3>
                    <p class="text-gray-600 text-sm">Your mobile wallet is funded instantly with confirmation.</p>
                </div>
            </div>

            <!-- CTA Button -->
            <div class="text-center mt-8">
                <a href="{{ url($buttonUrl) }}" class="inline-block bg-primary text-white font-medium px-5 py-2 rounded-lg shadow-sm hover:bg-opacity-90 transition duration-300 text-sm">
                    <i class="fas fa-wallet mr-2"></i> Start a Transaction
                </a>
            </div>
        </div>
    </section>

    <!-- Fee Structure Section -->
    <section class="py-10 bg-white">
        <div class="container mx-auto px-4 max-w-6xl">
            <div class="max-w-3xl mx-auto bg-white rounded-lg shadow-sm overflow-hidden border border-gray-100">
                <div class="md:flex">
                    <div class="md:flex-shrink-0 bg-primary flex items-center justify-center md:w-40 p-4">
                        <div class="text-center">
                            <div class="text-white text-3xl font-bold mb-1">4%</div>
                            <div class="text-white text-sm">Total Fee</div>
                        </div>
                    </div>
                    <div class="p-6">
                        <div class="text-xl font-semibold text-dark mb-3">Transparent Fee Structure</div>
                        <p class="text-gray-600 mb-4 text-sm">We charge a simple, transparent fee of 4% on all transactions. No hidden charges.</p>

                        <div class="grid grid-cols-2 gap-4 mt-4">
                            <div class="border rounded-lg p-3 hover:shadow-sm transition-all">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 rounded-full bg-indigo-100 flex items-center justify-center mr-3">
                                        <i class="fas fa-university text-primary"></i>
                                    </div>
                                    <div>
                                        <div class="font-semibold text-sm">3% Bank Fee</div>
                                        <div class="text-xs text-gray-500">Processing & card charges</div>
                                    </div>
                                </div>
                            </div>
                            <div class="border rounded-lg p-3 hover:shadow-sm transition-all">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 rounded-full bg-indigo-100 flex items-center justify-center mr-3">
                                        <i class="fas fa-cog text-primary"></i>
                                    </div>
                                    <div>
                                        <div class="font-semibold text-sm">1% Platform Fee</div>
                                        <div class="text-xs text-gray-500">Service & maintenance</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Example calculation -->
                        <div class="mt-4 bg-gray-50 p-3 rounded-lg">
                            <p class="font-medium text-gray-700 mb-1 text-sm">Example Transaction:</p>
                            <div class="grid grid-cols-2 gap-2 text-xs">
                                <div>Send amount: <span class="font-medium">K500.00</span></div>
                                <div>Fee (4%): <span class="font-medium">K20.00</span></div>
                                <div>Total charge: <span class="font-medium">K520.00</span></div>
                                <div>Recipient gets: <span class="font-medium">K500.00</span></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- Benefits Section -->
    <section class="py-10 bg-light">
        <div class="container mx-auto px-4 max-w-6xl">
            <div class="text-center mb-8">
                <h2 class="text-2xl font-bold text-dark mb-3">Why Choose Our Card-to-Wallet Service?</h2>
                <p class="text-gray-600 max-w-2xl mx-auto">Experience the best way to fund your mobile wallet</p>
            </div>

            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                <!-- Benefit 1 -->
                <div class="bg-white rounded-lg p-4 text-center shadow-sm hover:shadow-md transition-shadow duration-300 benefit-card">
                    <div class="inline-block p-3 bg-indigo-100 text-primary rounded-full mb-3">
                        <i class="fas fa-shield-alt text-lg"></i>
                    </div>
                    <h3 class="text-base font-semibold mb-2 text-dark">Security</h3>
                    <p class="text-gray-600 text-xs">Your card details are never stored on our platform.</p>
                </div>

                <!-- Benefit 2 -->
                <div class="bg-white rounded-lg p-4 text-center shadow-sm hover:shadow-md transition-shadow duration-300 benefit-card">
                    <div class="inline-block p-3 bg-indigo-100 text-primary rounded-full mb-3">
                        <i class="fas fa-bolt text-lg"></i>
                    </div>
                    <h3 class="text-base font-semibold mb-2 text-dark">Speed</h3>
                    <p class="text-gray-600 text-xs">Instant transfers with real-time confirmation.</p>
                </div>

                <!-- Benefit 3 -->
                <div class="bg-white rounded-lg p-4 text-center shadow-sm hover:shadow-md transition-shadow duration-300 benefit-card">
                    <div class="inline-block p-3 bg-indigo-100 text-primary rounded-full mb-3">
                        <i class="fas fa-mobile-alt text-lg"></i>
                    </div>
                    <h3 class="text-base font-semibold mb-2 text-dark">Convenience</h3>
                    <p class="text-gray-600 text-xs">Fund your wallet anytime, anywhere.</p>
                </div>

                <!-- Benefit 4 -->
                <div class="bg-white rounded-lg p-4 text-center shadow-sm hover:shadow-md transition-shadow duration-300 benefit-card">
                    <div class="inline-block p-3 bg-indigo-100 text-primary rounded-full mb-3">
                        <i class="fas fa-sync-alt text-lg"></i>
                    </div>
                    <h3 class="text-base font-semibold mb-2 text-dark">Multiple Wallets</h3>
                    <p class="text-gray-600 text-xs">Support for all major mobile money providers.</p>
                </div>
            </div>
        </div>
    </section>
    </section>

    <!-- Payment Methods Section -->
    <section class="py-8 bg-white">
        <div class="container mx-auto px-4 max-w-6xl">
            <div class="text-center mb-6">
                <h2 class="text-2xl font-bold text-dark mb-2">Accepted Payment Methods</h2>
                <p class="text-gray-600 max-w-2xl mx-auto">We accept all major credit and debit cards</p>
            </div>

            <div class="flex justify-center items-center space-x-6">
                <img src="{{ asset('assets/img/visa.png') }}" alt="Visa" class="h-10 object-contain">
                <img src="{{ asset('assets/img/mastercard.png') }}" alt="Mastercard" class="h-10 object-contain">
            </div>
        </div>
    </section>

    <!-- FAQ Preview Section -->
    <section class="py-12 bg-light" id="faq">
        <div class="container mx-auto px-4 max-w-6xl">
            <div class="text-center mb-8">
                <h2 class="text-2xl font-bold text-dark mb-3">Frequently Asked Questions</h2>
                <p class="text-gray-600 max-w-2xl mx-auto">Find quick answers to common questions</p>
            </div>

            <div class="max-w-2xl mx-auto">
                <!-- FAQ Item 1 -->
                <div class="bg-white rounded-lg shadow-sm mb-3 overflow-hidden">
                    <div class="p-4 cursor-pointer hover:bg-gray-50 transition-colors duration-200 faq-header">
                        <div class="flex justify-between items-center">
                            <h3 class="text-base font-semibold text-dark">How long does it take for funds to reflect?</h3>
                            <i class="fas fa-chevron-down text-primary text-sm faq-toggle"></i>
                        </div>
                        <div class="mt-2 text-gray-600 text-sm faq-answer">
                            Funds are typically reflected instantly once the card payment is successful. In rare cases, it might take up to 5 minutes.
                        </div>
                    </div>
                </div>

                <!-- FAQ Item 2 -->
                <div class="bg-white rounded-lg shadow-sm mb-3 overflow-hidden">
                    <div class="p-4 cursor-pointer hover:bg-gray-50 transition-colors duration-200 faq-header">
                        <div class="flex justify-between items-center">
                            <h3 class="text-base font-semibold text-dark">What cards are accepted for payment?</h3>
                            <i class="fas fa-chevron-down text-primary text-sm faq-toggle"></i>
                        </div>
                        <div class="mt-2 text-gray-600 text-sm faq-answer">
                            We accept all Visa and Mastercard debit and credit cards issued by any bank. Both local and international cards are supported.
                        </div>
                    </div>
                </div>

                <div class="text-center mt-6">
                    <a href="#" class="inline-flex items-center text-primary hover:underline font-medium text-sm">
                        View all FAQs <i class="fas fa-arrow-right ml-2"></i>
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="py-10 bg-primary text-white">
        <div class="container mx-auto px-4 text-center max-w-6xl">
            <h2 class="text-2xl font-bold mb-4">Ready to Get Started?</h2>
            <p class="max-w-xl mx-auto mb-6 opacity-90 text-sm">Create your account now and start funding your mobile wallet instantly.</p>
            <div class="flex flex-col sm:flex-row justify-center space-y-3 sm:space-y-0 sm:space-x-4">
                <a href="{{ url($buttonUrl) }}" class="inline-block bg-white text-primary font-medium px-6 py-3 rounded-lg shadow-sm hover:bg-gray-100 transition duration-300">
                    <i class="fas fa-credit-card mr-2"></i> {{ $buttonText }} Now
                </a>
                <a href="#how-it-works" class="inline-block bg-transparent border border-white text-white font-medium px-6 py-3 rounded-lg hover:bg-white hover:text-primary transition duration-300">
                    <i class="fas fa-info-circle mr-2"></i> Learn More
                </a>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-gray-900 text-white py-12">
        <div class="container mx-auto px-4 max-w-6xl">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="col-span-1">
                    <img src="{{ asset('assets/img/logo.png') }}" alt="Card to Wallet Logo" class="h-10 mb-4">
                    <p class="text-gray-400 text-sm mb-4">Your secure bridge between bank cards and mobile wallets in Zambia.</p>
                    <div class="flex space-x-4 mt-4">
                        <a href="#" class="text-gray-400 hover:text-primary transition duration-300" aria-label="Facebook">
                            <i class="fab fa-facebook-f text-lg"></i>
                        </a>
                        <a href="#" class="text-gray-400 hover:text-primary transition duration-300" aria-label="Twitter">
                            <i class="fab fa-twitter text-lg"></i>
                        </a>
                        <a href="#" class="text-gray-400 hover:text-primary transition duration-300" aria-label="Instagram">
                            <i class="fab fa-instagram text-lg"></i>
                        </a>
                    </div>
                </div>

                <div>
                    <h3 class="text-lg font-semibold mb-4">Quick Links</h3>
                    <div class="grid grid-cols-2 gap-4">
                        <ul class="space-y-2">
                            <li><a href="#" class="text-gray-400 hover:text-primary transition duration-300">Home</a></li>
                            <li><a href="#" class="text-gray-400 hover:text-primary transition duration-300">About Us</a></li>
                            <li><a href="#how-it-works" class="text-gray-400 hover:text-primary transition duration-300">How It Works</a></li>
                        </ul>
                        <ul class="space-y-2">
                            <li><a href="#faq" class="text-gray-400 hover:text-primary transition duration-300">FAQs</a></li>
                            <li><a href="#" class="text-gray-400 hover:text-primary transition duration-300">Terms of Service</a></li>
                            <li><a href="#" class="text-gray-400 hover:text-primary transition duration-300">Privacy Policy</a></li>
                        </ul>
                    </div>
                </div>

                <div>
                    <h3 class="text-lg font-semibold mb-4">Contact</h3>
                    <ul class="space-y-3">
                        <li class="flex items-start">
                            <i class="fas fa-map-marker-alt mt-1 mr-3 text-gray-400"></i>
                            <span class="text-gray-400">123 Cairo Road, Lusaka, Zambia</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-phone-alt mt-1 mr-3 text-gray-400"></i>
                            <span class="text-gray-400">+260 97 1234567</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-envelope mt-1 mr-3 text-gray-400"></i>
                            <span class="text-gray-400">support@cardtowallet.com</span>
                        </li>
                    </ul>
                </div>
            </div>
            </div>

            <hr class="border-gray-800 my-8">

            <div class="flex flex-col md:flex-row justify-between items-center">
                <p class="text-gray-400 text-sm">&copy; {{ date('Y') }} <strong>{{ config('app.name') }}</strong>. All Rights Reserved</p>
                <div class="mt-4 md:mt-0 flex items-center space-x-4">
                    <img src="{{ asset('assets/img/visa-mastercard.png') }}" alt="Payment Methods" class="h-8">
                </div>
            </div>
        </div>
    </footer>

    <script>
        // FAQ toggle functionality
        document.addEventListener('DOMContentLoaded', function() {
            const faqHeaders = document.querySelectorAll('.faq-header');

            faqHeaders.forEach(header => {
                header.addEventListener('click', function() {
                    const answer = this.querySelector('.faq-answer');
                    const icon = this.querySelector('.faq-toggle');

                    // Toggle active class
                    answer.classList.toggle('active');
                    icon.classList.toggle('active');

                    // Close other FAQs
                    faqHeaders.forEach(otherHeader => {
                        if (otherHeader !== header) {
                            const otherAnswer = otherHeader.querySelector('.faq-answer');
                            const otherIcon = otherHeader.querySelector('.faq-toggle');

                            otherAnswer.classList.remove('active');
                            otherIcon.classList.remove('active');
                        }
                    });
                });
            });
        });
    </script>
</body>
</html>
                    <h3 class="text-lg font-semibold mb-4">Contact</h3>
                    <ul class="space-y-3">
                        <li class="flex items-start">
                            <i class="fas fa-map-marker-alt mt-1 mr-3 text-gray-400"></i>
                            <span class="text-gray-400">123 Cairo Road, Lusaka, Zambia</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-phone-alt mt-1 mr-3 text-gray-400"></i>
                            <span class="text-gray-400">+260 97 1234567</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-envelope mt-1 mr-3 text-gray-400"></i>
                            <span class="text-gray-400">support@cardtowallet.com</span>
                        </li>
                    </ul>
