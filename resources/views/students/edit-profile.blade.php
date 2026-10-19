@extends('layouts.talentsys')

@section('title', 'Edit Profile')

@section('content')
@php
    $student = auth()->user()->student;
@endphp

@if(!$student)
    <div class="max-w-2xl mx-auto">
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-12 text-center">
            <i data-lucide="user-x" class="w-12 h-12 mx-auto text-slate-400 mb-3"></i>
            <h2 class="text-lg font-bold text-slate-800">No student profile found</h2>
            <p class="text-sm text-slate-500 mt-1">Please contact the talent coordinator.</p>
        </div>
    </div>
@else

<div class="max-w-3xl mx-auto">
    <a href="{{ route('profile') }}" class="text-sm text-violet-600 hover:text-violet-700 flex items-center gap-1 mb-3">
        <i data-lucide="arrow-left" class="w-4 h-4"></i> Back to Profile
    </a>

    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
        <div class="flex items-center gap-3 mb-2">
            <div class="w-10 h-10 rounded-lg bg-blue-100 flex items-center justify-center text-blue-600">
                <i data-lucide="edit" class="w-5 h-5"></i>
            </div>
            <div>
                <h2 class="text-lg font-bold text-slate-800">Edit Profile</h2>
                <p class="text-xs text-slate-500">Update the information the talent system has on you. Your name and matric number can't be changed here.</p>
            </div>
        </div>

        <form method="POST" action="{{ route('profile.update') }}" class="space-y-8 mt-6">
            @csrf

            {{-- =================== ACCOUNT =================== --}}
            <section>
                <h3 class="text-sm font-semibold text-slate-700 flex items-center gap-2 mb-4">
                    <i data-lucide="user" class="w-4 h-4 text-violet-600"></i> Account
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">Full Name</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400"><i data-lucide="user" class="w-4 h-4"></i></span>
                            <input type="text" value="{{ auth()->user()->name }}" disabled
                                class="w-full pl-9 pr-4 py-2.5 rounded-lg border border-slate-200 bg-slate-50 text-slate-500 text-sm cursor-not-allowed">
                        </div>
                        <p class="mt-1 text-xs text-slate-400">Contact the coordinator to change your name.</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">Matric Number</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400"><i data-lucide="hash" class="w-4 h-4"></i></span>
                            <input type="text" value="{{ $student->matric_no }}" disabled
                                class="w-full pl-9 pr-4 py-2.5 rounded-lg border border-slate-200 bg-slate-50 text-slate-500 text-sm cursor-not-allowed">
                        </div>
                    </div>
                </div>
            </section>

            <hr class="border-slate-200">

            {{-- =================== ACADEMIC =================== --}}
            <section>
                <h3 class="text-sm font-semibold text-slate-700 flex items-center gap-2 mb-4">
                    <i data-lucide="graduation-cap" class="w-4 h-4 text-violet-600"></i> Academic
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">Programme <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400"><i data-lucide="graduation-cap" class="w-4 h-4"></i></span>
                            <select name="programme" required
                                class="w-full pl-9 pr-4 py-2.5 rounded-lg border border-slate-300 bg-white text-slate-800 text-sm focus:ring-2 focus:ring-violet-500 focus:border-violet-500 outline-none transition appearance-none @error('programme') border-red-300 @enderror">
                                <option value="">-- Select Programme --</option>
                                <option value="Bachelor of Computer Science (Information Security)" {{ old('programme', $student->programme) === 'Bachelor of Computer Science (Information Security)' ? 'selected' : '' }}>Bachelor of Computer Science (Information Security)</option>
                                <option value="Bachelor of Computer Science (Multimedia Computing)" {{ old('programme', $student->programme) === 'Bachelor of Computer Science (Multimedia Computing)' ? 'selected' : '' }}>Bachelor of Computer Science (Multimedia Computing)</option>
                                <option value="Bachelor of Computer Science (Software Engineering)" {{ old('programme', $student->programme) === 'Bachelor of Computer Science (Software Engineering)' ? 'selected' : '' }}>Bachelor of Computer Science (Software Engineering)</option>
                                <option value="Bachelor of Computer Science (Web Technology)" {{ old('programme', $student->programme) === 'Bachelor of Computer Science (Web Technology)' ? 'selected' : '' }}>Bachelor of Computer Science (Web Technology)</option>
                                <option value="Bachelor of Information Technology" {{ old('programme', $student->programme) === 'Bachelor of Information Technology' ? 'selected' : '' }}>Bachelor of Information Technology</option>
                            </select>
                        </div>
                        @error('programme') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">Year of Study <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400"><i data-lucide="calendar" class="w-4 h-4"></i></span>
                            <input type="number" name="year" value="{{ old('year', $student->year) }}" required min="1" max="4"
                                class="w-full pl-9 pr-4 py-2.5 rounded-lg border border-slate-300 bg-white text-slate-800 text-sm focus:ring-2 focus:ring-violet-500 focus:border-violet-500 outline-none transition @error('year') border-red-300 @enderror">
                        </div>
                        @error('year') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">Faculty</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400"><i data-lucide="building" class="w-4 h-4"></i></span>
                            <input type="text" value="FSKTM (Faculty of Computer Science and Information Technology)" disabled
                                class="w-full pl-9 pr-4 py-2.5 rounded-lg border border-slate-200 bg-slate-50 text-slate-500 text-sm cursor-not-allowed">
                        </div>
                        <p class="mt-1 text-xs text-slate-400">This system is for FSKTM students only.</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">CGPA <span class="text-slate-400 text-xs">(0.00 – 4.00)</span></label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400"><i data-lucide="bar-chart-3" class="w-4 h-4"></i></span>
                            <input type="number" name="cgpa" value="{{ old('cgpa', $student->cgpa) }}" step="0.01" min="0" max="4"
                                class="w-full pl-9 pr-4 py-2.5 rounded-lg border border-slate-300 bg-white text-slate-800 text-sm focus:ring-2 focus:ring-violet-500 focus:border-violet-500 outline-none transition @error('cgpa') border-red-300 @enderror">
                        </div>
                        @error('cgpa') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">Expected Graduation <span class="text-slate-400 text-xs">(e.g. July 2027)</span></label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400"><i data-lucide="calendar-check" class="w-4 h-4"></i></span>
                            <input type="text" name="expected_graduation" value="{{ old('expected_graduation', $student->expected_graduation) }}" maxlength="100"
                                placeholder="e.g. July 2027"
                                class="w-full pl-9 pr-4 py-2.5 rounded-lg border border-slate-300 bg-white text-slate-800 text-sm focus:ring-2 focus:ring-violet-500 focus:border-violet-500 outline-none transition @error('expected_graduation') border-red-300 @enderror">
                        </div>
                        @error('expected_graduation') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                    </div>
                </div>
            </section>

            <hr class="border-slate-200">

            {{-- =================== PERSONAL =================== --}}
            <section>
                <h3 class="text-sm font-semibold text-slate-700 flex items-center gap-2 mb-4">
                    <i data-lucide="id-card" class="w-4 h-4 text-violet-600"></i> Personal
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">IC Number</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400"><i data-lucide="credit-card" class="w-4 h-4"></i></span>
                            <input type="text" name="ic_number" value="{{ old('ic_number', $student->ic_number) }}" maxlength="20"
                                placeholder="e.g. 030512101234"
                                class="w-full pl-9 pr-4 py-2.5 rounded-lg border border-slate-300 bg-white text-slate-800 text-sm focus:ring-2 focus:ring-violet-500 focus:border-violet-500 outline-none transition @error('ic_number') border-red-300 @enderror">
                        </div>
                        @error('ic_number') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">Date of Birth</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400"><i data-lucide="cake" class="w-4 h-4"></i></span>
                            <input type="date" name="date_of_birth" value="{{ old('date_of_birth', $student->date_of_birth?->format('Y-m-d')) }}"
                                class="w-full pl-9 pr-4 py-2.5 rounded-lg border border-slate-300 bg-white text-slate-800 text-sm focus:ring-2 focus:ring-violet-500 focus:border-violet-500 outline-none transition @error('date_of_birth') border-red-300 @enderror">
                        </div>
                        @error('date_of_birth') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">Gender</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400"><i data-lucide="users" class="w-4 h-4"></i></span>
                            <select name="gender"
                                class="w-full pl-9 pr-4 py-2.5 rounded-lg border border-slate-300 bg-white text-slate-800 text-sm focus:ring-2 focus:ring-violet-500 focus:border-violet-500 outline-none transition appearance-none @error('gender') border-red-300 @enderror">
                                <option value="">--</option>
                                <option value="Male" {{ old('gender', $student->gender) === 'Male' ? 'selected' : '' }}>Male</option>
                                <option value="Female" {{ old('gender', $student->gender) === 'Female' ? 'selected' : '' }}>Female</option>
                                <option value="Prefer not to say" {{ old('gender', $student->gender) === 'Prefer not to say' ? 'selected' : '' }}>Prefer not to say</option>
                            </select>
                        </div>
                        @error('gender') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">Nationality</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400"><i data-lucide="flag" class="w-4 h-4"></i></span>
                            <input type="text" name="nationality" value="{{ old('nationality', $student->nationality ?? 'Malaysian') }}" maxlength="100"
                                class="w-full pl-9 pr-4 py-2.5 rounded-lg border border-slate-300 bg-white text-slate-800 text-sm focus:ring-2 focus:ring-violet-500 focus:border-violet-500 outline-none transition @error('nationality') border-red-300 @enderror">
                        </div>
                        @error('nationality') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">Phone</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400"><i data-lucide="phone" class="w-4 h-4"></i></span>
                            <input type="tel" name="phone" value="{{ old('phone', $student->phone) }}" maxlength="30"
                                placeholder="e.g. 0123456789"
                                class="w-full pl-9 pr-4 py-2.5 rounded-lg border border-slate-300 bg-white text-slate-800 text-sm focus:ring-2 focus:ring-violet-500 focus:border-violet-500 outline-none transition @error('phone') border-red-300 @enderror">
                        </div>
                        @error('phone') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">Address</label>
                        <textarea name="address" rows="2" maxlength="1000"
                            placeholder="e.g. 12 Jalan Cempaka, Taman Maju, 86400 Parit Raja, Johor"
                            class="w-full px-4 py-2.5 rounded-lg border border-slate-300 bg-white text-slate-800 text-sm focus:ring-2 focus:ring-violet-500 focus:border-violet-500 outline-none transition resize-none @error('address') border-red-300 @enderror">{{ old('address', $student->address) }}</textarea>
                        @error('address') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                    </div>
                </div>
            </section>

            <hr class="border-slate-200">

            {{-- =================== EMERGENCY CONTACT =================== --}}
            <section>
                <h3 class="text-sm font-semibold text-slate-700 flex items-center gap-2 mb-4">
                    <i data-lucide="phone-call" class="w-4 h-4 text-violet-600"></i> Emergency Contact
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">Contact Name</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400"><i data-lucide="user-check" class="w-4 h-4"></i></span>
                            <input type="text" name="emergency_contact_name" value="{{ old('emergency_contact_name', $student->emergency_contact_name) }}" maxlength="255"
                                placeholder="e.g. Parent / Guardian name"
                                class="w-full pl-9 pr-4 py-2.5 rounded-lg border border-slate-300 bg-white text-slate-800 text-sm focus:ring-2 focus:ring-violet-500 focus:border-violet-500 outline-none transition @error('emergency_contact_name') border-red-300 @enderror">
                        </div>
                        @error('emergency_contact_name') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">Contact Phone</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400"><i data-lucide="phone" class="w-4 h-4"></i></span>
                            <input type="tel" name="emergency_contact_phone" value="{{ old('emergency_contact_phone', $student->emergency_contact_phone) }}" maxlength="30"
                                placeholder="e.g. 0129876543"
                                class="w-full pl-9 pr-4 py-2.5 rounded-lg border border-slate-300 bg-white text-slate-800 text-sm focus:ring-2 focus:ring-violet-500 focus:border-violet-500 outline-none transition @error('emergency_contact_phone') border-red-300 @enderror">
                        </div>
                        @error('emergency_contact_phone') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                    </div>
                </div>
            </section>

            <hr class="border-slate-200">

            {{-- =================== TALENT =================== --}}
            <section>
                <h3 class="text-sm font-semibold text-slate-700 flex items-center gap-2 mb-4">
                    <i data-lucide="sparkles" class="w-4 h-4 text-violet-600"></i> Talent & Interests
                </h3>
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">Talent / Specialisation</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400"><i data-lucide="sparkles" class="w-4 h-4"></i></span>
                            <input type="text" name="talent" value="{{ old('talent', $student->talent) }}" maxlength="255"
                                placeholder="e.g. Web Development, Data Analytics, UI/UX"
                                class="w-full pl-9 pr-4 py-2.5 rounded-lg border border-slate-300 bg-white text-slate-800 text-sm focus:ring-2 focus:ring-violet-500 focus:border-violet-500 outline-none transition @error('talent') border-red-300 @enderror">
                        </div>
                        @error('talent') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">Skills <span class="text-slate-400 text-xs">(comma-separated)</span></label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400"><i data-lucide="wrench" class="w-4 h-4"></i></span>
                            <input type="text" name="skills" value="{{ old('skills', is_array($student->skills) ? implode(', ', $student->skills) : '') }}"
                                placeholder="PHP, Laravel, JavaScript"
                                class="w-full pl-9 pr-4 py-2.5 rounded-lg border border-slate-300 bg-white text-slate-800 text-sm focus:ring-2 focus:ring-violet-500 focus:border-violet-500 outline-none transition @error('skills') border-red-300 @enderror">
                        </div>
                        @error('skills') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">Interests <span class="text-slate-400 text-xs">(comma-separated)</span></label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400"><i data-lucide="heart" class="w-4 h-4"></i></span>
                            <input type="text" name="interests" value="{{ old('interests', is_array($student->interests) ? implode(', ', $student->interests) : '') }}"
                                placeholder="Hackathons, Open Source"
                                class="w-full pl-9 pr-4 py-2.5 rounded-lg border border-slate-300 bg-white text-slate-800 text-sm focus:ring-2 focus:ring-violet-500 focus:border-violet-500 outline-none transition @error('interests') border-red-300 @enderror">
                        </div>
                        @error('interests') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                    </div>
                </div>
            </section>

            <div class="flex items-center justify-end gap-3 pt-2">
                <a href="{{ route('profile') }}" class="px-5 py-2.5 rounded-lg border border-slate-300 text-slate-700 text-sm font-medium hover:bg-slate-50 transition">Cancel</a>
                <button type="submit" class="px-6 py-2.5 gradient-primary text-white text-sm font-medium rounded-lg hover:scale-[1.02] transition shadow-md flex items-center gap-2">
                    <i data-lucide="save" class="w-4 h-4"></i> Save Changes
                </button>
            </div>
        </form>
    </div>
</div>
@endif
@endsection
