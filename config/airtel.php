<?php

//Please note link to access a Airtel GUI - https://airtelmoneyweb.airtel.co.zm/AirtelMoney/
//
//Net Settlement	MSISDN	MERCHANT	LOGIN ID
//1%	889002793	TECHMASTER ZAMBIA LIMITED	TECHM10	Done
//1.50%	889002794	TECHMASTER ZAMBIA LIMITED	TECHM15	Done
//2%	889002795	TECHMASTER ZAMBIA LIMITED	TECHM20	Done
//2.50%	889002796	TECHMASTER ZAMBIA LIMITED	TECHM25	Done
//HalfPerMer	889002797	TECHMASTER ZAMBIA LIMITED	TECH10MC	Done
//1PerMerCus	889002798	TECHMASTER ZAMBIA LIMITED	TECH10C	Done
//


return [

    'default_wallet' => 'TECHM20',
    'keys' => [
        'TECHM20' => [
            'sandbox' => [
                'client_id' => 'f574076b-c661-4e29-896d-5e614da501a0',
                'client_secret' => 'a36edb71-4b29-4106-a8ab-ecc5cc3d3546',
                'endpoint' => 'https://openapiuat.airtel.africa/',
            ],
            'production' => [
                'client_id' => 'df1cef0f-d4ba-4051-ae07-4eb5a5d47806',
                'client_secret' => 'aacd5004-6002-4d38-a0d7-76fc1af9d84f',
                'endpoint' => 'https://openapi.airtel.africa/',
            ],
            'details' => [
                'msisidn' => '889002795',
                'merchant' => 'TECHMASTER ZAMBIA LIMITED',
                'login_id' => 'TECHM20',
                'net_settlement' => '2%',
            ]
        ]
    ],

    /** Disabled for now
     * //    'techmasters_25' => [
     * 'TECHM25' => [
     * 'sandbox' => [
     * 'client_id' => '615cad73-731d-49c2-ae19-e01cd48a92b9',
     * 'client_secret' => '05d6c25d-41bc-4190-b7f7-7f8f89d75053',
     * 'endpoint' => 'https://openapiuat.airtel.africa/',
     * ],
     * 'production' => [
     * 'client_id' => '7bedc4bd-e4b4-4101-9767-5ce8d2c38908',
     * 'client_secret' => '522ba415-2aba-4b22-80b8-a1b2f356ea7e',
     * 'endpoint' => 'https://openapi.airtel.africa/',
     * ],
     * 'details' => [
     * 'msisidn' => '889002796',
     * 'merchant' => 'TECHMASTER ZAMBIA LIMITED',
     * 'login_id' => 'TECHM25',
     * 'net_settlement' => '2.50%',
     * ]
     * ],
     * //    'techmasters_15' => [
     * 'TECHM15' => [
     * 'sandbox' => [
     * 'client_id' => '171a9261-7c5f-4f57-a369-981884668f33',
     * 'client_secret' => '463ec285-0e0e-4d0a-901d-8fccf6e91722',
     * 'endpoint' => 'https://openapiuat.airtel.africa/',
     * ],
     * 'production' => [
     * 'client_id' => '171a9261-7c5f-4f57-a369-981884668f33',
     * 'client_secret' => '463ec285-0e0e-4d0a-901d-8fccf6e91722',
     * 'endpoint' => 'https://openapi.airtel.africa/',
     * ],
     * 'details' => [
     * 'msisidn' => '889002794',
     * 'merchant' => 'TECHMASTER ZAMBIA LIMITED',
     * 'login_id' => 'TECHM15',
     * 'net_settlement' => '1.50%',
     * ]
     * ],
     * //    'techmasters_10c' => [
     * 'TECH10C' => [
     * 'sandbox' => [
     * 'client_id' => '9e0fbe3f-90da-4e79-920a-dce31ef61719',
     * 'client_secret' => '7b07fb59-995f-4803-9ad9-76c7aed62c69',
     * 'endpoint' => 'https://openapiuat.airtel.africa/',
     * ],
     * 'production' => [
     * 'client_id' => '9e0fbe3f-90da-4e79-920a-dce31ef61719',
     * 'client_secret' => '7b07fb59-995f-4803-9ad9-76c7aed62c69',
     * 'endpoint' => 'https://openapi.airtel.africa/',
     * ],
     * 'details' => [
     * 'msisidn' => '889002798',
     * 'merchant' => 'TECHMASTER ZAMBIA LIMITED',
     * 'login_id' => 'TECH10C',
     * 'net_settlement' => '1PerMerCus',
     * ]
     * ],
     * //    'techmasters_10mc' => [
     * 'TECH10MC' => [
     * 'sandbox' => [
     * 'client_id' => '3746eeab-0c4b-45f6-9e48-32d044d9f221',
     * 'client_secret' => '5c8305eb-cd4a-4d2a-bc05-36913f34dfe1',
     * 'endpoint' => 'https://openapiuat.airtel.africa/',
     * ],
     * 'production' => [
     * 'client_id' => '3746eeab-0c4b-45f6-9e48-32d044d9f221',
     * 'client_secret' => '5c8305eb-cd4a-4d2a-bc05-36913f34dfe1',
     * 'endpoint' => 'https://openapi.airtel.africa/',
     * ],
     * 'details' => [
     * 'msisidn' => '889002797',
     * 'merchant' => 'TECHMASTER ZAMBIA LIMITED',
     * 'login_id' => 'HalfPerMer',
     * 'net_settlement' => 'HalfPerMer',
     * ]
     * ],
     *
     * 'TECHM10' => [
     * 'sandbox' => [
     * 'client_id' => '1b53d3db-b98f-467f-bd0f-3c25990ca062',
     * 'client_secret' => '1b6d68d5-a2e4-42b3-8e95-72a8f89892de',
     * 'endpoint' => 'https://openapiuat.airtel.africa/',
     * ],
     * 'production' => [
     * 'client_id' => '690b0aa3-c26f-4af9-aac7-81786a9719a6',
     * 'client_secret' => '748420c6-b8d8-44b8-99e2-542edbe413a0',
     * 'endpoint' => 'https://openapi.airtel.africa/',
     * ],
     * 'details' => [
     * 'msisidn' => '889002793',
     * 'merchant' => 'TECHMASTER ZAMBIA LIMITED',
     * 'login_id' => 'TECHM10',
     * 'net_settlement' => '1%',
     * ]
     * ],
     */
];
