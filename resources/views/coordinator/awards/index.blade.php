@extends('layouts.talentsys')

@section('title', 'Awards')

@section('content')
<div class="max-w-6xl mx-auto space-y-6">

    <div class="flex justify-between items-center">
        <h2 class="text-xl font-bold text-slate-800">Awards &amp; Recommendations</h2>
        <div class="flex gap-2">
            <a href="{{ route('coordinator.awards.top') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg border border-slate-300 text-slate-700 text-sm font-medium hover:bg-slate-50">
                <i data-lucide="star" class="w-4 h-4"></i> Top Performers
            </a>
            <a href="{{ route('coordinator.awards.create') }}" class="inline-flex items-center gap-2 px-4 py-2 gradient-primary text-white text-sm font-medium rounded-lg hover:scale-[1.02] transition shadow-md">
                <i data-lucide="plus" class="w-4 h-4"></i> Record Award
            </a>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
        <table class="w-full text-sm">
            <thead>
                <tr class="text-left text-slate-400 border-b border-slate-100 bg-slate-50">
                    <th class="py-3 px-4 font-medium">Student</th>
                    <th class="py-3 px-4 font-medium">Award</th>
                    <th class="py-3 px-4 font-medium">Category</th>
                    <th class="py-3 px-4 font-medium">Date</th>
                    <th class="py-3 px-4 font-medium">Recommended By</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($awards as $a)
                <tr class="hover:bg-slate-50">
                    <td class="py-3 px-4 font-medium text-slate-800">{{ $a->student->user->name ?? '—' }}</td>
                    <td class="py-3 px-4">{{ $a->award_name }}</td>
                    <td class="py-3 px-4"><span class="px-2 py-0.5 rounded-full text-xs font-medium bg-violet-100 text-violet-700">{{ $a->award_category ?? '—' }}</span></td>
                    <td class="py-3 px-4 text-slate-500">{{ $a->award_date->format('d M Y') }}</td>
                    <td class="py-3 px-4 text-slate-500">{{ $a->recommender->name ?? '—' }}</td>
                </tr>
                @empty
                <tr><td colspan="5" class="py-12 text-center text-slate-400">No awards recorded yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div>{{ $awards->links() }}</div>
</div>
@endsection
