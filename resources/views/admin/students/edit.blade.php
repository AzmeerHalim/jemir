@extends('layouts.talentsys')

@section('title', 'Edit Student')

@section('content')
<div class="max-w-2xl mx-auto">

    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
        <div class="flex items-center gap-3 mb-6">
            <div class="w-10 h-10 rounded-lg bg-blue-100 flex items-center justify-center text-blue-600">
                <i data-lucide="user" class="w-5 h-5"></i>
            </div>
            <div>
                <h2 class="text-lg font-bold text-slate-800">Edit Student</h2>
                <p class="text-xs text-slate-500">{{ $student->user->name }}</p>
            </div>
        </div>

        <form method="POST" action="{{ route('admin.students.update', $student->id) }}" class="space-y-4">
            @csrf

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1.5">Programme</label>
                <select name="course"
                    class="w-full px-4 py-2.5 rounded-lg border border-slate-300 bg-white text-slate-800 text-sm focus:ring-2 focus:ring-violet-500 focus:border-violet-500 outline-none transition">
                    <option value="BIS" {{ $student->course == 'BIS' ? 'selected' : '' }}>BCS (Information Security) - BIS</option>
                    <option value="BIM" {{ $student->course == 'BIM' ? 'selected' : '' }}>BCS (Multimedia Computing) - BIM</option>
                    <option value="BIP" {{ $student->course == 'BIP' ? 'selected' : '' }}>BCS (Software Engineering) - BIP</option>
                    <option value="BIW" {{ $student->course == 'BIW' ? 'selected' : '' }}>BCS (Web Technology) - BIW</option>
                    <option value="BIT" {{ $student->course == 'BIT' ? 'selected' : '' }}>Bachelor of Information Technology - BIT</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1.5">Year</label>
                <input type="number" name="year" value="{{ $student->year }}" min="1" max="4"
                    class="w-full px-4 py-2.5 rounded-lg border border-slate-300 bg-white text-slate-800 text-sm focus:ring-2 focus:ring-violet-500 focus:border-violet-500 outline-none transition">
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1.5">Talent</label>
                <input type="text" name="talent" value="{{ $student->talent }}"
                    class="w-full px-4 py-2.5 rounded-lg border border-slate-300 bg-white text-slate-800 text-sm focus:ring-2 focus:ring-violet-500 focus:border-violet-500 outline-none transition">
            </div>

            <div class="flex justify-end gap-3 pt-2">
                <a href="{{ route('admin.students') }}"
                    class="px-5 py-2.5 rounded-lg border border-slate-300 text-slate-700 text-sm font-medium hover:bg-slate-50 transition">Cancel</a>
                <button type="submit"
                    class="px-5 py-2.5 gradient-primary text-white text-sm font-medium rounded-lg hover:scale-[1.02] transition shadow-md">Update</button>
            </div>
        </form>
    </div>

</div>
@endsection
