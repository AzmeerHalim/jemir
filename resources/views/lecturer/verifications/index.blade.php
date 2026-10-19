@extends('layouts.talentsys')

@section('title', 'Pending Verifications')

@section('content')
<div class="max-w-6xl mx-auto space-y-6">
    <h2 class="text-xl font-bold text-slate-800 flex items-center gap-2">
        <i data-lucide="clipboard-check" class="w-6 h-6 text-violet-600"></i>
        Pending Verifications
    </h2>

    @if($pending->count() > 0)
    <div class="grid grid-cols-1 gap-3">
        @foreach($pending as $p)
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-5 hover:shadow-md transition">
            <div class="flex justify-between items-start">
                <div class="flex-1">
                    <div class="flex items-center gap-2 mb-1">
                        <span class="px-2 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-700">Pending</span>
                        <span class="text-xs text-slate-400">{{ $p->created_at->diffForHumans() }}</span>
                    </div>
                    <h3 class="text-sm font-semibold text-slate-800">{{ $p->activity->activity_name }}</h3>
                    <p class="text-xs text-slate-500 mt-1">
                        <i data-lucide="user" class="w-3 h-3 inline"></i>
                        {{ $p->student->user->name }} ({{ $p->student->matric_no }})
                        · <i data-lucide="calendar" class="w-3 h-3 inline"></i>
                        {{ $p->activity->activity_date->format('d M Y') }}
                    </p>
                    @if($p->participation_role)
                    <p class="text-xs text-slate-500 mt-1">Role: {{ $p->participation_role }}</p>
                    @endif
                </div>
                <a href="{{ route('lecturer.verifications.show', $p) }}" class="px-3 py-1.5 rounded-lg text-xs font-medium gradient-primary text-white">Review</a>
            </div>
        </div>
        @endforeach
    </div>
    <div>{{ $pending->links() }}</div>
    @else
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-12 text-center text-slate-400">
        <i data-lucide="check-circle" class="w-12 h-12 mx-auto mb-3"></i>
        <p class="font-medium text-slate-600">All caught up!</p>
        <p class="text-sm">No pending verifications.</p>
    </div>
    @endif
</div>
@endsection
