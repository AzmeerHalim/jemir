@extends('layouts.talentsys')

@section('title', 'History')

@section('content')
<div class="max-w-6xl mx-auto space-y-6">

    <h2 class="text-xl font-bold text-slate-800">Approval History</h2>

    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
        <table class="w-full text-sm">
            <thead>
                <tr class="text-left text-slate-400 bg-slate-50 border-b border-slate-100">
                    <th class="py-3 px-4 font-medium">Achievement</th>
                    <th class="py-3 px-4 font-medium">Student</th>
                    <th class="py-3 px-4 font-medium">Category</th>
                    <th class="py-3 px-4 font-medium">Status</th>
                    <th class="py-3 px-4 font-medium">Date</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @foreach($achievements as $a)
                <tr class="hover:bg-slate-50">
                    <td class="py-3 px-4 font-medium text-slate-800">{{ $a->title }}</td>
                    <td class="py-3 px-4">{{ $a->student->user->name }}</td>
                    <td class="py-3 px-4">
                        <span class="px-2 py-0.5 rounded-full text-xs font-medium bg-violet-100 text-violet-700">{{ $a->category->name }}</span>
                    </td>
                    <td class="py-3 px-4">
                        @if($a->status == 'approved')
                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-700">
                                <i data-lucide="check" class="w-3 h-3"></i> Approved
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-700">
                                <i data-lucide="x" class="w-3 h-3"></i> Rejected
                            </span>
                        @endif
                    </td>
                    <td class="py-3 px-4 text-slate-500">{{ $a->created_at->diffForHumans() }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

</div>
@endsection
