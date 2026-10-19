@extends('layouts.talentsys')

@section('title', 'Students')

@section('content')
<div class="max-w-6xl mx-auto space-y-6">

    <h2 class="text-xl font-bold text-slate-800">Manage Students</h2>

    @if(session('success'))
        <div class="flex items-center gap-2 px-4 py-3 bg-emerald-50 border border-emerald-200 text-emerald-700 rounded-lg text-sm">
            <i data-lucide="check-circle" class="w-4 h-4"></i> {{ session('success') }}
        </div>
    @endif

    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
        @foreach($students as $s)
        <div class="flex justify-between items-center px-4 py-3 border-b border-slate-100 last:border-b-0 hover:bg-slate-50">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-full bg-slate-200 flex items-center justify-center text-sm font-semibold text-slate-600">
                    {{ strtoupper(substr($s->user->name, 0, 1)) }}
                </div>
                <div>
                    <p class="text-sm font-medium text-slate-800">{{ $s->user->name }}</p>
                    <p class="text-xs text-slate-400">{{ $s->course }} | Year {{ $s->year }}</p>
                </div>
            </div>
            <a href="{{ route('admin.students.edit', $s->id) }}"
                class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-xs font-medium bg-violet-100 text-violet-700 hover:bg-violet-200 transition">
                <i data-lucide="edit" class="w-3 h-3"></i> Edit
            </a>
        </div>
        @endforeach
    </div>

</div>
@endsection
