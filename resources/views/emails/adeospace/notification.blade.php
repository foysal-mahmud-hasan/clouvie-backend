<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>New AdeoSpace inquiry</title>
</head>
<body style="font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Helvetica, Arial, sans-serif; background:#f5f5f5; margin:0; padding:24px; color:#1a1a1a;">
<div style="max-width:640px; margin:0 auto; background:#ffffff; border-radius:12px; padding:32px; box-shadow:0 1px 3px rgba(0,0,0,0.06);">
    <p style="font-size:12px; letter-spacing:0.2em; color:#777; margin:0 0 8px 0;">ADEOSPACE</p>
    <h1 style="font-size:22px; margin:0 0 24px 0;">New conversation request</h1>

    <table style="width:100%; border-collapse:collapse; font-size:14px;">
        <tr><td style="padding:8px 0; color:#777; width:140px;">Name</td><td style="padding:8px 0;">{{ $submission->name }}</td></tr>
        <tr><td style="padding:8px 0; color:#777;">Email</td><td style="padding:8px 0;"><a href="mailto:{{ $submission->email }}" style="color:#0d9488;">{{ $submission->email }}</a></td></tr>
        @if($submission->organisation)
        <tr><td style="padding:8px 0; color:#777;">Organisation</td><td style="padding:8px 0;">{{ $submission->organisation }}</td></tr>
        @endif
        @if($submission->sector)
        <tr><td style="padding:8px 0; color:#777;">Sector</td><td style="padding:8px 0;">{{ $submission->sector }}</td></tr>
        @endif
    </table>

    <h3 style="font-size:13px; letter-spacing:0.15em; color:#777; margin:24px 0 8px 0;">MESSAGE</h3>
    <div style="background:#fafafa; border-left:3px solid #0d9488; padding:16px 18px; border-radius:6px; white-space:pre-wrap; font-size:14px; line-height:1.6;">{{ $submission->message }}</div>

    <hr style="border:none; border-top:1px solid #eee; margin:32px 0 16px 0;">
    <table style="width:100%; font-size:11px; color:#999;">
        <tr><td>Submitted</td><td style="text-align:right;">{{ $submission->created_at?->format('Y-m-d H:i:s') }} UTC</td></tr>
        @if($submission->ip_address)
        <tr><td>IP</td><td style="text-align:right;">{{ $submission->ip_address }}</td></tr>
        @endif
        @if($submission->user_agent)
        <tr><td>User agent</td><td style="text-align:right; word-break:break-all;">{{ $submission->user_agent }}</td></tr>
        @endif
        <tr><td>Submission ID</td><td style="text-align:right;">#{{ $submission->id }}</td></tr>
    </table>

    <p style="font-size:11px; color:#aaa; margin-top:24px;">Reply directly to this email to respond to the sender &mdash; the Reply-To header is set to {{ $submission->email }}.</p>
</div>
</body>
</html>
