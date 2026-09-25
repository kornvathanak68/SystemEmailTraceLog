<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        h1 { font-size: 18px; margin-bottom: 4px; }
        .meta { color: #555; margin-bottom: 16px; }
        table { width: 100%; border-collapse: collapse; margin-top: 12px; }
        th, td { border: 1px solid #ccc; padding: 6px 8px; text-align: left; }
        th { background: #f0f0f0; }
        .fail { color: #b91c1c; }
    </style>
</head>
<body>
    <h1>Auto Email Delivery Report — {{ $run->period }}</h1>
    <div class="meta">
        Total rows: {{ $run->total_rows }} &nbsp; | &nbsp;
        Failed: {{ $run->failed_count }} &nbsp; | &nbsp;
        Generated: {{ now()->format('Y-m-d H:i') }}
    </div>

    <h3>Failed Deliveries</h3>
    <table>
        <thead>
            <tr>
                <th>Recipient</th>
                <th>Status</th>
                <th>Reason</th>
                <th>PIC</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($failedRecords as $r)
                <tr>
                    <td>{{ $r->recipient_email }}</td>
                    <td class="fail">{{ $r->status }}</td>
                    <td>{{ $r->failure_reason }}</td>
                    <td>{{ $r->pic->name ?? '-' }}</td>
                </tr>
            @empty
                <tr><td colspan="4">No failures.</td></tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
