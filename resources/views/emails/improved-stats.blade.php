@php
    function translateSource($source, $bank = null) {
        if ($bank) {
            return $bank;
        }
        return match($source) {
            'CYBERSOURCE' => 'ABSA',
            default => $source
        };
    }
@endphp
Greetings,<br/>
<p>Please find below the transaction report for {{ $report_date }}.</p>
<p>Note: The transactions captured occurred in the last 24 hours.</p>



<h4 style="color: #333;">Daily Transaction Summary</h4>
<TABLE cellspacing='0' border=1 cellpadding='5' style='border-collapse: collapse; width: 100%;'>
    <thead style='background-color: #2D1F3F; color: #fff'>
        <tr>
            <th style='padding: 10px;'>Platform</th>
            <th style='padding: 10px;'>Metric</th>
            <th style='padding: 10px;'>Value (Today)</th>
        </tr>
    </thead>
    <tbody>
        @foreach($cumulative_stats['daily']['by_source'] as $source)
        <tr style='background-color: #f9f9f9;'>
            <td style='padding: 10px;' align='left' rowspan="4"><b>{{ translateSource($source->source, $source->acquirer) }}</b></td>
            <td style='padding: 10px;' align='left'><b>Total Transactions</b></td>
            <td style='padding: 10px;' align='right'>{{ number_format($source->total_transactions) }}</td>
        </tr>
        <tr style='background-color: #eaeaea;'>
            <td style='padding: 10px;' align='left'><b>Successful Transactions</b></td>
            <td style='padding: 10px;' align='right'>{{ number_format($source->successful_transactions) }}</td>
        </tr>
        <tr style='background-color: #f9f9f9;'>
            <td style='padding: 10px;' align='left'><b>Total Value (Successful)</b></td>
            <td style='padding: 10px;' align='right'>{{ number_format($source->successful_amount, 2) }}</td>
        </tr>
        <tr style='background-color: #eaeaea;'>
            <td style='padding: 10px;' align='left'><b>Active Merchants</b></td>
            <td style='padding: 10px;' align='right'>{{ number_format($source->total_merchants) }}</td>
        </tr>
        @endforeach
        <tr style='background-color: #2D1F3F; color: #fff'>
            <td style='padding: 10px;' align='left' colspan="2"><b>TOTAL VALUE TODAY</b></td>
            <td style='padding: 10px;' align='right'><b>{{ number_format($cumulative_stats['daily']['totals']->successful_amount, 2) }}</b></td>
        </tr>
    </tbody>
</TABLE>

<h4 style="color: #333;">Summary By Bank & Currency</h4>
<TABLE cellspacing='0' border=1 cellpadding='5' style='border-collapse: collapse; width: 100%;'>
    <thead style='background-color: #2D1F3F; color: #fff'>
        <tr>
            <th style='padding: 10px;'>Platform</th>
            <th style='padding: 10px;'>Bank</th>
            <th style='padding: 10px;'>Currency</th>
            <th style='padding: 10px;'>Transactions</th>
            <th style='padding: 10px;'>Total Value</th>
        </tr>
    </thead>
    <tbody>
        @php
            $currentPlatform = '';
            $currentBank = '';
        @endphp
        @foreach($cumulative_stats['daily']['by_currency'] as $stat)
            @php
                $isTotal = isset($stat->is_total) && $stat->is_total;
            @endphp
            
            @if($isTotal)
                <tr style='background-color: #f0f0f0;'>
                    <td style='padding: 10px;' align='left'><b>{{ $stat->source }}</b></td>
                    <td style='padding: 10px;'></td>
                    <td style='padding: 10px;' align='left'><b>{{ $stat->txn_currency }}</b></td>
                    <td style='padding: 10px;' align='right'><b>{{ number_format($stat->total_transactions) }}</b></td>
                    <td style='padding: 10px;' align='right'><b>{{ number_format($stat->successful_amount, 2) }}</b></td>
                </tr>
            @else
                <tr style='background-color: {{ $loop->even ? "#eaeaea" : "#f9f9f9" }};'>
                    <td style='padding: 10px;' align='left'>
                        @if($stat->source !== $currentPlatform)
                            <b>{{ translateSource($stat->source) }}</b>
                            @php $currentPlatform = $stat->source; @endphp
                        @endif
                    </td>
                    <td style='padding: 10px;' align='left'>
                        @if($stat->bank !== $currentBank)
                            {{ $stat->bank }}
                            @php $currentBank = $stat->bank; @endphp
                        @endif
                    </td>
                    <td style='padding: 10px;' align='left'>{{ $stat->txn_currency }}</td>
                    <td style='padding: 10px;' align='right'>{{ number_format($stat->total_transactions) }}</td>
                    <td style='padding: 10px;' align='right'>{{ number_format($stat->successful_amount, 2) }}</td>
                </tr>
            @endif
        @endforeach
    </tbody>
