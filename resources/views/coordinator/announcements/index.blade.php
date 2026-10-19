@extends('layouts.talentsys')

@section('title', 'Announcements')

@section('content')
<div class="max-w-6xl mx-auto space-y-6">
    <div class="flex justify-between items-center">
        <div>
            <h2 class="text-xl font-bold text-slate-800 flex items-center gap-2">
                <i data-lucide="megaphone" class="w-6 h-6 text-violet-600"></i>
                Announcements
            </h2>
            <p class="text-sm text-slate-500">Manage broadcast announcements to all students.</p>
        </div>
        <a href="{{ route('coordinator.announcements.create') }}" class="inline-flex items-center gap-2 px-4 py-2 gradient-primary text-white text-sm font-medium rounded-lg hover:scale-[1.02] transition shadow-md">
            <i data-lucide="plus" class="w-4 h-4"></i> New Announcement
        </a>
    </div>

    @if($announcements->count() > 0)
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-left text-slate-400 border-b border-slate-100 bg-slate-50">
                        <th class="py-3 px-4 font-medium">Title</th>
                        <th class="py-3 px-4 font-medium">Message</th>
                        <th class="py-3 px-4 font-medium">Date</th>
                        <th class="py-3 px-4 font-medium">Author</th>
                        <th class="py-3 px-4 font-medium">Status</th>
                        <th class="py-3 px-4 font-medium">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($announcements as $a)
                    <tr class="hover:bg-slate-50">
                        <td class="py-3 px-4 font-medium text-slate-800">{{ $a->announcement_title }}</td>
                        <td class="py-3 px-4 text-slate-600 max-w-xs truncate">{{ Str::limit($a->announcement_message, 80) }}</td>
                        <td class="py-3 px-4 text-slate-500">{{ $a->announcement_date->format('d M Y') }}</td>
                        <td class="py-3 px-4 text-slate-600">{{ $a->author->name ?? '—' }}</td>
                        <td class="py-3 px-4">
                            @if($a->is_published)
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-700"><i data-lucide="check" class="w-3 h-3"></i> Published</span>
                            @else
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-medium bg-slate-100 text-slate-600">Draft</span>
                            @endif
                        </td>
                        <td class="py-3 px-4">
                            <form method="POST" action="{{ route('coordinator.announcements.destroy', $a) }}" onsubmit="return confirm('Delete this announcement?')">
                                @csrf @method('DELETE')
                                <button class="text-xs text-red-600 hover:text-red-700 font-medium">Delete</button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    <div>{{ $announcements->links() }}</div>
    @else
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-12 text-center text-slate-400">
        <i data-lucide="megaphone-off" class="w-12 h-12 mx-auto mb-3"></i>
        <p>No announcements yet.</p>
    </div>
    @endif
</div>
@endsection
