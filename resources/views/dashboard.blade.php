@extends('layouts.talentsys')

@section('title', 'Dashboard')

@section('content')
@php
    $student = auth()->user()->student;
    $score = $student?->getTotalScore() ?? 0;
    $achievements = $student?->achievements ?? collect();
    $pendingCount = $achievements->where('status', 'pending')->count();
    $approved = $achievements->where('status', 'approved');
    $achievementCount = $approved->count();

    $allStudents = \App\Models\Student::all();
    $ranked = $allStudents->sortByDesc(fn($s) => $s->getTotalScore())->values();
    $rank = $ranked->search(function($s) use ($student) { return $s->id === $student?->id; });
    $rank = $rank === false ? '-' : $rank + 1;

    // Category scores
    $categories = \App\Models\Category::all();
    $categoryScores = $categories->map(function($cat) use ($student) {
        return ['name' => $cat->name, 'score' => $student?->getScoreByCategory($cat->id) ?? 0];
    });

    // Monthly progress (last 6 months)
    $months = collect();
    for ($i = 5; $i >= 0; $i--) {
        $m = now()->subMonths($i);
        $monthlyScore = $approved->filter(function($a) use ($m) {
            return $a->created_at->format('Y-m') === $m->format('Y-m');
        })->sum('score');
        $months->push(['label' => $m->format('M'), 'score' => $monthlyScore]);
    }

    $topStudents = $ranked->take(5);
    $recentAchievements = $achievements->sortByDesc('created_at')->take(5);

    $categoryColors = [
        'Akademik' => '#3b82f6',
        'Sukan' => '#22c55e',
        'Kokurikulum' => '#8b5cf6',
        'Kepimpinan' => '#f97316',
        'Inovasi' => '#ec4899',
    ];
    if ($categories->count() === 0 || $categoryScores->sum('score') === 0) {
        $categoryScores = collect([
            ['name' => 'Academic', 'score' => 0],
            ['name' => 'Sports', 'score' => 0],
            ['name' => 'Leadership', 'score' => 0],
            ['name' => 'Community', 'score' => 0],
            ['name' => 'Skills', 'score' => 0],
        ]);
    }
@endphp

