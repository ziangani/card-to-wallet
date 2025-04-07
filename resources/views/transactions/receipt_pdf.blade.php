<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Transaction Receipt</title>
    <style>
        @page {
            margin: 10mm;
        }
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            color: #333;
            font-size: 12px;
            line-height: 1.4;
        }
        .header {
            background-color: #3366CC;
            color: white;
            padding: 15px;
            text-align: center;
            border-bottom: 3px solid #FF9900;
            margin-bottom: 20px;
        }
        .receipt-title {
            font-size: 20px;
            font-weight: bold;
            margin-bottom: 5px;
            text-transform: uppercase;
        }
        .receipt-subtitle {
            font-size: 14px;
            opacity: 0.9;
        }
        .receipt-id {
            font-family: monospace;
            background-color: #f0f0f0;
            padding: 3px 6px;
            border-radius: 3px;
            font-size: 12px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        table.main-table {
            margin-bottom: 20px;
            border: 1px solid #ddd;
        }
        table.main-table th {
            background-color: #f5f5f5;
            padding: 8px;
            text-align: left;
            border-bottom: 2px solid #ddd;
            font-weight: bold;
        }
        table.main-table td {
            padding: 8px;
            border-bottom: 1px solid #eee;
            vertical-align: top;
        }
        .status-badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 11px;
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
        .amounts-table {
            width: 100%;
            margin-top: 10px;
        }
        .amounts-table th {
            background-color: #f0f4f8;
            padding: 8px;
            text-align: left;
            border-bottom: 2px solid #ddd;
        }
        .amounts-table td {
            padding: 8px;
            border-bottom: 1px solid #eee;
        }
        .amounts-table tr.total td {
            border-top: 2px solid #ddd;
            font-weight: bold;
            font-size: 14px;
            color: #3366CC;
        }
        .footer {
            margin-top: 20px;
            text-align: center;
            font-size: 11px;
            color: #666;
            padding: 10px;
            border-top: 1px solid #eee;
        }
        .footer p {
            margin: 3px 0;
        }
        .watermark {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-45deg);
            font-size: 80px;
            color: rgba(51, 102, 204, 0.03);
            font-weight: bold;
            z-index: -1;
        }
    </style>
</head>
<body>
    <div class="watermark">RECEIPT</div>
    
    <div class="header">
        <div class="receipt-title">Transaction Receipt</div>
        <div class="receipt-subtitle">{{ config('app.name') }}</div>
    </div>
    
    <table class="main-table">
        <tr>
            <th colspan="4">Transaction Information</th>
        </tr>
        <tr>
            <td width="25%"><strong>Reference:</strong></td>
            <td width="25%"><span class="receipt-id">{{ $transaction->uuid }}</span></td>
            <td width="25%"><strong>Status:</strong></td>
            <td width="25%">
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
            <td><strong>Date:</strong></td>
            <td>{{ $transaction->created_at->format('M d, Y h:i A') }}</td>
            <td><strong>Transaction Type:</strong></td>
            <td>Card to Wallet Transfer</td>
        </tr>
        <tr>
            <td><strong>Recipient:</strong></td>
            <td>
                {{ $transaction->reference_4 ?: 'Unknown' }}<br>
                <span style="color: #666;">+260{{ $transaction->reference_1 }}</span>
            </td>
            <td><strong>Payment Method:</strong></td>
            <td>Credit/Debit Card</td>
        </tr>
        @if($transaction->purpose || $transaction->notes)
        <tr>
            @if($transaction->purpose)
            <td><strong>Purpose:</strong></td>
            <td>{{ $transaction->purpose }}</td>
            @else
            <td></td><td></td>
            @endif
            
            @if($transaction->notes)
            <td><strong>Notes:</strong></td>
            <td>{{ $transaction->notes }}</td>
            @else
            <td></td><td></td>
            @endif
        </tr>
        @endif
        @if($transaction->provider_reference)
        <tr>
            <td><strong>Provider Reference:</strong></td>
            <td colspan="3">{{ $transaction->provider_reference }}</td>
        </tr>
        @endif
    </table>
    
    <table class="main-table">
        <tr>
            <th colspan="2">Payment Summary</th>
        </tr>
        <tr>
            <td width="70%">Amount Funded:</td>
            <td width="30%" align="right">K{{ number_format($transaction->amount, 2) }}</td>
        </tr>
        <tr>
            <td>Fees:</td>
            <td align="right">K{{ number_format($transaction->fee_amount, 2) }}</td>
        </tr>
        <tr>
            <td style="padding-left: 20px; color: #666;">Bank Fee:</td>
            <td align="right" style="color: #666;">K{{ number_format($transaction->getTransactionVariableFee(), 2) }}</td>
        </tr>
        <tr>
            <td style="padding-left: 20px; color: #666;">Deposit Fee:</td>
            <td align="right" style="color: #666;">K{{ number_format($transaction->getTransactionFixedFee(), 2) }}</td>
        </tr>
        <tr class="total">
            <td><strong>Total:</strong></td>
            <td align="right"><strong>K{{ number_format($transaction->total_amount, 2) }}</strong></td>
        </tr>
    </table>
    
    <div class="footer">
        <p><strong>Thank you for using our service!</strong></p>
        <p>For any questions, please contact our support team.</p>
        <p>This is an electronically generated receipt and does not require a signature.</p>
        <p style="margin-top: 10px; font-size: 10px; color: #999;">Receipt ID: {{ $transaction->uuid }}</p>
    </div>
</body>
</html>
