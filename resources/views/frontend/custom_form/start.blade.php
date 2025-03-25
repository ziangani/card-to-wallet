@extends('layouts.frontend')
@section('meta-title', 'Pay: ' . $merchant->name )
@section('meta-description', '')
@section('meta-image', url("$app->logo_name"))
@section('header-logo', url("$app->logo_name"))

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
        <section class="page-header page-header-dark bg-purple-- bg-secondary">
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
                    <li class="step-item active step1">Find Your Bill</li>
                    <li class="step-item step2">Select Amount</li>
                    <li class="step-item step3">Provide KYC</li>
                    <li class="step-item step4">Make Payment</li>
                    <li class="step-item step5">Download Receipt</li>
                </ul>
                <div class="border-1 rounded p-0 pt-md-5 pb-5 pt-sm-2">
                    <div class="row g-4">
                        <div class="{{($is_split_form) ? 'col-lg-8' : 'col-lg-5'}} query-wrapper ">
                            <div class="card shadow ">
                                <div class="card-header">
                                    <div class="right me-3">
                                        <img src="{{asset("$app->logo_name")}}"
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
                                <form id="getBill" method="get" class="form" action="{{url("frm/{$app->id}/bills")}}">
                                    <div class="card-body">


                                        <div class="p-3 pt-0">
                                            <h5 class="p-3 pt-0">Populate the form below to complete your registration.</h5>
                                            <div class="ref_wrapper">
                                                <div class="p-3 {{($is_split_form) ? 'row' : ''}}">
                                                    @foreach($query_fields as $key => $field)
                                                        @if($field->type == 'select')
                                                            <div class="mb-4 {{($is_split_form) ? 'col-md-6' : ''}}">
                                                                <label
                                                                    class="form-label {{($field->required ? 'required' : '')}}"
                                                                    for="{{$key}}">{{$field->label}}</label>
                                                                <select
                                                                    class="{{($field->required ? 'foik' : '')}} form-select"
                                                                    name="{{$key}}"
                                                                    id="{{$key}}" {{($field->required ? 'required' : '')}}>
                                                                    <option value=""> - Select One -</option>
                                                                    @foreach($field->options as $option_id => $option_value)
                                                                        <option
                                                                            value="{{$option_id}}">{{$option_value}}</option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                        @endif

                                                        @if($field->type == 'text' || $field->type == 'number')
                                                            <div
                                                                class="form-group {{($is_split_form) ? 'col-md-6' : ''}}">
                                                                <label
                                                                    class="form-label {{($field->required ? 'required' : '')}}"
                                                                    for="{{$key}}">{{$field->label}}</label>
                                                                <div class="input-group mb-3">
                                                                    @if($field->prefix)
                                                                        <span
                                                                            class="input-group-text">{{$field->prefix}}</span>
                                                                    @endif
                                                                    <input
                                                                        class="{{($field->required ? 'foik' : '')}} form-control"
                                                                        id="{{$key}}"
                                                                        placeholder="{{$field->placeholder}}"
                                                                        {{($field->required ? 'required' : '')}}
                                                                        {{($field->autofocus ? 'autofocus' : '')}}
                                                                        {{($field->readonly ? 'readonly' : '')}}
                                                                        {{($field->disabled ? 'disabled' : '')}}
                                                                        @if($field->type == 'number')
                                                                            {{($field->min ? 'min=' . $field->min : '')}}
                                                                            {{($field->max ? 'max=' . $field->max : '')}}
                                                                            {{($field->step ? 'step=' . $field->step : '')}}
                                                                            {{($field->length ? 'maxlength=' . $field->length : '')}}
                                                                        @endif
                                                                        value="{{old($key)}}"
                                                                        name="{{$key}}"
                                                                        type="{{$field->type}}">
                                                                </div>
                                                            </div>
                                                        @endif
                                                    @endforeach
                                                </div>
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
                                            <div class="amount_wrapper" style="display: none">
                                                <div class="mb-3">
                                                    @if($amount_field->type == 'select')
                                                        <div class="mb-4">
                                                            <label class="form-label"
                                                                   for="amount_determinant">{{$amount_field->label}}</label>
                                                            <select class="form-select" name="{{$amount_field->name}}"
                                                                    id="amount_determinant" {{($amount_field->required ? 'required' : '')}}>
                                                                <option value=""> - Select One -</option>
                                                                @foreach($amount_field->options as $option_value => $option_id)
                                                                    <option
                                                                        value="{{$option_id}}"
                                                                        data-reference="{{$option_value}}"
                                                                        data-reference="{{$option_value}}"
                                                                    >{{$option_value}}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                        @if($app->qty_is_enabled)
                                                            <div class="mb-3">
                                                                <label for="qty_field" class="form-label">
                                                                    {{$app->qty_label}}
                                                                </label>
                                                                <select name="qty" id="qty_field" class="form-select"
                                                                        required>
                                                                    @php($counter = $app->qty_range_start)
                                                                    @while($counter <= $app->qty_range_finish)
                                                                        <option value="{{$counter}}">
                                                                            {{\App\Common\Helpers::pluralize($app->qty_label, $counter)}}
                                                                        </option>
                                                                        @php($counter++)
                                                                    @endwhile
                                                                </select>
                                                            </div>
                                                        @endif
                                                        <label class="form-label" for="amount_field">Payment
                                                            Amount</label>
                                                        <input type="number" id="amount_field" class="form-control"
                                                               name="amount" readonly
                                                               placeholder="Select a Package First">
                                                    @endif
                                                </div>

                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-footer">
                                        <div class="d-flex">
                                            <a href="{{url('/fm/'. $app->id)}}"
                                               class="btn btn-link block-on-click">Cancel</a>
                                            <button class="btn btn-primary ms-auto" type="button"
                                                    id="move-to-kyc">
                                                Proceed <i class="fa fa-chevron-right"></i>
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <div class="{{($is_split_form) ? 'col-lg-4' : 'col-lg-5'}} blur kyc-wrapper ">
                            <div class="card shadow ">
                                <div class="card-header">
                                    <div class="right me-3">
                                        <img src="{{asset("$app->logo_name")}}"
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
                                <form id="getBill" method="get" class="form" action="{{url("frm/{$app->id}/bills")}}">
                                    @csrf
                                    <div class="card-body">
                                        <div class="p-3">
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
                                                           name="tpin" class="form-control" id="tpin"
                                                           value="{{$kyc['tpin'] ?? ''}}"
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
                                                </div>
                                                <div class="col-md-6">
                                                    <label class='form-label' for="email">Email</label>
                                                    <input type="text" value="{{$kyc['email'] ?? ''}}"
                                                           name="email" class="form-control" id="email"
                                                           placeholder="Email Address">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-footer">
                                        <div class="d-flex">
                                            <a href="{{url('/fm/'. $app->id)}}"
                                               class="btn btn-link block-on-click">Cancel</a>
                                            <button class="btn btn-primary ms-auto" type="button"
                                                    id="move-to-payment">
                                                Proceed <i class="fa fa-chevron-right"></i>
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <div class="col-lg-5  payments-wrapper blur" style="display: none">
                            <div class="card bg-white shadow">
                                <div class="card-body">
                                    <div class=" rounded">
                                        <div class="row g-4">
                                            <div class="">
                                                <ul class="nav nav-fill nav-justified nav-tabs mb-4" id="myTab"
                                                    role="tablist">
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
                                                                                alt="airtel money"
                                                                                class="p-mode airtel"
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
                        <div class="col-lg-5 receipt-wrapper blur" style="display: none">
                            <div class="card shadow ">
                                <div class="card-header">
                                    <div class="right me-3">
                                        <img src="{{asset("$app->logo_name")}}"
                                             class="icon"
                                             alt="{{$app->name}}">
                                    </div>
                                    <div class="left">
                                        <span class="fw-semibold d-block">Download Receipt</span>
                                        <span class="small text-muted d-block" style="font-size: 12px">
                                        Order Id: <span class="d-order-id">123232</span>
                                    </span>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="">

                                        <div class="row">
                                            <div class="col-md-4 text-muted small">Transactions ID</div>
                                            <div class="col-md-8 text-sm-end fw-600">
                                                <span class="d-order-id">123232</span>
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
                                                <div class="d-ref">1232</div>
                                            </div>
                                        </div>
                                        <hr>
                                        <div class="row">
                                            <div class="col-md-4 text-muted small">Payment Desc</div>
                                            <div class="col-md-8 text-sm-end fw-600">
                                                <div class="d-desc">Ziagani</div>
                                            </div>
                                        </div>
                                        <hr>
                                        <div class="row">
                                            <div class="col-md-4 text-muted small">Mode</div>
                                            <div class="col-md-8 text-sm-end fw-600">
                                                Mobile -
                                                <span class="d-mobile"></span>
                                            </div>
                                        </div>
                                        <hr>
                                        <div class="row">
                                            <div class="col-md-4 text-muted small">Amount</div>
                                            <div class="col-sm text-sm-end text-6 fw-500">K<span
                                                    class="d-amount">120</span></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-footer">
                                    <div class="d-flex">
                                        <a class="btn btn-outline-secondary block-on-click disabled new-transaction"
                                           href="{{url("frm/$app->id/")}}" style="margin-right: 1%; width: 90%">
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
                                       href="{{url("frm/$app->id/")}}" style="margin-right: 1%; width: 90%">
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
    <script src="{{asset('assets/libs/jquery-ajax-form/jquery-ajax-form.min.js')}}"></script>
    <script type="application/javascript">

        let item_id = '';
        let item_amount = 0;
        let order_id = ''
        let balance = 0
        let mobile = ''
        let counter = 0
        let check_status = false
        let payment_mode = 'momo'
        let qty = {{ $app->qty_is_enabled ? $app->qty_range_start : 1 }};
        let unit_amount = 0
        let first_name = ''
        let last_name = ''
        let email = ''
        let kyc_mobile = ''
        let company = ''
        let customerType = ''
        let tpin = ''
        let address = ''
        let national_id = ''
        let form_completed = false
        let form_fields = ''

        jQuery(document).ready(function ($) {


            //Prevent browser reload
            $(window).bind('beforeunload', function () {
                if (check_status) {
                    return 'Are you sure you want to leave before completing the payment?';
                }
            });

            $('#amount_determinant').on('change', function () {
                if ($(this).val() == '') {
                    return false;
                }

                var reference = $(this).find('option:selected').data('reference');
                var description = $(this).find('option:selected').data('description');
                var selected_amount = parseFloat($(this).find('option:selected').val());
                var my_item_id = reference

                $('.d-ref').text(reference)
                $('.d-desc').text(description)


                unit_amount = selected_amount
                item_id = my_item_id;
                balance = (selected_amount * qty);
                item_amount = (selected_amount * qty);

                $('#amount_field').val(item_amount)

                $('.result-wrapper').show({direction: 'down'}, 0)
                $('.desc-wrapper').html('');
                $('.desc-wrapper').html($('.result-wrapper').clone());

            })


            //swap between payment methods
            $('.method-tab').on('click', function () {
                let tab = $(this).data('tab')
                payment_mode = tab
            })


            $('.form-control').on('blur keyup', function () {
                if ($(this).val() == '') {
                    $(this).addClass('is-invalid');
                } else {
                    $(this).removeClass('is-invalid');
                }
            });

            $('#move-to-kyc').on('click', function () {
                let errors = 0;
                if (!form_completed) {
                    $('.ref_wrapper .foik').each(function () {
                        console.log($(this).val())
                        var value = $(this).val().trim();
                        if (value === '') {
                            $(this).addClass('is-invalid');
                            errors++
                        } else {
                            $(this).removeClass('is-invalid');
                        }
                    });
                    if (errors > 0) {
                        form_completed = false
                        return;
                    }
                    $('.step1').removeClass('active')
                    $('.step2').addClass('active')
                    $('.ref_wrapper').hide()
                    $('.amount_wrapper').show()
                    form_completed = true
                    @if($is_split_form)
                    $('.query-wrapper').removeClass('col-lg-8').addClass('col-lg-5')
                    $('.kyc-wrapper').removeClass('col-lg-4').addClass('col-lg-5')
                    @endif
                } else {

                    if ($('#amount_field').val() == '' || $('#amount_field').val() == 0) {
                        $('#amount_field').focus()
                        $('#amount_field').addClass('is-invalid')
                        alert('Please select a package')
                        return;
                    } else {
                        $('#amount_field').removeClass('is-invalid')

                        $('.query-wrapper').hide();
                        $('.kyc-wrapper').show()
                        $('.kyc-wrapper').removeClass('blur col-lg-5').addClass('col-lg-7');
                        $('.payments-wrapper').show();

                        $('.step2').removeClass('active')
                        $('.step3').addClass('active')

                        $('.d-amount').text(item_amount)

                        console.log('reached here')
                    }
                }
            });

            $('#move-to-payment').on('click', function () {

                var clientType = $('#customerType').val();
                if (clientType === 'RETAIL') {
                    if ($('#id').val().trim() === '') {
                        $('#id').addClass('is-invalid');
                        return false;
                    }
                    if ($('#first-name').val().trim() === '') {
                        $('#first-name').addClass('is-invalid');
                        return false;
                    }
                    if ($('#last-name').val().trim() === '') {
                        $('#last-name').addClass('is-invalid');
                        return false;
                    }
                    if ($('#mobile').val().trim() === '') {
                        $('#mobile').addClass('is-invalid');
                        return false;
                    }
                    if ($('#email').val().trim() === '') {
                        $('#email').addClass('is-invalid');
                        return false;
                    }
                } else {
                    if ($('#tpin').val().trim() === '') {
                        $('#tpin').addClass('is-invalid');
                        return false;
                    }
                    if ($('#company-name').val().trim() === '') {
                        $('#company-name').addClass('is-invalid');
                        return false;
                    }
                    if ($('#mobile').val().trim() === '') {
                        $('#mobile').addClass('is-invalid');
                        return false;
                    }
                    if ($('#email').val().trim() === '') {
                        $('#email').addClass('is-invalid');
                        return false;
                    }
                }

                first_name = $('#first-name').val()
                last_name = $('#last-name').val()
                email = $('#email').val()
                kyc_mobile = $('#mobile').val()
                company = $('#company-name').val()
                customerType = $('#customerType').val()
                tpin = $('#tpin').val()
                address = $('#address').val()
                national_id = $('#id').val()


                $('.kyc-wrapper').hide();
                $('.payments-wrapper').show();
                $('.payments-wrapper').removeClass('blur');
                $('.receipt-wrapper').show();


                $('.step3').removeClass('active')
                $('.step4').addClass('active')

                $('.desc-wrapper').html('');
                $('.desc-wrapper').html($('.result-wrapper').clone());

            });

            $('#back-to-query').on('click', function () {
               window.location.reload()
            });

            $('#make-payment').on('click', function () {

                $('.step4').removeClass('active')
                $('.step5').addClass('active')

                var formArray = $('#getBill').serializeArray();
                var formJson = {};

                $.each(formArray, function (i, field) {
                    formJson[field.name] = field.value;
                });

                form_fields = JSON.stringify(formJson);

                if (payment_mode !== 'momo') {
                    $.ajax({
                        url: '{{url("frm/$app->id/create")}}',
                        method: 'post',
                        data: {
                            "_token": "{{ csrf_token() }}",
                            "amount": item_amount,
                            "item_id": item_id,
                            "item_amount": item_amount,
                            "balance": balance,
                            "payment_mode": 'card',
                            "qty": qty,
                            "first_name": first_name,
                            "last_name": last_name,
                            "email": email,
                            "kyc_mobile": kyc_mobile,
                            "company": company,
                            "customerType": customerType,
                            "tpin": tpin,
                            "address": address,
                            "id": national_id,
                            "form_values": form_fields

                        },
                        beforeSend: function () {
                            overlay = setOverlay('Processing payment. Please wait...', '.payments-wrapper .card .card-body');
                        },
                        success: function (data) {
                            if (data.status === 'SUCCESS') {
                                order_id = data.reference
                                $('.d-order-id').text(order_id)
                                $('.payments-wrapper').addClass('blur')
                                $('.receipt-wrapper').removeClass('blur')
                                $('receipt-wrapper').show()
                                setOverlay('Redirecting, Please wait...', '#main-wrapper');
                                window.location.href = data.checkout
                            } else {
                                swal('Could not initiate payment', data.statusText, 'warning')
                            }
                            overlay.unblock()
                        },
                        error: function (data) {
                            overlay.unblock()
                            swal('Something went wrong', 'Ensure you have an active internet connection or try again later.')
                        }
                    })
                } else {
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
                    //Create the transaction
                    item_amount = $('#amount_field').val();
                    mobile = $('#mobileNumber').val();
                    $('.d-mobile').text(mobile)
                    $('.d-amount').text(item_amount)
                    overlay = null;
                    //create a ajax request to create the transaction
                    $.ajax({
                        url: '{{url("frm/$app->id/create")}}',
                        method: 'post',
                        data: {
                            "_token": "{{ csrf_token() }}",
                            "amount": item_amount,
                            "item_id": item_id,
                            "item_amount": item_amount,
                            "balance": balance,
                            "mobile": mobile,
                            "payment_mode": "momo",
                            "qty": qty,
                            "first_name": first_name,
                            "last_name": last_name,
                            "email": email,
                            "kyc_mobile": kyc_mobile,
                            "company": company,
                            "customerType": customerType,
                            "tpin": tpin,
                            "address": address,
                            "id": national_id,
                            "form_values": form_fields
                        },
                        beforeSend: function () {
                            overlay = setOverlay('Processing payment. Please wait...', '.payments-wrapper .card .card-body');
                        },
                        success: function (data) {
                            if (data.status === 'SUCCESS') {
                                order_id = data.reference
                                $('.d-order-id').text(order_id)
                                console.log(data)
                                console.log(order_id)
                                $('.payments-wrapper').addClass('blur')
                                $('.receipt-wrapper').removeClass('blur')
                                $('receipt-wrapper').show()
                                check_status = true
                            } else {
                                swal('Could not initiate payment', data.statusText, 'warning')
                            }
                            overlay.unblock()
                        },
                        error: function (data) {
                            overlay.unblock()
                            swal('Something went wrong', 'Ensure you have an active internet connection or try again later.')
                        }
                    })
                }

            })


            setInterval(() => {
                checkTransactionStatus()
            }, 5000);


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
                if ($('#mobileNumber').val().trim() === '' || $('#mobileNumber').val().length === 3) {
                    $('#mobileNumber').val($(this).data('prefix'))
                    var className = $(this).data('class');
                    $('.p-mode').css('filter', 'grayscale(1)');
                    $('.p-mode.' + className).css('filter', 'grayscale(0)');
                }
                $('#mobileNumber').focus()
            })

            $('#qty_field').on('change', function () {
                qty = parseFloat($(this).val())
                var total = unit_amount * qty
                item_amount = total
                $('.d-amount').text(total)
                $('#amount_field').val(total)
            })


            function checkTransactionStatus() {
                var reference = order_id
                if (check_status) {
                    $.ajax({
                        url: '{{url("frm/$app->id/status")}}',
                        method: 'get',
                        data: {
                            "_token": "{{ csrf_token() }}",
                            "reference": order_id
                        },
                        success: function (data) {
                            if (data.status === 'SUCCESS') {
                                $('.transaction-status-indicator').removeClass('text-azure').addClass('text-success')
                                $('.transaction-status-indicator').html('<i class="fa fa-check"></i> Paid');

                                $('.new-transaction').removeClass('disabled')
                                $('.print-receipt').removeClass('disabled')
                                $('.print-receipt').attr('href', '{{url("frm/$app->id/receipt/")}}/' + data.ref)

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
                            // swal('Something went wrong', 'Ensure you have an active internet connection or try again later.')
                        }
                    })
                }
            }


            $(function () {
                toggleFields();
                $('#customerType').change(function () {
                    toggleFields();
                });

                $('#mobile').on('paste keyup', function (e) {
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

        });
    </script>
    @vite('resources/js/app.js')
    @livewireScripts
@endsection
