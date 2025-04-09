@extends('corporate.layouts.app')

@section('title', 'Corporate Dashboard')

@section('content')
<div class="row">
    <!-- Balance Card -->
    <div class="col-md-6 col-lg-4">
        <div class="balance-card">
            <div class="balance-label">Current Balance</div>
            <div class="balance-amount">K {{ number_format($balance, 2) }}</div>
            <div class="mt-3">
                <a href="{{ route('corporate.wallet.deposit') }}" class="btn btn-light btn-sm me-2">Deposit Funds</a>
                <a href="{{ route('corporate.wallet.transactions') }}" class="btn btn-outline-light btn-sm">View Transactions</a>
            </div>
        </div>
    </div>
    
    <!-- Stats Cards -->
    <div class="col-md-6 col-lg-8">
        <div class="row">
            <!-- Monthly Volume -->
            <div class="col-md-6">
                <div class="card corporate-card h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h6 class="card-title mb-0">Monthly Volume</h6>
                            <i class="fas fa-chart-line text-primary"></i>
                        </div>
                        <h3 class="mb-1">K {{ number_format($monthlyVolume, 2) }}</h3>
                        <div class="text-muted small">This month</div>
                    </div>
                </div>
            </div>
            
            <!-- Recipients -->
            <div class="col-md-6">
                <div class="card corporate-card h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h6 class="card-title mb-0">Recipients</h6>
                            <i class="fas fa-users text-primary"></i>
                        </div>
                        <h3 class="mb-1">{{ number_format($recipientCount) }}</h3>
                        <div class="text-muted small">This month</div>
                    </div>
                </div>
            </div>
            
            <!-- Pending Approvals -->
            <div class="col-md-6 mt-3">
                <div class="card corporate-card h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h6 class="card-title mb-0">Pending Approvals</h6>
                            <i class="fas fa-check-double text-primary"></i>
                        </div>
                        <h3 class="mb-1">{{ $pendingApprovalsCount }}</h3>
                        <div class="text-muted small">
                            @if($pendingApprovalsCount > 0)
                                <a href="{{ route('corporate.approvals.index') }}" class="text-primary">View all</a>
                            @else
                                No pending approvals
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Quick Actions -->
            <div class="col-md-6 mt-3">
                <div class="card corporate-card h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h6 class="card-title mb-0">Quick Actions</h6>
                            <i class="fas fa-bolt text-primary"></i>
                        </div>
                        <div class="d-grid gap-2">
                            <a href="{{ route('corporate.disbursements.create') }}" class="btn btn-sm btn-corporate-accent">New Disbursement</a>
                            <a href="{{ route('corporate.reports.generate') }}" class="btn btn-sm btn-outline-secondary">Generate Report</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row mt-4">
    <!-- Recent Disbursements -->
    <div class="col-lg-8">
        <div class="card corporate-card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h6 class="mb-0">Recent Disbursements</h6>
                <a href="{{ route('corporate.disbursements.index') }}" class="btn btn-sm btn-outline-primary">View All</a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover data-table mb-0">
                        <thead>
                            <tr>
                                <th>Reference</th>
                                <th>Date</th>
                                <th>Recipients</th>
                                <th>Amount</th>
                                <th>Status</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentDisbursements as $disbursement)
                                <tr>
                                    <td>{{ $disbursement->reference_number }}</td>
                                    <td>{{ $disbursement->created_at->format('M d, Y') }}</td>
                                    <td>{{ $disbursement->transaction_count }}</td>
                                    <td>K {{ number_format($disbursement->total_amount, 2) }}</td>
                                    <td>
                                        <span class="status-badge {{ strtolower($disbursement->status) }}">
                                            {{ ucfirst($disbursement->status) }}
                                        </span>
                                    </td>
                                    <td>
                                        <a href="{{ route('corporate.disbursements.show', $disbursement->id) }}" class="btn btn-sm btn-link">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-3">No disbursements found</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Recent Activity -->
    <div class="col-lg-4">
        <div class="card corporate-card">
            <div class="card-header">
                <h6 class="mb-0">Recent Activity</h6>
            </div>
            <div class="card-body p-0">
                <div class="list-group list-group-flush">
                    @foreach($recentActivity as $activity)
                        <div class="list-group-item px-3 py-3">
                            <div class="d-flex">
                                <div class="activity-icon me-3">
                                    @switch($activity['type'])
                                        @case('disbursement_created')
                                            <i class="fas fa-money-bill-wave text-primary"></i>
                                            @break
                                        @case('disbursement_approved')
                                            <i class="fas fa-check-circle text-success"></i>
                                            @break
                                        @case('wallet_deposit')
                                            <i class="fas fa-wallet text-info"></i>
                                            @break
                                        @case('rate_tier_changed')
                                            <i class="fas fa-chart-line text-warning"></i>
                                            @break
                                        @default
                                            <i class="fas fa-bell text-secondary"></i>
                                    @endswitch
                                </div>
                                <div class="flex-grow-1">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div>
                                            <div class="fw-bold">{{ $activity['description'] }}</div>
                                            <div class="text-muted small">{{ $activity['details'] }}</div>
                                        </div>
                                        <div class="text-muted small">{{ $activity['timestamp']->diffForHumans() }}</div>
                                    </div>
                                    <div class="text-muted small mt-1">By {{ $activity['user'] }}</div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
