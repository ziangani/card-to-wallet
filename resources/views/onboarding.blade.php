@extends('layouts.frontend')

@section('body')
    <style>

        .icon {
            width: 40px;
            height: auto;
            border-radius: 6px;
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

        .fw-bold {
            font-weight: bold;
        }

        .text-1 {
            font-size: .8rem;
        }

        .fw-600 {
            font-weight: 600;
        }

        .fw-500 {
            font-weight: 500;
        }

        .text-6 {
            font-size: 1.2rem;
        }

        .shadow {
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
        }


        /*For smaller screens*/
        @media (max-width: 1250px) {
            .card .card-footer .btn {
                padding: 0.5em;
            }
        }

        @media (min-width: 768px) {
            .payment-tab .card {
                min-height: 420px;
            }

            .form-check {
                min-width: 13em;
            }

        }
    </style>

    <div class="main-content">
        @include('frontend.partials.navigation')
        <div class="page-wrapper">
            <!-- Page header -->
            {{--            <div class="page-header d-print-none">--}}
            {{--                <div class="container-xl">--}}
            {{--                    <div class="row g-2 align-items-center">--}}
            {{--                        <div class="col">--}}
            {{--                            <h2 class="page-title text-white">--}}
            {{--                                Onboarding Portal--}}
            {{--                            </h2>--}}
            {{--                        </div>--}}
            {{--                    </div>--}}
            {{--                </div>--}}
            {{--            </div>--}}
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
                                <div class="card-header">
                                    <h3 class="card-title mb-0>">Application Progress</h3>
                                </div>
                                <div class="card-body">
                                    <h3 class="card-title">You're here:</h3>
                                    <ul class="steps steps-counter steps-vertical">
                                        <li class="step-item index-0 active">
                                            <div class="h4 m-0">Welcome</div>
                                            <div class="text-secondary">
                                                Review requirements before starting the application process.
                                            </div>
                                        </li>
                                        @php( $i = 1 )
                                        @foreach($sections as $section_id => $section)
                                            <li class="step-item index-{{$i++}}">
                                                <div class="h4 m-0">{{$section['name']}}</div>
                                                <div class="text-secondary">
                                                    {{$section['description']}}
                                                </div>
                                            </li>
                                        @endforeach

                                        <li class="step-item index-{{$i++}}">
                                            <div class="h4 m-0">Summary</div>
                                            <div class="text-secondary">
                                                Review the summary of your application.
                                            </div>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-8">
                            <div class="card payment-card pane-0 active" data-index="0">
                                <div class="card-header">
                                    <h3 class="card-title">Welcome to {{config('app.name')}} the Onboarding Portal</h3>
                                </div>
                                <div class="card-body">
                                    <div class="guide">
                                        <div class="container">
                                            <h1>Merchant Onboarding Requirements</h1>
                                            <p>Before proceeding with the onboarding process, please carefully review
                                                the requirements outlined below. Ensuring you meet these requirements
                                                and have all necessary documents will facilitate a smooth and efficient
                                                onboarding experience.</p>

                                            <div class="accordion" id="onboardingAccordion">

                                                <!-- Important Notes Section -->
                                                <div class="accordion-item">
                                                    <h2 class="accordion-header" id="notesHeading">
                                                        <button class="accordion-button collapsed" type="button"
                                                                data-bs-toggle="collapse" data-bs-target="#notes"
                                                                aria-expanded="false" aria-controls="notes">
                                                            Important Notes
                                                        </button>
                                                    </h2>
                                                    <div id="notes" class="accordion-collapse collapse"
                                                         aria-labelledby="notesHeading"
                                                         data-bs-parent="#onboardingAccordion">
                                                        <div class="accordion-body">
                                                            <ul>
                                                                <li><strong>Accuracy and Completeness:</strong> Ensure
                                                                    that all forms and documents are filled out
                                                                    completely and accurately to avoid delays.
                                                                </li>
                                                                <li><strong>Meeting Requirements:</strong> Meeting these
                                                                    minimum requirements does not guarantee approval;
                                                                    each application is subject to a review and approval
                                                                    process.
                                                                </li>
                                                                <li><strong>Confidentiality:</strong> All provided
                                                                    information will be handled with the highest level
                                                                    of confidentiality in accordance with data
                                                                    protection regulations.
                                                                </li>
                                                            </ul>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Business Category Section -->
                                                <div class="accordion-item">
                                                    <h2 class="accordion-header" id="businessCategoryHeading">
                                                        <button class="accordion-button collapsed" type="button"
                                                                data-bs-toggle="collapse"
                                                                data-bs-target="#businessCategory" aria-expanded="true"
                                                                aria-controls="businessCategory">
                                                            Business Category Classification
                                                        </button>
                                                    </h2>
                                                    <div id="businessCategory" class="accordion-collapse collapse"
                                                         aria-labelledby="businessCategoryHeading"
                                                         data-bs-parent="#onboardingAccordion">
                                                        <div class="accordion-body">
                                                            <p>Determine the correct business category for your
                                                                organization (e.g., Sole Proprietorship, Partnership,
                                                                Limited Liability Company, Public Limited Liability
                                                                Company, NGO, Religious Organization, etc.). This will
                                                                dictate specific onboarding requirements and
                                                                documentation.</p>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Documentation Section -->
                                                <div class="accordion-item">
                                                    <h2 class="accordion-header" id="documentationHeading">
                                                        <button class="accordion-button collapsed" type="button"
                                                                data-bs-toggle="collapse"
                                                                data-bs-target="#documentation" aria-expanded="false"
                                                                aria-controls="documentation">
                                                            Required Documentation
                                                        </button>
                                                    </h2>
                                                    <div id="documentation" class="accordion-collapse collapse"
                                                         aria-labelledby="documentationHeading"
                                                         data-bs-parent="#onboardingAccordion">
                                                        <div class="accordion-body">
                                                            <p>Prepare the following commonly required documents,
                                                                irrespective of your business category:</p>
                                                            <ul>
                                                                <li>TPIN Certificate: Your Taxpayer Identification
                                                                    Number (TPIN) certificate.
                                                                </li>
                                                                <li>Business License: A valid business license
                                                                    confirming your authorization to operate.
                                                                </li>
                                                                <li>Proof of Location/Address: Documentation verifying
                                                                    your business’s physical address (e.g., utility
                                                                    bill, rental
                                                                    agreement, property deed).
                                                                </li>
                                                                <li>Identification Documents: Copies of National
                                                                    Registration Cards (NRC), Passports, or Driver's
                                                                    Licenses for all directors, owners, or key
                                                                    representatives.
                                                                </li>
                                                                <li>Certificate of
                                                                    Registration/Incorporation/Compliance: The official
                                                                    certificate that confirms your business’s legal
                                                                    registration.
                                                                </li>
                                                            </ul>
                                                            <p><strong>Additional Documents Based on Business
                                                                    Category:</strong></p>
                                                            <ul>
                                                                <li><strong>Sole Ownership, Partnership, Club,
                                                                        Association, Society, Others:</strong>
                                                                    <ul>
                                                                        <li>Constitution or Partnership Deed (where
                                                                            applicable).
                                                                        </li>
                                                                        <li>
                                                                            Resolution authorizing merchant onboarding
                                                                            and Authorized representatives.
                                                                        </li>
                                                                    </ul>
                                                                </li>
                                                                <li><strong>Public Limited Liability, Limited Liability
                                                                        Company:</strong>
                                                                    <ul>
                                                                        <li>Memorandum/Articles of Association.</li>
                                                                        <li>Latest PACRA search printout.</li>
                                                                    </ul>
                                                                </li>
                                                                <li><strong>NGO, Religious Organizations:</strong>
                                                                    <ul>
                                                                        <li>Constitution (for NGOs) or relevant
                                                                            governing documents.
                                                                        </li>
                                                                        <li>
                                                                            Resolution authorizing merchant onboarding
                                                                            and Authorized representatives.
                                                                        </li>
                                                                    </ul>
                                                                </li>
                                                            </ul>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Financial Information Section -->
                                                <div class="accordion-item">
                                                    <h2 class="accordion-header" id="financialInfoHeading">
                                                        <button class="accordion-button collapsed" type="button"
                                                                data-bs-toggle="collapse"
                                                                data-bs-target="#financialInfo" aria-expanded="false"
                                                                aria-controls="financialInfo">
                                                            Financial Information
                                                        </button>
                                                    </h2>
                                                    <div id="financialInfo" class="accordion-collapse collapse"
                                                         aria-labelledby="financialInfoHeading"
                                                         data-bs-parent="#onboardingAccordion">
                                                        <div class="accordion-body">
                                                            <ul>
                                                                <li>Banking Details: Recent bank statements (within the
                                                                    last 3 months), and full bank account details for
                                                                    receiving payments. This may include a voided check
                                                                    or a letter from the bank confirming account
                                                                    details.
                                                                </li>
                                                            </ul>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Compliance Section -->
                                                <div class="accordion-item">
                                                    <h2 class="accordion-header" id="complianceHeading">
                                                        <button class="accordion-button collapsed" type="button"
                                                                data-bs-toggle="collapse" data-bs-target="#compliance"
                                                                aria-expanded="false" aria-controls="compliance">
                                                            Compliance and Legal Verification
                                                        </button>
                                                    </h2>
                                                    <div id="compliance" class="accordion-collapse collapse"
                                                         aria-labelledby="complianceHeading"
                                                         data-bs-parent="#onboardingAccordion">
                                                        <div class="accordion-body">
                                                            <ul>
                                                                <li>Be prepared for a compliance review, which may
                                                                    involve verifying your business operations,
                                                                    financial standing, and adherence to anti-money
                                                                    laundering (AML) and counter-terrorism financing
                                                                    (CTF) regulations.
                                                                </li>
                                                            </ul>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Contract Section -->
                                                <div class="accordion-item">
                                                    <h2 class="accordion-header" id="contractHeading">
                                                        <button class="accordion-button collapsed" type="button"
                                                                data-bs-toggle="collapse" data-bs-target="#contract"
                                                                aria-expanded="false" aria-controls="contract">
                                                            Contractual Agreement
                                                        </button>
                                                    </h2>
                                                    <div id="contract" class="accordion-collapse collapse"
                                                         aria-labelledby="contractHeading"
                                                         data-bs-parent="#onboardingAccordion">
                                                        <div class="accordion-body">
                                                            <ul>
                                                                <li>Review and sign the Merchant Service Agreement (MSA)
                                                                    provided during onboarding, which outlines the terms
                                                                    and conditions of using our payment gateway
                                                                    services.
                                                                </li>
                                                            </ul>
                                                        </div>
                                                    </div>
                                                </div>

                                            </div>
                                        </div>
                                        <div class="terms my-5">
                                            <div class="form-check">
                                                <label class="form-check">
                                                    <input class="form-check-input read-terms" type="checkbox">
                                                    <span class="form-check-label fw-bold">
                                                        By ticking this box, I confirm that I have read and understood the requirements outlined above.
                                                      </span>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-footer">
                                    <div class="row align-items-center">
                                        <div class="col"><a href="#" class="btn go-back disabled">Go Back</a></div>
                                        <div class="col-auto">
                                            <button type="button" class="btn btn-primary start">
                                                Proceed
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            @php( $i = count($sections) + 1 )
                            <div class="card payment-card- pane-{{$i}} " data-index="{{$i}}"
                                 style="display: none">
                                <div class="card-header">
                                    <h3 class="card-title">
                                        Review and Confirm Application
                                        <span class="card-subtitle">
                                            - Please review the information you've provided before proceeding.
                                        </span>
                                    </h3>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-12 payment-tab summary-wrapper">
                                            <div class="card bg-white shadow">
                                                <div class="card-header">
                                                    <h3 class="card-title>">Summary</h3>
                                                </div>
                                                <div class="card-body">
                                                    <div class="summary-accordion accordion"></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-footer">
                                    <div class="row align-items-center">
                                        <div class="col">
                                            <button type="button" class="btn go-back"
                                                    data-index="{{ $i = count($sections) + 1}}">Go Back
                                            </button>
                                        </div>
                                        <div class="col-auto">
                                            <button type="button"
                                                    class="btn btn-primary finish">
                                                Proceed
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            @php( $i = 0 )
                            @foreach($sections as $section_id => $section)
                                @php($i++)
                                <div class="card payment-card pane pane-{{$i}}" data-section="{{$section_id}}" data-index="{{$i}}"
                                     style="display: none">
                                    <div class="card-header">
                                        <h3 class="card-title">
                                            {{$section['name']}} <span class="card-subtitle">
                                                - {{$section['description']}}
                                            </span>
                                        </h3>
                                    </div>
                                    <div class="card-body">
                                        <div class="guide">
                                            <div class="mb-3">Fields with an <span class="text-danger">*</span> are
                                                required
                                            </div>
                                            <div class="mb-3">
                                                {{$section['details']}}
                                            </div>

                                            <div class="fields">
                                                @php($is_split_form = $section['is_split_form'])
                                                @if($section_id === 'business_ownership')
                                                    <div id="owners-container">
                                                        <div class="owner-section" data-owner-index="0">
                                                            <div class="d-flex justify-content-between align-items-center mb-3">
                                                                <h4 class="owner-title">Owner 1</h4>
                                                            </div>
                                                            <div class="p-0 {{($is_split_form) ? 'row' : ''}}">
                                                @else
                                                <div class="p-0 {{($is_split_form) ? 'row' : ''}}">
                                                @endif
                                                    @foreach($section['fields'] as $key => $field)
                                                        @php($field = (object)$field)



                                                        @if($field->type == 'table')
                                                            <div
                                                                class="mb-4 {{($is_split_form) ? 'col-md-12' : ''}}">
                                                                <label
                                                                    class="form-label {{((isset($field->required) && $field->required)? 'required' : '')}}"
                                                                    for="{{$key}}">{{$field->label}}</label>
                                                                @if($field->split)
                                                                    <div class="row">
                                                                        <div class="col-md-6">
                                                                            <table class="table table-bordered">
                                                                                <thead>
                                                                                <tr>
                                                                                    @foreach($field->headers as $header)
                                                                                        <th>{{$header}}</th>
                                                                                    @endforeach
                                                                                </tr>
                                                                                </thead>
                                                                                <tbody>
                                                                                @for($row = 0; $row < ceil($field->rows / 2); $row++)
                                                                                    <tr>
                                                                                        @foreach($field->fields as $field_key => $field_value)
                                                                                            <td>
                                                                                                @if($field_value['type'] == 'select')
                                                                                                    <select
                                                                                                        class="form-select {{((isset($field->required) && $field->required)? 'foik' : '')}}"
                                                                                                        {{((isset($field->required) && $field->required)? 'required' : '')}}
                                                                                                        name="{{$key}}_{{$row}}_{{$field_key}}"
                                                                                                        data-section="{{$section['name']}}"
                                                                                                        placeholder="{{$field_value['placeholder']}}"
                                                                                                        id="{{$key}}_{{$row}}_{{$field_key}}">
                                                                                                        <option
                                                                                                            value="">
                                                                                                            -
                                                                                                            Select
                                                                                                            One -
                                                                                                        </option>
                                                                                                        @foreach($field_value['options'] as $option_id => $option_value)
                                                                                                            <option
                                                                                                                value="{{$option_id}}">{{$option_value}}</option>
                                                                                                        @endforeach
                                                                                                    </select>
                                                                                                @else
                                                                                                    <input
                                                                                                        type="{{$field_value['type']}}"
                                                                                                        {{((isset($field->required) && $field->required)? 'required' : '')}}
                                                                                                        class="form-control {{((isset($field->required) && $field->required)? 'foik' : '')}}"
                                                                                                        name="{{$key}}_{{$row}}_{{$field_key}}"
                                                                                                        data-section="{{$section['name']}}"
                                                                                                        id="{{$key}}_{{$row}}_{{$field_key}}"
                                                                                                        placeholder="{{$field_value['placeholder']}}">
                                                                                                @endif
                                                                                            </td>
                                                                                        @endforeach
                                                                                    </tr>
                                                                                @endfor
                                                                                </tbody>
                                                                            </table>
                                                                        </div>
                                                                        <div class="col-md-6">
                                                                            <table class="table table-bordered">
                                                                                <thead>
                                                                                <tr>
                                                                                    @foreach($field->headers as $header)
                                                                                        <th>{{$header}}</th>
                                                                                    @endforeach
                                                                                </tr>
                                                                                </thead>
                                                                                <tbody>
                                                                                @for($row = ceil($field->rows / 2); $row < $field->rows; $row++)
                                                                                    <tr>
                                                                                        @foreach($field->fields as $field_key => $field_value)
                                                                                            <td>
                                                                                                @if($field_value['type'] == 'select')
                                                                                                    <select
                                                                                                        class="form-select {{((isset($field->required) && $field->required)? 'foik' : '')}}"
                                                                                                        data-section="{{$section['name']}}"
                                                                                                        name="{{$key}}_{{$row}}_{{$field_key}}"
                                                                                                        {{((isset($field->required) && $field->required)? 'required' : '')}}
                                                                                                        id="{{$key}}_{{$row}}_{{$field_key}}">
                                                                                                        <option
                                                                                                            value="">
                                                                                                            -
                                                                                                            Select
                                                                                                            One -
                                                                                                        </option>
                                                                                                        @foreach($field_value['options'] as $option_id => $option_value)
                                                                                                            <option
                                                                                                                value="{{$option_id}}">{{$option_value}}</option>
                                                                                                        @endforeach
                                                                                                    </select>
                                                                                                @else
                                                                                                    <input
                                                                                                        type="{{$field_value['type']}}"
                                                                                                        {{((isset($field->required) && $field->required)? 'required' : '')}}
                                                                                                        class="form-control {{((isset($field->required) && $field->required)? 'foik' : '')}}"
                                                                                                        data-section="{{$section['name']}}"
                                                                                                        name="{{$key}}_{{$row}}_{{$field_key}}"
                                                                                                        id="{{$key}}_{{$row}}_{{$field_key}}"
                                                                                                        placeholder="{{$field_value['placeholder']}}">
                                                                                                @endif
                                                                                            </td>
                                                                                        @endforeach
                                                                                    </tr>
                                                                                @endfor
                                                                                </tbody>
                                                                            </table>
                                                                        </div>
                                                                    </div>
                                                                @else
                                                                    <table class="table table-bordered">
                                                                        <thead>
                                                                        <tr>
                                                                            @foreach($field->headers as $header)
                                                                                <th>{{$header}}</th>
                                                                            @endforeach
                                                                        </tr>
                                                                        </thead>
                                                                        <tbody>
                                                                        @for($row = 0; $row < $field->rows; $row++)
                                                                            <tr>
                                                                                @foreach($field->fields as $field_key => $field_value)
                                                                                    <td>
                                                                                        @if($field_value['type'] == 'select')
                                                                                            <select
                                                                                                class="form-select {{((isset($field->required) && $field->required)? 'foik' : '')}}"
                                                                                                data-section="{{$section['name']}}"
                                                                                                name="{{$key}}_{{$row}}_{{$field_key}}"
                                                                                                {{((isset($field->required) && $field->required)? 'required' : '')}}
                                                                                                id="{{$key}}_{{$row}}_{{$field_key}}">
                                                                                                <option value=""> -
                                                                                                    Select One -
                                                                                                </option>
                                                                                                @foreach($field_value['options'] as $option_id => $option_value)
                                                                                                    <option
                                                                                                        value="{{$option_id}}">{{$option_value}}</option>
                                                                                                @endforeach
                                                                                            </select>
                                                                                        @else
                                                                                            <input
                                                                                                type="{{$field_value['type']}}"
                                                                                                {{((isset($field->required) && $field->required)? 'required' : '')}}
                                                                                                class="form-control {{((isset($field->required) && $field->required)? 'foik' : '')}}"
                                                                                                data-section="{{$section['name']}}"
                                                                                                name="{{$key}}_{{$row}}_{{$field_key}}"
                                                                                                id="{{$key}}_{{$row}}_{{$field_key}}"
                                                                                                placeholder="{{$field_value['placeholder']}}">
                                                                                        @endif
                                                                                    </td>
                                                                                @endforeach
                                                                            </tr>
                                                                        @endfor
                                                                        </tbody>
                                                                    </table>
                                                                @endif
                                                            </div>
                                                        @elseif($field->type == 'select')
                                                            <div
                                                                class="mb-4 {{($is_split_form) ? 'col-md-6' : ''}}">
                                                                <label
                                                                    class="form-label {{((isset($field->required) && $field->required)? 'required' : '')}}"
                                                                    for="{{$key}}">{{$field->label}}</label>
                                                                <select
                                                                    class="{{((isset($field->required) && $field->required)? 'foik' : '')}} form-select"
                                                                    name="{{$key}}"
                                                                    data-section="{{$section['name']}}"
                                                                    id="{{$key}}" {{((isset($field->required) && $field->required)? 'required' : '')}}>
                                                                    <option value=""> - Select One -</option>
                                                                    @foreach($field->options as $option_id => $option_value)
                                                                        <option
                                                                            value="{{$option_id}}">{{$option_value}}</option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                        @elseif($field->type == 'attachments')

                                                            @foreach($field->fields as $key => $field)
                                                                @php($field = (object)$field)

                                                                <div
                                                                    class="mb-4 {{($is_split_form) ? 'col-md-6' : ''}}">

                                                                    <label
                                                                        class="form-label {{((isset($field->required) && $field->required)? 'required' : '')}}"
                                                                        for="{{$key}}">{{$field->label}}</label>
                                                                    <form
                                                                        action="{{url('admissions/uploads/validate')}}"
                                                                        class="needsclick dz-clickable mb-4"
                                                                        id="upload-box-{{$key}}"
                                                                        method="post"
                                                                        enctype="multipart/form-data">
                                                                        {{csrf_field()}}
                                                                        <div class="fallback">
                                                                            <input name="attachment[{{$key}}]"
                                                                                   class="foik"
                                                                                   data-section="attachments"
                                                                                   type="file"/>
                                                                        </div>
                                                                        <div class="dz-message needsclick">
                                                                            <button type="button"
                                                                                    class="dz-button">Drop files
                                                                                here or click to
                                                                                upload.
                                                                            </button>
                                                                            <br>
                                                                            <span class="note needsclick">
                                                                                    Supported formats are <strong>Any Image Format</strong>, <strong>Mircosoft Word</strong> and <strong>PDF</strong>
                                                                                </span>
                                                                        </div>
                                                                    </form>
                                                                </div>
                                                            @endforeach
                                                        @elseif($field->type == 'checkbox' || $field->type == 'radio')

                                                            <div
                                                                class="mb-4 {{(count($field->options)> 6) ? 'col-md-12' : 'col-md-12'}}">
                                                                <label
                                                                    class="form-label {{((isset($field->required) && $field->required)? 'required' : '')}}"
                                                                    for="{{$key}}">{{$field->label}}</label>
                                                                <div>
                                                                    @foreach($field->options as $option_id => $option_value)
                                                                        <label
                                                                            class="form-check form-check-inline">
                                                                            <input
                                                                                class="form-check-input {{((isset($field->required) && $field->required)? 'foik' : '')}}"
                                                                                {{((isset($field->required) && $field->required)? 'required' : '')}}
                                                                                type="{{$field->type}}"
                                                                                name="{{$key}}"
                                                                                data-section="{{$section['name']}}"
                                                                                placeholder="{{$field->placeholder ?? ''}}"
                                                                                value="{{$option_id}}">
                                                                            <span class="form-check-label">
                                                                                    {{$option_value}}
                                                                                </span>
                                                                        </label>
                                                                    @endforeach
                                                                </div>
                                                            </div>
                                                        @elseif($field->type == 'select-group')
                                                            <div
                                                                class="mb-4 {{($is_split_form) ? 'col-md-6' : ''}}">
                                                                <label
                                                                    class="form-label {{((isset($field->required) && $field->required)? 'required' : '')}}"
                                                                    for="{{$key}}">{{$field->label}}</label>
                                                                <select
                                                                    class="{{(((isset($field->required) && $field->required)&& $field->required) ? 'foik' : '')}} form-select"
                                                                    name="{{$key}}"
                                                                    data-section="{{$section['name']}}"
                                                                    {{((isset($field->required) && $field->required)? 'required' : '')}}
                                                                    id="{{$key}}" {{((((isset($field->required) && $field->required)&& $field->required) && $field->required) ? 'required' : '')}}>
                                                                    <option value=""> - Select One -
                                                                    </option>
                                                                    @foreach($field->options as $option_header => $option_values)
                                                                        <optgroup
                                                                            label="{{$option_header}}">
                                                                            @foreach($option_values as $option_id => $option_value)
                                                                                <option
                                                                                    value="{{$option_id}}">{{$option_value}}</option>
                                                                            @endforeach
                                                                            @endforeach
                                                                        </optgroup>
                                                                </select>
                                                            </div>
                                                        @elseif($field->type == 'text' || $field->type == 'number' || $field->type == 'email' || $field->type == 'date')
                                                            <div
                                                                class="form-group mb-3 {{($is_split_form) ? 'col-md-6' : ''}}">
                                                                <label
                                                                    class="form-label {{(((isset($field->required) && $field->required)&& $field->required) ? 'required' : '')}}"
                                                                    for="{{$key}}">{{$field->label}}</label>
                                                                <div class="input-group">
                                                                    @if(isset($field->prefix))
                                                                        <span
                                                                            class="input-group-text">{{$field->prefix}}</span>
                                                                    @endif
                                                                    <input
                                                                        class="{{(((isset($field->required) && $field->required)&& $field->required) ? 'foik' : '')}} form-control"
                                                                        id="{{$key}}"
                                                                        data-section="{{$section['name']}}"
                                                                        placeholder="{{$field->placeholder ?? ''}}"
                                                                        {{((isset($field->required) && $field->required) ? 'required' : '')}}
                                                                        {{(isset($field->autofocus) ? 'autofocus' : '')}}
                                                                        {{(isset($field->readonly) ? 'readonly' : '')}}
                                                                        {{(isset($field->disabled) ? 'disabled' : '')}}
                                                                        @if($field->type == 'number')
                                                                            {{(isset($field->min) ? 'min=' . $field->min : '')}}
                                                                            {{(isset($field->max) ? 'max=' . $field->max : '')}}
                                                                            {{(isset($field->step) ? 'step=' . $field->step : '')}}
                                                                            {{(isset($field->length) ? 'maxlength=' . $field->length : '')}}
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
                                            @if($section_id === 'business_ownership')
                                                    </div>
                                                </div>
                                                <div class="mt-4">
                                                    <button type="button" class="btn btn-secondary" id="add-owner-btn">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-plus" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                                            <path d="M12 5l0 14" />
                                                            <path d="M5 12l14 0" />
                                                        </svg>
                                                        Add Another Owner
                                                    </button>
                                                </div>
                                            @endif

                                        </div>
                                    </div>
                                    <div class="card-footer">
                                        <div class="row align-items-center">
                                            <div class="col"><a href="#" class="btn go-back" data-index="{{$i}}">Go
                                                    Back</a></div>
                                            <div class="col-auto">
                                                <button type="button" class="btn btn-primary move-on"
                                                        data-index="{{$i}}">
                                                    Proceed
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
            @include('frontend.partials.footer')
        </div>
    </div>


    <div class="modal modal-blur fade" id="modalDocs" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Required Documents</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                <div class="modal-body">
                    <h3>
                        Ensure you have the below before proceeding
                    </h3>
                    <div class="mb-3">
                        <ol>
                            <li>Two recent size photos</li>
                            <li>Photocopies of current passport</li>
                            <li>Letter of admission from LAMU</li>
                            <li>Status of the host, parents or guardian</li>
                            <li>Proof of commitment from the sponsor</li>
                            <li>Police clearance report from your country</li>
                            <li>Medical report from a government hospital, including chest X-ray</li>
                            <li>Covering letter</li>
                        </ol>
                    </div>
                </div>
                <div class="modal-footer">
                    <div class="w-100">
                        <div class="row">
                            <div class="col"><a href="#" class="btn w-100" data-bs-dismiss="modal">
                                    Cancel
                                </a></div>
                            <div class="col"><a href="#" class="btn btn-primary w-100 start-application"
                                                data-bs-dismiss="modal">
                                    Apply Now
                                </a></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal modal-blur fade" id="modalTracking" tabindex="-1" role="dialog" aria-hidden="true">
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
                        Please provide your Tracking Number.
                    </h3>
                    <div class="mb-3">
                        <input type="text" class="form-control" placeholder="Tracking Number">
                        <small class="small text-muted">
                            The tracking number can be found in the SMS and Email sent to you.
                        </small>
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
    @vite(['resources/js/techpay.js', 'resources/js/owners.js'])
    <script>
        var accept = "image/*,application/pdf,.pdf, application/vnd.openxmlformats-officedocument.wordprocessingml.document,application/msword";
        var upload_overlay = null;
        var dz_config = {
            paramName: "upload_file", // The name that will be used to transfer the file
            maxFilesize: 20, // MB
            addRemoveLinks: true,
            dictResponseError: 'Server not Configured',
            acceptedFiles: accept,
            timeout: 60000,
            init: function () {
                var self = this;
                // config
                self.options.addRemoveLinks = true;
                self.options.dictRemoveFile = "Delete";
                // Send file starts
                self.on("sending", function (file) {
                    upload_overlay = setOverlay('Uploading File...<span id="upload-progress">0</span>% done...', '.guide')
                    $('.meter').show();
                });

                // File upload Progress
                self.on("totaluploadprogress", function (progress) {
                    console.log("progress ", progress);
                    $('#upload-progress').text(progress);
                    $('.roller').width(progress + '%');
                });

                self.on("queuecomplete", function (progress) {
                    $('.meter').delay(999).slideUp(999);
                });

                self.on("success", function (file, response) {
                    console.log(response);
                    if (response.status === 'SUCCESS') {
                        upload_overlay.unblock();
                        // swal('Upload Successful', 'Your document has been uploaded successfully', 'success')
                    } else {
                        upload_overlay.unblock();
                        this.removeFile(file);
                        swal('Upload Failure', response.statusMessage, 'warning')
                    }
                });

                this.on("error", function (file) {
                    if (!file.accepted) {
                        this.removeFile(file);
                        if (upload_overlay != null)
                            upload_overlay.unblock();
                        swal('Invalid File Selected', 'Valid file formats include are: Any Image Format, Microsoft Word Document or PDF', 'warning')
                    } else {
                        if (upload_overlay != null)
                            upload_overlay.unblock();
                        this.removeFile(file);
                        swal('Upload Failure', 'We could not process your document at this time. Please try again later', 'warning')
                    }
                });
            }
        };


        @foreach($sections['attachment']['fields']['attachments']['fields'] as $field)
            dz_config.paramName = "attachment[{{ $field['name'] }}]";
        dz_config.params = {'field_name': '{{ $field['name'] }}'};
        var dz_{{ $field['name'] }} = new Dropzone("#upload-box-{{ $field['name'] }}", dz_config);
        $("#upload-box-{{ $field['name'] }}").addClass('dropzone');
        @endforeach

        function validateSupportingDocuments() {
            @foreach($sections['attachment']['fields']['attachments']['fields'] as $field)
            var files_{{ $field['name'] }} = dz_{{ $field['name'] }}.getFilesWithStatus('success');
            if (files_{{ $field['name'] }}.length < 1) {
                swal('Files not attached', 'Kindly attach a copy of your {{$field['label']}}', 'info');
                return false;
            }
            @endforeach
                return true;
        }
    </script>
@endsection
