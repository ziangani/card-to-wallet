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
        .summary-section {
            margin-bottom: 30px;
            border: 1px solid #dee2e6;
            padding: 15px;
            border-radius: 4px;
        }
        .summary-header {
            background-color: #e9ecef;
            padding: 10px;
            margin: -15px -15px 15px -15px;
            border-radius: 4px 4px 0 0;
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
            width: 100%;
            border-collapse: collapse;
        }
        .monitoring-params th {
            background-color: #e9ecef;
            text-align: center;
            padding: 8px;
        }
        .monitoring-params td {
            padding: 8px;
            border-bottom: 1px solid #dee2e6;
        }
        .monitoring-params td:first-child {
            font-weight: bold;
        }
        .amounts {
            margin-top: 10px;
            padding: 10px;
            background-color: #f8f9fa;
            border-radius: 4px;
        }
        .note {
            font-style: italic;
            color: #6c757d;
            margin-top: 2px;
            font-size: 12px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>Transaction Exception Report</h2>
            <p>Generated on: {{ $date }}</p>
        </div>

        <div class="warning">
            This report contains transactions that have been flagged for review based on defined monitoring criteria. Detailed transaction data is attached in CSV files.
        </div>

        <div class="summary-section">
            <div class="summary-header">
                <h3>Transaction Exception Summary</h3>
            </div>

            <table style="width: 100%; border-collapse: collapse; margin-top: 15px;">
                <thead>
                    <tr>
                        <th style="padding: 8px; text-align: left; background-color: #f8f9fa; border-bottom: 2px solid #dee2e6;">Type</th>
                        <th style="padding: 8px; text-align: center; background-color: #f8f9fa; border-bottom: 2px solid #dee2e6;">Transactions</th>
                        <th style="padding: 8px; text-align: center; background-color: #f8f9fa; border-bottom: 2px solid #dee2e6;">Cards</th>
                        <th style="padding: 8px; text-align: right; background-color: #f8f9fa; border-bottom: 2px solid #dee2e6;">Total Amount</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($activities as $activity)
                        <tr>
                            <td style="padding: 8px; border-bottom: 1px solid #dee2e6;">
                                {{ $activity['type'] }}
                                <div class="note">See attached {{ $activity['filename'] }} for details</div>
                            </td>
                            <td style="padding: 8px; text-align: center; border-bottom: 1px solid #dee2e6;">{{ $activity['count'] }}</td>
                            <td style="padding: 8px; text-align: center; border-bottom: 1px solid #dee2e6;">
                                {{ isset($activity['cards']) ? $activity['cards'] : '-' }}
                            </td>
                            <td style="padding: 8px; text-align: right; border-bottom: 1px solid #dee2e6;">
                                @foreach($activity['amounts'] as $amount)
                                    {{ $amount }}<br>
                                @endforeach
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
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
                    <th colspan="2">ECI 7 (2D) Transactions</th>
                </tr>
                <tr>
                    <td>ECI Value</td>
                    <td>07 (Non-authenticated transactions)</td>
                </tr>
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

        <div style="margin-top: 30px; font-size: 12px; color: #6c757d;">
            <p>This is an automated monitoring report. Please review the attached CSV files for detailed transaction data.</p>
            <p>For any concerns, please contact the risk management team.</p>
        </div>
    </div>
</body>
</html>
