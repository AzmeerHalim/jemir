@extends('layouts.talentsys')

@section('title', 'Find Students')

@section('content')
<div class="max-w-7xl mx-auto space-y-6" x-data="{ selected: [], selectAll: false, message: '' }">
    <div class="flex justify-between items-center">
        <div>
            <h2 class="text-xl font-bold text-slate-800 flex items-center gap-2">
                <i data-lucide="search" class="w-6 h-6 text-violet-600"></i>
                Find Students by Talent
            </h2>
            <p class="text-sm text-slate-500">Search for students with specific talents, skills, or interests for your activities.</p>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-4">
        <form method="GET" class="space-y-3">
            <div class="flex flex-wrap items-center gap-3">
                <input type="text" name="q" value="{{ request('q') }}" placeholder="Search by name, matric, talent, skills..."
                    class="flex-1 min-w-[200px] px-3 py-2 rounded-lg border border-slate-300 text-sm focus:ring-2 focus:ring-violet-500 outline-none">
                <select name="programme" onchange="this.form.submit()" class="px-3 py-2 rounded-lg border border-slate-300 text-sm focus:ring-2 focus:ring-violet-500 outline-none">
                    <option value="">All Programmes</option>
                    @foreach($programmes as $p)
                        <option value="{{ $p }}" {{ request('programme') === $p ? 'selected' : '' }}>{{ $p }}</option>
                    @endforeach
                </select>
                <select name="year" onchange="this.form.submit()" class="px-3 py-2 rounded-lg border border-slate-300 text-sm focus:ring-2 focus:ring-violet-500 outline-none">
                    <option value="">All Years</option>
                    @for($y = 1; $y <= 4; $y++)
                        <option value="{{ $y }}" {{ request('year') == (string)$y ? 'selected' : '' }}>Year {{ $y }}</option>
                    @endfor
                </select>
                <button type="submit" class="px-4 py-2 gradient-primary text-white text-sm font-medium rounded-lg">Search</button>
            </div>

            @if($allTalents->isNotEmpty())
            <div class="pt-2 border-t border-slate-100">
                <p class="text-xs text-slate-500 mb-2">Filter by talent:</p>
                <div class="flex flex-wrap gap-1.5">
                    @foreach($allTalents as $t)
                        @php
                            $isActive = in_array($t, $selectedTalents);
                            $routeName = request()->route()->getName();
                            $currentTalents = is_array(request('talents')) ? request('talents') : array_filter(explode(',', request('talents', '')));
                            $talentParam = $isActive ? array_values(array_diff($currentTalents, [$t])) : array_merge($currentTalents, [$t]);
                            $talentParam = array_filter($talentParam, fn($v) => $v !== '');
                        @endphp
                        <a href="{{ route($routeName, array_filter(['q' => request('q'), 'programme' => request('programme'), 'year' => request('year'), 'talents' => $talentParam ?: null])) }}"
                            class="px-2.5 py-1 rounded-full text-xs font-medium transition {{ $isActive ? 'bg-violet-600 text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                            {{ $t }}
                        </a>
                    @endforeach
                    @if(!empty($selectedTalents))
                        @php $routeName = request()->route()->getName(); @endphp
                        <a href="{{ route($routeName, array_filter(['q' => request('q'), 'programme' => request('programme'), 'year' => request('year')])) }}"
                            class="px-2.5 py-1 rounded-full text-xs font-medium bg-red-50 text-red-600 hover:bg-red-100">
                            Clear filters
                        </a>
                    @endif
                </div>
            </div>
            @endif
        </form>
    </div>

    {{-- NOTIFY PANEL --}}
    <div x-show="selected.length > 0" x-cloak class="bg-violet-50 border border-violet-200 rounded-xl p-4">
        <form method="POST" action="{{ route('notify.students') }}">
            @csrf
            <template x-for="id in selected" :key="id">
                <input type="hidden" name="student_ids[]" :value="id">
            </template>
            <div class="flex flex-col sm:flex-row items-start sm:items-center gap-3">
                <div class="flex-1 w-full">
                    <textarea name="message" x-model="message" required rows="2" maxlength="500"
                        placeholder="Write a message to send to the selected students..."
                        class="w-full px-4 py-2 rounded-lg border border-slate-300 bg-white text-sm focus:ring-2 focus:ring-violet-500 outline-none resize-none"></textarea>
                </div>
                <button type="submit" class="px-5 py-2.5 gradient-primary text-white text-sm font-medium rounded-lg whitespace-nowrap">
                    Notify <span x-text="selected.length"></span> Student<span x-show="selected.length !== 1">s</span>
                </button>
            </div>
        </form>
    </div>

    @if($students->count() > 0)
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-left text-slate-400 border-b border-slate-100 bg-slate-50">
                        <th class="py-3 px-4 font-medium w-10">
                            <input type="checkbox"
                                x-model="selectAll"
                                @@change="if (selectAll) { selected = {{ json_encode($students->pluck('id')) }} } else { selected = [] }"
                                class="rounded border-slate-300 text-violet-600 focus:ring-violet-500">
                        </th>
                        <th class="py-3 px-4 font-medium">Student</th>
                        <th class="py-3 px-4 font-medium">Matric</th>
                        <th class="py-3 px-4 font-medium">Programme</th>
                        <th class="py-3 px-4 font-medium">Year</th>
                        <th class="py-3 px-4 font-medium">Talent</th>
                        <th class="py-3 px-4 font-medium">Skills</th>
                        <th class="py-3 px-4 font-medium">Points</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($students as $s)
                    <tr class="hover:bg-slate-50">
                        <td class="py-3 px-4">
                            <input type="checkbox"
                                value="{{ $s->id }}"
                                x-model="selected"
                                class="rounded border-slate-300 text-violet-600 focus:ring-violet-500">
                        </td>
                        <td class="py-3 px-4 font-medium text-slate-800">{{ $s->user->name }}</td>
                        <td class="py-3 px-4 text-slate-600">{{ $s->matric_no }}</td>
                        <td class="py-3 px-4">{{ $s->programme }}</td>
                        <td class="py-3 px-4">{{ $s->year }}</td>
                        <td class="py-3 px-4">
                            @if($s->talent)
                                <span class="px-2 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-700">{{ $s->talent }}</span>
                            @else
                                <span class="text-xs text-slate-400">—</span>
                            @endif
                        </td>
                        <td class="py-3 px-4">
                            @if($s->skills && count($s->skills) > 0)
                                <div class="flex flex-wrap gap-1">
                                    @foreach(array_slice($s->skills, 0, 3) as $skill)
                                        <span class="px-1.5 py-0.5 rounded text-[10px] font-medium bg-blue-100 text-blue-700">{{ $skill }}</span>
                                    @endforeach
                                    @if(count($s->skills) > 3)
                                        <span class="text-[10px] text-slate-400">+{{ count($s->skills) - 3 }}</span>
                                    @endif
                                </div>
                            @else
                                <span class="text-xs text-slate-400">—</span>
                            @endif
                        </td>
                        <td class="py-3 px-4 font-semibold text-violet-600">{{ $s->getTotalScore() }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    <div>{{ $students->links() }}</div>
    @else
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-12 text-center text-slate-400">
        <i data-lucide="users" class="w-12 h-12 mx-auto mb-3"></i>
        <p>No students found matching your criteria.</p>
    </div>
    @endif
</div>
@endsection
