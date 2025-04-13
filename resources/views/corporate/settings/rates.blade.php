@extends('corporate.layouts.app')

@section('title', 'Rate Settings')

@section('content')
<div class="mb-6">
    <h2 class="text-xl font-bold text-gray-800">Company Settings</h2>
    <p class="text-gray-500">Manage your company settings and preferences</p>
</div>

<div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
    <!-- Sidebar Navigation -->
    <div class="lg:col-span-1">
        <div class="bg-white rounded-xl shadow-sm overflow-hidden">
            <div class="p-6 border-b border-gray-100">
                <h2 class="text-lg font-bold text-dark">Settings</h2>
            </div>
            <div class="p-4">
                <nav class="space-y-1">
                    <a href="{{ route('corporate.settings.profile') }}" class="flex items-center px-4 py-3 text-dark hover:bg-gray-50 rounded-lg transition-colors">
                        <i class="fas fa-building w-6 text-gray-500"></i>
                        <span>Company Profile</span>
                    </a>
                    <a href="{{ route('corporate.settings.security') }}" class="flex items-center px-4 py-3 text-dark hover:bg-gray-50 rounded-lg transition-colors">
                        <i class="fas fa-shield-alt w-6 text-gray-500"></i>
                        <span>Security</span>
                    </a>
                    <a href="{{ route('corporate.settings.roles') }}" class="flex items-center px-4 py-3 text-dark hover:bg-gray-50 rounded-lg transition-colors">
                        <i class="fas fa-user-tag w-6 text-gray-500"></i>
                        <span>User Roles</span>
                    </a>
                    <a href="{{ route('corporate.settings.approvals') }}" class="flex items-center px-4 py-3 text-dark hover:bg-gray-50 rounded-lg transition-colors">
                        <i class="fas fa-check-double w-6 text-gray-500"></i>
                        <span>Approval Workflows</span>
                    </a>
                    <a href="{{ route('corporate.settings.rates') }}" class="flex items-center px-4 py-3 text-dark bg-primary bg-opacity-10 rounded-lg">
                        <i class="fas fa-percentage w-6 text-primary"></i>
                        <span class="font-medium">Rate Settings</span>
                    </a>
                </nav>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="lg:col-span-3">
        <!-- Current Rate Tier -->
