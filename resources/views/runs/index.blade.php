<!DOCTYPE html>
<html>
<head>
    <title>Report Runs</title>
    <style>
        body { font-family: system-ui, sans-serif; margin: 2rem; background: #f7f7f7; color: #222; }
        h1 { margin-bottom: 1rem; }
        table { width: 100%; border-collapse: collapse; background: #fff; box-shadow: 0 1px 3px rgba(0,0,0,.08); border-radius: 8px; overflow: hidden; }
        th, td { padding: 10px 14px; text-align: left; border-bottom: 1px solid #eee; font-size: 14px; }
        th { background: #fafafa; font-weight: 600; }
        tr:hover { background: #fcfcfc; }
        a { color: #2563eb; text-decoration: none; }
        .badge { display: inline-block; padding: 2px 8px; border-radius: 999px; font-size: 12px; font-weight: 600; }
        .badge-success { background: #dcfce7; color: #166534; }
        .badge-failed  { background: #fee2e2; color: #991b1b; }
        .badge-running { background: #fef3c7; color: #92400e; }
    </style>
</head>
<body>
    <h1>Test a Tool Auto Delivery Report — Runs</h1>
    <table>
        <thead>
            <tr>
                <th>#</th><th>Period</th><th>Stage</th><th>Status</th>
                <th>Total</th><th>Failed</th><th>Created</th>
            </tr>
        </thead>
        <tbody>
        @forelse ($runs as $run)
            <tr>
                <td><a href="{{ route('runs.show', $run) }}">{{ $run->id }}</a></td>
                <td>{{ $run->period }}</td>
                <td>{{ $run->stage }}</td>
                <td>
                    <span class="badge badge-{{ $run->status === 'success' ? 'success' : ($run->status === 'failed' ? 'failed' : 'running') }}">
                        {{ $run->status }}
                    </span>
                </td>
                <td>{{ $run->total_rows ?? '—' }}</td>
                <td>{{ $run->failed_count ?? '—' }}</td>
                <td>{{ $run->created_at->diffForHumans() }}</td>
            </tr>
        @empty
            <tr><td colspan="7" style="text-align:center;color:#888;padding:2rem;">No runs yet.</td></tr>
        @endforelse
        </tbody>
    </table>
    <div style="margin-top:1rem;">{{ $runs->links() }}</div>
</body>
</html>
