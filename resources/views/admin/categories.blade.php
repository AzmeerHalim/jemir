@extends('layouts.talentsys')

@section('title', 'Categories')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

    <h2 class="text-xl font-bold text-slate-800">Manage Categories</h2>

    @if(session('success'))
        <div class="flex items-center gap-2 px-4 py-3 bg-emerald-50 border border-emerald-200 text-emerald-700 rounded-lg text-sm">
            <i data-lucide="check-circle" class="w-4 h-4"></i> {{ session('success') }}
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Add -->
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
            <h3 class="text-sm font-semibold text-slate-800 mb-4">Add Category</h3>
            <form method="POST" action="{{ route('admin.categories.store') }}" class="flex gap-3">
                @csrf
                <input type="text" name="name" required placeholder="e.g. Academic"
                    class="flex-1 px-4 py-2.5 rounded-lg border border-slate-300 bg-white text-slate-800 text-sm focus:ring-2 focus:ring-violet-500 focus:border-violet-500 outline-none transition">
                <button type="submit"
                    class="px-4 py-2.5 gradient-primary text-white text-sm font-medium rounded-lg hover:scale-[1.02] transition shadow-md">Add</button>
            </form>
        </div>

        <!-- List -->
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
            <h3 class="text-sm font-semibold text-slate-800 mb-4">Category List</h3>
            <div class="space-y-2 max-h-80 overflow-y-auto">
                @forelse($categories as $cat)
                <div x-data="{ edit: false }"
                    class="flex justify-between items-center px-3 py-2.5 rounded-lg bg-slate-50 hover:bg-slate-100 transition">
                    <template x-if="!edit">
                        <div class="flex justify-between items-center w-full">
                            <span class="text-sm text-slate-800">{{ $cat->name }}</span>
                            <div class="flex gap-2">
                                <button @@click="edit = true"
                                    class="inline-flex items-center gap-1 px-2 py-1 rounded text-xs font-medium bg-blue-100 text-blue-700 hover:bg-blue-200 transition">
                                    <i data-lucide="pencil" class="w-3 h-3"></i>
                                </button>
                                <form method="POST" action="{{ route('admin.categories.delete', $cat->id) }}" class="inline"
                                    onsubmit="return confirm('Delete this category?')">
                                    @csrf @method('DELETE')
                                    <button class="inline-flex items-center gap-1 px-2 py-1 rounded text-xs font-medium bg-red-100 text-red-700 hover:bg-red-200 transition">
                                        <i data-lucide="trash-2" class="w-3 h-3"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </template>
                    <template x-if="edit">
                        <form method="POST" action="{{ route('admin.categories.update', $cat->id) }}" class="flex gap-2 w-full">
                            @csrf @method('PUT')
                            <input type="text" name="name" value="{{ $cat->name }}"
                                class="flex-1 px-2 py-1 rounded border border-slate-300 text-sm focus:ring-2 focus:ring-violet-500 outline-none">
                            <button class="px-2 py-1 rounded text-xs font-medium bg-emerald-100 text-emerald-700 hover:bg-emerald-200 transition">
                                <i data-lucide="check" class="w-3 h-3"></i>
                            </button>
                            <button type="button" @@click="edit = false"
                                class="px-2 py-1 rounded text-xs font-medium bg-slate-200 text-slate-600 hover:bg-slate-300 transition">
                                <i data-lucide="x" class="w-3 h-3"></i>
                            </button>
                        </form>
                    </template>
                </div>
                @empty
                <p class="text-sm text-slate-400">No categories yet.</p>
                @endforelse
            </div>
        </div>
    </div>

</div>
@endsection
