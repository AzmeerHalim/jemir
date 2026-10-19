@extends('layouts.talentsys')

@section('title', 'My Profile')

@section('content')
@php
    $student = auth()->user()->student;
    $user = auth()->user();
    $scores = \App\Models\Category::all()->map(fn($c) => [
        'name' => $c->name,
        'score' => $student?->getScoreByCategory($c->id) ?? 0,
    ]);
    $totalScore = $student?->getTotalScore() ?? 0;
    $approvedCount = $student?->achievements()->where('status', 'approved')->count() ?? 0;
    $pendingCount = $student?->achievements()->where('status', 'pending')->count() ?? 0;
    $rejectedCount = $student?->achievements()->where('status', 'rejected')->count() ?? 0;
    $verifiedParticipationsCount = $student?->verifiedParticipations()->count() ?? 0;
    $rank = $student
        ? \App\Models\Merit::where('student_id', $student->id)->value('ranking')
        : null;
    $recentAchievements = $student?->achievements()->latest()->take(5)->get() ?? collect();
    $clusters = \App\Models\Cluster::orderBy('cluster_name')->get();
@endphp

@if(!$student)
    <div class="max-w-2xl mx-auto">
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-12 text-center">
            <i data-lucide="user-x" class="w-12 h-12 mx-auto text-slate-400 mb-3"></i>
            <h2 class="text-lg font-bold text-slate-800">No student profile found</h2>
            <p class="text-sm text-slate-500 mt-1">Please contact the talent coordinator.</p>
        </div>
    </div>
@else

