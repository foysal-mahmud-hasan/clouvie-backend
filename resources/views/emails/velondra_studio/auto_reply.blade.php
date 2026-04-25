<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>We got your message</title>
</head>
<body style="font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Helvetica, Arial, sans-serif; background:#f5f5f5; margin:0; padding:24px; color:#1a1a1a;">
<div style="max-width:560px; margin:0 auto; background:#ffffff; border-radius:12px; padding:40px; box-shadow:0 1px 3px rgba(0,0,0,0.06);">
    <p style="font-size:12px; letter-spacing:0.2em; color:#777; margin:0 0 8px 0;">VELONDRA STUDIO</p>
    <h1 style="font-size:24px; margin:0 0 16px 0;">Thanks, {{ $submission->name }} &mdash; we got your message.</h1>

    <p style="font-size:15px; line-height:1.6; color:#333;">
        We&rsquo;ve received your project inquiry and someone from the team will get back to you within 24 hours.
        For urgent projects, you can also reach us on WhatsApp at <strong>+880 1832-311826</strong>.
    </p>

    <p style="font-size:15px; line-height:1.6; color:#333;">
        Here&rsquo;s a quick recap of what you sent us, in case you want to follow up:
    </p>

    <div style="background:#fafafa; border-radius:8px; padding:18px 20px; margin:20px 0; font-size:14px; line-height:1.6;">
        @if($submission->company)<div><strong>Company / Brand:</strong> {{ $submission->company }}</div>@endif
        @if($submission->budget)<div><strong>Budget range:</strong> {{ $submission->budget }}</div>@endif
        @if(!empty($submission->services))<div><strong>Services:</strong> {{ implode(', ', $submission->services) }}</div>@endif
        <div style="margin-top:12px; white-space:pre-wrap;">{{ $submission->message }}</div>
    </div>

    <p style="font-size:15px; line-height:1.6; color:#333;">
        Talk soon,<br>
        <strong>Velondra Studio</strong><br>
        Mirpur 2, Dhaka, Bangladesh
    </p>

    <hr style="border:none; border-top:1px solid #eee; margin:32px 0 16px 0;">
    <p style="font-size:11px; color:#aaa; margin:0;">
        This is an automated confirmation. You can reply to this email and it will reach the team.
    </p>
</div>
</body>
</html>
