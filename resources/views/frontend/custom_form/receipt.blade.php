<!DOCTYPE html>
<html class="no-js" lang="en">
<head>
    <!-- Meta Tags -->
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="author" content="@ziangani">
    <title>{{config('app.name')}} Receipt</title>
    <link rel="stylesheet" href="{{public_path('assets/css/pdf_styles.css')}}">
    <style>

        #watermark {
            opacity: .2;
            position: fixed;
            bottom: 5cm;
            left: 2cm;
            width: 16cm;
            height: 15cm;
        }
        .small {
            font-size: 12px;
        }
    </style>
</head>

<body>
<div id="watermark">
    <img src="{{public_path('techpay.png')}}"
         height="100%" width="100%" style="opacity: .3"/>
</div>
<div class="cs-container" style="bottom: -30px;">
    <div class="cs-invoice cs-style1">
        <div class="cs-invoice_in" id="download_section">
            <div class="cs-invoice_head cs-type1 cs-mb25">
                <div class="cs-invoice_left">
                    <img src="{{public_path("assets/img/logo.png")}}" width="100px"
                        STYLE="margin-bottom: 20px;" alt="Logo">
                    <p class="cs-invoice_number cs-primary_color cs-f16">
                        <span class="cs-primary_color"
                              style="font-weight: bold; font-size: 22px; position:relative;">
                            Proof of Payment<br/>
                            <span style="color: #555; font-size: 15px">Admission</span>
                        </span>
                    </p>
                </div>
                <div class="cs-invoice_right cs-text_right" style="float: right">
                    <div class="cs-logo" style="margin-bottom: 20px">

                    </div>
                </div>
            </div>

            <table border="0">
                <tbody>
                <tr>
                    <td>
                        <div class="cs-list_left"><b class="cs-primary_color">
                                Payment Channel:</b><br/> {{$transaction->external_channel}}
                        </div>
                    </td>
                    <td>
                        <div class="cs-list_right"><b class="cs-primary_color">
                                Payment Reference:</b><br/> {{$transaction->provider_external_reference}}
                        </div>
                    </td>
                    <td>
                        <div class="cs-list_right">
                            <b class="cs-primary_color">Payment
                                Date:</b><br/> {{date('d-M-Y H:i:s', strtotime($transaction->created_at))}}
                        </div>
                    </td>
                </tr>
                </tbody>
            </table>

            <div class="cs-mb20">
                <ul class="cs-list cs-style1">
                    <li>
                    </li>
                    <li>
                    </li>
                </ul>
            </div>
            <div class="cs-table cs-style1">
                <div class="cs-round_border">
                    <div class="cs-table_responsive">
                        <table>
                            <thead>
                            <tr>
                                <th class="cs-width_5 cs-semi_bold cs-primary_color cs-focus_bg">Made To</th>
                                <th class="cs-width_5 cs-semi_bold cs-primary_color cs-focus_bg">Description</th>
                                <th class="cs-width_2 cs-semi_bold cs-primary_color cs-focus_bg cs-text_right">Amount
                                    Paid
                                </th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($payments as $payment)
                                <tr>
                                    <td class="cs-width_5">
                                        <div>
                                            Admissions Payment
                                        </div>
                                        <div class="text-secondary small">
                                            {{\App\Common\Helpers::getAppName()}}
                                        </div>
                                    </td>
                                    <td class="cs-width_5">
                                        <div>{{$payment->description}}</div>
                                        <div
                                                class="text-secondary small">{{$payment->reference}}
                                        </div>
                                    </td>
                                    <td class="cs-width_2 cs-text_right">
                                        {{number_format($payment->amount ?? 0, 2)}}
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="cs-invoice_footer">
                    <div class="cs-left_footer cs-mobile_hide"></div>
                    <div class="cs-right_footer">
                        <table>
                            <tbody>
                            <tr class="cs-border_none">
                                <td class="cs-width_3 cs-border_top_0 cs-bold cs-f16 cs-primary_color">Total Amount</td>
                                <td class="cs-width_3 cs-border_top_0 cs-bold cs-f16 cs-primary_color cs-text_right">
                                    K{{number_format($total, 2)}}</td>
                            </tr>
                            <tr class="cs-border_none">
                                <td colspan="2" class="cs-width_3 cs-border_top_0 cs-primary_color">
                                    <span class="cs-bold">Amount in words:</span> {{\App\Common\AmountToWords::readAsZMW(number_format($total, 2, '.', ''))}} Only
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="cs-note">
                <div class="cs-note_left">
                    <svg xmlns="http://www.w3.org/2000/svg" class="ionicon" viewBox="0 0 512 512">
                        <path d="M416 221.25V416a48 48 0 01-48 48H144a48 48 0 01-48-48V96a48 48 0 0148-48h98.75a32 32 0 0122.62 9.37l141.26 141.26a32 32 0 019.37 22.62z"
                              fill="none" stroke="currentColor" stroke-linejoin="round" stroke-width="32"/>
                        <path d="M256 56v120a32 32 0 0032 32h120M176 288h160M176 368h160" fill="none"
                              stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="32"/>
                    </svg>
                </div>
                <div class="cs-note_right">

                </div>
            </div><!-- .cs-note -->
            <div class="visible-print text-center">
                <img src="data:image/png;base64, {!! base64_encode(QrCode::format('svg')->size(150)->style('round')->eyeColor(0, 91,68,155, 0, 0, 0)->generate(url('/v/' . $transaction->provider_external_reference))) !!} ">

            </div>
            <div style="text-align: center; margin-top: -20px">
                <div style="margin-bottom: -10px; float: right; vertical-align: super">
                    Powered by:
                    <img src="{{public_path('techpay.png')}}" width="70px" alt="Logo">
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>
