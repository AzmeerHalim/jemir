@extends('layouts.talentsys')

@section('title', 'Verification History')

@section('content')
<div class="max-w-6xl mx-auto space-y-6">
    <h2 class="text-xl font-bold text-slate-800">Verification History</h2>

    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
        <table class="w-full text-sm">
            <thead>
                <tr class="text-left text-slate-400 border-b border-slate-100 bg-slate-50">
                    <th class="py-3 px-4 font-medium">Student</th>
                    <th class="py-3 px-4 font-medium">Activity</th>
                    <th class="py-3 px-4 font-medium">Status</th>
                    <th class="py-3 px-4 font-medium">Reviewed</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($reviewed as $p)
                <tr class="hover:bg-slate-50">
                    <td class="py-3 px-4">{{ $p->student->user->name }}</td>
                    <td class="py-3 px-4">{{ $p->activity->activity_name }}</td>
                    <td class="py-3 px-4">
                        @if($p->lecturer_status === 'verified')
                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-700">Verified</span>
                        @else
                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-700">Rejected</span>
                        @endif
                    </td>
                    <td class="py-3 px-4 text-slate-500">{{ $p->verified_at?->format('d M Y H:i') ?? '—' }}</td>
                </tr>
                @empty
                <tr><td colspan="4" class="py-12 text-center text-slate-400">No history yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div>{{ $reviewed->links() }}</div>
</div>
@endsection
