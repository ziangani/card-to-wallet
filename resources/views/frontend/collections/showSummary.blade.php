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
                                        <li class="step-item ">
                                            <div class="h4 m-0">Select Bill & Amount</div>
                                            <div class="text-secondary small">
                                                Select the bills you'd like to pay for and elect the amount you'd like
                                                to pay.
                                            </div>
                                        </li>
                                        <li class="step-item">
                                            <div class="h4 m-0">Provide Your Details</div>
                                            <div class="text-secondary small">Kindly provide your personal
                                                information.
                                            </div>
                                        </li>
                                        <li class="step-item active">
                                            <div class="h4 m-0">Confirm Details</div>
                                            <div class="text-secondary small">Verify the details provided are correct
                                                and
                                                complete the payment.
                                            </div>
                                        </li>

                                        <li class="step-item">
                                            <div class="h4 m-0">Make Payment</div>
                                            <div class="text-secondary small">Select from the list of payment methods
                                                and
                                                complete the payment.
                                            </div>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-8">
                            @if(count($payments) == 0)
                                <div class="card">
                                    <div class="card-body">
                                        <div class="empty">
                                            <div class="empty-img"><img
                                                        src="{{asset('/static/illustrations/undraw_empty_cart_co35.png')}}"
                                                        height="128" alt="">
                                            </div>
                                            <p class="empty-title">Shopping Cart Empty</p>
                                            <p class="empty-subtitle text-secondary">
                                                Avoid costly fines and penalties. Pay your bills on time.
                                            </p>
                                            <div class="empty-action">
                                                <a href="{{url('/start')}}" class="btn btn-warning">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24"
                                                         height="24"
                                                         viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"
                                                         fill="none"
                                                         stroke-linecap="round" stroke-linejoin="round">
                                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                                        <path d="M12 5l0 14"></path>
                                                        <path d="M5 12l14 0"></path>
                                                    </svg>
                                                    Make Payment
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @else
                                <div class="card payment-card card-2">
                                    <form action="{{url('redirectPayment')}}" method="get" id="billsForm">
                                        {{csrf_field()}}
                                        <div class="heading">
                                            <div class="card-body pb-0">
                                                <h3 class="card-title">
                                                    Confirm Payment Details

                                                    <span class=" float-end">
                                                    <span class="badge ms-2 text-sm-center">
                                                     Total:</span>   K{{number_format($total, 2)}}
                                                </span>
                                                </h3>
                                            </div>
                                        </div>
                                        <div class="card-body">

                                            <div class="card">
                                                <div class="card-header">
                                                    <ul class="nav nav-tabs card-header-tabs" data-bs-toggle="tabs">
                                                        <li class="nav-item">
                                                            <a href="#tabs-home-ex2" class="nav-link active"
                                                               data-bs-toggle="tab">
                                                                <svg xmlns="http://www.w3.org/2000/svg"
                                                                     class="icon icon-tabler icon-tabler-receipt"
                                                                     width="24"
                                                                     height="24" viewBox="0 0 24 24" stroke-width="2"
                                                                     stroke="currentColor" fill="none"
                                                                     stroke-linecap="round" stroke-linejoin="round">
                                                                    <path stroke="none" d="M0 0h24v24H0z"
                                                                          fill="none"></path>
                                                                    <path
                                                                            d="M5 21v-16a2 2 0 0 1 2 -2h10a2 2 0 0 1 2 2v16l-3 -2l-2 2l-2 -2l-2 2l-2 -2l-3 2m4 -14h6m-6 4h6m-2 4h2"></path>
                                                                </svg>
                                                                Payment Details</a>
                                                        </li>
                                                        <li class="nav-item">
                                                            <a href="#tabs-profile-ex2" class="nav-link"
                                                               data-bs-toggle="tab">
                                                                <svg xmlns="http://www.w3.org/2000/svg"
                                                                     class="icon icon-tabler icon-tabler-user"
                                                                     width="24"
                                                                     height="24" viewBox="0 0 24 24" stroke-width="2"
                                                                     stroke="currentColor" fill="none"
                                                                     stroke-linecap="round" stroke-linejoin="round">
                                                                    <path stroke="none" d="M0 0h24v24H0z"
                                                                          fill="none"></path>
                                                                    <path d="M8 7a4 4 0 1 0 8 0a4 4 0 0 0 -8 0"></path>
                                                                    <path
                                                                            d="M6 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2"></path>
                                                                </svg>
                                                                Payer Details</a>
                                                        </li>
                                                    </ul>
                                                </div>
                                                <div class="card-body">
                                                    <div class="tab-content">
                                                        <div class="tab-pane active show" id="tabs-home-ex2">
                                                            <div>

                                                                <table
                                                                        class=" table table-striped table-hover table-bordered {{(count($payments) > 3) ? 'dt-enabled' : ''}}">
                                                                    <thead>
                                                                    <th>Account</th>
                                                                    <th>Description</th>
                                                                    <th class="text-end">Amount To Be Paid</th>
                                                                    <th>Options</th>
                                                                    </thead>
                                                                    <tbody>
                                                                        <?php $i = 0; ?>
                                                                    @foreach($payments as $payment)
                                                                        <tr>
                                                                            <td>
                                                                                <div class="text-secondary small">
                                                                                   {{$payment['merchant_application_name'] ?? '-'}}
                                                                                </div>
                                                                                <div>{{$payment['accountName'] ?? '-'}}</div>
                                                                            </td>
                                                                            <td>
                                                                                <div class="text-secondary small">
                                                                                    Reference: {{$payment['reference'] ?? '-'}}
                                                                                </div>
                                                                                <div>{{$payment['description'] ?? ''}}</div>
                                                                            </td>
                                                                            <td class="text-end">
                                                                                K{{number_format($payment['amountPaid'] ?? 0, 2)}}
                                                                            </td>
                                                                            <td>
                                                                                <a href="{{url('/removeitem/' . $payment['reference'])}}"
                                                                                   class="btn btn-danger btn-sm">
                                                                                    <svg xmlns="http://www.w3.org/2000/svg"
                                                                                         class="icon icon-tabler icon-tabler-trash"
                                                                                         width="24"
                                                                                         height="24" viewBox="0 0 24 24"
                                                                                         stroke-width="2"
                                                                                         stroke="currentColor"
                                                                                         fill="none"
                                                                                         stroke-linecap="round"
                                                                                         stroke-linejoin="round">
                                                                                        <path stroke="none"
                                                                                              d="M0 0h24v24H0z"
                                                                                              fill="none"></path>
                                                                                        <path d="M4 7l16 0"></path>
                                                                                        <path d="M10 11l0 6"></path>
                                                                                        <path d="M14 11l0 6"></path>
                                                                                        <path
                                                                                                d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12"></path>
                                                                                        <path
                                                                                                d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3"></path>
                                                                                    </svg>
                                                                                    Remove
                                                                                </a>
                                                                            </td>
                                                                        </tr>
                                                                            <?php ++$i ?>
                                                                    @endforeach
                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                        </div>
                                                        <div class="tab-pane" id="tabs-profile-ex2">
                                                            <div>
                                                                <div class="">
                                                                        <?php if ($kyc['customerType'] == 'RETAIL'){ ?>
                                                                    <table
                                                                            class=" table table-striped table-responsive table-hover table-bordered">
                                                                        <tr>
                                                                            <td><strong>ID:</strong>
                                                                            </td>
                                                                            <td><?php echo $kyc['id']; ?></td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td><strong>First
                                                                                    Name:</strong></td>
                                                                            <td><?php echo $kyc['firstName']; ?></td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td><strong>Last
                                                                                    Name:</strong></td>
                                                                            <td><?php echo $kyc['lastName']; ?></td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td><strong>Mobile:</strong>
                                                                            </td>
                                                                            <td><?php echo $kyc['mobile']; ?></td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td><strong>Email:</strong>
                                                                            </td>
                                                                            <td><?php echo $kyc['email']; ?></td>
                                                                        </tr>
                                                                    </table>
                                                                    <?php }else{ ?>
                                                                    <table
                                                                            class=" table table-striped table-responsive table-hover table-bordered">
                                                                        <tr>
                                                                            <td><strong>TPIN:</strong>
                                                                            </td>
                                                                            <td><?php echo $kyc['id']; ?></td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td><strong>Company
                                                                                    Name:</strong></td>
                                                                            <td><?php echo $kyc['company']; ?></td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td><strong>Mobile:</strong>
                                                                            </td>
                                                                            <td><?php echo $kyc['mobile']; ?></td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td><strong>Email:</strong>
                                                                            </td>
                                                                            <td><?php echo $kyc['email']; ?></td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td>
                                                                                <strong>Address:</strong>
                                                                            </td>
                                                                            <td><?php echo $kyc['address']; ?></td>
                                                                        </tr>
                                                                    </table>
                                                                    <?php } ?>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                            </div>
                                        </div>
                                        <div class="card-footer">
                                            <div class="row align-items-center">
                                                <div class="col"><a href="{{url('start')}}" class="btn">Go Back</a>
                                                </div>
                                                <div class="col-auto">
                                                    <a class="btn btn-secondary"
                                                       href="{{url('start')}}">
                                                        Add to Cart
                                                    </a>
                                                </div>
                                                <div class="col-auto">
                                                    <button type="button" data-bs-toggle="modal"
                                                            data-bs-target="#paymentsModal" class="btn btn-primary">
                                                        Proceed To Checkout
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal modal-blur fade" id="paymentsModal" tabindex="-1">
                <div class="modal-dialog modal-sm modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Payment Instructions</h5>
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
                                        If you are paying via Mobile Money ensure the selected account is sufficiently
                                        funded.
                                    </li>
                                    <li>
                                        If you are paying via Visa/MasterCard your account is sufficiently funded.
                                    </li>
                                </ol>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn me-auto" data-bs-dismiss="modal">Close</button>
                            <button type="button" href="button" id="postPayment" class="btn btn-primary">Proceed To
                                Payment
                            </button>
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

    <script>
        // jQuery(document).ready(function () {
        //     $('#accountNumber').val('323232')
        //     $('#get-account').click()
        // })
    </script>
@endsection
