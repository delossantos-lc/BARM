<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>{{ $event }}</title>
</head>
<body style="margin:0;background:#f7eef2;font-family:Arial,Helvetica,sans-serif;color:#29262a;">
<div style="padding:28px 14px;">
    <div style="max-width:680px;margin:auto;background:#ffffff;border-radius:16px;overflow:hidden;border:1px solid #f0d7e1;">
        <div style="padding:22px 26px;background:#d95b91;color:#ffffff;">
            <div style="font-size:13px;opacity:.9;">{{ $settings->institution_name ?: 'Lourdes College' }}</div>
            <h1 style="margin:5px 0 0;font-size:22px;">{{ $event }}</h1>
        </div>

        <div style="padding:26px;">
            <p style="margin-top:0;line-height:1.6;">
                A library transaction requires your attention or has been completed.
            </p>

            <table role="presentation" style="width:100%;border-collapse:collapse;">
                @foreach($details as $label => $value)
                    @if($value !== null && $value !== '')
                        <tr>
                            <td style="width:38%;padding:10px;border-bottom:1px solid #eeeeee;color:#756b70;">
                                {{ $label }}
                            </td>
                            <td style="padding:10px;border-bottom:1px solid #eeeeee;font-weight:600;">
                                @if(is_array($value))
                                    {{ implode(', ', $value) }}
                                @else
                                    {{ $value }}
                                @endif
                            </td>
                        </tr>
                    @endif
                @endforeach
                <tr>
                    <td style="padding:10px;color:#756b70;">Date and Time</td>
                    <td style="padding:10px;font-weight:600;">
                        {{ $occurredAt->format(($settings->date_format ?: 'F j, Y') . ' ' . ($settings->time_format ?: 'h:i:s A')) }}
                    </td>
                </tr>
            </table>
        </div>

        <div style="padding:16px 26px;background:#fff7fa;color:#756b70;font-size:12px;">
            Automated notification from {{ $settings->system_title ?: 'RFID-BARM System' }}.
        </div>
    </div>
</div>
</body>
</html>
