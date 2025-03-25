<?php

namespace App\Http\Controllers\Frontend;

use App\Common\GeneralStatus;
use App\Common\Helpers;
use App\Http\Controllers\Controller;
use App\Integrations\TechPay\HostedCheckOut;
use App\Models\Applications;
use App\Models\Attachments;
use App\Models\CompanyBank;
use App\Models\CompanyContact;
use App\Models\CompanyDetail;
use App\Models\CompanyFinancial;
use App\Models\CompanyOwnership;
use App\Models\CompanyWebsite;
use App\Models\Education;
use App\Models\NextOfKin;
use App\Models\OnboardingApplications;
use App\Models\PayerKyc;
use App\Models\Personal;
use App\Models\Programme;
use App\Models\Approval;
use App\Models\SmsNotifications;
use App\Models\TransactionsBreakdown;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class OnboardingController extends Controller
{
    public function validateDocuments(Request $request)
    {
        $field = $request->all();
        $field_key = key($field['attachment']);
        $field_name = 'attachment[' . $field_key . ']';
        $document = $request->attachment[$field_key];

        if (!$document) {
            return response()->json([
                'status' => 'ERROR',
                'statusMessage' => 'Document required'
            ]);
        }
        //image/ doc formats
        $validExtensions = array('pdf', 'png', 'jpg', 'bmp', 'doc', 'docx', 'webp', 'jpeg', 'gif');

        if (!in_array(strtolower($document->getClientOriginalExtension()), $validExtensions)) {
            return response()->json([
                'status' => 'ERROR',
                'statusMessage' => 'Invalid file format attached . Valid formats are ' . join(', ', $validExtensions)
            ]);
        }
        try {
            $certificateName = $document->getClientOriginalName();

            $newName = date('Ymdhis') . '_' . rand(10000, 999999) . '_' . $certificateName;
            $path = storage_path(DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'tmp');
            $request->attachment[$field_key]->move($path, $newName);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'ERROR',
                'statusMessage' => 'An error occurred' . $e->getMessage()
            ]);
        }
        $docs = $request->session()->get('docs');
        if (!$docs) {
            $docs = [];
        }
        $docs[$field_name] = $newName;
        session()->put('docs', $docs);

        return response()->json([
            'status' => 'SUCCESS',
            'statusMessage' => 'Validated successfully. ',
        ]);

    }

    public function index()
    {


        $application_fee = 1;
        $processing_fee = 0;
        $sections = [
            'general' => [
                'name' => 'General Information',
                'description' => 'Provide your general information',
                'details' => 'Please complete this section with information about your business or organization'
            ],
            'business_ownership' => [
                'name' => 'Business Ownership',
                'description' => 'This section gathers information about the beneficial owners of the business or company.',
                'details' => 'This section gathers information about the beneficial owners of the business or company. This is the list of directors/shareholders (list shareholders with 5% or more shares/voting right in the company)Included details of such persons below and should be accompanied by copies of identity documents.'
            ],
            'contact_information' => [
                'name' => 'Contact Information OF Authorized Personnel',
                'description' => 'This section gathers information about the designated contact person in your organization.',
                'is_split_form' => true,
                'details' => 'This section gathers information about the designated contact person in your organization. All correspondence between TECHMASTERS and your organization will be addressed to the persons specified below. Included details of contact persons below should be accompanied by copies of identity documents.'
            ],
            'financial_details' => [
                'name' => 'Financial Details',
                'description' => 'This section requests and gathers financial information about the business or company.',
                'is_split_form' => true,
                'details' => 'This section requests and gathers financial information about the business or company. All figures provided are expected to be as close to accurate as possible as this is part of the KYC framework as well as forming a part of TechPay’s risk management framework in compliance with Bank of Zambia regulations.'
            ],
            'website_information' => [
                'name' => 'Website Information',
                'description' => 'This section gathers information about the website or mobile app that you intend to connect to TechPay.',
                'is_split_form' => true,
                'details' => 'Kindly supply the relevant information where applicable about the website or mobile app that you intend to connect to the TechPay platform. Pick ONLY what is applicable or relevant to your implementation.'
            ],
            'bank_details' => [
                'name' => 'Bank Details',
                'description' => 'This section gathers information about the bank account that you intend to use for receiving payments.',
                'is_split_form' => true,
                'details' => 'Complete this section with information about your bank, mobile money, or e-wallet details, where your collected payments from your website or you use of the TechPay platform will be deposited as per the agreed settlement period in your merchant agreement. Kindly supply the relevant information where applicable and ensure it is correct.'
            ],
            'attachment' => [
                'name' => 'Attachments',
                'description' => 'In this section you can upload any required documents for your application',
                'is_split_form' => true,
                'details' => 'In this section you can upload any required documents for your application. Please ensure that the documents are clear and legible. The following documents are required for your application to be processed:'
            ],
        ];
        $sections['attachment']['fields'] = [
            'attachments' => [
                'name' => 'attachments',
                'label' => 'Attachments',
                'type' => 'attachments',
                'length' => 50,
                'description' => 'Attachments',
                'placeholder' => 'Attachments',
                'example' => 'Attachments',
                'required' => true,
                'fields' => [
                    'utility_bill' => [
                        'name' => 'utility_bill',
                        'label' => 'Utility Bill',
                        'type' => 'file',
                        'length' => 50,
                        'description' => 'Utility Bill',
                        'placeholder' => 'Utility Bill',
                        'example' => 'Utility Bill',
                        'required' => true,
                    ],
                    'tpin_certificate' => [
                        'name' => 'tpin_certificate',
                        'label' => 'TPIN Certificate',
                        'type' => 'file',
                        'length' => 50,
                        'description' => 'TPIN Certificate',
                        'placeholder' => 'TPIN Certificate',
                        'example' => 'TPIN Certificate',
                        'required' => true,
                    ],
                    'business_license' => [
                        'name' => 'business_license',
                        'label' => 'Business License',
                        'type' => 'file',
                        'length' => 50,
                        'description' => 'Business License',
                        'placeholder' => 'Business License',
                        'example' => 'Business License',
                        'required' => true,
                    ],
                    'proof_of_location' => [
                        'name' => 'proof_of_location',
                        'label' => 'Proof of Location/Address',
                        'type' => 'file',
                        'length' => 50,
                        'description' => 'Proof of Location/Address',
                        'placeholder' => 'Proof of Location/Address',
                        'example' => 'Proof of Location/Address',
                        'required' => true,
                    ],
                    'identification_documents' => [
                        'name' => 'identification_documents',
                        'label' => 'Identification Documents',
                        'type' => 'file',
                        'length' => 50,
                        'description' => 'Identification Documents',
                        'placeholder' => 'Identification Documents',
                        'example' => 'Identification Documents',
                        'required' => true,
                    ],
                    'certificate_of_registration' => [
                        'name' => 'certificate_of_registration',
                        'label' => 'Certificate of Registration/Incorporation/Compliance',
                        'type' => 'file',
                        'length' => 50,
                        'description' => 'Certificate of Registration/Incorporation/Compliance',
                        'placeholder' => 'Certificate of Registration/Incorporation/Compliance',
                        'example' => 'Certificate of Registration/Incorporation/Compliance',
                        'required' => true,
                    ],
                ]
            ],
        ];
        $sections['business_ownership']['fields'] = [
            'salutation' => [
                'name' => 'salutation',
                'label' => 'Salutation',
                'type' => 'select',
                'length' => 50,
                'description' => 'Salutation',
                'placeholder' => 'Salutation',
                'example' => 'Mr.',
                'required' => true,
                'options' => [
                    'Mr.' => 'Mr.',
                    'Ms.' => 'Ms.',
                    'Other' => 'Other',
                ],
            ],
            'full_names' => [
                'name' => 'full_names',
                'label' => 'Full Names',
                'type' => 'text',
                'length' => 50,
                'description' => 'Full Names',
                'placeholder' => 'Full Names',
                'example' => 'Mutale Masamu',
                'required' => true,
            ],
            'nationality' => [
                'name' => 'nationality',
                'label' => 'Nationality',
                'type' => 'select',
                'length' => 50,
                'description' => 'Nationality',
                'placeholder' => 'Nationality',
                'options' => [
                    'Zambian' => 'Zambian',
                    'Angolan' => 'Angolan',
                    'Botswana' => 'Botswana',
                    'Congolese' => 'Congolese',
                    'Malawian' => 'Malawian',
                    'Mozambican' => 'Mozambican',
                    'Namibian' => 'Namibian',
                    'South African' => 'South African',
                    'Tanzanian' => 'Tanzanian',
                    'Zimbabwean' => 'Zimbabwean',
                    'Other' => 'Other',
                ],
                'example' => 'Zambian',
                'required' => true,
            ],
            'date_of_birth' => [
                'name' => 'date_of_birth',
                'label' => 'Date of Birth',
                'type' => 'date',
                'length' => 50,
                'description' => 'Date of Birth',
                'placeholder' => 'Date of Birth',
                'example' => '1990-01-01',
                'required' => true,
            ],
            'place_of_birth' => [
                'name' => 'place_of_birth',
                'label' => 'Place of Birth',
                'type' => 'text',
                'length' => 50,
                'description' => 'Place of Birth',
                'placeholder' => 'Place of Birth',
                'example' => 'Lusaka',
                'required' => true,
            ],
            'id_type' => [
                'name' => 'id_type',
                'label' => 'ID Type',
                'type' => 'select',
                'length' => 50,
                'description' => 'ID Type',
                'placeholder' => 'ID Type',
                'options' => [
                    'NRC' => 'NRC',
                    'Passport' => 'Passport',
                    'Driver’s License' => 'Driver’s License',
                ],
                'example' => 'NRC',
                'required' => true,
            ],
            'identification_number' => [
                'name' => 'identification_number',
                'label' => 'Identification Number',
                'type' => 'text',
                'length' => 50,
                'description' => 'Identification Number',
                'placeholder' => 'Identification Number',
                'example' => '123456',
                'required' => true,
            ],
            'country_of_residence' => [
                'name' => 'country_of_residence',
                'label' => 'Country of Residence',
                'type' => 'select',
                'length' => 50,
                'description' => 'Country of Residence',
                'placeholder' => 'Country of Residence',
                'options' => [
                    'Zambia' => 'Zambia',
                    'Angola' => 'Angola',
                    'Botswana' => 'Botswana',
                    'Congo' => 'Congo',
                    'Malawi' => 'Malawi',
                    'Mozambique' => 'Mozambique',
                    'Namibia' => 'Namibia',
                    'South Africa' => 'South Africa',
                    'Tanzania' => 'Tanzania',
                    'Zimbabwe' => 'Zimbabwe',
                    'Other' => 'Other',
                ],
                'example' => 'Zambia',
                'required' => true,
            ],
            'residential_address' => [
                'name' => 'residential_address',
                'label' => 'Residential Address',
                'type' => 'text',
                'length' => 50,
                'description' => 'Residential Address',
                'placeholder' => 'Residential Address',
                'example' => '1234, Lusaka, Zambia',
                'required' => true,
            ],
            'designation' => [
                'name' => 'designation',
                'label' => 'Designation/Rank',
                'type' => 'text',
                'length' => 50,
                'description' => 'Designation',
                'placeholder' => 'Designation',
                'example' => 'Director',
                'required' => true,
            ],
            'mobile' => [
                'name' => 'mobile',
                'label' => 'Mobile Number',
                'type' => 'text',
                'length' => 50,
                'description' => 'Mobile Number',
                'placeholder' => 'Mobile Number',
                'example' => '0977123456',
                'required' => true,
            ],
            'email' => [
                'name' => 'email',
                'label' => 'Email',
                'type' => 'email',
                'length' => 50,
                'description' => 'Email',
                'placeholder' => 'Email',
                'example' => 'john@example.com',
                'required' => true,
            ],
        ];
        $sections['business_ownership']['is_split_form'] = true;
        $sections['general']['is_split_form'] = true;
        $sections['general']['fields'] = [
            'company_name' => [
                'name' => 'company_name',
                'label' => 'Company/Organization Name',
                'type' => 'text',
                'length' => 50,
                'description' => 'Company Name',
                'placeholder' => 'Company Name',
                'example' => 'John & Sons Limited',
                'required' => true,
            ],
            'trading_name' => [
                'name' => 'trading_name',
                'label' => 'Trading Name(If applicable)',
                'type' => 'text',
                'length' => 50,
                'description' => 'Trading Name',
                'placeholder' => 'Trading Name',
                'example' => 'John & Sons General Dealers',
                'required' => false,
            ],
            'type_of_ownership' => [
                'name' => 'type_of_ownership',
                'label' => 'Type of Ownership',
                'type' => 'select',
                'length' => 50,
                'description' => 'Type of Ownership',
                'placeholder' => 'Type of Ownership',
                'example' => 'Sole Ownership',
                'required' => true,
                'options' => [
                    'Sole Ownership' => 'Sole Ownership',
                    'Limited Liability Company' => 'Limited Liability Company',
                    'Partnership' => 'Partnership',
                    'Non-Profit Organization / NGO' => 'Non-Profit Organization / NGO',
                    'Public Limited Liability' => 'Public Limited Liability',
                    'Government' => 'Government',
                    'Religious' => 'Religious',
                ],
            ],
            'rc_number' => [
                'name' => 'rc_number',
                'label' => 'RC Number',
                'type' => 'text',
                'length' => 50,
                'description' => 'RC Number',
                'placeholder' => 'RC Number',
                'example' => '123456',
                'required' => true,
            ],
            'tpin' => [
                'name' => 'tpin',
                'label' => 'TPIN',
                'type' => 'number',
                'length' => 50,
                'description' => 'TPIN',
                'placeholder' => 'TPIN',
                'example' => '123456',
                'required' => true,
            ],
            'date_registered' => [
                'name' => 'date_registered',
                'label' => 'Date Registered',
                'type' => 'date',
                'length' => 50,
                'description' => 'Date Registered',
                'placeholder' => 'Date Registered',
                'example' => '2021-01-01',
                'required' => true,
            ],
            'nature_of_business' => [
                'name' => 'nature_of_business',
                'label' => 'Nature Of Business',
                'type' => 'select',
                'length' => 50,
                'description' => 'Nature Of Business',
                'placeholder' => 'Nature Of Business',
                'example' => 'General Dealers',
                'required' => true,
                'options' => [
                    'General Dealers' => 'General Dealers',
                    'Retail' => 'Retail',
                    'Wholesale' => 'Wholesale',
                    'Manufacturing' => 'Manufacturing',
                    'Agriculture' => 'Agriculture',
                    'Construction' => 'Construction',
                    'Mining' => 'Mining',
                    'Transport' => 'Transport',
                    'Hospitality' => 'Hospitality',
                    'Health' => 'Health',
                    'Education' => 'Education',
                    'ICT' => 'ICT',
                    'Finance' => 'Finance',
                    'Insurance' => 'Insurance',
                    'Real Estate' => 'Real Estate',
                    'Legal' => 'Legal',
                    'Consultancy' => 'Consultancy',
                    'Non-Profit' => 'Non-Profit',
                    'Other' => 'Other'
                ],
            ],
            'office_address' => [
                'name' => 'office_address',
                'label' => 'Office Address',
                'type' => 'text',
                'length' => 50,
                'description' => 'Office Address',
                'placeholder' => 'Office Address',
                'example' => '1234, Lusaka, Zambia',
                'required' => true,
            ],
            'postal_address' => [
                'name' => 'postal_address',
                'label' => 'Postal Address',
                'type' => 'text',
                'length' => 50,
                'description' => 'Postal Address',
                'placeholder' => 'Postal Address',
                'example' => '1234, Lusaka, Zambia',
                'required' => true,
            ],
            'country_of_incorporation' => [
                'name' => 'country_of_incorporation',
                'label' => 'Country of Incorporation',
                'type' => 'select',
                'length' => 50,
                'description' => 'Country of Incorporation',
                'placeholder' => 'Country of Incorporation',
                'options' => [
                    'Zambia' => 'Zambia',
                    'Angola' => 'Angola',
                    'Botswana' => 'Botswana',
                    'Congo' => 'Congo',
                    'Malawi' => 'Malawi',
                    'Mozambique' => 'Mozambique',
                    'Namibia' => 'Namibia',
                    'South Africa' => 'South Africa',
                    'Tanzania' => 'Tanzania',
                    'Zimbabwe' => 'Zimbabwe',
                    'Other' => 'Other',
                ],
                'example' => 'Zambia',
                'required' => true,
            ],
            'office_telephone' => [
                'name' => 'office_telephone',
                'label' => 'Office Telephone',
                'type' => 'text',
                'length' => 50,
                'description' => 'Office Telephone',
                'placeholder' => 'Office Telephone',
                'example' => '0977123456',
                'required' => true,
            ],
            'customer_service_telephone' => [
                'name' => 'customer_service_telephone',
                'label' => 'Customer Service Telephone',
                'type' => 'text',
                'length' => 50,
                'description' => 'Customer Service Telephone',
                'placeholder' => 'Customer Service Telephone',
                'example' => '0977123456',
                'required' => false,
            ],
            'official_email' => [
                'name' => 'official_email',
                'label' => 'Official Email',
                'type' => 'email',
                'length' => 50,
                'description' => 'Official Email',
                'placeholder' => 'Official Email',
                'example' => 'john@example.com',
                'required' => true,
            ],
            'customer_service_email' => [
                'name' => 'customer_service_email',
                'label' => 'Customer Service Email',
                'type' => 'email',
                'length' => 50,
                'description' => 'Customer Service Email',
                'placeholder' => 'Customer Service Email',
                'example' => 'service@example.com',
                'required' => false,
            ],
            'official_website' => [
                'name' => 'official_website',
                'label' => 'Official Website Address',
                'type' => 'text',
                'length' => 50,
                'description' => 'Official Website Address',
                'placeholder' => 'Official Website Address',
                'example' => 'https://example.com',
                'required' => false,
            ],
        ];
        $sections['contact_information']['fields'] = [
            'primary_full_name' => [
                'name' => 'primary_full_name',
                'label' => 'Primary Contact Full Name',
                'type' => 'text',
                'length' => 50,
                'description' => 'Full Name',
                'placeholder' => 'Full Name',
                'example' => 'John Mulenga',
                'required' => true,
            ],
            'primary_country' => [
                'name' => 'primary_country',
                'label' => 'Primary Contact Country',
                'type' => 'select',
                'length' => 50,
                'description' => 'Country',
                'placeholder' => 'Country',
                'options' => [
                    'Zambia' => 'Zambia',
                    'Angola' => 'Angola',
                    'Botswana' => 'Botswana',
                    'Congo' => 'Congo',
                    'Malawi' => 'Malawi',
                    'Mozambique' => 'Mozambique',
                    'Namibia' => 'Namibia',
                    'South Africa' => 'South Africa',
                    'Tanzania' => 'Tanzania',
                    'Zimbabwe' => 'Zimbabwe',
                    'Other' => 'Other',
                ],
                'example' => 'Zambia',
                'required' => true,
            ],
            'primary_phone_number' => [
                'name' => 'primary_phone_number',
                'label' => 'Primary Contact Phone Number',
                'type' => 'text',
                'length' => 12,
                'description' => 'Phone Number',
                'placeholder' => 'Phone Number',
                'example' => '0977123456',
                'required' => true,
            ],
            'primary_email' => [
                'name' => 'primary_email',
                'label' => 'Primary Contact Email',
                'type' => 'email',
                'length' => 50,
                'description' => 'Email',
                'placeholder' => 'Email',
                'example' => 'john@example.com',
                'required' => true,
            ],
            'primary_address' => [
                'name' => 'primary_address',
                'label' => 'Primary Contact Address',
                'type' => 'text',
                'length' => 50,
                'description' => 'Address',
                'placeholder' => 'Address',
                'example' => '1234, Lusaka, Zambia',
                'required' => true,
            ],
            'primary_town' => [
                'name' => 'primary_town',
                'label' => 'Primary Contact Town',
                'type' => 'text',
                'length' => 50,
                'description' => 'Town',
                'placeholder' => 'Town',
                'example' => 'Lusaka',
                'required' => true,
            ],
            'primary_designation' => [
                'name' => 'primary_designation',
                'label' => 'Primary Contact Designation/Rank',
                'type' => 'text',
                'length' => 50,
                'description' => 'Designation',
                'placeholder' => 'Designation',
                'example' => 'Director',
                'required' => true,
            ],
            'secondary_full_name' => [
                'name' => 'secondary_full_name',
                'label' => 'Secondary Contact Full Name',
                'type' => 'text',
                'length' => 50,
                'description' => 'Full Name',
                'placeholder' => 'Full Name',
                'example' => 'John Mulenga',
                'required' => false,
            ],
            'secondary_country' => [
                'name' => 'secondary_country',
                'label' => 'Secondary Contact Country',
                'type' => 'select',
                'length' => 50,
                'description' => 'Country',
                'placeholder' => 'Country',
                'options' => [
                    'Zambia' => 'Zambia',
                    'Angola' => 'Angola',
                    'Botswana' => 'Botswana',
                    'Congo' => 'Congo',
                    'Malawi' => 'Malawi',
                    'Mozambique' => 'Mozambique',
                    'Namibia' => 'Namibia',
                    'South Africa' => 'South Africa',
                    'Tanzania' => 'Tanzania',
                    'Zimbabwe' => 'Zimbabwe',
                    'Other' => 'Other',
                ],
                'example' => 'Zambia',
                'required' => false,
            ],
            'secondary_phone_number' => [
                'name' => 'secondary_phone_number',
                'label' => 'Secondary Contact Phone Number',
                'type' => 'text',
                'length' => 12,
                'description' => 'Phone Number',
                'placeholder' => 'Phone Number',
                'example' => '0977123456',
                'required' => false,
            ],
            'secondary_email' => [
                'name' => 'secondary_email',
                'label' => 'Secondary Contact Email',
                'type' => 'email',
                'length' => 50,
                'description' => 'Email',
                'placeholder' => 'Email',
                'example' => 'john@example.com',
                'required' => false,
            ],
            'secondary_address' => [
                'name' => 'secondary_address',
                'label' => 'Secondary Contact Address',
                'type' => 'text',
                'length' => 50,
                'description' => 'Address',
                'placeholder' => 'Address',
                'example' => '1234, Lusaka, Zambia',
                'required' => false,
            ],
            'secondary_town' => [
                'name' => 'secondary_town',
                'label' => 'Secondary Contact Town',
                'type' => 'text',
                'length' => 50,
                'description' => 'Town',
                'placeholder' => 'Town',
                'example' => 'Lusaka',
                'required' => false,
            ],
            'secondary_designation' => [
                'name' => 'secondary_designation',
                'label' => 'Secondary Contact Designation/Rank',
                'type' => 'text',
                'length' => 50,
                'description' => 'Designation',
                'placeholder' => 'Designation',
                'example' => 'Director',
                'required' => false,
            ],
        ];
        $sections['financial_details']['fields'] = [
            'finance_details' => [
                'name' => 'finance_details',
                'label' => 'Finance Details',
                'type' => 'table',
                'length' => 50,
                'description' => 'Finance Details',
                'placeholder' => 'Finance Details',
                'example' => 'Finance Details',
                'required' => true,
                'split' => false,
                'rows' => 6,
                'headers' => [
                    'Description',
                    'Volume',
                    'Value',
                ],
                'fields' => [
                    'description' => [
                        'name' => 'description',
                        'label' => 'Description',
                        'type' => 'select',
                        'length' => 50,
                        'description' => 'Description',
                        'placeholder' => 'Description',
                        'example' => 'Volume',
                        'required' => true,
                        'options' => [
                            'Average Transaction (Yearly)' => 'Average Transaction (Yearly)',
                            'Average Transaction (Monthly)' => 'Average Transaction (Monthly)',
                            'Previous Annual Sales' => 'Previous Annual Sales',
                            'Projected Annual Sales' => 'Projected Annual Sales',
                            'Average Daily Sales' => 'Average Daily Sales',
                            'Sales with deferred delivery of goods/services' => 'Sales with deferred delivery of goods/services',
                            'Sales completed upon full payment' => 'Sales completed upon full payment',
                            'Subscription base sales if applicable' => 'Subscription base sales if applicable',
                            'Source of other income' => 'Source of other income',
                        ],
                    ],
                    'volume' => [
                        'name' => 'volume',
                        'label' => 'Volume',
                        'type' => 'number',
                        'length' => 50,
                        'description' => 'Volume',
                        'placeholder' => 'Volume',
                        'example' => '1000',
                        'required' => true,
                    ],
                    'value' => [
                        'name' => 'value',
                        'label' => 'Value',
                        'type' => 'number',
                        'length' => 50,
                        'description' => 'Value',
                        'placeholder' => 'Value',
                        'example' => '1000',
                        'required' => true,
                    ],
                ]
            ],
        ];
        $sections['website_information']['fields'] = [
            'accept_international_payments' => [
                'name' => 'accept_international_payments',
                'label' => 'Accept International Payments',
                'type' => 'select',
                'length' => 50,
                'description' => 'Accept International Payments',
                'placeholder' => 'Accept International Payments',
                'example' => 'Yes',
                'required' => true,
                'options' => [
                    'Yes' => 'Yes',
                    'No' => 'No',
                ],
            ],
            'products_services' => [
                'name' => 'products_services',
                'label' => 'Products and/or Services',
                'type' => 'text',
                'length' => 50,
                'description' => 'Products and/or Services',
                'placeholder' => 'Products and/or Services',
                'example' => 'Products and/or Services',
                'required' => true,
            ],
            'delivery_days' => [
                'name' => 'delivery_days',
                'label' => 'Delivery Days',
                'type' => 'number',
                'length' => 50,
                'description' => 'Delivery Days',
                'placeholder' => 'Delivery Days',
                'example' => '7',
                'required' => true,
            ],
            'total_sales_points' => [
                'name' => 'total_sales_points',
                'label' => 'Total Sales Points',
                'type' => 'number',
                'length' => 50,
                'description' => 'Total Sales Points',
                'placeholder' => 'Total Sales Points',
                'example' => '5',
                'required' => true,
            ],
            'secure_platform' => [
                'name' => 'secure_platform',
                'label' => 'Secure Platform',
                'type' => 'select',
                'length' => 50,
                'description' => 'If using a client web application, Mobile App or Third-Party Web Application or Mobile App, is it secure and does it apply secure processes?',
                'placeholder' => 'Secure Platform',
                'example' => 'Yes',
                'required' => true,
                'options' => [
                    'Yes' => 'Yes',
                    'No' => 'No',
                ],
            ],
            'security_details' => [
                'name' => 'security_details',
                'label' => 'Security Details',
                'type' => 'text',
                'length' => 50,
                'description' => 'Detail how security is handled for above answer',
                'placeholder' => 'Security Details',
                'example' => 'Security Details',
                'required' => true,
            ],
            'payment_services_request' => [
                'name' => 'payment_services_request',
                'label' => 'Payment Services Request',
                'type' => 'checkbox',
                'length' => 50,
                'description' => 'Payment Services Request',
                'placeholder' => 'Payment Services Request',
                'example' => 'Payment Services Request',
                'required' => true,
                'options' => [
                    'MPGS' => 'MasterCard Payment Gateway',
                    'CyberSource' => 'CyberSource',
                    'Card Payments' => 'Card Payments',
                    'Mobile Money Payments' => 'Mobile Money Payments',
                ],
            ],
            'techpay_services_requested' => [
                'name' => 'techpay_services_requested',
                'label' => 'TechPay Services Requested',
                'type' => 'checkbox',
                'length' => 50,
                'description' => 'TechPay Services Requested',
                'placeholder' => 'TechPay Services Requested',
                'example' => 'TechPay Services Requested',
                'required' => true,
                'options' => [
                    'POS Terminal' => 'POS Terminals',
                    'Woocommerce Plugin' => 'Woocommerce Plugin',
                    'Direct API Integration' => 'Direct API Integration',
                    'USSD' => 'USSD Payments',
                    'QR Code Payments' => 'QR Code Payments',
                    'WhatsApp Payments' => 'WhatsApp Payments',
                    'Facebook Messenger Payments' => 'Facebook Messenger Payments',
                ]
            ],

            'policies' => [
                'name' => 'policies',
                'label' => 'Policies',
                'type' => 'checkbox',
                'length' => 50,
                'description' => 'Confirm if client or third-party web application and/or mobile App have the following in place:',
                'placeholder' => 'Policies',
                'example' => 'Policies',
                'required' => true,
                'options' => [
                    'Customer Return Policy' => 'Customer Return Policy',
                    'Customer Privacy Policy' => 'Customer Privacy Policy',
                    'Cookie Notification Policy' => 'Cookie Notification Policy',
                    'Data Protection/Gathering Notice' => 'Data Protection/Gathering Notice',
                    'Chargeback or Refund Policy' => 'Chargeback or Refund Policy',
                    'Cancellation Policy' => 'Cancellation Policy',
                ],
            ],
        ];
        $sections['bank_details']['fields'] = [
            'bank_name' => [
                'name' => 'bank_name',
                'label' => 'Bank Name',
                'type' => 'select',
                'length' => 50,
                'description' => 'Bank Name',
                'placeholder' => 'Bank Name',
                'example' => 'ZANACO',
                'required' => true,
                'options' => [
                    'AB Bank Zambia' => 'AB Bank Zambia',
                    'Absa Bank' => 'Absa Bank',
                    'Access Bank' => 'Access Bank',
                    'Bank of China' => 'Bank of China',
                    'Citi Bank Zambia' => 'Citi Bank Zambia',
                    'Eco Bank Zambia' => 'Eco Bank Zambia',
                    'First Alliance Bank' => 'First Alliance Bank',
                    'First Capital Bank' => 'First Capital Bank',
                    'First National Bank' => 'First National Bank',
                    'Indo Zambia Bank' => 'Indo Zambia Bank',
                    'Stanbic Bank Zambia' => 'Stanbic Bank Zambia',
                    'Standard Chartered Bank' => 'Standard Chartered Bank',
                    'United Bank for Africa' => 'United Bank for Africa',
                    'Zambia Industrial Commercial Bank' => 'Zambia Industrial Commercial Bank',
                    'Zambia National Commercial Bank' => 'Zambia National Commercial Bank',
                ],
            ],
            'bank_branch' => [
                'name' => 'bank_branch',
                'label' => 'Bank Branch',
                'type' => 'text',
                'length' => 50,
                'description' => 'Bank Branch',
                'placeholder' => 'Bank Branch',
                'example' => 'Lusaka',
                'required' => true,
            ],
            'bank_sort_code' => [
                'name' => 'bank_sort_code',
                'label' => 'Bank Sort Code',
                'type' => 'text',
                'length' => 50,
                'description' => 'Bank Sort Code',
                'placeholder' => 'Bank Sort Code',
                'example' => '123456',
                'required' => true,
            ],
            'account_type' => [
                'name' => 'account_type',
                'label' => 'Type of Account',
                'type' => 'select',
                'length' => 50,
                'description' => 'Type of Account',
                'placeholder' => 'Type of Account',
                'example' => 'Current',
                'required' => true,
                'options' => [
                    'Current' => 'Current',
                    'Savings' => 'Savings',
                    'Others' => 'Others',
                ],
            ],
            'account_number' => [
                'name' => 'account_number',
                'label' => 'Account Number',
                'type' => 'text',
                'length' => 50,
                'description' => 'Account Number',
                'placeholder' => 'Account Number',
                'example' => '123456',
                'required' => true,
            ],
            'account_name' => [
                'name' => 'account_name',
                'label' => 'Account Name',
                'type' => 'text',
                'length' => 50,
                'description' => 'Account Name',
                'placeholder' => 'Account Name',
                'example' => 'John Mulenga',
                'required' => true,
            ],
        ];
        $data = [
            'sections' => $sections,
            'application_fee' => $application_fee,
            'processing_fee' => $processing_fee,
        ];
        return view('onboarding', $data);
    }


    public function submitApplication(Request $request)
    {
        $mobile = $request->mobile;
        $fields = $request->fields;
        $fields = json_decode($fields, true);

        $user_ip = Helpers::getUserIp();
        $user_agent = $request->header('User-Agent');
        Helpers::LogPerformance('ONBOARDING', 'ADMISSION_CREATION_ATTEMPT', 'ADMISSIONS', $mobile, 'ADMISSIONS', $user_ip, $user_agent, '', '', '', $request->all());
        try {

            if (count($fields) == 0)
                return response()->json([
                    'status' => 'ERROR',
                    'statusMessage' => 'Invalid request',
                ]);


            // Insert into appropriate models
            DB::beginTransaction();
            // Insert into Company details
            $companyData = [];
            foreach ($fields['general'] as $field => $value) {
                $companyData[$field] = Helpers::processFormField($field, $value);
            }
            $company = CompanyDetail::create($companyData);

            // Insert into Contact Information
            $contactData = [];
            foreach ($fields['contact_information'] as $field => $value) {
                $contactData[$field] = Helpers::processFormField($field, $value);
            }
            $contactData['company_id'] = $company->id;
            $contact = CompanyContact::create($contactData);

            // Insert multiple business owners
            if (isset($fields['business_ownership']) && is_array($fields['business_ownership'])) {
                foreach ($fields['business_ownership'] as $owner) {
                    if (!empty(array_filter($owner))) { // Only process if owner has any data
                        $ownershipData = [];
                        foreach ($owner as $field => $value) {
                            $ownershipData[$field] = Helpers::processFormField($field, $value);
                        }
                        $ownershipData['company_id'] = $company->id;
                        CompanyOwnership::create($ownershipData);
                    }
                }
            }

            // Insert into Financial Details
            $financialData = [];

            // Loop through the financial details fields
            foreach ($fields['financial_details'] as $key => $value) {
                // Extract the index and field name from the key
                preg_match('/finance_details_(\d+)_(\w+)/', $key, $matches);
                $index = $matches[1];
                $field = $matches[2];

                // Assign the value to the appropriate field
                $financialData[$index][$field] = $value;
                $financialData[$index]['company_id'] = $company->id;
            }

            // Insert each entry into the CompanyFinancial model
            foreach ($financialData as $data) {
                CompanyFinancial::create($data);
            }

            foreach ($financialData as $data) {
                CompanyFinancial::create($data);
            }

            // Insert into Website Information
            $websiteData = [];
            foreach ($fields['website_information'] as $field => $value) {
                $websiteData[$field] = Helpers::processFormField($field, $value);
            }
            $websiteData['company_id'] = $company->id;
            $website = CompanyWebsite::create($websiteData);

            // Insert into Bank Details
            $bankData = [];
            foreach ($fields['bank_details'] as $field => $value) {
                $bankData[$field] = Helpers::processFormField($field, $value);
            }
            $bankData['company_id'] = $company->id;
            $bank = CompanyBank::create($bankData);


            $reference = Helpers::generateApplicationRef();
            $application = new OnboardingApplications();
            $application->reference = $reference;
            $application->company_id = $company->id;
            $application->status = 'IN_REVIEW';  // Changed from PENDING to IN_REVIEW
            $application->approval_level = 0;
            $application->save();

            Log::info("Created application", [
                'reference' => $reference,
                'status' => $application->status,
                'level' => $application->approval_level
            ]);

            // Create initial approval record
            Approval::create([
                'reference' => $reference,
                'module' => 'onboarding',
                'level' => 0,
                'level_name' => 'DATA_ENTRY',
                'status' => 'IN_REVIEW',
                'initiated_by' => 0, // Will always be blank from frontend
                'actioned_by' => null,
                'comments' => 'Application submitted'
            ]);

            // Notify data entry users of new application
            $application->notifyReviewers();

            $attachments = session('docs');
            if ($attachments) {
                foreach ($attachments as $name => $file) {

                    Storage::move('tmp' . DIRECTORY_SEPARATOR . trim($file), 'public' . DIRECTORY_SEPARATOR . 'attachments' . DIRECTORY_SEPARATOR . $file);
                    $doc = new Attachments();
                    $doc->reference = $reference;
                    $doc->name = $name;
                    $doc->file_name = $file;
                    $doc->save();
                }
            }

            $sms_text = "Hi $contact->primary_full_name,\n"
                . "Your application has been received. We will get back to you soon.\n"
                . "Tracking No.: {$reference}\n"
                . "Thank you.\n"
                . Helpers::getAppName();

            $sms = new SmsNotifications();
            $sms->message = $sms_text;
            $sms->mobile = $mobile;
            $sms->status = GeneralStatus::STATUS_PENDING;
            $sms->sender = Helpers::getSenderId();
            $sms->save();

            DB::commit();


            $user_ip = Helpers::getUserIp();
            $user_agent = $request->header('User-Agent');
            Helpers::LogPerformance('ONBOARDING', 'ADMISSION_CREATION_SUCCESS', 'ADMISSIONS', $mobile, 'ADMISSIONS', $user_ip, $user_agent, '', '', '', $request->all());

            return response()->json([
                'status' => 'SUCCESS',
                'statusMessage' => 'Application submitted successfully with reference: ' . $reference,
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Failed to log application: " . $e->getMessage());
            return response()->json([
                'status' => 'ERROR',
                'statusMessage' => 'Something went wrong; ' . $e->getMessage(),
            ]);
        }
    }

}
