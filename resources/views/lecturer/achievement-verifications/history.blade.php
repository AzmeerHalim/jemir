@extends('layouts.talentsys')

@section('title', 'Achievement Verification History')

@section('content')
<div class="max-w-6xl mx-auto space-y-6">
    <div class="flex justify-between items-center">
        <div>
            <h2 class="text-xl font-bold text-slate-800 flex items-center gap-2">
                <i data-lucide="history" class="w-6 h-6 text-violet-600"></i> Achievement Verification History
            </h2>
        </div>
        <a href="{{ route('lecturer.achievement-verifications.index') }}" class="text-sm text-violet-600 hover:text-violet-700 font-medium flex items-center gap-1">
            <i data-lucide="arrow-left" class="w-4 h-4"></i> Back
        </a>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-left text-slate-400 border-b border-slate-100 bg-slate-50">
                        <th class="py-3 px-4 font-medium">Achievement</th>
                        <th class="py-3 px-4 font-medium">Student</th>
                        <th class="py-3 px-4 font-medium">Verified By</th>
                        <th class="py-3 px-4 font-medium">Decision</th>
                        <th class="py-3 px-4 font-medium">Date</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($reviewed as $a)
                    <tr class="hover:bg-slate-50">
                        <td class="py-3 px-4">
                            <p class="font-medium text-slate-800">{{ $a->title }}</p>
                            <p class="text-xs text-slate-400">{{ ucfirst($a->level) }}@if($a->category) · {{ $a->category->name }}@endif</p>
                        </td>
                        <td class="py-3 px-4 text-slate-600">
                            {{ $a->student->user->name }}<br>
                            <span class="text-xs text-slate-400">{{ $a->student->matric_no }}</span>
                        </td>
                        <td class="py-3 px-4 text-slate-600">
                            {{ $a->lecturerReviewer?->name ?? '—' }}
                            @if($a->lecturer_notes)
                                <p class="text-xs text-slate-400 mt-1 line-clamp-2">{{ $a->lecturer_notes }}</p>
                            @endif
                        </td>
                        <td class="py-3 px-4">
                            @if($a->lecturer_status === 'verified')
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-700">
                                    <i data-lucide="check" class="w-3 h-3"></i> Verified
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-700">
                                    <i data-lucide="x" class="w-3 h-3"></i> Rejected
                                </span>
                            @endif
                        </td>
                        <td class="py-3 px-4 text-slate-500">{{ $a->lecturer_reviewed_at?->format('d M Y H:i') ?? '—' }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="py-12 text-center text-slate-400">No history yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($reviewed->hasPages())
        <div class="px-4 py-3 border-t border-slate-100">{{ $reviewed->links() }}</div>
        @endif
    </div>
</div>
@endsection
