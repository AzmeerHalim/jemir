<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TalentSys — FSKTM Student Talent Management System</title>
    @vite(['resources/css/app.css'])
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800&display=swap" rel="stylesheet">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        [x-cloak] { display: none !important; }
        .hero-pattern {
            background-image: radial-gradient(circle at 20% 50%, rgba(59, 130, 246, 0.06) 0%, transparent 50%),
                              radial-gradient(circle at 80% 20%, rgba(59, 130, 246, 0.04) 0%, transparent 50%),
                              radial-gradient(circle at 60% 80%, rgba(99, 102, 241, 0.04) 0%, transparent 50%);
        }
    </style>
</head>
<body class="font-sans antialiased text-gray-800 bg-white">

{{-- ======================== NAVBAR ======================== --}}
<header x-data="{ open: false }" class="fixed top-0 inset-x-0 z-50 transition-colors duration-300" :class="open ? 'bg-white shadow-lg' : 'bg-white/85 backdrop-blur-md shadow-sm'">
    <nav class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16">
            <div class="flex items-center gap-10">
                <a href="/" class="flex items-center gap-2.5 text-xl font-bold text-gray-900 tracking-tight">
                    <span class="w-9 h-9 rounded-lg bg-blue-600 text-white flex items-center justify-center text-sm font-extrabold">S</span>
                    TalentSys
                </a>
                <div class="hidden md:flex items-center gap-8 text-sm font-medium">
                    <a href="#home" class="text-gray-600 hover:text-blue-600 transition">Home</a>
                    <a href="#features" class="text-gray-600 hover:text-blue-600 transition">Features</a>
                    <a href="#about" class="text-gray-600 hover:text-blue-600 transition">About</a>
                </div>
            </div>

            <div class="hidden md:flex items-center gap-3">
                <a href="{{ route('login') }}" class="px-4 py-2 text-sm font-medium text-gray-700 hover:text-blue-600 transition rounded-lg hover:bg-gray-100">
                    Log in
                </a>
                <a href="{{ route('register') }}" class="px-4 py-2 text-sm font-semibold text-white bg-blue-600 hover:bg-blue-700 rounded-lg shadow-sm shadow-blue-600/25 transition">
                    Register
                </a>
            </div>

            <button @click="open = !open" class="md:hidden p-2 rounded-lg text-gray-600 hover:bg-gray-100 transition">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path :class="open ? 'hidden' : 'block'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    <path :class="open ? 'block' : 'hidden'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <div x-show="open" x-transition.opacity x-cloak class="md:hidden pb-4">
            <div class="flex flex-col gap-3 pt-2 border-t border-gray-100">
                <a href="#home" @click="open = false" class="px-3 py-2 text-sm font-medium text-gray-600 hover:text-blue-600 rounded-lg hover:bg-gray-50 transition">Home</a>
                <a href="#features" @click="open = false" class="px-3 py-2 text-sm font-medium text-gray-600 hover:text-blue-600 rounded-lg hover:bg-gray-50 transition">Features</a>
                <a href="#about" @click="open = false" class="px-3 py-2 text-sm font-medium text-gray-600 hover:text-blue-600 rounded-lg hover:bg-gray-50 transition">About</a>
                <div class="flex gap-3 pt-2">
                    <a href="{{ route('login') }}" class="flex-1 text-center px-4 py-2.5 text-sm font-medium text-gray-700 border border-gray-300 rounded-lg hover:bg-gray-50 transition">Log in</a>
                    <a href="{{ route('register') }}" class="flex-1 text-center px-4 py-2.5 text-sm font-semibold text-white bg-blue-600 hover:bg-blue-700 rounded-lg transition">Register</a>
                </div>
            </div>
        </div>
    </nav>
</header>

{{-- ======================== HERO ======================== --}}
<section id="home" class="hero-pattern min-h-screen flex items-center justify-center px-4 pt-16">
    <div class="max-w-4xl mx-auto text-center py-20">
        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-blue-50 border border-blue-100 text-blue-700 text-xs font-semibold mb-6">
            <span class="w-1.5 h-1.5 rounded-full bg-blue-600 animate-pulse"></span>
            FSKTM Student Affairs — UTHM
        </div>

        <h1 class="text-4xl sm:text-5xl lg:text-6xl font-extrabold text-gray-900 leading-tight tracking-tight mb-6">
            FSKTM Student Talent<br class="hidden sm:block">
            Management System
        </h1>

        <p class="max-w-2xl mx-auto text-base sm:text-lg text-gray-500 leading-relaxed mb-10">
            A centralized platform for FSKTM students to record achievements, track talent development,
            and streamline award nominations — all in one place.
        </p>

        <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
            <a href="{{ route('register') }}" class="w-full sm:w-auto px-7 py-3.5 text-base font-semibold text-white bg-blue-600 hover:bg-blue-700 rounded-xl shadow-lg shadow-blue-600/25 hover:shadow-blue-600/40 transition transform hover:-translate-y-0.5">
                Get Started
                <svg class="inline w-4 h-4 ml-1.5 -mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
            </a>
            <a href="#features" class="w-full sm:w-auto px-7 py-3.5 text-base font-semibold text-gray-700 bg-white border border-gray-200 hover:border-gray-300 hover:bg-gray-50 rounded-xl shadow-sm transition">
                Learn More
            </a>
        </div>

        <p class="mt-8 text-xs text-gray-400">
            Already have an account? <a href="{{ route('login') }}" class="text-blue-600 font-medium hover:text-blue-700">Log in</a>
        </p>
    </div>
