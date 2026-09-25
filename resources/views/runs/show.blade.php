<!DOCTYPE html>
<html>
<head>
    <title>Run #{{ $run->id }}</title>
    <style>
        body { font-family: system-ui, sans-serif; margin: 2rem; background: #f7f7f7; color: #222; }
        h1, h2 { margin-bottom: .5rem; }
        .card { background: #fff; padding: 1.25rem 1.5rem; border-radius: 10px; box-shadow: 0 1px 3px rgba(0,0,0,.08); margin-bottom: 1.5rem; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 8px 12px; text-align: left; border-bottom: 1px solid #eee; font-size: 13px; }
        th { background: #fafafa; font-weight: 600; }
        a { color: #2563eb; text-decoration: none; }
        .badge { display: inline-block; padding: 2px 8px; border-radius: 999px; font-size: 12px; font-weight: 600; }
        .badge-failed { background: #fee2e2; color: #991b1b; }
        .badge-ok { background: #dcfce7; color: #166534; }
        .meta { display: grid; grid-template-columns: max-content 1fr; gap: 6px 16px; font-size: 14px; }
        .meta dt { color: #666; }
    </style>
</head>
<body>
    <a href="{{ route('runs.index') }}">← back to all runs</a>
    <h1>Run #{{ $run->id }} — {{ $run->period }}</h1>

    <div class="card">
        <dl class="meta">
            <dt>Stage</dt>        <dd>{{ $run->stage }}</dd>
            <dt>Status</dt>       <dd>{{ $run->status }}</dd>
            <dt>Source file</dt>  <dd>{{ $run->source_file }}</dd>
            <dt>Total rows</dt>   <dd>{{ $run->total_rows }}</dd>
            <dt>Failed</dt>       <dd>{{ $run->failed_count }}</dd>
            <dt>Error</dt>        <dd>{{ $run->error ?? '—' }}</dd>
            <dt>Created</dt>      <dd>{{ $run->created_at }}</dd>
        </dl>
    </div>

    <h2>Failed Records ({{ $failed->count() }})</h2>
    <div class="card">
        <table>
            <thead>
                <tr><th>Recipient</th><th>Status</th><th>Reason</th><th>PIC</th></tr>
            </thead>
            <tbody>
            @forelse ($failed as $rec)
                <tr>
                    <td>{{ $rec->recipient_email }}</td>
                    <td><span class="badge badge-failed">{{ $rec->status }}</span></td>
                    <td>{{ $rec->failure_reason ?? '—' }}</td>
                    <td>{{ $rec->pic?->name ?? '—' }}</td>
                </tr>
            @empty
                <tr><td colspan="4" style="text-align:center;color:#888;">No failures.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>

    <h2>All Records ({{ $records->total() }})</h2>
    <div class="card">
        <table>
            <thead>
                <tr><th>Message ID</th><th>Recipient</th><th>Subject</th><th>Status</th><th>Sent At</th></tr>
            </thead>
            <tbody>
            @forelse ($records as $rec)
                <tr>
                    <td>{{ $rec->message_id }}</td>
                    <td>{{ $rec->recipient_email }}</td>
                    <td>{{ $rec->subject }}</td>
                    <td>{{ $rec->status }}</td>
                    <td>{{ $rec->sent_at }}</td>
                </tr>
            @empty
                <tr><td colspan="5" style="text-align:center;color:#888;">No records.</td></tr>
            @endforelse
            </tbody>
        </table>
        <div style="margin-top:1rem;">{{ $records->links() }}</div>
    </div>
</body>
</html>
