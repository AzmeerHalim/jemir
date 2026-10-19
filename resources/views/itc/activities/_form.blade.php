@php $isEdit = !empty($activity); @endphp
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
        <h2 class="text-lg font-bold text-slate-800 mb-4">{{ $isEdit ? 'Edit Activity' : 'New Activity' }}</h2>
        <form method="POST" action="{{ $action }}" class="space-y-4">
            @csrf @if($method !== 'POST') @method($method) @endif

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1.5">Activity Name <span class="text-red-500">*</span></label>
                <input type="text" name="activity_name" value="{{ old('activity_name', $activity->activity_name ?? '') }}" required
                    class="w-full px-4 py-2.5 rounded-lg border border-slate-300 bg-white text-sm focus:ring-2 focus:ring-violet-500 outline-none">
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Type <span class="text-red-500">*</span></label>
                    <input type="text" name="activity_type" value="{{ old('activity_type', $activity->activity_type ?? '') }}" required
                        placeholder="Competition, Workshop..."
                        class="w-full px-4 py-2.5 rounded-lg border border-slate-300 bg-white text-sm focus:ring-2 focus:ring-violet-500 outline-none">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Level <span class="text-red-500">*</span></label>
                    <select name="activity_level" required class="w-full px-4 py-2.5 rounded-lg border border-slate-300 bg-white text-sm focus:ring-2 focus:ring-violet-500 outline-none">
                        @foreach(['faculty','university','state','national','international'] as $lvl)
                            <option value="{{ $lvl }}" {{ old('activity_level', $activity->activity_level ?? '') === $lvl ? 'selected' : '' }}>{{ ucfirst($lvl) }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Date <span class="text-red-500">*</span></label>
                    <input type="date" name="activity_date" value="{{ old('activity_date', isset($activity) ? $activity->activity_date->format('Y-m-d') : '') }}" required
                        class="w-full px-4 py-2.5 rounded-lg border border-slate-300 bg-white text-sm focus:ring-2 focus:ring-violet-500 outline-none">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Registration Deadline</label>
                    <input type="date" name="registration_deadline" value="{{ old('registration_deadline', isset($activity) && $activity->registration_deadline ? $activity->registration_deadline->format('Y-m-d') : '') }}"
                        class="w-full px-4 py-2.5 rounded-lg border border-slate-300 bg-white text-sm focus:ring-2 focus:ring-violet-500 outline-none">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Organizer <span class="text-red-500">*</span></label>
                    <input type="text" name="organizer" value="{{ old('organizer', $activity->organizer ?? '') }}" required
                        class="w-full px-4 py-2.5 rounded-lg border border-slate-300 bg-white text-sm focus:ring-2 focus:ring-violet-500 outline-none">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Category <span class="text-slate-400 text-xs">(optional)</span></label>
                    <select name="category_id"
                        class="w-full px-4 py-2.5 rounded-lg border border-slate-300 bg-white text-sm focus:ring-2 focus:ring-violet-500 outline-none">
                        <option value="">-- Select category --</option>
                        @foreach(($categories ?? \App\Models\Category::orderBy('name')->get()) as $cat)
                            <option value="{{ $cat->id }}" {{ (string) old('category_id', $activity->category_id ?? '') === (string) $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                        @endforeach
                    </select>
                    @error('category_id') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1.5">Accompanying Lecturer <span class="text-slate-400 text-xs">(optional)</span></label>
                <select name="accompanying_id"
                    class="w-full px-4 py-2.5 rounded-lg border border-slate-300 bg-white text-sm focus:ring-2 focus:ring-violet-500 outline-none">
                    <option value="">-- Select lecturer --</option>
                    @foreach(($lecturers ?? \App\Models\AccompanyingLecturer::where('is_active', true)->orderBy('lecturer_name')->get()) as $l)
                        <option value="{{ $l->id }}" {{ (string) old('accompanying_id', $activity->accompanying_id ?? '') === (string) $l->id ? 'selected' : '' }}>{{ $l->lecturer_name }}</option>
                    @endforeach
                </select>
                <p class="mt-1 text-xs text-slate-400">Students joining this activity will be auto-assigned to this lecturer for verification.</p>
                @error('accompanying_id') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1.5">Location</label>
                <input type="text" name="location" value="{{ old('location', $activity->location ?? '') }}"
                    class="w-full px-4 py-2.5 rounded-lg border border-slate-300 bg-white text-sm focus:ring-2 focus:ring-violet-500 outline-none">
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1.5">Description</label>
                <textarea name="description" rows="3" class="w-full px-4 py-2.5 rounded-lg border border-slate-300 bg-white text-sm focus:ring-2 focus:ring-violet-500 outline-none resize-none">{{ old('description', $activity->description ?? '') }}</textarea>
            </div>

            <div class="flex items-center gap-2">
                <input type="hidden" name="is_open" value="0">
                <input type="checkbox" name="is_open" value="1" id="is_open" {{ old('is_open', $activity->is_open ?? true) ? 'checked' : '' }}
                    class="rounded border-slate-300 text-violet-600 focus:ring-violet-500">
                <label for="is_open" class="text-sm text-slate-700">Open for registration</label>
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1.5">Max Participants <span class="text-slate-400 text-xs">(optional)</span></label>
                <input type="number" name="max_participants" value="{{ old('max_participants', $activity->max_participants ?? '') }}" min="1"
                    placeholder="Leave blank for unlimited"
                    class="w-full px-4 py-2.5 rounded-lg border border-slate-300 bg-white text-sm focus:ring-2 focus:ring-violet-500 outline-none">
                @error('max_participants') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1.5">Required Talents <span class="text-slate-400 text-xs">(comma-separated)</span></label>
                <input type="text" name="required_talents"
                    value="{{ old('required_talents', isset($activity) && $activity->required_talents ? implode(', ', $activity->required_talents) : '') }}"
                    placeholder="e.g. Singing, Guitar, Public Speaking"
                    class="w-full px-4 py-2.5 rounded-lg border border-slate-300 bg-white text-sm focus:ring-2 focus:ring-violet-500 outline-none">
                <p class="mt-1 text-xs text-slate-400">Used to find and suggest matching students for this activity.</p>
                @error('required_talents') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
            </div>

            <div class="flex justify-end gap-3 pt-2">
                <a href="{{ route('itc.activities.index') }}" class="px-5 py-2.5 rounded-lg border border-slate-300 text-slate-700 text-sm font-medium hover:bg-slate-50">Cancel</a>
                <button type="submit" class="px-5 py-2.5 gradient-primary text-white text-sm font-medium rounded-lg hover:scale-[1.02] transition shadow-md">{{ $isEdit ? 'Update' : 'Create' }}</button>
            </div>
        </form>
    </div>
</div>
