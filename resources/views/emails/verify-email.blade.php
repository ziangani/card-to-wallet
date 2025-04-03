<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify Your Email Address</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            text-align: center;
            padding: 20px 0;
            border-bottom: 1px solid #eee;
        }
        .header img {
            max-width: 150px;
        }
        .content {
            padding: 20px 0;
        }
        .button {
            display: inline-block;
            background-color: #3366CC;
            color: #ffffff !important;
            text-decoration: none;
            padding: 12px 30px;
            border-radius: 5px;
            margin: 20px 0;
            font-weight: bold;
        }
        .footer {
            text-align: center;
            padding: 20px 0;
            font-size: 12px;
            color: #777;
            border-top: 1px solid #eee;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <img src="{{ asset('assets/img/logo.png') }}" alt="Logo">
            <h2>Verify Your Email Address</h2>
        </div>
        
        <div class="content">
            <p>Hello {{ $name }},</p>
            
            <p>Thank you for registering with {{ config('app.name') }}. Please click the button below to verify your email address:</p>
            
            <p style="text-align: center;">
                <a href="{{ $verification_url }}" class="button">Verify Email Address</a>
            </p>
            
            <p>If you did not create an account, no further action is required.</p>
            
            <p>If you're having trouble clicking the button, copy and paste the URL below into your web browser:</p>
            
            <p style="word-break: break-all;">{{ $verification_url }}</p>
            
            <p>Regards,<br>{{ config('app.name') }} Team</p>
        </div>
        
        <div class="footer">
            <p>&copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
            <p>{{ $url }}</p>
        </div>
    </div>
</body>
</html>
