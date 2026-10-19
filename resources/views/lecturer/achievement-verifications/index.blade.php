@extends('layouts.talentsys')

@section('title', 'Pending Achievement Verifications')

@section('content')
<div class="max-w-6xl mx-auto space-y-6">
    <div class="flex justify-between items-center">
        <div>
            <h2 class="text-xl font-bold text-slate-800 flex items-center gap-2">
                <i data-lucide="trophy" class="w-6 h-6 text-violet-600"></i> Pending Achievement Verifications
            </h2>
            <p class="text-sm text-slate-500">Stage 1 of 2 — review self-submitted achievements before the talent coordinator's final approval.</p>
        </div>
        <a href="{{ route('lecturer.achievement-verifications.history') }}" class="text-sm text-violet-600 hover:text-violet-700 font-medium flex items-center gap-1">
            <i data-lucide="history" class="w-4 h-4"></i> View history
        </a>
    </div>

    @if($pending->count() > 0)
    <div class="grid grid-cols-1 gap-3">
        @foreach($pending as $a)
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-5 hover:shadow-md transition">
            <div class="flex justify-between items-start">
                <div class="flex-1">
                    <div class="flex items-center gap-2 mb-1">
                        <span class="px-2 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-700">Pending</span>
                        @if($a->category)
                            <span class="px-2 py-0.5 rounded-full text-xs font-medium bg-violet-100 text-violet-700">{{ $a->category->name }}</span>
                        @endif
                        <span class="text-xs text-slate-400">{{ $a->created_at->diffForHumans() }}</span>
                    </div>
                    <h3 class="text-sm font-semibold text-slate-800">{{ $a->title }}</h3>
                    <p class="text-xs text-slate-500 mt-1">
                        <i data-lucide="user" class="w-3 h-3 inline"></i>
                        {{ $a->student->user->name }} ({{ $a->student->matric_no }})
                        · <span class="capitalize">{{ $a->level }}</span>
                        @if($a->date_achieved)
                            · {{ $a->date_achieved->format('d M Y') }}
                        @endif
                    </p>
                    @if($a->accompanyingLecturer)
                        <p class="text-xs text-slate-500 mt-1">
                            <i data-lucide="user-check" class="w-3 h-3 inline"></i> Assigned to you as the accompanying lecturer.
                        </p>
                    @endif
                    @if($a->description)
                        <p class="text-xs text-slate-500 mt-1 line-clamp-2">{{ Str::limit($a->description, 160) }}</p>
                    @endif
                </div>
                <a href="{{ route('lecturer.achievement-verifications.show', $a) }}" class="px-3 py-1.5 rounded-lg text-xs font-medium gradient-primary text-white">Review</a>
            </div>
        </div>
        @endforeach
    </div>
    <div>{{ $pending->links() }}</div>
    @else
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-12 text-center text-slate-400">
        <i data-lucide="check-circle" class="w-12 h-12 mx-auto mb-3"></i>
        <p class="font-medium text-slate-600">All caught up!</p>
        <p class="text-sm">No pending self-submitted achievements.</p>
    </div>
    @endif
</div>
@endsection
