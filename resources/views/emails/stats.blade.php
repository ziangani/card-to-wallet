Greetings,<br/>
<p>Note 1: All transactions are in USD.</p>
<p>Note 2: All transactions are pulled from MPGS (UBA) for now.</p>
<p>Note 3: The transactions captured occurred in the last 24 hours.</p>
<p>Note 4: This report is still in BETA.</p>
<h4 style="color: #333;">Overall Transaction Summary</h4>
<TABLE cellspacing='0' border=1 cellpadding='5' style='border-collapse: collapse; width: 100%;'>
    <thead style='background-color: #333; color: #fff'>
        <tr>
            <th style='padding: 10px;'>Type</th>
            <th style='padding: 10px;'>Value (Total)</th>
            <th style='padding: 10px;'>Value (Success)</th>
            <th style='padding: 10px;'>Value (Failed)</th>
            <th style='padding: 10px;'>Volume (Total)</th>
            <th style='padding: 10px;'>Volume (Success)</th>
            <th style='padding: 10px;'>Volume (Failed)</th>
        </tr>
    </thead>
    <tbody>
        <tr style='background-color: #f9f9f9;'>
            <td style='padding: 10px;' align='left'><b>Payments</b></td>
            <td style='padding: 10px;' align='right'><b>{{ $summary[0]['payments']['value']['total'] }}</b></td>
            <td style='padding: 10px;' align='right'><b>{{ $summary[0]['payments']['value']['success'] }}</b></td>
            <td style='padding: 10px;' align='right'><b>{{ $summary[0]['payments']['value']['failed'] }}</b></td>
            <td style='padding: 10px;' align='right'>
                <b>{{ number_format($summary[0]['payments']['volume']['total'], 0) }}</b></td>
            <td style='padding: 10px;' align='right'>
                <b>{{ number_format($summary[0]['payments']['volume']['success'], 0) }}</b></td>
            <td style='padding: 10px;' align='right'>
                <b>{{ number_format($summary[0]['payments']['volume']['failed'], 0) }}</b></td>
        </tr>
        <tr style='background-color: #eaeaea;'>
            <td style='padding: 10px;' align='left'><b>Authorizations</b></td>
            <td style='padding: 10px;' align='right'><b>{{ $summary[0]['authorizations']['value']['total'] }}</b></td>
            <td style='padding: 10px;' align='right'><b>{{ $summary[0]['authorizations']['value']['success'] }}</b></td>
            <td style='padding: 10px;' align='right'><b>{{ $summary[0]['authorizations']['value']['failed'] }}</b></td>
            <td style='padding: 10px;' align='right'>
                <b>{{ number_format($summary[0]['authorizations']['volume']['total'], 0) }}</b></td>
            <td style='padding: 10px;' align='right'>
                <b>{{ number_format($summary[0]['authorizations']['volume']['success'], 0) }}</b></td>
            <td style='padding: 10px;' align='right'>
                <b>{{ number_format($summary[0]['authorizations']['volume']['failed'], 0) }}</b></td>
        </tr>
    </tbody>
</TABLE>

<h4 style="color: #333;">Transactions By Merchants</h4>
<TABLE cellspacing='0' border=1 cellpadding='5' style='border-collapse: collapse; width: 100%;'>
    <thead style='background-color: #333; color: #fff'>
    <tr>
        <th style='padding: 10px;'>Merchant</th>
        <th style='padding: 10px;'>Type</th>
        <th style='padding: 10px;'>Value (Total)</th>
        <th style='padding: 10px;'>Value (Success)</th>
        <th style='padding: 10px;'>Value (Failed)</th>
        <th style='padding: 10px;'>Volume (Total)</th>
        <th style='padding: 10px;'>Volume (Success)</th>
        <th style='padding: 10px;'>Volume (Failed)</th>
    </tr>
    </thead>
    <tbody>
    @foreach($merchant_stats as $item)
        <tr style='background-color: #f9f9f9;'>
            <td style='padding: 10px;' align='left' rowspan="2"><b>{{ $item['name'] }}</b></td>
            <td style='padding: 10px;' align='left'><b>Payments</b></td>
            <td style='padding: 10px;' align='right'><b>{{ $item['payments']['value']['total'] }}</b></td>
            <td style='padding: 10px;' align='right'><b>{{ $item['payments']['value']['success'] }}</b></td>
            <td style='padding: 10px;' align='right'><b>{{ $item['payments']['value']['failed'] }}</b></td>
            <td style='padding: 10px;' align='right'><b>{{ number_format($item['payments']['volume']['total'], 0) }}</b>
            </td>
            <td style='padding: 10px;' align='right'>
                <b>{{ number_format($item['payments']['volume']['success'], 0) }}</b></td>
            <td style='padding: 10px;' align='right'>
                <b>{{ number_format($item['payments']['volume']['failed'], 0) }}</b></td>
        </tr>
        <tr style='background-color: #eaeaea;'>
            <td style='padding: 10px;' align='left'><b>Authorizations</b></td>
            <td style='padding: 10px;' align='right'><b>{{ $item['authorizations']['value']['total'] }}</b></td>
            <td style='padding: 10px;' align='right'><b>{{ $item['authorizations']['value']['success'] }}</b></td>
            <td style='padding: 10px;' align='right'><b>{{ $item['authorizations']['value']['failed'] }}</b></td>
            <td style='padding: 10px;' align='right'>
                <b>{{ number_format($item['authorizations']['volume']['total'], 0) }}</b></td>
            <td style='padding: 10px;' align='right'>
                <b>{{ number_format($item['authorizations']['volume']['success'], 0) }}</b></td>
            <td style='padding: 10px;' align='right'>
                <b>{{ number_format($item['authorizations']['volume']['failed'], 0) }}</b></td>
        </tr>
    @endforeach
    </tbody>
</TABLE>

<h4 style="color: #333;">Miscellaneous Cumulative Statistics</h4>
<TABLE cellspacing='0' border=1 cellpadding='5' style='border-collapse: collapse; width: 100%;'>
    <thead style='background-color: #333; color: #fff'>
    <tr>
        <th style='padding: 10px;'>Statistic</th>
        <th style='padding: 10px;'>Volume</th>
        <th style='padding: 10px;'>Value</th>
    </tr>
    </thead>
    <tbody>
    @foreach($misc_stats as $item)
        <tr style='background-color: #f9f9f9;'>
            <td style='padding: 10px;' align='left'><b>{{ $item['name'] }}</b></td>
            <td style='padding: 10px;' align='right'><b>{{$item['volume'] }}</b></td>
            <td style='padding: 10px;' align='right'><b>{{ $item['value'] }}</b></td>
        </tr>
    @endforeach
    </tbody>
</TABLE>
<br/>
<br/>
<br/>