</TABLE>

<h4 style="color: #333;">Daily Settlement Summary</h4>
<TABLE cellspacing='0' border=1 cellpadding='5' style='border-collapse: collapse; width: 100%;'>
    <thead style='background-color: #2D1F3F; color: #fff'>
        <tr>
            <th style='padding: 10px;'>Platform</th>
            <th style='padding: 10px;'>Currency</th>
            <th style='padding: 10px;'>Total Value</th>
            <th style='padding: 10px;'>Bank Settlement</th>
            <th style='padding: 10px;'>Our Commission</th>
            <th style='padding: 10px;'>Merchant Settlement</th>
        </tr>
    </thead>
    <tbody>
        @foreach($settlement_stats['daily'] as $stat)
        <tr style='background-color: {{ $loop->even ? "#eaeaea" : "#f9f9f9" }};'>
            <td style='padding: 10px;' align='left'><b>{{ translateSource($stat->source) }}</b></td>
            <td style='padding: 10px;' align='left'>{{ $stat->currency }}</td>
            <td style='padding: 10px;' align='right'>{{ number_format($stat->total_value - $stat->total_credit_value, 2) }}</td>
            <td style='padding: 10px;' align='right'>{{ number_format($stat->total_bank_settlement, 2) }}</td>
            <td style='padding: 10px;' align='right'>{{ number_format($stat->total_our_charge, 2) }}</td>
            <td style='padding: 10px;' align='right'>{{ number_format($stat->total_merchant_settlement, 2) }}</td>
        </tr>
        @endforeach
    </tbody>
</TABLE>

<h4 style="color: #333;">Daily Settlement Charges</h4>
<TABLE cellspacing='0' border=1 cellpadding='5' style='border-collapse: collapse; width: 100%;'>
    <thead style='background-color: #2D1F3F; color: #fff'>
        <tr>
            <th style='padding: 10px;'>Platform</th>
            <th style='padding: 10px;'>Currency</th>
            <th style='padding: 10px;'>VAT (3%)</th>
            <th style='padding: 10px;'>Rolling Reserve (5%)</th>
            <th style='padding: 10px;'>Total Bank Charges</th>
        </tr>
    </thead>
    <tbody>
        @foreach($settlement_stats['daily'] as $stat)
        <tr style='background-color: {{ $loop->even ? "#eaeaea" : "#f9f9f9" }};'>
            <td style='padding: 10px;' align='left'><b>{{ translateSource($stat->source) }}</b></td>
            <td style='padding: 10px;' align='left'>{{ $stat->currency }}</td>
            <td style='padding: 10px;' align='right'>{{ number_format($stat->total_vat, 2) }}</td>
            <td style='padding: 10px;' align='right'>{{ number_format($stat->total_rolling_reserve, 2) }}</td>
            <td style='padding: 10px;' align='right'>{{ number_format($stat->total_bank_charge, 2) }}</td>
        </tr>
        @endforeach
    </tbody>
</TABLE>

<h4 style="color: #333;">Daily Currency Distribution</h4>
<TABLE cellspacing='0' border=1 cellpadding='5' style='border-collapse: collapse; width: 100%;'>
    <thead style='background-color: #2D1F3F; color: #fff'>
        <tr>
            <th style='padding: 10px;'>Currency</th>
            <th style='padding: 10px;'>Transactions</th>
            <th style='padding: 10px;'>Total Amount</th>
        </tr>
    </thead>
    <tbody>
        @foreach($cumulative_stats['currency_distribution'] as $curr)
        <tr style='background-color: {{ $loop->even ? "#eaeaea" : "#f9f9f9" }};'>
            <td style='padding: 10px;' align='left'><b>{{ $curr->txn_currency }}</b></td>
            <td style='padding: 10px;' align='right'>{{ number_format($curr->total_transactions) }}</td>
            <td style='padding: 10px;' align='right'>{{ number_format($curr->successful_amount, 2) }}</td>
        </tr>
        @endforeach
    </tbody>
</TABLE>

