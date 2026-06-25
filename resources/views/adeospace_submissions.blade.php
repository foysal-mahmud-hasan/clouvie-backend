<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>AdeoSpace &mdash; Submissions</title>
    <style>
        * { box-sizing: border-box; }
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Helvetica, Arial, sans-serif; background:#0d0d0f; color:#e6e6e6; margin:0; padding:32px; }
        h1 { font-size:24px; margin:0 0 4px 0; letter-spacing:-0.01em; }
        .sub { color:#888; font-size:13px; margin-bottom:24px; }
        .count { color:#2dd4bf; font-weight:600; }
        table { width:100%; border-collapse:collapse; background:#17171a; border-radius:8px; overflow:hidden; font-size:13px; }
        th, td { text-align:left; padding:12px 14px; border-bottom:1px solid #25252a; vertical-align:top; }
        th { background:#1d1d22; color:#aaa; font-size:11px; text-transform:uppercase; letter-spacing:0.1em; font-weight:600; }
        tr:last-child td { border-bottom:none; }
        tr:hover td { background:#1c1c20; }
        .meta { color:#777; font-size:11px; }
        .msg { max-width:380px; white-space:pre-wrap; color:#ccc; font-size:13px; line-height:1.5; }
        .pagination { margin-top:20px; }
        .pagination a, .pagination span { display:inline-block; padding:6px 10px; margin-right:4px; background:#1d1d22; color:#aaa; text-decoration:none; border-radius:4px; font-size:12px; }
        .pagination .current { background:#0d9488; color:#fff; }
        a { color:#2dd4bf; text-decoration:none; }
        a:hover { text-decoration:underline; }
        .empty { background:#17171a; padding:48px; text-align:center; border-radius:8px; color:#666; }
    </style>
</head>
<body>
    <h1>AdeoSpace submissions</h1>
    <p class="sub"><span class="count">{{ $submissions->total() }}</span> total &middot; showing page {{ $submissions->currentPage() }} of {{ $submissions->lastPage() }}</p>

    @if($submissions->isEmpty())
        <div class="empty">No submissions yet.</div>
    @else
        <table>
            <thead>
                <tr>
                    <th style="width:50px;">#</th>
                    <th>Name / Email</th>
                    <th>Organisation</th>
                    <th>Sector</th>
                    <th>Message</th>
                    <th>When</th>
                </tr>
            </thead>
            <tbody>
            @foreach($submissions as $row)
                <tr>
                    <td class="meta">{{ $row->id }}</td>
                    <td>
                        <div>{{ $row->name }}</div>
                        <div class="meta"><a href="mailto:{{ $row->email }}">{{ $row->email }}</a></div>
                    </td>
                    <td>{{ $row->organisation ?? '—' }}</td>
                    <td>{{ $row->sector ?? '—' }}</td>
                    <td class="msg">{{ $row->message }}</td>
                    <td class="meta">
                        {{ $row->created_at?->format('Y-m-d H:i') }}
                        @if($row->notified_at)
                            <div title="Notification sent at {{ $row->notified_at }}">&#9993; sent</div>
                        @else
                            <div style="color:#a55;" title="Notification email failed or pending">&#9993; pending</div>
                        @endif
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
        <div class="pagination">{{ $submissions->links() }}</div>
    @endif
</body>
</html>
