<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>New Antarious demo request</title>
</head>
<body style="font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Helvetica, Arial, sans-serif; background:#F4F1EA; margin:0; padding:24px; color:#1a1a1a;">
<div style="max-width:640px; margin:0 auto; background:#ffffff; border-radius:12px; padding:32px; box-shadow:0 1px 3px rgba(0,0,0,0.06); border-top:4px solid #1F4E79;">
    <p style="font-size:12px; letter-spacing:0.2em; color:#1F4E79; margin:0 0 8px 0; font-weight:600;">ANTARIOUS</p>
    <h1 style="font-size:22px; margin:0 0 24px 0; color:#1F4E79;">New demo request</h1>

    <table style="width:100%; border-collapse:collapse; font-size:14px;">
        <tr><td style="padding:8px 0; color:#777; width:140px;">Name</td><td style="padding:8px 0;">{{ $submission->name }}</td></tr>
        <tr><td style="padding:8px 0; color:#777;">Work email</td><td style="padding:8px 0;"><a href="mailto:{{ $submission->email }}" style="color:#B98E2E;">{{ $submission->email }}</a></td></tr>
        <tr><td style="padding:8px 0; color:#777;">Company</td><td style="padding:8px 0;">{{ $submission->company }}</td></tr>
        @if($submission->role)
        <tr><td style="padding:8px 0; color:#777;">Role</td><td style="padding:8px 0;">{{ $submission->role }}</td></tr>
        @endif
        @if($submission->team_size)
        <tr><td style="padding:8px 0; color:#777;">Team size</td><td style="padding:8px 0;">{{ $submission->team_size }}</td></tr>
        @endif
    </table>

    @if($submission->use_case)
    <h3 style="font-size:13px; letter-spacing:0.15em; color:#777; margin:24px 0 8px 0;">USE CASE</h3>
    <div style="background:#FAF7F0; border-left:3px solid #B98E2E; padding:16px 18px; border-radius:6px; white-space:pre-wrap; font-size:14px; line-height:1.6;">{{ $submission->use_case }}</div>
    @endif

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
