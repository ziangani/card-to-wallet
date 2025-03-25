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
            max-width: 800px;
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
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f8f9fa;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .amount {
            text-align: right;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>CyberSource Settlement Summary Report</h2>
        </div>

        <div class="content">
            <p>Dear Team,</p>

            <p>Please find below the CyberSource Settlement Summary for the period {{ $startDate }} to {{ $endDate }}.</p>

            <div class="details">
                <p><strong>Report Details:</strong></p>
                <ul>
                    <li>Period: {{ $startDate }} to {{ $endDate }}</li>
                    @if($reportCount > 0)
                        <li>Payment Detail Files Attached: {{ $reportCount }}</li>
                    @endif
                </ul>
            </div>

            @if($summaryData->isNotEmpty())
                <table>
                    <thead>
                        <tr>
                            <th>Merchant</th>
                            <th>Currency</th>
                            <th class="amount">Debit Value</th>
                            <th class="amount">Credit Value</th>
                            <th class="amount">Net Settlement</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($summaryData as $row)
                            <tr>
                                <td>{{ $row->merchant_name }} ({{ $row->merchant }})</td>
                                <td>{{ $row->currency }}</td>
                                <td class="amount">{{ number_format($row->debit_value, 2) }}</td>
                                <td class="amount">{{ number_format($row->credit_value, 2) }}</td>
                                <td class="amount">{{ number_format(($row->debit_value - $row->credit_value), 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <p>No settlement data found for this period.</p>
            @endif

            @if($reportCount > 0)
                <p>The attached ZIP file contains all Payment Detail Reports for this period.</p>
            @endif

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
