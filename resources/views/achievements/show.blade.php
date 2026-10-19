@extends('layouts.talentsys')

@section('title', $achievement->title)

@section('content')
<div class="max-w-4xl mx-auto space-y-4">
    <a href="{{ route('achievements.index') }}" class="text-sm text-violet-600 hover:text-violet-700 flex items-center gap-1">
        <i data-lucide="arrow-left" class="w-4 h-4"></i> Back to My Achievements
    </a>

    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
        <div class="flex flex-wrap gap-2 mb-3">
            @if($achievement->participation_id)
                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-700">
                    <i data-lucide="clipboard-check" class="w-3 h-3"></i> Participation
                </span>
            @else
                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-medium bg-violet-100 text-violet-700">
                    <i data-lucide="pencil" class="w-3 h-3"></i> Self-submitted
                </span>
            @endif
            <span class="px-2.5 py-0.5 rounded-full text-xs font-medium bg-slate-100 text-slate-700">{{ ucfirst($achievement->level) }}</span>
            @if($achievement->category)
                <span class="px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-700">{{ $achievement->category->name }}</span>
            @endif
            @if($achievement->status === 'approved')
                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-700"><i data-lucide="check" class="w-3 h-3"></i> Approved</span>
            @elseif($achievement->status === 'pending')
                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-700"><i data-lucide="clock" class="w-3 h-3"></i> Pending</span>
            @else
                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-700"><i data-lucide="x" class="w-3 h-3"></i> Rejected</span>
            @endif
        </div>
        <h2 class="text-2xl font-bold text-slate-800 mb-2">{{ $achievement->title }}</h2>
        @if($achievement->event_name)
            <p class="text-sm text-slate-500">{{ $achievement->event_name }}</p>
        @endif

        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm mt-4">
            <div>
                <p class="text-slate-500 text-xs">Date Achieved</p>
                <p class="font-medium text-slate-800">{{ $achievement->date_achieved?->format('d M Y') ?? '—' }}</p>
            </div>
            <div>
                <p class="text-slate-500 text-xs">Organizer</p>
                <p class="font-medium text-slate-800">{{ $achievement->organizer ?? '—' }}</p>
            </div>
            <div>
                <p class="text-slate-500 text-xs">Location</p>
                <p class="font-medium text-slate-800">{{ $achievement->location ?? '—' }}</p>
            </div>
            <div>
                <p class="text-slate-500 text-xs">Accompanying Lecturer</p>
                <p class="font-medium text-slate-800">{{ $achievement->accompanyingLecturer?->lecturer_name ?? '—' }}</p>
            </div>
            @if($achievement->status === 'approved')
                <div>
                    <p class="text-slate-500 text-xs">Points Awarded</p>
                    <p class="font-bold text-emerald-600">+{{ $achievement->score }} pts</p>
                </div>
            @else
                <div>
                    <p class="text-slate-500 text-xs">Reviewed At</p>
                    <p class="font-medium text-slate-800">{{ $achievement->reviewed_at?->format('d M Y H:i') ?? '—' }}</p>
                </div>
            @endif
        </div>

        @if($achievement->description)
        <p class="mt-4 text-sm text-slate-600">{{ $achievement->description }}</p>
        @endif

        <div class="mt-6 flex flex-wrap gap-3">
            @if($achievement->certificate)
                <a href="{{ asset('storage/'.$achievement->certificate) }}" target="_blank" class="inline-flex items-center gap-1 text-sm text-violet-600 hover:text-violet-700 font-medium">
                    <i data-lucide="file-text" class="w-4 h-4"></i> View Certificate
                </a>
            @endif
            @if($achievement->event_photo_path)
                <a href="{{ asset('storage/'.$achievement->event_photo_path) }}" target="_blank" class="inline-flex items-center gap-1 text-sm text-violet-600 hover:text-violet-700 font-medium">
                    <i data-lucide="image" class="w-4 h-4"></i> View Event Photo
                </a>
            @endif
            @if($achievement->participation_id)
                <a href="{{ route('student.participations.show', $achievement->participation_id) }}" class="inline-flex items-center gap-1 text-sm text-violet-600 hover:text-violet-700 font-medium">
                    <i data-lucide="external-link" class="w-4 h-4"></i> View Linked Participation
                </a>
            @endif
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-5">
            <h3 class="text-sm font-semibold text-violet-700 flex items-center gap-2 mb-3">
                <i data-lucide="user-check" class="w-4 h-4"></i> Lecturer Review
            </h3>
            @if($achievement->lecturerReviewer)
                <div class="grid grid-cols-2 gap-3 text-sm">
                    <div><p class="text-slate-500 text-xs">Reviewed by</p><p class="font-medium text-slate-800">{{ $achievement->lecturerReviewer->name }}</p></div>
                    <div><p class="text-slate-500 text-xs">Reviewed at</p><p class="font-medium text-slate-800">{{ $achievement->lecturer_reviewed_at?->format('d M Y H:i') ?? '—' }}</p></div>
                </div>
                @if($achievement->lecturer_notes)
                    <p class="text-xs text-slate-500 mt-2">Notes</p>
                    <p class="text-sm text-slate-700 mt-1 whitespace-pre-line">{{ $achievement->lecturer_notes }}</p>
                @endif
            @else
                <p class="text-sm text-slate-500">Awaiting lecturer review.</p>
            @endif
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-5">
            <h3 class="text-sm font-semibold text-emerald-700 flex items-center gap-2 mb-3">
                <i data-lucide="badge-check" class="w-4 h-4"></i> Coordinator Final Approval
            </h3>
            @if($achievement->coordinatorReviewer)
                <div class="grid grid-cols-2 gap-3 text-sm">
                    <div><p class="text-slate-500 text-xs">Reviewed by</p><p class="font-medium text-slate-800">{{ $achievement->coordinatorReviewer->name }}</p></div>
                    <div><p class="text-slate-500 text-xs">Reviewed at</p><p class="font-medium text-slate-800">{{ $achievement->coordinator_reviewed_at?->format('d M Y H:i') ?? '—' }}</p></div>
                </div>
                @if($achievement->coordinator_notes)
                    <p class="text-xs text-slate-500 mt-2">Notes</p>
                    <p class="text-sm text-slate-700 mt-1 whitespace-pre-line">{{ $achievement->coordinator_notes }}</p>
                @endif
            @else
                <p class="text-sm text-slate-500">Awaiting coordinator final approval.</p>
            @endif
        </div>
    </div>

    @if($achievement->status === 'pending' && ! $achievement->participation_id)
    <div class="flex justify-end gap-3">
        <a href="{{ route('achievements.edit', $achievement) }}" class="px-5 py-2.5 rounded-lg border border-slate-300 text-slate-700 text-sm font-medium hover:bg-slate-50">Edit</a>
        <form action="{{ route('achievements.destroy', $achievement) }}" method="POST" onsubmit="return confirm('Delete this achievement?')">
            @csrf @method('DELETE')
            <button class="px-5 py-2.5 bg-red-500 text-white text-sm font-medium rounded-lg hover:bg-red-600 transition">Delete</button>
        </form>
    </div>
    @endif
</div>
@endsection
