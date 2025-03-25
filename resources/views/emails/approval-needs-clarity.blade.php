Greetings,<br/>
<p>A merchant application requires clarification on the following:</p>
<div style="margin: 20px; padding: 15px; background-color: #f9f9f9; border-left: 4px solid #2D1F3F;">
    {{ $comments ?? 'Additional information needed' }}
</div>

<h4 style="color: #333;">Application Details</h4>
<TABLE cellspacing='0' border=1 cellpadding='5' style='border-collapse: collapse; width: 100%;'>
    <thead style='background-color: #2D1F3F; color: #fff'>
        <tr>
            <th style='padding: 10px;'>Field</th>
            <th style='padding: 10px;'>Details</th>
        </tr>
    </thead>
    <tbody>
        <tr style='background-color: #f9f9f9;'>
            <td style='padding: 10px;' align='left'><b>Reference</b></td>
            <td style='padding: 10px;' align='left'>{{ $application['reference'] }}</td>
        </tr>
        <tr style='background-color: #eaeaea;'>
            <td style='padding: 10px;' align='left'><b>Company Name</b></td>
            <td style='padding: 10px;' align='left'>{{ $application['company']['company_name'] }}</td>
        </tr>
        <tr style='background-color: #f9f9f9;'>
            <td style='padding: 10px;' align='left'><b>Current Level</b></td>
            <td style='padding: 10px;' align='left'>{{ $application['current_level_name'] }}</td>
        </tr>
        <tr style='background-color: #eaeaea;'>
            <td style='padding: 10px;' align='left'><b>Status</b></td>
            <td style='padding: 10px;' align='left'>{{ $application['status'] }}</td>
        </tr>
        <tr style='background-color: #f9f9f9;'>
            <td style='padding: 10px;' align='left'><b>Submitted By</b></td>
            <td style='padding: 10px;' align='left'>{{ $application['initiator']['name'] ?? 'Frontend Submission' }}</td>
        </tr>
        <tr style='background-color: #eaeaea;'>
            <td style='padding: 10px;' align='left'><b>Submission Date</b></td>
            <td style='padding: 10px;' align='left'>{{ \Carbon\Carbon::parse($application['created_at'])->format('d-M-Y H:i') }}</td>
        </tr>
    </tbody>
</TABLE>

<br/>
<p>Please log in to the system to review this application. Click the button below:</p>

<div style="text-align: center; margin: 30px 0;">
    <a href="{{ url('/tpadmin/backend/onboarding-applications/'.$application['id']) }}"
       style="background-color: #2D1F3F;
              color: #ffffff;
              padding: 12px 25px;
              text-decoration: none;
              border-radius: 5px;
              font-weight: bold;">
        Review Application
    </a>
</div>

<br/>
<p>Note: This is an automated notification. Please do not reply to this email.</p>
