<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Antarious &mdash; Demo requests</title>
    <style>
        * { box-sizing: border-box; }
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Helvetica, Arial, sans-serif; background:#0d0d0f; color:#e6e6e6; margin:0; padding:32px; }
        h1 { font-size:24px; margin:0 0 4px 0; letter-spacing:-0.01em; }
        .sub { color:#888; font-size:13px; margin-bottom:24px; }
        .count { color:#B98E2E; font-weight:600; }
        table { width:100%; border-collapse:collapse; background:#17171a; border-radius:8px; overflow:hidden; font-size:13px; }
        th, td { text-align:left; padding:12px 14px; border-bottom:1px solid #25252a; vertical-align:top; }
        th { background:#1d1d22; color:#aaa; font-size:11px; text-transform:uppercase; letter-spacing:0.1em; font-weight:600; }
        tr:last-child td { border-bottom:none; }
        tr:hover td { background:#1c1c20; }
        .meta { color:#777; font-size:11px; }
        .use-case { max-width:380px; white-space:pre-wrap; color:#ccc; font-size:13px; line-height:1.5; }
        .pagination { margin-top:20px; }
        .pagination a, .pagination span { display:inline-block; padding:6px 10px; margin-right:4px; background:#1d1d22; color:#aaa; text-decoration:none; border-radius:4px; font-size:12px; }
        .pagination .current { background:#B98E2E; color:#fff; }
        a { color:#B98E2E; text-decoration:none; }
        a:hover { text-decoration:underline; }
        .empty { background:#17171a; padding:48px; text-align:center; border-radius:8px; color:#666; }
    </style>
</head>
<body>
    <h1>Antarious demo requests</h1>
    <p class="sub"><span class="count">{{ $requests->total() }}</span> total &middot; showing page {{ $requests->currentPage() }} of {{ $requests->lastPage() }}</p>

    @if($requests->isEmpty())
        <div class="empty">No demo requests yet.</div>
    @else
        <table>
            <thead>
                <tr>
                    <th style="width:50px;">#</th>
                    <th>Name / Email</th>
                    <th>Company</th>
                    <th>Role</th>
                    <th>Team size</th>
                    <th>Use case</th>
                    <th>When</th>
                </tr>
            </thead>
            <tbody>
            @foreach($requests as $row)
                <tr>
                    <td class="meta">{{ $row->id }}</td>
                    <td>
                        <div>{{ $row->name }}</div>
                        <div class="meta"><a href="mailto:{{ $row->email }}">{{ $row->email }}</a></div>
                    </td>
                    <td>{{ $row->company }}</td>
                    <td>{{ $row->role ?? '—' }}</td>
                    <td>{{ $row->team_size ?? '—' }}</td>
                    <td class="use-case">{{ $row->use_case ?? '—' }}</td>
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
        <div class="pagination">{{ $requests->links() }}</div>
    @endif
</body>
</html>
