@extends('layouts.talentsys')

@section('title', 'Upload Proof')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
        <div class="flex items-center gap-3 mb-4">
            <div class="w-10 h-10 rounded-lg gradient-primary flex items-center justify-center text-white shadow">
                <i data-lucide="upload" class="w-5 h-5"></i>
            </div>
            <div>
                <h2 class="text-lg font-bold text-slate-800">Upload Proof Documents</h2>
                <p class="text-xs text-slate-500">{{ $participation->activity->activity_name }}</p>
            </div>
        </div>

        <div class="bg-amber-50 border border-amber-200 rounded-lg p-4 mb-5 text-sm text-amber-800">
            <i data-lucide="alert-triangle" class="w-4 h-4 inline mr-1"></i>
            Only upload these documents after you have attended the event. Your participation is currently
            <span class="font-semibold">{{ $participation->participation_status }}</span>.
        </div>

        <form method="POST" action="{{ route('student.participations.update', $participation) }}" enctype="multipart/form-data" class="space-y-5">
            @csrf
            @method('PUT')

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1.5">Certificate</label>
                @if($participation->certificate_path)
                    <div class="mb-2 flex items-center gap-2 text-xs text-emerald-600">
                        <i data-lucide="file-check" class="w-3 h-3"></i>
                        <span>Already uploaded. Select a new file to replace.</span>
                    </div>
                @endif
                <input type="file" name="certificate" accept=".pdf,.jpg,.jpeg,.png"
                    class="w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-violet-50 file:text-violet-700 hover:file:bg-violet-100">
                <p class="text-xs text-slate-400 mt-1">PDF, JPG, PNG (max 5MB)</p>
                @error('certificate') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1.5">Event Photo</label>
                @if($participation->event_photo_path)
                    <div class="mb-2 flex items-center gap-2 text-xs text-emerald-600">
                        <i data-lucide="file-check" class="w-3 h-3"></i>
                        <span>Already uploaded. Select a new file to replace.</span>
                    </div>
                @endif
                <input type="file" name="event_photo" accept=".jpg,.jpeg,.png"
                    class="w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-violet-50 file:text-violet-700 hover:file:bg-violet-100">
                <p class="text-xs text-slate-400 mt-1">JPG, PNG (max 5MB)</p>
                @error('event_photo') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
            </div>

            <div class="flex justify-end gap-3 pt-2">
                <a href="{{ route('student.participations.show', $participation) }}"
                    class="px-5 py-2.5 rounded-lg border border-slate-300 text-slate-700 text-sm font-medium hover:bg-slate-50">Cancel</a>
                <button type="submit" class="px-5 py-2.5 gradient-primary text-white text-sm font-medium rounded-lg hover:scale-[1.02] transition shadow-md">
                    Upload Documents
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
