@extends('layouts.talentsys')

@section('title', 'Submit New Achievement')

@section('content')
<div class="max-w-3xl mx-auto">
    <a href="{{ route('achievements.index') }}" class="text-sm text-violet-600 hover:text-violet-700 flex items-center gap-1 mb-3">
        <i data-lucide="arrow-left" class="w-4 h-4"></i> Back to My Achievements
    </a>

    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
        <div class="flex items-center gap-3 mb-2">
            <div class="w-10 h-10 rounded-lg gradient-primary flex items-center justify-center text-white shadow">
                <i data-lucide="trophy" class="w-5 h-5"></i>
            </div>
            <div>
                <h2 class="text-lg font-bold text-slate-800">Submit New Achievement</h2>
                <p class="text-xs text-slate-500">Two-stage approval: accompanying lecturer first, then talent coordinator final sign-off.</p>
            </div>
        </div>

        <form method="POST" action="{{ route('achievements.store') }}" enctype="multipart/form-data" class="space-y-8 mt-6">
            @csrf

            {{-- =================== ACHIEVEMENT =================== --}}
            <section>
                <h3 class="text-sm font-semibold text-slate-700 flex items-center gap-2 mb-4">
                    <i data-lucide="award" class="w-4 h-4 text-violet-600"></i> Achievement Details
                </h3>
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">Achievement Title <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400"><i data-lucide="type" class="w-4 h-4"></i></span>
                            <input type="text" name="title" value="{{ old('title') }}" required autofocus
                                placeholder="e.g. Hackathon Champion 2026"
                                class="w-full pl-9 pr-4 py-2.5 rounded-lg border border-slate-300 bg-white text-slate-800 text-sm focus:ring-2 focus:ring-violet-500 focus:border-violet-500 outline-none transition @error('title') border-red-300 @enderror">
                        </div>
                        @error('title') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1.5">Category <span class="text-red-500">*</span></label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400"><i data-lucide="layers" class="w-4 h-4"></i></span>
                                <select name="category_id" required
                                    class="w-full pl-9 pr-4 py-2.5 rounded-lg border border-slate-300 bg-white text-slate-800 text-sm focus:ring-2 focus:ring-violet-500 focus:border-violet-500 outline-none transition appearance-none @error('category_id') border-red-300 @enderror">
                                    <option value="">Select category</option>
                                    @foreach($categories as $cat)
                                        <option value="{{ $cat->id }}" {{ (string) old('category_id') === (string) $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            @error('category_id') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1.5">Level <span class="text-red-500">*</span></label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400"><i data-lucide="bar-chart-3" class="w-4 h-4"></i></span>
                                <select name="level" required
                                    class="w-full pl-9 pr-4 py-2.5 rounded-lg border border-slate-300 bg-white text-slate-800 text-sm focus:ring-2 focus:ring-violet-500 focus:border-violet-500 outline-none transition appearance-none @error('level') border-red-300 @enderror">
                                    <option value="faculty" {{ old('level', 'faculty') === 'faculty' ? 'selected' : '' }}>Faculty (5 pts)</option>
                                    <option value="university" {{ old('level') === 'university' ? 'selected' : '' }}>University (10 pts)</option>
                                    <option value="national" {{ old('level') === 'national' ? 'selected' : '' }}>National (20 pts)</option>
                                    <option value="international" {{ old('level') === 'international' ? 'selected' : '' }}>International (30 pts)</option>
                                </select>
                            </div>
                            <p class="mt-1 text-xs text-slate-400">Score awarded on final coordinator approval.</p>
                            @error('level') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">Description</label>
                        <div class="relative">
                            <textarea name="description" rows="3" maxlength="500"
                                placeholder="Describe your achievement in detail..."
                                class="w-full px-4 py-2.5 rounded-lg border border-slate-300 bg-white text-slate-800 text-sm focus:ring-2 focus:ring-violet-500 focus:border-violet-500 outline-none transition resize-none @error('description') border-red-300 @enderror">{{ old('description') }}</textarea>
                        </div>
                        <div class="flex justify-end mt-1">
                            <span class="text-xs text-slate-400">{{ strlen(old('description', '')) }}/500</span>
                        </div>
                        @error('description') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                    </div>
                </div>
            </section>

            <hr class="border-slate-200">

            {{-- =================== EVENT =================== --}}
            <section>
                <h3 class="text-sm font-semibold text-slate-700 flex items-center gap-2 mb-4">
                    <i data-lucide="calendar" class="w-4 h-4 text-violet-600"></i> Event Details
                </h3>
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">Event / Competition Name</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400"><i data-lucide="megaphone" class="w-4 h-4"></i></span>
                            <input type="text" name="event_name" value="{{ old('event_name') }}"
                                placeholder="e.g. National ICT Innovation Competition"
                                class="w-full pl-9 pr-4 py-2.5 rounded-lg border border-slate-300 bg-white text-slate-800 text-sm focus:ring-2 focus:ring-violet-500 focus:border-violet-500 outline-none transition">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1.5">Organizer</label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400"><i data-lucide="building" class="w-4 h-4"></i></span>
                                <input type="text" name="organizer" value="{{ old('organizer') }}"
                                    placeholder="e.g. Ministry of Education"
                                    class="w-full pl-9 pr-4 py-2.5 rounded-lg border border-slate-300 bg-white text-slate-800 text-sm focus:ring-2 focus:ring-violet-500 focus:border-violet-500 outline-none transition">
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1.5">Date of Achievement</label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400"><i data-lucide="calendar" class="w-4 h-4"></i></span>
                                <input type="date" name="date_achieved" value="{{ old('date_achieved') }}"
                                    class="w-full pl-9 pr-4 py-2.5 rounded-lg border border-slate-300 bg-white text-slate-800 text-sm focus:ring-2 focus:ring-violet-500 focus:border-violet-500 outline-none transition">
                            </div>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">Location</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400"><i data-lucide="map-pin" class="w-4 h-4"></i></span>
                            <input type="text" name="location" value="{{ old('location') }}"
                                placeholder="e.g. Kuala Lumpur Convention Centre"
                                class="w-full pl-9 pr-4 py-2.5 rounded-lg border border-slate-300 bg-white text-slate-800 text-sm focus:ring-2 focus:ring-violet-500 focus:border-violet-500 outline-none transition">
                        </div>
                    </div>
                </div>
            </section>

            <hr class="border-slate-200">

            {{-- =================== ACCOMPANYING LECTURER =================== --}}
            <section>
                <h3 class="text-sm font-semibold text-slate-700 flex items-center gap-2 mb-4">
                    <i data-lucide="user-check" class="w-4 h-4 text-violet-600"></i> Accompanying Lecturer
                </h3>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Select the accompanying lecturer who can verify this submission</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400"><i data-lucide="user-check" class="w-4 h-4"></i></span>
                        <select name="accompanying_id"
                            class="w-full pl-9 pr-4 py-2.5 rounded-lg border border-slate-300 bg-white text-slate-800 text-sm focus:ring-2 focus:ring-violet-500 focus:border-violet-500 outline-none transition appearance-none @error('accompanying_id') border-red-300 @enderror">
                            <option value="">-- Select lecturer --</option>
                            @foreach(($lecturers ?? \App\Models\AccompanyingLecturer::where('is_active', true)->orderBy('lecturer_name')->get()) as $lecturer)
                                <option value="{{ $lecturer->id }}" {{ (string) old('accompanying_id') === (string) $lecturer->id ? 'selected' : '' }}>
                                    {{ $lecturer->lecturer_name }} ({{ $lecturer->user?->email ?? $lecturer->lecturer_email }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <p class="mt-1 text-xs text-slate-400">Stage 1 of approval — the selected lecturer must verify before the talent coordinator's final sign-off.</p>
                    @error('accompanying_id') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>
            </section>

            <hr class="border-slate-200">

            {{-- =================== EVIDENCE =================== --}}
            <section>
                <h3 class="text-sm font-semibold text-slate-700 flex items-center gap-2 mb-4">
                    <i data-lucide="file-check-2" class="w-4 h-4 text-violet-600"></i> Evidence
                </h3>
                <p class="text-xs text-slate-500 mb-4">Both uploads are recommended — the certificate proves the achievement; the event photo helps the lecturer verify.</p>
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">Certificate (recommended)</label>
                        <input type="file" name="certificate" accept=".pdf,.jpg,.jpeg,.png"
                            class="w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-violet-50 file:text-violet-700 hover:file:bg-violet-100">
                        <p class="mt-1 text-xs text-slate-400">PDF, JPG, PNG (max 5MB).</p>
                        @error('certificate') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">Event Photo (optional)</label>
                        <input type="file" name="event_photo" accept=".jpg,.jpeg,.png"
                            class="w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-violet-50 file:text-violet-700 hover:file:bg-violet-100">
                        <p class="mt-1 text-xs text-slate-400">JPG, PNG (max 5MB).</p>
                        @error('event_photo') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                    </div>
                </div>
            </section>

            <div class="flex items-center justify-between pt-2">
                <p class="text-xs text-slate-500">Status starts at <span class="font-semibold text-amber-600">Pending</span> until both stages approve.</p>
                <div class="flex gap-3">
                    <a href="{{ route('achievements.index') }}" class="px-5 py-2.5 rounded-lg border border-slate-300 text-slate-700 text-sm font-medium hover:bg-slate-50 transition">Cancel</a>
                    <button type="submit" class="px-6 py-2.5 gradient-primary text-white text-sm font-medium rounded-lg hover:scale-[1.02] transition shadow-md flex items-center gap-2">
                        <i data-lucide="send" class="w-4 h-4"></i> Submit Achievement
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
