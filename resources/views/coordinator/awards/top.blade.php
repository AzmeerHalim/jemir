@extends('layouts.talentsys')

@section('title', 'Top Performers')

@section('content')
<div class="max-w-5xl mx-auto space-y-6">
    <h2 class="text-xl font-bold text-slate-800 flex items-center gap-2">
        <i data-lucide="star" class="w-6 h-6 text-amber-500"></i>
        Top Performers
    </h2>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        @foreach($top as $index => $s)
        <div class="bg-white rounded-xl shadow-sm border p-5 {{ $index === 0 ? 'border-amber-400 bg-amber-50/40' : 'border-slate-200' }}">
            <div class="flex items-center justify-between">
                <span class="text-2xl">{{ $index === 0 ? '🥇' : ($index === 1 ? '🥈' : '🥉') }}</span>
                <span class="text-xs text-slate-500">#{{ $index + 1 }}</span>
            </div>
            <div class="mt-3 flex items-center gap-3">
                <div class="w-12 h-12 rounded-full gradient-primary flex items-center justify-center text-white text-lg font-bold">
                    {{ strtoupper(substr($s->user->name, 0, 1)) }}
                </div>
                <div>
                    <p class="font-semibold text-slate-800">{{ $s->user->name }}</p>
                    <p class="text-xs text-slate-500">{{ $s->programme }} · Year {{ $s->year }}</p>
                </div>
            </div>
            <p class="text-2xl font-bold text-violet-600 mt-3">{{ $s->getTotalScore() }} pts</p>
            <form method="POST" action="{{ route('coordinator.awards.store') }}" class="mt-3">
                @csrf
                <input type="hidden" name="student_id" value="{{ $s->id }}">
                <input type="hidden" name="award_name" value="Top Performer Recognition">
                <input type="hidden" name="award_category" value="Excellence">
                <input type="hidden" name="award_date" value="{{ date('Y-m-d') }}">
                <button type="submit" class="w-full px-3 py-1.5 rounded-lg text-xs font-medium gradient-primary text-white hover:scale-[1.02] transition">Award Now</button>
            </form>
        </div>
        @endforeach
    </div>
</div>
@endsection
