@extends('layouts.checkout')

@section('body')
    <style>
        .payment-card {
            border-radius: 10px;
        }
    </style>
    <!-- Page body -->
    <div class="page-body">
        <div class="container-xl">
            <div class="row justify-content-center  mt-lg-6">
                <div class="header">
                    <img class="d-block mx-auto mb-lg-4" src="{{asset('assets/img/logo.png')}}" alt="" width="200">
                </div>
                <div class="col-sm-12 col-md-6 col-lg-5 pb-0 payment-card card shadow">
                    <div class="card-header- mt-5 text-center">
                        <h3 class="fw-bold fs-2 mb-3">
                            Check Out
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="row">

                            <div class="">
                                <h4 class="d-flex justify-content-between align-items-center mb-1">
                                    <span class="">Payment Summary</span>
                                </h4>
                                <p class="text-left">Select a payment method to complete your transaction.</p>
                                <div class="text-right w-100 mb-2 text-muted">
                                     <span class="badge badge-outline text-azure">
                                        Order Id: {{$paymentRequest->id}}
                                    </span>
                                </div>
                                <ul class="list-group mb-3 fs-3">
                                    <li class="list-group-item">
                                        <table class="table table-bordered">
                                            <tr>
                                                <th class="fw-normal">
                                                    <div>Description</div>
                                                    <div class="text-muted small">Merchant</div>
                                                </th>
                                                <td>
                                                    <div>{{$paymentRequest->description}}</div>
                                                    <div class="text-muted small">
                                                        {{$paymentRequest->merchant->name}}
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>Amount</th>
                                                <td>
                                                    <div class="fw-bold">
                                                        USD {{number_format($paymentRequest->amount, 2)}}</div>
                                                </td>
                                            </tr>
                                        </table>
                                    </li>
                                </ul>
                                <div class="accordion payment-methods" id="accordion-example">
{{--                                    <div class="accordion-item">--}}
{{--                                        <h2 class="accordion-header" id="heading-1">--}}
{{--                                            <button class="accordion-button collapsed" type="button"--}}
{{--                                                    data-bs-toggle="collapse" data-bs-target="#collapse-1"--}}
{{--                                                    aria-expanded="false">--}}

