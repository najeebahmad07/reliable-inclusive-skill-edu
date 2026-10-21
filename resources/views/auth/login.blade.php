<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign In | {{ config('rise.app_name', 'RISE') }}</title>
    <link rel="icon" type="image/png" href="{{ asset('assets/images/logo.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <script>
        (function() {
            try {
                const savedTheme = localStorage.getItem('rise_theme');
                if (savedTheme === 'dark' || (!savedTheme && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                    document.documentElement.classList.add('dark');
                }
            } catch (e) {}
        })();
    </script>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = { darkMode: 'class' };
    </script>
    <style>
        body { font-family: 'Inter', system-ui, sans-serif; font-size: 14px; }
    </style>
</head>
<body class="h-full bg-[#F5F7FF] dark:bg-[#111827] text-[#172033] dark:text-[#F9FAFB] flex items-center justify-center p-4 transition-colors">
    <div class="w-full max-w-[420px]">
        <!-- Brand Header -->
        <div class="text-center mb-6">
            <img src="{{ asset('assets/images/logo.png') }}" alt="{{ config('rise.app_name', 'RISE') }}"
                 class="h-16 w-auto mx-auto mb-3 object-contain drop-shadow-sm">
            <h1 class="text-2xl font-bold tracking-tight text-[#172033] dark:text-[#F9FAFB]">{{ config('rise.app_name', 'RISE') }}</h1>
            <p class="text-xs font-semibold text-[#374151] dark:text-[#D1D5DB] mt-1">{{ config('rise.tagline', 'Reliable Inclusive Skill Education') }}</p>
        </div>

        <!-- Login Card -->
        <div class="bg-white dark:bg-[#1F2937] rounded-2xl shadow-md border border-[#D9E0EE] dark:border-[#374151] p-8">
            <h2 class="text-xl font-bold text-[#172033] dark:text-[#F9FAFB] mb-1">Sign In to Dashboard</h2>
            <p class="text-xs text-[#374151] dark:text-[#D1D5DB] mb-6">Enter your authorized administrative credentials to access the portal</p>

            @if(session('error'))
            <div class="mb-4 p-3.5 rounded-xl bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-800 text-rose-800 dark:text-rose-300 text-xs flex items-center space-x-2.5">
                <i class="fa-solid fa-circle-exclamation text-rose-600 text-base"></i>
                <span class="font-medium">{{ session('error') }}</span>
            </div>
            @endif

            @if(session('success'))
            <div class="mb-4 p-3.5 rounded-xl bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 text-emerald-800 dark:text-emerald-300 text-xs flex items-center space-x-2.5">
                <i class="fa-solid fa-circle-check text-emerald-600 text-base"></i>
                <span class="font-medium">{{ session('success') }}</span>
            </div>
            @endif

            <form method="POST" action="{{ route('login.post') }}" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-semibold text-[#374151] dark:text-[#D1D5DB] mb-1.5">Email Address</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-[#64748B]">
                            <i class="fa-regular fa-envelope text-sm"></i>
                        </div>
                        <input type="email" name="email" value="{{ old('email') }}" required autofocus
                               class="w-full pl-10 pr-3.5 py-2.5 bg-[#F5F7FF] dark:bg-[#111827] border border-[#D9E0EE] dark:border-[#374151] rounded-xl text-sm text-[#172033] dark:text-[#F9FAFB] focus:outline-none focus:ring-2 focus:ring-[#4361EE]/20 focus:border-[#4361EE] transition"
                               placeholder="admin@domain.com">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-[#374151] dark:text-[#D1D5DB] mb-1.5">Password</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-[#64748B]">
                            <i class="fa-solid fa-lock text-sm"></i>
                        </div>
                        <input type="password" name="password" required
                               class="w-full pl-10 pr-3.5 py-2.5 bg-[#F5F7FF] dark:bg-[#111827] border border-[#D9E0EE] dark:border-[#374151] rounded-xl text-sm text-[#172033] dark:text-[#F9FAFB] focus:outline-none focus:ring-2 focus:ring-[#4361EE]/20 focus:border-[#4361EE] transition"
                               placeholder="••••••••">
                    </div>
                </div>

                <div class="flex items-center justify-between text-xs pt-1">
                    <label class="flex items-center space-x-2 cursor-pointer">
                        <input type="checkbox" name="remember" class="rounded border-[#D9E0EE] text-[#4361EE] focus:ring-0">
                        <span class="text-[#374151] dark:text-[#D1D5DB] font-medium">Remember me</span>
                    </label>
                    <a href="{{ route('verify.student') }}" class="text-[#4361EE] dark:text-blue-400 hover:underline font-bold">Public Verification &rarr;</a>
                </div>

                <button type="submit"
                        class="w-full py-2.5 px-4 bg-[#4361EE] hover:bg-[#3730A3] text-white font-bold rounded-xl text-sm shadow transition flex items-center justify-center space-x-2 mt-2">
                    <span>Sign In to Dashboard</span>
                    <i class="fa-solid fa-arrow-right text-xs"></i>
                </button>
            </form>
        </div>

        <div class="text-center mt-6 text-xs text-[#374151] dark:text-[#D1D5DB]">
            &copy; {{ date('Y') }} {{ config('rise.app_name', 'RISE') }}. All rights reserved.
        </div>
    </div>
</body>
</html>
