@extends('layouts.talentsys')

@section('title', 'Announcements')

@section('content')
<div class="max-w-3xl mx-auto space-y-4">
    <h2 class="text-xl font-bold text-slate-800 flex items-center gap-2">
        <i data-lucide="megaphone" class="w-6 h-6 text-violet-600"></i>
        Announcements
    </h2>

    @if($announcements->count() > 0)
        <div class="space-y-3">
            @foreach($announcements as $a)
            <a href="{{ route('student.announcements.show', $a) }}" class="block bg-white rounded-xl shadow-sm border border-slate-200 p-5 hover:shadow-md transition">
                <h3 class="text-sm font-semibold text-slate-800">{{ $a->announcement_title }}</h3>
                <p class="text-xs text-slate-500 mt-1">{{ $a->announcement_date->format('d M Y') }} · by {{ $a->author->name ?? 'Admin' }}</p>
                <p class="text-sm text-slate-600 mt-2 line-clamp-2">{{ Str::limit($a->announcement_message, 150) }}</p>
            </a>
            @endforeach
        </div>
        <div>{{ $announcements->links() }}</div>
    @else
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-12 text-center text-slate-400">
            <i data-lucide="megaphone-off" class="w-12 h-12 mx-auto mb-3"></i>
            <p>No announcements yet.</p>
        </div>
    @endif
</div>
@endsection
