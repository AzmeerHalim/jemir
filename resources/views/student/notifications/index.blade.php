@extends('layouts.talentsys')

@section('title', 'Notifications')

@section('content')
<div class="max-w-3xl mx-auto space-y-4">
    <div class="flex justify-between items-center">
        <h2 class="text-xl font-bold text-slate-800 flex items-center gap-2">
            <i data-lucide="bell" class="w-6 h-6 text-violet-600"></i>
            Notifications
        </h2>
        <form method="POST" action="{{ route('student.notifications.readAll') }}">
            @csrf
            <button type="submit" class="text-sm text-violet-600 hover:text-violet-700 font-medium">Mark all as read</button>
        </form>
    </div>

    @if($notifications->count() > 0)
        <div class="space-y-2">
            @foreach($notifications as $n)
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-4 {{ is_null($n->read_at) ? 'border-l-4 border-l-violet-500' : '' }}">
                <div class="flex justify-between items-start gap-3">
                    <div class="flex-1">
                        <p class="text-sm text-slate-800">{{ $n->notification_message }}</p>
                        <p class="text-xs text-slate-400 mt-1">{{ $n->notification_date->diffForHumans() }}</p>
                    </div>
                    @if(is_null($n->read_at))
                    <form method="POST" action="{{ route('student.notifications.read', $n) }}">
                        @csrf
                        <button class="text-xs text-violet-600 hover:text-violet-700 font-medium">Mark read</button>
                    </form>
                    @endif
                </div>
                @if($n->link)
                <a href="{{ $n->link }}" class="inline-block mt-2 text-xs text-violet-600 hover:text-violet-700 font-medium">View →</a>
                @endif
            </div>
            @endforeach
        </div>
        <div>{{ $notifications->links() }}</div>
    @else
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-12 text-center text-slate-400">
            <i data-lucide="bell-off" class="w-12 h-12 mx-auto mb-3"></i>
            <p>No notifications yet.</p>
        </div>
    @endif
</div>
@endsection