<div class="bg-white rounded-xl shadow-sm overflow-hidden mb-6">
    <div class="p-6 border-b border-gray-200">
        <h3 class="text-lg font-semibold text-gray-800">Your Current Rate</h3>
        <p class="text-sm text-gray-500">Your company's current transaction fee rate</p>
    </div>
    
    <div class="p-6">
        @if($rateAssignment)
            <div class="flex flex-col md:flex-row md:items-center md:justify-between">
                <div>
                    <div class="flex items-center mb-2">
                        <span class="text-3xl font-bold text-primary">{{ number_format($rateAssignment->fee_percentage, 2) }}%</span>
                        @if($rateAssignment->discount_percentage > 0)
                            <span class="ml-3 px-2 py-1 bg-success bg-opacity-10 text-success text-xs font-medium rounded-full">
                                {{ number_format($rateAssignment->discount_percentage, 2) }}% Discount Applied
                            </span>
                        @endif
                    </div>
                    <p class="text-sm text-gray-600">
                        Base rate: {{ number_format($rateAssignment->rateTier->base_percentage, 2) }}%
                        @if($rateAssignment->discount_percentage > 0)
                            with {{ number_format($rateAssignment->discount_percentage, 2) }}% discount
                        @endif
                    </p>
                    <p class="text-sm text-gray-600 mt-1">
                        Tier: <span class="font-medium">{{ $rateAssignment->rateTier->name }}</span>
                    </p>
                    <p class="text-sm text-gray-600 mt-1">
                        Effective from: {{ $rateAssignment->effective_from->format('M d, Y') }}
                        @if($rateAssignment->effective_to)
                            to {{ $rateAssignment->effective_to->format('M d, Y') }}
                        @endif
                    </p>
                </div>
                
                <div class="mt-4 md:mt-0">
                    <div class="flex flex-col items-center bg-gray-50 rounded-lg p-4">
                        <div class="text-sm text-gray-500 mb-1">Monthly Transaction Volume</div>
                        <div class="text-2xl font-bold text-gray-800">K {{ number_format($monthlyVolume ?? 0, 2) }}</div>
                        <div class="text-xs text-gray-500 mt-1">Last 30 days</div>
                    </div>
                </div>
            </div>
            
            <div class="mt-6 border-t border-gray-100 pt-6">
                <h4 class="font-medium text-gray-800 mb-2">Rate Details</h4>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="bg-gray-50 rounded-lg p-4">
                        <div class="text-sm text-gray-500 mb-1">Transaction Fee</div>
                        <div class="text-xl font-bold text-gray-800">{{ number_format($rateAssignment->fee_percentage, 2) }}%</div>
                        <div class="text-xs text-gray-500 mt-1">Per transaction</div>
                    </div>
                    
                    <div class="bg-gray-50 rounded-lg p-4">
                        <div class="text-sm text-gray-500 mb-1">Minimum Fee</div>
                        <div class="text-xl font-bold text-gray-800">K {{ number_format($rateAssignment->rateTier->min_fee, 2) }}</div>
                        <div class="text-xs text-gray-500 mt-1">Per transaction</div>
                    </div>
                    
                    <div class="bg-gray-50 rounded-lg p-4">
                        <div class="text-sm text-gray-500 mb-1">Maximum Fee</div>
                        <div class="text-xl font-bold text-gray-800">
                            @if($rateAssignment->rateTier->max_fee)
                                K {{ number_format($rateAssignment->rateTier->max_fee, 2) }}
                            @else
                                No limit
                            @endif
                        </div>
                        <div class="text-xs text-gray-500 mt-1">Per transaction</div>
                    </div>
                </div>
            </div>
        @else
            <div class="text-center py-6">
                <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-gray-100 text-gray-400 mb-4">
                    <i class="fas fa-percentage text-2xl"></i>
                </div>
                <h3 class="text-lg font-medium text-gray-900 mb-1">No rate assignment found</h3>
                <p class="text-gray-500">Your company doesn't have a rate tier assigned yet.</p>
            </div>
        @endif
    </div>
</div>

