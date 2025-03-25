<!DOCTYPE html>
<html>
<head>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        .content {
            margin-bottom: 30px;
        }
        .details {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        .footer {
            text-align: center;
            font-size: 12px;
            color: #666;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #eee;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>CyberSource Settlement Report</h2>
        </div>

        <div class="content">
            <p>Dear Team,</p>

            <p>Please find attached the CyberSource Payment Batch Detail Reports for {{ $date }}.</p>

            <div class="details">
                <p><strong>Report Details:</strong></p>
                <ul>
                    <li>Date: {{ $date }}</li>
                    <li>Total Files: {{ $reportCount }}</li>
                    <li>Report Type: Payment Batch Detail Report</li>
                </ul>
            </div>

            <p>The attached ZIP file contains all Payment Batch Detail Reports generated for this date. </p>

            <p>If you have any questions or concerns, please don't hesitate to contact the technical team.</p>

            <p>Best regards,</p>
        </div>

        <div class="footer">
            <p>This is an automated email from TechPay. Please do not reply to this email.</p>
            <p>© {{ date('Y') }} TechPay Limited. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
