@extends('layouts.talentsys')

@section('title', 'Participation Details')

@section('content')
<div class="max-w-3xl mx-auto space-y-4">
    <a href="{{ route('student.participations.index') }}" class="text-sm text-violet-600 hover:text-violet-700 flex items-center gap-1">
        <i data-lucide="arrow-left" class="w-4 h-4"></i> Back
    </a>

    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
        <h2 class="text-lg font-bold text-slate-800 mb-2">{{ $participation->activity->activity_name }}</h2>
        <p class="text-sm text-slate-500 mb-4">{{ $participation->activity->activity_date->format('d M Y') }} · {{ $participation->activity->location }}</p>

        <div class="grid grid-cols-2 gap-4 text-sm">
            <div><p class="text-slate-500">Role</p><p class="font-medium text-slate-800">{{ $participation->participation_role ?? '—' }}</p></div>
            <div><p class="text-slate-500">Lecturer</p><p class="font-medium text-slate-800">{{ $participation->accompanyingLecturer?->lecturer_name ?? '—' }}</p></div>
            <div><p class="text-slate-500">Status</p>
                <p class="font-medium">
                    @if($participation->participation_status === 'verified') <span class="text-emerald-600">Verified</span>
                    @elseif($participation->participation_status === 'pending') <span class="text-amber-600">Pending</span>
                    @else <span class="text-red-600">Rejected</span>
                    @endif
                </p>
            </div>
            <div><p class="text-slate-500">Verified At</p><p class="font-medium text-slate-800">{{ $participation->verified_at?->format('d M Y H:i') ?? '—' }}</p></div>
        </div>

        @if($participation->verification_notes)
        <div class="mt-4 p-3 bg-slate-50 rounded-lg text-sm">
            <p class="text-xs text-slate-500 mb-1">Verification Notes</p>
            <p class="text-slate-800">{{ $participation->verification_notes }}</p>
        </div>
        @endif

        @if($participation->rejection_reason)
        <div class="mt-4 p-3 bg-red-50 rounded-lg text-sm">
            <p class="text-xs text-red-500 mb-1">Rejection Reason</p>
            <p class="text-red-700">{{ $participation->rejection_reason }}</p>
        </div>
        @endif

        <div class="mt-4 flex gap-3">
            @if(in_array($participation->participation_status, ['pending', 'rejected']))
                <a href="{{ route('student.participations.edit', $participation) }}" class="px-4 py-2 rounded-lg text-sm font-medium bg-blue-50 text-blue-700 hover:bg-blue-100 flex items-center gap-1">
                    <i data-lucide="upload" class="w-4 h-4"></i> Upload Proof
                </a>
            @endif
            @if($participation->certificate_path)
                <a href="{{ asset('storage/'.$participation->certificate_path) }}" target="_blank" class="text-sm text-violet-600 hover:text-violet-700 font-medium flex items-center gap-1">
                    <i data-lucide="file-text" class="w-4 h-4"></i> View Certificate
                </a>
            @endif
            @if($participation->event_photo_path)
                <a href="{{ asset('storage/'.$participation->event_photo_path) }}" target="_blank" class="text-sm text-violet-600 hover:text-violet-700 font-medium flex items-center gap-1">
                    <i data-lucide="image" class="w-4 h-4"></i> View Event Photo
                </a>
            @endif
        </div>
    </div>
</div>
@endsection
