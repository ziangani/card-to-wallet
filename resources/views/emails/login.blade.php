<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Login Credentials - {{config('app.name')}}</title>
    <!--[if mso]>
    <style type="text/css">body, table, td, a { font-family: Arial, Helvetica, sans-serif !important; }</style>
    <![endif]-->
</head>

<body style="font-family: Helvetica, Arial, sans-serif; margin: 0; padding: 0; background-color: #f5f5f5;">
    <table role="presentation" style="width: 100%; border-collapse: collapse; border: 0; border-spacing: 0; background-color: #f5f5f5;">
        <tbody>
            <tr>
                <td align="center" style="padding: 2rem;">
                    <table role="presentation" style="max-width: 600px; border-collapse: collapse; border: 0; border-spacing: 0; text-align: left; background-color: #ffffff; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                        <tbody>
                            <tr>
                                <td style="padding: 40px;">
                                    <!-- Header with Logo -->
                                    <div style="text-align: center; margin-bottom: 32px;">
                                        <h1 style="margin: 0; font-size: 28px; color: #002749;">{{config('app.name')}}<span style="color: #f96f59;">.</span></h1>
                                    </div>

                                    <!-- Greeting -->
                                    <p style="margin: 0 0 24px 0; font-size: 16px; line-height: 24px; color: #333333;">
                                        Dear {{$name}},
                                    </p>

                                    <!-- Welcome Message -->
                                    <p style="margin: 0 0 24px 0; font-size: 16px; line-height: 24px; color: #333333;">
                                        Welcome to {{config('app.name')}}! <br/>Your account has been created successfully. Below are your login credentials:
                                    </p>

                                    <!-- Credentials Box -->
                                    <div style="background-color: #f8f9fa; border-radius: 6px; padding: 24px; margin-bottom: 24px;">
                                        <h2 style="margin: 0 0 16px 0; font-size: 18px; color: #002749;">Your Login Details</h2>
                                        <p style="margin: 0 0 12px 0; font-size: 16px; line-height: 24px; color: #333333;">
                                            <strong style="color: #002749;">Email Address:</strong>
                                            <span style="color: #666666;">{{$auth_id}}</span>
                                        </p>
                                        <p style="margin: 0 0 12px 0; font-size: 16px; line-height: 24px; color: #333333;">
                                            <strong style="color: #002749;">Password:</strong>
                                            <span style="color: #666666;">{{$password}}</span>
                                        </p>
                                    </div>

                                    <!-- CTA Button -->
                                    <div style="text-align: center; margin-bottom: 24px;">
                                        <a href="{{$url}}" style="display: inline-block; padding: 12px 24px; background-color: #002749; color: #ffffff; text-decoration: none; border-radius: 4px; font-weight: bold;">Login to Your Account</a>
                                    </div>

                                    <!-- Security Notice -->
                                    <div style="border-left: 4px solid #f96f59; padding-left: 16px; margin-bottom: 24px;">
                                        <p style="margin: 0; font-size: 14px; line-height: 21px; color: #666666;">
                                            <strong style="color: #f96f59;">Security Tip:</strong><br>
                                            For your security, we recommend changing your password immediately after your first login.
                                        </p>
                                    </div>

                                    <!-- Disclaimer -->
                                    <p style="margin: 0 0 24px 0; font-size: 14px; line-height: 21px; color: #666666;">
                                        If you did not request this account, please disregard this email and contact our support team immediately.
                                    </p>

                                    <!-- Signature -->
                                    <p style="margin: 0; font-size: 16px; line-height: 24px; color: #333333;">
                                        Best regards,<br>
                                        The {{\App\Common\Helpers::getAppName()}} Team
                                    </p>
                                </td>
                            </tr>
                        </tbody>
                    </table>

                    <!-- Footer -->
                    <table role="presentation" style="max-width: 600px; border-collapse: collapse; border: 0; border-spacing: 0; text-align: center; margin-top: 20px;">
                        <tr>
                            <td style="padding: 20px 0;">
                                <p style="margin: 0; font-size: 14px; line-height: 21px; color: #666666;">
                                    Powered by <a href="https://www.techpay.co.zm" style="color: #002749; text-decoration: none;">TechPay Limited</a>
                                </p>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </tbody>
    </table>
</body>

</html>
