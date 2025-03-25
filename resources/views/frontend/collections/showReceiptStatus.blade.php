@extends('layouts.frontend')

@section('body')

    <div class="main-content">
        @include('frontend.partials.navigation')
        <div class="page-wrapper">
            <div class="page-header d-print-none">
                <div class="container">
                    <div class="row g-3 align-items-center">
                        @if($transaction->status == 'COMPLETE')
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon mb-2 text-green icon-lg" width="24"
                                 height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                 stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                <circle cx="12" cy="12" r="9"/>
                                <path d="M9 12l2 2l4 -4"/>
                            </svg>
                        @elseif($transaction->status == 'FAILED')
                            <svg xmlns="http://www.w3.org/2000/svg"
                                 class="icon icon-tabler icon-tabler-exclamation-circle" width="24" height="24"
                                 viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                 stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                <path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0"></path>
                                <path d="M12 9v4"></path>
                                <path d="M12 16v.01"></path>
                            </svg>
                        @else
                            <div class="col-auto">
                                <div class="spinner-border text-orange" role="status"></div>
                            </div>
                        @endif

                        <div class="col">
                            <h2 class="page-title">
                                @if($transaction->status == 'COMPLETE')
                                    This transaction was successful
                                @elseif($transaction->status == 'FAILED')
                                    Your transaction failed
                                @else
                                    Your transaction is in progress
                                @endif

                            </h2>
                            <div class="text-secondary">
                                <ul class="list-inline list-inline-dots mb-0 mt-1 small">

                                    @if($transaction->status == 'PENDING')
                                        <li class="list-inline-item">Checked every 10 seconds</li>
                                        <li class="list-inline-item">
                                            Last checked: {{\App\Common\Helpers::timeAgo($transaction->updated_at)}}
                                        </li>
                                    @else
                                        <li class="list-inline-item">Thank your for making this payment.</li>
                                    @endif
                                </ul>
                            </div>
                        </div>
                        <div class="col-md-auto ms-auto d-print-none">
                            <div class="btn-list">
                                <a href="{{url('start')}}" class="btn {{($can_print_receipt) ? '' : 'disabled'}}">
                                    Make New Payment
                                </a>
                                <a href="{{url('printReceipt/' . $transaction->reference)}}" target="_blank" class="btn btn-primary {{($can_print_receipt) ? '' : 'disabled'}}">
                                    Re-Print Proof of Payment
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Page body -->
            <div class="page-body">
                <div class="container-xl">
                    <div class="row row-cards">

                        <div class="col-lg-12">
                            @include('layouts.alerts')
                        </div>
                        <div class="col-lg-8">
                            <div class="card payment-card card-2">
                                <div class="heading">
                                    <div class="card-body pb-0">
                                        <h3 class="card-title">
                                            Your transaction will be updated shortly
                                        </h3>
                                    </div>
                                </div>
                                <div class="card-body">

                                    <div class="">
                                        <table
                                            class="table-card table table-striped table-hover table-bordered dt-enabled">
                                            <thead>
                                            <th>Reference</th>
                                            <th>Description</th>
                                            <th class="text-end">Amount Paid</th>
                                            <th>Options</th>
                                            </thead>
                                            <tbody>
                                            @foreach($payments as $details)
                                                @php
                                                    $payment = json_decode($details->details, true);
                                                @endphp
                                                <tr>
                                                    <td>
                                                        <div>{{$payment['accountName'] ?? '-'}}</div>
                                                        <div
                                                            class="text-secondary small">{{$payment['reference'] ?? '-'}}
                                                        </div>
                                                    </td>
                                                    <td>
                                                        {{$payment['description'] ?? ''}}
                                                    </td>
                                                    <td class="text-end">
                                                        K{{number_format($details->amount ?? 0, 2)}}
                                                    </td>
                                                    <td>
                                                        <a href="{{url('printReceipt/' . $transaction->reference)}}" target="_blank"
                                                           class="btn btn-sm {{($can_print_receipt) ? '' : 'disabled'}}">
                                                            <svg xmlns="http://www.w3.org/2000/svg"
                                                                 class="icon icon-tabler icon-tabler-printer"
                                                                 width="24" height="24"
                                                                 viewBox="0 0 24 24" stroke-width="2"
                                                                 stroke="currentColor" fill="none"
                                                                 stroke-linecap="round"
                                                                 stroke-linejoin="round">
                                                                <path stroke="none" d="M0 0h24v24H0z"
                                                                      fill="none"></path>
                                                                <path
                                                                    d="M17 17h2a2 2 0 0 0 2 -2v-4a2 2 0 0 0 -2 -2h-14a2 2 0 0 0 -2 2v4a2 2 0 0 0 2 2h2"></path>
                                                                <path
                                                                    d="M17 9v-4a2 2 0 0 0 -2 -2h-6a2 2 0 0 0 -2 2v4"></path>
                                                                <path
                                                                    d="M7 13m0 2a2 2 0 0 1 2 -2h6a2 2 0 0 1 2 2v4a2 2 0 0 1 -2 2h-6a2 2 0 0 1 -2 -2z"></path>
                                                            </svg>
                                                            Print Receipt
                                                        </a>
                                                    </td>
                                                </tr>
                                            @endforeach
                                            </tbody>
                                        </table>

                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="card payment-card details-card">
                                <div class="card-body">
                                    <h3 class="card-title">Transaction Details:</h3>
                                    <table class="table table-bordered">
                                        <tbody>
                                        <tr>
                                            <th> Status:</th>
                                            <td class="">
                                                    <span
                                                        class="badge {{($can_print_receipt) ? 'bg-green-lt' : 'bg-orange-lt'}}">
                                                        {{$transaction->status}}
                                                    </span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th> Order No:</th>
                                            <td>{{$transaction->id}}</td>
                                        </tr>
                                        <tr>
                                            <th>Amount:</th>
                                            <td class="hr-text-right">K{{number_format($transaction->amount, 2)}}</td>
                                        </tr>
{{--                                        @if($kyc->customer_type == 'RETAIL')--}}
                                        @if(true)
                                            <tr>
                                                <td><strong>First
                                                        Name:</strong></td>
                                                <td>{{$kyc->first_name}}</td>
                                            </tr>
                                            <tr>
                                                <td><strong>Last
                                                        Name:</strong></td>
                                                <td>{{$kyc->surname}}</td>
                                            </tr>
                                            <tr>
                                                <td><strong>Mobile:</strong>
                                                </td>
                                                <td>{{$kyc->mobile}}</td>
                                            </tr>
                                        @else
                                            <tr>
                                                <td><strong>TPIN:</strong>
                                                </td>
                                                <td>{{$kyc->tpin}}</td>
                                            </tr>
                                            <tr>
                                                <td><strong>Company
                                                        Name:</strong></td>
                                                <td>{{$kyc->company}}/td>
                                            </tr>
                                            <tr>
                                                <td><strong>Mobile:</strong>
                                                </td>
                                                <td>{{$kyc->mobile}}</td>
                                            </tr>
                                            <tr>
                                                <td><strong>Email:</strong>
                                                </td>
                                                <td>{{$kyc->email}}</td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <strong>Address:</strong>
                                                </td>
                                                <td>{{$kyc->address}}</td>
                                        @endif
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal modal-blur fade" id="successModal" tabindex="-1">
                <div class="modal-dialog modal-sm modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        <div class="modal-status bg-success"></div>
                        <div class="modal-body text-center py-4">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon mb-2 text-green icon-lg" width="24"
                                 height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                 stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                <circle cx="12" cy="12" r="9"/>
                                <path d="M9 12l2 2l4 -4"/>
                            </svg>
                            <h3>Payment successful</h3>
                            <div class="text-secondary">Your payment of <b>K{{number_format($total, 2)}}</b> has been
                                processed successful.
                                A copy of the receipt has been sent to the provided email.
                            </div>
                        </div>
                        <div class="modal-footer">
                            <div class="w-100">
                                <div class="row">
                                    <div class="col">
                                        <a href="#" class="btn w-100" data-bs-dismiss="modal">
                                            <svg xmlns="http://www.w3.org/2000/svg"
                                                 class="icon icon-tabler icon-tabler-notes" width="24" height="24"
                                                 viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                                 stroke-linecap="round" stroke-linejoin="round">
                                                <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                                <path
                                                    d="M5 3m0 2a2 2 0 0 1 2 -2h10a2 2 0 0 1 2 2v14a2 2 0 0 1 -2 2h-10a2 2 0 0 1 -2 -2z"></path>
                                                <path d="M9 7l6 0"></path>
                                                <path d="M9 11l6 0"></path>
                                                <path d="M9 15l4 0"></path>
                                            </svg>
                                            View Details
                                        </a>
                                    </div>
                                    <div class="col">
                                        <a href="{{url('printReceipt/' . $transaction->reference)}}" target="_blank"
                                           class="btn btn-success w-100">
                                            <svg xmlns="http://www.w3.org/2000/svg"
                                                 class="icon icon-tabler icon-tabler-printer"
                                                 width="24" height="24"
                                                 viewBox="0 0 24 24" stroke-width="2"
                                                 stroke="currentColor" fill="none"
                                                 stroke-linecap="round"
                                                 stroke-linejoin="round">
                                                <path stroke="none" d="M0 0h24v24H0z"
                                                      fill="none"></path>
                                                <path
                                                    d="M17 17h2a2 2 0 0 0 2 -2v-4a2 2 0 0 0 -2 -2h-14a2 2 0 0 0 -2 2v4a2 2 0 0 0 2 2h2"></path>
                                                <path
                                                    d="M17 9v-4a2 2 0 0 0 -2 -2h-6a2 2 0 0 0 -2 2v4"></path>
                                                <path
                                                    d="M7 13m0 2a2 2 0 0 1 2 -2h6a2 2 0 0 1 2 2v4a2 2 0 0 1 -2 2h-6a2 2 0 0 1 -2 -2z"></path>
                                            </svg>
                                            Print Receipt
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @include('frontend.partials.footer')
        </div>
    </div>
@endsection
@section('scripts')
    @parent
{{--    @vite('resources/js/payments.js')--}}

    @if($transaction->status == 'COMPLETE')
        <script>
            $(document).ready(function () {
                $('#successModal').modal('show');
            });
        </script>
    @endif
    @if($transaction->status == 'PENDING')
        <script>
            if (!document.hidden) {
                setTimeout(() => {
                    setOverlay('Preparing payment', '.payments-card');
                    location.reload(true);
                }, 5000); // 10000 milliseconds is equal to 10 seconds
            }
        </script>

    @endif
@endsection
