@extends('layouts.frontend')
@section('meta-title', 'Pay: ' . $merchant->name . ' || ')
@section('meta-description', '')
@section('meta-image', url("general/merchant_application_logos/$app->logo_name"))
@section('header-logo', url("general/merchant_application_logos/$app->logo_name"))

@section('content')
    <style>

        .icon {
            width: 40px;
            height: auto;
            border-radius: 6px;
        }

        .blur {
            filter: blur(5px);
            pointer-events: none;
            /*    make element zoomed out*/
            transform: scale(0.8);

        }

        .ts-control, .ts-wrapper.single.input-active .ts-control {
            padding: 1.1em 1em;
        }

        .ts-wrapper.form-control.single.plugin-remove_button.has-options,
        .ts-wrapper.form-control.single.plugin-remove_button.focus.input-active.dropdown-active {
            padding: 0 !important;
            border: 0 !important;
        }

        /*Start*/

        .icon {
            width: 40px;
            height: auto;
            border-radius: 6px;
        }

        .payments-types li img {
            width: 45px;
            border-radius: 6px;
        }

        .detected-payment-mode {
            padding-left: 0;
        }

        .detected-payment-mode li {
            display: inline-block;
            margin-right: 4%;
            list-style: none;
        }

        .detected-payment-mode li img {
            width: 50px;
            border-radius: 45px;
            /*    make the image black and white*/
            -webkit-filter: grayscale(100%); /* Safari 6.0 - 9.0 */
            filter: grayscale(100%);
        }

        ul#myTab {
            margin: -15px 0 -15px -15px;
        }

        #myTab li.nav-item a {
            padding-left: 10px !important;
            font-size: 16px !important;
            font-weight: 500;
        }

        span.spinner-border.small {
            width: 1em;
            height: 1em;
            border-width: 0.15em;
            animation-duration: .85s;
        }

        .btn .icon {
            vertical-align: middle;
        }

        .blur .card .card-footer .btn {
            padding: 0.5em;
        }

        /*For smaller screens*/
        @media (max-width: 1250px) {
            .card .card-footer .btn {
                padding: 0.5em;
            }
        }
    </style>
    <!-- Content
    ============================================= -->
    <div class="livewire" x-data="{amount : 0, mobile: '', description: '', reference: ''}">
        <section class="page-header page-header-dark bg-purple">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <h1>Pay: {{$merchant->name}}</h1>
                    </div>
                    <div class="col-md-4 share-buttons">
                        <div style="text-align: right">
                            <a class="text-gradient" href="#!">
                                <svg xmlns="http://www.w3.org/2000/svg"
                                     class="icon small text-white icon-tabler icon-tabler-brand-twitter"
                                     width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"
                                     fill="none" stroke-linecap="round" stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                    <path
                                        d="M22 4.01c-1 .49 -1.98 .689 -3 .99c-1.121 -1.265 -2.783 -1.335 -4.38 -.737s-2.643 2.06 -2.62 3.737v1c-3.245 .083 -6.135 -1.395 -8 -4c0 0 -4.182 7.433 4 11c-1.872 1.247 -3.739 2.088 -6 2c3.308 1.803 6.913 2.423 10.034 1.517c3.58 -1.04 6.522 -3.723 7.651 -7.742a13.84 13.84 0 0 0 .497 -3.753c0 -.249 1.51 -2.772 1.818 -4.013z"/>
                                </svg>
                            </a>
                            <a class="text-gradient" href="#!">
                                <svg xmlns="http://www.w3.org/2000/svg"
                                     class="icon text-white icon-tabler icon-tabler-brand-facebook"
                                     width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"
                                     fill="none" stroke-linecap="round" stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                    <path d="M7 10v4h3v7h4v-7h3l1 -4h-4v-2a1 1 0 0 1 1 -1h3v-4h-3a5 5 0 0 0 -5 5v2h-3"/>
                                </svg>
                            </a>
                            <a class="text-gradient" href="#!">
                                <svg xmlns="http://www.w3.org/2000/svg"
                                     class="icon text-white icon-tabler icon-tabler-brand-whatsapp"
                                     width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"
                                     fill="none" stroke-linecap="round" stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                    <path d="M3 21l1.65 -3.8a9 9 0 1 1 3.4 2.9l-5.05 .9"/>
                                    <path
                                        d="M9 10a.5 .5 0 0 0 1 0v-1a.5 .5 0 0 0 -1 0v1a5 5 0 0 0 5 5h1a.5 .5 0 0 0 0 -1h-1a.5 .5 0 0 0 0 1"/>
                                </svg>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <div id="content">

            <section class="container heading">
                <ul class="steps steps-green steps-counter my-1 mt-5">
                    <li class="step-item step1">Find Your Bill</li>
                    <li class="step-item step2">Select Amount</li>
                    <li class="step-item step3">Make Payment</li>
                    <li class="step-item active step4">Download Receipt</li>
                </ul>
                <div class="border-1 rounded p-0 pt-md-5 pb-5 pt-sm-2">
                    <div class="row g-4">
                        <div class="col-lg-5 query-wrapper " style="display: none">
                            <div class="card shadow ">
                                <div class="card-header">
                                    <div class="right me-3">
                                        <img src="{{asset("general/merchant_application_logos/$app->logo_name")}}"
                                             class="icon"
                                             alt="{{$app->name}}">
                                    </div>
                                    <div class="left">
                                        <span class="fw-semibold d-block">{{$app->name}}</span>
                                        <span class="small text-muted d-block" style="font-size: 12px">
                                        {{$app->category->name}}
                                    </span>
                                    </div>
                                </div>
                                <form id="getBill" method="get" class="form" action="{{url("query/{$app->id}/bills")}}">
                                    @csrf
                                    <div class="card-body">

                                        <div class="p-3">

                                            <div class="mb-3 ref_wrapper blur">
                                                <label class="form-label" for="select-bill">
                                                    Enter Your Name or Reference</label>
                                                <select class="form-control" autofocus
                                                        style="padding: 0"
                                                        id="select-bill"
                                                        placeholder="Enter Your Name or Reference">
                                                </select>
                                            </div>
                                            <div class="mb3 result-wrapper" style="display: none">
                                                <div class="form-label">Being Payment For</div>
                                                <div class="border w-100 p-2 mb-3">
                                                    <div class="fw-600 d-ref">Payment Reference</div>
                                                    <small class="text-secondary d-desc">
                                                        Payment Description
                                                    </small>
                                                </div>
                                            </div>
                                            <div class="mb-3 amount_wrapper" style="display: none">
                                                <label class="form-label" for="amount_field">Enter Payment
                                                    Amount</label>
                                                <input type="number" id="amount_field" class="form-control"
                                                       name="amount" readonly
                                                       placeholder="Enter Your email or Mobile Number">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-footer">
                                        <div class="d-flex">
                                            <a href="{{url('/query/'. $app->id)}}" class="btn btn-link block-on-click">Cancel</a>
                                            <button class="btn btn-primary ms-auto" type="button" id="move-to-payment">
                                                Proceed <i class="fa fa-chevron-right"></i>
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <div class="col-lg-5  payments-wrapper blur">

                            <div class="card bg-white shadow">
                                <div class="card-body">
                                    <div class=" rounded">
                                        <div class="row g-4">
                                            <div class="">
                                                <ul class="nav nav-fill nav-justified nav-tabs mb-4" id="myTab" role="tablist">
                                                    <li class="nav-item">
                                                        <a class="nav-link text-4 lh-lg active method-tab"
                                                           id="first-tab"
                                                           style="padding-left: 0;" data-tab="momo"
                                                           data-bs-toggle="tab" href="#momoTab" role="tab"
                                                           aria-controls="momoTab" aria-selected="true">
                                                            <i class="fa fa-mobile"></i>
                                                            Mobile
                                                            Money
                                                        </a>
                                                    </li>
                                                    <li class="nav-item">
                                                        <a class="nav-link text-4 method-tab lh-lg" id="second-tab"
                                                           data-bs-toggle="tab" data-tab="card"
                                                           href="#cardTab" role="tab" aria-controls="cardTab"
                                                           aria-selected="false">
                                                            <i class="fa fa-credit-card"></i>
                                                            Debit/Credit Cards
                                                        </a>
                                                    </li>
                                                </ul>
                                                <div class="tab-content my-3" id="myTabContent">
                                                    <!-- Cards Details
                                                    ============================================= -->
                                                    <div class="tab-pane fade show active" id="momoTab" role="tabpanel"
                                                         aria-labelledby="first-tab">
                                                        {{--                                <h3 class="text-4 mb-4">Enter Your Mobile Wallet Details</h3>--}}
                                                        <form id="mobilePayment" method="post"
                                                              action="{{url("pay/" . request()->maid . "/mobilePayment")}}">
                                                            <div class="row g-3">

                                                                <div class="desc-wrapper">

                                                                </div>

                                                                <div class="col-md-12">
                                                                    <label class="form-label" for="cardNumber">Enter
                                                                        Mobile
                                                                        Number</label>
                                                                    <div class="input-group mb-3">
                                                                        <span class="input-group-text">26</span>
                                                                        <input type="text" class="form-control"
                                                                               maxlength="10"
                                                                               step="1"
                                                                               id="mobileNumber" required=""
                                                                               placeholder="0955123456">
                                                                        <small class="mobile-help text-danger"></small>
                                                                    </div>
                                                                </div>

                                                                <div class="col-12">
                                                                    <h3 class="text-1 mb-2">Detected Mobile Network</h3>
                                                                    <ul class="detected-payment-mode mb-0">
                                                                        <li>
                                                                            <img
                                                                                src="{{asset('assets/frontend/img/airtel.png')}}"
                                                                                alt="airtel money" class="p-mode airtel"
                                                                                data-prefix="097"
                                                                                data-class="airtel">
                                                                        </li>
                                                                        <li>
                                                                            <img
                                                                                src="{{asset('assets/frontend/img/mtn.jpg')}}"
                                                                                alt="mtn momo" class="p-mode mtn"
                                                                                data-prefix="096"
                                                                                data-class="mtn">
                                                                        </li>
                                                                        <li>
                                                                            <img
                                                                                src="{{asset('assets/frontend/img/zamtel.jpg')}}"
                                                                                alt="zamtel kwacha"
                                                                                class="p-mode zamtel"
                                                                                data-prefix="095"
                                                                                data-class="zamtel"
                                                                                style="width: 68px; vertical-align: text-bottom;">
                                                                        </li>
                                                                    </ul>
                                                                </div>
                                                            </div>
                                                        </form>
                                                    </div>
                                                    <!-- Cards Details end -->
                                                    <!-- Pay via Paypal
                                                  ============================================= -->
                                                    <div class="tab-pane fade" id="cardTab" role="tabpanel"
                                                         aria-labelledby="second-tab">
                                                        <form id="payment" method="post">
                                                            <div class="row g-3">

                                                                <div class="desc-wrapper">

                                                                </div>

                                                                <div class="col-md-12">
                                                                    <img src="{{asset('static/Visa-MasterCard.jpg')}}"
                                                                         alt="Pay via Visa or MasterCard">
                                                                </div>
                                                            </div>
                                                        </form>
                                                    </div>
                                                    <!-- Pay via Paypal end -->
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-footer">
                                    <div class="d-flex">
                                        <button type="button" class="btn btn-link block-on-click" id="back-to-query">
                                            Back
                                        </button>
                                        <button class="btn btn-primary ms-auto" type="button" id="make-payment">
                                            Pay K<span class="d-amount"></span> <i
                                                class="fa fa-chevron-right"></i>
                                        </button>

                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-5 receipt-wrapper">
                            <div class="card shadow ">
                                <div class="card-header">
                                    <div class="right me-3">
                                        <img src="{{asset("general/merchant_application_logos/$app->logo_name")}}"
                                             class="icon"
                                             alt="{{$app->name}}">
                                    </div>
                                    <div class="left">
                                        <span class="fw-semibold d-block">Download Receipt</span>
                                        <span class="small text-muted d-block" style="font-size: 12px">
                                        Order Id: <span class="d-order-id">{{$transaction->id}}</span>
                                    </span>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="">

                                        <div class="row">
                                            <div class="col-md-4 text-muted small">Transactions ID</div>
                                            <div class="col-md-8 text-sm-end fw-600">
                                                <span class="d-order-id">{{$transaction->id}}</span>
                                            </div>
                                        </div>
                                        <hr>
                                        <div class="row">
                                            <div class="col-md-4 text-muted small">Status</div>
                                            <div
                                                class="col-md-8 text-sm-end fw-600 text-azure transaction-status-indicator">
                                                <span class="spinner-border small" role="status"></span>
                                                Pending
                                            </div>
                                        </div>
                                        <hr>
                                        <div class="row">
                                            <div class="col-md-4 text-muted small">Payment Ref</div>
                                            <div class="col-md-8 text-sm-end fw-600">
                                                <div class="d-ref">
                                                    {{$transaction->breakdown()->first()->reference}}
                                                </div>
                                            </div>
                                        </div>
                                        <hr>
                                        <div class="row">
                                            <div class="col-md-4 text-muted small">Payment Desc</div>
                                            <div class="col-md-8 text-sm-end fw-600">
                                                <div class="d-desc">
                                                    {{$transaction->breakdown()->first()->description}}
                                                </div>
                                            </div>
                                        </div>
                                        <hr>
                                        <div class="row">
                                            <div class="col-md-4 text-muted small">Mode</div>
                                            <div class="col-md-8 text-sm-end fw-600">
                                                CARD
                                                <span class="d-mobile"></span>
                                            </div>
                                        </div>
                                        <hr>
                                        <div class="row">
                                            <div class="col-md-4 text-muted small">Amount</div>
                                            <div class="col-sm text-sm-end text-6 fw-500">K<span
                                                    class="d-amount">
                                                {{number_format($transaction->amount, 2)}}
                                                </span></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-footer">
                                    <div class="d-flex">
                                        <a class="btn btn-outline-secondary block-on-click disabled new-transaction"
                                           href="{{url("query/$app->id/")}}" style="margin-right: 1%; width: 90%">
                                            <i class="fa fa-paperclip "></i>
                                            New Transaction
                                        </a>
                                        <a class="btn btn-primary ms-auto disabled print-receipt" target="_blank">
                                            <i class="fas fa-print"></i>
                                            Print Receipt
                                        </a>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>

        <div class="modal modal-blur fade" id="successModal" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered" role="document">
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
                        <div class="text-secondary">Your payment of <b>K<span class="d-amount">0</span> </b> has been
                            processed successful.
                            A copy of the receipt has been sent to the provided email.
                        </div>
                    </div>
                    <div class="modal-footer">
                        <div class="w-100">
                            <div class="row">
                                {{--                                <div class="col">--}}
                                {{--                                    <a href="#" class="btn btn-outline-secondary w-100 btn-sm" data-bs-dismiss="modal">--}}
                                {{--                                        <svg xmlns="http://www.w3.org/2000/svg"--}}
                                {{--                                             class="icon icon-tabler icon-tabler-notes" width="24" height="24"--}}
                                {{--                                             viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"--}}
                                {{--                                             stroke-linecap="round" stroke-linejoin="round">--}}
                                {{--                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>--}}
                                {{--                                            <path--}}
                                {{--                                                d="M5 3m0 2a2 2 0 0 1 2 -2h10a2 2 0 0 1 2 2v14a2 2 0 0 1 -2 2h-10a2 2 0 0 1 -2 -2z"></path>--}}
                                {{--                                            <path d="M9 7l6 0"></path>--}}
                                {{--                                            <path d="M9 11l6 0"></path>--}}
                                {{--                                            <path d="M9 15l4 0"></path>--}}
                                {{--                                        </svg>--}}
                                {{--                                        Close--}}
                                {{--                                    </a>--}}
                                {{--                                </div>--}}
                                <div class="col">
                                    <a class="btn btn-outline-secondary w-100 btn-sm block-on-click disabled new-transaction"
                                       href="{{url("query/$app->id/")}}" style="margin-right: 1%; width: 90%">
                                        <i class="fa fa-paperclip "></i>
                                        New Payment
                                    </a>
                                </div>
                                <div class="col">
                                    <a href="#" target="_blank"
                                       class="btn btn-success btn-sm print-receipt w-100">
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
    </div>
    <!-- Content end -->
@endsection

@section('scripts')
    @parent
    <script src="{{asset('assets/libs/easy-responsive-tabs/easy-responsive-tabs.js')}}"></script>
    <script type="application/javascript">
        let item_id = '';
        let item_amount = 0;
        let order_id = ''
        let balance = 0
        let mobile = ''
        let counter = 0
        let check_status = true
        let payment_mode = 'momo'
        jQuery(document).ready(function ($) {

            //Prevent browser reload
            $(window).bind('beforeunload', function () {
                if (check_status) {
                    return 'Are you sure you want to leave before completing the payment?';
                }
            });

            $('.d-amount').text('{{number_format($transaction->amount, 2)}}')

            function checkTransactionStatus() {
                var reference = order_id
                if (check_status) {
                    $.ajax({
                        url: '{{url("query/$app->id/status")}}',
                        method: 'get',
                        data: {
                            "_token": "{{ csrf_token() }}",
                            "reference": {{$transaction->id }}
                        },
                        success: function (data) {
                            if (data.status === 'SUCCESS') {
                                $('.transaction-status-indicator').removeClass('text-azure').addClass('text-success')
                                $('.transaction-status-indicator').html('<i class="fa fa-check"></i> Paid');

                                $('.new-transaction').removeClass('disabled')
                                $('.print-receipt').removeClass('disabled')
                                $('.print-receipt').attr('href', '{{url("query/$app->id/receipt/")}}/' + data.ref)

                                $('.step3').removeClass('active')
                                $('.step4').addClass('active')

                                $('#successModal').modal('show');

                                check_status = false
                            } else if (data.status === 'FAILED') {
                                $('.transaction-status-indicator').removeClass('text-azure').addClass('text-danger')
                                $('.transaction-status-indicator').html('<i class="fa fa-times"></i> Failed');

                                $('.new-transaction').removeClass('disabled')

                                $('.step3').removeClass('active')
                                $('.step4').addClass('active')
                                check_status = false
                            } else if (data.status !== 'PENDING') {
                                swal('Could not get status', data.statusText, 'warning')
                                $('.new-transaction').removeClass('disabled')
                                $('.transaction-status-indicator').removeClass('text-azure').addClass('text-danger')
                                $('.transaction-status-indicator').html('<i class="fa fa-times"></i> ERROR');
                                // check_status = false
                            }
                        },
                        error: function (data) {
                            overlay.unblock()
                            swal('Something went wrong', 'Ensure you have an active internet connection or try again later.')
                        }
                    })
                }
            }



            setInterval(() => {
                checkTransactionStatus()
            }, 5000);

        });


    </script>

    <script type="application/javascript">
        jQuery(document).ready(function ($) {
            //Payment logic
            $('#amount_field').on('input change', function () {
                var input = $(this).val();
                var inputValue = $(this).val();
                // Remove non-numeric characters using a regular expression
                var numericValue = inputValue.replace(/\D/g, '');
                // Set the cleaned value back to the input field
                $(this).val(numericValue);
                //display a formatted string to 2 decimal places
                var formatted = numericValue.replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1,');
                $('.d-amount').text(formatted)
            });


            $('#mobileNumber').on('input change', function () {
                var input = $(this).val();
                var inputValue = $(this).val();
                // Remove non-numeric characters using a regular expression
                var numericValue = inputValue.replace(/\D/g, '');
                // Set the cleaned value back to the input field
                $(this).val(numericValue);

                $('.p-mode').css('filter', 'grayscale(1)'); // reset all logos to grey
                if (input.startsWith('096') || input.startsWith('076')) {
                    $('.p-mode.mtn').css('filter', 'grayscale(0)');
                }
                if (input.startsWith('095') || input.startsWith('075')) {
                    $('.p-mode.zamtel').css('filter', 'grayscale(0)');
                }
                if (input.startsWith('097') || input.startsWith('077')) {
                    $('.p-mode.airtel').css('filter', 'grayscale(0)');
                }
            });

            $('.p-mode').on('click', function () {
                if ($('#mobileNumber').val() == '' || $('#mobileNumber').val().length === 3) {
                    $('#mobileNumber').val($(this).data('prefix'))
                    var className = $(this).data('class');
                    $('.p-mode').css('filter', 'grayscale(1)');
                    $('.p-mode.' + className).css('filter', 'grayscale(0)');
                }
                $('#mobileNumber').focus()
            })


        });
    </script>
    @vite('resources/js/app.js')
    @livewireScripts
@endsection
