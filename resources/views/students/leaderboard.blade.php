@extends('layouts.talentsys')

@section('title', 'Leaderboard')

@section('content')
@php
    $allStudents = $students;
    $currentStudentId = auth()->user()->student?->id;
    $currentRank = $allStudents->search(function($s) use ($currentStudentId) {
        return $s->id === $currentStudentId;
    });
    $currentRank = $currentRank === false ? null : $currentRank + 1;
    $nextRankScore = ($currentRank && $currentRank > 1) ? $allStudents->get($currentRank - 2)?->total_score : null;
    $currentScore = $currentRank ? $allStudents->get($currentRank - 1)?->total_score ?? 0 : 0;
    $gapToNext = $nextRankScore ? $nextRankScore - $currentScore : 0;
@endphp

<div class="max-w-6xl mx-auto space-y-6">

    <!-- HEADER -->
    <div class="flex justify-between items-center">
        <div>
            <h2 class="text-xl font-bold text-slate-800 flex items-center gap-2">
                <i data-lucide="trophy" class="w-6 h-6 text-amber-500"></i>
                Leaderboard
            </h2>
            <p class="text-sm text-slate-500">See how you rank among your peers</p>
        </div>
        <form method="GET" class="flex items-center gap-3">
            <select name="category_id" onchange="this.form.submit()"
                class="px-3 py-2 rounded-lg border border-slate-300 bg-white text-sm text-slate-700 focus:ring-2 focus:ring-violet-500 focus:border-violet-500 outline-none">
                <option value="">All Categories</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                @endforeach
            </select>
        </form>
    </div>

    <!-- TOP 3 PODIUM -->
    @if($allStudents->count() >= 3)
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 items-end">
        <!-- 2nd -->
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6 text-center order-2 md:order-1">
            <div class="w-12 h-12 rounded-full bg-gradient-to-br from-slate-300 to-slate-400 flex items-center justify-center text-white mx-auto mb-3">
                <i data-lucide="medal" class="w-6 h-6"></i>
            </div>
            <div class="w-16 h-16 rounded-full bg-slate-200 flex items-center justify-center text-xl font-bold text-slate-600 mx-auto mb-2">
                {{ strtoupper(substr($allStudents[1]->user->name, 0, 1)) }}
            </div>
            <p class="font-semibold text-slate-800">{{ $allStudents[1]->user->name }}</p>
            <p class="text-xs text-slate-400">{{ $allStudents[1]->programme }}</p>
            <p class="text-lg font-bold text-slate-700 mt-2">{{ $allStudents[1]->total_score }} pts</p>
            <p class="text-xs text-slate-400 mt-1">2nd Place</p>
        </div>

        <!-- 1st -->
        <div class="bg-white rounded-xl shadow-sm border-2 border-amber-400 p-6 text-center order-1 md:order-2 scale-105 relative">
            <div class="absolute -top-3 left-1/2 -translate-x-1/2">
                <span class="text-2xl">👑</span>
            </div>
            <div class="w-12 h-12 rounded-full bg-gradient-to-br from-amber-300 to-amber-500 flex items-center justify-center text-white mx-auto mb-3 mt-2">
                <i data-lucide="crown" class="w-6 h-6"></i>
            </div>
            <div class="w-20 h-20 rounded-full gradient-primary flex items-center justify-center text-2xl font-bold text-white mx-auto mb-2 shadow-lg">
                {{ strtoupper(substr($allStudents[0]->user->name, 0, 1)) }}
            </div>
            <p class="font-bold text-slate-800 text-lg">{{ $allStudents[0]->user->name }}</p>
            <p class="text-xs text-slate-400">{{ $allStudents[0]->programme }}</p>
            <p class="text-xl font-bold text-amber-600 mt-2">{{ $allStudents[0]->total_score }} pts</p>
            <p class="text-xs text-amber-500 font-medium mt-1">1st Place</p>
        </div>

        <!-- 3rd -->
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6 text-center order-3 md:order-3">
            <div class="w-12 h-12 rounded-full bg-gradient-to-br from-orange-300 to-orange-400 flex items-center justify-center text-white mx-auto mb-3">
                <i data-lucide="medal" class="w-6 h-6"></i>
            </div>
            <div class="w-16 h-16 rounded-full bg-slate-200 flex items-center justify-center text-xl font-bold text-slate-600 mx-auto mb-2">
                {{ strtoupper(substr($allStudents[2]->user->name, 0, 1)) }}
            </div>
            <p class="font-semibold text-slate-800">{{ $allStudents[2]->user->name }}</p>
            <p class="text-xs text-slate-400">{{ $allStudents[2]->programme }}</p>
            <p class="text-lg font-bold text-slate-700 mt-2">{{ $allStudents[2]->total_score }} pts</p>
            <p class="text-xs text-slate-400 mt-1">3rd Place</p>
        </div>
    </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- FULL RANKINGS TABLE -->
        <div class="lg:col-span-2 bg-white rounded-xl shadow-sm border border-slate-200 p-6">
            <h3 class="text-sm font-semibold text-slate-800 mb-4">Complete Rankings</h3>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="text-left text-slate-400 border-b border-slate-100">
                            <th class="pb-3 font-medium w-12">Rank</th>
                            <th class="pb-3 font-medium">Student</th>
                            <th class="pb-3 font-medium">Programme</th>
                            <th class="pb-3 font-medium text-right">Points</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($allStudents as $index => $s)
                        <tr class="{{ $s->id === $currentStudentId ? 'bg-violet-50' : 'hover:bg-slate-50' }}">
                            <td class="py-3">
                                @if($index === 0)
                                    <span class="text-lg">🥇</span>
                                @elseif($index === 1)
                                    <span class="text-lg">🥈</span>
                                @elseif($index === 2)
                                    <span class="text-lg">🥉</span>
                                @else
                                    <span class="text-sm font-bold text-slate-400">#{{ $index + 1 }}</span>
                                @endif
                            </td>
                            <td class="py-3">
                                <div class="flex items-center gap-2">
                                    <div class="w-7 h-7 rounded-full bg-slate-200 flex items-center justify-center text-xs font-semibold text-slate-600">
                                        {{ strtoupper(substr($s->user->name, 0, 1)) }}
                                    </div>
                                    <span class="font-medium text-slate-800">{{ $s->user->name }}</span>
                                </div>
                            </td>
                            <td class="py-3 text-slate-600">{{ $s->programme }}</td>
                            <td class="py-3 text-right font-semibold text-violet-600">{{ $s->total_score }} pts</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- YOUR STATS CARD -->
        @if($currentRank)
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
            <h3 class="text-sm font-semibold text-slate-800 mb-4">Your Position</h3>
            <div class="text-center mb-4">
                <div class="w-16 h-16 rounded-full gradient-primary flex items-center justify-center text-white text-2xl font-bold mx-auto shadow-lg">
                    #{{ $currentRank }}
                </div>
                <p class="text-sm text-slate-500 mt-2">Current Rank</p>
            </div>
            <div class="text-center mb-4">
                <p class="text-3xl font-bold text-violet-600">{{ $currentScore }}</p>
                <p class="text-xs text-slate-500">Total Points</p>
            </div>
            @if($gapToNext > 0)
            <div class="mb-4">
                <div class="flex justify-between text-xs mb-1">
                    <span class="text-slate-500">Progress</span>
                    <span class="text-slate-600 font-medium">{{ $currentScore }} / {{ $nextRankScore }} pts</span>
                </div>
                <div class="w-full bg-slate-100 rounded-full h-2.5">
                    <div class="gradient-primary h-2.5 rounded-full" style="width: {{ min(($currentScore / max($nextRankScore, 1)) * 100, 100) }}%"></div>
                </div>
            </div>
            <p class="text-sm text-slate-500 text-center">
                <span class="font-medium text-amber-600">{{ $gapToNext }}</span> points needed to reach #{{ $currentRank - 1 }}
            </p>
            @elseif($currentRank === 1)
            <p class="text-sm text-emerald-600 font-medium text-center">You're at the top! 🎉</p>
            @endif
        </div>
        @endif
    </div>

</div>
@endsection