</section>

{{-- ======================== FEATURES ======================== --}}
<section id="features" class="py-24 px-4 bg-white">
    <div class="max-w-6xl mx-auto">
        <div class="text-center mb-16">
            <span class="text-xs font-semibold tracking-widest text-blue-600 uppercase">Features</span>
            <h2 class="mt-3 text-3xl sm:text-4xl font-bold text-gray-900 tracking-tight">
                Everything you need to track talent
            </h2>
            <p class="mt-4 max-w-xl mx-auto text-gray-500">
                A complete suite of tools to manage student achievements, calculate merits, and identify top performers.
            </p>
        </div>

        <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
            <div class="group relative bg-white p-8 rounded-2xl border border-gray-100 shadow-sm hover:shadow-lg hover:border-gray-200 transition duration-300">
                <div class="w-12 h-12 rounded-xl bg-blue-50 flex items-center justify-center mb-5 group-hover:bg-blue-100 transition">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Achievement Tracking</h3>
                <p class="text-sm text-gray-500 leading-relaxed">Upload and manage academic and co-curricular achievements with certificate verification and multi-stage approval.</p>
            </div>

            <div class="group relative bg-white p-8 rounded-2xl border border-gray-100 shadow-sm hover:shadow-lg hover:border-gray-200 transition duration-300">
                <div class="w-12 h-12 rounded-xl bg-emerald-50 flex items-center justify-center mb-5 group-hover:bg-emerald-100 transition">
                    <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Automated Merit Scoring</h3>
                <p class="text-sm text-gray-500 leading-relaxed">Automatic score calculation based on achievement level — faculty, university, national, or international.</p>
            </div>

            <div class="group relative bg-white p-8 rounded-2xl border border-gray-100 shadow-sm hover:shadow-lg hover:border-gray-200 transition duration-300">
                <div class="w-12 h-12 rounded-xl bg-amber-50 flex items-center justify-center mb-5 group-hover:bg-amber-100 transition">
                    <svg class="w-6 h-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 21v-4m0 0V5a2 2 0 012-2h6.5l1 1H21l-3 6 3 6h-8.5l-1-1H5a2 2 0 00-2 2zm9-13.5V9"/>
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Leaderboard & Rankings</h3>
                <p class="text-sm text-gray-500 leading-relaxed">Real-time leaderboard with category and programme filters to identify top-performing students instantly.</p>
            </div>

            <div class="group relative bg-white p-8 rounded-2xl border border-gray-100 shadow-sm hover:shadow-lg hover:border-gray-200 transition duration-300">
                <div class="w-12 h-12 rounded-xl bg-indigo-50 flex items-center justify-center mb-5 group-hover:bg-indigo-100 transition">
                    <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Activity Management</h3>
                <p class="text-sm text-gray-500 leading-relaxed">ITC administrators can create and manage activities, set registration deadlines, and track student participation.</p>
            </div>

            <div class="group relative bg-white p-8 rounded-2xl border border-gray-100 shadow-sm hover:shadow-lg hover:border-gray-200 transition duration-300">
                <div class="w-12 h-12 rounded-xl bg-rose-50 flex items-center justify-center mb-5 group-hover:bg-rose-100 transition">
                    <svg class="w-6 h-6 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Two-Stage Approval</h3>
                <p class="text-sm text-gray-500 leading-relaxed">Robust verification workflow with lecturer review followed by coordinator final approval for every submission.</p>
            </div>

            <div class="group relative bg-white p-8 rounded-2xl border border-gray-100 shadow-sm hover:shadow-lg hover:border-gray-200 transition duration-300">
                <div class="w-12 h-12 rounded-xl bg-sky-50 flex items-center justify-center mb-5 group-hover:bg-sky-100 transition">
                    <svg class="w-6 h-6 text-sky-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Multi-Role Access</h3>
                <p class="text-sm text-gray-500 leading-relaxed">Six distinct roles — Student, Coordinator, Lecturer, ITC Admin, Committee, and Student Affairs — each with tailored dashboards.</p>
            </div>
        </div>
    </div>
</section>

