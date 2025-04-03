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
    </style>
</head>
<body class="bg-light">
    <!-- Header -->
    <header class="bg-white shadow-sm">
        <div class="container mx-auto px-4 py-4 flex justify-between items-center max-w-6xl">
            <a href="{{ url('/') }}" class="flex items-center">
                <img src="{{ asset('assets/img/logo.png') }}" alt="Logo" class="h-10">
            </a>
            <nav class="flex items-center space-x-2">
                <a href="{{ url('/login') }}" class="text-dark hover:text-primary font-medium px-4 py-2 rounded-lg hover:bg-gray-100 transition duration-300">Login</a>
                <a href="{{ url('/register') }}" class="bg-primary text-white font-medium px-4 py-2 rounded-lg hover:bg-opacity-90 transition duration-300">Register</a>
            </nav>
        </div>
    </header>

    <!-- Hero Section -->
    <section class="py-20 hero-gradient text-white relative overflow-hidden">
        <div class="container mx-auto px-4 max-w-6xl relative z-10 mx-auto">
            <div class="flex flex-col md:flex-row items-center justify-between">
                <div class="md:w-1/2 text-left mb-10 md:mb-0">
                    <h1 class="text-4xl md:text-5xl font-bold mb-6 leading-tight">{{ $title }}</h1>
                    <p class="text-xl mb-6 font-light">{{ $subtitle }}</p>
                    <p class="text-lg mb-8 opacity-90">{{ $description }}</p>
                    <a href="{{ url($buttonUrl) }}" class="inline-block bg-white text-primary font-semibold px-8 py-4 rounded-lg shadow-md hover:bg-gray-100 transition duration-300 text-lg">
                        <i class="fas fa-credit-card mr-2"></i> {{ $buttonText }}
                    </a>
                </div>
                <div class="md:w-1/2 flex justify-center">
                    <img src="{{ asset('assets/img/woman-with-phone.png') }}" alt="Mobile Wallet" class="max-w-full h-90 rounded-lg shadow-lg-" onerror="this.src='https://via.placeholder.com/500x400?text=Mobile+Wallet';this.onerror='';">
                </div>
            </div>
        </div>
    </section>

    <!-- How It Works Section -->
    <section class="py-16 bg-white">
        <div class="container mx-auto px-4 max-w-6xl">
            <div class="text-center mb-12">
                <h2 class="text-3xl font-bold text-dark mb-4">How It Works</h2>
                <p class="text-gray-600 max-w-2xl mx-auto">Fund your mobile wallet in three simple steps</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Step 1 -->
                <div class="bg-light rounded-lg p-6 text-center shadow-sm hover:shadow-md transition-shadow duration-300">
                    <div class="inline-block p-3 bg-indigo-100 text-primary rounded-full mb-4">
                        <i class="fas fa-mobile-alt text-xl"></i>
                    </div>
                    <h3 class="text-lg font-semibold mb-2 text-dark">Enter Mobile Details</h3>
                    <p class="text-gray-600 text-sm">Enter your mobile number and the amount you want to transfer.</p>
                </div>

                <!-- Step 2 -->
                <div class="bg-light rounded-lg p-6 text-center shadow-sm hover:shadow-md transition-shadow duration-300">
                    <div class="inline-block p-3 bg-indigo-100 text-primary rounded-full mb-4">
                        <i class="fas fa-credit-card text-xl"></i>
                    </div>
                    <h3 class="text-lg font-semibold mb-2 text-dark">Make Secure Payment</h3>
                    <p class="text-gray-600 text-sm">Complete your payment using your Visa or Mastercard securely.</p>
                </div>

                <!-- Step 3 -->
                <div class="bg-light rounded-lg p-6 text-center shadow-sm hover:shadow-md transition-shadow duration-300">
                    <div class="inline-block p-3 bg-indigo-100 text-primary rounded-full mb-4">
                        <i class="fas fa-check-circle text-xl"></i>
                    </div>
                    <h3 class="text-lg font-semibold mb-2 text-dark">Receive Funds Instantly</h3>
                    <p class="text-gray-600 text-sm">Your mobile wallet is funded instantly with confirmation.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Fee Structure Section -->
    <section class="py-12 bg-light">
        <div class="container mx-auto px-4 max-w-6xl">
            <div class="max-w-4xl mx-auto bg-white rounded-lg shadow-sm overflow-hidden">
                <div class="md:flex">
                    <div class="md:flex-shrink-0 bg-primary flex items-center justify-center md:w-40">
                        <div class="text-center p-6">
                            <div class="text-white text-3xl font-bold">4%</div>
                            <div class="text-white text-base">Total Fee</div>
                        </div>
                    </div>
                    <div class="p-6">
                        <div class="text-xl font-semibold text-dark mb-3">Transparent Fee Structure</div>
                        <p class="text-gray-600 mb-4 text-sm">We charge a simple, transparent fee of 4% on all transactions.</p>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                            <div class="border rounded-lg p-3">
                                <div class="flex items-center">
                                    <div class="w-8 h-8 rounded-full bg-indigo-100 flex items-center justify-center mr-3">
                                        <i class="fas fa-university text-primary text-sm"></i>
                                    </div>
                                    <div>
                                        <div class="font-semibold text-sm">3% Bank Fee</div>
                                        <div class="text-xs text-gray-500">Processing & card charges</div>
                                    </div>
                                </div>
                            </div>
                            <div class="border rounded-lg p-3">
                                <div class="flex items-center">
                                    <div class="w-8 h-8 rounded-full bg-indigo-100 flex items-center justify-center mr-3">
                                        <i class="fas fa-cog text-primary text-sm"></i>
                                    </div>
                                    <div>
                                        <div class="font-semibold text-sm">1% Platform Fee</div>
                                        <div class="text-xs text-gray-500">Service & maintenance</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Benefits Section -->
    <section class="py-16 bg-white">
        <div class="container mx-auto px-4 max-w-6xl">
            <div class="text-center mb-10">
                <h2 class="text-3xl font-bold text-dark mb-4">Why Choose Our Card-to-Wallet Service?</h2>
                <p class="text-gray-600 max-w-2xl mx-auto">Experience the best way to fund your mobile wallet</p>
            </div>

            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <!-- Benefit 1 -->
                <div class="bg-light rounded-lg p-4 text-center shadow-sm hover:shadow-md transition-shadow duration-300">
                    <div class="inline-block p-3 bg-indigo-100 text-primary rounded-full mb-3">
                        <i class="fas fa-shield-alt text-lg"></i>
                    </div>
                    <h3 class="text-base font-semibold mb-2 text-dark">Security</h3>
                    <p class="text-gray-600 text-xs">Your card details are never stored on our platform.</p>
                </div>

                <!-- Benefit 2 -->
                <div class="bg-light rounded-lg p-4 text-center shadow-sm hover:shadow-md transition-shadow duration-300">
                    <div class="inline-block p-3 bg-indigo-100 text-primary rounded-full mb-3">
                        <i class="fas fa-bolt text-lg"></i>
                    </div>
                    <h3 class="text-base font-semibold mb-2 text-dark">Speed</h3>
                    <p class="text-gray-600 text-xs">Instant transfers with real-time confirmation.</p>
                </div>

                <!-- Benefit 3 -->
                <div class="bg-light rounded-lg p-4 text-center shadow-sm hover:shadow-md transition-shadow duration-300">
                    <div class="inline-block p-3 bg-indigo-100 text-primary rounded-full mb-3">
                        <i class="fas fa-mobile-alt text-lg"></i>
                    </div>
                    <h3 class="text-base font-semibold mb-2 text-dark">Convenience</h3>
                    <p class="text-gray-600 text-xs">Fund your wallet anytime, anywhere.</p>
                </div>

                <!-- Benefit 4 -->
                <div class="bg-light rounded-lg p-4 text-center shadow-sm hover:shadow-md transition-shadow duration-300">
                    <div class="inline-block p-3 bg-indigo-100 text-primary rounded-full mb-3">
                        <i class="fas fa-sync-alt text-lg"></i>
                    </div>
                    <h3 class="text-base font-semibold mb-2 text-dark">Multiple Wallets</h3>
                    <p class="text-gray-600 text-xs">Support for all major mobile money providers.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- FAQ Preview Section -->
    <section class="py-12 bg-light">
        <div class="container mx-auto px-4 max-w-6xl">
            <div class="text-center mb-8">
                <h2 class="text-2xl font-bold text-dark mb-3">Frequently Asked Questions</h2>
                <p class="text-gray-600 max-w-2xl mx-auto text-sm">Find quick answers to common questions</p>
            </div>

            <div class="max-w-2xl mx-auto">
                <!-- FAQ Item 1 -->
                <div class="bg-white rounded-lg shadow-sm mb-3 overflow-hidden">
                    <div class="p-4 cursor-pointer hover:bg-gray-50 transition-colors duration-200">
                        <div class="flex justify-between items-center">
                            <h3 class="text-base font-semibold text-dark">How long does it take for funds to reflect?</h3>
                            <i class="fas fa-chevron-down text-primary text-sm"></i>
                        </div>
                        <div class="mt-2 text-gray-600 text-sm">
                            Funds are typically reflected instantly once the card payment is successful.
                        </div>
                    </div>
                </div>

                <!-- FAQ Item 2 -->
                <div class="bg-white rounded-lg shadow-sm mb-3 overflow-hidden">
                    <div class="p-4 cursor-pointer hover:bg-gray-50 transition-colors duration-200">
                        <div class="flex justify-between items-center">
                            <h3 class="text-base font-semibold text-dark">What cards are accepted for payment?</h3>
                            <i class="fas fa-chevron-down text-primary text-sm"></i>
                        </div>
                        <div class="mt-2 text-gray-600 text-sm">
                            We accept all Visa and Mastercard debit and credit cards issued by any bank.
                        </div>
                    </div>
                </div>

                <div class="text-center mt-6">
                    <a href="#" class="text-primary hover:underline font-medium text-sm">View all FAQs <i class="fas fa-arrow-right ml-1"></i></a>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="py-12 bg-primary text-white">
        <div class="container mx-auto px-4 text-center max-w-6xl">
            <h2 class="text-2xl font-bold mb-4">Ready to Get Started?</h2>
            <p class="max-w-xl mx-auto mb-6 opacity-90 text-sm">Create your account now and start funding your mobile wallet instantly.</p>
            <div class="flex justify-center space-x-4">
                <a href="{{ url($buttonUrl) }}" class="inline-block bg-white text-primary font-semibold px-6 py-3 rounded-lg shadow-md hover:bg-gray-100 transition duration-300">
                    {{ $buttonText }} Now
                </a>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-gray-900 text-white py-10">
        <div class="container mx-auto px-4 max-w-6xl">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <img src="{{ asset('assets/img/logo.png') }}" alt="Logo" class="h-8 mb-3">
                    <p class="text-gray-400 text-sm">Your secure bridge between bank cards and mobile wallets in Zambia.</p>
                    <div class="flex space-x-4 mt-3">
                        <a href="#" class="text-gray-400 hover:text-primary transition duration-300">
                            <i class="fab fa-facebook-f"></i>
                        </a>
                        <a href="#" class="text-gray-400 hover:text-primary transition duration-300">
                            <i class="fab fa-twitter"></i>
                        </a>
                        <a href="#" class="text-gray-400 hover:text-primary transition duration-300">
                            <i class="fab fa-instagram"></i>
                        </a>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <h3 class="text-base font-semibold mb-3">Quick Links</h3>
                        <ul class="space-y-1">
                            <li><a href="#" class="text-gray-400 hover:text-primary text-sm transition duration-300">Home</a></li>
                            <li><a href="#" class="text-gray-400 hover:text-primary text-sm transition duration-300">About Us</a></li>
                            <li><a href="#" class="text-gray-400 hover:text-primary text-sm transition duration-300">How It Works</a></li>
                            <li><a href="#" class="text-gray-400 hover:text-primary text-sm transition duration-300">FAQs</a></li>
                        </ul>
                    </div>

                    <div>
                        <h3 class="text-base font-semibold mb-3">Legal</h3>
                        <ul class="space-y-1">
                            <li><a href="#" class="text-gray-400 hover:text-primary text-sm transition duration-300">Terms of Service</a></li>
                            <li><a href="#" class="text-gray-400 hover:text-primary text-sm transition duration-300">Privacy Policy</a></li>
                            <li><a href="#" class="text-gray-400 hover:text-primary text-sm transition duration-300">Refund Policy</a></li>
                        </ul>
                    </div>
                </div>

                <div>
                    <h3 class="text-base font-semibold mb-3">Contact</h3>
                    <ul class="space-y-2">
                        <li class="flex items-start">
                            <i class="fas fa-map-marker-alt mt-1 mr-2 text-gray-400 text-xs"></i>
                            <span class="text-gray-400 text-sm">123 Cairo Road, Lusaka, Zambia</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-phone-alt mt-1 mr-2 text-gray-400 text-xs"></i>
                            <span class="text-gray-400 text-sm">+260 97 1234567</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-envelope mt-1 mr-2 text-gray-400 text-xs"></i>
                            <span class="text-gray-400 text-sm">support@cardtowallet.com</span>
                        </li>
                    </ul>
                </div>
            </div>

            <hr class="border-gray-800 my-6">

            <div class="flex flex-col md:flex-row justify-between items-center">
                <p class="text-gray-400 text-sm">&copy; {{ date('Y') }} <strong>{{ config('app.name') }}</strong>. All Rights Reserved</p>
                <div class="mt-3 md:mt-0">
                    <img src="{{ asset('assets/img/visa-mastercard.png') }}" alt="Payment Methods" class="h-6">
                </div>
            </div>
        </div>
    </footer>

    <script>
        // Simple FAQ toggle functionality
        document.addEventListener('DOMContentLoaded', function() {
            const faqItems = document.querySelectorAll('.faq-item');

            faqItems.forEach(item => {
                item.addEventListener('click', function() {
                    const answer = this.querySelector('div:nth-child(2)');
                    const icon = this.querySelector('i');

                    if (answer.style.display === 'none') {
                        answer.style.display = 'block';
                        icon.classList.remove('fa-chevron-down');
                        icon.classList.add('fa-chevron-up');
                    } else {
                        answer.style.display = 'none';
                        icon.classList.remove('fa-chevron-up');
                        icon.classList.add('fa-chevron-down');
                    }
                });
            });
        });
    </script>
</body>
</html>
