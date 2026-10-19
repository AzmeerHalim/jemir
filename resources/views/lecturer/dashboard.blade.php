@extends('layouts.talentsys')

@section('title', 'Lecturer Dashboard')

@section('content')
@php
    $profileId = auth()->user()->accompanyingLecturer?->id;
    $pendingParticipations = $profileId
        ? \App\Models\Participation::where('accompanying_id', $profileId)->where('lecturer_status', 'pending')->count()
        : 0;
    $pendingAchievements = $profileId
        ? \App\Models\Achievement::whereNull('participation_id')->where('accompanying_id', $profileId)->where('lecturer_status', 'pending')->count()
        : 0;
    $verifiedTotal = $profileId
        ? \App\Models\Participation::where('accompanying_id', $profileId)->whereIn('lecturer_status', ['verified', 'rejected'])->count()
        : 0;
    $recentPending = \App\Models\Participation::with('activity', 'student.user')
        ->where('accompanying_id', $profileId)
        ->where('lecturer_status', 'pending')
        ->latest()->take(5)->get();
@endphp

<div class="max-w-7xl mx-auto space-y-6">
    <div class="flex justify-between items-center">
        <div>
            <h2 class="text-xl font-bold text-slate-800">Lecturer Dashboard</h2>
            <p class="text-sm text-slate-500">Welcome back, {{ auth()->user()->name }} — review and verify student submissions.</p>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-5 flex items-center gap-4">
            <div class="w-12 h-12 rounded-lg bg-amber-100 flex items-center justify-center text-amber-600"><i data-lucide="clipboard-check" class="w-6 h-6"></i></div>
            <div><p class="text-2xl font-bold text-slate-800">{{ $pendingParticipations }}</p><p class="text-xs text-slate-500">Pending Participations</p></div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-5 flex items-center gap-4">
            <div class="w-12 h-12 rounded-lg bg-violet-100 flex items-center justify-center text-violet-600"><i data-lucide="trophy" class="w-6 h-6"></i></div>
            <div><p class="text-2xl font-bold text-slate-800">{{ $pendingAchievements }}</p><p class="text-xs text-slate-500">Pending Achievements</p></div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-5 flex items-center gap-4">
            <div class="w-12 h-12 rounded-lg bg-emerald-100 flex items-center justify-center text-emerald-600"><i data-lucide="check" class="w-6 h-6"></i></div>
            <div><p class="text-2xl font-bold text-slate-800">{{ $verifiedTotal }}</p><p class="text-xs text-slate-500">Total Reviewed</p></div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-5 flex items-center gap-4">
            <div class="w-12 h-12 rounded-lg bg-blue-100 flex items-center justify-center text-blue-600"><i data-lucide="history" class="w-6 h-6"></i></div>
            <div><p class="text-2xl font-bold text-slate-800">{{ $pendingParticipations + $pendingAchievements }}</p><p class="text-xs text-slate-500">Needs Your Attention</p></div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 bg-white rounded-xl shadow-sm border border-slate-200 p-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-sm font-semibold text-slate-800">Pending Participation Verifications</h3>
                <a href="{{ route('lecturer.verifications.index') }}" class="text-xs text-violet-600 hover:text-violet-700 font-medium">View All</a>
            </div>
            @if($recentPending->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="text-left text-slate-400 border-b border-slate-100">
                            <th class="pb-3 font-medium">Student</th>
                            <th class="pb-3 font-medium">Activity</th>
                            <th class="pb-3 font-medium">Date</th>
                            <th class="pb-3 font-medium">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($recentPending as $p)
                        <tr class="hover:bg-slate-50">
                            <td class="py-3 font-medium text-slate-800">{{ $p->student->user->name }}</td>
                            <td class="py-3 text-slate-600">{{ $p->activity->activity_name }}</td>
                            <td class="py-3 text-slate-500">{{ $p->created_at->format('d M Y') }}</td>
                            <td class="py-3"><a href="{{ route('lecturer.verifications.show', $p) }}" class="text-xs text-violet-600 hover:text-violet-700 font-medium">Review</a></td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <p class="text-sm text-slate-400 text-center py-8">All caught up! No pending verifications.</p>
            @endif
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
            <h3 class="text-sm font-semibold text-slate-800 mb-4">Quick Actions</h3>
            <div class="space-y-2">
                <a href="{{ route('lecturer.verifications.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg bg-amber-50 hover:bg-amber-100 text-sm font-medium text-amber-700 transition">
                    <i data-lucide="clipboard-check" class="w-4 h-4"></i> Participations
                    @if($pendingParticipations > 0)<span class="ml-auto px-2 py-0.5 rounded-full text-xs bg-amber-200 text-amber-800">{{ $pendingParticipations }}</span>@endif
                </a>
                <a href="{{ route('lecturer.achievement-verifications.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg bg-violet-50 hover:bg-violet-100 text-sm font-medium text-violet-700 transition">
                    <i data-lucide="trophy" class="w-4 h-4"></i> Achievements
                    @if($pendingAchievements > 0)<span class="ml-auto px-2 py-0.5 rounded-full text-xs bg-violet-200 text-violet-800">{{ $pendingAchievements }}</span>@endif
                </a>
                <a href="{{ route('lecturer.verifications.history') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg bg-slate-50 hover:bg-slate-100 text-sm font-medium text-slate-700 transition">
                    <i data-lucide="history" class="w-4 h-4"></i> Verification History
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
