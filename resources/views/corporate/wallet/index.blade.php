@extends('corporate.layouts.app')

@section('title', 'Corporate Wallet')

@section('content')
<div class="row">
    <!-- Balance Card -->
    <div class="col-md-6 col-lg-4">
        <div class="balance-card">
            <div class="balance-label">Current Balance</div>
            <div class="balance-amount">K {{ number_format($wallet->balance, 2) }}</div>
            <div class="mt-3">
                <a href="{{ route('corporate.wallet.deposit') }}" class="btn btn-light btn-sm me-2">Deposit Funds</a>
                <a href="{{ route('corporate.disbursements.create') }}" class="btn btn-outline-light btn-sm">New Disbursement</a>
            </div>
        </div>
    </div>
    
    <!-- Rate Information -->
    <div class="col-md-6 col-lg-8">
        <div class="card corporate-card">
            <div class="card-header">
                <h6 class="mb-0">Rate Information</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <div class="text-muted small">Current Rate Tier</div>
                            <div class="h5">{{ $rateAssignment->rateTier->name }}</div>
                        </div>
                        <div class="mb-3">
                            <div class="text-muted small">Fee Percentage</div>
                            <div class="h5">{{ $rateAssignment->getEffectiveFeePercentage() }}%</div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <div class="text-muted small">Monthly Volume</div>
                            <div class="h5">K {{ number_format($monthlyVolume, 2) }}</div>
                        </div>
                        @if($nextTier)
                            <div class="mb-3">
                                <div class="text-muted small">Volume to Next Tier ({{ $nextTier->name }})</div>
                                <div class="h5">K {{ number_format($volumeToNextTier, 2) }}</div>
                            </div>
                        @else
                            <div class="mb-3">
                                <div class="text-muted small">Tier Status</div>
                                <div class="h5">Highest Tier</div>
                            </div>
                        @endif
                    </div>
                </div>
                
                @if($nextTier)
                    <div class="progress mt-2" style="height: 10px;">
                        @php
                            $percentage = min(100, ($monthlyVolume / $nextTier->monthly_volume_minimum) * 100);
                        @endphp
                        <div class="progress-bar bg-primary" role="progressbar" style="width: {{ $percentage }}%;" aria-valuenow="{{ $percentage }}" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                    <div class="d-flex justify-content-between mt-1">
                        <div class="small">{{ $rateAssignment->rateTier->name }} ({{ $rateAssignment->getEffectiveFeePercentage() }}%)</div>
                        <div class="small">{{ $nextTier->name }} ({{ $nextTier->fee_percentage }}%)</div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<div class="row mt-4">
    <!-- Recent Transactions -->
    <div class="col-12">
        <div class="card corporate-card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h6 class="mb-0">Recent Transactions</h6>
                <div>
                    <a href="{{ route('corporate.wallet.transactions') }}" class="btn btn-sm btn-outline-primary me-2">View All</a>
                    <a href="{{ route('corporate.wallet.deposit') }}" class="btn btn-sm btn-corporate-accent">Deposit Funds</a>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover data-table mb-0">
                        <thead>
                            <tr>
                                <th>Reference</th>
                                <th>Date</th>
                                <th>Type</th>
                                <th>Description</th>
                                <th>Amount</th>
                                <th>Balance</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentTransactions as $transaction)
                                <tr>
                                    <td>{{ $transaction->reference_number }}</td>
                                    <td>{{ $transaction->created_at->format('M d, Y H:i') }}</td>
                                    <td>
                                        @switch($transaction->transaction_type)
                                            @case('deposit')
                                                <span class="badge bg-success">Deposit</span>
                                                @break
                                            @case('withdrawal')
                                                <span class="badge bg-danger">Withdrawal</span>
                                                @break
                                            @case('transfer')
                                                <span class="badge bg-primary">Transfer</span>
                                                @break
                                            @case('fee')
                                                <span class="badge bg-warning">Fee</span>
                                                @break
                                            @case('adjustment')
                                                <span class="badge bg-info">Adjustment</span>
                                                @break
                                            @default
                                                <span class="badge bg-secondary">{{ ucfirst($transaction->transaction_type) }}</span>
                                        @endswitch
                                    </td>
                                    <td>{{ $transaction->description }}</td>
                                    <td class="{{ $transaction->transaction_type == 'deposit' ? 'text-success' : 'text-danger' }}">
                                        {{ $transaction->transaction_type == 'deposit' ? '+' : '-' }} K {{ number_format(abs($transaction->amount), 2) }}
                                    </td>
                                    <td>K {{ number_format($transaction->balance_after, 2) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-3">No transactions found</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row mt-4">
    <!-- Deposit Instructions -->
    <div class="col-md-6">
        <div class="card corporate-card">
            <div class="card-header">
                <h6 class="mb-0">Deposit Instructions</h6>
            </div>
            <div class="card-body">
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i> Funds will be credited to your wallet once the deposit is confirmed.
                </div>
                
                <h6 class="mb-3">Bank Transfer</h6>
                <div class="mb-3">
                    <div class="text-muted small">Bank Name</div>
                    <div class="fw-bold">First National Bank Zambia</div>
                </div>
                <div class="mb-3">
                    <div class="text-muted small">Account Name</div>
                    <div class="fw-bold">Card to Wallet Ltd</div>
                </div>
                <div class="mb-3">
                    <div class="text-muted small">Account Number</div>
                    <div class="fw-bold">62345678901</div>
                </div>
                <div class="mb-3">
                    <div class="text-muted small">Branch Code</div>
                    <div class="fw-bold">260001</div>
                </div>
                <div class="mb-3">
                    <div class="text-muted small">Reference</div>
                    <div class="fw-bold">{{ Auth::user()->company->registration_number }}</div>
                </div>
                
                <div class="mt-4">
                    <a href="{{ route('corporate.wallet.deposit') }}" class="btn btn-corporate-accent">Notify Us of Deposit</a>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Pending Deposits -->
    <div class="col-md-6">
        <div class="card corporate-card">
            <div class="card-header">
                <h6 class="mb-0">Pending Deposits</h6>
            </div>
            <div class="card-body p-0">
                @if(count($pendingDeposits) > 0)
                    <div class="list-group list-group-flush">
                        @foreach($pendingDeposits as $deposit)
                            <div class="list-group-item px-3 py-3">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <div class="fw-bold">K {{ number_format($deposit->amount, 2) }}</div>
                                        <div class="text-muted small">{{ $deposit->description }}</div>
                                        <div class="text-muted small">Ref: {{ $deposit->reference_number }}</div>
                                    </div>
                                    <div class="text-end">
                                        <span class="badge bg-warning">Pending</span>
                                        <div class="text-muted small">{{ $deposit->created_at->format('M d, Y') }}</div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="p-4 text-center">
                        <i class="fas fa-check-circle text-success mb-3" style="font-size: 2rem;"></i>
                        <p class="mb-0">No pending deposits</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
