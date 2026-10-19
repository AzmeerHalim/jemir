<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Achievements Report</title>
<style>
body { font-family: DejaVu Sans, sans-serif; font-size: 11px; }
h1 { color: #7c3aed; font-size: 18px; margin-bottom: 4px; }
.meta { color: #64748b; font-size: 10px; margin-bottom: 16px; }
table { width: 100%; border-collapse: collapse; margin-top: 8px; }
th, td { border: 1px solid #e2e8f0; padding: 6px 8px; text-align: left; }
th { background: #f1f5f9; font-weight: 600; color: #334155; }
.status-approved { color: #16a34a; font-weight: 600; }
.status-pending { color: #ca8a04; font-weight: 600; }
.status-rejected { color: #dc2626; font-weight: 600; }
</style>
</head>
<body>
<h1>Achievements Report</h1>
<p class="meta">Generated: {{ now()->format('d M Y H:i') }} | Total: {{ $achievements->count() }} records</p>

<table>
    <thead>
        <tr>
            <th>Student</th>
            <th>Matric</th>
            <th>Programme</th>
            <th>Title</th>
            <th>Category</th>
            <th>Level</th>
            <th>Points</th>
            <th>Status</th>
            <th>Date</th>
        </tr>
    </thead>
    <tbody>
        @foreach($achievements as $a)
        <tr>
            <td>{{ $a->student->user->name ?? '—' }}</td>
            <td>{{ $a->student->matric_no ?? '—' }}</td>
            <td>{{ $a->student->programme ?? '—' }}</td>
            <td>{{ $a->title }}</td>
            <td>{{ $a->category->name ?? '—' }}</td>
            <td>{{ ucfirst($a->level) }}</td>
            <td>{{ $a->score }}</td>
            <td class="status-{{ $a->status }}">{{ ucfirst($a->status) }}</td>
            <td>{{ $a->created_at->format('Y-m-d') }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
</body>
</html>
