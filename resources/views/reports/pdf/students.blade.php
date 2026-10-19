<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Students Report</title>
<style>
body { font-family: DejaVu Sans, sans-serif; font-size: 11px; }
h1 { color: #7c3aed; font-size: 18px; margin-bottom: 4px; }
.meta { color: #64748b; font-size: 10px; margin-bottom: 16px; }
table { width: 100%; border-collapse: collapse; margin-top: 8px; }
th, td { border: 1px solid #e2e8f0; padding: 6px 8px; text-align: left; }
th { background: #f1f5f9; font-weight: 600; color: #334155; }
</style>
</head>
<body>
<h1>Students Report</h1>
<p class="meta">Generated: {{ now()->format('d M Y H:i') }} | Total: {{ $students->count() }} students</p>

<table>
    <thead>
        <tr>
            <th>Name</th>
            <th>Matric</th>
            <th>Programme</th>
            <th>Year</th>
            <th>Cluster</th>
            <th>Total Points</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>
        @foreach($students as $s)
        <tr>
            <td>{{ $s->user->name ?? '—' }}</td>
            <td>{{ $s->matric_no }}</td>
            <td>{{ $s->programme }}</td>
            <td>{{ $s->year }}</td>
            <td>{{ $s->cluster->cluster_name ?? '—' }}</td>
            <td>{{ $s->getTotalScore() }}</td>
            <td>{{ ucfirst($s->student_status) }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
</body>
</html>
