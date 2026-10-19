@extends('layouts.talentsys')

@section('title', 'Join Activity')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
        <div class="flex items-center gap-3 mb-4">
            <div class="w-10 h-10 rounded-lg gradient-primary flex items-center justify-center text-white shadow">
                <i data-lucide="user-plus" class="w-5 h-5"></i>
            </div>
            <div>
                <h2 class="text-lg font-bold text-slate-800">Join Activity</h2>
                <p class="text-xs text-slate-500">{{ $activity->activity_name }}</p>
            </div>
        </div>

        <div class="bg-slate-50 rounded-lg p-4 mb-5 text-sm">
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <span class="text-slate-500">Date:</span>
                    <span class="font-medium text-slate-800 ml-1">{{ $activity->activity_date->format('d M Y') }}</span>
                </div>
                <div>
                    <span class="text-slate-500">Organizer:</span>
                    <span class="font-medium text-slate-800 ml-1">{{ $activity->organizer }}</span>
                </div>
                <div>
                    <span class="text-slate-500">Level:</span>
                    <span class="font-medium text-slate-800 ml-1">{{ ucfirst($activity->activity_level) }}</span>
                </div>
                <div>
                    <span class="text-slate-500">Type:</span>
                    <span class="font-medium text-slate-800 ml-1">{{ $activity->activity_type }}</span>
                </div>
            </div>
            <div class="mt-3 pt-3 border-t border-slate-200">
                <p class="text-xs text-slate-500">
                    <i data-lucide="info" class="w-3 h-3 inline mr-1"></i>
                    You can upload your certificate and event photos after the event from the My Participations page.
                </p>
            </div>
        </div>

        <form method="POST" action="{{ route('student.participations.store', $activity) }}" class="space-y-5">
            @csrf

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1.5">Your Role / Position</label>
                <input type="text" name="participation_role" value="{{ old('participation_role') }}"
                    placeholder="e.g. Participant, Team Leader"
                    class="w-full px-4 py-2.5 rounded-lg border border-slate-300 bg-white text-sm focus:ring-2 focus:ring-violet-500 outline-none">
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1.5">Accompanying Lecturer <span class="text-red-500">*</span></label>
                <select name="accompanying_id" required
                    class="w-full px-4 py-2.5 rounded-lg border border-slate-300 bg-white text-sm focus:ring-2 focus:ring-violet-500 outline-none @error('accompanying_id') border-red-300 @enderror">
                    <option value="">Select lecturer</option>
                    @foreach($lecturers as $l)
                        <option value="{{ $l->id }}"
                            {{ old('accompanying_id', $activity->accompanying_id) == $l->id ? 'selected' : '' }}>
                            {{ $l->lecturer_name }} ({{ $l->user?->email ?? $l->lecturer_email }})
                        </option>
                    @endforeach
                </select>
                @if($activity->accompanying_id)
                    <p class="mt-1 text-xs text-blue-600">Pre-assigned by the ITC admin for this activity.</p>
                @endif
                @error('accompanying_id') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
            </div>

            <div class="flex justify-end gap-3 pt-2">
                <a href="{{ route('student.activities.show', $activity) }}"
                    class="px-5 py-2.5 rounded-lg border border-slate-300 text-slate-700 text-sm font-medium hover:bg-slate-50">Cancel</a>
                <button type="submit" class="px-5 py-2.5 gradient-primary text-white text-sm font-medium rounded-lg hover:scale-[1.02] transition shadow-md">
                    Submit Participation
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
