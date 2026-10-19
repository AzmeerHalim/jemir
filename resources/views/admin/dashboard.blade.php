@extends('layouts.talentsys')

@section('title', 'Admin Dashboard')

@section('content')
@php
    $totalStudents = \App\Models\Student::count();
    $totalAchievements = \App\Models\Achievement::count();
    $pending = \App\Models\Achievement::where('status', 'pending')->count();
    $totalScore = \App\Models\Achievement::where('status', 'approved')->sum('score');

    $categories = \App\Models\Category::all();
    $programmeStats = \App\Models\Student::with('achievements')->get()->groupBy('course')->map(function($students, $course) {
        $total = $students->sum(fn($s) => $s->achievements->where('status', 'approved')->sum('score'));
        return ['programme' => $course, 'score' => $total];
    })->values();

    $categoryStats = $categories->map(fn($cat) => [
        'name' => $cat->name,
        'count' => \App\Models\Achievement::where('category_id', $cat->id)->count()
    ]);

    $recentPending = \App\Models\Achievement::with('student.user')
        ->where('status', 'pending')->latest()->take(5)->get();

    $topStudents = \App\Models\Student::with('user')->get()
        ->sortByDesc(fn($s) => $s->getTotalScore())->take(3)->values();
@endphp

<div class="max-w-7xl mx-auto space-y-6">

    <div class="flex justify-between items-center">
        <div>
            <h2 class="text-xl font-bold text-slate-800">Admin Dashboard</h2>
            <p class="text-sm text-slate-500">Welcome back, Administrator</p>
        </div>
        <p class="text-sm text-slate-400">{{ now()->format('l, j F Y') }}</p>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-5 flex items-center gap-4">
            <div class="w-12 h-12 rounded-lg bg-blue-100 flex items-center justify-center text-blue-600">
                <i data-lucide="users" class="w-6 h-6"></i>
            </div>
            <div>
                <p class="text-2xl font-bold text-slate-800">{{ $totalStudents }}</p>
                <p class="text-xs text-slate-500">Total Students</p>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-5 flex items-center gap-4">
            <div class="w-12 h-12 rounded-lg bg-emerald-100 flex items-center justify-center text-emerald-600">
                <i data-lucide="award" class="w-6 h-6"></i>
            </div>
            <div>
                <p class="text-2xl font-bold text-slate-800">{{ $totalAchievements }}</p>
                <p class="text-xs text-slate-500">Total Achievements</p>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-5 flex items-center gap-4">
            <div class="w-12 h-12 rounded-lg bg-amber-100 flex items-center justify-center text-amber-600">
                <i data-lucide="clock" class="w-6 h-6"></i>
            </div>
            <div>
                <p class="text-2xl font-bold text-slate-800">{{ $pending }}</p>
                <p class="text-xs text-slate-500">Pending Approvals</p>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-5 flex items-center gap-4">
            <div class="w-12 h-12 rounded-lg bg-violet-100 flex items-center justify-center text-violet-600">
                <i data-lucide="trending-up" class="w-6 h-6"></i>
            </div>
            <div>
                <p class="text-2xl font-bold text-slate-800">{{ $totalScore }}</p>
                <p class="text-xs text-slate-500">Merit Points Awarded</p>
            </div>
        </div>
    </div>

    <!-- Charts -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
            <h3 class="text-sm font-semibold text-slate-800 mb-4">Achievements by Programme</h3>
            <div class="h-64"><canvas id="programmeChart"></canvas></div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
            <h3 class="text-sm font-semibold text-slate-800 mb-4">Category Distribution</h3>
            <div class="h-64"><canvas id="categoryChart"></canvas></div>
        </div>
    </div>

    <!-- Top 3 Podium -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
        <h3 class="text-sm font-semibold text-slate-800 mb-4">Top Performers This Month</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            @foreach($topStudents as $index => $s)
            <div class="text-center p-4 rounded-xl border {{ $index === 0 ? 'border-amber-400 bg-amber-50' : ($index === 1 ? 'border-slate-300 bg-slate-50' : 'border-orange-300 bg-orange-50') }}">
                <span class="text-2xl">{{ $index === 0 ? '🥇' : ($index === 1 ? '🥈' : '🥉') }}</span>
                <div class="w-16 h-16 rounded-full bg-slate-200 flex items-center justify-center text-lg font-bold text-slate-600 mx-auto mt-2">
                    {{ strtoupper(substr($s->user->name, 0, 1)) }}
                </div>
                <p class="font-semibold text-slate-800 mt-2">{{ $s->user->name }}</p>
                <p class="text-xs text-slate-500">{{ $s->course }}</p>
                <p class="text-lg font-bold text-violet-600 mt-1">{{ $s->getTotalScore() }} pts</p>
            </div>
            @endforeach
        </div>
    </div>

    <!-- Recent Pending -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-sm font-semibold text-slate-800">Recent Pending Approvals</h3>
            <a href="{{ route('admin.approvals') }}" class="text-xs text-violet-600 hover:text-violet-700 font-medium">View All</a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-left text-slate-400 border-b border-slate-100">
                        <th class="pb-3 font-medium">Student</th>
                        <th class="pb-3 font-medium">Achievement</th>
                        <th class="pb-3 font-medium">Points</th>
                        <th class="pb-3 font-medium">Date</th>
                        <th class="pb-3 font-medium">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($recentPending as $a)
                    <tr class="hover:bg-slate-50">
                        <td class="py-3">{{ $a->student->user->name }}</td>
                        <td class="py-3 font-medium text-slate-800">{{ $a->title }}</td>
                        <td class="py-3">{{ $a->score }}</td>
                        <td class="py-3 text-slate-500">{{ $a->created_at->format('d M Y') }}</td>
                        <td class="py-3">
                            <a href="{{ route('admin.approvals') }}" class="text-xs text-violet-600 font-medium hover:text-violet-700">Review</a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
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

    const catCtx = document.getElementById('categoryChart')?.getContext('2d');
    if (catCtx) {
        new Chart(catCtx, {
            type: 'pie',
            data: {
                labels: {!! json_encode($categoryStats->pluck('name')) !!},
                datasets: [{
                    data: {!! json_encode($categoryStats->pluck('count')) !!},
                    backgroundColor: ['#8b5cf6', '#3b82f6', '#22c55e', '#f97316', '#ec4899'],
                }]
            },
            options: {
                responsive: true, maintainAspectRatio: false,
                plugins: { legend: { position: 'bottom', labels: { padding: 16, usePointStyle: true } } }
            }
        });
    }
});
</script>
@endsection
