<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — TalentSys</title>
    @vite(['resources/css/app.css'])
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.min.js"></script>
    <script defer>
        document.addEventListener('DOMContentLoaded', function () {
            if (typeof lucide !== 'undefined') lucide.createIcons();
        });
    </script>
</head>
<body class="min-h-screen flex items-center justify-center bg-gradient-to-br from-violet-100 via-blue-50 to-indigo-100 relative overflow-hidden">

    <!-- Floating Orbs -->
    <div class="absolute top-20 -left-20 w-72 h-72 bg-violet-300/30 rounded-full blur-3xl animate-pulse"></div>
    <div class="absolute bottom-20 -right-20 w-96 h-96 bg-blue-300/30 rounded-full blur-3xl animate-pulse" style="animation-delay: 1s;"></div>
    <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-64 h-64 bg-indigo-300/20 rounded-full blur-3xl animate-pulse" style="animation-delay: 2s;"></div>

    <!-- Login Card -->
    <div class="relative w-full max-w-md mx-4">
        <div class="bg-white/80 backdrop-blur-xl rounded-2xl shadow-xl border border-white/20 p-8">

            <!-- Logo -->
            <div class="flex justify-center mb-6">
                <div class="w-16 h-16 rounded-2xl gradient-primary flex items-center justify-center text-white shadow-lg">
                    <i data-lucide="graduation-cap" class="w-8 h-8"></i>
                </div>
            </div>

            <!-- Title -->
            <h2 class="text-2xl font-bold text-center text-slate-900">TalentSys</h2>
            <p class="text-sm text-center text-slate-500 mt-1">FSKTM Student Talent Management System</p>
            <p class="text-xs text-center text-slate-400 mt-0.5">Student Affairs FSKTM</p>

            <form method="POST" action="{{ route('login') }}" class="mt-8 space-y-5">
                @csrf

                <!-- Email -->
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Email Address</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400">
                            <i data-lucide="mail" class="w-5 h-5"></i>
                        </span>
                        <input type="email" name="email" value="{{ old('email') }}" required autofocus
                            placeholder="Enter your email"
                            class="w-full pl-10 pr-4 py-2.5 rounded-lg border border-slate-300 bg-white text-slate-800 text-sm focus:ring-2 focus:ring-violet-500 focus:border-violet-500 outline-none transition @error('email') border-red-300 @enderror">
                    </div>
                    @error('email')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Password -->
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Password</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400">
                            <i data-lucide="lock" class="w-5 h-5"></i>
                        </span>
                        <input type="password" name="password" required
                            placeholder="Enter your password"
                            class="w-full pl-10 pr-4 py-2.5 rounded-lg border border-slate-300 bg-white text-slate-800 text-sm focus:ring-2 focus:ring-violet-500 focus:border-violet-500 outline-none transition @error('password') border-red-300 @enderror">
                    </div>
                    @error('password')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Role -->
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Login as</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400 pointer-events-none">
                            <i data-lucide="users" class="w-5 h-5"></i>
                        </span>
                        <select name="role" required
                            class="w-full pl-10 pr-4 py-2.5 rounded-lg border border-slate-300 bg-white text-slate-800 text-sm focus:ring-2 focus:ring-violet-500 focus:border-violet-500 outline-none transition appearance-none @error('role') border-red-300 @enderror">
                            <option value="">-- Select your role --</option>
                            @foreach (\App\Enums\UserRole::cases() as $r)
                                <option value="{{ $r->value }}" {{ old('role') === $r->value ? 'selected' : '' }}>{{ $r->label() }}</option>
                            @endforeach
                        </select>
                    </div>
                    @error('role')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Remember + Forgot -->
                <div class="flex items-center justify-between text-sm">
                    <label class="flex items-center gap-2 text-slate-600">
                        <input type="checkbox" name="remember"
                            class="rounded border-slate-300 text-violet-600 focus:ring-violet-500">
                        Remember me
                    </label>
                    <a href="{{ route('password.request') }}"
                        class="text-violet-600 hover:text-violet-700 font-medium">
                        Forgot password?
                    </a>
                </div>

                <!-- Submit -->
                <button type="submit"
                    class="w-full py-2.5 gradient-primary text-white font-semibold rounded-lg hover:scale-[1.02] transition shadow-md">
                    Login
                </button>

            </form>

            <!-- Register link -->
            <p class="mt-6 text-center text-sm text-slate-500">
                Don't have an account?
                <a href="{{ route('register') }}" class="text-violet-600 hover:text-violet-700 font-medium">Register</a>
            </p>

            <!-- Copyright -->
            <p class="mt-6 text-xs text-center text-slate-400">
                &copy; {{ date('Y') }} TalentSys — FSKTM Student Affairs
            </p>

        </div>
    </div>

</body>
</html>
