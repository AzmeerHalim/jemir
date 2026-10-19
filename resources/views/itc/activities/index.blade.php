@extends('layouts.talentsys')

@section('title', 'Activities')

@section('content')
<div class="max-w-7xl mx-auto space-y-6">
    <div class="flex justify-between items-center">
        <h2 class="text-xl font-bold text-slate-800">Manage Activities</h2>
        <a href="{{ route('itc.activities.create') }}" class="inline-flex items-center gap-2 px-4 py-2 gradient-primary text-white text-sm font-medium rounded-lg hover:scale-[1.02] transition shadow-md">
            <i data-lucide="plus" class="w-4 h-4"></i> New Activity
        </a>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-left text-slate-400 border-b border-slate-100 bg-slate-50">
                        <th class="py-3 px-4 font-medium">Name</th>
                        <th class="py-3 px-4 font-medium">Type</th>
                        <th class="py-3 px-4 font-medium">Category</th>
                        <th class="py-3 px-4 font-medium">Level</th>
                        <th class="py-3 px-4 font-medium">Date</th>
                        <th class="py-3 px-4 font-medium">Status</th>
                        <th class="py-3 px-4 font-medium">Matching</th>
                        <th class="py-3 px-4 font-medium">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($activities as $a)
                    <tr class="hover:bg-slate-50">
                        <td class="py-3 px-4 font-medium text-slate-800">
                            {{ $a->activity_name }}
                            @if($a->required_talents)
                                <div class="flex flex-wrap gap-1 mt-1">
                                    @foreach($a->required_talents as $t)
                                        <span class="px-1.5 py-0.5 rounded text-[10px] font-medium bg-amber-100 text-amber-700">{{ $t }}</span>
                                    @endforeach
                                </div>
                            @endif
                        </td>
                        <td class="py-3 px-4">{{ $a->activity_type }}</td>
                        <td class="py-3 px-4">
                            @if($a->category)
                                <span class="px-2 py-0.5 rounded-full text-xs font-medium bg-slate-100 text-slate-700">{{ $a->category->name }}</span>
                            @else
                                <span class="text-xs text-slate-400">—</span>
                            @endif
                        </td>
                        <td class="py-3 px-4"><span class="px-2 py-0.5 rounded-full text-xs font-medium bg-violet-100 text-violet-700">{{ ucfirst($a->activity_level) }}</span></td>
                        <td class="py-3 px-4 text-slate-500">{{ $a->activity_date->format('d M Y') }}</td>
                        <td class="py-3 px-4">
                            @if($a->is_open)
                                <span class="px-2 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-700">Open</span>
                            @else
                                <span class="px-2 py-0.5 rounded-full text-xs font-medium bg-slate-200 text-slate-600">Closed</span>
                            @endif
                        </td>
                        <td class="py-3 px-4">
                            @if($a->required_talents)
                                @php $matchCount = $a->matchingStudentsCount(); @endphp
                                <div class="flex items-center gap-2">
                                    <a href="{{ route('itc.talent-search', ['talents' => $a->getTalentsArray()]) }}" class="text-xs text-violet-600 hover:text-violet-700 font-medium">
                                        {{ $matchCount }} student{{ $matchCount !== 1 ? 's' : '' }}
                                    </a>
                                    @if($matchCount > 0)
                                    <form method="POST" action="{{ route('itc.activities.invite', $a) }}" onsubmit="return confirm('Notify {{ $matchCount }} students about this activity?')">
                                        @csrf
                                        <button class="text-xs text-amber-600 hover:text-amber-700 font-medium">Invite</button>
                                    </form>
                                    @endif
                                </div>
                            @else
                                <span class="text-xs text-slate-400">—</span>
                            @endif
                        </td>
                        <td class="py-3 px-4">
                            <div class="flex gap-2">
                                <a href="{{ route('itc.activities.edit', $a) }}" class="text-xs text-blue-600 hover:text-blue-700 font-medium">Edit</a>
                                <form method="POST" action="{{ route('itc.activities.destroy', $a) }}" onsubmit="return confirm('Delete this activity?')">
                                    @csrf @method('DELETE')
                                    <button class="text-xs text-red-600 hover:text-red-700 font-medium">Delete</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="8" class="py-12 text-center text-slate-400">No activities yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div>{{ $activities->links() }}</div>
</div>
@endsection
