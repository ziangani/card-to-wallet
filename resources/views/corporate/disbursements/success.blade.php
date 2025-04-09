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
        <h2 class="text-xl font-bold text-corporate-primary">Disbursement Submitted</h2>
        <p class="text-gray-500">Your bulk disbursement has been submitted successfully</p>
    </div>

    <!-- Step Indicator -->
    <div class="bg-white rounded-xl shadow-sm overflow-hidden mb-6">
        <div class="p-6">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <div class="flex items-center">
                        <div class="w-10 h-10 rounded-full bg-corporate-success text-white flex items-center justify-center font-semibold">
                            <i class="fas fa-check"></i>
                        </div>
                        <div class="ml-3">
                            <h3 class="font-medium text-corporate-success">Upload File</h3>
                            <p class="text-xs text-gray-500">Prepare and upload recipient data</p>
                        </div>
                    </div>
                    <div class="h-1 bg-corporate-success mt-3"></div>
                </div>
                <div class="flex-1">
                    <div class="flex items-center">
                        <div class="w-10 h-10 rounded-full bg-corporate-success text-white flex items-center justify-center font-semibold">
                            <i class="fas fa-check"></i>
                        </div>
                        <div class="ml-3">
                            <h3 class="font-medium text-corporate-success">Validate</h3>
                            <p class="text-xs text-gray-500">Review and fix any errors</p>
                        </div>
                    </div>
                    <div class="h-1 bg-corporate-success mt-3"></div>
                </div>
                <div class="flex-1">
                    <div class="flex items-center">
                        <div class="w-10 h-10 rounded-full bg-corporate-success text-white flex items-center justify-center font-semibold">
                            <i class="fas fa-check"></i>
                        </div>
                        <div class="ml-3">
                            <h3 class="font-medium text-corporate-success">Review</h3>
                            <p class="text-xs text-gray-500">Confirm disbursement details</p>
                        </div>
                    </div>
                    <div class="h-1 bg-corporate-success mt-3"></div>
                </div>
                <div class="flex-1">
                    <div class="flex items-center">
                        <div class="w-10 h-10 rounded-full bg-corporate-success text-white flex items-center justify-center font-semibold">
                            <i class="fas fa-check"></i>
                        </div>
                        <div class="ml-3">
                            <h3 class="font-medium text-corporate-success">Submit</h3>
                            <p class="text-xs text-gray-500">Process the disbursement</p>
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
            <div class="text-center mb-6">
                <div class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-corporate-success bg-opacity-10 text-corporate-success mb-4">
                    <i class="fas fa-check-circle text-4xl"></i>
                </div>
                <h3 class="text-xl font-bold text-corporate-primary mb-2">Disbursement Submitted Successfully</h3>
                <p class="text-gray-600">Your bulk disbursement has been submitted and is now pending approval.</p>
            </div>
            
            <div class="bg-corporate-warning bg-opacity-10 text-corporate-warning rounded-lg p-4 mb-6">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <i class="fas fa-exclamation-circle mt-0.5"></i>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-corporate-warning">Approval Required</h3>
                        <div class="text-sm text-corporate-warning opacity-80">
                            <p>This disbursement requires approval before processing. You will be notified once it has been approved.</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                    <h4 class="text-sm font-medium text-gray-500 mb-1">Disbursement Reference</h4>
                    <p class="text-lg font-semibold text-corporate-primary">BD-25040801</p>
                </div>
                <div>
                    <h4 class="text-sm font-medium text-gray-500 mb-1">Status</h4>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-sm font-medium bg-corporate-warning bg-opacity-10 text-corporate-warning">
                        Pending Approval
                    </span>
                </div>
                <div>
                    <h4 class="text-sm font-medium text-gray-500 mb-1">Total Recipients</h4>
                    <p class="text-lg font-semibold text-corporate-primary">345</p>
                </div>
                <div>
                    <h4 class="text-sm font-medium text-gray-500 mb-1">Total Amount</h4>
                    <p class="text-lg font-semibold text-corporate-primary">K 179,836.25</p>
                </div>
                <div>
                    <h4 class="text-sm font-medium text-gray-500 mb-1">Submitted By</h4>
                    <div class="flex items-center">
                        <div class="w-6 h-6 rounded-full bg-corporate-primary text-white flex items-center justify-center text-xs mr-2">
                            JD
                        </div>
                        <p class="text-base font-medium text-gray-900">John Doe</p>
                    </div>
                </div>
                <div>
                    <h4 class="text-sm font-medium text-gray-500 mb-1">Submission Date</h4>
                    <p class="text-base font-medium text-gray-900">April 8, 2025 - 4:30 PM</p>
                </div>
            </div>
            
            <div class="border-t border-gray-200 pt-6">
                <h4 class="font-medium text-corporate-primary mb-3">Next Steps</h4>
                <ol class="space-y-4 text-sm text-gray-600">
                    <li class="flex">
                        <div class="flex-shrink-0 w-6 h-6 rounded-full bg-corporate-warning text-white flex items-center justify-center text-xs mr-3">
                            1
                        </div>
                        <div>
                            <p class="font-medium text-gray-900">Approval Process</p>
                            <p>The disbursement will be reviewed by one or more approvers based on your company's approval policy.</p>
                        </div>
                    </li>
                    <li class="flex">
                        <div class="flex-shrink-0 w-6 h-6 rounded-full bg-corporate-warning text-white flex items-center justify-center text-xs mr-3">
                            2
                        </div>
                        <div>
                            <p class="font-medium text-gray-900">Processing</p>
                            <p>Once approved, the disbursement will be processed and funds will be sent to the recipients.</p>
                        </div>
                    </li>
                    <li class="flex">
                        <div class="flex-shrink-0 w-6 h-6 rounded-full bg-corporate-warning text-white flex items-center justify-center text-xs mr-3">
                            3
                        </div>
                        <div>
                            <p class="font-medium text-gray-900">Completion</p>
                            <p>You will receive a notification when the disbursement is completed, along with a detailed report.</p>
                        </div>
                    </li>
                </ol>
            </div>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="flex flex-col md:flex-row space-y-3 md:space-y-0 md:space-x-3">
        <a href="{{ route('corporate.disbursements.show', 'BD-25040801') }}" class="flex-1 px-4 py-3 bg-corporate-primary text-white rounded-lg text-center hover:bg-opacity-90 transition">
            <i class="fas fa-eye mr-2"></i> View Disbursement Details
        </a>
        <a href="{{ route('corporate.approvals.index') }}" class="flex-1 px-4 py-3 border border-corporate-warning text-corporate-warning rounded-lg text-center hover:bg-corporate-warning hover:text-white transition">
            <i class="fas fa-check-double mr-2"></i> View Pending Approvals
        </a>
        <a href="{{ route('corporate.dashboard') }}" class="flex-1 px-4 py-3 border border-gray-300 text-gray-700 rounded-lg text-center hover:bg-gray-50 transition">
            <i class="fas fa-home mr-2"></i> Return to Dashboard
        </a>
    </div>
@endsection
