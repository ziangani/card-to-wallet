<div class="bg-white rounded-xl shadow-sm overflow-hidden">
    <div class="p-6">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6">
            <div>
                <h2 class="text-xl font-bold text-dark mb-1">Saved Beneficiaries</h2>
                <p class="text-gray-600">Quick access to your saved recipients</p>
            </div>
            
            <a href="{{ route('beneficiaries.index') }}" class="text-primary hover:underline mt-2 md:mt-0">
                <i class="fas fa-users mr-1"></i> Manage all
            </a>
        </div>
        
        @if(count($savedBeneficiaries ?? []) > 0)
            <div class="space-y-4">
                @foreach($savedBeneficiaries as $beneficiary)
                    <div class="flex items-center justify-between p-3 bg-light rounded-lg hover:bg-gray-100 transition">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 h-10 w-10">
                                @if($beneficiary->wallet_provider)
                                    @if($beneficiary->wallet_provider->api_code === 'airtel')
                                        <img class="h-10 w-10 rounded-full" src="{{ asset('assets/img/airtel.png') }}" alt="Airtel">
                                    @elseif($beneficiary->wallet_provider->api_code === 'mtn')
                                        <img class="h-10 w-10 rounded-full" src="{{ asset('assets/img/mtn.jpg') }}" alt="MTN">
                                    @elseif($beneficiary->wallet_provider->api_code === 'zamtel')
                                        <img class="h-10 w-10 rounded-full" src="{{ asset('assets/img/zamtel.jpg') }}" alt="Zamtel">
                                    @else
                                        <div class="h-10 w-10 rounded-full bg-primary text-white flex items-center justify-center">
                                            {{ strtoupper(substr($beneficiary->recipient_name, 0, 1)) }}
                                        </div>
                                    @endif
                                @else
                                    <div class="h-10 w-10 rounded-full bg-primary text-white flex items-center justify-center">
                                        {{ strtoupper(substr($beneficiary->recipient_name, 0, 1)) }}
                                    </div>
                                @endif
                            </div>
                            <div class="ml-3">
                                <div class="text-sm font-medium text-dark">
                                    {{ $beneficiary->recipient_name }}
                                    @if($beneficiary->is_favorite)
                                        <i class="fas fa-star text-secondary ml-1 text-xs"></i>
                                    @endif
                                </div>
                                <div class="text-xs text-gray-500">
                                    +260{{ $beneficiary->wallet_number }}
                                </div>
                                <div class="text-xs text-gray-500">
                                    {{ $beneficiary->wallet_provider->name ?? 'Unknown Provider' }}
                                </div>
                            </div>
                        </div>
                        <div>
                            <a href="{{ route('transactions.initiate', ['beneficiary_id' => $beneficiary->id]) }}" class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-primary bg-opacity-10 text-primary hover:bg-opacity-20 transition">
                                <i class="fas fa-paper-plane"></i>
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
            
            @if(count($savedBeneficiaries) > 5)
                <div class="mt-4 text-center">
                    <a href="{{ route('beneficiaries.index') }}" class="text-primary hover:underline text-sm">
                        View all beneficiaries ({{ count($allBeneficiaries ?? []) }})
                    </a>
                </div>
            @endif
        @else
            <div class="text-center py-8">
                <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-gray-100 text-gray-400 mb-4">
                    <i class="fas fa-users text-2xl"></i>
                </div>
                <h3 class="text-lg font-medium text-gray-900 mb-1">No saved beneficiaries</h3>
                <p class="text-gray-500">Save recipients for quick access</p>
                <div class="mt-4">
                    <a href="{{ route('beneficiaries.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary hover:bg-opacity-90 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary">
                        <i class="fas fa-plus mr-2"></i> Add Beneficiary
                    </a>
                </div>
            </div>
        @endif
    </div>
    
    <!-- Fund Wallet Button -->
    {{-- <div class="p-6 bg-primary bg-opacity-5 border-t border-primary border-opacity-10">
        <div class="text-center">
            <h3 class="text-lg font-medium text-dark mb-2">Need to fund your wallet?</h3>
            <p class="text-gray-600 mb-4">Use your card to fund your mobile wallet instantly</p>
            <a href="{{ route('transactions.initiate') }}" class="inline-block w-full bg-primary text-white py-3 px-4 rounded-lg hover:bg-opacity-90 transition duration-300 font-medium">
                <i class="fas fa-wallet mr-2"></i> Fund Wallet Now
            </a>
        </div>
    </div> --}}
</div>
