@extends('layouts.talentsys')

@section('title', 'Participation Approvals')

@section('content')
<div class="max-w-7xl mx-auto space-y-6">

    <div class="flex justify-between items-center">
        <div>
            <h2 class="text-xl font-bold text-slate-800 flex items-center gap-2">
                <i data-lucide="clipboard-check" class="w-6 h-6 text-violet-600"></i>
                Participation Approvals
            </h2>
            <p class="text-xs text-slate-500 mt-1">Final approval queue — items already verified by the accompanying lecturer.</p>
        </div>
        <a href="{{ route('coordinator.participation-approvals.history') }}" class="text-sm text-violet-600 hover:text-violet-700 font-medium flex items-center gap-1">
            <i data-lucide="history" class="w-4 h-4"></i> View history
        </a>
    </div>

    <div class="grid grid-cols-2 gap-4">
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-4">
            <p class="text-xs text-slate-500">Awaiting your approval</p>
            <p class="text-xl font-bold text-amber-600">{{ $pendingCount }}</p>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-4">
        <form method="GET" class="flex flex-wrap items-center gap-3">
            <div class="flex-1 min-w-[220px]">
                <div class="relative">
                    <i data-lucide="search" class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400"></i>
                    <input type="text" name="search" value="{{ $search }}" placeholder="Search student or activity..."
                        class="w-full pl-9 pr-4 py-2 rounded-lg border border-slate-300 text-sm focus:ring-2 focus:ring-violet-500 focus:border-violet-500 outline-none">
                </div>
            </div>
            <button type="submit" class="px-4 py-2 gradient-primary text-white text-sm font-medium rounded-lg">Filter</button>
        </form>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-left text-slate-400 border-b border-slate-100 bg-slate-50">
                        <th class="py-3 px-4 font-medium">Student</th>
                        <th class="py-3 px-4 font-medium">Activity</th>
                        <th class="py-3 px-4 font-medium">Accompanying Lecturer</th>
                        <th class="py-3 px-4 font-medium">Verified by</th>
                        <th class="py-3 px-4 font-medium">Date</th>
                        <th class="py-3 px-4 font-medium">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($participations as $p)
                    <tr class="hover:bg-slate-50">
                        <td class="py-3 px-4">
                            <div class="flex items-center gap-2">
                                <div class="w-7 h-7 rounded-full bg-slate-200 flex items-center justify-center text-xs font-semibold text-slate-600">
                                    {{ strtoupper(substr($p->student->user->name, 0, 1)) }}
                                </div>
                                <div>
                                    <p class="font-medium text-slate-800">{{ $p->student->user->name }}</p>
                                    <p class="text-xs text-slate-400">{{ $p->student->matric_no }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="py-3 px-4">
                            <p class="font-medium text-slate-800">{{ $p->activity->activity_name }}</p>
                            <p class="text-xs text-slate-400">{{ $p->activity->activity_date->format('d M Y') }}</p>
                        </td>
                        <td class="py-3 px-4 text-slate-600">{{ $p->accompanyingLecturer?->lecturer_name ?? '—' }}</td>
                        <td class="py-3 px-4 text-slate-600">
                            <span class="px-2 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-700">
                                <i data-lucide="check" class="w-3 h-3 inline"></i>
                                {{ $p->lecturerReviewer?->name ?? '—' }}
                            </span>
                            <p class="text-xs text-slate-400 mt-1">{{ $p->lecturer_reviewed_at?->diffForHumans() }}</p>
                        </td>
                        <td class="py-3 px-4 text-slate-500">{{ $p->created_at->format('d M Y') }}</td>
                        <td class="py-3 px-4">
                            <a href="{{ route('coordinator.participation-approvals.show', $p) }}" class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-xs font-medium gradient-primary text-white">
                                Review
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="py-12 text-center text-slate-400">
                            <i data-lucide="inbox" class="w-10 h-10 mx-auto mb-2"></i>
                            <p>No participations awaiting your approval.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($participations->hasPages())
        <div class="px-4 py-3 border-t border-slate-100">
            {{ $participations->links() }}
        </div>
        @endif
    </div>

</div>
@endsection
