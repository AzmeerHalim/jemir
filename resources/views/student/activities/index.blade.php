@extends('layouts.talentsys')

@section('title', 'Activities')

@section('content')
<div class="max-w-6xl mx-auto space-y-6">

    <div class="flex justify-between items-center">
        <div>
            <h2 class="text-xl font-bold text-slate-800 flex items-center gap-2">
                <i data-lucide="calendar" class="w-6 h-6 text-violet-600"></i>
                Activities &amp; Events
            </h2>
            <p class="text-sm text-slate-500">Browse and join upcoming activities</p>
        </div>
        <form method="GET" class="flex items-center gap-2">
            <select name="level" onchange="this.form.submit()"
                class="px-3 py-2 rounded-lg border border-slate-300 bg-white text-sm focus:ring-2 focus:ring-violet-500 outline-none">
                <option value="">All Levels</option>
                @foreach(['faculty','university','state','national','international'] as $lvl)
                    <option value="{{ $lvl }}" {{ request('level') === $lvl ? 'selected' : '' }}>{{ ucfirst($lvl) }}</option>
                @endforeach
            </select>
        </form>
    </div>

    @if($activities->count() > 0)
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        @foreach($activities as $a)
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-5 hover:shadow-md transition">
            <div class="flex items-center gap-2 mb-2">
                <span class="px-2 py-0.5 rounded-full text-xs font-medium bg-violet-100 text-violet-700">{{ ucfirst($a->activity_level) }}</span>
                <span class="px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-700">{{ $a->activity_type }}</span>
                @if($a->isFull())
                    <span class="px-2 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-700">Full</span>
                @endif
            </div>
            <h3 class="text-sm font-semibold text-slate-800 mb-2 line-clamp-2">{{ $a->activity_name }}</h3>
            <div class="space-y-1 mb-3 text-xs text-slate-500">
                <p><i data-lucide="calendar" class="w-3 h-3 inline mr-1"></i> {{ $a->activity_date->format('d M Y') }}</p>
                @if($a->registration_deadline)
                <p><i data-lucide="clock" class="w-3 h-3 inline mr-1"></i> Deadline: {{ $a->registration_deadline->format('d M Y') }}</p>
                @endif
                <p><i data-lucide="map-pin" class="w-3 h-3 inline mr-1"></i> {{ $a->location ?? '—' }}</p>
                <p><i data-lucide="users" class="w-3 h-3 inline mr-1"></i>
                    {{ $a->participations_count }} joined
                    @if($a->max_participants)
                        / {{ $a->max_participants }} max
                    @endif
                </p>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('student.activities.show', $a) }}" class="flex-1 text-center px-3 py-1.5 rounded-lg text-xs font-medium bg-slate-100 text-slate-700 hover:bg-slate-200">Details</a>
                @if(in_array($a->id, $joined))
                    <span class="flex-1 text-center px-3 py-1.5 rounded-lg text-xs font-medium bg-emerald-100 text-emerald-700">Joined</span>
                @elseif($a->isFull())
                    <span class="flex-1 text-center px-3 py-1.5 rounded-lg text-xs font-medium bg-red-100 text-red-600 cursor-not-allowed">Full</span>
                @else
                    <a href="{{ route('student.participations.create', $a) }}" class="flex-1 text-center px-3 py-1.5 rounded-lg text-xs font-medium gradient-primary text-white">Join</a>
                @endif
            </div>
        </div>
        @endforeach
    </div>
    <div>{{ $activities->links() }}</div>
    @else
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-12 text-center text-slate-400">
        <i data-lucide="calendar-x" class="w-12 h-12 mx-auto mb-3"></i>
        <p>No upcoming activities at the moment.</p>
    </div>
    @endif
</div>
@endsection
