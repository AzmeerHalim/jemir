<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Dashboard') — TalentSys</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script defer>
        document.addEventListener('DOMContentLoaded', function () {
            if (typeof lucide !== 'undefined') lucide.createIcons();
        });
    </script>
</head>
<body class="bg-slate-50 text-slate-800 font-sans antialiased">

<div x-data="{ sidebarOpen: true, profileOpen: false }" class="flex min-h-screen">

    <!-- SIDEBAR OVERLAY (mobile) -->
    <div x-show="sidebarOpen" x-cloak @@click="sidebarOpen = false"
         class="fixed inset-0 z-20 bg-black/50 lg:hidden"></div>

    <!-- SIDEBAR -->
    <aside x-show="sidebarOpen" x-cloak
           class="fixed inset-y-0 left-0 z-30 w-64 bg-white border-r border-slate-200 flex flex-col transition-all duration-300 lg:static lg:inset-auto lg:translate-x-0">

        <!-- LOGO -->
        <div class="p-6 border-b border-slate-100">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-lg gradient-primary flex items-center justify-center text-white shadow-md">
                    <i data-lucide="graduation-cap" class="w-5 h-5"></i>
                </div>
                <div>
                    <h1 class="text-lg font-bold text-slate-900">TalentSys</h1>
                    <p class="text-xs text-slate-500">
                        @php $role = auth()->user()->role; @endphp
                        {{ $role?->portal() ?? 'Portal' }}
                    </p>
                </div>
            </div>
        </div>

        <!-- NAVIGATION -->
        <nav class="flex-1 px-4 py-6 space-y-1 overflow-y-auto">
            @if(auth()->user()->role === \App\Enums\UserRole::Coordinator)
                <x-nav-link-talentsys href="{{ route('coordinator.dashboard') }}" active="{{ request()->routeIs('coordinator.dashboard') }}" icon="layout-dashboard" label="Dashboard" />
                <x-nav-link-talentsys href="{{ route('coordinator.approvals') }}" active="{{ request()->routeIs('coordinator.approvals*') }}" icon="check-circle" label="Approvals" />
                <x-nav-link-talentsys href="{{ route('coordinator.students') }}" active="{{ request()->routeIs('coordinator.students*') }}" icon="users" label="Students" />
                <x-nav-link-talentsys href="{{ route('coordinator.users.index') }}" active="{{ request()->routeIs('coordinator.users*') }}" icon="user-cog" label="Users" />
                <x-nav-link-talentsys href="{{ route('coordinator.categories') }}" active="{{ request()->routeIs('coordinator.categories') }}" icon="folder-tree" label="Categories" />
                <x-nav-link-talentsys href="{{ route('coordinator.awards.index') }}" active="{{ request()->routeIs('coordinator.awards*') }}" icon="award" label="Awards" />
                <x-nav-link-talentsys href="{{ route('coordinator.reports') }}" active="{{ request()->routeIs('coordinator.reports') }}" icon="bar-chart-3" label="Reports" />
                <x-nav-link-talentsys href="{{ route('coordinator.announcements.index') }}" active="{{ request()->routeIs('coordinator.announcements*') }}" icon="megaphone" label="Announcements" />
                <x-nav-link-talentsys href="{{ route('coordinator.settings') }}" active="{{ request()->routeIs('coordinator.settings') }}" icon="settings" label="Settings" />
            @elseif(auth()->user()->role === \App\Enums\UserRole::Lecturer)
                <x-nav-link-talentsys href="{{ route('lecturer.dashboard') }}" active="{{ request()->routeIs('lecturer.dashboard') }}" icon="layout-dashboard" label="Dashboard" />
                <x-nav-link-talentsys href="{{ route('lecturer.verifications.index') }}" active="{{ request()->routeIs('lecturer.verifications.index') }}" icon="clipboard-check" label="Participations" />
                <x-nav-link-talentsys href="{{ route('lecturer.achievement-verifications.index') }}" active="{{ request()->routeIs('lecturer.achievement-verifications*') }}" icon="trophy" label="Achievements" />
                <x-nav-link-talentsys href="{{ route('lecturer.verifications.history') }}" active="{{ request()->routeIs('lecturer.verifications.history') || request()->routeIs('lecturer.achievement-verifications.history') }}" icon="history" label="History" />
            @elseif(auth()->user()->role === \App\Enums\UserRole::ItcAdmin)
                <x-nav-link-talentsys href="{{ route('itc.dashboard') }}" active="{{ request()->routeIs('itc.dashboard') }}" icon="layout-dashboard" label="Dashboard" />
                <x-nav-link-talentsys href="{{ route('itc.activities.index') }}" active="{{ request()->routeIs('itc.activities*') }}" icon="calendar" label="Activities" />
                <x-nav-link-talentsys href="{{ route('itc.clusters.index') }}" active="{{ request()->routeIs('itc.clusters*') }}" icon="layers" label="Clusters" />
            @elseif(auth()->user()->role === \App\Enums\UserRole::Committee)
                <x-nav-link-talentsys href="{{ route('committee.dashboard') }}" active="{{ request()->routeIs('committee.dashboard') }}" icon="layout-dashboard" label="Dashboard" />
                <x-nav-link-talentsys href="{{ route('committee.search') }}" active="{{ request()->routeIs('committee.search') }}" icon="search" label="Search Talents" />
                <x-nav-link-talentsys href="{{ route('leaderboard') }}" active="{{ request()->routeIs('leaderboard') }}" icon="medal" label="Leaderboard" />
                <x-nav-link-talentsys href="{{ route('management.reports.achievements') }}" active="{{ request()->routeIs('management.reports.*') }}" icon="bar-chart-3" label="Reports" />
            @elseif(auth()->user()->role === \App\Enums\UserRole::StudentAffairs)
                <x-nav-link-talentsys href="{{ route('affairs.dashboard') }}" active="{{ request()->routeIs('affairs.dashboard') }}" icon="layout-dashboard" label="Dashboard" />
                <x-nav-link-talentsys href="{{ route('management.reports.achievements') }}" active="{{ request()->routeIs('management.reports.achievements') }}" icon="trophy" label="Achievements" />
                <x-nav-link-talentsys href="{{ route('management.reports.students') }}" active="{{ request()->routeIs('management.reports.students') }}" icon="users" label="Students" />
                <x-nav-link-talentsys href="{{ route('leaderboard') }}" active="{{ request()->routeIs('leaderboard') }}" icon="medal" label="Leaderboard" />
            @else
                <x-nav-link-talentsys href="{{ route('student.dashboard') }}" active="{{ request()->routeIs('student.dashboard') }}" icon="layout-dashboard" label="Dashboard" />
                <x-nav-link-talentsys href="{{ route('student.activities.index') }}" active="{{ request()->routeIs('student.activities*') }}" icon="calendar" label="Activities" />
                <x-nav-link-talentsys href="{{ route('student.participations.index') }}" active="{{ request()->routeIs('student.participations*') }}" icon="clipboard-list" label="Participations" />
                <x-nav-link-talentsys href="{{ route('achievements.index') }}" active="{{ request()->routeIs('achievements.*') }}" icon="trophy" label="My Achievements" />
                <x-nav-link-talentsys href="{{ route('leaderboard') }}" active="{{ request()->routeIs('leaderboard') }}" icon="medal" label="Leaderboard" />
                <x-nav-link-talentsys href="{{ route('profile') }}" active="{{ request()->routeIs('profile') }}" icon="user" label="My Profile" />
                <x-nav-link-talentsys href="{{ route('history') }}" active="{{ request()->routeIs('history') }}" icon="history" label="History" />
                <x-nav-link-talentsys href="{{ route('student.announcements.index') }}" active="{{ request()->routeIs('student.announcements*') }}" icon="megaphone" label="Announcements" />
            @endif
        </nav>

        <!-- USER FOOTER -->
        <div class="p-4 border-t border-slate-100">
            <div class="flex items-center gap-3 mb-3">
                <div class="w-9 h-9 rounded-full gradient-primary flex items-center justify-center text-white text-sm font-semibold shadow">
                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-slate-800 truncate">{{ auth()->user()->name }}</p>
                    <p class="text-xs text-slate-400">{{ $role?->label() ?? 'User' }}</p>
                </div>
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit"
                    class="w-full flex items-center gap-2 px-4 py-2.5 text-sm font-medium text-slate-600 rounded-lg hover:bg-red-50 hover:text-red-600 transition">
                    <i data-lucide="log-out" class="w-4 h-4"></i>
                    <span>Logout</span>
                </button>
            </form>
        </div>

    </aside>

    <!-- MAIN CONTENT -->
    <div class="flex-1 flex flex-col min-h-screen lg:ml-0">

        <!-- TOPBAR -->
        <header class="sticky top-0 z-10 h-16 bg-white border-b border-slate-200 flex items-center justify-between px-6">
            <div class="flex items-center gap-4">
                <!-- Toggle -->
                <button @@click="sidebarOpen = !sidebarOpen"
                    class="p-2 text-slate-500 hover:bg-slate-100 rounded-lg transition">
                    <i data-lucide="menu" class="w-5 h-5"></i>
                </button>

                <!-- Page Title -->
                <h1 class="text-lg font-semibold text-slate-800">
                    @yield('title', 'Dashboard')
                </h1>
            </div>

            <div class="flex items-center gap-4">
                <!-- SEARCH -->
                <div class="hidden md:flex items-center gap-2 px-3 py-1.5 bg-slate-100 rounded-full text-sm">
                    <i data-lucide="search" class="w-4 h-4 text-slate-400"></i>
                    <input type="text" placeholder="Search..."
                        class="bg-transparent border-none outline-none text-slate-600 placeholder-slate-400 w-40">
                </div>

                <!-- NOTIFICATION -->
                @if(auth()->user()->role === \App\Enums\UserRole::Coordinator)
                <div class="flex items-center gap-1">
                    <a href="{{ route('coordinator.approvals') }}"
                       title="Achievement approvals"
                       class="relative p-2 text-slate-500 hover:bg-slate-100 rounded-lg transition">
                        <i data-lucide="award" class="w-5 h-5"></i>
                        @php $achPending = \App\Models\Achievement::where('status', 'pending')->count(); @endphp
                        @if($achPending > 0)
                        <span class="absolute -top-0.5 -right-0.5 w-4 h-4 bg-red-500 text-white text-[10px] font-bold rounded-full flex items-center justify-center">
                            {{ $achPending }}
                        </span>
                        @endif
                    </a>
                    <a href="{{ route('coordinator.participation-approvals.index') }}"
                       title="Participation approvals"
                       class="relative p-2 text-slate-500 hover:bg-slate-100 rounded-lg transition">
                        <i data-lucide="clipboard-check" class="w-5 h-5"></i>
                        @php $partPending = \App\Models\Participation::where('lecturer_status', 'verified')->where('coordinator_status', 'pending')->count(); @endphp
                        @if($partPending > 0)
                        <span class="absolute -top-0.5 -right-0.5 w-4 h-4 bg-amber-500 text-white text-[10px] font-bold rounded-full flex items-center justify-center">
                            {{ $partPending }}
                        </span>
                        @endif
                    </a>
                </div>
                @elseif(auth()->user()->role === \App\Enums\UserRole::Student && Route::has('student.notifications.index'))
                <a href="{{ route('student.notifications.index') }}"
                   class="relative p-2 text-slate-500 hover:bg-slate-100 rounded-lg transition">
                    <i data-lucide="bell" class="w-5 h-5"></i>
                    @php $unread = auth()->user()->unreadNotificationsCount(); @endphp
                    @if($unread > 0)
                    <span class="absolute -top-0.5 -right-0.5 w-4 h-4 bg-red-500 text-white text-[10px] font-bold rounded-full flex items-center justify-center">
                        {{ $unread }}
                    </span>
                    @endif
                </a>
                @elseif(auth()->user()->role === \App\Enums\UserRole::Lecturer)
                <div class="flex items-center gap-1">
                    <a href="{{ route('lecturer.verifications.index') }}"
                       title="Pending participations"
                       class="relative p-2 text-slate-500 hover:bg-slate-100 rounded-lg transition">
                        <i data-lucide="clipboard-check" class="w-5 h-5"></i>
                        @php
                            $lecturerProfileId = auth()->user()->accompanyingLecturer?->id;
                            $lecturerPending = $lecturerProfileId
                                ? \App\Models\Participation::where('accompanying_id', $lecturerProfileId)->where('lecturer_status', 'pending')->count()
                                : 0;
                        @endphp
                        @if($lecturerPending > 0)
                        <span class="absolute -top-0.5 -right-0.5 w-4 h-4 bg-red-500 text-white text-[10px] font-bold rounded-full flex items-center justify-center">
                            {{ $lecturerPending }}
                        </span>
                        @endif
                    </a>
                    <a href="{{ route('lecturer.achievement-verifications.index') }}"
                       title="Pending achievement verifications"
                       class="relative p-2 text-slate-500 hover:bg-slate-100 rounded-lg transition">
                        <i data-lucide="trophy" class="w-5 h-5"></i>
                        @php
                            $lecturerAchievementPending = $lecturerProfileId
                                ? \App\Models\Achievement::whereNull('participation_id')->whereNotNull('accompanying_id')->where('accompanying_id', $lecturerProfileId)->where('lecturer_status', 'pending')->count()
                                : 0;
                        @endphp
                        @if($lecturerAchievementPending > 0)
                        <span class="absolute -top-0.5 -right-0.5 w-4 h-4 bg-amber-500 text-white text-[10px] font-bold rounded-full flex items-center justify-center">
                            {{ $lecturerAchievementPending }}
                        </span>
                        @endif
                    </a>
                </div>
                @endif

                <!-- USER DROPDOWN -->
                <div class="relative" @@click.outside="profileOpen = false">
                    <button @@click="profileOpen = !profileOpen"
                        class="w-10 h-10 rounded-full gradient-primary flex items-center justify-center text-white text-sm font-semibold shadow hover:opacity-90 transition">
                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                    </button>

                    <div x-show="profileOpen" x-cloak
                         @@click.outside="profileOpen = false"
                         class="absolute right-0 mt-2 w-56 bg-white rounded-xl shadow-lg border border-slate-200 py-2 z-50">
                        <div class="px-4 py-3 border-b border-slate-100">
                            <p class="text-sm font-medium text-slate-800">{{ auth()->user()->name }}</p>
                            <p class="text-xs text-slate-400">{{ auth()->user()->email }}</p>
                        </div>

                        @if(auth()->user()->role === \App\Enums\UserRole::Student)
                        <a href="{{ route('profile') }}"
                            class="flex items-center gap-3 px-4 py-2 text-sm text-slate-600 hover:bg-slate-50 hover:text-slate-800">
                            <i data-lucide="user" class="w-4 h-4"></i> My Profile
                        </a>
                        @elseif(auth()->user()->role === \App\Enums\UserRole::Coordinator)
                        <a href="{{ route('coordinator.settings') }}"
                            class="flex items-center gap-3 px-4 py-2 text-sm text-slate-600 hover:bg-slate-50 hover:text-slate-800">
                            <i data-lucide="settings" class="w-4 h-4"></i> Settings
                        </a>
                        @endif

                        <div class="border-t border-slate-100 mt-1 pt-1">
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit"
                                    class="w-full flex items-center gap-3 px-4 py-2 text-sm text-red-600 hover:bg-red-50">
                                    <i data-lucide="log-out" class="w-4 h-4"></i> Logout
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <!-- PAGE CONTENT -->
        <main class="flex-1 p-6">
            @if(session('success'))
                <div class="mb-4 flex items-center gap-2 px-4 py-3 bg-emerald-50 border border-emerald-200 text-emerald-700 rounded-lg text-sm">
                    <i data-lucide="check-circle" class="w-4 h-4 shrink-0"></i>
                    <span>{{ session('success') }}</span>
                </div>
            @endif
            @if(session('error'))
                <div class="mb-4 flex items-center gap-2 px-4 py-3 bg-red-50 border border-red-200 text-red-600 rounded-lg text-sm">
                    <i data-lucide="alert-circle" class="w-4 h-4 shrink-0"></i>
                    <span>{{ session('error') }}</span>
                </div>
            @endif

            @yield('content')
        </main>

        <!-- FOOTER -->
        <footer class="px-6 py-4 border-t border-slate-200 text-center text-xs text-slate-400">
            &copy; {{ date('Y') }} TalentSys — FSKTM Student Talent Management System.
        </footer>

    </div>

</div>

<style>
    [x-cloak] { display: none !important; }
</style>

</body>
</html>
