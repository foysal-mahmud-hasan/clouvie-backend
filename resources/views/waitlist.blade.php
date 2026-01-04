<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Waitlist Admin - Clouvie</title>
    <style>
        body {
            font-family: system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
            background: #f3f4f6;
            margin: 0;
            padding: 24px;
        }
        .container {
            max-width: 1100px;
            margin: 0 auto;
        }
        h1 {
            margin-bottom: 8px;
        }
        .subtitle {
            color: #6b7280;
            margin-bottom: 24px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 10px 25px rgba(15, 23, 42, 0.08);
        }
        thead {
            background: #111827;
            color: white;
        }
        th, td {
            padding: 12px 16px;
            text-align: left;
            font-size: 14px;
        }
        th {
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            font-size: 12px;
        }
        tbody tr:nth-child(even) {
            background: #f9fafb;
        }
        .badge {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 999px;
            background: #eef2ff;
            color: #3730a3;
            font-size: 12px;
        }
        .muted {
            color: #6b7280;
            font-size: 12px;
        }
        .empty {
            text-align: center;
            padding: 40px 16px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 10px 25px rgba(15, 23, 42, 0.08);
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Waitlist Signups</h1>
        <p class="subtitle">Internal-only view for marketing & product teams.</p>

        @if($entries->count() === 0)
            <div class="empty">
                <h2>No one on the waitlist yet</h2>
                <p class="muted">Once people submit the form on your marketing site, they will appear here.</p>
            </div>
        @else
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Monthly Revenue</th>
                        <th>Joined At</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($entries as $entry)
                        <tr>
                            <td>{{ $entry->id }}</td>
                            <td>{{ $entry->name }}</td>
                            <td>{{ $entry->email }}</td>
                            <td>
                                @if($entry->monthly_revenue_range)
                                    <span class="badge">{{ $entry->monthly_revenue_range }}</span>
                                @else
                                    <span class="muted">Not provided</span>
                                @endif
                            </td>
                            <td class="muted">{{ $entry->created_at->format('M d, Y H:i') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
</body>
</html>