<div class="max-w-7xl mx-auto space-y-6">

    <!-- WELCOME -->
    <div class="flex justify-between items-center">
        <div>
            <h2 class="text-xl font-bold text-slate-800">Welcome back, {{ auth()->user()->name }}</h2>
            <p class="text-sm text-slate-500">{{ now()->format('l, j F Y') }}</p>
        </div>
        <p class="text-sm text-slate-400">Track your achievements and merit points</p>
    </div>

    <!-- STATS CARDS -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-5 flex items-center gap-4">
            <div class="w-12 h-12 rounded-lg gradient-card flex items-center justify-center text-white shadow">
                <i data-lucide="trophy" class="w-6 h-6"></i>
            </div>
            <div>
                <p class="text-2xl font-bold text-slate-800">{{ $score }}</p>
                <p class="text-xs text-slate-500">Total Merit Points</p>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-5 flex items-center gap-4">
            <div class="w-12 h-12 rounded-lg bg-blue-100 flex items-center justify-center text-blue-600">
                <i data-lucide="award" class="w-6 h-6"></i>
            </div>
            <div>
                <p class="text-2xl font-bold text-slate-800">{{ $achievementCount }}</p>
                <p class="text-xs text-slate-500">Achievements</p>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-5 flex items-center gap-4">
            <div class="w-12 h-12 rounded-lg bg-amber-100 flex items-center justify-center text-amber-600">
                <i data-lucide="clock" class="w-6 h-6"></i>
            </div>
            <div>
                <p class="text-2xl font-bold text-slate-800">{{ $pendingCount }}</p>
                <p class="text-xs text-slate-500">Pending</p>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-5 flex items-center gap-4">
            <div class="w-12 h-12 rounded-lg bg-emerald-100 flex items-center justify-center text-emerald-600">
                <i data-lucide="trending-up" class="w-6 h-6"></i>
            </div>
            <div>
                <p class="text-2xl font-bold text-slate-800">#{{ $rank }}</p>
                <p class="text-xs text-slate-500">Your Rank</p>
            </div>
        </div>
    </div>

    <!-- CHARTS SECTION -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Merit by Category -->
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
            <h3 class="text-sm font-semibold text-slate-800 mb-4">Merit Points by Category</h3>
            <div class="h-64 flex items-center justify-center">
                <canvas id="categoryChart"></canvas>
            </div>
        </div>

        <!-- Monthly Progress -->
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
            <h3 class="text-sm font-semibold text-slate-800 mb-4">Monthly Progress</h3>
            <div class="h-64 flex items-center justify-center">
                <canvas id="monthlyChart"></canvas>
            </div>
        </div>
    </div>

    <!-- RECENT ACHIEVEMENTS -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-sm font-semibold text-slate-800">Recent Achievements</h3>
            <a href="{{ route('achievements.index') }}" class="text-xs text-violet-600 hover:text-violet-700 font-medium">View All</a>
        </div>

        @if($recentAchievements->count() > 0)
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-left text-slate-400 border-b border-slate-100">
                        <th class="pb-3 font-medium">Achievement</th>
                        <th class="pb-3 font-medium">Category</th>
                        <th class="pb-3 font-medium">Points</th>
                        <th class="pb-3 font-medium">Date</th>
                        <th class="pb-3 font-medium">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($recentAchievements as $a)
                    <tr class="hover:bg-slate-50">
                        <td class="py-3 text-slate-800 font-medium">{{ $a->title }}</td>
                        <td class="py-3">
                            <span class="px-2.5 py-0.5 rounded-full text-xs font-medium bg-violet-100 text-violet-700">
                                {{ $a->category?->name ?? 'N/A' }}
                            </span>
                        </td>
                        <td class="py-3 text-slate-600">{{ $a->score ? '+'.$a->score : '-' }}</td>
                        <td class="py-3 text-slate-500">{{ $a->created_at->format('d M Y') }}</td>
                        <td class="py-3">
                            @if($a->status === 'approved')
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-700">
                                    <i data-lucide="check" class="w-3 h-3"></i> Approved
                                </span>
                            @elseif($a->status === 'pending')
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-700">
                                    <i data-lucide="clock" class="w-3 h-3"></i> Pending
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-700">
                                    <i data-lucide="x" class="w-3 h-3"></i> Rejected
                                </span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="text-center py-8 text-slate-400">
            <i data-lucide="inbox" class="w-10 h-10 mx-auto mb-2"></i>
            <p class="text-sm">No achievements yet. Submit your first achievement!</p>
            <a href="{{ route('achievements.create') }}" class="inline-block mt-2 text-sm text-violet-600 hover:text-violet-700 font-medium">
                Submit Achievement
            </a>
        </div>
        @endif
    </div>

    <!-- LEADERBOARD PREVIEW -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-sm font-semibold text-slate-800">Top 5 Students</h3>
            <a href="{{ route('leaderboard') }}" class="text-xs text-violet-600 hover:text-violet-700 font-medium">View Full Leaderboard</a>
        </div>

        <div class="space-y-2">
            @foreach($topStudents as $index => $s)
            <div class="flex items-center justify-between py-2 px-3 rounded-lg {{ $s->user_id === auth()->id() ? 'bg-violet-50 border border-violet-200' : 'hover:bg-slate-50' }}">
                <div class="flex items-center gap-3">
                    <span class="w-6 text-sm font-bold text-slate-400">#{{ $index + 1 }}</span>
                    <div class="w-8 h-8 rounded-full bg-slate-200 flex items-center justify-center text-xs font-semibold text-slate-600">
                        {{ strtoupper(substr($s->user->name, 0, 1)) }}
                    </div>
                    <div>
                        <p class="text-sm font-medium text-slate-800">{{ $s->user->name }}</p>
                        <p class="text-xs text-slate-400">{{ $s->course }}</p>
                    </div>
                </div>
                <span class="text-sm font-semibold text-violet-600">{{ $s->getTotalScore() }} pts</span>
            </div>
            @endforeach
        </div>
    </div>

</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    if (typeof Chart === 'undefined') return;

    // Category Chart (Pie)
    const catCtx = document.getElementById('categoryChart')?.getContext('2d');
    if (catCtx) {
        new Chart(catCtx, {
            type: 'doughnut',
            data: {
                labels: {!! json_encode($categoryScores->pluck('name')) !!},
                datasets: [{
                    data: {!! json_encode($categoryScores->pluck('score')) !!},
                    backgroundColor: ['#3b82f6', '#22c55e', '#8b5cf6', '#f97316', '#ec4899'],
                    borderWidth: 0,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'bottom', labels: { padding: 16, usePointStyle: true } }
                }
            }
        });
    }

    // Monthly Chart (Bar)
    const monCtx = document.getElementById('monthlyChart')?.getContext('2d');
    if (monCtx) {
        new Chart(monCtx, {
            type: 'bar',
            data: {
                labels: {!! json_encode($months->pluck('label')) !!},
                datasets: [{
                    label: 'Points',
                    data: {!! json_encode($months->pluck('score')) !!},
                    backgroundColor: '#8b5cf6',
                    borderRadius: 6,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: { beginAtZero: true, ticks: { color: '#94a3b8' } },
                    x: { ticks: { color: '#94a3b8' } }
                }
            }
        });
    }
});
</script>
@endsection
