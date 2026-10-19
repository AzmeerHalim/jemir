@extends('layouts.talentsys')

@section('title', $announcement->announcement_title)

@section('content')
<div class="max-w-3xl mx-auto space-y-4">
    <a href="{{ route('student.announcements.index') }}" class="text-sm text-violet-600 hover:text-violet-700 flex items-center gap-1">
        <i data-lucide="arrow-left" class="w-4 h-4"></i> Back
    </a>
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
        <h2 class="text-xl font-bold text-slate-800">{{ $announcement->announcement_title }}</h2>
        <p class="text-xs text-slate-500 mt-1">{{ $announcement->announcement_date->format('d M Y') }} · {{ $announcement->author->name ?? 'Admin' }}</p>
        <p class="mt-4 text-sm text-slate-700 whitespace-pre-line">{{ $announcement->announcement_message }}</p>
    </div>
</div>
@endsection
