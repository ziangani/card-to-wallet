<div class="bg-white rounded-xl shadow-sm overflow-hidden">
    <div class="p-6">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between">
            <div class="mb-4 md:mb-0">
                <h2 class="text-xl font-bold text-dark mb-1">Account Verification Status</h2>
                <p class="text-gray-600">Complete verification to unlock higher transaction limits</p>
            </div>

            <div class="flex items-center">
                @if(auth()->user()->verification_level === 'verified')
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-success text-white">
                        <i class="fas fa-check-circle mr-1"></i> Verified
                    </span>
                @else
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-warning text-dark">
                        <i class="fas fa-exclamation-circle mr-1"></i> Basic
                    </span>
                @endif
            </div>
        </div>

        <div class="mt-6">
            <div class="relative">
                <div class="overflow-hidden h-2 text-xs flex rounded bg-gray-200">
                    @php
                        // Calculate verification progress
                        $progress = 0;

                        // Email verification - 25%
                        if (auth()->user()->is_email_verified) {
                            $progress += 25;
                        }

                        // Phone verification - 25%
                        if (auth()->user()->is_phone_verified) {
                            $progress += 25;
                        }

                        // KYC documents - 50%
                        $kycProgress = 0;
                        $kycDocuments = auth()->user()->kycDocuments ?? collect();

                        // National ID or Passport - 25%
                        if ($kycDocuments->where('document_type', 'national_id')->where('status', 'approved')->count() > 0 ||
                            $kycDocuments->where('document_type', 'passport')->where('status', 'approved')->count() > 0) {
                            $kycProgress += 25;
                        }

                        // Proof of address - 25%
                        if ($kycDocuments->where('document_type', 'proof_of_address')->where('status', 'approved')->count() > 0) {
                            $kycProgress += 25;
                        }

                        $progress += $kycProgress;
                    @endphp

                    <div style="width: {{ $progress }}%" class="shadow-none flex flex-col text-center whitespace-nowrap text-white justify-center {{ $progress < 50 ? 'bg-warning' : ($progress < 100 ? 'bg-primary' : 'bg-success') }}"></div>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mt-4">
                <!-- Email Verification -->
                <div class="bg-gray-50 rounded-lg p-4">
                    <div class="flex items-center justify-between mb-2">
                        <h3 class="font-medium text-dark">Email</h3>
                        @if(auth()->user()->is_email_verified)
                            <span class="text-success"><i class="fas fa-check-circle"></i></span>
                        @else
                            <span class="text-warning"><i class="fas fa-clock"></i></span>
                        @endif
                    </div>
                    <p class="text-sm text-gray-600 mb-3">Verify your email address</p>

                    @if(auth()->user()->is_email_verified)
                        <span class="inline-block px-2 py-1 text-xs bg-success bg-opacity-10 text-success rounded">Verified</span>
                    @else
                        <a href="{{ route('verification.notice') }}" class="inline-block px-2 py-1 text-xs bg-primary text-white rounded hover:bg-opacity-90 transition">Verify Now</a>
                    @endif
                </div>

                <!-- Phone Verification -->
                <div class="bg-gray-50 rounded-lg p-4">
                    <div class="flex items-center justify-between mb-2">
                        <h3 class="font-medium text-dark">Phone</h3>
                        @if(auth()->user()->is_phone_verified)
                            <span class="text-success"><i class="fas fa-check-circle"></i></span>
                        @else
                            <span class="text-warning"><i class="fas fa-clock"></i></span>
                        @endif
                    </div>
                    <p class="text-sm text-gray-600 mb-3">Verify your phone number</p>

                    @if(auth()->user()->is_phone_verified)
                        <span class="inline-block px-2 py-1 text-xs bg-success bg-opacity-10 text-success rounded">Verified</span>
                    @else
                        <a href="{{ route('phone.verification.notice') }}" class="inline-block px-2 py-1 text-xs bg-primary text-white rounded hover:bg-opacity-90 transition">Verify Now</a>
                    @endif
                </div>

                <!-- ID Verification -->
                <div class="bg-gray-50 rounded-lg p-4">
                    <div class="flex items-center justify-between mb-2">
                        <h3 class="font-medium text-dark">ID Verification</h3>
                        @php
                            $hasApprovedId = $kycDocuments->where('document_type', 'national_id')->where('status', 'approved')->count() > 0 ||
                                            $kycDocuments->where('document_type', 'passport')->where('status', 'approved')->count() > 0;

                            $hasPendingId = $kycDocuments->where('document_type', 'national_id')->where('status', 'pending')->count() > 0 ||
                                           $kycDocuments->where('document_type', 'passport')->where('status', 'pending')->count() > 0;
                        @endphp

                        @if($hasApprovedId)
                            <span class="text-success"><i class="fas fa-check-circle"></i></span>
                        @elseif($hasPendingId)
                            <span class="text-warning"><i class="fas fa-clock"></i></span>
                        @else
                            <span class="text-gray-400"><i class="fas fa-times-circle"></i></span>
                        @endif
                    </div>
                    <p class="text-sm text-gray-600 mb-3">Upload your ID document</p>

                    @if($hasApprovedId)
                        <span class="inline-block px-2 py-1 text-xs bg-success bg-opacity-10 text-success rounded">Verified</span>
                    @elseif($hasPendingId)
                        <span class="inline-block px-2 py-1 text-xs bg-warning bg-opacity-10 text-warning rounded">Pending</span>
                    @else
                        <a href="{{ route('profile.kyc') }}" class="inline-block px-2 py-1 text-xs bg-primary text-white rounded hover:bg-opacity-90 transition">Upload Now</a>
                    @endif
                </div>

                <!-- Address Verification -->
                <div class="bg-gray-50 rounded-lg p-4">
                    <div class="flex items-center justify-between mb-2">
                        <h3 class="font-medium text-dark">Address</h3>
                        @php
                            $hasApprovedAddress = $kycDocuments->where('document_type', 'proof_of_address')->where('status', 'approved')->count() > 0;
                            $hasPendingAddress = $kycDocuments->where('document_type', 'proof_of_address')->where('status', 'pending')->count() > 0;
                        @endphp

                        @if($hasApprovedAddress)
                            <span class="text-success"><i class="fas fa-check-circle"></i></span>
                        @elseif($hasPendingAddress)
                            <span class="text-warning"><i class="fas fa-clock"></i></span>
                        @else
                            <span class="text-gray-400"><i class="fas fa-times-circle"></i></span>
                        @endif
                    </div>
                    <p class="text-sm text-gray-600 mb-3">Upload proof of address</p>

                    @if($hasApprovedAddress)
                        <span class="inline-block px-2 py-1 text-xs bg-success bg-opacity-10 text-success rounded">Verified</span>
                    @elseif($hasPendingAddress)
                        <span class="inline-block px-2 py-1 text-xs bg-warning bg-opacity-10 text-warning rounded">Pending</span>
                    @else
                        <a href="{{ route('profile.kyc') }}" class="inline-block px-2 py-1 text-xs bg-primary text-white rounded hover:bg-opacity-90 transition">Upload Now</a>
                    @endif
                </div>
            </div>
        </div>

        <div class="mt-6 bg-light rounded-lg p-4">
            <div class="flex items-start">
                <div class="flex-shrink-0 mt-1">
                    <i class="fas fa-info-circle text-primary"></i>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-dark">Transaction Limits</h3>
                    <div class="mt-1 text-sm text-gray-600">
                        @if(auth()->user()->verification_level === 'verified')
                            <p>Your account is fully verified. You can now enjoy higher transaction limits:</p>
                            <ul class="list-disc pl-5 mt-1 space-y-1">
                                <li>Up to K5,000 per transaction</li>
                                <li>Up to K10,000 daily</li>
                                <li>Up to K50,000 monthly</li>
                            </ul>
                        @else
                            <p>Your account has basic verification. Current transaction limits:</p>
                            <ul class="list-disc pl-5 mt-1 space-y-1">
                                <li>Up to K1,000 per transaction</li>
                                <li>Up to K2,000 daily</li>
                                <li>Up to K5,000 monthly</li>
                            </ul>
                            <p class="mt-2">Complete verification to increase your limits.</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
