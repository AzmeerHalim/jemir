@extends('layouts.talentsys')

@section('title', $activity->activity_name)

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

    <a href="{{ route('student.activities.index') }}" class="text-sm text-violet-600 hover:text-violet-700 flex items-center gap-1">
        <i data-lucide="arrow-left" class="w-4 h-4"></i> Back to Activities
    </a>

    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
        <div class="flex flex-wrap gap-2 mb-3">
            <span class="px-2 py-0.5 rounded-full text-xs font-medium bg-violet-100 text-violet-700">{{ ucfirst($activity->activity_level) }}</span>
            <span class="px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-700">{{ $activity->activity_type }}</span>
            @if($activity->is_open && !$deadlinePassed && !$isFull)
                <span class="px-2 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-700">Open</span>
            @else
                <span class="px-2 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-700">Closed</span>
            @endif
        </div>
        <h2 class="text-2xl font-bold text-slate-800 mb-2">{{ $activity->activity_name }}</h2>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm mt-4">
            <div>
                <p class="text-slate-500 text-xs">Date</p>
                <p class="font-medium text-slate-800">{{ $activity->activity_date->format('d M Y') }}</p>
            </div>
            <div>
                <p class="text-slate-500 text-xs">Organizer</p>
                <p class="font-medium text-slate-800">{{ $activity->organizer }}</p>
            </div>
            <div>
                <p class="text-slate-500 text-xs">Location</p>
                <p class="font-medium text-slate-800">{{ $activity->location ?? '—' }}</p>
            </div>
            <div>
                <p class="text-slate-500 text-xs">Registration Deadline</p>
                <p class="font-medium text-slate-800">
                    {{ $activity->registration_deadline?->format('d M Y') ?? 'No deadline' }}
                    @if($deadlinePassed)
                        <span class="text-red-500 text-xs block">(passed)</span>
                    @endif
                </p>
            </div>
            <div>
                <p class="text-slate-500 text-xs">Participants</p>
                <p class="font-medium text-slate-800">
                    {{ $participantsCount }} joined
                    @if($activity->max_participants)
                        <span class="text-slate-400">/ {{ $activity->max_participants }}</span>
                        @if($spotsRemaining !== null)
                            <span class="text-xs {{ $spotsRemaining > 0 ? 'text-emerald-600' : 'text-red-500' }} block">
                                {{ $spotsRemaining }} spot{{ $spotsRemaining !== 1 ? 's' : '' }} remaining
                            </span>
                        @endif
                    @else
                        <span class="text-xs text-slate-400 block">Unlimited</span>
                    @endif
                </p>
            </div>
        </div>
        @if($activity->description)
        <p class="mt-4 text-sm text-slate-600">{{ $activity->description }}</p>
        @endif

        <div class="mt-6 flex gap-3">
            @if($alreadyJoined)
                <span class="px-4 py-2 rounded-lg text-sm font-medium bg-emerald-100 text-emerald-700">You have joined this activity</span>
                <a href="{{ route('student.participations.index') }}" class="px-4 py-2 rounded-lg text-sm font-medium bg-slate-100 text-slate-700 hover:bg-slate-200">View My Participations</a>
            @elseif(!$activity->is_open)
                <span class="px-4 py-2 rounded-lg text-sm font-medium bg-slate-100 text-slate-500">Registration closed</span>
            @elseif($deadlinePassed)
                <span class="px-4 py-2 rounded-lg text-sm font-medium bg-red-50 text-red-600">Registration deadline has passed</span>
            @elseif($isFull)
                <span class="px-4 py-2 rounded-lg text-sm font-medium bg-red-50 text-red-600">Activity is full — no spots remaining</span>
            @else
                <a href="{{ route('student.participations.create', $activity) }}" class="px-4 py-2 gradient-primary text-white text-sm font-medium rounded-lg hover:scale-[1.02] transition shadow-md">Join Activity</a>
            @endif
        </div>
    </div>
</div>
@endsection
