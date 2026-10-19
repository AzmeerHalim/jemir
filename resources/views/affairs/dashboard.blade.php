@extends('layouts.talentsys')

@section('title', 'Student Affairs Dashboard')

@section('content')
@php
    $totalStudents = \App\Models\StudentProfile::where('student_status', 'active')->count();
    $approvedAchievements = \App\Models\Achievement::where('status', 'approved')->count();
    $totalPoints = \App\Models\Achievement::where('status', 'approved')->sum('score');
    $programmeCount = \App\Models\StudentProfile::select('programme')->distinct()->count();

    $programmeStats = \App\Models\StudentProfile::with('achievements')->get()->groupBy('programme')->map(function($students, $programme) {
        $total = $students->sum(fn($s) => $s->achievements->where('status', 'approved')->sum('score'));
        return ['programme' => $programme, 'score' => $total];
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
            <h2 class="text-xl font-bold text-slate-800">Student Affairs Dashboard</h2>
            <p class="text-sm text-slate-500">Welcome back, {{ auth()->user()->name }} — oversight of FSKTM student talent.</p>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-5 flex items-center gap-4">
            <div class="w-12 h-12 rounded-lg bg-blue-100 flex items-center justify-center text-blue-600"><i data-lucide="users" class="w-6 h-6"></i></div>
            <div><p class="text-2xl font-bold text-slate-800">{{ $totalStudents }}</p><p class="text-xs text-slate-500">Active Students</p></div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-5 flex items-center gap-4">
            <div class="w-12 h-12 rounded-lg bg-emerald-100 flex items-center justify-center text-emerald-600"><i data-lucide="award" class="w-6 h-6"></i></div>
            <div><p class="text-2xl font-bold text-slate-800">{{ $approvedAchievements }}</p><p class="text-xs text-slate-500">Approved Achievements</p></div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-5 flex items-center gap-4">
            <div class="w-12 h-12 rounded-lg bg-violet-100 flex items-center justify-center text-violet-600"><i data-lucide="star" class="w-6 h-6"></i></div>
            <div><p class="text-2xl font-bold text-slate-800">{{ $totalPoints }}</p><p class="text-xs text-slate-500">Total Merit Points</p></div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-5 flex items-center gap-4">
            <div class="w-12 h-12 rounded-lg bg-amber-100 flex items-center justify-center text-amber-600"><i data-lucide="graduation-cap" class="w-6 h-6"></i></div>
            <div><p class="text-2xl font-bold text-slate-800">{{ $programmeCount }}</p><p class="text-xs text-slate-500">Active Programmes</p></div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
                <h3 class="text-sm font-semibold text-slate-800 mb-4">Achievements by Programme</h3>
                <div class="h-72"><canvas id="programmeChart"></canvas></div>
            </div>
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
                <h3 class="text-sm font-semibold text-slate-800 mb-4">Monthly Merit Points Trend (12 Months)</h3>
                <div class="h-72"><canvas id="monthlyChart"></canvas></div>
            </div>
        </div>

        <div class="space-y-6">
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
                <h3 class="text-sm font-semibold text-slate-800 mb-4">Quick Actions</h3>
                <div class="space-y-2">
                    <a href="{{ route('management.reports.achievements') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg bg-violet-50 hover:bg-violet-100 text-sm font-medium text-violet-700 transition">
                        <i data-lucide="file-text" class="w-4 h-4"></i> Achievements Report (PDF)
                    </a>
                    <a href="{{ route('management.reports.students') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg bg-blue-50 hover:bg-blue-100 text-sm font-medium text-blue-700 transition">
                        <i data-lucide="download" class="w-4 h-4"></i> Students Report (CSV)
                    </a>
                    <a href="{{ route('leaderboard') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg bg-amber-50 hover:bg-amber-100 text-sm font-medium text-amber-700 transition">
                        <i data-lucide="medal" class="w-4 h-4"></i> Leaderboard
                    </a>
                    <a href="{{ route('committee.search') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg bg-emerald-50 hover:bg-emerald-100 text-sm font-medium text-emerald-700 transition">
                        <i data-lucide="search" class="w-4 h-4"></i> Find Students
                    </a>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
                <h3 class="text-sm font-semibold text-slate-800 mb-4">Summary</h3>
                <div class="space-y-3 text-sm">
                    <div class="flex justify-between"><span class="text-slate-500">Faculty</span><span class="font-medium text-slate-800">FSKTM</span></div>
                    <div class="flex justify-between"><span class="text-slate-500">Institution</span><span class="font-medium text-slate-800">UTHM</span></div>
                    <div class="flex justify-between"><span class="text-slate-500">Approval System</span><span class="font-medium text-slate-800">2-Stage</span></div>
                    <div class="flex justify-between"><span class="text-slate-500">Achievement Levels</span><span class="font-medium text-slate-800">Faculty → International</span></div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    if (typeof Chart === 'undefined') return;

    const programmeCtx = document.getElementById('programmeChart')?.getContext('2d');
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

    const monthlyCtx = document.getElementById('monthlyChart')?.getContext('2d');
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
