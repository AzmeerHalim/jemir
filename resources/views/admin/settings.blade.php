@extends('layouts.talentsys')

@section('title', 'Settings')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">

    <h2 class="text-xl font-bold text-slate-800 flex items-center gap-2">
        <i data-lucide="settings" class="w-6 h-6 text-violet-600"></i>
        Settings
    </h2>

    @if(session('success'))
        <div class="flex items-center gap-2 px-4 py-3 bg-emerald-50 border border-emerald-200 text-emerald-700 rounded-lg text-sm">
            <i data-lucide="check-circle" class="w-4 h-4"></i> {{ session('success') }}
        </div>
    @endif

    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
        <h3 class="text-sm font-semibold text-slate-800 mb-4">Profile Information</h3>
        <form method="POST" action="{{ route('admin.settings.update') }}" class="space-y-4">
            @csrf @method('PUT')

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1.5">Name</label>
                <input type="text" name="name" value="{{ auth()->user()->name }}" required
                    class="w-full px-4 py-2.5 rounded-lg border border-slate-300 bg-white text-slate-800 text-sm focus:ring-2 focus:ring-violet-500 focus:border-violet-500 outline-none transition">
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1.5">Email</label>
                <input type="email" name="email" value="{{ auth()->user()->email }}" required
                    class="w-full px-4 py-2.5 rounded-lg border border-slate-300 bg-white text-slate-800 text-sm focus:ring-2 focus:ring-violet-500 focus:border-violet-500 outline-none transition">
            </div>

            <div class="pt-2">
                <button type="submit"
                    class="px-5 py-2.5 gradient-primary text-white text-sm font-medium rounded-lg hover:scale-[1.02] transition shadow-md">Save Changes</button>
            </div>
        </form>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
        <h3 class="text-sm font-semibold text-slate-800 mb-4">Change Password</h3>
        <form method="POST" action="{{ route('password.update') }}" class="space-y-4">
            @csrf @method('PUT')

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1.5">Current Password</label>
                <input type="password" name="current_password" required
                    class="w-full px-4 py-2.5 rounded-lg border border-slate-300 bg-white text-slate-800 text-sm focus:ring-2 focus:ring-violet-500 focus:border-violet-500 outline-none transition">
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1.5">New Password</label>
                <input type="password" name="password" required
                    class="w-full px-4 py-2.5 rounded-lg border border-slate-300 bg-white text-slate-800 text-sm focus:ring-2 focus:ring-violet-500 focus:border-violet-500 outline-none transition">
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1.5">Confirm New Password</label>
                <input type="password" name="password_confirmation" required
                    class="w-full px-4 py-2.5 rounded-lg border border-slate-300 bg-white text-slate-800 text-sm focus:ring-2 focus:ring-violet-500 focus:border-violet-500 outline-none transition">
            </div>

            <div class="pt-2">
                <button type="submit"
                    class="px-5 py-2.5 gradient-primary text-white text-sm font-medium rounded-lg hover:scale-[1.02] transition shadow-md">Update Password</button>
            </div>
        </form>
    </div>

</div>
@endsection
