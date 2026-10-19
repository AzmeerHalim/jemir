@extends('layouts.talentsys')

@section('title', 'Users')

@section('content')
<div class="max-w-6xl mx-auto space-y-6">

    <div class="flex justify-between items-center">
        <h2 class="text-xl font-bold text-slate-800">User Management</h2>
        <a href="{{ route('coordinator.users.create') }}" class="inline-flex items-center gap-2 px-4 py-2 gradient-primary text-white text-sm font-medium rounded-lg hover:scale-[1.02] transition shadow-md">
            <i data-lucide="plus" class="w-4 h-4"></i> Add User
        </a>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-4">
        <form method="GET" class="flex flex-wrap items-center gap-3">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by name, email, username..."
                class="flex-1 min-w-[200px] px-3 py-2 rounded-lg border border-slate-300 text-sm focus:ring-2 focus:ring-violet-500 outline-none">
            <select name="role" onchange="this.form.submit()"
                class="px-3 py-2 rounded-lg border border-slate-300 text-sm focus:ring-2 focus:ring-violet-500 outline-none">
                <option value="">All Roles</option>
                @foreach($roles as $r)
                    <option value="{{ $r->value }}" {{ request('role') === $r->value ? 'selected' : '' }}>{{ $r->label() }}</option>
                @endforeach
            </select>
            <button type="submit" class="px-4 py-2 gradient-primary text-white text-sm font-medium rounded-lg">Filter</button>
        </form>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
        <table class="w-full text-sm">
            <thead>
                <tr class="text-left text-slate-400 border-b border-slate-100 bg-slate-50">
                    <th class="py-3 px-4 font-medium">Name</th>
                    <th class="py-3 px-4 font-medium">Username</th>
                    <th class="py-3 px-4 font-medium">Email</th>
                    <th class="py-3 px-4 font-medium">Role</th>
                    <th class="py-3 px-4 font-medium">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($users as $u)
                <tr class="hover:bg-slate-50">
                    <td class="py-3 px-4">
                        <div class="flex items-center gap-2">
                            <div class="w-7 h-7 rounded-full bg-slate-200 flex items-center justify-center text-xs font-semibold text-slate-600">
                                {{ strtoupper(substr($u->name, 0, 1)) }}
                            </div>
                            <span class="font-medium text-slate-800">{{ $u->name }}</span>
                        </div>
                    </td>
                    <td class="py-3 px-4 text-slate-600">{{ $u->username ?? '—' }}</td>
                    <td class="py-3 px-4 text-slate-600">{{ $u->email }}</td>
                    <td class="py-3 px-4">
                        @php $r = $u->role instanceof \App\Enums\UserRole ? $u->role : \App\Enums\UserRole::tryFrom($u->role); @endphp
                        <span class="px-2 py-0.5 rounded-full text-xs font-medium bg-violet-100 text-violet-700">{{ $r?->label() ?? $u->role }}</span>
                    </td>
                    <td class="py-3 px-4">
                        <div class="flex gap-2">
                            <a href="{{ route('coordinator.users.edit', $u) }}" class="text-xs text-blue-600 hover:text-blue-700 font-medium">Edit</a>
                            @if($u->id !== auth()->id())
                            <form method="POST" action="{{ route('coordinator.users.destroy', $u) }}" onsubmit="return confirm('Delete this user?')">
                                @csrf @method('DELETE')
                                <button class="text-xs text-red-600 hover:text-red-700 font-medium">Delete</button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="5" class="py-12 text-center text-slate-400">No users found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div>{{ $users->links() }}</div>
</div>
@endsection
