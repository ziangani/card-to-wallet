<!DOCTYPE html>
<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <style>
        @media only screen and (max-width: 620px) {
            .wrapper {
                width: 100% !important;
                padding: 20px !important;
            }
        }
        .button {
            background: #002749;
            border-radius: 4px;
            color: #ffffff;
            display: inline-block;
            font-weight: bold;
            padding: 16px 24px;
            text-decoration: none;
            margin: 24px 0;
        }
        .button:hover {
            background: #003a6b;
        }
        .footer {
            color: #666666;
            font-size: 12px;
            text-align: center;
            margin-top: 40px;
        }
    </style>
</head>
<body style="background-color: #f6f6f6; font-family: sans-serif; -webkit-font-smoothing: antialiased; font-size: 14px; line-height: 1.4; margin: 0; padding: 0; -ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%;">
    <table role="presentation" border="0" cellpadding="0" cellspacing="0" class="wrapper" style="background: #ffffff; border-radius: 8px; margin: 40px auto; max-width: 580px; padding: 40px; width: 100%;">
        <tr>
            <td align="center">
                <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%">
                    <tr>
                        <td>
                            <img src="{{ asset('images/logo.png') }}" alt="{{ config('app.name') }}" style="margin-bottom: 24px; max-width: 200px;">
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <h1 style="color: #002749; font-size: 24px; font-weight: bold; margin: 0 0 24px 0;">Welcome to {{ config('app.name') }}</h1>
                            
                            <p style="color: #333333; font-size: 16px; line-height: 24px; margin: 0 0 24px 0;">
                                Hello {{ $name }},
                            </p>

                            <p style="color: #333333; font-size: 16px; line-height: 24px; margin: 0 0 24px 0;">
                                You have been invited to join {{ $company }} on the {{ config('app.name') }} Corporate Portal. To get started, please set up your password by clicking the button below:
                            </p>

                            <p style="text-align: center;">
                                <a href="{{ $setup_url }}" class="button">Set Up Your Password</a>
                            </p>

                            <div style="background-color: #f8f9fa; border-left: 4px solid #002749; margin: 24px 0; padding: 16px;">
                                <p style="color: #666666; font-size: 14px; line-height: 20px; margin: 0;">
                                    <strong>Note:</strong> This link will expire in {{ $expiry_hours }} hours for security reasons.
                                </p>
                            </div>

                            <p style="color: #333333; font-size: 16px; line-height: 24px; margin: 24px 0;">
                                If you're unable to click the button above, you can copy and paste the following URL into your browser:
                            </p>

                            <p style="background-color: #f8f9fa; border-radius: 4px; color: #666666; font-size: 12px; margin: 0; padding: 12px; word-break: break-all;">
                                {{ $setup_url }}
                            </p>

                            <p style="color: #333333; font-size: 16px; line-height: 24px; margin: 24px 0 0 0;">
                                Best regards,<br>
                                The {{ config('app.name') }} Team
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <td>
                <div class="footer">
                    <p>
                        If you didn't expect this invitation, please ignore this email or contact support.
                    </p>
                    <p>
                        &copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
                    </p>
                </div>
            </td>
        </tr>
    </table>
</body>
</html>