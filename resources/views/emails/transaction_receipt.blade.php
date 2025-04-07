<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transaction Receipt</title>
    <style>
        /* Base styles */
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            line-height: 1.6;
            color: #333;
            margin: 0;
            padding: 0;
            background-color: #f5f7fa;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        /* Header styles */
        .header {
            background-color: #3366CC;
            color: white;
            padding: 30px 20px;
            text-align: center;
            border-bottom: 5px solid #FF9900;
        }
        .logo-circle {
            width: 80px;
            height: 80px;
            background-color: white;
            border-radius: 50%;
            margin: 0 auto 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            color: #3366CC;
            font-size: 20px;
        }
        .header h1 {
            margin: 0;
            font-size: 28px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 2px;
        }
        .header p {
            margin: 5px 0 0;
            font-size: 16px;
            opacity: 0.9;
        }

        /* Content styles */
        .content {
            padding: 30px;
        }
        .greeting {
            font-size: 18px;
            margin-bottom: 20px;
            color: #333;
        }
        .message {
            background-color: #f8f9fa;
            border-left: 4px solid #3366CC;
            padding: 15px;
            margin-bottom: 25px;
            border-radius: 0 4px 4px 0;
        }

        /* Transaction details */
        .transaction-box {
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 25px;
            background-color: #fafafa;
        }
        .section-title {
            font-size: 18px;
            color: #3366CC;
            margin-top: 0;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 2px solid #f0f0f0;
        }
        .transaction-details table {
            width: 100%;
            border-collapse: collapse;
        }
        .transaction-details table td {
            padding: 10px;
            border-bottom: 1px solid #eee;
        }
        .transaction-details table tr:last-child td {
            border-bottom: none;
        }
        .transaction-details table td:first-child {
            font-weight: bold;
            width: 40%;
            color: #555;
        }

        /* Status badges */
        .status-badge {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .status-completed {
            background-color: #28A745;
            color: white;
        }
        .status-pending {
            background-color: #FFC107;
            color: #333;
        }
        .status-failed {
            background-color: #DC3545;
            color: white;
        }

        /* Amounts section */
        .amounts {
            background-color: #f0f4f8;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 25px;
        }
        .amounts table {
            width: 100%;
            border-collapse: collapse;
        }
        .amounts table td {
            padding: 10px;
        }
        .amounts table tr:not(:last-child) td {
            border-bottom: 1px solid #ddd;
        }
        .amounts table tr.total {
            font-weight: bold;
            font-size: 18px;
            color: #3366CC;
        }
        .amounts table tr.total td {
            padding-top: 15px;
            border-top: 2px solid #ddd;
        }

        /* Call to action */
        .cta {
            text-align: center;
            margin: 30px 0;
        }
        .button {
            display: inline-block;
            background-color: #3366CC;
            color: white;
            text-decoration: none;
            padding: 12px 25px;
            border-radius: 5px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-size: 14px;
        }

        /* Footer styles */
        .footer {
            background-color: #f5f7fa;
            text-align: center;
            padding: 20px;
            color: #666;
            border-top: 1px solid #eee;
            font-size: 13px;
        }
        .footer p {
            margin: 5px 0;
        }
        .social-links {
            margin: 15px 0;
        }
        .social-link {
            display: inline-block;
            width: 30px;
            height: 30px;
            background-color: #ddd;
            border-radius: 50%;
            margin: 0 5px;
            color: white;
            line-height: 30px;
            text-align: center;
            text-decoration: none;
            font-size: 16px;
        }

        /* Responsive adjustments */
        @media only screen and (max-width: 600px) {
            .container {
                width: 100%;
                border-radius: 0;
            }
            .content {
                padding: 20px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="logo-circle">C2W</div>
            <h1>Transaction Receipt</h1>
            <p>{{ config('app.name') }}</p>
        </div>

        <div class="content">
            <p class="greeting">Dear Customer,</p>

            <div class="message">
                <p>Thank you for using our service. Your transaction has been processed successfully. Please find attached your detailed receipt for your recent Card to Wallet transfer.</p>
            </div>

            <div class="transaction-box">
                <h3 class="section-title">Transaction Summary</h3>
                <div class="transaction-details">
                    <table>
                        <tr>
                            <td>Reference:</td>
                            <td><strong style="font-family: monospace; background: #f0f0f0; padding: 3px 6px; border-radius: 3px;">{{ $transaction->uuid }}</strong></td>
                        </tr>
                        <tr>
                            <td>Date:</td>
                            <td>{{ $transaction->created_at->format('M d, Y h:i A') }}</td>
                        </tr>
                        <tr>
                            <td>Status:</td>
                            <td>
                                @if($transaction->status === 'COMPLETED')
                                    <span class="status-badge status-completed">Completed</span>
                                @elseif($transaction->status === 'pending' || $transaction->status === 'payment_initiated')
                                    <span class="status-badge status-pending">Pending</span>
                                @elseif($transaction->status === 'failed' || $transaction->status === 'payment_failed')
                                    <span class="status-badge status-failed">Failed</span>
                                @else
                                    {{ ucfirst($transaction->status) }}
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td>Recipient:</td>
                            <td>
                                <strong>{{ $transaction->reference_4 ?: 'Unknown' }}</strong><br>
                                <span style="color: #666;">+260{{ $transaction->reference_1 }}</span>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>

            <div class="amounts">
                <h3 class="section-title">Payment Details</h3>
                <table>
                    <tr>
                        <td>Amount Funded:</td>
                        <td align="right">K{{ number_format($transaction->amount, 2) }}</td>
                    </tr>
                    <tr>
                        <td>Fees:</td>
                        <td align="right">K{{ number_format($transaction->fee_amount, 2) }}</td>
                    </tr>
                    <tr class="total">
                        <td>Total:</td>
                        <td align="right">K{{ number_format($transaction->total_amount, 2) }}</td>
                    </tr>
                </table>
            </div>

            <p>A detailed receipt is attached to this email as a PDF file for your records. You can print this receipt for your reference or save it for future use.</p>

            <p>If you have any questions or need assistance regarding this transaction, please don't hesitate to contact our support team.</p>

            <div class="cta">
                <a href="#" class="button">View Transaction History</a>
            </div>
        </div>

        <div class="footer">
            <div class="social-links">
                <a href="#" class="social-link" style="background-color: #3b5998;">F</a>
                <a href="#" class="social-link" style="background-color: #1da1f2;">T</a>
                <a href="#" class="social-link" style="background-color: #0077b5;">L</a>
                <a href="#" class="social-link" style="background-color: #c4302b;">Y</a>
            </div>
            <p><strong>{{ config('app.name') }}</strong> - Secure Card to Wallet Transfers</p>
            <p>This is an automated email. Please do not reply to this message.</p>
            <p>&copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
