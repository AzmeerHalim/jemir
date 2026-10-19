@extends('layouts.talentsys')

@section('title', $category->name . ' Leaderboard')

@section('content')
<div class="max-w-5xl mx-auto space-y-6">

    <div class="flex justify-between items-center">
        <h2 class="text-xl font-bold text-slate-800">{{ $category->name }} Leaderboard</h2>
        <form method="GET">
            <select name="programme" onchange="this.form.submit()"
                class="px-3 py-2 rounded-lg border border-slate-300 bg-white text-sm text-slate-700 focus:ring-2 focus:ring-violet-500 outline-none">
                <option value="">All Programmes</option>
                @foreach($programmes as $p)
                    <option value="{{ $p }}" {{ $programme == $p ? 'selected' : '' }}>{{ $p }}</option>
                @endforeach
            </select>
        </form>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-left text-slate-400 bg-slate-50 border-b border-slate-100">
                        <th class="py-3 px-4 font-medium">Rank</th>
                        <th class="py-3 px-4 font-medium">Student</th>
                        <th class="py-3 px-4 font-medium">Programme</th>
                        <th class="py-3 px-4 font-medium text-right">Points</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($students as $index => $s)
                    <tr class="hover:bg-slate-50">
                        <td class="py-3 px-4 font-bold text-slate-400">#{{ $index + 1 }}</td>
                        <td class="py-3 px-4">
                            <div class="flex items-center gap-2">
                                <div class="w-7 h-7 rounded-full bg-slate-200 flex items-center justify-center text-xs font-semibold text-slate-600">
                                    {{ strtoupper(substr($s->user->name, 0, 1)) }}
                                </div>
                                <span class="font-medium text-slate-800">{{ $s->user->name }}</span>
                            </div>
                        </td>
                        <td class="py-3 px-4 text-slate-600">{{ $s->course }}</td>
                        <td class="py-3 px-4 text-right font-semibold text-violet-600">{{ $s->score }} pts</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
