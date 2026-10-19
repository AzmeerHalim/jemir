@extends('layouts.talentsys')

@section('title', 'My Participations')

@section('content')
<div class="max-w-6xl mx-auto space-y-6">

    <h2 class="text-xl font-bold text-slate-800 flex items-center gap-2">
        <i data-lucide="clipboard-list" class="w-6 h-6 text-violet-600"></i>
        My Participations
    </h2>

    @if($participations->count() > 0)
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
        <table class="w-full text-sm">
            <thead>
                <tr class="text-left text-slate-400 border-b border-slate-100 bg-slate-50">
                    <th class="py-3 px-4 font-medium">Activity</th>
                    <th class="py-3 px-4 font-medium">Date</th>
                    <th class="py-3 px-4 font-medium">Role</th>
                    <th class="py-3 px-4 font-medium">Lecturer</th>
                    <th class="py-3 px-4 font-medium">Status</th>
                    <th class="py-3 px-4 font-medium">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @foreach($participations as $p)
                <tr class="hover:bg-slate-50">
                    <td class="py-3 px-4 font-medium text-slate-800">{{ $p->activity->activity_name }}</td>
                    <td class="py-3 px-4 text-slate-500">{{ $p->activity->activity_date->format('d M Y') }}</td>
                    <td class="py-3 px-4 text-slate-600">{{ $p->participation_role ?? '—' }}</td>
                    <td class="py-3 px-4 text-slate-600">{{ $p->accompanyingLecturer?->lecturer_name ?? '—' }}</td>
                    <td class="py-3 px-4">
                        @if($p->participation_status === 'verified')
                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-700"><i data-lucide="check" class="w-3 h-3"></i> Verified</span>
                        @elseif($p->participation_status === 'pending')
                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-700"><i data-lucide="clock" class="w-3 h-3"></i> Pending</span>
                        @else
                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-700"><i data-lucide="x" class="w-3 h-3"></i> Rejected</span>
                        @endif
                    </td>
                    <td class="py-3 px-4">
                        <div class="flex items-center gap-2">
                            <a href="{{ route('student.participations.show', $p) }}" class="text-xs text-violet-600 hover:text-violet-700 font-medium">View</a>
                            @if(in_array($p->participation_status, ['pending', 'rejected']))
                                <a href="{{ route('student.participations.edit', $p) }}" class="text-xs text-blue-600 hover:text-blue-700 font-medium">Upload Proof</a>
                            @endif
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @else
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-12 text-center text-slate-400">
        <i data-lucide="inbox" class="w-12 h-12 mx-auto mb-3"></i>
        <p>No participations yet.</p>
        <a href="{{ route('student.activities.index') }}" class="inline-block mt-3 text-sm text-violet-600 hover:text-violet-700 font-medium">Browse Activities</a>
    </div>
    @endif
</div>
@endsection
