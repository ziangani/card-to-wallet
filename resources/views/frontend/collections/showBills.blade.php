@extends('layouts.frontend')

@section('body')

    <div class="main-content">
        @include('frontend.partials.navigation')
        <div class="page-wrapper">
            <!-- Page header -->
            <div class="page-header d-print-none">
                <div class="container-xl">
                    <div class="row g-2 align-items-center">
                        <div class="col">
                            <h2 class="page-title">
                                Make Payment
                            </h2>
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
                        <div class="col-lg-4">
                            <div class="card payment-card">
                                <div class="card-body">
                                    <h3 class="card-title">Your progress step:</h3>
                                    <ul class="steps  steps-counter steps-vertical">
                                        <li class="step-item">
                                            <div class="h4 m-0">Find your bills</div>
                                            <div class="text-secondary small">
                                                Use your Customer account number to get your outstanding bills.
                                            </div>
                                        </li>
                                        <li class="step-item active">
                                            <div class="h4 m-0">Select Bill & Amount</div>
                                            <div class="text-secondary small">
                                                Select the bills you'd like to pay for and elect the amount you'd like
                                                to pay.
                                            </div>
                                        </li>
                                        <li class="step-item">
                                            <div class="h4 m-0">Provide Your Details</div>
                                            <div class="text-secondary small">Kindly provide your personal information.</div>
                                        </li>
                                        <li class="step-item">
                                            <div class="h4 m-0">Confirm Details</div>
                                            <div class="text-secondary small">Verify the details provided are correct and
                                                complete the payment.
                                            </div>
                                        </li>

                                        <li class="step-item">
                                            <div class="h4 m-0">Make Payment</div>
                                            <div class="text-secondary small">Select from the list of payment methods and
                                                complete the payment.
                                            </div>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-8">
                            <div class="card payment-card card-2">
                                <form action="{{url('getAmounts')}}" method="post" id="billsForm">
                                    {{csrf_field()}}
                                    <div class="heading">
                                        <div class="card-body">
                                            <h3 class="card-title">Select Bill & Amount</h3>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <div class="">
                                            <div class="table-responsive">
                                                <table
                                                    class="table card-table table-vcenter text-nowrap datatable {{(count($bills) > 3) ? 'dt-enabled' : ''}}">
                                                    <thead>
                                                    <tr>
                                                        <th class="w-1">
                                                            <input class="form-check-input m-0 align-middle"
                                                                   type="checkbox" id="selectAllCheckbox"
                                                                   aria-label="Select all invoices">
                                                        </th>
                                                        <th>Account Name - Bill Ref</th>
                                                        <th>Bill Description - Period</th>
                                                        <th style="text-align: right">(K)Amount to Pay</th>
                                                    </tr>
                                                    </thead>
                                                    <tbody>
                                                    @foreach($bills as $bill)
                                                        @if($bill['description'] != 'Balance B/F')
                                                            <tr>
                                                                <td>
                                                                    <input class="form-check-input m-0 align-middle bill-option"
                                                                           value="{{$bill['reference']}}" data-ref="{{$bill['reference']}}"
                                                                           checked type="checkbox"
                                                                           aria-label="Select bill">
                                                                </td>
                                                                <td>
                                                                    <div>{{$bill['accountName'] ?? '-'}}</div>
                                                                    <div
                                                                        class="text-secondary small">{{$bill['reference'] ?? '-'}}</div>
                                                                </td>
                                                                <td>
                                                                    <div>{{$bill['description'] ?? '-'}}</div>
                                                                    <div
                                                                        class="text-secondary small">{{ date('M-Y', strtotime($bill['alt_description'])) }}</div>
                                                                </td>
                                                                <td class="text-end">
                                                                    <input class="form-control text-end float-end w-95 bill-value bill-{{$bill['reference']}}"
                                                                           type="number" name="references[{{$bill['reference']}}]" min="1" step="0.1"
                                                                           value="{{number_format($bill['balanceDue'], 2, '.', '')}}">
                                                                </td>
                                                            </tr>
                                                        @endif
                                                    @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-footer">
                                        <div class="row align-items-center">
                                            <div class="col"> <a href="{{url('start')}}" class="btn">Go Back</a></div>
                                            <div class="col-auto">
                                                <button type="button" class="btn btn-primary" id="get-amounts">
                                                    Proceed
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </form>
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
    <script>
        jQuery(document).ready(function () {

            $("#selectAllCheckbox").on("click", function() {
                var checked = $(this).is(":checked");
                $(".bill-option").prop('checked', checked);
                $(".bill-value").attr('disabled', !checked);
            });

            $('.bill-option').change(function () {
                let ref = $(this).data('ref')
                if ($(this).is(':checked')) {
                    $('.bill-value.bill-' + ref).attr('disabled', false)
                } else {
                    $('.bill-value.bill-' + ref).attr('disabled', true)
                }
            })
        })
    </script>
@endsection
