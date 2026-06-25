<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>We got your message</title>
</head>
<body style="font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Helvetica, Arial, sans-serif; background:#f5f5f5; margin:0; padding:24px; color:#1a1a1a;">
<div style="max-width:560px; margin:0 auto; background:#ffffff; border-radius:12px; padding:40px; box-shadow:0 1px 3px rgba(0,0,0,0.06);">
    <p style="font-size:12px; letter-spacing:0.2em; color:#777; margin:0 0 8px 0;">ADEOSPACE</p>
    <h1 style="font-size:24px; margin:0 0 16px 0;">Thanks, {{ $submission->name }} &mdash; we got your message.</h1>

    <p style="font-size:15px; line-height:1.6; color:#333;">
        We&rsquo;ve received your message and someone from the team will review it and get back to you within 48 hours if there is a fit.
    </p>

    <p style="font-size:15px; line-height:1.6; color:#333;">
        Here&rsquo;s a quick recap of what you sent us, in case you want to follow up:
    </p>

    <div style="background:#fafafa; border-radius:8px; padding:18px 20px; margin:20px 0; font-size:14px; line-height:1.6;">
        @if($submission->organisation)<div><strong>Organisation:</strong> {{ $submission->organisation }}</div>@endif
        @if($submission->sector)<div><strong>Sector:</strong> {{ $submission->sector }}</div>@endif
        <div style="margin-top:12px; white-space:pre-wrap;">{{ $submission->message }}</div>
    </div>

    <p style="font-size:15px; line-height:1.6; color:#333;">
        Talk soon,<br>
        <strong>AdeoSpace Team</strong><br>
        Dhaka, Bangladesh
    </p>

    <hr style="border:none; border-top:1px solid #eee; margin:32px 0 16px 0;">
    <p style="font-size:11px; color:#aaa; margin:0;">
        This is an automated confirmation. You can reply to this email and it will reach the team.
    </p>
</div>
</body>
</html>
