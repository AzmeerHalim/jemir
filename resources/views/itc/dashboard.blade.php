@extends('layouts.talentsys')

@section('title', 'ITC Admin Dashboard')

@section('content')
@php
    $totalActivities = \App\Models\Activity::count();
    $openActivities = \App\Models\Activity::where('is_open', true)->count();
    $totalClusters = \App\Models\Cluster::count();
    $totalParticipations = \App\Models\Participation::count();
    $upcomingActivities = \App\Models\Activity::with('category')->open()->upcoming()->latest('activity_date')->take(5)->get();
@endphp

<div class="max-w-7xl mx-auto space-y-6">
    <div class="flex justify-between items-center">
        <div>
            <h2 class="text-xl font-bold text-slate-800">ITC Admin Dashboard</h2>
            <p class="text-sm text-slate-500">Welcome back, {{ auth()->user()->name }} — manage activities and student talent clusters.</p>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-5 flex items-center gap-4">
            <div class="w-12 h-12 rounded-lg bg-blue-100 flex items-center justify-center text-blue-600"><i data-lucide="calendar" class="w-6 h-6"></i></div>
            <div><p class="text-2xl font-bold text-slate-800">{{ $totalActivities }}</p><p class="text-xs text-slate-500">Total Activities</p></div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-5 flex items-center gap-4">
            <div class="w-12 h-12 rounded-lg bg-emerald-100 flex items-center justify-center text-emerald-600"><i data-lucide="check-circle" class="w-6 h-6"></i></div>
            <div><p class="text-2xl font-bold text-slate-800">{{ $openActivities }}</p><p class="text-xs text-slate-500">Open for Registration</p></div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-5 flex items-center gap-4">
            <div class="w-12 h-12 rounded-lg bg-violet-100 flex items-center justify-center text-violet-600"><i data-lucide="layers" class="w-6 h-6"></i></div>
            <div><p class="text-2xl font-bold text-slate-800">{{ $totalClusters }}</p><p class="text-xs text-slate-500">Talent Clusters</p></div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-5 flex items-center gap-4">
            <div class="w-12 h-12 rounded-lg bg-amber-100 flex items-center justify-center text-amber-600"><i data-lucide="users" class="w-6 h-6"></i></div>
            <div><p class="text-2xl font-bold text-slate-800">{{ $totalParticipations }}</p><p class="text-xs text-slate-500">Total Participations</p></div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 bg-white rounded-xl shadow-sm border border-slate-200 p-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-sm font-semibold text-slate-800">Upcoming Activities</h3>
                <a href="{{ route('itc.activities.index') }}" class="text-xs text-violet-600 hover:text-violet-700 font-medium">Manage All</a>
            </div>
            @if($upcomingActivities->count() > 0)
            <div class="space-y-3">
                @foreach($upcomingActivities as $a)
                <div class="flex items-center justify-between p-3 rounded-lg bg-slate-50 hover:bg-slate-100">
                    <div>
                        <p class="text-sm font-medium text-slate-800">{{ $a->activity_name }}</p>
                        <p class="text-xs text-slate-500">{{ $a->activity_date->format('d M Y') }} · {{ ucfirst($a->activity_level) }} · {{ $a->activity_type }}</p>
                    </div>
                    <a href="{{ route('itc.activities.edit', $a) }}" class="text-xs text-blue-600 hover:text-blue-700 font-medium">Edit</a>
                </div>
                @endforeach
            </div>
            @else
            <p class="text-sm text-slate-400 text-center py-8">No upcoming activities.</p>
            @endif
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
            <h3 class="text-sm font-semibold text-slate-800 mb-4">Quick Actions</h3>
            <div class="space-y-2">
                <a href="{{ route('itc.activities.create') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg bg-violet-50 hover:bg-violet-100 text-sm font-medium text-violet-700 transition">
                    <i data-lucide="plus" class="w-4 h-4"></i> New Activity
                </a>
                <a href="{{ route('itc.activities.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg bg-blue-50 hover:bg-blue-100 text-sm font-medium text-blue-700 transition">
                    <i data-lucide="calendar" class="w-4 h-4"></i> Manage Activities
                </a>
                <a href="{{ route('itc.clusters.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg bg-emerald-50 hover:bg-emerald-100 text-sm font-medium text-emerald-700 transition">
                    <i data-lucide="layers" class="w-4 h-4"></i> Manage Clusters
                </a>
                <a href="{{ route('itc.talent-search') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg bg-amber-50 hover:bg-amber-100 text-sm font-medium text-amber-700 transition">
                    <i data-lucide="search" class="w-4 h-4"></i> Find Students
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
