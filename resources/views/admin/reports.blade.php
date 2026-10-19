@extends('layouts.talentsys')

@section('title', 'Reports')

@section('content')
@php
    $categories = \App\Models\Category::all();
    $programmeStats = \App\Models\Student::with('achievements')->get()->groupBy('course')->map(function($students, $course) {
        $total = $students->sum(fn($s) => $s->achievements->where('status', 'approved')->sum('score'));
        return ['programme' => $course, 'score' => $total];
    })->values();

    $monthlyData = collect();
    for ($i = 11; $i >= 0; $i--) {
        $m = now()->subMonths($i);
        $score = \App\Models\Achievement::where('status', 'approved')
            ->whereYear('created_at', $m->year)
            ->whereMonth('created_at', $m->month)
            ->sum('score');
        $monthlyData->push(['label' => $m->format('M Y'), 'score' => $score]);
    }
@endphp

<div class="max-w-7xl mx-auto space-y-6">

    <div class="flex justify-between items-center">
        <div>
            <h2 class="text-xl font-bold text-slate-800 flex items-center gap-2">
                <i data-lucide="bar-chart-3" class="w-6 h-6 text-violet-600"></i>
                Reports
            </h2>
            <p class="text-sm text-slate-500">Overview of achievement data and statistics</p>
        </div>
        <a href="{{ route('coordinator.reports.export') }}"
            class="inline-flex items-center gap-2 px-4 py-2 gradient-primary text-white text-sm font-medium rounded-lg hover:scale-[1.02] transition shadow-md">
            <i data-lucide="download" class="w-4 h-4"></i> Export CSV
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
            <h3 class="text-sm font-semibold text-slate-800 mb-4">Achievements by Programme</h3>
            <div class="h-72"><canvas id="reportsProgrammeChart"></canvas></div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
            <h3 class="text-sm font-semibold text-slate-800 mb-4">Monthly Points Trend (12 Months)</h3>
            <div class="h-72"><canvas id="reportsMonthlyChart"></canvas></div>
        </div>
    </div>

</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    if (typeof Chart === 'undefined') return;

    const programmeCtx = document.getElementById('reportsProgrammeChart')?.getContext('2d');
    if (programmeCtx) {
        new Chart(programmeCtx, {
            type: 'bar',
            data: {
                labels: {!! json_encode($programmeStats->pluck('programme')) !!},
                datasets: [{
                    label: 'Points',
                    data: {!! json_encode($programmeStats->pluck('score')) !!},
                    backgroundColor: ['#8b5cf6', '#3b82f6', '#22c55e', '#f97316', '#ec4899'],
                    borderRadius: 6,
                }]
            },
            options: {
                responsive: true, maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: { y: { beginAtZero: true, ticks: { color: '#94a3b8' } }, x: { ticks: { color: '#94a3b8' } } }
            }
        });
    }

    const monthlyCtx = document.getElementById('reportsMonthlyChart')?.getContext('2d');
    if (monthlyCtx) {
        new Chart(monthlyCtx, {
            type: 'line',
            data: {
                labels: {!! json_encode($monthlyData->pluck('label')) !!},
                datasets: [{
                    label: 'Points',
                    data: {!! json_encode($monthlyData->pluck('score')) !!},
                    borderColor: '#8b5cf6',
                    backgroundColor: 'rgba(139, 92, 246, 0.1)',
                    fill: true,
                    tension: 0.4,
                    pointBackgroundColor: '#8b5cf6',
                }]
            },
            options: {
                responsive: true, maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: { y: { beginAtZero: true, ticks: { color: '#94a3b8' } }, x: { ticks: { color: '#94a3b8', maxTicksLimit: 8 } } }
            }
        });
    }
});
</script>
@endsection
