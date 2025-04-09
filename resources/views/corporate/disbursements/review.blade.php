@extends('corporate.layouts.app')

@section('title', 'Review Disbursement - ' . config('app.name'))
@section('meta_description', 'Review and confirm your bulk disbursement')
@section('header_title', 'Review Disbursement')

@section('content')
    <div class="mb-6">
        <div class="flex items-center mb-2">
            <a href="{{ route('corporate.disbursements.validate') }}" class="text-corporate-primary hover:underline">
                <i class="fas fa-arrow-left mr-2"></i> Back to Validation
            </a>
        </div>
        <h2 class="text-xl font-bold text-corporate-primary">Review Disbursement</h2>
        <p class="text-gray-500">Confirm the details before submitting your bulk disbursement</p>
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
                        <div class="w-10 h-10 rounded-full bg-corporate-primary text-white flex items-center justify-center font-semibold">3</div>
                        <div class="ml-3">
                            <h3 class="font-medium text-corporate-primary">Review</h3>
                            <p class="text-xs text-gray-500">Confirm disbursement details</p>
                        </div>
                    </div>
                    <div class="h-1 bg-corporate-primary mt-3"></div>
                </div>
                <div class="flex-1">
                    <div class="flex items-center">
                        <div class="w-10 h-10 rounded-full bg-gray-200 text-gray-500 flex items-center justify-center font-semibold">4</div>
                        <div class="ml-3">
                            <h3 class="font-medium text-gray-500">Submit</h3>
                            <p class="text-xs text-gray-500">Process the disbursement</p>
                        </div>
                    </div>
                    <div class="h-1 bg-gray-200 mt-3"></div>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Disbursement Details -->
            <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-corporate-primary mb-4">Disbursement Details</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <p class="text-sm text-gray-500 mb-1">Disbursement Name</p>
                            <p class="text-base font-medium text-gray-900">April Salaries</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 mb-1">Reference Number</p>
                            <p class="text-base font-medium text-gray-900">BD-25040801</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 mb-1">Created By</p>
                            <div class="flex items-center">
                                <div class="w-6 h-6 rounded-full bg-corporate-primary text-white flex items-center justify-center text-xs mr-2">
                                    JD
                                </div>
                                <p class="text-base font-medium text-gray-900">John Doe</p>
                            </div>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 mb-1">Date</p>
                            <p class="text-base font-medium text-gray-900">April 8, 2025</p>
                        </div>
                        <div class="md:col-span-2">
                            <p class="text-sm text-gray-500 mb-1">Description</p>
                            <p class="text-base text-gray-900">Monthly salary payments for all staff members.</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recipients Preview -->
            <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                <div class="p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold text-corporate-primary">Recipients Preview</h3>
                        <div class="flex items-center">
                            <span class="text-sm text-gray-500 mr-2">Showing 10 of 345 recipients</span>
                            <a href="#" class="text-sm text-corporate-accent hover:underline">View All</a>
                        </div>
                    </div>
                    
                    <div class="overflow-x-auto">
                        <table class="min-w-full corporate-table">
                            <thead>
                                <tr class="bg-gray-50">
                                    <th class="text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Mobile Number</th>
                                    <th class="text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Provider</th>
                                    <th class="text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Recipient</th>
                                    <th class="text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                <tr>
                                    <td class="whitespace-nowrap">
                                        <div class="text-sm text-gray-900">+260 977123456</div>
                                    </td>
                                    <td>
                                        <div class="flex items-center">
                                            <img src="{{ asset('assets/img/mtn.jpg') }}" alt="MTN" class="w-6 h-6 rounded-full mr-2">
                                            <div class="text-sm text-gray-900">MTN</div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="text-sm text-gray-900">John Banda</div>
                                    </td>
                                    <td class="text-right">
                                        <div class="text-sm font-medium text-gray-900">K 500.00</div>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="whitespace-nowrap">
                                        <div class="text-sm text-gray-900">+260 966123456</div>
                                    </td>
                                    <td>
                                        <div class="flex items-center">
                                            <img src="{{ asset('assets/img/airtel.png') }}" alt="Airtel" class="w-6 h-6 rounded-full mr-2">
                                            <div class="text-sm text-gray-900">AIRTEL</div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="text-sm text-gray-900">Mary Phiri</div>
                                    </td>
                                    <td class="text-right">
                                        <div class="text-sm font-medium text-gray-900">K 750.00</div>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="whitespace-nowrap">
                                        <div class="text-sm text-gray-900">+260 977456789</div>
                                    </td>
                                    <td>
                                        <div class="flex items-center">
                                            <img src="{{ asset('assets/img/mtn.jpg') }}" alt="MTN" class="w-6 h-6 rounded-full mr-2">
                                            <div class="text-sm text-gray-900">MTN</div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="text-sm text-gray-900">David Mulenga</div>
                                    </td>
                                    <td class="text-right">
                                        <div class="text-sm font-medium text-gray-900">K 600.00</div>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="whitespace-nowrap">
                                        <div class="text-sm text-gray-900">+260 955123456</div>
                                    </td>
                                    <td>
                                        <div class="flex items-center">
                                            <img src="{{ asset('assets/img/zamtel.jpg') }}" alt="Zamtel" class="w-6 h-6 rounded-full mr-2">
                                            <div class="text-sm text-gray-900">ZAMTEL</div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="text-sm text-gray-900">Sarah Tembo</div>
                                    </td>
                                    <td class="text-right">
                                        <div class="text-sm font-medium text-gray-900">K 450.00</div>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="whitespace-nowrap">
                                        <div class="text-sm text-gray-900">+260 977987654</div>
                                    </td>
                                    <td>
                                        <div class="flex items-center">
                                            <img src="{{ asset('assets/img/mtn.jpg') }}" alt="MTN" class="w-6 h-6 rounded-full mr-2">
                                            <div class="text-sm text-gray-900">MTN</div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="text-sm text-gray-900">James Zulu</div>
                                    </td>
                                    <td class="text-right">
                                        <div class="text-sm font-medium text-gray-900">K 550.00</div>
                                    </td>
                                </tr>
                                <!-- More rows would be here -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Sidebar -->
        <div class="space-y-6">
            <!-- Summary Card -->
            <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-corporate-primary mb-4">Disbursement Summary</h3>
                    
                    <div class="space-y-4">
                        <div class="flex justify-between items-center pb-3 border-b border-gray-200">
                            <span class="text-gray-600">Total Recipients</span>
                            <span class="font-medium text-gray-900">345</span>
                        </div>
                        <div class="flex justify-between items-center pb-3 border-b border-gray-200">
                            <span class="text-gray-600">Subtotal</span>
                            <span class="font-medium text-gray-900">K 175,450.00</span>
                        </div>
                        <div class="flex justify-between items-center pb-3 border-b border-gray-200">
                            <div>
                                <span class="text-gray-600">Fee</span>
                                <span class="text-xs text-gray-500 block">(2.5% - Gold Tier)</span>
                            </div>
                            <span class="font-medium text-gray-900">K 4,386.25</span>
                        </div>
                        <div class="flex justify-between items-center pt-2">
                            <span class="text-lg font-semibold text-corporate-primary">Total Amount</span>
                            <span class="text-lg font-bold text-corporate-primary">K 179,836.25</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Wallet Balance -->
            <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-corporate-primary mb-4">Wallet Balance</h3>
                    
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <p class="text-sm text-gray-500 mb-1">Available Balance</p>
                            <p class="text-2xl font-bold text-corporate-primary">K 250,000.00</p>
                        </div>
                        <div class="w-12 h-12 rounded-full bg-corporate-primary bg-opacity-10 flex items-center justify-center text-corporate-primary">
                            <i class="fas fa-wallet"></i>
                        </div>
                    </div>
                    
                    <div class="bg-corporate-success bg-opacity-10 text-corporate-success rounded-lg p-3 text-sm">
                        <i class="fas fa-check-circle mr-2"></i> Sufficient balance for this disbursement
                    </div>
                </div>
            </div>

            <!-- Approval Requirements -->
            <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-corporate-primary mb-4">Approval Requirements</h3>
                    
                    <div class="bg-corporate-warning bg-opacity-10 text-corporate-warning rounded-lg p-3 text-sm mb-4">
                        <i class="fas fa-exclamation-circle mr-2"></i> This disbursement requires approval from at least 1 approver
                    </div>
                    
                    <p class="text-sm text-gray-600 mb-4">Based on your company's approval policy, disbursements over K50,000 require approval before processing.</p>
                    
                    <div class="space-y-3">
                        <div class="flex items-center">
                            <div class="w-8 h-8 rounded-full bg-corporate-primary text-white flex items-center justify-center text-xs mr-3">
                                JS
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-900">Jane Smith</p>
                                <p class="text-xs text-gray-500">Approver</p>
                            </div>
                        </div>
                        <div class="flex items-center">
                            <div class="w-8 h-8 rounded-full bg-corporate-primary text-white flex items-center justify-center text-xs mr-3">
                                RM
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-900">Robert Mumba</p>
                                <p class="text-xs text-gray-500">Approver</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                <div class="p-6">
                    <form action="{{ route('corporate.disbursements.submit') }}" method="POST">
                        @csrf
                        <input type="hidden" name="disbursement_id" value="BD-25040801">
                        
                        <div class="mb-4">
                            <label for="notes" class="block text-sm font-medium text-gray-700 mb-1">Notes (Optional)</label>
                            <textarea id="notes" name="notes" rows="2" class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-corporate-primary focus:border-corporate-primary" placeholder="Add any notes for approvers"></textarea>
                        </div>
                        
                        <div class="flex flex-col space-y-3">
                            <button type="submit" class="w-full px-4 py-2 bg-corporate-primary text-white rounded-lg hover:bg-opacity-90 transition">
                                Submit for Approval
                            </button>
                            <a href="{{ route('corporate.disbursements.index') }}" class="w-full px-4 py-2 border border-gray-300 text-center text-gray-700 rounded-lg hover:bg-gray-50 transition">
                                Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
