<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transaction Export</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.5;
            color: #333;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 1px solid #ddd;
        }
        .logo {
            max-width: 150px;
            margin-bottom: 10px;
        }
        h1 {
            font-size: 18px;
            margin: 0 0 5px;
        }
        h2 {
            font-size: 14px;
            margin: 0 0 20px;
            font-weight: normal;
        }
        .info {
            margin-bottom: 20px;
        }
        .info p {
            margin: 0 0 5px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
            font-weight: bold;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .footer {
            text-align: center;
            font-size: 10px;
            color: #777;
            margin-top: 30px;
            padding-top: 10px;
            border-top: 1px solid #ddd;
        }
        .status-completed {
            color: #28A745;
            font-weight: bold;
        }
        .status-pending {
            color: #FFC107;
            font-weight: bold;
        }
        .status-failed {
            color: #DC3545;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ config('app.name') }} - Transaction Export</h1>
        <h2>Generated on {{ $generated_at }}</h2>
    </div>

    <div class="info">
        <p><strong>User:</strong> {{ $user->first_name }} {{ $user->last_name }}</p>
        <p><strong>Email:</strong> {{ $user->email }}</p>
        <p><strong>Total Transactions:</strong> {{ count($transactions) }}</p>
    </div>

    @if(count($transactions) > 0)
        <table>
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Reference</th>
                    <th>Recipient</th>
                    <th>Phone Number</th>
                    <th>Amount (ZMW)</th>
                    <th>Fee (ZMW)</th>
                    <th>Total (ZMW)</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($transactions as $transaction)
                    <tr>
                        <td>{{ $transaction->created_at->format('Y-m-d H:i:s') }}</td>
                        <td>{{ $transaction->uuid }}</td>
                        <td>{{ $transaction->reference_4 ?: 'Unknown' }}</td>
                        <td>+260{{ $transaction->reference_1 }}</td>
                        <td>{{ number_format($transaction->amount, 2) }}</td>
                        <td>{{ number_format($transaction->fee_amount, 2) }}</td>
                        <td>{{ number_format($transaction->total_amount, 2) }}</td>
                        <td>
                            @if($transaction->status === 'COMPLETED')
                                <span class="status-completed">Completed</span>
                            @elseif($transaction->status === 'pending' || $transaction->status === 'payment_initiated')
                                <span class="status-pending">Pending</span>
                            @elseif($transaction->status === 'failed' || $transaction->status === 'payment_failed')
                                <span class="status-failed">Failed</span>
                            @else
                                {{ ucfirst($transaction->status) }}
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="summary">
            <h3>Summary</h3>
            <p><strong>Total Amount:</strong> ZMW {{ number_format($transactions->where('status', 'COMPLETED')->sum('amount'), 2) }}</p>
            <p><strong>Total Fees:</strong> ZMW {{ number_format($transactions->where('status', 'COMPLETED')->sum('fee_amount'), 2) }}</p>
            <p><strong>Total Paid:</strong> ZMW {{ number_format($transactions->where('status', 'COMPLETED')->sum('total_amount'), 2) }}</p>
        </div>
    @else
        <p>No transactions found matching your criteria.</p>
    @endif

    <div class="footer">
        <p>This is an automatically generated report from {{ config('app.name') }}.</p>
        <p>For any questions or concerns, please contact support.</p>
    </div>
</body>
</html>
