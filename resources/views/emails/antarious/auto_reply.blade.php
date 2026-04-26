<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>We got your demo request</title>
</head>
<body style="font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Helvetica, Arial, sans-serif; background:#F4F1EA; margin:0; padding:24px; color:#1a1a1a;">
<div style="max-width:560px; margin:0 auto; background:#ffffff; border-radius:12px; padding:40px; box-shadow:0 1px 3px rgba(0,0,0,0.06); border-top:4px solid #1F4E79;">
    <p style="font-size:12px; letter-spacing:0.2em; color:#1F4E79; margin:0 0 8px 0; font-weight:600;">ANTARIOUS</p>
    <h1 style="font-size:24px; margin:0 0 16px 0; color:#1F4E79;">Thanks, {{ $submission->name }} &mdash; your demo request is in.</h1>

    <p style="font-size:15px; line-height:1.6; color:#333;">
        Someone from the Antarious team will reach out within 24 hours with a tailored walkthrough and access details.
        If anything is time-sensitive, just reply to this email and it&rsquo;ll land in our sales inbox.
    </p>

    <p style="font-size:15px; line-height:1.6; color:#333;">
        For reference, here&rsquo;s what you sent us:
    </p>

    <div style="background:#FAF7F0; border-radius:8px; padding:18px 20px; margin:20px 0; font-size:14px; line-height:1.6;">
        <div><strong>Company:</strong> {{ $submission->company }}</div>
        @if($submission->role)<div><strong>Role:</strong> {{ $submission->role }}</div>@endif
        @if($submission->team_size)<div><strong>Team size:</strong> {{ $submission->team_size }}</div>@endif
        @if($submission->use_case)
        <div style="margin-top:12px;"><strong>Use case:</strong></div>
        <div style="margin-top:4px; white-space:pre-wrap;">{{ $submission->use_case }}</div>
        @endif
    </div>

    <p style="font-size:15px; line-height:1.6; color:#333;">
        Talk soon,<br>
        <strong>The Antarious team</strong>
    </p>

    <hr style="border:none; border-top:1px solid #eee; margin:32px 0 16px 0;">
    <p style="font-size:11px; color:#aaa; margin:0;">
        This is an automated confirmation. You can reply to this email and it will reach our sales inbox.
    </p>
</div>
</body>
</html>
