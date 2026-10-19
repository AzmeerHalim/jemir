@extends('layouts.talentsys')

@section('title', 'History')

@section('content')
@php
    $student = auth()->user()->student;
    $allAchievements = $student?->achievements()->with('category')->latest()->get() ?? collect();
    $totalAchievements = $allAchievements->count();
    $totalPoints = $allAchievements->where('status', 'approved')->sum('score');
    $thisSemester = $allAchievements->where('created_at', '>=', now()->subMonths(6));
    $approved = $allAchievements->where('status', 'approved')->count();
    $approvalRate = $totalAchievements > 0 ? round(($approved / $totalAchievements) * 100) : 0;
@endphp

<div class="max-w-6xl mx-auto space-y-6">

    <div class="flex justify-between items-center">
        <div>
            <h2 class="text-xl font-bold text-slate-800 flex items-center gap-2">
                <i data-lucide="history" class="w-6 h-6 text-violet-600"></i>
                Achievement History
            </h2>
            <p class="text-sm text-slate-500">View all your submitted achievements</p>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-4">
            <p class="text-xs text-slate-500">Total Achievements</p>
            <p class="text-xl font-bold text-slate-800">{{ $totalAchievements }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-4">
            <p class="text-xs text-slate-500">Total Points Earned</p>
            <p class="text-xl font-bold text-violet-600">{{ $totalPoints }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-4">
            <p class="text-xs text-slate-500">This Semester</p>
            <p class="text-xl font-bold text-slate-800">{{ $thisSemester->count() }} achievements</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-4">
            <p class="text-xs text-slate-500">Approval Rate</p>
            <p class="text-xl font-bold text-{{ $approvalRate >= 70 ? 'emerald' : ($approvalRate >= 40 ? 'amber' : 'red') }}-600">{{ $approvalRate }}%</p>
        </div>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-left text-slate-400 border-b border-slate-100 bg-slate-50">
                        <th class="py-3 px-4 font-medium">Date</th>
                        <th class="py-3 px-4 font-medium">Achievement</th>
                        <th class="py-3 px-4 font-medium">Category</th>
                        <th class="py-3 px-4 font-medium">Level</th>
                        <th class="py-3 px-4 font-medium">Points</th>
                        <th class="py-3 px-4 font-medium">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($allAchievements as $a)
                    <tr class="hover:bg-slate-50">
                        <td class="py-3 px-4 text-slate-500">{{ $a->created_at->format('d M Y') }}</td>
                        <td class="py-3 px-4 font-medium text-slate-800">{{ $a->title }}</td>
                        <td class="py-3 px-4">
                            <span class="px-2 py-0.5 rounded-full text-xs font-medium bg-violet-100 text-violet-700">{{ $a->category?->name }}</span>
                        </td>
                        <td class="py-3 px-4 capitalize text-slate-600">{{ $a->level }}</td>
                        <td class="py-3 px-4 font-semibold {{ $a->score ? 'text-emerald-600' : 'text-slate-400' }}">{{ $a->score ? '+'.$a->score : '-' }}</td>
                        <td class="py-3 px-4">
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
                                @if($a->rejection_reason)
                                    <p class="text-xs text-red-500 mt-0.5">{{ $a->rejection_reason }}</p>
                                @endif
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="py-8 text-center text-slate-400">
                            <i data-lucide="inbox" class="w-10 h-10 mx-auto mb-2"></i>
                            <p>No achievements yet</p>
                            <a href="{{ route('achievements.create') }}" class="text-violet-600 font-medium text-sm hover:text-violet-700">
                                Submit your first achievement
                            </a>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
