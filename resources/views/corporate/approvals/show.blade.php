@extends('corporate.layouts.app')

@section('title', 'Approval Details')

@section('content')
<div class="mb-6">
    <div class="flex items-center mb-2">
        <a href="{{ route('corporate.approvals.index') }}" class="text-primary hover:underline">
            <i class="fas fa-arrow-left mr-2"></i> Back to Approvals
        </a>
    </div>
    <h2 class="text-xl font-bold text-gray-800">Approval Request Details</h2>
    <p class="text-gray-500">Review and manage this approval request</p>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Main Content -->
    <div class="lg:col-span-2 space-y-6">
        <!-- Request Details -->
        <div class="bg-white rounded-xl shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="font-semibold text-gray-800">Request Information</h3>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <p class="text-sm text-gray-500 mb-1">Request ID</p>
                        <p class="text-base font-medium text-gray-900">{{ $approvalRequest->uuid }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 mb-1">Status</p>
                        <div>
                            @php
                                $statusClasses = [
                                    'pending' => 'bg-yellow-100 text-yellow-800',
                                    'approved' => 'bg-green-100 text-green-800',
                                    'rejected' => 'bg-red-100 text-red-800',
                                    'cancelled' => 'bg-gray-100 text-gray-800',
                                ];
                                $statusClass = $statusClasses[$approvalRequest->status] ?? 'bg-gray-100 text-gray-800';
                            @endphp
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statusClass }}">
                                {{ ucfirst($approvalRequest->status) }}
                            </span>
                        </div>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 mb-1">Request Type</p>
                        <p class="text-base font-medium text-gray-900">{{ $entityDetails['title'] }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 mb-1">Requested By</p>
                        <div class="flex items-center">
                            <div class="w-8 h-8 rounded-full bg-primary text-white flex items-center justify-center text-xs mr-2">
                                {{ strtoupper(substr($approvalRequest->requester->name ?? 'U', 0, 1)) }}
                            </div>
                            <div>
                                <p class="text-base font-medium text-gray-900">{{ $approvalRequest->requester->name ?? 'Unknown User' }}</p>
                            </div>
                        </div>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 mb-1">Date Requested</p>
                        <p class="text-base font-medium text-gray-900">{{ $approvalRequest->created_at->format('M d, Y h:i A') }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 mb-1">Approvals</p>
                        <p class="text-base font-medium text-gray-900">{{ $approvalRequest->received_approvals }}/{{ $approvalRequest->required_approvals }} Required</p>
                    </div>
                    @if($approvalRequest->expires_at)
                    <div>
                        <p class="text-sm text-gray-500 mb-1">Expires</p>
                        <p class="text-base font-medium text-gray-900">{{ $approvalRequest->expires_at->format('M d, Y h:i A') }}</p>
                    </div>
                    @endif
                    @if($approvalRequest->completed_at)
                    <div>
                        <p class="text-sm text-gray-500 mb-1">Completed</p>
                        <p class="text-base font-medium text-gray-900">{{ $approvalRequest->completed_at->format('M d, Y h:i A') }}</p>
                    </div>
                    @endif
                </div>
                
                <div class="mt-6">
                    <p class="text-sm text-gray-500 mb-1">Description</p>
                    <p class="text-base text-gray-900">{{ $approvalRequest->description }}</p>
                </div>
            </div>
        </div>
        
        <!-- Entity Details -->
        <div class="bg-white rounded-xl shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="font-semibold text-gray-800">{{ $entityDetails['title'] }} Details</h3>
            </div>
            <div class="p-6">
                <div class="mb-4">
                    <h4 class="text-lg font-medium text-gray-900 mb-2">{{ $entityDetails['description'] }}</h4>
                </div>
                
                <div class="bg-gray-50 rounded-lg p-4">
                    <dl class="grid grid-cols-1 md:grid-cols-2 gap-x-4 gap-y-3">
                        @foreach($entityDetails['details'] as $label => $value)
                            <div class="sm:col-span-1">
                                <dt class="text-sm font-medium text-gray-500">{{ $label }}</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $value }}</dd>
                            </div>
                        @endforeach
                    </dl>
                </div>
                
                @if($approvalRequest->entity_type === 'bulk_disbursement' && $entity)
                    <div class="mt-6">
                        <a href="{{ route('corporate.disbursements.show', $entity->id) }}" class="text-primary hover:underline">
                            <i class="fas fa-external-link-alt mr-1"></i> View Full Disbursement Details
                        </a>
                    </div>
                @endif
            </div>
        </div>
        
        <!-- Approval Actions -->
        <div class="bg-white rounded-xl shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="font-semibold text-gray-800">Approval Actions</h3>
            </div>
            
            @if($approvalActions->isEmpty())
                <div class="p-6 text-center">
                    <div class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-gray-100 text-gray-400 mb-3">
                        <i class="fas fa-clipboard-list"></i>
                    </div>
                    <h4 class="text-base font-medium text-gray-900 mb-1">No actions yet</h4>
                    <p class="text-sm text-gray-500">This request is waiting for approvals.</p>
                </div>
            @else
                <div class="p-6">
                    <div class="flow-root">
                        <ul class="-mb-8">
                            @foreach($approvalActions as $action)
                                <li>
                                    <div class="relative pb-8">
                                        @if(!$loop->last)
                                            <span class="absolute top-5 left-5 -ml-px h-full w-0.5 bg-gray-200" aria-hidden="true"></span>
                                        @endif
                                        <div class="relative flex items-start space-x-3">
                                            <div class="relative">
                                                @if($action->action === 'approved')
                                                    <div class="h-10 w-10 rounded-full bg-green-100 flex items-center justify-center ring-8 ring-white">
                                                        <i class="fas fa-check text-green-600"></i>
                                                    </div>
                                                @else
                                                    <div class="h-10 w-10 rounded-full bg-red-100 flex items-center justify-center ring-8 ring-white">
                                                        <i class="fas fa-times text-red-600"></i>
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="min-w-0 flex-1">
                                                <div>
                                                    <div class="text-sm">
                                                        <span class="font-medium text-gray-900">{{ $action->approver->name }}</span>
                                                        <span class="text-gray-500">
                                                            {{ $action->action === 'approved' ? 'approved' : 'rejected' }} this request
                                                        </span>
                                                    </div>
                                                    <p class="mt-0.5 text-sm text-gray-500">
                                                        {{ $action->created_at->format('M d, Y h:i A') }}
                                                    </p>
                                                </div>
                                                @if($action->comments)
                                                    <div class="mt-2 text-sm text-gray-700">
                                                        <p>{{ $action->comments }}</p>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif
        </div>
    </div>
    
    <!-- Sidebar -->
    <div class="space-y-6">
        <!-- Status Card -->
        <div class="bg-white rounded-xl shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="font-semibold text-gray-800">Status</h3>
            </div>
            <div class="p-6">
                @if($approvalRequest->status === 'pending')
                    <div class="flex items-center mb-4">
                        <div class="w-12 h-12 rounded-full bg-yellow-100 flex items-center justify-center text-yellow-600 mr-4">
                            <i class="fas fa-clock text-xl"></i>
                        </div>
                        <div>
                            <h4 class="text-lg font-medium text-gray-900">Pending Approval</h4>
                            <p class="text-sm text-gray-500">This request needs {{ $approvalRequest->required_approvals - $approvalRequest->received_approvals }} more approval(s).</p>
                        </div>
                    </div>
                    
                    @if($canApprove && !$hasApproved && !$isRequester)
                        <div class="space-y-3">
                            <form action="{{ route('corporate.approvals.approve', $approvalRequest->id) }}" method="POST">
                                @csrf
                                <div class="mb-3">
                                    <label for="comments" class="block text-sm font-medium text-gray-700 mb-1">Comments (Optional)</label>
                                    <textarea id="comments" name="comments" rows="2" class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-primary focus:border-primary"></textarea>
                                </div>
                                <button type="submit" class="w-full px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                                    <i class="fas fa-check mr-2"></i> Approve Request
                                </button>
                            </form>
                            
                            <form action="{{ route('corporate.approvals.reject', $approvalRequest->id) }}" method="POST">
                                @csrf
                                <div class="mb-3">
                                    <label for="reject_comments" class="block text-sm font-medium text-gray-700 mb-1">Rejection Reason (Optional)</label>
                                    <textarea id="reject_comments" name="comments" rows="2" class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-primary focus:border-primary"></textarea>
                                </div>
                                <button type="submit" class="w-full px-4 py-2 bg-white border border-red-600 text-red-600 rounded-lg hover:bg-red-50">
                                    <i class="fas fa-times mr-2"></i> Reject Request
                                </button>
                            </form>
                        </div>
                    @elseif($hasApproved)
                        <div class="bg-green-50 border border-green-200 rounded-lg p-4 text-sm text-green-800">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-check-circle text-green-600"></i>
                                </div>
                                <div class="ml-3">
                                    <p>You have already approved this request.</p>
                                </div>
                            </div>
                        </div>
                    @elseif($isRequester)
                        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 text-sm text-yellow-800">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-info-circle text-yellow-600"></i>
                                </div>
                                <div class="ml-3">
                                    <p>You cannot approve your own request.</p>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="bg-gray-50 border border-gray-200 rounded-lg p-4 text-sm text-gray-800">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-info-circle text-gray-600"></i>
                                </div>
                                <div class="ml-3">
                                    <p>You do not have permission to approve this request.</p>
                                </div>
                            </div>
                        </div>
                    @endif
                @elseif($approvalRequest->status === 'approved')
                    <div class="flex items-center mb-4">
                        <div class="w-12 h-12 rounded-full bg-green-100 flex items-center justify-center text-green-600 mr-4">
                            <i class="fas fa-check text-xl"></i>
                        </div>
                        <div>
                            <h4 class="text-lg font-medium text-gray-900">Approved</h4>
                            <p class="text-sm text-gray-500">This request has been approved and processed.</p>
                        </div>
                    </div>
                @elseif($approvalRequest->status === 'rejected')
                    <div class="flex items-center mb-4">
                        <div class="w-12 h-12 rounded-full bg-red-100 flex items-center justify-center text-red-600 mr-4">
                            <i class="fas fa-times text-xl"></i>
                        </div>
                        <div>
                            <h4 class="text-lg font-medium text-gray-900">Rejected</h4>
                            <p class="text-sm text-gray-500">This request has been rejected.</p>
                        </div>
                    </div>
                @elseif($approvalRequest->status === 'cancelled')
                    <div class="flex items-center mb-4">
                        <div class="w-12 h-12 rounded-full bg-gray-100 flex items-center justify-center text-gray-600 mr-4">
                            <i class="fas fa-ban text-xl"></i>
                        </div>
                        <div>
                            <h4 class="text-lg font-medium text-gray-900">Cancelled</h4>
                            <p class="text-sm text-gray-500">This request has been cancelled.</p>
                        </div>
                    </div>
                @endif
            </div>
        </div>
        
        <!-- Requester Info -->
        <div class="bg-white rounded-xl shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="font-semibold text-gray-800">Requester</h3>
            </div>
            <div class="p-6">
                <div class="flex items-center">
                    <div class="w-12 h-12 rounded-full bg-primary text-white flex items-center justify-center text-lg mr-4">
                        {{ strtoupper(substr($approvalRequest->requester->name ?? 'U', 0, 1)) }}
                    </div>
                    <div>
                        <h4 class="text-lg font-medium text-gray-900">{{ $approvalRequest->requester->name ?? 'Unknown User' }}</h4>
                        <p class="text-sm text-gray-500">{{ $approvalRequest->requester->email ?? '' }}</p>
                    </div>
                </div>
                
                @if($approvalRequest->requester)
                    <div class="mt-4 pt-4 border-t border-gray-200">
                        <div class="flex items-center text-sm text-gray-500">
                            <i class="fas fa-user-tag mr-2"></i>
                            <span>
                                @php
                                    $primaryRole = $approvalRequest->requester->corporateUserRoles()
                                        ->where('company_id', $company->id)
                                        ->where('is_primary', true)
                                        ->first();
                                @endphp
                                {{ $primaryRole ? $primaryRole->role->name : 'No primary role' }}
                            </span>
                        </div>
                        <div class="flex items-center text-sm text-gray-500 mt-2">
                            <i class="fas fa-calendar-alt mr-2"></i>
                            <span>Member since {{ $approvalRequest->requester->created_at->format('M Y') }}</span>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
