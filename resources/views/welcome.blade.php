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
                    },
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
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
            scroll-behavior: smooth;
        }
        
        /* Enhanced Hero Section */
        .hero-section {
            background-image: linear-gradient(rgba(0, 0, 0, 0.65), rgba(0, 0, 0, 0.65)), url('{{ asset('assets/img/victoria-falls.jpg') }}');
            background-size: cover;
            background-position: center;
            position: relative;
        }
        
        .hero-section::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            height: 100px;
            background: linear-gradient(to top, rgba(248, 249, 250, 1), rgba(248, 249, 250, 0));
            pointer-events: none;
        }
        
        /* Animated Elements */
        .fade-in {
            animation: fadeIn 0.8s ease-in-out;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        /* Enhanced Cards */
        .feature-card {
            transition: all 0.3s ease;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
        }
        
        .feature-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 20px rgba(0, 0, 0, 0.1);
        }
        
        /* Enhanced CTA Button */
        .cta-button {
            position: relative;
            overflow: hidden;
            transition: all 0.3s ease;
            box-shadow: 0 4px 12px rgba(255, 153, 0, 0.3);
        }
        
        .cta-button::after {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, rgba(255,255,255,0) 0%, rgba(255,255,255,0.2) 50%, rgba(255,255,255,0) 100%);
            transition: all 0.6s ease;
        }
        
        .cta-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(255, 153, 0, 0.4);
        }
        
        .cta-button:hover::after {
            left: 100%;
        }
        
        /* Enhanced Steps */
        .step-card {
            position: relative;
            transition: all 0.3s ease;
        }
        
        .step-card:hover {
            transform: translateY(-5px);
        }
        
        .step-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            border-radius: 4px 4px 0 0;
        }
        
        .step-card.step-1::before {
            background-color: #CE1126; /* Zambian red */
        }
        
        .step-card.step-2::before {
            background-color: #000000; /* Black */
        }
        
        .step-card.step-3::before {
            background-color: #FF9500; /* Orange */
        }
        
        .step-number {
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            font-weight: bold;
            font-size: 18px;
            margin-bottom: 16px;
        }
        
        /* Enhanced Fee Card */
        .fee-card {
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.05);
            transition: all 0.3s ease;
        }
        
        .fee-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1);
        }
        
        /* Enhanced Benefits */
        .benefit-card {
            transition: all 0.3s ease;
            border-left-width: 4px;
        }
        
        .benefit-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 15px rgba(0, 0, 0, 0.05);
        }
        
        /* Enhanced FAQ */
        .faq-item {
            border-radius: 12px;
            overflow: hidden;
            transition: all 0.3s ease;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
        }
        
        .faq-item:hover {
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
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
        
        /* Enhanced Network Logos */
        .network-logo {
            transition: all 0.3s ease;
            filter: grayscale(0);
            opacity: 1;
        }
        
        .network-logo:hover {
            transform: scale(1.05);
        }
        
        /* Accessibility */
        .skip-link {
            position: absolute;
            top: -40px;
            left: 0;
            background: #007751;
            color: white;
            padding: 8px;
            z-index: 100;
            transition: top 0.3s;
        }
        
        .skip-link:focus {
            top: 0;
        }
        
        /* Scroll Indicator */
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
    </style>
</head>
<body class="bg-light">
    <!-- Accessibility Skip Link -->
    <a href="#main-content" class="skip-link">Skip to main content</a>

    <!-- Enhanced Header -->
    <header class="bg-white shadow-sm sticky top-0 z-30">
        <div class="container mx-auto px-4 py-3 flex justify-between items-center max-w-6xl">
            <a href="{{ url('/') }}" class="flex items-center" aria-label="Home">
                <img src="{{ asset('assets/img/logo.png') }}" alt="Card to Wallet Logo" class="h-10">
            </a>
            <nav class="flex items-center space-x-6">
                <a href="#how-it-works" class="text-dark hover:text-primary font-medium px-3 py-2 rounded-lg hover:bg-gray-100 transition duration-300">How It Works</a>
                <a href="#fee-structure" class="text-dark hover:text-primary font-medium px-3 py-2 rounded-lg hover:bg-gray-100 transition duration-300">Pricing</a>
                <a href="#faq" class="text-dark hover:text-primary font-medium px-3 py-2 rounded-lg hover:bg-gray-100 transition duration-300">FAQ</a>
                <a href="{{ url('/login') }}" class="text-dark hover:text-primary font-medium px-4 py-2 rounded-lg hover:bg-gray-100 transition duration-300">Login</a>
                <a href="{{ url('/register') }}" class="bg-primary text-white font-medium px-5 py-2 rounded-lg hover:bg-opacity-90 transition duration-300 shadow-button">Register</a>
            </nav>
        </div>
    </header>

    <!-- Enhanced Hero Section -->
    <section class="hero-section py-28 md:py-36 relative overflow-hidden" id="main-content">
        <div class="container mx-auto px-4 max-w-6xl relative z-10 text-center">
            <h1 class="text-4xl md:text-5xl lg:text-6xl font-bold mb-6 leading-tight text-white fade-in">Fund Mobile Money Wallets Instantly with Your Card</h1>
            <p class="text-xl md:text-2xl mb-8 font-light text-white max-w-2xl mx-auto fade-in" style="animation-delay: 0.2s">Instantly fund any Zambian mobile money wallet using your Visa or Mastercard</p>
            <a href="{{ url($buttonUrl) }}" class="inline-block bg-orange-500 text-white font-semibold px-8 py-4 rounded-lg shadow-lg hover:bg-orange-600 transition duration-300 text-lg cta-button fade-in" style="animation-delay: 0.4s">
                <i class="fas fa-credit-card mr-2"></i> Fund Mobile Wallet Now
            </a>

            <div class="mt-16 fade-in" style="animation-delay: 0.6s">
                <h3 class="text-white font-semibold mb-5 text-lg">Fund Any Zambian Network</h3>
                <p class="text-white opacity-90 mb-6">Supported mobile money platforms:</p>
                <div class="flex justify-center gap-8">
                    <div class="bg-white p-3 rounded-xl shadow-lg transform transition hover:scale-105">
                        <img src="{{ asset('assets/img/mtn.jpg') }}" alt="MTN" class="h-14 rounded-lg network-logo">
                    </div>
                    <div class="bg-white p-3 rounded-xl shadow-lg transform transition hover:scale-105">
                        <img src="{{ asset('assets/img/airtel.png') }}" alt="Airtel" class="h-14 rounded-lg network-logo">
                    </div>
                    <div class="bg-white p-3 rounded-xl shadow-lg transform transition hover:scale-105">
                        <img src="{{ asset('assets/img/zamtel.jpg') }}" alt="Zamtel" class="h-14 rounded-lg network-logo">
                    </div>
                </div>
            </div>
            
            <!-- Scroll Indicator -->
            {{-- <div class="absolute bottom-8 left-1/2 transform -translate-x-1/2 text-white scroll-indicator">
                <a href="#how-it-works" class="flex flex-col items-center">
                    <span class="text-sm mb-2">Learn More</span>
                    <i class="fas fa-chevron-down"></i>
                </a>
            </div> --}}
        </div>
    </section>

    <!-- Enhanced How It Works Section -->
    <section class="py-20 bg-white" id="how-it-works">
        <div class="container mx-auto px-4 max-w-6xl">
            <div class="text-center mb-16">
                <span class="inline-block px-4 py-1 bg-green-100 text-green-800 rounded-full text-sm font-medium mb-3">Simple Process</span>
                <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">How It Works</h2>
                <p class="text-gray-600 max-w-2xl mx-auto">Complete your transfer in three simple steps</p>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- Step 1 -->
                <div class="bg-white p-8 rounded-xl shadow-card step-card step-1 hover:shadow-lg">
                    <div class="step-number bg-red-100 text-red-600">1</div>
                    <h3 class="text-xl font-bold mb-3">Enter Details</h3>
                    <p class="text-gray-600">Enter the mobile number and amount you want to transfer to the recipient's wallet</p>
                    <div class="mt-6">
                        <img src="{{ asset('assets/img/undraw_mobile_pay_re_sjb8.svg') }}" alt="Enter Details Illustration" class="h-32 mx-auto">
                    </div>
                </div>
                
                <!-- Step 2 -->
                <div class="bg-white p-8 rounded-xl shadow-card step-card step-2 hover:shadow-lg">
                    <div class="step-number bg-gray-100 text-gray-800">2</div>
                    <h3 class="text-xl font-bold mb-3">Pay Securely</h3>
                    <p class="text-gray-600">Use your Visa or Mastercard to securely complete the payment process</p>
                    <div class="mt-6">
                        <img src="{{ asset('assets/img/undraw_credit_card_re_blml.svg') }}" alt="Pay Securely Illustration" class="h-32 mx-auto">
                    </div>
                </div>
                
                <!-- Step 3 -->
                <div class="bg-white p-8 rounded-xl shadow-card step-card step-3 hover:shadow-lg">
                    <div class="step-number bg-orange-100 text-orange-600">3</div>
                    <h3 class="text-xl font-bold mb-3">Receive Funds</h3>
                    <p class="text-gray-600">Funds are instantly credited to the recipient's mobile wallet</p>
                    <div class="mt-6">
                        <img src="{{ asset('assets/img/undraw_receipt_re_fre3.svg') }}" alt="Receive Funds Illustration" class="h-32 mx-auto">
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Enhanced Fee Structure Section -->
    <section class="py-16 bg-gray-50" id="fee-structure">
        <div class="container mx-auto px-4 max-w-6xl">
            <div class="text-center mb-12">
                <span class="inline-block px-4 py-1 bg-blue-100 text-blue-800 rounded-full text-sm font-medium mb-3">Transparent Pricing</span>
                <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">Fee Structure</h2>
                <p class="text-gray-600 max-w-2xl mx-auto">We believe in complete transparency with our fees</p>
            </div>
            
            <div class="fee-card bg-white overflow-hidden max-w-4xl mx-auto">
                <div class="md:flex">
                    <div class="md:flex-shrink-0 bg-primary flex items-center justify-center md:w-48 p-8">
                        <div class="text-center">
                            <div class="text-white text-5xl font-bold mb-2">{{ \App\Models\Transaction::getFeeDescription() }}</div>
                            <div class="text-white text-lg font-medium">Total Fee</div>
                        </div>
                    </div>
                    <div class="p-8">
                        <div class="text-2xl font-semibold text-gray-900 mb-4">Transparent Fee Structure</div>
                        <p class="text-gray-600 mb-6">We charge a simple, transparent fee of {{ \App\Models\Transaction::getFeeDescription() }} on all transactions. No hidden charges or surprise fees.</p>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                            <div class="bg-white rounded-xl p-5 border border-gray-200 hover:shadow-md transition-all">
                                <div class="flex items-center">
                                    <div class="w-12 h-12 rounded-full bg-indigo-100 flex items-center justify-center mr-4">
                                        <i class="fas fa-university text-primary text-xl"></i>
                                    </div>
                                    <div>
                                        <div class="font-semibold text-lg">3% Bank Fee</div>
                                        <div class="text-sm text-gray-500">Processing & card charges</div>
                                    </div>
                                </div>
                            </div>
                            <div class="bg-white rounded-xl p-5 border border-gray-200 hover:shadow-md transition-all">
                                <div class="flex items-center">
                                    <div class="w-12 h-12 rounded-full bg-indigo-100 flex items-center justify-center mr-4">
                                        <i class="fas fa-cog text-primary text-xl"></i>
                                    </div>
                                    <div>
                                        <div class="font-semibold text-lg">1% Platform Fee</div>
                                        <div class="text-sm text-gray-500">Service & maintenance</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Example calculation -->
                        <div class="bg-gray-50 p-6 rounded-xl">
                            <p class="font-medium text-gray-900 mb-3 text-lg">Example Transaction:</p>
                            <div class="grid grid-cols-2 gap-4">
                                <div class="bg-white p-3 rounded-lg border border-gray-100">
                                    <span class="text-sm text-gray-500">Send amount:</span>
                                    <div class="font-bold text-xl text-gray-900">K500.00</div>
                                </div>
                                <div class="bg-white p-3 rounded-lg border border-gray-100">
                                    <span class="text-sm text-gray-500">Fee ({{ \App\Models\Transaction::getFeeDescription() }}):</span>
                                    <div class="font-bold text-xl text-gray-900">K27.50</div>
                                </div>
                                <div class="bg-white p-3 rounded-lg border border-gray-100">
                                    <span class="text-sm text-gray-500">Total charge:</span>
                                    <div class="font-bold text-xl text-gray-900">K527.50</div>
                                </div>
                                <div class="bg-white p-3 rounded-lg border border-gray-100">
                                    <span class="text-sm text-gray-500">Recipient gets:</span>
                                    <div class="font-bold text-xl text-primary">K500.00</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Enhanced Benefits Section -->
    <section class="py-16 bg-white">
        <div class="container mx-auto px-4 max-w-6xl">
            <div class="text-center mb-12">
                <span class="inline-block px-4 py-1 bg-green-100 text-green-800 rounded-full text-sm font-medium mb-3">Why Choose Us</span>
                <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">Our Advantages</h2>
                <p class="text-gray-600 max-w-2xl mx-auto">Experience the best service for your money transfers</p>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="bg-white p-6 rounded-xl shadow-card benefit-card border-l-4 border-green-600 hover:shadow-lg">
                    <div class="w-14 h-14 rounded-full bg-green-100 flex items-center justify-center mb-4">
                        <i class="fas fa-map-marker-alt text-green-600 text-xl"></i>
                    </div>
                    <h3 class="text-xl font-bold mb-3">Zambian Focused</h3>
                    <p class="text-gray-600">Designed specifically for Zambian mobile money users with local support and understanding</p>
                </div>
                
                <div class="bg-white p-6 rounded-xl shadow-card benefit-card border-l-4 border-red-600 hover:shadow-lg">
                    <div class="w-14 h-14 rounded-full bg-red-100 flex items-center justify-center mb-4">
                        <i class="fas fa-bolt text-red-600 text-xl"></i>
                    </div>
                    <h3 class="text-xl font-bold mb-3">Instant Transfers</h3>
                    <p class="text-gray-600">Funds arrive in seconds, not hours, ensuring your recipients get money when they need it</p>
                </div>
                
                <div class="bg-white p-6 rounded-xl shadow-card benefit-card border-l-4 border-orange-500 hover:shadow-lg">
                    <div class="w-14 h-14 rounded-full bg-orange-100 flex items-center justify-center mb-4">
                        <i class="fas fa-shield-alt text-orange-600 text-xl"></i>
                    </div>
                    <h3 class="text-xl font-bold mb-3">Secure Payments</h3>
                    <p class="text-gray-600">Bank-level security for all transactions with advanced encryption and fraud protection</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Enhanced Payment Methods Section -->
    <section class="py-16 bg-gray-50">
        <div class="container mx-auto px-4 max-w-6xl">
            <div class="text-center mb-10">
                <span class="inline-block px-4 py-1 bg-blue-100 text-blue-800 rounded-full text-sm font-medium mb-3">Payment Options</span>
                <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">Accepted Payment Methods</h2>
                <p class="text-gray-600 max-w-2xl mx-auto">We accept all major credit and debit cards for your convenience</p>
            </div>

            <div class="flex justify-center items-center space-x-8">
                <div class="bg-white p-6 rounded-xl shadow-sm hover:shadow-md transition-all">
                    <img src="{{ asset('assets/img/visa.png') }}" alt="Visa" class="h-16 object-contain">
                </div>
                <div class="bg-white p-6 rounded-xl shadow-sm hover:shadow-md transition-all">
                    <img src="{{ asset('assets/img/mastercard.png') }}" alt="Mastercard" class="h-16 object-contain">
                </div>
            </div>
        </div>
    </section>

    <!-- Enhanced FAQ Section -->
    <section class="py-16 bg-white" id="faq">
        <div class="container mx-auto px-4 max-w-6xl">
            <div class="text-center mb-12">
                <span class="inline-block px-4 py-1 bg-purple-100 text-purple-800 rounded-full text-sm font-medium mb-3">Support</span>
                <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">Frequently Asked Questions</h2>
                <p class="text-gray-600 max-w-2xl mx-auto">Find quick answers to common questions about our service</p>
            </div>

            <div class="max-w-3xl mx-auto">
                <!-- FAQ Item 1 -->
                <div class="bg-white rounded-xl shadow-sm mb-4 overflow-hidden faq-item">
                    <div class="p-5 cursor-pointer hover:bg-gray-50 transition-colors duration-200 faq-header">
                        <div class="flex justify-between items-center">
                            <h3 class="text-lg font-semibold text-gray-900">How long does it take for funds to reflect?</h3>
                            <div class="w-8 h-8 rounded-full bg-gray-100 flex items-center justify-center">
                                <i class="fas fa-chevron-down text-primary text-sm faq-toggle"></i>
                            </div>
                        </div>
                        <div class="mt-3 text-gray-600 faq-answer">
                            Funds are typically reflected instantly once the card payment is successful. In rare cases, it might take up to 5 minutes due to network conditions.
                        </div>
                    </div>
                </div>

                <!-- FAQ Item 2 -->
                <div class="bg-white rounded-xl shadow-sm mb-4 overflow-hidden faq-item">
                    <div class="p-5 cursor-pointer hover:bg-gray-50 transition-colors duration-200 faq-header">
                        <div class="flex justify-between items-center">
                            <h3 class="text-lg font-semibold text-gray-900">What cards are accepted for payment?</h3>
                            <div class="w-8 h-8 rounded-full bg-gray-100 flex items-center justify-center">
                                <i class="fas fa-chevron-down text-primary text-sm faq-toggle"></i>
                            </div>
                        </div>
                        <div class="mt-3 text-gray-600 faq-answer">
                            We accept all Visa and Mastercard debit and credit cards issued by any bank. Both local and international cards are supported for your convenience.
                        </div>
                    </div>
                </div>

                <div class="text-center mt-8">
                    <a href="#" class="inline-flex items-center px-6 py-3 bg-gray-100 hover:bg-gray-200 text-gray-800 rounded-lg transition duration-300">
                        View all FAQs <i class="fas fa-arrow-right ml-2"></i>
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Enhanced CTA Section -->
    <section class="py-16 bg-primary text-white relative overflow-hidden">
        <div class="absolute top-0 right-0 w-1/3 h-full opacity-10">
            <img src="{{ asset('assets/img/world-map.png') }}" alt="World Map" class="w-full h-full object-cover">
        </div>
        <div class="container mx-auto px-4 text-center max-w-6xl relative z-10">
            <h2 class="text-3xl md:text-4xl font-bold mb-6">Ready to Get Started?</h2>
            <p class="max-w-xl mx-auto mb-8 opacity-90 text-lg">Create your account now and start funding your mobile wallet instantly with our secure platform.</p>
            <div class="flex flex-col sm:flex-row justify-center space-y-4 sm:space-y-0 sm:space-x-6">
                <a href="{{ url($buttonUrl) }}" class="inline-block bg-white text-primary font-medium px-8 py-4 rounded-lg shadow-lg hover:bg-gray-100 transition duration-300 text-lg">
                    <i class="fas fa-credit-card mr-2"></i> {{ $buttonText }} Now
                </a>
                <a href="#how-it-works" class="inline-block bg-transparent border border-white text-white font-medium px-8 py-4 rounded-lg hover:bg-white hover:text-primary transition duration-300 text-lg">
                    <i class="fas fa-info-circle mr-2"></i> Learn More
                </a>
            </div>
        </div>
    </section>

    <!-- Enhanced Footer -->
    <footer class="bg-gray-900 text-white py-16">
        <div class="container mx-auto px-4 max-w-6xl">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-12">
                <div>
                    <img src="{{ asset('assets/img/logo.png') }}" alt="Card to Wallet Logo" class="h-12 mb-6">
                    <p class="text-gray-400 text-base mb-6 max-w-md">Zambia's instant mobile money funding platform. Fast, reliable, and trusted by thousands.</p>
                    <div class="flex space-x-5 mt-6">
                    <a href="#" class="w-10 h-10 rounded-full bg-gray-800 flex items-center justify-center text-gray-400 hover:bg-primary hover:text-white transition duration-300" aria-label="Facebook">
                        <i class="fab fa-facebook-f text-lg"></i>
                    </a>
                    <a href="#" class="w-10 h-10 rounded-full bg-gray-800 flex items-center justify-center text-gray-400 hover:bg-primary hover:text-white transition duration-300" aria-label="Twitter">
                        <i class="fab fa-twitter text-lg"></i>
                    </a>
                    <a href="#" class="w-10 h-10 rounded-full bg-gray-800 flex items-center justify-center text-gray-400 hover:bg-primary hover:text-white transition duration-300" aria-label="Instagram">
                        <i class="fab fa-instagram text-lg"></i>
                    </a>
                    <a href="#" class="w-10 h-10 rounded-full bg-gray-800 flex items-center justify-center text-gray-400 hover:bg-primary hover:text-white transition duration-300" aria-label="LinkedIn">
                        <i class="fab fa-linkedin-in text-lg"></i>
                    </a>
                </div>
            </div>
            <div>
                <h3 class="text-lg font-semibold mb-6">Quick Links</h3>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <ul class="space-y-3">
                            <li><a href="{{ url('/') }}" class="text-gray-400 hover:text-white transition duration-300">Home</a></li>
                            <li><a href="#how-it-works" class="text-gray-400 hover:text-white transition duration-300">How It Works</a></li>
                            <li><a href="#fee-structure" class="text-gray-400 hover:text-white transition duration-300">Pricing</a></li>
                            <li><a href="#faq" class="text-gray-400 hover:text-white transition duration-300">FAQ</a></li>
                        </ul>
                    </div>
                    <div>
                        <ul class="space-y-3">
                            <li><a href="{{ url('/login') }}" class="text-gray-400 hover:text-white transition duration-300">Login</a></li>
                            <li><a href="{{ url('/register') }}" class="text-gray-400 hover:text-white transition duration-300">Register</a></li>
                            <li><a href="{{ url('/terms') }}" class="text-gray-400 hover:text-white transition duration-300">Terms of Service</a></li>
                            <li><a href="{{ url('/privacy') }}" class="text-gray-400 hover:text-white transition duration-300">Privacy Policy</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="border-t border-gray-800 mt-12 pt-8">
            <div class="flex flex-col md:flex-row justify-between items-center">
                <div class="mb-4 md:mb-0">
                    <p class="text-gray-400">&copy; {{ date('Y') }} {{ config('app.name') }}. All Rights Reserved.</p>
                </div>
                <div class="flex items-center space-x-4">
                    <img src="{{ asset('assets/img/visa.png') }}" alt="Visa" class="h-8">
                    <img src="{{ asset('assets/img/mastercard.png') }}" alt="Mastercard" class="h-8">
                </div>
            </div>
        </div>
    </div>
</footer>
</body>
</html>