<div class="max-w-6xl mx-auto space-y-6">

    {{-- =================== HEADER =================== --}}
    <div class="flex flex-col md:flex-row md:justify-between md:items-center gap-4">
        <div>
            <h2 class="text-xl font-bold text-slate-800 flex items-center gap-2">
                <i data-lucide="user-circle" class="w-6 h-6 text-violet-600"></i>
                My Profile
            </h2>
            <p class="text-sm text-slate-500">All the information the talent system has on you.</p>
        </div>
        <a href="{{ route('profile.edit') }}"
            class="inline-flex items-center gap-2 px-4 py-2.5 gradient-primary text-white text-sm font-medium rounded-lg hover:scale-[1.02] transition shadow-md">
            <i data-lucide="edit" class="w-4 h-4"></i> Edit Profile
        </a>
    </div>

    {{-- =================== STATS =================== --}}
    <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-4 flex items-center gap-3">
            <div class="w-10 h-10 rounded-lg gradient-primary flex items-center justify-center text-white shadow">
                <i data-lucide="trophy" class="w-5 h-5"></i>
            </div>
            <div>
                <p class="text-2xl font-bold text-slate-800">{{ $totalScore }}</p>
                <p class="text-xs text-slate-500">Total Points</p>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-4">
            <p class="text-xs text-slate-500">Approved</p>
            <p class="text-2xl font-bold text-emerald-600">{{ $approvedCount }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-4">
            <p class="text-xs text-slate-500">Pending</p>
            <p class="text-2xl font-bold text-amber-600">{{ $pendingCount }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-4">
            <p class="text-xs text-slate-500">Verified Participations</p>
            <p class="text-2xl font-bold text-blue-600">{{ $verifiedParticipationsCount }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-4">
            <p class="text-xs text-slate-500">Leaderboard Rank</p>
            <p class="text-2xl font-bold text-violet-600">{{ $rank ? '#'.$rank : '—' }}</p>
        </div>
    </div>

    {{-- =================== PROFILE CARD =================== --}}
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
        <div class="flex flex-col sm:flex-row gap-6 items-center sm:items-start">
            <div class="flex-shrink-0">
                <div class="w-28 h-28 rounded-full gradient-primary flex items-center justify-center text-white text-3xl font-bold shadow-lg">
                    {{ strtoupper(substr($user->name, 0, 1)) }}
                </div>
            </div>
            <div class="flex-1 text-center sm:text-left">
                <h2 class="text-2xl font-bold text-slate-800">{{ $user->name }}</h2>
                <p class="text-sm text-slate-500 mt-0.5">@ {{ $user->email }}</p>
                <div class="flex flex-wrap items-center justify-center sm:justify-start gap-2 mt-3">
                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-medium bg-violet-100 text-violet-700">
                        <i data-lucide="hash" class="w-3 h-3"></i> {{ $student->matric_no }}
                    </span>
                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-700">
                        <i data-lucide="graduation-cap" class="w-3 h-3"></i> {{ $student->programme ?? '—' }}
                    </span>
                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-700">
                        <i data-lucide="calendar" class="w-3 h-3"></i> Year {{ $student->year }}
                    </span>
                    @if($student->cgpa)
                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-700">
                            <i data-lucide="bar-chart-3" class="w-3 h-3"></i> CGPA {{ $student->cgpa }}
                        </span>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- =================== TWO-COLUMN BODY =================== --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        {{-- LEFT --}}
        <div class="space-y-6">

            <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
                <h3 class="text-sm font-semibold text-slate-800 flex items-center gap-2 mb-4">
                    <i data-lucide="id-card" class="w-4 h-4 text-violet-600"></i> Personal Information
                </h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                    <div><p class="text-slate-500 text-xs">IC Number</p><p class="font-medium text-slate-800">{{ $student->ic_number ?? '—' }}</p></div>
                    <div><p class="text-slate-500 text-xs">Date of Birth</p><p class="font-medium text-slate-800">{{ $student->date_of_birth?->format('d M Y') ?? '—' }}</p></div>
                    <div><p class="text-slate-500 text-xs">Gender</p><p class="font-medium text-slate-800">{{ $student->gender ?? '—' }}</p></div>
                    <div><p class="text-slate-500 text-xs">Nationality</p><p class="font-medium text-slate-800">{{ $student->nationality ?? '—' }}</p></div>
                    <div><p class="text-slate-500 text-xs">Phone</p><p class="font-medium text-slate-800">{{ $student->phone ?? '—' }}</p></div>
                    <div><p class="text-slate-500 text-xs">Talent</p><p class="font-medium text-slate-800">{{ $student->talent ?? '—' }}</p></div>
                    <div class="sm:col-span-2"><p class="text-slate-500 text-xs">Address</p><p class="font-medium text-slate-800">{{ $student->address ?? '—' }}</p></div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
                <h3 class="text-sm font-semibold text-slate-800 flex items-center gap-2 mb-4">
                    <i data-lucide="building-2" class="w-4 h-4 text-violet-600"></i> Academic
                </h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                    <div><p class="text-slate-500 text-xs">Programme</p><p class="font-medium text-slate-800">{{ $student->programme ?? '—' }}</p></div>
                    <div><p class="text-slate-500 text-xs">Year of Study</p><p class="font-medium text-slate-800">{{ $student->year ?? '—' }}</p></div>
                    <div><p class="text-slate-500 text-xs">Faculty</p><p class="font-medium text-slate-800">{{ $student->faculty ?? 'FSKTM (Faculty of Computer Science and Information Technology)' }}</p></div>
                    <div><p class="text-slate-500 text-xs">CGPA</p><p class="font-medium text-slate-800">{{ $student->cgpa ?? '—' }}</p></div>
                    <div><p class="text-slate-500 text-xs">Expected Graduation</p><p class="font-medium text-slate-800">{{ $student->expected_graduation ?? '—' }}</p></div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
                <h3 class="text-sm font-semibold text-slate-800 flex items-center gap-2 mb-4">
                    <i data-lucide="phone-call" class="w-4 h-4 text-violet-600"></i> Emergency Contact
                </h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                    <div><p class="text-slate-500 text-xs">Name</p><p class="font-medium text-slate-800">{{ $student->emergency_contact_name ?? '—' }}</p></div>
                    <div><p class="text-slate-500 text-xs">Phone</p><p class="font-medium text-slate-800">{{ $student->emergency_contact_phone ?? '—' }}</p></div>
                </div>
            </div>
        </div>

        {{-- RIGHT --}}
        <div class="space-y-6">

            <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
                <h3 class="text-sm font-semibold text-slate-800 flex items-center gap-2 mb-4">
                    <i data-lucide="layers" class="w-4 h-4 text-violet-600"></i> Talent Cluster
                </h3>
                <p class="text-sm text-slate-800 font-medium">
                    {{ $student->cluster?->cluster_name ?? 'Not assigned to a cluster yet' }}
                </p>
                @if($student->cluster)
                    <p class="text-xs text-slate-500 mt-1">{{ $student->cluster->description }}</p>
                @else
                    <p class="text-xs text-slate-400 mt-1">Ask the talent coordinator to assign you to a cluster based on your talent.</p>
                @endif
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
                <h3 class="text-sm font-semibold text-slate-800 flex items-center gap-2 mb-4">
                    <i data-lucide="bar-chart-2" class="w-4 h-4 text-violet-600"></i> Points by Category
                </h3>
                <div class="text-center mb-4">
                    <p class="text-3xl font-bold text-violet-600">{{ $totalScore }}</p>
                    <p class="text-xs text-slate-500">Total Merit Points</p>
                </div>
                <div class="space-y-3">
                    @forelse($scores as $s)
                        <div>
                            <div class="flex justify-between text-xs mb-1">
                                <span class="text-slate-600">{{ $s['name'] }}</span>
                                <span class="text-slate-800 font-medium">{{ $s['score'] }}</span>
                            </div>
                            <div class="w-full bg-slate-100 rounded-full h-1.5">
                                <div class="bg-violet-500 h-1.5 rounded-full" style="width: {{ $totalScore > 0 ? min(($s['score'] / $totalScore) * 100, 100) : 0 }}%"></div>
                            </div>
                        </div>
                    @empty
                        <p class="text-sm text-slate-400 text-center">No categories yet.</p>
                    @endforelse
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
                <h3 class="text-sm font-semibold text-slate-800 flex items-center gap-2 mb-4">
                    <i data-lucide="wrench" class="w-4 h-4 text-violet-600"></i> Skills
                </h3>
                @if($student->skills && count($student->skills) > 0)
                    <div class="flex flex-wrap gap-2">
                        @foreach($student->skills as $skill)
                            <span class="px-2.5 py-1 bg-violet-100 text-violet-700 text-xs font-medium rounded-full">{{ $skill }}</span>
                        @endforeach
                    </div>
                @else
                    <p class="text-sm text-slate-400">No skills added yet.</p>
                @endif
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
                <h3 class="text-sm font-semibold text-slate-800 flex items-center gap-2 mb-4">
                    <i data-lucide="heart" class="w-4 h-4 text-violet-600"></i> Interests
                </h3>
                @if($student->interests && count($student->interests) > 0)
                    <div class="flex flex-wrap gap-2">
                        @foreach($student->interests as $interest)
                            <span class="px-2.5 py-1 bg-blue-100 text-blue-700 text-xs font-medium rounded-full">{{ $interest }}</span>
                        @endforeach
                    </div>
                @else
                    <p class="text-sm text-slate-400">No interests added yet.</p>
                @endif
            </div>
        </div>
    </div>

    {{-- =================== RECENT ACTIVITY =================== --}}
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-sm font-semibold text-slate-800 flex items-center gap-2">
                <i data-lucide="history" class="w-4 h-4 text-violet-600"></i> Recent Activity
            </h3>
            <a href="{{ route('achievements.index') }}" class="text-xs text-violet-600 hover:text-violet-700 font-medium">View all</a>
        </div>
        <div class="space-y-0">
            @forelse($recentAchievements as $a)
            <div class="flex gap-4 py-3 border-l-2 border-slate-200 pl-4 relative">
                <div class="absolute -left-[9px] top-5 w-4 h-4 rounded-full border-2 border-white
                    {{ $a->status === 'approved' ? 'bg-emerald-500' : ($a->status === 'pending' ? 'bg-amber-500' : 'bg-red-500') }}"></div>
                <div class="flex-1">
                    <p class="text-sm font-medium text-slate-800">{{ $a->title }}</p>
                    <div class="flex items-center gap-3 mt-0.5 text-xs text-slate-500">
                        <span>{{ $a->created_at->format('d M Y') }}</span>
                        @if($a->score)<span class="text-emerald-600 font-medium">+{{ $a->score }} pts</span>@endif
                        <span class="px-2 py-0.5 rounded-full text-xs font-medium
                            {{ $a->status === 'approved' ? 'bg-emerald-100 text-emerald-700' : ($a->status === 'pending' ? 'bg-amber-100 text-amber-700' : 'bg-red-100 text-red-700') }}">
                            {{ ucfirst($a->status) }}
                        </span>
                    </div>
                </div>
            </div>
            @empty
            <p class="text-sm text-slate-400 text-center py-6">No achievements yet.</p>
            @endforelse
        </div>
    </div>

</div>
@endif
@endsection
