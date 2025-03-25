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
            background-color: #f8f9fa;
            padding: 15px;
            margin-bottom: 20px;
            border-bottom: 2px solid #dee2e6;
        }
        .activity-section {
            margin-bottom: 30px;
            border: 1px solid #dee2e6;
            padding: 15px;
            border-radius: 4px;
        }
        .activity-header {
            background-color: #e9ecef;
            padding: 10px;
            margin: -15px -15px 15px -15px;
            border-radius: 4px 4px 0 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        th, td {
            padding: 8px;
            text-align: left;
            border-bottom: 1px solid #dee2e6;
        }
        th {
            background-color: #f8f9fa;
        }
        .warning {
            color: #856404;
            background-color: #fff3cd;
            padding: 10px;
            border-radius: 4px;
            margin-bottom: 15px;
        }
        .monitoring-params {
            margin: 20px 0;
            padding: 15px;
            background-color: #f8f9fa;
            border-radius: 4px;
        }
        .monitoring-params table {
            margin: 10px 0;
        }
        .monitoring-params th {
            background-color: #e9ecef;
            text-align: center;
        }
        .monitoring-params td:first-child {
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>Suspicious Transaction Activity Report</h2>
            <p>Generated on: {{ $date }}</p>
        </div>

        <div class="warning">
            This report contains potentially suspicious transaction activities that require review.
        </div>

        <div class="monitoring-params">
            <h3>Monitoring Parameters</h3>
            <table>
                <tr>
                    <th colspan="2">Large Transaction Thresholds</th>
                </tr>
                @foreach($parameters['large_amount_thresholds'] as $currency => $amount)
                    <tr>
                        <td>{{ $currency }}</td>
                        <td>{{ number_format($amount, 2) }}</td>
                    </tr>
                @endforeach
                <tr>
                    <th colspan="2">Other Parameters</th>
                </tr>
                <tr>
                    <td>Max Daily Card Usage</td>
                    <td>{{ $parameters['same_card_max_daily'] }} transactions</td>
                </tr>
                <tr>
                    <td>Split Transaction Window</td>
                    <td>{{ $parameters['split_transaction_time_window'] }} minutes</td>
                </tr>
                <tr>
                    <td>Split Transaction Min Count</td>
                    <td>{{ $parameters['split_transaction_min_count'] }} transactions</td>
                </tr>
            </table>
        </div>

        @foreach($activities as $activity)
            <div class="activity-section">
                <div class="activity-header">
                    <h3>{{ $activity['type'] }}</h3>
                    <p>{{ $activity['description'] }}</p>
                </div>

                <table>
                    <thead>
                        <tr>
                            <th>Transaction ID</th>
                            <th>Date/Time</th>
                            <th>Amount</th>
                            <th>Card</th>
                            <th>Merchant Name</th>
                            <th>Merchant Code</th>
                            @if($activity['type'] === 'ECI 7 Transactions')
                                <th>ECI</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($activity['transactions'] as $transaction)
                            <tr>
                                <td>{{ $transaction->txn_id }}</td>
                                <td>{{ $transaction->txn_date }}</td>
                                <td>{{ $transaction->txn_currency }} {{ number_format($transaction->txn_amount, 2) }}</td>
                                <td>{{ $transaction->card_type ?? 'N/A' }} ending in {{ $transaction->card_suffix }}</td>
                                <td>{{ optional($transaction->merchants)->name ?? 'N/A' }}</td>
                                <td>{{ $transaction->merchant }}</td>
                                @if($activity['type'] === 'ECI 7 Transactions')
                                    <td>{{ $transaction->eci_raw }}</td>
                                @endif
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endforeach

        <div style="margin-top: 30px; font-size: 12px; color: #6c757d;">
            <p>This is an automated security report. Please review these transactions and take appropriate action if necessary.</p>
            <p>For urgent concerns, please contact the security team immediately.</p>
        </div>
    </div>
</body>
</html>