<h4 style="color: #333;">Daily Platform Performance</h4>
<TABLE cellspacing='0' border=1 cellpadding='5' style='border-collapse: collapse; width: 100%;'>
    <thead style='background-color: #2D1F3F; color: #fff'>
        <tr>
            <th style='padding: 10px;'>Source</th>
            <th style='padding: 10px;'>Currency</th>
            <th style='padding: 10px;'>Card Type</th>
            <th style='padding: 10px;'>Success Rate</th>
            <th style='padding: 10px;'>Volume</th>
            <th style='padding: 10px;'>Amount</th>
            <th style='padding: 10px;'>Avg. Value</th>
        </tr>
    </thead>
    <tbody>
        @foreach($summary as $item)
        @php
            $cardType = match($item->card_type) {
                '001' => 'Visa',
                '002' => 'Mastercard',
                '003' => 'American Express',
                '004' => 'Discover',
                default => $item->card_type ?: 'Unknown'
            };
        @endphp
        <tr style='background-color: {{ $loop->even ? "#eaeaea" : "#f9f9f9" }};'>
            <td style='padding: 10px;' align='left'><b>{{ translateSource($item->source) }}</b></td>
            <td style='padding: 10px;' align='left'>{{ $item->txn_currency }}</td>
            <td style='padding: 10px;' align='left'>{{ $cardType }}</td>
            <td style='padding: 10px;' align='right'>
                {{ number_format(($item->total_volume > 0 ? ($item->success_volume / $item->total_volume) * 100 : 0), 1) }}%
            </td>
            <td style='padding: 10px;' align='right'>{{ number_format($item->total_volume) }}</td>
            <td style='padding: 10px;' align='right'>{{ number_format($item->success_amount, 2) }}</td>
            <td style='padding: 10px;' align='right'>{{ number_format($item->avg_success_amount, 2) }}</td>
        </tr>
        @endforeach
    </tbody>
</TABLE>

<h4 style="color: #333;">Daily Merchant Activity</h4>
<TABLE cellspacing='0' border=1 cellpadding='5' style='border-collapse: collapse; width: 100%;'>
    <thead style='background-color: #2D1F3F; color: #fff'>
        <tr>
            <th style='padding: 10px;'>Merchant</th>
            <th style='padding: 10px;'>Platform</th>
            <th style='padding: 10px;'>Currency</th>
            <th style='padding: 10px;'>Success Rate</th>
            <th style='padding: 10px;'>Volume</th>
            <th style='padding: 10px;'>Total Amount</th>
            <th style='padding: 10px;'>Avg. Transaction</th>
        </tr>
    </thead>
    <tbody>
        @foreach($top_merchants as $merchant)
        <tr style='background-color: {{ $loop->even ? "#eaeaea" : "#f9f9f9" }};'>
            <td style='padding: 10px;' align='left'><b>{{ $merchant->merchant_name ?: $merchant->merchant }}</b></td>
            <td style='padding: 10px;' align='left'>{{ translateSource($merchant->source) }}</td>
            <td style='padding: 10px;' align='left'>{{ $merchant->txn_currency }}</td>
            <td style='padding: 10px;' align='right'>
                {{ number_format(($merchant->total_volume > 0 ? ($merchant->success_volume / $merchant->total_volume) * 100 : 0), 1) }}%
            </td>
            <td style='padding: 10px;' align='right'>{{ number_format($merchant->total_volume) }}</td>
            <td style='padding: 10px;' align='right'>{{ number_format($merchant->success_amount, 2) }}</td>
            <td style='padding: 10px;' align='right'>{{ number_format($merchant->avg_transaction_value, 2) }}</td>
        </tr>
        @endforeach
    </tbody>
</TABLE>

<h4 style="color: #333;">Daily Card Usage Analysis</h4>
<TABLE cellspacing='0' border=1 cellpadding='5' style='border-collapse: collapse; width: 100%;'>
    <thead style='background-color: #2D1F3F; color: #fff'>
        <tr>
            <th style='padding: 10px;'>Platform</th>
            <th style='padding: 10px;'>Card Type</th>
            <th style='padding: 10px;'>Unique Cards</th>
            <th style='padding: 10px;'>Total Txns</th>
            <th style='padding: 10px;'>Success Rate</th>
            <th style='padding: 10px;'>Total Amount</th>
        </tr>
    </thead>
    <tbody>
        @foreach($card_stats as $stat)
        @php
            $cardType = match($stat->card_type) {
                '001' => 'Visa',
                '002' => 'Mastercard',
                '003' => 'American Express',
                '004' => 'Discover',
                default => $stat->card_type ?: 'Unknown'
            };
        @endphp
        <tr style='background-color: {{ $loop->even ? "#eaeaea" : "#f9f9f9" }};'>
            <td style='padding: 10px;' align='left'><b>{{ translateSource($stat->source) }}</b></td>
            <td style='padding: 10px;' align='left'>{{ $cardType }}</td>
            <td style='padding: 10px;' align='right'>{{ number_format($stat->unique_cards) }}</td>
            <td style='padding: 10px;' align='right'>{{ number_format($stat->total_transactions) }}</td>
            <td style='padding: 10px;' align='right'>
                {{ number_format(($stat->total_transactions > 0 ? ($stat->successful_transactions / $stat->total_transactions) * 100 : 0), 1) }}%
            </td>
            <td style='padding: 10px;' align='right'>{{ number_format($stat->total_amount, 2) }}</td>
        </tr>
        @endforeach
    </tbody>
