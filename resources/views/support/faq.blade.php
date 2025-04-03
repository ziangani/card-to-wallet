@extends('layouts.app')

@section('title', 'FAQ - ' . config('app.name'))
@section('meta_description', 'Frequently asked questions about card-to-wallet transfers')
@section('header_title', 'Frequently Asked Questions')

@section('content')
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Sidebar -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                <div class="p-6 border-b border-gray-100">
                    <h2 class="text-xl font-bold text-dark">Support Options</h2>
                </div>
                <div class="p-4">
                    <nav class="space-y-1">
                        <a href="{{ route('support') }}" class="flex items-center px-4 py-3 text-dark hover:bg-gray-50 rounded-lg transition-colors">
                            <i class="fas fa-headset w-6 text-gray-500"></i>
                            <span>Contact Support</span>
                        </a>
                        <a href="{{ route('faq') }}" class="flex items-center px-4 py-3 text-dark bg-primary bg-opacity-10 rounded-lg">
                            <i class="fas fa-question-circle w-6 text-primary"></i>
                            <span class="font-medium">Frequently Asked Questions</span>
                        </a>
                    </nav>
                </div>
            </div>

            <!-- FAQ Categories -->
            <div class="bg-white rounded-xl shadow-sm overflow-hidden mt-6">
                <div class="p-6 border-b border-gray-100">
                    <h2 class="text-xl font-bold text-dark">Categories</h2>
                </div>
                <div class="p-4">
                    <nav class="space-y-1">
                        <a href="#general" class="flex items-center px-4 py-3 text-dark hover:bg-gray-50 rounded-lg transition-colors">
                            <i class="fas fa-info-circle w-6 text-primary"></i>
                            <span>General Questions</span>
                        </a>
                        <a href="#account" class="flex items-center px-4 py-3 text-dark hover:bg-gray-50 rounded-lg transition-colors">
                            <i class="fas fa-user-circle w-6 text-primary"></i>
                            <span>Account & Verification</span>
                        </a>
                        <a href="#transactions" class="flex items-center px-4 py-3 text-dark hover:bg-gray-50 rounded-lg transition-colors">
                            <i class="fas fa-exchange-alt w-6 text-primary"></i>
                            <span>Transactions</span>
                        </a>
                        <a href="#fees" class="flex items-center px-4 py-3 text-dark hover:bg-gray-50 rounded-lg transition-colors">
                            <i class="fas fa-money-bill-wave w-6 text-primary"></i>
                            <span>Fees & Limits</span>
                        </a>
                        <a href="#security" class="flex items-center px-4 py-3 text-dark hover:bg-gray-50 rounded-lg transition-colors">
                            <i class="fas fa-shield-alt w-6 text-primary"></i>
                            <span>Security</span>
                        </a>
                    </nav>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="lg:col-span-2">
            <!-- Search -->
            <div class="bg-white rounded-xl shadow-sm overflow-hidden mb-6">
                <div class="p-6">
                    <div class="relative">
                        <input type="text" id="faq-search" placeholder="Search frequently asked questions..." 
                            class="w-full px-4 py-3 pl-12 border border-gray-300 rounded-lg focus:ring-primary focus:border-primary">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-search text-gray-400"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- General Questions -->
            <div id="general" class="bg-white rounded-xl shadow-sm overflow-hidden mb-6">
                <div class="p-6 border-b border-gray-100">
                    <h2 class="text-xl font-bold text-dark">General Questions</h2>
                </div>
                <div class="p-6">
                    <div class="space-y-4">
                        <div class="border border-gray-200 rounded-lg overflow-hidden">
                            <button class="faq-question w-full flex justify-between items-center p-4 text-left font-medium text-dark hover:bg-gray-50 focus:outline-none">
                                <span>What is Card-to-Wallet?</span>
                                <i class="fas fa-chevron-down text-gray-400 transition-transform"></i>
                            </button>
                            <div class="faq-answer px-4 pb-4 hidden">
                                <p class="text-gray-600">
                                    Card-to-Wallet is a service that allows you to fund mobile money wallets directly from your credit or debit card. It provides a convenient way to transfer money to mobile wallets without the need to visit physical locations or ATMs.
                                </p>
                            </div>
                        </div>

                        <div class="border border-gray-200 rounded-lg overflow-hidden">
                            <button class="faq-question w-full flex justify-between items-center p-4 text-left font-medium text-dark hover:bg-gray-50 focus:outline-none">
                                <span>Which mobile money providers do you support?</span>
                                <i class="fas fa-chevron-down text-gray-400 transition-transform"></i>
                            </button>
                            <div class="faq-answer px-4 pb-4 hidden">
                                <p class="text-gray-600">
                                    We currently support the following mobile money providers in Zambia:
                                </p>
                                <ul class="list-disc pl-5 mt-2 space-y-1 text-gray-600">
                                    <li>Airtel Money</li>
                                    <li>MTN Mobile Money</li>
                                    <li>Zamtel Kwacha</li>
                                </ul>
                                <p class="text-gray-600 mt-2">
                                    We are continuously working to add more providers to our platform.
                                </p>
                            </div>
                        </div>

                        <div class="border border-gray-200 rounded-lg overflow-hidden">
                            <button class="faq-question w-full flex justify-between items-center p-4 text-left font-medium text-dark hover:bg-gray-50 focus:outline-none">
                                <span>How do I get started?</span>
                                <i class="fas fa-chevron-down text-gray-400 transition-transform"></i>
                            </button>
                            <div class="faq-answer px-4 pb-4 hidden">
                                <p class="text-gray-600">
                                    Getting started is easy:
                                </p>
                                <ol class="list-decimal pl-5 mt-2 space-y-1 text-gray-600">
                                    <li>Create an account on our platform</li>
                                    <li>Verify your email and phone number</li>
                                    <li>Complete your profile information</li>
                                    <li>Start making transfers to mobile wallets</li>
                                </ol>
                                <p class="text-gray-600 mt-2">
                                    For higher transaction limits, you'll need to complete KYC verification by uploading your identification documents.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Account & Verification -->
            <div id="account" class="bg-white rounded-xl shadow-sm overflow-hidden mb-6">
                <div class="p-6 border-b border-gray-100">
                    <h2 class="text-xl font-bold text-dark">Account & Verification</h2>
                </div>
                <div class="p-6">
                    <div class="space-y-4">
                        <div class="border border-gray-200 rounded-lg overflow-hidden">
                            <button class="faq-question w-full flex justify-between items-center p-4 text-left font-medium text-dark hover:bg-gray-50 focus:outline-none">
                                <span>Why do I need to verify my identity?</span>
                                <i class="fas fa-chevron-down text-gray-400 transition-transform"></i>
                            </button>
                            <div class="faq-answer px-4 pb-4 hidden">
                                <p class="text-gray-600">
                                    Identity verification is required for several important reasons:
                                </p>
                                <ul class="list-disc pl-5 mt-2 space-y-1 text-gray-600">
                                    <li>To comply with financial regulations and anti-money laundering laws</li>
                                    <li>To protect your account from unauthorized access</li>
                                    <li>To increase your transaction limits</li>
                                    <li>To ensure the security of our platform for all users</li>
                                </ul>
                            </div>
                        </div>

                        <div class="border border-gray-200 rounded-lg overflow-hidden">
                            <button class="faq-question w-full flex justify-between items-center p-4 text-left font-medium text-dark hover:bg-gray-50 focus:outline-none">
                                <span>What documents do I need for KYC verification?</span>
                                <i class="fas fa-chevron-down text-gray-400 transition-transform"></i>
                            </button>
                            <div class="faq-answer px-4 pb-4 hidden">
                                <p class="text-gray-600">
                                    For KYC verification, you'll need to provide:
                                </p>
                                <ul class="list-disc pl-5 mt-2 space-y-1 text-gray-600">
                                    <li>A valid government-issued ID (National ID, Passport, or Driver's License)</li>
                                    <li>Proof of address (utility bill, bank statement, or official letter dated within the last 3 months)</li>
                                    <li>A selfie holding your ID document (for facial verification)</li>
                                </ul>
                                <p class="text-gray-600 mt-2">
                                    All documents must be clear, legible, and show all four corners of the document.
                                </p>
                            </div>
                        </div>

                        <div class="border border-gray-200 rounded-lg overflow-hidden">
                            <button class="faq-question w-full flex justify-between items-center p-4 text-left font-medium text-dark hover:bg-gray-50 focus:outline-none">
                                <span>How long does verification take?</span>
                                <i class="fas fa-chevron-down text-gray-400 transition-transform"></i>
                            </button>
                            <div class="faq-answer px-4 pb-4 hidden">
                                <p class="text-gray-600">
                                    Email and phone verification are instant. KYC document verification typically takes 1-2 business days. You'll receive a notification once your documents have been reviewed. If there are any issues with your documents, we'll let you know what needs to be corrected.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Transactions -->
            <div id="transactions" class="bg-white rounded-xl shadow-sm overflow-hidden mb-6">
                <div class="p-6 border-b border-gray-100">
                    <h2 class="text-xl font-bold text-dark">Transactions</h2>
                </div>
                <div class="p-6">
                    <div class="space-y-4">
                        <div class="border border-gray-200 rounded-lg overflow-hidden">
                            <button class="faq-question w-full flex justify-between items-center p-4 text-left font-medium text-dark hover:bg-gray-50 focus:outline-none">
                                <span>How long do transactions take to process?</span>
                                <i class="fas fa-chevron-down text-gray-400 transition-transform"></i>
                            </button>
                            <div class="faq-answer px-4 pb-4 hidden">
                                <p class="text-gray-600">
                                    Most transactions are processed instantly. After your card payment is successful, the funds are typically credited to the mobile wallet within minutes. In rare cases, it may take up to 30 minutes due to network delays or system maintenance by the mobile money provider.
                                </p>
                            </div>
                        </div>

                        <div class="border border-gray-200 rounded-lg overflow-hidden">
                            <button class="faq-question w-full flex justify-between items-center p-4 text-left font-medium text-dark hover:bg-gray-50 focus:outline-none">
                                <span>What happens if my transaction fails?</span>
                                <i class="fas fa-chevron-down text-gray-400 transition-transform"></i>
                            </button>
                            <div class="faq-answer px-4 pb-4 hidden">
                                <p class="text-gray-600">
                                    If your transaction fails, you'll see an error message with details about the failure. If your card was charged but the wallet was not credited, the system will automatically initiate a refund to your card. Refunds typically take 3-5 business days to reflect in your card account, depending on your bank's processing time.
                                </p>
                                <p class="text-gray-600 mt-2">
                                    You can check the status of your transaction in your transaction history. If you don't see a refund after 5 business days, please contact our support team with your transaction ID.
                                </p>
                            </div>
                        </div>

                        <div class="border border-gray-200 rounded-lg overflow-hidden">
                            <button class="faq-question w-full flex justify-between items-center p-4 text-left font-medium text-dark hover:bg-gray-50 focus:outline-none">
                                <span>Can I cancel a transaction?</span>
                                <i class="fas fa-chevron-down text-gray-400 transition-transform"></i>
                            </button>
                            <div class="faq-answer px-4 pb-4 hidden">
                                <p class="text-gray-600">
                                    Once a transaction is initiated and the payment is processed, it cannot be canceled. This is because mobile money transfers are processed immediately. Please double-check all transaction details before confirming your payment.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Fees & Limits -->
            <div id="fees" class="bg-white rounded-xl shadow-sm overflow-hidden mb-6">
                <div class="p-6 border-b border-gray-100">
                    <h2 class="text-xl font-bold text-dark">Fees & Limits</h2>
                </div>
                <div class="p-6">
                    <div class="space-y-4">
                        <div class="border border-gray-200 rounded-lg overflow-hidden">
                            <button class="faq-question w-full flex justify-between items-center p-4 text-left font-medium text-dark hover:bg-gray-50 focus:outline-none">
                                <span>What are the fees for using Card-to-Wallet?</span>
                                <i class="fas fa-chevron-down text-gray-400 transition-transform"></i>
                            </button>
                            <div class="faq-answer px-4 pb-4 hidden">
                                <p class="text-gray-600">
                                    We charge a 4% fee on all transactions. This fee covers payment processing costs, mobile money provider fees, and platform maintenance. The fee is calculated on the transfer amount and added to your total payment. For example, if you transfer K1,000, the fee will be K40, making your total payment K1,040.
                                </p>
                            </div>
                        </div>

                        <div class="border border-gray-200 rounded-lg overflow-hidden">
                            <button class="faq-question w-full flex justify-between items-center p-4 text-left font-medium text-dark hover:bg-gray-50 focus:outline-none">
                                <span>What are the transaction limits?</span>
                                <i class="fas fa-chevron-down text-gray-400 transition-transform"></i>
                            </button>
                            <div class="faq-answer px-4 pb-4 hidden">
                                <p class="text-gray-600">
                                    Transaction limits depend on your verification level:
                                </p>
                                <div class="mt-2">
                                    <h4 class="font-medium text-dark">Basic Verification (Email & Phone verified):</h4>
                                    <ul class="list-disc pl-5 mt-1 space-y-1 text-gray-600">
                                        <li>Up to K1,000 per transaction</li>
                                        <li>Up to K2,000 daily</li>
                                        <li>Up to K5,000 monthly</li>
                                    </ul>
                                </div>
                                <div class="mt-3">
                                    <h4 class="font-medium text-dark">Full Verification (KYC completed):</h4>
                                    <ul class="list-disc pl-5 mt-1 space-y-1 text-gray-600">
                                        <li>Up to K5,000 per transaction</li>
                                        <li>Up to K10,000 daily</li>
                                        <li>Up to K50,000 monthly</li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <div class="border border-gray-200 rounded-lg overflow-hidden">
                            <button class="faq-question w-full flex justify-between items-center p-4 text-left font-medium text-dark hover:bg-gray-50 focus:outline-none">
                                <span>Is there a minimum transfer amount?</span>
                                <i class="fas fa-chevron-down text-gray-400 transition-transform"></i>
                            </button>
                            <div class="faq-answer px-4 pb-4 hidden">
                                <p class="text-gray-600">
                                    Yes, the minimum transfer amount is K10. This is to ensure that the transaction is cost-effective considering the processing fees involved.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Security -->
            <div id="security" class="bg-white rounded-xl shadow-sm overflow-hidden">
                <div class="p-6 border-b border-gray-100">
                    <h2 class="text-xl font-bold text-dark">Security</h2>
                </div>
                <div class="p-6">
                    <div class="space-y-4">
                        <div class="border border-gray-200 rounded-lg overflow-hidden">
                            <button class="faq-question w-full flex justify-between items-center p-4 text-left font-medium text-dark hover:bg-gray-50 focus:outline-none">
                                <span>Is my card information secure?</span>
                                <i class="fas fa-chevron-down text-gray-400 transition-transform"></i>
                            </button>
                            <div class="faq-answer px-4 pb-4 hidden">
                                <p class="text-gray-600">
                                    Yes, your card information is completely secure. We do not store your card details on our servers. All card payments are processed through MasterCard Payment Gateway Services (MPGS), which is PCI-DSS compliant and uses industry-standard encryption to protect your data.
                                </p>
                            </div>
                        </div>

                        <div class="border border-gray-200 rounded-lg overflow-hidden">
                            <button class="faq-question w-full flex justify-between items-center p-4 text-left font-medium text-dark hover:bg-gray-50 focus:outline-none">
                                <span>How do you protect my account?</span>
                                <i class="fas fa-chevron-down text-gray-400 transition-transform"></i>
                            </button>
                            <div class="faq-answer px-4 pb-4 hidden">
                                <p class="text-gray-600">
                                    We implement multiple security measures to protect your account:
                                </p>
                                <ul class="list-disc pl-5 mt-2 space-y-1 text-gray-600">
                                    <li>Email and phone verification for all accounts</li>
                                    <li>Secure password hashing</li>
                                    <li>Account lockout after multiple failed login attempts</li>
                                    <li>Regular security audits and updates</li>
                                    <li>Encrypted connections (HTTPS) for all communications</li>
                                </ul>
                            </div>
                        </div>

                        <div class="border border-gray-200 rounded-lg overflow-hidden">
                            <button class="faq-question w-full flex justify-between items-center p-4 text-left font-medium text-dark hover:bg-gray-50 focus:outline-none">
                                <span>What should I do if I suspect unauthorized activity?</span>
                                <i class="fas fa-chevron-down text-gray-400 transition-transform"></i>
                            </button>
                            <div class="faq-answer px-4 pb-4 hidden">
                                <p class="text-gray-600">
                                    If you suspect unauthorized activity on your account:
                                </p>
                                <ol class="list-decimal pl-5 mt-2 space-y-1 text-gray-600">
                                    <li>Change your password immediately</li>
                                    <li>Contact our support team with details of the suspicious activity</li>
                                    <li>Review your transaction history for any unauthorized transactions</li>
                                    <li>If you see unauthorized card charges, contact your bank to report them</li>
                                </ol>
                                <p class="text-gray-600 mt-2">
                                    We take security very seriously and will investigate all reports of unauthorized activity.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // FAQ accordion functionality
        const faqQuestions = document.querySelectorAll('.faq-question');
        
        faqQuestions.forEach(question => {
            question.addEventListener('click', function() {
                const answer = this.nextElementSibling;
                const icon = this.querySelector('i');
                
                // Toggle answer visibility
                answer.classList.toggle('hidden');
                
                // Rotate icon
                if (answer.classList.contains('hidden')) {
                    icon.style.transform = 'rotate(0deg)';
                } else {
                    icon.style.transform = 'rotate(180deg)';
                }
            });
        });

        // FAQ search functionality
        const searchInput = document.getElementById('faq-search');
        
        searchInput.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            
            faqQuestions.forEach(question => {
                const questionText = question.querySelector('span').textContent.toLowerCase();
                const answer = question.nextElementSibling;
                const answerText = answer.textContent.toLowerCase();
                const faqItem = question.closest('.border');
                
                if (questionText.includes(searchTerm) || answerText.includes(searchTerm)) {
                    faqItem.style.display = 'block';
                } else {
                    faqItem.style.display = 'none';
                }
            });
        });

        // Smooth scroll to sections
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function(e) {
                e.preventDefault();
                
                const targetId = this.getAttribute('href');
                const targetElement = document.querySelector(targetId);
                
                if (targetElement) {
                    window.scrollTo({
                        top: targetElement.offsetTop - 20,
                        behavior: 'smooth'
                    });
                }
            });
        });
    });
</script>
@endpush
