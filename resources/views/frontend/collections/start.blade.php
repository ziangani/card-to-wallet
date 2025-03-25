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
                                Make An Application
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
                            <div class="bg-white">
                                @include('layouts.alerts')
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="card payment-card">
                                <div class="card-body">
                                    <h3 class="card-title">Your progress step:</h3>
                                    <ul class="steps  steps-counter steps-vertical">
                                        <li class="step-item active">
                                            <div class="h4 m-0">Find your bills</div>
                                            <div class="text-secondary">
                                                Use your Customer account number to get your outstanding bills.
                                            </div>
                                        </li>
                                        <li class="step-item">
                                            <div class="h4 m-0">Select Bill & Amount</div>
                                            <div class="text-secondary">
                                                Select the bills you'd like to pay for and elect the amount you'd like
                                                to pay.
                                            </div>
                                        </li>
                                        <li class="step-item">
                                            <div class="h4 m-0">Provide Your Details</div>
                                            <div class="text-secondary">Kindly provide your personal information.</div>
                                        </li>
                                        <li class="step-item">
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
                            <div class="card payment-card card-1">
                                <form action="{{url('getBills')}}" method="get" id="billsForm">
                                    <div class="card-header">
                                        <h3 class="card-title">Let's Find Your Bills</h3>
                                    </div>
                                    <div class="card-body">
                                        <div class="query-body">
                                            <div class="mb-3">
                                                <label class="form-label">Select your council</label>
                                                <select type="text" class="form-select" id="select-people"
                                                        name="service_provider">
                                                    @foreach($apps as $app)
                                                        <option value="{{$app->id}}"
                                                                data-custom-properties="&lt;span class=&quot;avatar avatar-xs&quot; style=&quot;background-image: url({{asset("$app->logo_name")}})&quot;&gt;&lt;/span&gt;">
                                                            {{$app->name}}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label" for="SearchBy">Search By</label>
                                                <select class="form-select" name="SearchBy" id="SearchBy">
                                                    <option value="1">Customer Account Number</option>
                                                </select>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label" for="accountNumber">Customer Account
                                                    Number
                                                    <small class="form-hint">
                                                        The number can be found on the statement you receive from your
                                                        council. Example account number: C1234567.
                                                        <span class="form-help" data-bs-toggle="popover"
                                                              data-bs-placement="top" data-bs-html="true"
                                                              data-bs-content="<p>The number can be found on the statement you receive from your council. Example account number: C1234567.</p>">?</span>
                                                    </small>
                                                </label>
                                                <div class="input-group">
                                                    <span class="input-group-text">C</span>
                                                    <input autofocus type="number" id="accountNumber"
                                                           class="form-control"
                                                           step="1" required name="account" value="{{old('account')}}"
                                                           placeholder="Enter your Customer Account Number">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-footer">
                                        <div class="row align-items-center">
                                            {{--                                        <div class="col">Learn more about <a href="#">Project</a></div>--}}
                                            <div class="col"><a href="{{url('/')}}" class="btn">Go Back</a></div>
                                            <div class="col-auto">
                                                <button type="button" class="btn btn-primary" id="get-account">
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

    <div class="modal modal-blur fade" id="modalCouncils" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-sm modal-dialog-centered" role="document">
            <div class="modal-content">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                <div class="modal-status bg-primary"></div>
                <div class="modal-body text-center py-4">
                    <svg xmlns="http://www.w3.org/2000/svg"
                         class="icon mb-3 text-muted icon-lg icon-tabler icon-tabler-map-search" width="24"
                         height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                         stroke-linecap="round" stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                        <path d="M11 18l-2 -1l-6 3v-13l6 -3l6 3l6 -3v7.5"/>
                        <path d="M9 4v13"/>
                        <path d="M15 7v5"/>
                        <path d="M18 18m-3 0a3 3 0 1 0 6 0a3 3 0 1 0 -6 0"/>
                        <path d="M20.2 20.2l1.8 1.8"/>
                    </svg>
                    <h3>
                        Which Council do you want to pay?
                    </h3>
                    <div class="mb-3">
                        <select type="text" class="form-select" id="select-council"
                                name="council">
                            @foreach($apps as $app)
                                <option value="{{$app->id}}"
                                        data-custom-properties="&lt;span class=&quot;avatar avatar-xs&quot; style=&quot;background-image: url({{asset("general/merchant_application_logos/$app->logo_name")}})&quot;&gt;&lt;/span&gt;">
                                    {{$app->name}}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <div class="w-100">
                        <div class="row">
                            <div class="col"><a href="#" class="btn w-100" data-bs-dismiss="modal">
                                    Cancel
                                </a></div>
                            <div class="col"><a href="#" class="btn btn-primary w-100" data-bs-dismiss="modal">
                                    Proceed
                                </a></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    @parent
    {{--    @vite('resources/js/payments.js')--}}
    <script>
        jQuery(document).ready(function () {
            $('#accountNumber').val('20230000002')
            // $('#get-account').click()
            // $('#modalCouncils').modal('show')
            // new TomSelect('#select-council', {
            //     copyClassesToDropdown: false,
            //     dropdownParent: 'div',
            //     controlInput: '<input>',
            //     render: {
            //         item: function (data, escape) {
            //             if (data.customProperties) {
            //                 return '<div><span class="dropdown-item-indicator">' + data.customProperties + '</span>' + escape(data.text) + '</div>';
            //             }
            //             return '<div>' + escape(data.text) + '</div>';
            //         },
            //         option: function (data, escape) {
            //             if (data.customProperties) {
            //                 return '<div><span class="dropdown-item-indicator">' + data.customProperties + '</span>' + escape(data.text) + '</div>';
            //             }
            //             return '<div>' + escape(data.text) + '</div>';
            //         },
            //     },
            // });
        })
    </script>
    <script>
        // @formatter:off
        document.addEventListener("DOMContentLoaded", function () {
            var el;
            window.TomSelect && (new TomSelect(el = document.getElementById('select-people'), {
                copyClassesToDropdown: false,
                dropdownParent: 'body',
                controlInput: '<input>',
                render:{
                    item: function(data,escape) {
                        if( data.customProperties ){
                            return '<div><span class="dropdown-item-indicator">' + data.customProperties + '</span>' + escape(data.text) + '</div>';
                        }
                        return '<div>' + escape(data.text) + '</div>';
                    },
                    option: function(data,escape){
                        if( data.customProperties ){
                            return '<div><span class="dropdown-item-indicator">' + data.customProperties + '</span>' + escape(data.text) + '</div>';
                        }
                        return '<div>' + escape(data.text) + '</div>';
                    },
                },
            }));
        });
        // @formatter:on
    </script>
    <script>
        // $("#billsForm").validate({
        //     debug: true,
        //     submitHandler: function (form) {
        //         let overlay = setOverlay('Fetching bills', '.query-body');
        //         let nextButton = $('#get-account')
        //         $(nextButton).addClass('disabled')
        //         $(form).submit()
        //         console.log('debug')
        //     },
        //     errorClass: "is-invalid",
        //     validClass: "is-valid",
        //
        // });
    </script>
@endsection
