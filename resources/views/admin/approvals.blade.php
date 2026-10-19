@extends('layouts.talentsys')

@section('title', 'Approvals')

@section('content')
@php
    $status = request('status', 'pending');
    $search = request('search');
    $categoryId = request('category_id');
    $programme = request('programme');

    $query = \App\Models\Achievement::with('student.user', 'category')
        ->whereNull('participation_id');

    if ($status === 'pending') $query->where('lecturer_status', 'pending');
    elseif ($status === 'approved') $query->where('status', 'approved');
    elseif ($status === 'rejected') $query->where('status', 'rejected');

    if ($search) {
        $query->where(function($q) use ($search) {
            $q->where('title', 'like', "%{$search}%")
              ->orWhereHas('student.user', fn($q) => $q->where('name', 'like', "%{$search}%"));
        });
    }
    if ($categoryId) $query->where('category_id', $categoryId);
    if ($programme) $query->whereHas('student', fn($q) => $q->where('programme', $programme));

    $achievements = $query->latest()->paginate(10)->withQueryString();
    $categories = \App\Models\Category::all();
    $programmes = \App\Models\Student::select('programme')->distinct()->pluck('programme');

    $totalPending = \App\Models\Achievement::whereNull('participation_id')->where('lecturer_status', 'pending')->count();
    $approvedToday = \App\Models\Achievement::where('status', 'approved')
        ->whereDate('updated_at', today())->count();
    $rejectedToday = \App\Models\Achievement::where('status', 'rejected')
        ->whereDate('updated_at', today())->count();
@endphp