{{-- ======================== ABOUT ======================== --}}
<section id="about" class="py-24 px-4 bg-gray-50">
    <div class="max-w-6xl mx-auto">
        <div class="grid lg:grid-cols-2 gap-16 items-center">
            <div>
                <span class="text-xs font-semibold tracking-widest text-blue-600 uppercase">About</span>
                <h2 class="mt-3 text-3xl sm:text-4xl font-bold text-gray-900 tracking-tight">
                    Empowering student talent at FSKTM
                </h2>
                <p class="mt-6 text-gray-500 leading-relaxed">
                    The FSKTM Student Talent Management System (TalentSys) was developed for
                    <span class="font-medium text-gray-700">HEPA FSKTM</span> to streamline the
                    collection, verification, and reporting of student achievements at the
                    Faculty of Computer Science and Information Technology, UTHM.
                </p>
                <p class="mt-4 text-gray-500 leading-relaxed">
                    The platform supports the full lifecycle — from achievement submission and
                    lecturer verification to coordinator approval and automated merit scoring.
                    It also facilitates award nominations by identifying top-performing students
                    through a real-time leaderboard.
                </p>
                <div class="mt-8 grid grid-cols-2 sm:grid-cols-3 gap-6">
                    <div>
                        <p class="text-2xl font-bold text-gray-900">6</p>
                        <p class="text-sm text-gray-500 mt-1">User Roles</p>
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-gray-900">2</p>
                        <p class="text-sm text-gray-500 mt-1">Approval Stages</p>
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-gray-900">4</p>
                        <p class="text-sm text-gray-500 mt-1">Achievement Levels</p>
                    </div>
                </div>
            </div>

            <div class="relative">
                <div class="aspect-square max-w-md mx-auto rounded-2xl bg-white border border-gray-200 shadow-lg p-8">
                    <div class="space-y-5">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-lg bg-blue-50 flex items-center justify-center">
                                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm font-semibold text-gray-900">Student</p>
                                <p class="text-xs text-gray-500">Submit achievements, join activities</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-lg bg-emerald-50 flex items-center justify-center">
                                <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm font-semibold text-gray-900">Lecturer</p>
                                <p class="text-xs text-gray-500">First-stage verification</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-lg bg-amber-50 flex items-center justify-center">
                                <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/>
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm font-semibold text-gray-900">Coordinator</p>
                                <p class="text-xs text-gray-500">Final approval & management</p>
                            </div>
                        </div>
                        <div class="border-t border-gray-100 pt-4">
                            <div class="flex items-center justify-between text-sm">
                                <span class="text-gray-500">ITC Admin</span>
                                <span class="text-gray-400">Activity Management</span>
                            </div>
                            <div class="flex items-center justify-between text-sm mt-2">
                                <span class="text-gray-500">Committee</span>
                                <span class="text-gray-400">Search & Review</span>
                            </div>
                            <div class="flex items-center justify-between text-sm mt-2">
                                <span class="text-gray-500">Student Affairs</span>
                                <span class="text-gray-400">Reports & Oversight</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ======================== FOOTER ======================== --}}
<footer class="bg-gray-900 text-gray-400">
    <div class="max-w-6xl mx-auto px-4 py-16">
        <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-10">
            <div class="lg:col-span-2">
                <div class="flex items-center gap-2.5 mb-4">
                    <span class="w-8 h-8 rounded-lg bg-blue-600 text-white flex items-center justify-center text-xs font-extrabold">S</span>
                    <span class="text-lg font-bold text-white">TalentSys</span>
                </div>
                <p class="text-sm leading-relaxed max-w-sm">
                    FSKTM Student Talent Management System — a platform by HEPA FSKTM, Universiti Tun Hussein Onn Malaysia.
                </p>
            </div>

            <div>
                <h4 class="text-sm font-semibold text-white mb-4">Quick Links</h4>
                <ul class="space-y-2.5 text-sm">
                    <li><a href="#home" class="hover:text-white transition">Home</a></li>
                    <li><a href="#features" class="hover:text-white transition">Features</a></li>
                    <li><a href="#about" class="hover:text-white transition">About</a></li>
                    <li><a href="{{ route('login') }}" class="hover:text-white transition">Login</a></li>
                    <li><a href="{{ route('register') }}" class="hover:text-white transition">Register</a></li>
                </ul>
            </div>

            <div>
                <h4 class="text-sm font-semibold text-white mb-4">Contact</h4>
                <ul class="space-y-2.5 text-sm">
                    <li>FSKTM, UTHM</li>
                    <li>Parit Raja, Batu Pahat</li>
                    <li>Johor, Malaysia</li>
                </ul>
            </div>
        </div>

        <div class="mt-12 pt-8 border-t border-gray-800 flex flex-col sm:flex-row items-center justify-between gap-4 text-sm">
            <p>&copy; {{ date('Y') }} TalentSys. All rights reserved.</p>
            <p>Student Affairs — FSKTM UTHM</p>
        </div>
    </div>
</footer>

</body>
</html>