{{--                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"--}}
{{--                                                     viewBox="0 0 24 24" fill="currentColor"--}}
{{--                                                     class="icon icon-tabler mr-2 icons-tabler-filled icon-tabler-device-mobile">--}}
{{--                                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>--}}
{{--                                                    <path--}}
{{--                                                        d="M16 2a3 3 0 0 1 2.995 2.824l.005 .176v14a3 3 0 0 1 -2.824 2.995l-.176 .005h-8a3 3 0 0 1 -2.995 -2.824l-.005 -.176v-14a3 3 0 0 1 2.824 -2.995l.176 -.005h8zm-4 14a1 1 0 0 0 -.993 .883l-.007 .117l.007 .127a1 1 0 0 0 1.986 0l.007 -.117l-.007 -.127a1 1 0 0 0 -.993 -.883zm1 -12h-2l-.117 .007a1 1 0 0 0 0 1.986l.117 .007h2l.117 -.007a1 1 0 0 0 0 -1.986l-.117 -.007z"/>--}}
{{--                                                </svg>--}}

{{--                                                Pay with Mobile Money--}}
{{--                                            </button>--}}
{{--                                        </h2>--}}
{{--                                        <div id="collapse-1" class="accordion-collapse collapse"--}}
{{--                                             data-bs-parent="#accordion-example">--}}
{{--                                            <div class="accordion-body pt-3">--}}
{{--                                                <form>--}}

{{--                                                    <div class="mb-3">--}}
{{--                                                        <label class="form-label" for="cardNumber">Enter--}}
{{--                                                            Mobile--}}
{{--                                                            Number</label>--}}
{{--                                                        <div class="input-group mb-3">--}}
{{--                                                            <span class="input-group-text">26</span>--}}
{{--                                                            <input type="text" class="form-control"--}}
{{--                                                                   maxlength="10"--}}
{{--                                                                   step="1"--}}
{{--                                                                   id="mobileNumber" required=""--}}
{{--                                                                   placeholder="0955123456">--}}
{{--                                                            <small class="mobile-help text-danger"></small>--}}
{{--                                                        </div>--}}
{{--                                                    </div>--}}
{{--                                                    <div class="mb-3">--}}
{{--                                                        <div class="small text-muted">Detected Mobile Network</div>--}}
{{--                                                        <ul class="detected-payment-mode mb-3">--}}
{{--                                                            <li>--}}
{{--                                                                <img--}}
{{--                                                                    src="{{asset('assets/img/airtel.png')}}"--}}
{{--                                                                    alt="airtel money" class="p-mode airtel"--}}
{{--                                                                    data-prefix="097"--}}
{{--                                                                    data-class="airtel">--}}
{{--                                                            </li>--}}
{{--                                                            <li>--}}
{{--                                                                <img--}}
{{--                                                                    src="{{asset('assets/img/mtn.jpg')}}"--}}
{{--                                                                    alt="mtn momo" class="p-mode mtn"--}}
{{--                                                                    data-prefix="096"--}}
{{--                                                                    data-class="mtn">--}}
{{--                                                            </li>--}}
{{--                                                            <li>--}}
{{--                                                                <img--}}
{{--                                                                    src="{{asset('assets/img/zamtel.jpg')}}"--}}
{{--                                                                    alt="zamtel kwacha"--}}
{{--                                                                    class="p-mode zamtel"--}}
{{--                                                                    data-prefix="095"--}}
{{--                                                                    data-class="zamtel"--}}
{{--                                                                    style="width: 68px;">--}}
{{--                                                            </li>--}}
{{--                                                        </ul>--}}
{{--                                                    </div>--}}
{{--                                                    <button type="button" class="btn w-100 btn-primary check-mobile">--}}
{{--                                                        Pay with Mobile Money--}}
{{--                                                    </button>--}}
{{--                                                </form>--}}
{{--                                            </div>--}}
{{--                                        </div>--}}
{{--                                    </div>--}}
                                    <div class="accordion-item">
{{--                                        <h2 class="accordion-header" id="heading-2">--}}
{{--                                            <button class="accordion-button" type="button"--}}
{{--                                                    data-bs-toggle="collapse" data-bs-target="#collapse-2"--}}
{{--                                                    aria-expanded="false">--}}
{{--                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"--}}
{{--                                                     viewBox="0 0 24 24" fill="currentColor"--}}
{{--                                                     class="icon mr-2 icon-tabler icons-tabler-filled icon-tabler-credit-card">--}}
{{--                                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>--}}
{{--                                                    <path--}}
{{--                                                        d="M22 10v6a4 4 0 0 1 -4 4h-12a4 4 0 0 1 -4 -4v-6h20zm-14.99 4h-.01a1 1 0 1 0 .01 2a1 1 0 0 0 0 -2zm5.99 0h-2a1 1 0 0 0 0 2h2a1 1 0 0 0 0 -2zm5 -10a4 4 0 0 1 4 4h-20a4 4 0 0 1 4 -4h12z"/>--}}
{{--                                                </svg>--}}

{{--                                                Pay with Card--}}
{{--                                            </button>--}}
{{--                                        </h2>--}}
                                        <div id="collapse-2" class="accordion-collapse collapse- show"
                                             data-bs-parent="#accordion-example">
                                            <div class="accordion-body pt-3">
                                                <p class="fw-bold text-center"> Visa, Mastercard cards are accepted.</p>
                                                <img src="{{asset('assets/img/visa-mastercard.png')}}" alt="Mastercard"
                                                     class="mb-3 d-block mx-auto" style="width: 80%">
                                                
                                                <form id="cardPaymentForm">
                                                    <div class="mb-3">
                                                        <label class="form-label" for="cardNumber">Card Number</label>
                                                        <input type="text" class="form-control" id="cardNumber" 
                                                               maxlength="16" required placeholder="1234 5678 9012 3456">
                                                        <small class="card-help text-danger"></small>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-4">
                                                            <div class="mb-3">
                                                                <label class="form-label" for="expiryMonth">Expiry Month</label>
                                                                <input type="text" class="form-control" id="expiryMonth" 
                                                                       maxlength="2" required placeholder="MM">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <div class="mb-3">
                                                                <label class="form-label" for="expiryYear">Expiry Year</label>
                                                                <input type="text" class="form-control" id="expiryYear" 
                                                                       maxlength="2" required placeholder="YY">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <div class="mb-3">
                                                                <label class="form-label" for="cvv">CVV</label>
                                                                <input type="text" class="form-control" id="cvv" 
                                                                       maxlength="3" required placeholder="123">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <button type="button" class="btn btn-primary w-100 process-3ds-payment">
                                                        Pay with Card
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="list-group- mb-3 fs-3 payment-status" style="display: none">
                                    <h3>
                                      <span style="vertical-align: middle"
                                            class="transaction-status-indicator status-indicator status-blue status-indicator-animated d-inline-block">
                                      <span class="status-indicator-circle"></span>
                                      <span class="status-indicator-circle"></span>
                                      <span class="status-indicator-circle"></span>
                                    </span>
                                        <span class=" d-inline-block" style="vertical-align: sub">
                                            Payment Status
                                        </span>
                                    </h3>
                                    <div class="list-group-item">
                                        <table class="table table-bordered">
                                            <tr>
                                                <th class="col-5">Payment Status:</th>
                                                <td class="col-7">
                                                    <div class="col">
                                                        <span id="transaction-status"
                                                              class="fs-3 transaction-status-indicator text-blue-fg badge bg-blue">
                                                            Pending</span>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th class="col-5">Last Checked:</th>
                                                <td class="col-7" id="last-checked">10 Seconds ago</td>
                                            </tr>
                                            <tr>
                                                <th class="col-5">Reference:</th>
                                                <td class="col-7">{{$token}}</td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <footer class="my-3 pt-3 text-muted text-center text-small">
                            <ul class="list-inline">

                            </ul>
                        </footer>
                    </div>
                    <div class="card-footer text-center">
                        <p class="mb-1">&copy; {{\App\Common\Helpers::getAppName()}} Limited {{date('Y')}}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal modal-blur fade" id="paymentsModal" tabindex="-1">
        <div class="modal-dialog modal-sm modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        Mobile Money Payment Guide
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="text-center">
                        <img class="mb-3 w-50" src="{{asset('assets/img/undraw_online_payments_re_y8f2.svg')}}"
                             alt="payment options">
                    </div>
                    <div class="info-div">
                        <ol>
                            <li>
                                You will receive a payment request of
                                <b>USD {{number_format($paymentRequest->amount, 2)}}</b>
                                on the mobile number <b class="d-mobile">XXX</b>.
                            </li>
                            <li>
                                Ensure you have enough funds in your mobile money wallet.
                            </li>
                            <li>
                                Confirm the payment request on your mobile phone.
                            </li>
                        </ol>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn me-auto" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary make-payment"
                            data-mode="momo">
                        Pay K{{number_format($paymentRequest->amount, 2)}}
                    </button>
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
                    <h3>Payment Successful</h3>
                    <div class="text-secondary">
                        Your payment of <b>USD<span
                                class="d-amount">{{number_format($paymentRequest->amount, 2)}}</span></b> was
                        successful.
                        <br/>
                        Redirecting to the merchant's website...
                    </div>
                </div>

            </div>
        </div>
    </div>

    <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
         aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Modal title</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body" id="hco-embedded">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary">Save changes</button>
                </div>
            </div>
        </div>
    </div>
    
    <div class="modal modal-blur fade" id="threeDSModal" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">3D Secure Authentication</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="threeds-iframe-container">
                    <!-- 3DS iframe will be inserted here -->
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')

    <script src="{{$mpgs_endpoint}}/static/checkout/checkout.min.js" data-error="errorCallback"
            data-cancel="cancelCallback"></script>
    <script type="text/javascript">
        function errorCallback(error) {
            console.log(JSON.stringify(error));
        }

        function cancelCallback() {
            console.log('Payment cancelled');
        }
    </script>
    <script>
        let check_status = false;
        let payment_mode = '';
        let mobile = '';
        let last_check_ts = new Date();
        let update_last_checked = false;
        let threeDSData = {}; // Store 3DS data

        // Get browser information for 3DS
        function getBrowserInfo() {
            return {
                colorDepth: window.screen.colorDepth,
                javaEnabled: navigator.javaEnabled(),
                language: navigator.language,
                screenHeight: window.screen.height,
                screenWidth: window.screen.width,
                timeZone: new Date().getTimezoneOffset(),
                userAgent: navigator.userAgent,
                ipAddress: '127.0.0.1' // This will be replaced by the server
            };
        }

        // Create a container for the 3DS challenge iframe
        function create3DSChallengeContainer(html) {
            // Remove any existing container
            $('#threeds-container').remove();
            
            // Create a new container
            const $challengeContainer = $('<div>')
                .attr('id', 'threeds-container')
                .css({
                    'position': 'fixed',
                    'top': '0',
                    'left': '0',
                    'width': '100%',
                    'height': '100%',
                    'background': 'rgba(0,0,0,0.8)',
                    'z-index': '9999',
                    'display': 'flex',
                    'align-items': 'center',
                    'justify-content': 'center'
                });
            
            // Add the 3DS HTML to the container
            $challengeContainer.html(html);
            
            // Add the container to the body
            $('body').append($challengeContainer);
            
            // Find and execute any scripts
            $challengeContainer.find('script').each(function() {
                if (this.text) {
                    eval(this.text);
                }
            });
            
            // Style the iframe
            $('#redirectTo3ds1Frame').css({
                'width': '500px',
                'height': '600px',
                'border': 'none',
                'background': 'white',
                'border-radius': '8px',
                'box-shadow': '0 4px 6px rgba(0,0,0,0.1)'
            });
            
            // After 3DS challenge is completed, the callback will redirect to the status page
            // The server will handle the redirect to http://127.0.0.1:8001/checkout/{token}?indi=1
            
            // Add a message to inform the user
            const $message = $('<div>')
                .css({
                    'position': 'absolute',
                    'bottom': '20px',
                    'left': '0',
                    'width': '100%',
                    'text-align': 'center',
                    'color': 'white',
                    'font-size': '16px'
                })
                .html('<p>Please complete the authentication process.<br>You will be redirected automatically when finished.</p>');
            
            $challengeContainer.append($message);
        }

        jQuery(document).ready(function ($) {
            // Check if we're returning from MPGS
            if (new URLSearchParams(window.location.search).has('indi')) {
                check_status = true;
                update_last_checked = true;
                $('.payment-status').show();
                $('.payment-methods').hide();
                checkTransactionStatus(); // Check immediately
            }

            // Format card number as user types
            $('#cardNumber').on('input', function() {
                var input = $(this).val();
                // Remove non-numeric characters
                var numericValue = input.replace(/\D/g, '');
                $(this).val(numericValue);
            });

            // Format expiry month as user types
            $('#expiryMonth').on('input', function() {
                var input = $(this).val();
                // Remove non-numeric characters
                var numericValue = input.replace(/\D/g, '');
                $(this).val(numericValue);
            });

            // Format expiry year as user types
            $('#expiryYear').on('input', function() {
                var input = $(this).val();
                // Remove non-numeric characters
                var numericValue = input.replace(/\D/g, '');
                $(this).val(numericValue);
            });

            // Format CVV as user types
            $('#cvv').on('input', function() {
                var input = $(this).val();
                // Remove non-numeric characters
                var numericValue = input.replace(/\D/g, '');
                $(this).val(numericValue);
            });

            // Process 3DS payment
            $('.process-3ds-payment').on('click', function() {
                // Validate card details
                var cardNumber = $('#cardNumber').val();
                var expiryMonth = $('#expiryMonth').val();
                var expiryYear = $('#expiryYear').val();
                var cvv = $('#cvv').val();

                // Basic validation
                if (cardNumber.length !== 16) {
                    $('.card-help').text('Card number must be 16 digits');
                    return;
                }
                if (expiryMonth.length !== 2 || parseInt(expiryMonth) < 1 || parseInt(expiryMonth) > 12) {
                    $('.card-help').text('Expiry month must be between 01 and 12');
                    return;
                }
                if (expiryYear.length !== 2) {
                    $('.card-help').text('Expiry year must be 2 digits');
                    return;
                }
                if (cvv.length !== 3) {
                    $('.card-help').text('CVV must be 3 digits');
                    return;
                }

                $('.card-help').text('');

                // Step 1: Initiate 3DS authentication
                $.ajax({
                    url: '{{url("checkout/$token/3ds/initiate")}}',
                    method: 'post',
                    contentType: 'application/json',
                    data: JSON.stringify({
                        "_token": "{{ csrf_token() }}",
                        "card_number": cardNumber,
                        "currency": "USD"
                    }),
                    beforeSend: function() {
                        overlay = setOverlay('Initiating 3DS authentication...', '.payment-card .card-body');
                    },
                    success: function(response) {
                        if (response.status === 'SUCCESS') {
                            // Store 3DS data for next step
                            threeDSData = {
                                orderId: response.data.orderId,
                                transactionId: response.data.transactionId
                            };
                            
                            // Step 2: Authenticate payer
                            $.ajax({
                                url: '{{url("checkout/$token/3ds/authenticate")}}',
                                method: 'post',
                                contentType: 'application/json',
                                data: JSON.stringify({
                                    "_token": "{{ csrf_token() }}",
                                    "order_id": threeDSData.orderId,
                                    "transaction_id": threeDSData.transactionId,
                                    "card_number": cardNumber,
                                    "expiry_month": expiryMonth,
                                    "expiry_year": expiryYear,
                                    "amount": {{$paymentRequest->amount}},
                                    "currency": "USD",
                                    "browser_details": getBrowserInfo()
                                }),
                                success: function(authResponse) {
                                    overlay.unblock();
                                    
                                    if (authResponse.status === 'SUCCESS') {
                                        if (authResponse.data.requiresChallenge) {
                                            // Show 3DS challenge
                                            create3DSChallengeContainer(authResponse.data.challengeHtml);
                                        } else {
                                            // Frictionless flow - payment already completed
                                            check_status = true;
                                            update_last_checked = true;
                                            $('.payment-status').show();
                                            $('.payment-methods').hide();
                                            checkTransactionStatus();
                                        }
                                    } else {
                                        swal('Authentication Failed', authResponse.statusMessage, 'error');
                                    }
                                },
                                error: function(xhr) {
                                    overlay.unblock();
                                    let errorMessage = 'Authentication error';
                                    try {
                                        const response = JSON.parse(xhr.responseText);
                                        errorMessage = response.statusMessage || errorMessage;
                                    } catch (e) {
                                        console.error('Error parsing error response:', e);
                                    }
                                    swal('Authentication Failed', errorMessage, 'error');
                                }
                            });
                        } else {
                            overlay.unblock();
                            swal('Authentication Failed', response.statusMessage, 'error');
                        }
                    },
                    error: function(xhr) {
                        overlay.unblock();
                        let errorMessage = 'An error occurred';
                        try {
                            const response = JSON.parse(xhr.responseText);
                            errorMessage = response.statusMessage || errorMessage;
                        } catch (e) {
                            console.error('Error parsing error response:', e);
                        }
                        swal('Error', errorMessage, 'error');
                    }
                });
            });

            $('.check-mobile').on('click', function () {
                var input = $('#mobileNumber').val();
                var pattern = /^(096|076|095|075|097|077)\d+$/;
                if (!pattern.test(input)) {
                    $('.mobile-help').text('Invalid mobile. Please enter a number that starts with 096, 076, 095, 075, 097, or 077.');
                    return false;
                }
                if (input.length < 10) {
                    $('.mobile-help').text('Mobile number too short. Please enter a valid 10 digit number.');
                    return false;
                }
                if (input.length > 10) {
                    $('.mobile-help').text('Mobile number too long. Please enter a valid 10 digit number.');
                    return false;
                }
                $('.mobile-help').text('');

                $('#paymentsModal').modal('show');
                $('.d-mobile').text('26' + input)
            })

            $('.make-payment').on('click', function () {

                $('#paymentsModal').modal('hide');

                payment_mode = $(this).data('mode');
                mobile = $('#mobileNumber').val();


                $.ajax({
                    url: '{{url("checkout/$token/process")}}',
                    method: 'post',
                    data: {
                        "_token": "{{ csrf_token() }}",
                        "payment_mode": payment_mode,
                        "mobile": mobile
                    },
                    beforeSend: function () {
                        overlay = setOverlay('Processing payment. Please wait...', '.payment-card .card-body');
                    },
                    success: function (data) {
                        if (data.status === 'SUCCESS') {
                            check_status = true;
                            update_last_checked = true;
                            $('.payment-status').show()
                            $('.payment-methods').hide()
                            if (payment_mode !== 'momo') {
                                setOverlay('Redirecting, Please wait...', '.payment-card .card-body');
                                Checkout.configure({
                                    session: {
                                        id: data.session
                                    }
                                });

                                Checkout.showPaymentPage();
                            }

                        } else {
                            swal('Could not initiate payment', data.statusMessage, 'warning')
                        }
                        overlay.unblock()
                    },
                    error: function (data) {
                        overlay.unblock()
                        swal('Something went wrong', 'Ensure you have an active internet connection or try again later.', 'info')
                    }
                })
            })


            setInterval(() => {
                checkTransactionStatus()
            }, 10000);

            setInterval(() => {
                // Update the last checked time
                if (update_last_checked) {
                    var now = new Date();
                    var diff = Math.abs(now - last_check_ts);
                    var minutes = Math.floor(diff / 1000 / 60);
                    var seconds = Math.floor((diff / 1000) % 60);
                    var text = (minutes === 0) ? seconds + ' seconds ago' : minutes + ' minutes ago';
                    $('#last-checked').html(text);
                }
            }, 1000);

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
                if ($('#mobileNumber').val().trim() === '' || $('#mobileNumber').val().length === 3) {
                    $('#mobileNumber').val($(this).data('prefix'))
                    var className = $(this).data('class');
                    $('.p-mode').css('filter', 'grayscale(1)');
                    $('.p-mode.' + className).css('filter', 'grayscale(0)');
                }
                $('#mobileNumber').focus()
            })

            function checkTransactionStatus() {
                if (check_status) {
                    $.ajax({
                        url: '{{url("checkout/$token/status")}}',
                        method: 'get',
                        data: {
                            "_token": "{{ csrf_token() }}",
                        },
                        success: function (data) {
                            last_check_ts = new Date();
                            if (data.status === 'SUCCESS') {
                                $('.transaction-status-indicator.badge').removeClass('text-blue-fg bg-blue').addClass('text-white bg-success')
                                $('.transaction-status-indicator.status-indicator-animated').removeClass('status-blue').addClass('status-green')
                                $('#transaction-status').html('<i class="fa fa-check"></i> Paid');
                                $('#successModal').modal('show');
                                check_status = false
                                update_last_checked = false
                            } else if (data.status === 'FAILED') {
                                $('.transaction-status-indicator.badge').removeClass('text-blue-fg bg-blue').addClass('text-white bg-danger')
                                $('.transaction-status-indicator.status-indicator-animated').removeClass('status-blue').addClass('status-danger')
                                $('#transaction-status').html('<i class="fa fa-times"></i> Failed');

                                check_status = false
                                update_last_checked = false
                            } else if (data.status !== 'PENDING') {
                                swal('Could not get status', data.statusText, 'warning')
                                $('.transaction-status-indicator').removeClass('text-blue-fg bg-blue').addClass('text-danger')
                                $('.transaction-status-indicator').html('<i class="fa fa-times"></i> ERROR');
                                // check_status = false
                            }
                        },
                        error: function (data) {
                            overlay.unblock()
                            // swal('Something went wrong', 'Ensure you have an active internet connection or try again later.')
                        }
                    })
                }
            }
        });

    </script>
    @vite('resources/js/app.js')
@endsection
