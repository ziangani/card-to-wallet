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
                        primary: '#007751', /* Zambian green */
                        secondary: '#CE1126', /* Zambian red */
                        accent: '#000000', /* Black */
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
            /*filter: grayscale(0.5);*/
            /*opacity: 0.9;*/
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

    <!-- Hero Section with Zambian Flag Colors -->
    <section class="py-32 bg-cover bg-center relative overflow-hidden" id="main-content" style="background-image: linear-gradient(rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0.6)), url('{{ asset('assets/img/victoria-falls.jpg') }}')">
        <div class="container mx-auto px-4 max-w-6xl relative z-10 text-center">
            <h1 class="text-4xl md:text-5xl font-bold mb-6 leading-tight text-white">Fund Your Zambian Mobile Wallet</h1>
            <p class="text-xl mb-8 font-light text-white max-w-2xl mx-auto">Instant transfers using your Visa/Mastercard to MTN, Airtel and Zamtel</p>
            <a href="{{ url($buttonUrl) }}" class="inline-block bg-orange-500 text-white font-semibold px-8 py-4 rounded-lg shadow-md hover:bg-orange-600 transition duration-300 text-lg payment-button">
                <i class="fas fa-credit-card mr-2"></i> Start Transfer
            </a>

            <div class="mt-12">
                <h3 class="text-white font-semibold mb-4">Supported Mobile Networks</h3>
                <div class="flex justify-center gap-6">
                    <img src="{{ asset('assets/img/mtn.jpg') }}" alt="MTN" class="h-12 bg-white p-2 rounded-lg">
                    <img src="{{ asset('assets/img/airtel.png') }}" alt="Airtel" class="h-12 bg-white p-2 rounded-lg">
                    <img src="{{ asset('assets/img/zamtel.jpg') }}" alt="Zamtel" class="h-12 bg-white p-2 rounded-lg">
                </div>
            </div>
        </div>
    </section>

    <!-- Networks & How It Works Section -->
    <section class="py-16 bg-white">
        <div class="container mx-auto px-4 max-w-6xl">
            <h2 class="text-3xl font-bold text-center mb-12 text-green-600">How It Works</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- Step 1 -->
                <div class="bg-gray-50 p-6 rounded-lg border-t-4 border-red-600">
                    <div class="text-red-600 text-2xl font-bold mb-3">1</div>
                    <h3 class="text-lg font-bold mb-2">Enter Details</h3>
                    <p class="text-gray-600">Mobile number and amount to transfer</p>
                </div>
                
                <!-- Step 2 -->
                <div class="bg-gray-50 p-6 rounded-lg border-t-4 border-black">
                    <div class="text-black text-2xl font-bold mb-3">2</div>
                    <h3 class="text-lg font-bold mb-2">Pay Securely</h3>
                    <p class="text-gray-600">Use your Visa/Mastercard</p>
                </div>
                
                <!-- Step 3 -->
                <div class="bg-gray-50 p-6 rounded-lg border-t-4 border-orange-500">
                    <div class="text-orange-500 text-2xl font-bold mb-3">3</div>
                    <h3 class="text-lg font-bold mb-2">Receive Funds</h3>
                    <p class="text-gray-600">Instantly in your mobile wallet</p>
                </div>
            </div>
            
        </div>
    </section>

    <!-- Key Benefits Section -->
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
    <!-- Key Benefits Section -->
    <section class="py-12 bg-gray-50">
        <div class="container mx-auto px-4 max-w-6xl">
            <div class="text-center mb-8">
                <h2 class="text-2xl font-bold text-dark mb-3">Why Choose Us</h2>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="bg-white p-6 rounded-lg shadow-sm border-l-4 border-green-600">
                    <h3 class="font-bold mb-2">Zambian Focused</h3>
                    <p class="text-gray-600 text-sm">Designed specifically for Zambian mobile money users</p>
                </div>
                <div class="bg-white p-6 rounded-lg shadow-sm border-l-4 border-red-600">
                    <h3 class="font-bold mb-2">Instant Transfers</h3>
                    <p class="text-gray-600 text-sm">Funds arrive in seconds, not hours</p>
                </div>
                <div class="bg-white p-6 rounded-lg shadow-sm border-l-4 border-orange-500">
                    <h3 class="font-bold mb-2">Secure Payments</h3>
                    <p class="text-gray-600 text-sm">Bank-level security for all transactions</p>
                </div>
            </div>
        </div>
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
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div>
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