<!-- Rate Tiers -->
<div class="bg-white rounded-xl shadow-sm overflow-hidden">
    <div class="p-6 border-b border-gray-200">
        <h3 class="text-lg font-semibold text-gray-800">Available Rate Tiers</h3>
        <p class="text-sm text-gray-500">Transaction fee rates based on monthly volume</p>
    </div>
    
    <div class="p-6">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50">
                        <th class="px-6 py-3 text-left font-medium text-gray-500">Tier</th>
                        <th class="px-6 py-3 text-left font-medium text-gray-500">Monthly Volume</th>
                        <th class="px-6 py-3 text-left font-medium text-gray-500">Base Rate</th>
                        <th class="px-6 py-3 text-left font-medium text-gray-500">Min Fee</th>
                        <th class="px-6 py-3 text-left font-medium text-gray-500">Max Fee</th>
                        <th class="px-6 py-3 text-left font-medium text-gray-500">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($rateTiers as $tier)
                        <tr class="hover:bg-gray-50 {{ $rateAssignment && $rateAssignment->corporate_rate_tier_id == $tier->id ? 'bg-primary bg-opacity-5' : '' }}">
                            <td class="px-6 py-4 font-medium {{ $rateAssignment && $rateAssignment->corporate_rate_tier_id == $tier->id ? 'text-primary' : 'text-gray-900' }}">
                                {{ $tier->name }}
                            </td>
                            <td class="px-6 py-4">
                                @if($tier->min_monthly_volume && $tier->max_monthly_volume)
                                    K {{ number_format($tier->min_monthly_volume, 2) }} - K {{ number_format($tier->max_monthly_volume, 2) }}
                                @elseif($tier->min_monthly_volume)
                                    K {{ number_format($tier->min_monthly_volume, 2) }}+
                                @elseif($tier->max_monthly_volume)
                                    Up to K {{ number_format($tier->max_monthly_volume, 2) }}
                                @else
                                    Any volume
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                {{ number_format($tier->base_percentage, 2) }}%
                            </td>
                            <td class="px-6 py-4">
                                K {{ number_format($tier->min_fee, 2) }}
                            </td>
                            <td class="px-6 py-4">
                                @if($tier->max_fee)
                                    K {{ number_format($tier->max_fee, 2) }}
                                @else
                                    No limit
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                @if($rateAssignment && $rateAssignment->corporate_rate_tier_id == $tier->id)
                                    <span class="px-2 py-1 bg-success text-white text-xs font-medium rounded-full">
                                        Current Tier
                                    </span>
                                @elseif($monthlyVolume >= $tier->min_monthly_volume && (!$tier->max_monthly_volume || $monthlyVolume <= $tier->max_monthly_volume))
                                    <span class="px-2 py-1 bg-primary bg-opacity-10 text-primary text-xs font-medium rounded-full">
                                        Eligible
                                    </span>
                                @else
                                    <span class="px-2 py-1 bg-gray-100 text-gray-600 text-xs font-medium rounded-full">
                                        Not Eligible
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        
        <div class="mt-6 p-4 bg-gray-50 rounded-lg">
            <div class="flex items-start">
                <div class="flex-shrink-0 w-10 h-10 rounded-full bg-primary bg-opacity-10 flex items-center justify-center text-primary mr-4">
                    <i class="fas fa-info-circle"></i>
                </div>
                <div>
                    <h4 class="font-medium text-gray-800">How Rate Tiers Work</h4>
                    <p class="text-sm text-gray-600 mt-1">Your transaction fee rate is determined by your monthly transaction volume. As your volume increases, you may qualify for lower rates. Rate changes require approval and are typically reviewed monthly.</p>
                    <p class="text-sm text-gray-600 mt-2">If you believe you qualify for a better rate or have questions about your current rate, please contact your account manager.</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Rate History -->
<div class="bg-white rounded-xl shadow-sm overflow-hidden mt-6">
    <div class="p-6 border-b border-gray-200">
        <h3 class="text-lg font-semibold text-gray-800">Rate History</h3>
        <p class="text-sm text-gray-500">Previous rate assignments for your company</p>
    </div>
    
    <div class="p-6">
        @if(isset($rateHistory) && count($rateHistory) > 0)
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-gray-50">
                            <th class="px-6 py-3 text-left font-medium text-gray-500">Tier</th>
                            <th class="px-6 py-3 text-left font-medium text-gray-500">Rate</th>
                            <th class="px-6 py-3 text-left font-medium text-gray-500">Discount</th>
                            <th class="px-6 py-3 text-left font-medium text-gray-500">Effective From</th>
                            <th class="px-6 py-3 text-left font-medium text-gray-500">Effective To</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($rateHistory as $history)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 font-medium text-gray-900">
                                    {{ $history->rateTier->name }}
                                </td>
                                <td class="px-6 py-4">
                                    {{ number_format($history->fee_percentage, 2) }}%
                                </td>
                                <td class="px-6 py-4">
                                    @if($history->discount_percentage > 0)
                                        {{ number_format($history->discount_percentage, 2) }}%
                                    @else
                                        -
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    {{ $history->effective_from->format('M d, Y') }}
                                </td>
                                <td class="px-6 py-4">
                                    @if($history->effective_to)
                                        {{ $history->effective_to->format('M d, Y') }}
                                    @else
                                        Current
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-center py-6">
                <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-gray-100 text-gray-400 mb-4">
                    <i class="fas fa-history text-2xl"></i>
                </div>
                <h3 class="text-lg font-medium text-gray-900 mb-1">No rate history</h3>
                <p class="text-gray-500">Your company doesn't have any previous rate assignments.</p>
            </div>
        @endif
    </div>
</div>
@endsection
