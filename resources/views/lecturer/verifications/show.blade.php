@extends('layouts.talentsys')

@section('title', 'Review Participation')

@section('content')
<div class="max-w-3xl mx-auto space-y-4">
    <a href="{{ route('lecturer.verifications.index') }}" class="text-sm text-violet-600 hover:text-violet-700 flex items-center gap-1">
        <i data-lucide="arrow-left" class="w-4 h-4"></i> Back
    </a>

    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
        <h2 class="text-lg font-bold text-slate-800 mb-1">{{ $participation->activity->activity_name }}</h2>
        <p class="text-sm text-slate-500">{{ $participation->activity->activity_date->format('d M Y') }} · {{ $participation->activity->location }}</p>

        <div class="grid grid-cols-2 gap-4 text-sm mt-4">
            <div><p class="text-slate-500">Student</p><p class="font-medium text-slate-800">{{ $participation->student->user->name }} ({{ $participation->student->matric_no }})</p></div>
            <div><p class="text-slate-500">Role</p><p class="font-medium text-slate-800">{{ $participation->participation_role ?? '—' }}</p></div>
            <div><p class="text-slate-500">Programme</p><p class="font-medium text-slate-800">{{ $participation->student->programme }}</p></div>
            <div><p class="text-slate-500">Year</p><p class="font-medium text-slate-800">{{ $participation->student->year }}</p></div>
        </div>

        <div class="mt-4 flex gap-3">
            @if($participation->certificate_path)
                <a href="{{ asset('storage/'.$participation->certificate_path) }}" target="_blank" class="inline-flex items-center gap-1 text-sm text-violet-600 hover:text-violet-700 font-medium">
                    <i data-lucide="file-text" class="w-4 h-4"></i> Certificate
                </a>
            @endif
            @if($participation->event_photo_path)
                <a href="{{ asset('storage/'.$participation->event_photo_path) }}" target="_blank" class="inline-flex items-center gap-1 text-sm text-violet-600 hover:text-violet-700 font-medium">
                    <i data-lucide="image" class="w-4 h-4"></i> Event Photo
                </a>
            @endif
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <form method="POST" action="{{ route('lecturer.verifications.verify', $participation) }}" class="bg-white rounded-xl shadow-sm border border-slate-200 p-5 space-y-3">
            @csrf
            <h3 class="text-sm font-semibold text-emerald-700">Verify Participation</h3>
            <div>
                <label class="block text-xs font-medium text-slate-700 mb-1">Notes (optional)</label>
                <textarea name="verification_notes" rows="2" class="w-full px-3 py-2 rounded-lg border border-slate-300 text-sm focus:ring-2 focus:ring-emerald-500 outline-none resize-none"></textarea>
            </div>
            <button type="submit" class="w-full px-4 py-2 bg-emerald-500 text-white text-sm font-medium rounded-lg hover:bg-emerald-600 transition">Verify</button>
        </form>

        <form method="POST" action="{{ route('lecturer.verifications.reject', $participation) }}" class="bg-white rounded-xl shadow-sm border border-slate-200 p-5 space-y-3">
            @csrf
            <h3 class="text-sm font-semibold text-red-700">Reject Participation</h3>
            <div>
                <label class="block text-xs font-medium text-slate-700 mb-1">Reason <span class="text-red-500">*</span></label>
                <textarea name="rejection_reason" rows="2" required class="w-full px-3 py-2 rounded-lg border border-slate-300 text-sm focus:ring-2 focus:ring-red-500 outline-none resize-none"></textarea>
            </div>
            <button type="submit" class="w-full px-4 py-2 bg-red-500 text-white text-sm font-medium rounded-lg hover:bg-red-600 transition">Reject</button>
        </form>
    </div>
</div>
@endsection
