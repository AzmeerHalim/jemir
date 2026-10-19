@extends('layouts.talentsys')

@section('title', 'My Achievements')

@section('content')
@php
    $statusTotal = ($statusCounts['pending'] ?? 0) + ($statusCounts['approved'] ?? 0) + ($statusCounts['rejected'] ?? 0);
    $approvedTotal = $statusCounts['approved'] ?? 0;
    $totalPoints = $achievements->where('status', 'approved')->sum('score');
@endphp
<div class="max-w-6xl mx-auto space-y-6">

    <div class="flex flex-col md:flex-row md:justify-between md:items-center gap-4">
        <div>
            <h2 class="text-xl font-bold text-slate-800 flex items-center gap-2">
                <i data-lucide="trophy" class="w-6 h-6 text-violet-600"></i>
                My Achievements
            </h2>
            <p class="text-sm text-slate-500">Your unified list of verified participations and self-submitted achievements.</p>
        </div>
        <a href="{{ route('achievements.create') }}"
            class="inline-flex items-center gap-2 px-4 py-2.5 gradient-primary text-white font-medium rounded-lg hover:scale-[1.02] transition shadow-md text-sm">
            <i data-lucide="plus" class="w-4 h-4"></i> Add Achievement
        </a>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-4 flex items-center gap-3">
            <div class="w-10 h-10 rounded-lg gradient-primary flex items-center justify-center text-white shadow">
                <i data-lucide="trophy" class="w-5 h-5"></i>
            </div>
            <div>
                <p class="text-2xl font-bold text-slate-800">{{ $totalPoints }}</p>
                <p class="text-xs text-slate-500">Total Points</p>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-4">
            <p class="text-xs text-slate-500">Approved</p>
            <p class="text-2xl font-bold text-emerald-600">{{ $approvedTotal }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-4">
            <p class="text-xs text-slate-500">Pending</p>
            <p class="text-2xl font-bold text-amber-600">{{ $statusCounts['pending'] ?? 0 }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-4">
            <p class="text-xs text-slate-500">Rejected</p>
            <p class="text-2xl font-bold text-red-600">{{ $statusCounts['rejected'] ?? 0 }}</p>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between">
            <h3 class="text-sm font-semibold text-slate-800 flex items-center gap-2">
                <i data-lucide="trophy" class="w-4 h-4 text-violet-600"></i> All Achievements
                <span class="px-2 py-0.5 rounded-full text-xs font-medium bg-violet-100 text-violet-700">{{ $achievements->count() }}</span>
            </h3>
        </div>

        <div class="px-5 py-3 border-b border-slate-100 bg-slate-50 flex flex-wrap items-center gap-3">
            @php
                $statusTabs = [
                    'all' => 'All',
                    'pending' => 'Pending',
                    'approved' => 'Approved',
                    'rejected' => 'Rejected',
                ];
            @endphp
            @foreach($statusTabs as $key => $label)
                <a href="{{ route('achievements.index', array_filter(['status' => $key === 'all' ? null : $key, 'source' => $currentSource === 'all' ? null : $currentSource, 'search' => $search])) }}"
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-medium transition
                        {{ $currentStatus === $key ? 'gradient-primary text-white shadow' : 'bg-white border border-slate-200 text-slate-600 hover:bg-slate-100' }}">
                    {{ $label }}
                    <span class="px-1.5 rounded-full text-[10px] {{ $currentStatus === $key ? 'bg-white/20' : 'bg-slate-100' }}">{{ $key === 'all' ? $statusTotal : ($statusCounts[$key] ?? 0) }}</span>
                </a>
            @endforeach

            <span class="hidden md:inline text-slate-300">|</span>

            @php
                $sourceTabs = [
                    'all' => 'All sources',
                    'participation' => 'Participation',
                    'self' => 'Self-submitted',
                ];
            @endphp
            @foreach($sourceTabs as $key => $label)
                <a href="{{ route('achievements.index', array_filter(['status' => $currentStatus === 'all' ? null : $currentStatus, 'source' => $key === 'all' ? null : $key, 'search' => $search])) }}"
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-medium transition
                        {{ $currentSource === $key ? 'bg-violet-600 text-white shadow' : 'bg-white border border-slate-200 text-slate-600 hover:bg-slate-100' }}">
                    {{ $label }}
                    <span class="px-1.5 rounded-full text-[10px] {{ $currentSource === $key ? 'bg-white/20' : 'bg-slate-100' }}">{{ $key === 'all' ? $statusTotal : ($sourceCounts[$key] ?? 0) }}</span>
                </a>
            @endforeach

            <form method="GET" class="flex-1 min-w-[180px]">
                @if($currentStatus !== 'all')<input type="hidden" name="status" value="{{ $currentStatus }}">@endif
                @if($currentSource !== 'all')<input type="hidden" name="source" value="{{ $currentSource }}">@endif
                <div class="relative">
                    <i data-lucide="search" class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400"></i>
                    <input type="text" name="search" value="{{ $search }}" placeholder="Search title..."
                        class="w-full pl-9 pr-4 py-1.5 rounded-lg border border-slate-300 text-xs focus:ring-2 focus:ring-violet-500 focus:border-violet-500 outline-none">
                </div>
            </form>
        </div>

        @if($achievements->count() > 0)
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-left text-slate-400 border-b border-slate-100 bg-slate-50">
                        <th class="py-3 px-4 font-medium">Achievement</th>
                        <th class="py-3 px-4 font-medium">Category</th>
                        <th class="py-3 px-4 font-medium">Date</th>
                        <th class="py-3 px-4 font-medium">Source</th>
                        <th class="py-3 px-4 font-medium">Status</th>
                        <th class="py-3 px-4 font-medium">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($achievements as $a)
                    <tr class="hover:bg-slate-50">
                        <td class="py-3 px-4">
                            <p class="font-medium text-slate-800">{{ $a->title }}</p>
                            <p class="text-xs text-slate-400">{{ ucfirst($a->level ?? '—') }}@if($a->event_name) · {{ $a->event_name }}@endif</p>
                        </td>
                        <td class="py-3 px-4">
                            @if($a->category)
                                <span class="px-2 py-0.5 rounded-full text-xs font-medium bg-slate-100 text-slate-700">{{ $a->category->name }}</span>
                            @else
                                <span class="text-xs text-slate-400">—</span>
                            @endif
                        </td>
                        <td class="py-3 px-4 text-slate-500">
                            {{ $a->date_achieved?->format('d M Y') ?? '—' }}
                            @if($a->status === 'approved')
                                <p class="text-xs font-bold text-emerald-600">+{{ $a->score }} pts</p>
                            @endif
                        </td>
                        <td class="py-3 px-4">
                            @if($a->participation_id)
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-700">
                                    <i data-lucide="clipboard-check" class="w-3 h-3"></i> Participation
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium bg-violet-100 text-violet-700">
                                    <i data-lucide="pencil" class="w-3 h-3"></i> Self-submitted
                                </span>
                            @endif
                        </td>
                        <td class="py-3 px-4">
                            @if($a->status === 'approved')
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-700"><i data-lucide="check" class="w-3 h-3"></i> Approved</span>
                            @elseif($a->status === 'pending')
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-700"><i data-lucide="clock" class="w-3 h-3"></i> Pending</span>
                            @else
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-700"><i data-lucide="x" class="w-3 h-3"></i> Rejected</span>
                            @endif
                        </td>
                        <td class="py-3 px-4">
                            <a href="{{ route('achievements.show', $a) }}" class="text-xs text-violet-600 hover:text-violet-700 font-medium">View</a>
                            @if($a->status === 'pending' && ! $a->participation_id)
                                <a href="{{ route('achievements.edit', $a) }}" class="ml-2 text-xs text-blue-600 hover:text-blue-700 font-medium">Edit</a>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="text-center py-16 text-slate-400">
            <i data-lucide="trophy" class="w-12 h-12 mx-auto mb-3"></i>
            <p class="font-medium text-slate-600">No achievements to show</p>
            <p class="text-sm mt-1">
                @if($search)
                    No results for "<span class="font-semibold text-slate-600">{{ $search }}</span>".
                @elseif($currentStatus !== 'all' || $currentSource !== 'all')
                    No achievements match the current filters.
                @else
                    Join an activity or submit your own achievement to get started.
                @endif
            </p>
            <a href="{{ route('achievements.create') }}"
                class="inline-flex items-center gap-2 mt-4 px-4 py-2 gradient-primary text-white font-medium rounded-lg text-sm hover:scale-[1.02] transition shadow-md">
                <i data-lucide="plus" class="w-4 h-4"></i> Add Achievement
            </a>
        </div>
        @endif
    </div>

</div>
@endsection
