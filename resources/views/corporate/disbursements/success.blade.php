@extends('corporate.layouts.app')

@section('title', 'Disbursement Submitted - ' . config('app.name'))
@section('meta_description', 'Your bulk disbursement has been submitted successfully')
@section('header_title', 'Disbursement Submitted')

@section('content')
    <div class="mb-6">
        <div class="flex items-center mb-2">
            <a href="{{ route('corporate.disbursements.index') }}" class="text-corporate-primary hover:underline">
                <i class="fas fa-arrow-left mr-2"></i> Back to Disbursements
            </a>
        </div>
        <h2 class="text-xl font-bold text-corporate-primary">Disbursement Submitted Successfully</h2>
        <p class="text-gray-500">Your disbursement has been submitted and is being processed</p>
    </div>

    <!-- Step Indicator -->
    <div class="bg-white rounded-xl shadow-sm overflow-hidden mb-6">
        <div class="p-6">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <div class="flex items-center">
                        <div class="w-8 h-8 rounded-full bg-corporate-success text-white flex items-center justify-center text-sm font-medium">
                            <i class="fas fa-check"></i>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-corporate-success">Upload File</p>
                            <p class="text-xs text-gray-500">Upload recipient data</p>
                        </div>
                    </div>
                    <div class="h-1 bg-corporate-success mt-3"></div>
                </div>
                <div class="flex-1">
                    <div class="flex items-center">
                        <div class="w-8 h-8 rounded-full bg-corporate-success text-white flex items-center justify-center text-sm font-medium">
                            <i class="fas fa-check"></i>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-corporate-success">Validate</p>
                            <p class="text-xs text-gray-500">Review validation results</p>
                        </div>
                    </div>
                    <div class="h-1 bg-corporate-success mt-3"></div>
                </div>
                <div class="flex-1">
                    <div class="flex items-center">
                        <div class="w-8 h-8 rounded-full bg-corporate-success text-white flex items-center justify-center text-sm font-medium">
                            <i class="fas fa-check"></i>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-corporate-success">Review</p>
                            <p class="text-xs text-gray-500">Confirm details</p>
                        </div>
                    </div>
                    <div class="h-1 bg-corporate-success mt-3"></div>
                </div>
                <div class="flex-1">
                    <div class="flex items-center">
                        <div class="w-8 h-8 rounded-full bg-corporate-success text-white flex items-center justify-center text-sm font-medium">
                            <i class="fas fa-check"></i>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-corporate-success">Submit</p>
                            <p class="text-xs text-gray-500">Process disbursement</p>
                        </div>
                    </div>
                    <div class="h-1 bg-corporate-success mt-3"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Success Message -->
    <div class="bg-white rounded-xl shadow-sm overflow-hidden mb-6">
        <div class="p-6">
            <div class="text-center py-8">
                <div class="w-20 h-20 rounded-full bg-corporate-success bg-opacity-10 text-corporate-success flex items-center justify-center text-3xl mx-auto mb-6">
                    <i class="fas fa-check-circle"></i>
                </div>
                
                <h3 class="text-2xl font-bold text-corporate-primary mb-2">
                    @if($disbursement->status === 'pending_approval')
                        Disbursement Submitted for Approval
                    @else
                        Disbursement Processing Started
                    @endif
                </h3>
                
                <p class="text-gray-600 max-w-lg mx-auto mb-8">
                    @if($disbursement->status === 'pending_approval')
                        Your disbursement has been submitted and is awaiting approval. You will be notified once it has been approved.
                    @else
                        Your disbursement is now being processed. You can track its progress on the disbursement details page.
                    @endif
                </p>
                
                <div class="flex flex-col sm:flex-row justify-center space-y-3 sm:space-y-0 sm:space-x-4">
                    <a href="{{ route('corporate.disbursements.show', $disbursement->id) }}" class="px-6 py-3 bg-corporate-primary text-white rounded-lg hover:bg-opacity-90 transition">
                        View Disbursement Details
                    </a>
                    <a href="{{ route('corporate.disbursements.index') }}" class="px-6 py-3 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition">
                        Back to Disbursements
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Disbursement Summary -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Left Column - Details -->
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-corporate-primary mb-4">Disbursement Details</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <div>
                            <p class="text-sm text-gray-500 mb-1">Name</p>
                            <p class="text-base font-medium text-gray-900">{{ $disbursement->name }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 mb-1">Reference Number</p>
                            <p class="text-base font-medium text-gray-900">{{ $disbursement->reference_number }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 mb-1">Status</p>
                            @php
                                $statusClasses = [
                                    'draft' => 'bg-gray-100 text-gray-800',
                                    'pending_approval' => 'bg-corporate-warning bg-opacity-10 text-corporate-warning',
                                    'approved' => 'bg-blue-100 text-blue-800',
                                    'processing' => 'bg-blue-100 text-blue-800',
                                    'completed' => 'bg-corporate-success bg-opacity-10 text-corporate-success',
                                    'partially_completed' => 'bg-corporate-error bg-opacity-10 text-corporate-error',
                                    'failed' => 'bg-red-100 text-red-800',
                                    'cancelled' => 'bg-gray-100 text-gray-800',
                                ];
                                $statusClass = $statusClasses[$disbursement->status] ?? 'bg-gray-100 text-gray-800';
                                $statusLabel = str_replace('_', ' ', ucfirst($disbursement->status));
                            @endphp
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-sm font-medium {{ $statusClass }}">
                                {{ $statusLabel }}
                            </span>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 mb-1">Submission Date</p>
                            <p class="text-base font-medium text-gray-900">{{ $disbursement->created_at->format('M d, Y h:i A') }}</p>
                        </div>
                        @if($disbursement->description)
                            <div class="md:col-span-2">
                                <p class="text-sm text-gray-500 mb-1">Description</p>
                                <p class="text-base text-gray-900">{{ $disbursement->description }}</p>
                            </div>
                        @endif
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div class="bg-gray-50 rounded-lg p-4">
                            <p class="text-sm text-gray-500 mb-1">Total Recipients</p>
                            <p class="text-xl font-bold text-corporate-primary">{{ number_format($disbursement->transaction_count) }}</p>
                        </div>
                        <div class="bg-gray-50 rounded-lg p-4">
                            <p class="text-sm text-gray-500 mb-1">Total Amount</p>
                            <p class="text-xl font-bold text-corporate-primary">{{ $disbursement->currency }} {{ number_format($disbursement->total_amount, 2) }}</p>
                        </div>
                        <div class="bg-gray-50 rounded-lg p-4">
                            <p class="text-sm text-gray-500 mb-1">Total Fee</p>
                            <p class="text-xl font-bold text-corporate-primary">{{ $disbursement->currency }} {{ number_format($disbursement->total_fee, 2) }}</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Next Steps -->
            <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-corporate-primary mb-4">Next Steps</h3>
                    
                    <div class="space-y-4">
                        @if($disbursement->status === 'pending_approval')
                            <div class="flex items-start">
                                <div class="flex-shrink-0 w-8 h-8 rounded-full bg-corporate-primary bg-opacity-10 text-corporate-primary flex items-center justify-center text-sm font-medium mr-3">
                                    1
                                </div>
                                <div>
                                    <h4 class="text-base font-medium text-gray-900">Approval Process</h4>
                                    <p class="text-sm text-gray-600 mt-1">
                                        Your disbursement requires approval from {{ $minApprovers }} {{ Str::plural('approver', $minApprovers) }}. 
                                        Approvers have been notified and will review your request.
                                    </p>
                                </div>
                            </div>
                            
                            <div class="flex items-start">
                                <div class="flex-shrink-0 w-8 h-8 rounded-full bg-corporate-primary bg-opacity-10 text-corporate-primary flex items-center justify-center text-sm font-medium mr-3">
                                    2
                                </div>
                                <div>
                                    <h4 class="text-base font-medium text-gray-900">Processing</h4>
                                    <p class="text-sm text-gray-600 mt-1">
                                        Once approved, your disbursement will be automatically processed. 
                                        You will receive a notification when processing begins.
                                    </p>
                                </div>
                            </div>
                            
                            <div class="flex items-start">
                                <div class="flex-shrink-0 w-8 h-8 rounded-full bg-corporate-primary bg-opacity-10 text-corporate-primary flex items-center justify-center text-sm font-medium mr-3">
                                    3
                                </div>
                                <div>
                                    <h4 class="text-base font-medium text-gray-900">Completion</h4>
                                    <p class="text-sm text-gray-600 mt-1">
                                        You will receive a notification when the disbursement is complete. 
                                        A detailed report will be available for download.
                                    </p>
                                </div>
                            </div>
                        @else
                            <div class="flex items-start">
                                <div class="flex-shrink-0 w-8 h-8 rounded-full bg-corporate-primary bg-opacity-10 text-corporate-primary flex items-center justify-center text-sm font-medium mr-3">
                                    1
                                </div>
                                <div>
                                    <h4 class="text-base font-medium text-gray-900">Processing</h4>
                                    <p class="text-sm text-gray-600 mt-1">
                                        Your disbursement is now being processed. This typically takes 5-15 minutes 
                                        depending on the number of transactions.
                                    </p>
                                </div>
                            </div>
                            
                            <div class="flex items-start">
                                <div class="flex-shrink-0 w-8 h-8 rounded-full bg-corporate-primary bg-opacity-10 text-corporate-primary flex items-center justify-center text-sm font-medium mr-3">
                                    2
                                </div>
                                <div>
                                    <h4 class="text-base font-medium text-gray-900">Tracking</h4>
                                    <p class="text-sm text-gray-600 mt-1">
                                        You can track the progress of your disbursement on the details page. 
                                        The status will update in real-time.
                                    </p>
                                </div>
                            </div>
                            
                            <div class="flex items-start">
                                <div class="flex-shrink-0 w-8 h-8 rounded-full bg-corporate-primary bg-opacity-10 text-corporate-primary flex items-center justify-center text-sm font-medium mr-3">
                                    3
                                </div>
                                <div>
                                    <h4 class="text-base font-medium text-gray-900">Completion</h4>
                                    <p class="text-sm text-gray-600 mt-1">
                                        You will receive a notification when the disbursement is complete. 
                                        A detailed report will be available for download.
                                    </p>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Right Column - Additional Info -->
        <div class="space-y-6">
            <!-- Transaction Summary -->
            <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-corporate-primary mb-4">Transaction Summary</h3>
                    
                    <div class="space-y-3">
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-500">Total Amount:</span>
                            <span class="text-sm font-medium text-gray-900">{{ $disbursement->currency }} {{ number_format($disbursement->total_amount, 2) }}</span>
                        </div>
                            <div class="flex justify-between">
                                <span class="text-sm text-gray-500">Fee ({{ \App\Models\Transaction::getCorporateFeeDescription() }}):</span>
                                <span class="text-sm font-medium text-gray-900">{{ $disbursement->currency }} {{ number_format($disbursement->total_fee, 2) }}</span>
                            </div>
                        <div class="flex justify-between pt-2 border-t border-gray-100">
                            <span class="text-sm font-medium text-gray-700">Total Debit:</span>
                            <span class="text-sm font-bold text-corporate-primary">{{ $disbursement->currency }} {{ number_format($disbursement->total_amount + $disbursement->total_fee, 2) }}</span>
                        </div>
                    </div>
                    
                    <div class="mt-6 pt-6 border-t border-gray-200">
                        <h4 class="font-medium text-corporate-primary mb-3">Wallet Balance</h4>
                        <div class="flex items-center">
                            <div class="w-10 h-10 rounded-full bg-corporate-primary bg-opacity-10 flex items-center justify-center text-corporate-primary mr-3">
                                <i class="fas fa-wallet"></i>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Available Balance</p>
                                <p class="text-lg font-bold text-corporate-primary">{{ $wallet->currency }} {{ number_format($wallet->balance, 2) }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Quick Actions -->
            <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-corporate-primary mb-4">Quick Actions</h3>
                    
                    <div class="space-y-3">
                        <a href="{{ route('corporate.disbursements.show', $disbursement->id) }}" class="flex items-center p-3 bg-corporate-primary bg-opacity-5 rounded-lg hover:bg-opacity-10 transition">
                            <div class="w-10 h-10 rounded-full bg-corporate-primary bg-opacity-10 flex items-center justify-center text-corporate-primary mr-3">
                                <i class="fas fa-eye"></i>
                            </div>
                            <div>
                                <h4 class="font-medium text-corporate-primary">View Details</h4>
                                <p class="text-xs text-gray-500">View complete disbursement details</p>
                            </div>
                        </a>
                        
                        <a href="{{ route('corporate.disbursements.create') }}" class="flex items-center p-3 bg-corporate-primary bg-opacity-5 rounded-lg hover:bg-opacity-10 transition">
                            <div class="w-10 h-10 rounded-full bg-corporate-primary bg-opacity-10 flex items-center justify-center text-corporate-primary mr-3">
                                <i class="fas fa-plus"></i>
                            </div>
                            <div>
                                <h4 class="font-medium text-corporate-primary">New Disbursement</h4>
                                <p class="text-xs text-gray-500">Create another disbursement</p>
                            </div>
                        </a>
                        
                        <a href="{{ route('corporate.wallet.deposit') }}" class="flex items-center p-3 bg-corporate-primary bg-opacity-5 rounded-lg hover:bg-opacity-10 transition">
                            <div class="w-10 h-10 rounded-full bg-corporate-primary bg-opacity-10 flex items-center justify-center text-corporate-primary mr-3">
                                <i class="fas fa-money-bill-wave"></i>
                            </div>
                            <div>
                                <h4 class="font-medium text-corporate-primary">Add Funds</h4>
                                <p class="text-xs text-gray-500">Deposit funds to your wallet</p>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
            
            <!-- Help & Support -->
            <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-corporate-primary mb-4">Help & Support</h3>
                    
                    <div class="bg-blue-50 p-4 rounded-lg">
                        <h4 class="font-medium text-blue-700 mb-2">Need Assistance?</h4>
                        <p class="text-sm text-blue-600 mb-3">
                            If you have any questions or need help with your disbursement, our support team is here to help.
                        </p>
                        <div class="space-y-2">
                            <div class="flex items-center">
                                <i class="fas fa-envelope text-blue-500 w-5"></i>
                                <span class="text-sm text-blue-600 ml-2">support@cardtowallet.com</span>
                            </div>
                            <div class="flex items-center">
                                <i class="fas fa-phone text-blue-500 w-5"></i>
                                <span class="text-sm text-blue-600 ml-2">+260 211 123 456</span>
                            </div>
                            <div class="flex items-center">
                                <i class="fas fa-clock text-blue-500 w-5"></i>
                                <span class="text-sm text-blue-600 ml-2">Mon-Fri, 8:00 AM - 5:00 PM</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
