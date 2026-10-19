@extends('layouts.talentsys')

@section('title', 'Clusters')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <h2 class="text-xl font-bold text-slate-800">Talent Clusters</h2>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
            <h3 class="text-sm font-semibold text-slate-800 mb-4">Add Cluster</h3>
            <form method="POST" action="{{ route('itc.clusters.store') }}" class="space-y-3">
                @csrf
                <input type="text" name="cluster_name" required placeholder="Cluster name"
                    class="w-full px-4 py-2.5 rounded-lg border border-slate-300 bg-white text-sm focus:ring-2 focus:ring-violet-500 outline-none">
                <textarea name="description" rows="2" placeholder="Description (optional)"
                    class="w-full px-4 py-2.5 rounded-lg border border-slate-300 bg-white text-sm focus:ring-2 focus:ring-violet-500 outline-none resize-none"></textarea>
                <button type="submit" class="w-full px-4 py-2.5 gradient-primary text-white text-sm font-medium rounded-lg hover:scale-[1.02] transition shadow-md">Add Cluster</button>
            </form>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
            <h3 class="text-sm font-semibold text-slate-800 mb-4">Cluster List</h3>
            <div class="space-y-2 max-h-96 overflow-y-auto">
                @forelse($clusters as $c)
                <div class="flex justify-between items-center p-3 rounded-lg bg-slate-50">
                    <div>
                        <p class="text-sm font-medium text-slate-800">{{ $c->cluster_name }}</p>
                        <p class="text-xs text-slate-500">{{ $c->student_profiles_count ?? 0 }} students</p>
                    </div>
                    <form method="POST" action="{{ route('itc.clusters.destroy', $c) }}" onsubmit="return confirm('Delete?')">
                        @csrf @method('DELETE')
                        <button class="text-xs text-red-600 hover:text-red-700 font-medium">Delete</button>
                    </form>
                </div>
                @empty
                <p class="text-sm text-slate-400">No clusters yet.</p>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