<div class="max-w-7xl mx-auto space-y-6">

    <div class="flex justify-between items-center">
        <div>
            <h2 class="text-xl font-bold text-slate-800 flex items-center gap-2">
                <i data-lucide="check-circle" class="w-6 h-6 text-violet-600"></i>
                Achievement Approvals
            </h2>
        </div>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-3 gap-4">
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-4">
            <p class="text-xs text-slate-500">Total Pending</p>
            <p class="text-xl font-bold text-amber-600">{{ $totalPending }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-4">
            <p class="text-xs text-slate-500">Approved Today</p>
            <p class="text-xl font-bold text-emerald-600">{{ $approvedToday }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-4">
            <p class="text-xs text-slate-500">Rejected Today</p>
            <p class="text-xl font-bold text-red-600">{{ $rejectedToday }}</p>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-4">
        <form method="GET" class="flex flex-wrap items-center gap-3">
            <div class="flex-1 min-w-[200px]">
                <div class="relative">
                    <i data-lucide="search" class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400"></i>
                    <input type="text" name="search" value="{{ $search }}" placeholder="Search student or title..."
                        class="w-full pl-9 pr-4 py-2 rounded-lg border border-slate-300 text-sm focus:ring-2 focus:ring-violet-500 focus:border-violet-500 outline-none">
                </div>
            </div>
            <select name="programme"
                class="px-3 py-2 rounded-lg border border-slate-300 text-sm focus:ring-2 focus:ring-violet-500 focus:border-violet-500 outline-none">
                <option value="">All Programmes</option>
                @foreach($programmes as $p)
                    <option value="{{ $p }}" {{ $programme === $p ? 'selected' : '' }}>{{ $p }}</option>
                @endforeach
            </select>
            <select name="category_id"
                class="px-3 py-2 rounded-lg border border-slate-300 text-sm focus:ring-2 focus:ring-violet-500 focus:border-violet-500 outline-none">
                <option value="">All Categories</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat->id }}" {{ $categoryId == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                @endforeach
            </select>
            <select name="status"
                class="px-3 py-2 rounded-lg border border-slate-300 text-sm focus:ring-2 focus:ring-violet-500 focus:border-violet-500 outline-none">
                <option value="pending" {{ $status === 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="approved" {{ $status === 'approved' ? 'selected' : '' }}>Approved</option>
                <option value="rejected" {{ $status === 'rejected' ? 'selected' : '' }}>Rejected</option>
                <option value="all" {{ $status === 'all' ? 'selected' : '' }}>All</option>
            </select>
            <button type="submit" class="px-4 py-2 gradient-primary text-white text-sm font-medium rounded-lg">Filter</button>
        </form>
    </div>

    <!-- Bulk Actions -->
    <form id="bulk-form" method="POST" action="{{ route('admin.approvals.bulk-approve') }}">
        @csrf
        <input type="hidden" name="ids" id="bulk-ids">

        <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="text-left text-slate-400 border-b border-slate-100 bg-slate-50">
                            <th class="py-3 px-4 w-10">
                                <input type="checkbox" id="select-all" class="rounded border-slate-300 text-violet-600 focus:ring-violet-500">
                            </th>
                            <th class="py-3 px-4 font-medium">Student</th>
                            <th class="py-3 px-4 font-medium">Programme</th>
                            <th class="py-3 px-4 font-medium">Achievement</th>
                            <th class="py-3 px-4 font-medium">Category</th>
                            <th class="py-3 px-4 font-medium">Points</th>
                            <th class="py-3 px-4 font-medium">Date</th>
                            <th class="py-3 px-4 font-medium">Evidence</th>
                            <th class="py-3 px-4 font-medium">Status</th>
                            <th class="py-3 px-4 font-medium">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($achievements as $a)
                        <tr class="hover:bg-slate-50 {{ $a->status === 'pending' ? 'border-l-2 border-l-amber-400' : '' }}">
                            <td class="py-3 px-4">
                                <input type="checkbox" name="selected[]" value="{{ $a->id }}"
                                    class="select-item rounded border-slate-300 text-violet-600 focus:ring-violet-500"
                                    {{ $a->status !== 'pending' ? 'disabled' : '' }}>
                            </td>
                            <td class="py-3 px-4">
                                <div class="flex items-center gap-2">
                                    <div class="w-7 h-7 rounded-full bg-slate-200 flex items-center justify-center text-xs font-semibold text-slate-600">
                                        {{ strtoupper(substr($a->student->user->name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <p class="font-medium text-slate-800">{{ $a->student->user->name }}</p>
                                        <p class="text-xs text-slate-400">{{ $a->student->matric_no }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="py-3 px-4 text-slate-600">{{ $a->student->course }}</td>
                            <td class="py-3 px-4 font-medium text-slate-800">{{ $a->title }}</td>
                            <td class="py-3 px-4">
                                <span class="px-2 py-0.5 rounded-full text-xs font-medium bg-violet-100 text-violet-700">{{ $a->category?->name }}</span>
                            </td>
                            <td class="py-3 px-4">{{ $a->score }}</td>
                            <td class="py-3 px-4 text-slate-500">{{ $a->created_at->format('d M Y') }}</td>
                            <td class="py-3 px-4">
                                @if($a->certificate)
                                <button type="button" @@click="document.getElementById('evidence-{{ $a->id }}').showModal()"
                                    class="text-xs text-violet-600 hover:text-violet-700 font-medium flex items-center gap-1">
                                    <i data-lucide="file-text" class="w-3 h-3"></i> View
                                </button>

                                <!-- Evidence Modal -->
                                <dialog id="evidence-{{ $a->id }}"
                                    class="bg-transparent backdrop:bg-black/60">
                                    <div class="bg-white rounded-2xl shadow-2xl max-w-2xl w-full p-6">
                                        <div class="flex justify-between items-start mb-4">
                                            <div>
                                                <h3 class="text-lg font-bold text-slate-800">{{ $a->student->user->name }}</h3>
                                                <p class="text-sm text-slate-500">{{ $a->student->matric_no }} | {{ $a->student->course }}</p>
                                            </div>
                                            <button type="button"
                                                onclick="this.closest('dialog').close()"
                                                class="p-1 hover:bg-slate-100 rounded-lg transition">
                                                <i data-lucide="x" class="w-5 h-5 text-slate-400"></i>
                                            </button>
                                        </div>
                                        <div class="mb-4">
                                            <p class="font-semibold text-slate-800">{{ $a->title }}</p>
                                            <p class="text-sm text-slate-500">{{ $a->category?->name }} | {{ ucfirst($a->level) }}</p>
                                        </div>
                                        @if($a->description)
                                        <p class="text-sm text-slate-600 mb-4">{{ $a->description }}</p>
                                        @endif
                                        <div class="bg-slate-50 rounded-lg p-4 mb-4 flex items-center justify-center min-h-[200px]">
                                            <a href="{{ asset('storage/'.$a->certificate) }}" target="_blank"
                                                class="text-violet-600 hover:text-violet-700 font-medium">
                                                <i data-lucide="external-link" class="w-5 h-5 inline"></i> Open Certificate
                                            </a>
                                        </div>
                                        @if($a->status === 'pending')
                                        <div class="flex gap-3 justify-end">
                                            <form method="POST" action="{{ route('admin.achievements.reject', $a->id) }}" class="inline">
                                                @csrf
                                                <button onclick="return confirm('Reject this achievement?')"
                                                    class="px-4 py-2 rounded-lg bg-red-500 text-white text-sm font-medium hover:bg-red-600 transition">Reject</button>
                                            </form>
                                            <form method="POST" action="{{ route('admin.achievements.approve', $a->id) }}" class="inline">
                                                @csrf
                                                <button class="px-4 py-2 rounded-lg bg-emerald-500 text-white text-sm font-medium hover:bg-emerald-600 transition">Approve</button>
                                            </form>
                                        </div>
                                        @endif
                                    </div>
                                </dialog>
                                @else
                                <span class="text-xs text-slate-400">—</span>
                                @endif
                            </td>
                            <td class="py-3 px-4">
                                @if($a->status === 'approved')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-700">
                                        <i data-lucide="check" class="w-3 h-3"></i> Approved
                                    </span>
                                @elseif($a->status === 'pending')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-700">
                                        <i data-lucide="clock" class="w-3 h-3"></i> Pending
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-700">
                                        <i data-lucide="x" class="w-3 h-3"></i> Rejected
                                    </span>
                                @endif
                            </td>
                            <td class="py-3 px-4">
                                @if($a->status === 'pending')
                                <div class="flex gap-2">
                                    <form method="POST" action="{{ route('admin.achievements.approve', $a->id) }}" class="inline">
                                        @csrf
                                        <button class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg text-xs font-medium bg-emerald-100 text-emerald-700 hover:bg-emerald-200 transition">
                                            <i data-lucide="check" class="w-3 h-3"></i> Approve
                                        </button>
                                    </form>
                                    <form method="POST" action="{{ route('admin.achievements.reject', $a->id) }}" class="inline">
                                        @csrf
                                        <button onclick="return confirm('Reject this achievement?')"
                                            class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg text-xs font-medium bg-red-100 text-red-700 hover:bg-red-200 transition">
                                            <i data-lucide="x" class="w-3 h-3"></i> Reject
                                        </button>
                                    </form>
                                </div>
                                @else
                                <span class="text-xs text-slate-400">—</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="10" class="py-12 text-center text-slate-400">
                                <i data-lucide="inbox" class="w-10 h-10 mx-auto mb-2"></i>
                                <p>No achievements found</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($achievements->hasPages())
            <div class="px-4 py-3 border-t border-slate-100">
                {{ $achievements->links() }}
            </div>
            @endif
        </div>

        <!-- Bulk approve/reject buttons -->
        @if($achievements->count() > 0)
        <div class="flex gap-3 mt-4">
            <button type="submit" form="bulk-form"
                onclick="document.getElementById('bulk-ids').value = getSelectedIds();"
                formaction="{{ route('admin.approvals.bulk-approve') }}"
                class="px-4 py-2 rounded-lg bg-emerald-500 text-white text-sm font-medium hover:bg-emerald-600 transition">
                Bulk Approve
            </button>
            <button type="submit" form="bulk-form"
                onclick="document.getElementById('bulk-ids').value = getSelectedIds();"
                formaction="{{ route('admin.approvals.bulk-reject') }}"
                class="px-4 py-2 rounded-lg bg-red-500 text-white text-sm font-medium hover:bg-red-600 transition">
                Bulk Reject
            </button>
        </div>
        @endif
    </form>

</div>

<script>
function getSelectedIds() {
    const checkboxes = document.querySelectorAll('.select-item:checked');
    return Array.from(checkboxes).map(cb => cb.value).join(',');
}
document.addEventListener('DOMContentLoaded', function () {
    const selectAll = document.getElementById('select-all');
    if (selectAll) {
        selectAll.addEventListener('change', function () {
            document.querySelectorAll('.select-item:not(:disabled)').forEach(cb => cb.checked = this.checked);
        });
    }
});
</script>
@endsection
