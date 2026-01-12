<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Welcome to the Clouvie waitlist</title>
</head>
<body>
    <p>Hi {{ $entry->name }},</p>

    <p>Thanks for joining the Clouvie waitlist. 🎉</p>

    <p>We have received your details and will reach out to you at {{ $entry->email }} as soon as we’re ready to onboard new users.</p>

    @if (! empty($entry->monthly_revenue_range))
        <p><strong>Monthly revenue range:</strong> {{ $entry->monthly_revenue_range }}</p>
    @endif

    <p>Best regards,<br>Clouvie Team</p>
</body>
</html>
