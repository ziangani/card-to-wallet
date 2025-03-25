@extends('layouts.checkout')

@section('body')
    <style>
        .payment-card {
            border-radius: 10px;
        }

        .form-hint {
            /*display: none;*/
        }

        .form-group {
            margin-bottom: 2rem !important;
        }

        .form-label {
            margin-bottom: 0.2rem;
        }
    </style>
    <div class="d-flex flex-column">
        <div class="page page-center">
            <div class="container container-tight py-4">
                <div class="text-center mb-4">
                    <a href="{{url('/')}}" class="navbar-brand navbar-brand-autodark">
                        <img class="d-block mx-auto mb-lg-4" src="{{asset('assets/img/logo.png')}}" alt="" width="200">
                    </a>
                </div>
                <div class="card card-md payment-card">
                    <div class="card-body">
                        <h2 class="mb-3 text-center">Pay {{$merchant->name}}</h2>
                        <p class="text-secondary mb-4 text-center">
                            Fill in the form below to make your payment.
                        </p>
                        <div class="payment-form">
                            <div class="form-group mb-3">

                                <label class="form-label required" for="amount">Payment Amount (USD)</label>
                                <div>
                                    <div class="input-group mb-2">
                                        <input autofocus type="number" step="1" min="1" class="form-control required"
                                               placeholder="For example 250" id="amount">
                                        <small class="amount-help w-100 text-danger"></small>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group mb-3">
                                <label class="form-label required" for="paymentNote">Payment Note</label>
                                <div>
                                    <input class="form-control required" id="paymentNote"
                                              placeholder="Enter payment note"
                                              required></input>
                                    <small class="note-help w-100 text-danger"></small>
                                </div>
                            </div>
                            <div class="form-group  mb-3">
                                <label class="form-label">
                                    Email address <span class="text-muted small">(optional)</span>
                                </label>
                                <div>
                                    <input id="email" type="email" class="form-control" aria-describedby="emailHelp"
                                           placeholder="Enter email">
                                    <small class="email-help w-100 text-danger"></small>
                                    <small class="form-hint">
                                        You'll receive a receipt and updates at this email address
                                    </small>
                                </div>
                            </div>
                        </div>

                        <div class="my-4">
                            <button type="button" class="btn btn-primary w-100 make-payment">
                                Make Payment
                            </button>
                        </div>
                    </div>
                    <div class="card-footer text-center">
                        <p class="mb-1">&copy; {{\App\Common\Helpers::getAppName()}} Limited {{date('Y')}}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        jQuery(document).ready(function ($) {
            let amount;
            let paymentNote;
            let email;

            function validateForm() {
                let isValid = true;
                amount = $('#amount').val();
                paymentNote = $('#paymentNote').val();
                email = $('#email').val();

                // Clear previous error messages
                $('.amount-help').text('');
                $('.note-help').text('');
                $('.email-help').text('');

                // Validate amount
                if (!amount || amount <= 0) {
                    $('.amount-help').text('Please enter a valid amount.');
                    $('#amount').addClass('is-invalid');
                    isValid = false;
                } else {
                    $('#amount').removeClass('is-invalid').addClass('is-valid');
                }

                // Validate payment note
                if (!paymentNote) {
                    $('.note-help').text('Please enter a payment note.');
                    $('#paymentNote').addClass('is-invalid');
                    isValid = false;
                } else {
                    $('#paymentNote').removeClass('is-invalid').addClass('is-valid');
                }

                // Validate email (optional)
                if (email && !validateEmail(email)) {
                    $('.email-help').text('Please enter a valid email address.');
                    $('#email').addClass('is-invalid');
                    isValid = false;
                } else if (email) {
                    $('#email').removeClass('is-invalid').addClass('is-valid');
                }
                return isValid;
            }

            $('.make-payment').on('click', function () {

                let isValid = validateForm();
                if (isValid) {
                    $.ajax({
                        url: '{{url("tpm/{$merchant->code}/process")}}',
                        method: 'post',
                        data: {
                            "_token": "{{ csrf_token() }}",
                            "amount": amount,
                            "paymentNote": paymentNote,
                            "email": email
                        },
                        beforeSend: function () {
                            overlay = setOverlay('Processing, Please wait...', '.payment-card .card-body');
                        },
                        success: function (data) {
                            if (data.status === 'SUCCESS') {
                                setOverlay('Redirecting, Please wait...', '.payment-card .card-body');
                                window.location.href = data.url;
                            } else {
                                swal('Could not initiate payment', data.statusMessage, 'warning');
                            }
                            overlay.unblock();
                        },
                        error: function (data) {
                            overlay.unblock();
                            swal('Something went wrong', 'Ensure you have an active internet connection or try again later.', 'info');
                        }
                    });
                }
            });

            $('#amount, #paymentNote, #email').on('input', function () {
                let field = $(this).attr('id');
                if (field === 'amount') {
                    $('.amount-help').text('');
                    $('#amount').removeClass('is-invalid').removeClass('is-valid');
                } else if (field === 'paymentNote') {
                    $('.note-help').text('');
                    $('#paymentNote').removeClass('is-invalid').removeClass('is-valid');
                } else if (field === 'email') {
                    $('.email-help').text('');
                    $('#email').removeClass('is-invalid').removeClass('is-valid');
                }
            });

            function validateEmail(email) {
                const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                return re.test(String(email).toLowerCase());
            }
        });
    </script>

    @vite('resources/js/app.js')
@endsection
