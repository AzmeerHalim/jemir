<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register — TalentSys</title>
    @vite(['resources/css/app.css'])
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.min.js"></script>
    <script defer>
        document.addEventListener('DOMContentLoaded', function () {
            if (typeof lucide !== 'undefined') lucide.createIcons();
        });
    </script>
</head>
<body class="min-h-screen flex items-center justify-center bg-gradient-to-br from-violet-100 via-blue-50 to-indigo-100 relative overflow-x-hidden py-10">

    <div class="absolute top-20 -left-20 w-72 h-72 bg-violet-300/30 rounded-full blur-3xl animate-pulse"></div>
    <div class="absolute bottom-20 -right-20 w-96 h-96 bg-blue-300/30 rounded-full blur-3xl animate-pulse" style="animation-delay: 1s;"></div>
    <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-64 h-64 bg-indigo-300/20 rounded-full blur-3xl animate-pulse" style="animation-delay: 2s;"></div>

    <div class="relative w-full max-w-3xl mx-4 my-auto">
        <div class="bg-white/80 backdrop-blur-xl rounded-2xl shadow-xl border border-white/20 p-8">

            <div class="flex justify-center mb-6">
                <div class="w-16 h-16 rounded-2xl gradient-primary flex items-center justify-center text-white shadow-lg">
                    <i data-lucide="graduation-cap" class="w-8 h-8"></i>
                </div>
            </div>

            <h2 class="text-2xl font-bold text-center text-slate-900">Create a Student Account</h2>
            <p class="text-sm text-center text-slate-500 mt-1">TalentSys — FSKTM Student Talent Management System</p>
            <p class="text-xs text-center text-slate-400 mt-0.5">Student Affairs FSKTM</p>
            <p class="text-xs text-center text-slate-400 mt-3 max-w-xl mx-auto">
                This page registers <strong>Student</strong> accounts only. Staff accounts (Coordinator, Lecturer, ITC Admin, Committee, Student Affairs) are created by the Talent Unit Coordinator.
            </p>

            <form method="POST" action="{{ route('register') }}" class="mt-8 space-y-8">
                @csrf

                {{-- =================== ACCOUNT =================== --}}
                <section>
                    <h3 class="text-sm font-semibold text-slate-700 flex items-center gap-2 mb-4">
                        <i data-lucide="user-round" class="w-4 h-4 text-violet-600"></i> Account
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1.5">Full Name <span class="text-red-500">*</span></label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400"><i data-lucide="user" class="w-4 h-4"></i></span>
                                <input type="text" name="name" value="{{ old('name') }}" required autofocus
                                    class="w-full pl-9 pr-4 py-2.5 rounded-lg border border-slate-300 bg-white text-sm focus:ring-2 focus:ring-violet-500 focus:border-violet-500 outline-none @error('name') border-red-300 @enderror">
                            </div>
                            @error('name') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1.5">Username <span class="text-red-500">*</span></label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400"><i data-lucide="at-sign" class="w-4 h-4"></i></span>
                                <input type="text" name="username" value="{{ old('username') }}" required pattern="[a-zA-Z0-9_.\-]+"
                                    placeholder="e.g. ahmad2024"
                                    class="w-full pl-9 pr-4 py-2.5 rounded-lg border border-slate-300 bg-white text-sm focus:ring-2 focus:ring-violet-500 focus:border-violet-500 outline-none @error('username') border-red-300 @enderror">
                            </div>
                            <p class="mt-1 text-xs text-slate-400">Letters, numbers, dot, dash, underscore. Must be unique.</p>
                            @error('username') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-slate-700 mb-1.5">Email <span class="text-red-500">*</span></label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400"><i data-lucide="mail" class="w-4 h-4"></i></span>
                                <input type="email" name="email" value="{{ old('email') }}" required
                                    placeholder="you@example.com"
                                    class="w-full pl-9 pr-4 py-2.5 rounded-lg border border-slate-300 bg-white text-sm focus:ring-2 focus:ring-violet-500 focus:border-violet-500 outline-none @error('email') border-red-300 @enderror">
                            </div>
                            @error('email') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1.5">Password <span class="text-red-500">*</span></label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400"><i data-lucide="lock" class="w-4 h-4"></i></span>
                                <input type="password" name="password" required
                                    class="w-full pl-9 pr-4 py-2.5 rounded-lg border border-slate-300 bg-white text-sm focus:ring-2 focus:ring-violet-500 focus:border-violet-500 outline-none @error('password') border-red-300 @enderror">
                            </div>
                            <p class="mt-1 text-xs text-slate-400">Minimum 8 characters.</p>
                            @error('password') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1.5">Confirm Password <span class="text-red-500">*</span></label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400"><i data-lucide="lock-keyhole" class="w-4 h-4"></i></span>
                                <input type="password" name="password_confirmation" required
                                    class="w-full pl-9 pr-4 py-2.5 rounded-lg border border-slate-300 bg-white text-sm focus:ring-2 focus:ring-violet-500 focus:border-violet-500 outline-none @error('password_confirmation') border-red-300 @enderror">
                            </div>
                            @error('password_confirmation') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </section>

                <hr class="border-slate-200">

                {{-- =================== ACADEMIC =================== --}}
                <section>
                    <h3 class="text-sm font-semibold text-slate-700 flex items-center gap-2 mb-4">
                        <i data-lucide="book-open" class="w-4 h-4 text-violet-600"></i> Academic
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1.5">Matric Number <span class="text-red-500">*</span></label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400"><i data-lucide="hash" class="w-4 h-4"></i></span>
                                <input type="text" name="matric_no" value="{{ old('matric_no') }}" required
                                    placeholder="e.g. A123456"
                                    class="w-full pl-9 pr-4 py-2.5 rounded-lg border border-slate-300 bg-white text-sm focus:ring-2 focus:ring-violet-500 focus:border-violet-500 outline-none @error('matric_no') border-red-300 @enderror">
                            </div>
                            @error('matric_no') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                        </div>

                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-1.5">Programme <span class="text-red-500">*</span></label>
                                <div class="relative">
                                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400"><i data-lucide="graduation-cap" class="w-4 h-4"></i></span>
                                    <select name="programme" required
                                        class="w-full pl-9 pr-4 py-2.5 rounded-lg border border-slate-300 bg-white text-slate-800 text-sm focus:ring-2 focus:ring-violet-500 focus:border-violet-500 outline-none transition appearance-none @error('programme') border-red-300 @enderror">
                                        <option value="">-- Select Programme --</option>
                                        <option value="Bachelor of Computer Science (Information Security)" {{ old('programme') === 'Bachelor of Computer Science (Information Security)' ? 'selected' : '' }}>Bachelor of Computer Science (Information Security)</option>
                                        <option value="Bachelor of Computer Science (Multimedia Computing)" {{ old('programme') === 'Bachelor of Computer Science (Multimedia Computing)' ? 'selected' : '' }}>Bachelor of Computer Science (Multimedia Computing)</option>
                                        <option value="Bachelor of Computer Science (Software Engineering)" {{ old('programme') === 'Bachelor of Computer Science (Software Engineering)' ? 'selected' : '' }}>Bachelor of Computer Science (Software Engineering)</option>
                                        <option value="Bachelor of Computer Science (Web Technology)" {{ old('programme') === 'Bachelor of Computer Science (Web Technology)' ? 'selected' : '' }}>Bachelor of Computer Science (Web Technology)</option>
                                        <option value="Bachelor of Information Technology" {{ old('programme') === 'Bachelor of Information Technology' ? 'selected' : '' }}>Bachelor of Information Technology</option>
                                    </select>
                                </div>
                                @error('programme') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                            </div>
                            @error('programme') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1.5">Year of Study <span class="text-red-500">*</span></label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400"><i data-lucide="calendar" class="w-4 h-4"></i></span>
                                <input type="number" name="year" value="{{ old('year') }}" required min="1" max="4"
                                    class="w-full pl-9 pr-4 py-2.5 rounded-lg border border-slate-300 bg-white text-sm focus:ring-2 focus:ring-violet-500 focus:border-violet-500 outline-none @error('year') border-red-300 @enderror">
                            </div>
                            @error('year') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1.5">Faculty</label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400"><i data-lucide="building" class="w-4 h-4"></i></span>
                                <input type="text" name="faculty_display" value="FSKTM (Faculty of Computer Science and Information Technology)" disabled
                                    class="w-full pl-9 pr-4 py-2.5 rounded-lg border border-slate-200 bg-slate-50 text-slate-500 text-sm cursor-not-allowed">
                            </div>
                            <p class="mt-1 text-xs text-slate-400">This system is for FSKTM students only.</p>
                        </div>

                        @if(isset($clusters) && $clusters->isNotEmpty())
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-slate-700 mb-1.5">Talent Cluster <span class="text-slate-400 text-xs">(optional)</span></label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400"><i data-lucide="layers" class="w-4 h-4"></i></span>
                                <select name="cluster_id"
                                    class="w-full pl-9 pr-4 py-2.5 rounded-lg border border-slate-300 bg-white text-sm focus:ring-2 focus:ring-violet-500 focus:border-violet-500 outline-none appearance-none @error('cluster_id') border-red-300 @enderror">
                                    <option value="">-- Select Cluster --</option>
                                    @foreach($clusters as $cluster)
                                        <option value="{{ $cluster->id }}" {{ (string) old('cluster_id') === (string) $cluster->id ? 'selected' : '' }}>{{ $cluster->cluster_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <p class="mt-1 text-xs text-slate-400">Used for cluster-based leaderboard filtering.</p>
                            @error('cluster_id') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                        </div>
                        @endif
                    </div>
                </section>

                <hr class="border-slate-200">

                {{-- =================== PERSONAL =================== --}}
                <section>
                    <h3 class="text-sm font-semibold text-slate-700 flex items-center gap-2 mb-4">
                        <i data-lucide="id-card" class="w-4 h-4 text-violet-600"></i> Personal <span class="text-xs font-normal text-slate-400">(optional, can be filled later)</span>
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1.5">Gender</label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400"><i data-lucide="users" class="w-4 h-4"></i></span>
                                <select name="gender"
                                    class="w-full pl-9 pr-4 py-2.5 rounded-lg border border-slate-300 bg-white text-sm focus:ring-2 focus:ring-violet-500 focus:border-violet-500 outline-none appearance-none @error('gender') border-red-300 @enderror">
                                    <option value="">--</option>
                                    <option value="Male" {{ old('gender') === 'Male' ? 'selected' : '' }}>Male</option>
                                    <option value="Female" {{ old('gender') === 'Female' ? 'selected' : '' }}>Female</option>
                                    <option value="Prefer not to say" {{ old('gender') === 'Prefer not to say' ? 'selected' : '' }}>Prefer not to say</option>
                                </select>
                            </div>
                            @error('gender') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1.5">Date of Birth</label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400"><i data-lucide="cake" class="w-4 h-4"></i></span>
                                <input type="date" name="date_of_birth" value="{{ old('date_of_birth') }}"
                                    class="w-full pl-9 pr-4 py-2.5 rounded-lg border border-slate-300 bg-white text-sm focus:ring-2 focus:ring-violet-500 focus:border-violet-500 outline-none @error('date_of_birth') border-red-300 @enderror">
                            </div>
                            @error('date_of_birth') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1.5">Nationality</label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400"><i data-lucide="flag" class="w-4 h-4"></i></span>
                                <input type="text" name="nationality" value="{{ old('nationality', 'Malaysian') }}"
                                    class="w-full pl-9 pr-4 py-2.5 rounded-lg border border-slate-300 bg-white text-sm focus:ring-2 focus:ring-violet-500 focus:border-violet-500 outline-none @error('nationality') border-red-300 @enderror">
                            </div>
                            @error('nationality') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1.5">IC Number</label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400"><i data-lucide="credit-card" class="w-4 h-4"></i></span>
                                <input type="text" name="ic_number" value="{{ old('ic_number') }}" maxlength="20"
                                    placeholder="e.g. 010101010101"
                                    class="w-full pl-9 pr-4 py-2.5 rounded-lg border border-slate-300 bg-white text-sm focus:ring-2 focus:ring-violet-500 focus:border-violet-500 outline-none @error('ic_number') border-red-300 @enderror">
                            </div>
                            @error('ic_number') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1.5">Phone</label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400"><i data-lucide="phone" class="w-4 h-4"></i></span>
                                <input type="tel" name="phone" value="{{ old('phone') }}" maxlength="30"
                                    placeholder="e.g. 0123456789"
                                    class="w-full pl-9 pr-4 py-2.5 rounded-lg border border-slate-300 bg-white text-sm focus:ring-2 focus:ring-violet-500 focus:border-violet-500 outline-none @error('phone') border-red-300 @enderror">
                            </div>
                            @error('phone') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-slate-700 mb-1.5">Address</label>
                            <textarea name="address" rows="2"
                                class="w-full px-4 py-2.5 rounded-lg border border-slate-300 bg-white text-sm focus:ring-2 focus:ring-violet-500 focus:border-violet-500 outline-none @error('address') border-red-300 @enderror">{{ old('address') }}</textarea>
                            @error('address') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-slate-700 mb-1.5">Talent / Interest</label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400"><i data-lucide="sparkles" class="w-4 h-4"></i></span>
                                <input type="text" name="talent" value="{{ old('talent') }}"
                                    placeholder="e.g. Web Development"
                                    class="w-full pl-9 pr-4 py-2.5 rounded-lg border border-slate-300 bg-white text-sm focus:ring-2 focus:ring-violet-500 focus:border-violet-500 outline-none @error('talent') border-red-300 @enderror">
                            </div>
                            @error('talent') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1.5">Skills <span class="text-slate-400 text-xs">(comma-separated)</span></label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400"><i data-lucide="wrench" class="w-4 h-4"></i></span>
                                <input type="text" name="skills" value="{{ old('skills') }}"
                                    placeholder="PHP, Laravel, JavaScript"
                                    class="w-full pl-9 pr-4 py-2.5 rounded-lg border border-slate-300 bg-white text-sm focus:ring-2 focus:ring-violet-500 focus:border-violet-500 outline-none @error('skills') border-red-300 @enderror">
                            </div>
                            @error('skills') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1.5">Interests <span class="text-slate-400 text-xs">(comma-separated)</span></label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400"><i data-lucide="heart" class="w-4 h-4"></i></span>
                                <input type="text" name="interests" value="{{ old('interests') }}"
                                    placeholder="Hackathons, Open Source"
                                    class="w-full pl-9 pr-4 py-2.5 rounded-lg border border-slate-300 bg-white text-sm focus:ring-2 focus:ring-violet-500 focus:border-violet-500 outline-none @error('interests') border-red-300 @enderror">
                            </div>
                            @error('interests') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </section>

                <div class="flex items-center justify-between pt-2">
                    <p class="text-xs text-slate-500">
                        Faculty: <span class="font-semibold text-slate-700">FSKTM</span>
                    </p>
                    <button type="submit"
                        class="px-6 py-2.5 gradient-primary text-white font-semibold rounded-lg hover:scale-[1.02] transition shadow-md flex items-center gap-2">
                        <i data-lucide="user-plus" class="w-4 h-4"></i> Create Student Account
                    </button>
                </div>
            </form>

            <p class="mt-6 text-center text-sm text-slate-500">
                Already have an account?
                <a href="{{ route('login') }}" class="text-violet-600 hover:text-violet-700 font-medium">Login</a>
            </p>

            <p class="mt-6 text-xs text-center text-slate-400">
                &copy; {{ date('Y') }} TalentSys — FSKTM Student Affairs
            </p>

        </div>
    </div>

</body>
</html>