</TABLE>

<h4 style="color: #333;">Daily Transaction Timeline</h4>
<TABLE cellspacing='0' border=1 cellpadding='5' style='border-collapse: collapse; width: 100%;'>
    <thead style='background-color: #2D1F3F; color: #fff'>
        <tr>
            <th style='padding: 10px;'>Hour</th>
            <th style='padding: 10px;'>Platform</th>
            <th style='padding: 10px;'>Total Txns</th>
            <th style='padding: 10px;'>Success Rate</th>
            <th style='padding: 10px;'>Amount</th>
        </tr>
    </thead>
    <tbody>
        @foreach($hourly_stats as $hour)
        <tr style='background-color: {{ $loop->even ? "#eaeaea" : "#f9f9f9" }};'>
            <td style='padding: 10px;' align='center'>{{ sprintf('%02d:00', $hour->hour) }}</td>
            <td style='padding: 10px;' align='left'>{{ translateSource($hour->source) }}</td>
            <td style='padding: 10px;' align='right'>{{ number_format($hour->total_count) }}</td>
            <td style='padding: 10px;' align='right'>
                {{ number_format(($hour->total_count > 0 ? ($hour->success_count / $hour->total_count) * 100 : 0), 1) }}%
            </td>
            <td style='padding: 10px;' align='right'>{{ number_format($hour->total_amount, 2) }}</td>
        </tr>
        @endforeach
    </tbody>
</TABLE>

<h4 style="color: #333;">Cumulative Statistics</h4>
<TABLE cellspacing='0' border=1 cellpadding='5' style='border-collapse: collapse; width: 100%;'>
    <thead style='background-color: #2D1F3F; color: #fff'>
        <tr>
            <th style='padding: 10px;'>Platform</th>
            <th style='padding: 10px;'>Total Transactions</th>
            <th style='padding: 10px;'>Successful Transactions</th>
            <th style='padding: 10px;'>Total Value</th>
            <th style='padding: 10px;'>Active Merchants</th>
        </tr>
    </thead>
    <tbody>
        @foreach($cumulative_stats['inception']['by_source'] as $stat)
        <tr style='background-color: {{ $loop->even ? "#eaeaea" : "#f9f9f9" }};'>
            <td style='padding: 10px;' align='left'><b>{{ translateSource($stat->source, $stat->acquirer) }}</b></td>
            <td style='padding: 10px;' align='right'>{{ number_format($stat->total_transactions) }}</td>
            <td style='padding: 10px;' align='right'>{{ number_format($stat->successful_transactions) }}</td>
            <td style='padding: 10px;' align='right'>{{ number_format($stat->successful_amount, 2) }}</td>
            <td style='padding: 10px;' align='right'>{{ number_format($stat->total_merchants) }}</td>
        </tr>
        @endforeach
        <tr style='background-color: #2D1F3F; color: #fff'>
            <td style='padding: 10px;' align='left'><b>TOTAL VALUE TO DATE</b></td>
            <td style='padding: 10px;' align='right' colspan="3"><b>{{ number_format($cumulative_stats['inception']['totals']->successful_amount, 2) }}</b></td>
            <td></td>
        </tr>
    </tbody>
</TABLE>

<h4 style="color: #333;">Cumulative Rolling Reserve</h4>
<TABLE cellspacing='0' border=1 cellpadding='5' style='border-collapse: collapse; width: 100%;'>
    <thead style='background-color: #2D1F3F; color: #fff'>
        <tr>
            <th style='padding: 10px;'>Platform</th>
            <th style='padding: 10px;'>Currency</th>
            <th style='padding: 10px;'>Total Rolling Reserve Held</th>
        </tr>
    </thead>
    <tbody>
        @foreach($settlement_stats['rolling_reserve_held'] as $stat)
        <tr style='background-color: {{ $loop->even ? "#eaeaea" : "#f9f9f9" }};'>
            <td style='padding: 10px;' align='left'><b>{{ translateSource($stat->source) }}</b></td>
            <td style='padding: 10px;' align='left'>{{ $stat->currency }}</td>
            <td style='padding: 10px;' align='right'>{{ number_format($stat->total_rolling_reserve, 2) }}</td>
        </tr>
        @endforeach
    </tbody>
</TABLE>

<br/>
<p>Note: All amounts are in their respective transaction currencies.</p>
<br/>
<br/>
