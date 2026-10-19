@extends('layouts.talentsys')

@section('title', 'Committee Dashboard')

@section('content')
@php
    $totalStudents = \App\Models\StudentProfile::where('student_status', 'active')->count();
    $totalApproved = \App\Models\Achievement::where('status', 'approved')->count();
    $totalPoints = \App\Models\Achievement::where('status', 'approved')->sum('score');
    $topTalents = \App\Models\StudentProfile::whereNotNull('talent')->where('student_status', 'active')
        ->select('talent', \Illuminate\Support\Facades\DB::raw('count(*) as c'))
        ->groupBy('talent')->orderByDesc('c')->take(5)->get();
    $topStudents = \App\Models\StudentProfile::with('user')->where('student_status', 'active')->get()
        ->sortByDesc(fn($s) => $s->getTotalScore())->take(5)->values();
@endphp

<div class="max-w-7xl mx-auto space-y-6">
    <div class="flex justify-between items-center">
        <div>
            <h2 class="text-xl font-bold text-slate-800">Committee Dashboard</h2>
            <p class="text-sm text-slate-500">Welcome back, {{ auth()->user()->name }} — review student talent data.</p>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-5 flex items-center gap-4">
            <div class="w-12 h-12 rounded-lg bg-blue-100 flex items-center justify-center text-blue-600"><i data-lucide="users" class="w-6 h-6"></i></div>
            <div><p class="text-2xl font-bold text-slate-800">{{ $totalStudents }}</p><p class="text-xs text-slate-500">Active Students</p></div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-5 flex items-center gap-4">
            <div class="w-12 h-12 rounded-lg bg-emerald-100 flex items-center justify-center text-emerald-600"><i data-lucide="award" class="w-6 h-6"></i></div>
            <div><p class="text-2xl font-bold text-slate-800">{{ $totalApproved }}</p><p class="text-xs text-slate-500">Approved Achievements</p></div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-5 flex items-center gap-4">
            <div class="w-12 h-12 rounded-lg bg-violet-100 flex items-center justify-center text-violet-600"><i data-lucide="star" class="w-6 h-6"></i></div>
            <div><p class="text-2xl font-bold text-slate-800">{{ $totalPoints }}</p><p class="text-xs text-slate-500">Total Merit Points</p></div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-5 flex items-center gap-4">
            <div class="w-12 h-12 rounded-lg bg-amber-100 flex items-center justify-center text-amber-600"><i data-lucide="sparkles" class="w-6 h-6"></i></div>
            <div><p class="text-2xl font-bold text-slate-800">{{ $topTalents->count() }}</p><p class="text-xs text-slate-500">Talent Categories</p></div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 bg-white rounded-xl shadow-sm border border-slate-200 p-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-sm font-semibold text-slate-800">Top Performing Students</h3>
                <a href="{{ route('leaderboard') }}" class="text-xs text-violet-600 hover:text-violet-700 font-medium">Full Leaderboard</a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="text-left text-slate-400 border-b border-slate-100">
                            <th class="pb-3 font-medium w-12">#</th>
                            <th class="pb-3 font-medium">Student</th>
                            <th class="pb-3 font-medium">Programme</th>
                            <th class="pb-3 font-medium">Points</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($topStudents as $index => $s)
                        <tr class="hover:bg-slate-50">
                            <td class="py-3 font-bold text-slate-400">{{ $index + 1 }}</td>
                            <td class="py-3 font-medium text-slate-800">{{ $s->user->name }}</td>
                            <td class="py-3 text-slate-600">{{ $s->programme }}</td>
                            <td class="py-3 font-semibold text-violet-600">{{ $s->getTotalScore() }} pts</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="space-y-6">
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
                <h3 class="text-sm font-semibold text-slate-800 mb-4">Quick Actions</h3>
                <div class="space-y-2">
                    <a href="{{ route('committee.search') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg bg-violet-50 hover:bg-violet-100 text-sm font-medium text-violet-700 transition">
                        <i data-lucide="search" class="w-4 h-4"></i> Find Students
                    </a>
                    <a href="{{ route('leaderboard') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg bg-amber-50 hover:bg-amber-100 text-sm font-medium text-amber-700 transition">
                        <i data-lucide="medal" class="w-4 h-4"></i> Leaderboard
                    </a>
                    <a href="{{ route('management.reports.achievements') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg bg-emerald-50 hover:bg-emerald-100 text-sm font-medium text-emerald-700 transition">
                        <i data-lucide="bar-chart-3" class="w-4 h-4"></i> Export Reports
                    </a>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
                <h3 class="text-sm font-semibold text-slate-800 mb-4">Top Talents</h3>
                @if($topTalents->count() > 0)
                <div class="space-y-2">
                    @foreach($topTalents as $t)
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-slate-700">{{ $t->talent }}</span>
                        <span class="text-xs font-medium text-slate-500">{{ $t->c }} student{{ $t->c !== 1 ? 's' : '' }}</span>
                    </div>
                    @endforeach
                </div>
                @else
                <p class="text-sm text-slate-400">No talent data yet.</p>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
