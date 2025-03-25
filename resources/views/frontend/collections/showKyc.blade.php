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
                        @include('layouts.alerts')
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
                                        <li class="step-item active">
                                            <div class="h4 m-0">Provide Your Details</div>
                                            <div class="text-secondary small">Kindly provide your personal information.</div>
                                        </li>
                                        <li class="step-item ">
                                            <div class="h4 m-0">Confirm Details</div>
                                            <div class="text-secondary small">Verify the details provided are correct and
                                                complete the payment.
                                            </div>
                                        </li>

                                        <li class="step-item">
                                            <div class="h4 m-0">Make Payment</div>
                                            <div class="text-secondary">Select from the list of payment methods and
                                                complete the payment.
                                            </div>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-8">
                            <div class="card payment-card card-2">
                                <form action="{{url('validateKyc')}}" method="post" id="billsForm">
                                    {{csrf_field()}}
                                    <div class="card-body">

                                        <div class="mb-3 row">
                                            <div class="col-md-6 ">
                                                <label class="form-label" for="customerType">Paying Customer Type
                                                    <span style="color: #d43f3a">*</span>
                                                </label>
                                                <select id="customerType" name="customerType" class="form-select">
                                                    <option value="RETAIL" id="customer-type-retail">An Individual
                                                    </option>
                                                    <option value="CORPORATE" id="customer-type-corporate">A Company
                                                    </option>
                                                </select>
                                            </div>
                                            <div class="retail-field col-md-6">
                                                <label class='form-label' for="id">NRC/Passport No. <span
                                                        style="color: #d43f3a">*</span></label>
                                                <input type="text" value="{{$kyc['id'] ?? ''}}"
                                                       name="id" class="form-control" id="id" placeholder="ID">
                                            </div>
                                            <div class="corporate-field col-md-6">
                                                <label class='form-label' for="tpin">TPIN <span
                                                        style="color: #d43f3a">*</span></label>
                                                <input type="number"
                                                       name="tpin" class="form-control" id="tpin" value="{{$kyc['tpin'] ?? ''}}"
                                                       placeholder="TPIN">
                                            </div>
                                        </div>
                                        <div class=" row">
                                            <div class="retail-field col-md-6">
                                                <label class='form-label' for="first-name">First Name <span
                                                        style="color: #d43f3a">*</span></label>
                                                <input type="text"
                                                       value="{{$kyc['firstName'] ?? ''}}"
                                                       name="firstName" class="form-control" id="first-name"
                                                       placeholder="First Name">
                                            </div>

                                            <div class="retail-field col-md-6">
                                                <label class='form-label' for="last-name">Last Name <span
                                                        style="color: #d43f3a">*</span></label>
                                                <input type="text"
                                                       value="{{$kyc['lastName'] ?? ''}}"
                                                       name="lastName" class="form-control" id="last-name"
                                                       placeholder="Last Name">
                                            </div>
                                        </div>
                                        <div class="mb-3 row">
                                            <div class="corporate-field col-md-6">
                                                <label class='form-label' for="company-name">Company Name <span
                                                        style="color: #d43f3a">*</span></label>
                                                <input type="text"
                                                       value="{{$kyc['company'] ?? ''}}"
                                                       name="company" class="form-control" id="company-name"
                                                       placeholder="Company Name">
                                            </div>
                                            <div class="corporate-field col-md-6">
                                                <label class='form-label' for="address">Address <span
                                                        style="color: #d43f3a">*</span></label>
                                                <input type="text"
                                                       value="{{$kyc['address'] ?? ''}}"
                                                       name="address" class="form-control" id="address"
                                                       placeholder="Residential Address">
                                            </div>
                                        </div>
                                        <div class="mb-3 row">
                                            <div class="col-md-6">
                                                <label class='form-label' for="mobile">Mobile <span
                                                        style="color: #d43f3a">*</span></label>
                                                <input type="text" name="mobile"
                                                       value="{{$kyc['mobile'] ?? ''}}"
                                                       class="form-control" id="mobile"
                                                       placeholder="Mobile Number">
                                                <span id="mobile-help" class="help-block">With country code(e.g 260XXXXXXXXX)</span>
                                            </div>
                                            <div class="col-md-6">
                                                <label class='form-label' for="email">Email</label>
                                                <input type="text" value="{{$kyc['email'] ?? ''}}"
                                                       name="email" class="form-control" id="email"
                                                       placeholder="Email Address">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="card-footer">
                                        <div class="row align-items-center">
                                            <div class="col"><a href="javascript:history.back()" class="btn">Go Back</a>
                                            </div>
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
    <script type="text/javascript">
        $(function () {
            toggleFields();
            $('#customerType').change(function () {
                toggleFields();
            });

            $('#mobile').on('paste keyup', function(e) {
                var value = $(this).val();
                var numericValue = value.replace(/\D/g, '');
                $(this).val(numericValue);
            });
        });

        function toggleFields() {
            if ($('#customerType').val() === 'RETAIL') {
                $('.retail-field').show();
                $('.corporate-field').hide();
            } else {
                $('.retail-field').hide();
                $('.corporate-field').show();
            }
        }
    </script>
@endsection
