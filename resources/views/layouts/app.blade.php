<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') | {{ config('rise.app_name', 'RISE') }}</title>

    <!-- Project Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('assets/images/logo.png') }}">

    <!-- Google Font: Inter (400, 500, 600, 700) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- FontAwesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <!-- ApexCharts CDN for Modern SaaS Visualizations -->
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

    <!-- Anti-Flash Theme Preference Script -->
    <script>
        (function() {
            try {
                const savedTheme = localStorage.getItem('rise_theme');
                const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
                if (savedTheme === 'dark' || (!savedTheme && prefersDark)) {
                    document.documentElement.classList.add('dark');
                } else {
                    document.documentElement.classList.remove('dark');
                }
            } catch (e) {}
        })();
    </script>

    <!-- Tailwind CSS with Dark Mode Class Strategy -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'system-ui', '-apple-system', 'sans-serif'],
                    },
                    colors: {
                        brand: {
                            50: '#eff6ff',
                            100: '#dbeafe',
                            500: '#4361EE',
                            600: '#3730A3',
                            700: '#2e49a8',
                            800: '#223880',
                        },
                        saas: {
                            blue: '#4361EE',
                            indigo: '#3730A3',
                            violet: '#7C3AED',
                            green: '#059669',
                            teal: '#0D9488',
                            pink: '#E64980',
                            amber: '#D97706',
                            pagelight: '#F5F7FF',
                            pagedark: '#111827',
                            surfacelight: '#FFFFFF',
                            surfacedark: '#1F2937',
                            borderlight: '#D9E0EE',
                            borderdark: '#374151',
                            textprimary: '#172033',
                            textsecondary: '#374151',
                            darktextprimary: '#F9FAFB',
                            darktextsecondary: '#D1D5DB'
                        }
                    }
                }
            }
        }
    </script>

    <style>
        /* Modern Medium Typography & Smooth Transitions */
        body {
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
            font-size: 14px;
            line-height: 1.5;
            color: #172033;
        }
        .dark body {
            color: #F9FAFB;
        }

        /* High Contrast SaaS Typography Rule - NO FAINT GRAY TEXT */
        html:not(.dark) .text-slate-400,
        html:not(.dark) .text-slate-500,
        html:not(.dark) .text-gray-400,
        html:not(.dark) .text-gray-500 {
            color: #374151 !important;
        }
        html.dark .text-slate-400,
        html.dark .text-slate-500,
        html.dark .text-gray-400,
        html.dark .text-gray-500 {
            color: #D1D5DB !important;
        }
        html:not(.dark) .text-slate-600,
        html:not(.dark) .text-slate-700,
        html:not(.dark) .text-slate-800 {
            color: #172033 !important;
        }
        html.dark .text-slate-200,
        html.dark .text-slate-300 {
            color: #F3F4F6 !important;
        }
        html.dark .text-slate-100 {
            color: #F9FAFB !important;
        }

        /* Input Placeholders - Legible Contrast */
        input::placeholder, textarea::placeholder {
            color: #64748B !important;
            opacity: 1;
        }
        .dark input::placeholder, .dark textarea::placeholder {
            color: #94A3B8 !important;
            opacity: 1;
        }

        /* Collapsed Sidebar Styles */
        body.sidebar-collapsed #sidebar {
            width: 72px;
        }
        body.sidebar-collapsed #sidebar .nav-text,
        body.sidebar-collapsed #sidebar .nav-badge,
        body.sidebar-collapsed #sidebar .section-title,
        body.sidebar-collapsed #sidebar .brand-full,
        body.sidebar-collapsed #sidebar .user-info {
            display: none !important;
        }
        body.sidebar-collapsed #sidebar .brand-mini {
            display: flex !important;
        }
        body.sidebar-collapsed #sidebar .nav-link {
            justify-content: center !important;
            padding-left: 0 !important;
            padding-right: 0 !important;
        }
        body.sidebar-collapsed #sidebar .nav-icon {
            margin-right: 0 !important;
            font-size: 16px !important;
        }

        /* Tooltip behavior when collapsed */
        body.sidebar-collapsed #sidebar .nav-item {
            position: relative;
        }
        body.sidebar-collapsed #sidebar .nav-item:hover .tooltip-label {
            display: block !important;
        }

        /* Compact, clean form inputs */
        input[type="text"], input[type="email"], input[type="password"],
        input[type="number"], input[type="date"], select, textarea {
            font-size: 13.5px !important;
        }

        /* Table Styling */
        .table-saas th {
            padding: 10px 14px;
            font-size: 12.5px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.04em;
        }
        .table-saas td {
            padding: 11px 14px;
            font-size: 13.5px;
        }

        /* Custom scrollbars */
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 9999px; }
        .dark ::-webkit-scrollbar-thumb { background: #334155; }
        ::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
    </style>
    @stack('styles')
</head>
<body class="h-full bg-[#F5F7FF] dark:bg-[#111827] text-[#172033] dark:text-[#F9FAFB] antialiased flex transition-colors duration-200">

    <!-- Sidebar Container -->
    <aside id="sidebar"
           class="w-64 bg-[#0f172a] text-slate-300 flex-shrink-0 flex flex-col fixed inset-y-0 left-0 z-40 md:static transition-all duration-300 ease-in-out -translate-x-full md:translate-x-0 border-r border-slate-800 select-none shadow-xl md:shadow-none">

        <!-- Sidebar Brand Header -->
        <div class="h-16 px-4 flex items-center justify-between border-b border-slate-800/80 bg-[#090d16] flex-shrink-0">
            <!-- Full Brand -->
            <a href="{{ route('dashboard') }}" class="brand-full flex items-center space-x-3 overflow-hidden">
                <img src="{{ asset('assets/images/logo.png') }}" alt="{{ config('rise.app_name', 'RISE') }}"
                     class="h-9 w-auto max-w-[120px] object-contain drop-shadow-sm">
                <div class="leading-tight">
                    <span class="block text-sm font-bold text-white tracking-wider uppercase">{{ config('rise.app_name', 'RISE') }}</span>
                    <span class="block text-[11px] text-slate-400 font-medium truncate max-w-[130px]">{{ config('rise.tagline', 'Skill Education') }}</span>
                </div>
            </a>

            <!-- Mini Brand (Shown on Collapsed) -->
            <a href="{{ route('dashboard') }}" class="brand-mini hidden w-full justify-center">
                <img src="{{ asset('assets/images/logo.png') }}" alt="{{ config('rise.app_name', 'RISE') }}" class="h-8 w-8 object-contain">
            </a>

            <!-- Mobile Close Button -->
            <button id="closeMobileSidebar" class="md:hidden text-slate-400 hover:text-white p-1 rounded transition">
                <i class="fa-solid fa-xmark text-lg"></i>
            </button>
        </div>

        <!-- Navigation Items List -->
        <div class="flex-1 overflow-y-auto px-3 py-3 space-y-4">
            <!-- Section: Main Dashboard -->
            <div>
                <div class="section-title px-2 mb-1.5 text-[11px] font-bold tracking-wider uppercase text-slate-500">Overview</div>
                <ul class="space-y-1">
                    <li class="nav-item">
                        <a href="{{ route('dashboard') }}"
                           class="nav-link flex items-center px-3 py-2 rounded-lg text-[13.5px] font-medium transition-all {{ request()->routeIs('dashboard') ? 'bg-[#4e73df] text-white shadow-sm font-semibold' : 'text-slate-300 hover:bg-slate-800/80 hover:text-white' }}">
                            <i class="fa-solid fa-gauge-high nav-icon w-5 text-center mr-3 text-[15px] opacity-90"></i>
                            <span class="nav-text">Dashboard</span>
                        </a>
                        <div class="tooltip-label hidden absolute left-full ml-2 top-1/2 -translate-y-1/2 px-2.5 py-1 bg-slate-900 text-white text-xs font-medium rounded shadow-lg whitespace-nowrap z-50">
                            Dashboard
                        </div>
                    </li>
                </ul>
            </div>

            @if(Auth::user()->isSuperAdmin())
            <!-- Section: Super Admin Management -->
            <div>
                <div class="section-title px-2 mb-1.5 text-[11px] font-bold tracking-wider uppercase text-slate-500">Administration</div>
                <ul class="space-y-1">
                    <li class="nav-item">
                        <a href="{{ route('admin_management.index') }}"
                           class="nav-link flex items-center px-3 py-2 rounded-lg text-[13.5px] font-medium transition-all {{ request()->routeIs('admin_management.*') ? 'bg-[#4e73df] text-white shadow-sm font-semibold' : 'text-slate-300 hover:bg-slate-800/80 hover:text-white' }}">
                            <i class="fa-solid fa-building-columns nav-icon w-5 text-center mr-3 text-[15px] opacity-90"></i>
                            <span class="nav-text">Center Admins</span>
                        </a>
                        <div class="tooltip-label hidden absolute left-full ml-2 top-1/2 -translate-y-1/2 px-2.5 py-1 bg-slate-900 text-white text-xs font-medium rounded shadow-lg whitespace-nowrap z-50">
                            Center Admins
                        </div>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('academic.programs') }}"
                           class="nav-link flex items-center px-3 py-2 rounded-lg text-[13.5px] font-medium transition-all {{ request()->routeIs('academic.programs*') ? 'bg-[#4e73df] text-white shadow-sm font-semibold' : 'text-slate-300 hover:bg-slate-800/80 hover:text-white' }}">
                            <i class="fa-solid fa-graduation-cap nav-icon w-5 text-center mr-3 text-[15px] opacity-90"></i>
                            <span class="nav-text">Programs</span>
                        </a>
                        <div class="tooltip-label hidden absolute left-full ml-2 top-1/2 -translate-y-1/2 px-2.5 py-1 bg-slate-900 text-white text-xs font-medium rounded shadow-lg whitespace-nowrap z-50">
                            Programs
                        </div>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('academic.courses') }}"
                           class="nav-link flex items-center px-3 py-2 rounded-lg text-[13.5px] font-medium transition-all {{ request()->routeIs('academic.courses*') ? 'bg-[#4e73df] text-white shadow-sm font-semibold' : 'text-slate-300 hover:bg-slate-800/80 hover:text-white' }}">
                            <i class="fa-solid fa-book-bookmark nav-icon w-5 text-center mr-3 text-[15px] opacity-90"></i>
                            <span class="nav-text">Courses</span>
                        </a>
                        <div class="tooltip-label hidden absolute left-full ml-2 top-1/2 -translate-y-1/2 px-2.5 py-1 bg-slate-900 text-white text-xs font-medium rounded shadow-lg whitespace-nowrap z-50">
                            Courses
                        </div>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('academic.subjects') }}"
                           class="nav-link flex items-center px-3 py-2 rounded-lg text-[13.5px] font-medium transition-all {{ request()->routeIs('academic.subjects*') ? 'bg-[#4e73df] text-white shadow-sm font-semibold' : 'text-slate-300 hover:bg-slate-800/80 hover:text-white' }}">
                            <i class="fa-solid fa-list-check nav-icon w-5 text-center mr-3 text-[15px] opacity-90"></i>
                            <span class="nav-text">Subjects</span>
                        </a>
                        <div class="tooltip-label hidden absolute left-full ml-2 top-1/2 -translate-y-1/2 px-2.5 py-1 bg-slate-900 text-white text-xs font-medium rounded shadow-lg whitespace-nowrap z-50">
                            Subjects
                        </div>
                    </li>
                </ul>
            </div>
            @endif

            <!-- Section: Students & Academics -->
            <div>
                <div class="section-title px-2 mb-1.5 text-[11px] font-bold tracking-wider uppercase text-slate-500">Students</div>
                <ul class="space-y-1">
                    <li class="nav-item">
                        <a href="{{ route('students.index') }}"
                           class="nav-link flex items-center px-3 py-2 rounded-lg text-[13.5px] font-medium transition-all {{ request()->routeIs('students.index') || request()->routeIs('students.show') || request()->routeIs('students.edit') ? 'bg-[#4e73df] text-white shadow-sm font-semibold' : 'text-slate-300 hover:bg-slate-800/80 hover:text-white' }}">
                            <i class="fa-solid fa-user-graduate nav-icon w-5 text-center mr-3 text-[15px] opacity-90"></i>
                            <span class="nav-text">All Students</span>
                        </a>
                        <div class="tooltip-label hidden absolute left-full ml-2 top-1/2 -translate-y-1/2 px-2.5 py-1 bg-slate-900 text-white text-xs font-medium rounded shadow-lg whitespace-nowrap z-50">
                            All Students
                        </div>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('students.create') }}"
                           class="nav-link flex items-center px-3 py-2 rounded-lg text-[13.5px] font-medium transition-all {{ request()->routeIs('students.create') ? 'bg-[#4e73df] text-white shadow-sm font-semibold' : 'text-slate-300 hover:bg-slate-800/80 hover:text-white' }}">
                            <i class="fa-solid fa-user-plus nav-icon w-5 text-center mr-3 text-[15px] opacity-90"></i>
                            <span class="nav-text">Enroll Student</span>
                        </a>
                        <div class="tooltip-label hidden absolute left-full ml-2 top-1/2 -translate-y-1/2 px-2.5 py-1 bg-slate-900 text-white text-xs font-medium rounded shadow-lg whitespace-nowrap z-50">
                            Enroll Student
                        </div>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('marks.index') }}"
                           class="nav-link flex items-center px-3 py-2 rounded-lg text-[13.5px] font-medium transition-all {{ request()->routeIs('marks.index') ? 'bg-[#4e73df] text-white shadow-sm font-semibold' : 'text-slate-300 hover:bg-slate-800/80 hover:text-white' }}">
                            <i class="fa-solid fa-pen-nib nav-icon w-5 text-center mr-3 text-[15px] opacity-90"></i>
                            <span class="nav-text">Marks Entry</span>
                        </a>
                        <div class="tooltip-label hidden absolute left-full ml-2 top-1/2 -translate-y-1/2 px-2.5 py-1 bg-slate-900 text-white text-xs font-medium rounded shadow-lg whitespace-nowrap z-50">
                            Marks Entry
                        </div>
                    </li>
                    @if(Auth::user()->isSuperAdmin())
                    <li class="nav-item">
                        <a href="{{ route('marks.superadmin_marksheet') }}"
                           class="nav-link flex items-center px-3 py-2 rounded-lg text-[13.5px] font-medium transition-all {{ request()->routeIs('marks.superadmin_marksheet') ? 'bg-[#4e73df] text-white shadow-sm font-semibold' : 'text-slate-300 hover:bg-slate-800/80 hover:text-white' }}">
                            <i class="fa-solid fa-file-invoice nav-icon w-5 text-center mr-3 text-[15px] opacity-90"></i>
                            <span class="nav-text">Search Marksheet</span>
                        </a>
                        <div class="tooltip-label hidden absolute left-full ml-2 top-1/2 -translate-y-1/2 px-2.5 py-1 bg-slate-900 text-white text-xs font-medium rounded shadow-lg whitespace-nowrap z-50">
                            Search Marksheet
                        </div>
                    </li>
                    @endif
                    <li class="nav-item">
                        <a href="{{ route('placements.index') }}"
                           class="nav-link flex items-center justify-between px-3 py-2 rounded-lg text-[13.5px] font-medium transition-all {{ request()->routeIs('placements.*') ? 'bg-[#4e73df] text-white shadow-sm font-semibold' : 'text-slate-300 hover:bg-slate-800/80 hover:text-white' }}">
                            <span class="flex items-center">
                                <i class="fa-solid fa-award nav-icon w-5 text-center mr-3 text-[15px] opacity-90"></i>
                                <span class="nav-text">Placements</span>
                            </span>
                            <span class="nav-badge text-[10px] bg-emerald-500/20 text-emerald-400 font-bold px-1.5 py-0.5 rounded leading-none">NEW</span>
                        </a>
                        <div class="tooltip-label hidden absolute left-full ml-2 top-1/2 -translate-y-1/2 px-2.5 py-1 bg-slate-900 text-white text-xs font-medium rounded shadow-lg whitespace-nowrap z-50">
                            Placements
                        </div>
                    </li>
                </ul>
            </div>

            <!-- Section: Careers & Jobs -->
            <div>
                <div class="section-title px-2 mb-1.5 text-[11px] font-bold tracking-wider uppercase text-slate-500">Careers</div>
                <ul class="space-y-1">
                    <li class="nav-item">
                        <a href="{{ route('jobs.index') }}"
                           class="nav-link flex items-center px-3 py-2 rounded-lg text-[13.5px] font-medium transition-all {{ request()->routeIs('jobs.index') || request()->routeIs('jobs.show') ? 'bg-[#4e73df] text-white shadow-sm font-semibold' : 'text-slate-300 hover:bg-slate-800/80 hover:text-white' }}">
                            <i class="fa-solid fa-briefcase nav-icon w-5 text-center mr-3 text-[15px] opacity-90"></i>
                            <span class="nav-text">Manage Jobs</span>
                        </a>
                        <div class="tooltip-label hidden absolute left-full ml-2 top-1/2 -translate-y-1/2 px-2.5 py-1 bg-slate-900 text-white text-xs font-medium rounded shadow-lg whitespace-nowrap z-50">
                            Manage Jobs
                        </div>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('jobs.frontend') }}" target="_blank"
                           class="nav-link flex items-center px-3 py-2 rounded-lg text-[13.5px] font-medium text-slate-300 hover:bg-slate-800/80 hover:text-white transition-all">
                            <i class="fa-solid fa-arrow-up-right-from-square nav-icon w-5 text-center mr-3 text-[15px] opacity-90"></i>
                            <span class="nav-text">Job Portal</span>
                        </a>
                        <div class="tooltip-label hidden absolute left-full ml-2 top-1/2 -translate-y-1/2 px-2.5 py-1 bg-slate-900 text-white text-xs font-medium rounded shadow-lg whitespace-nowrap z-50">
                            Public Job Portal
                        </div>
                    </li>
                </ul>
            </div>

            <!-- Section: Finance & Wallet -->
            <div>
                <div class="section-title px-2 mb-1.5 text-[11px] font-bold tracking-wider uppercase text-slate-500">Finance</div>
                <ul class="space-y-1">
                    <li class="nav-item">
                        <a href="{{ route('wallet.index') }}"
                           class="nav-link flex items-center px-3 py-2 rounded-lg text-[13.5px] font-medium transition-all {{ request()->routeIs('wallet.index') ? 'bg-[#4e73df] text-white shadow-sm font-semibold' : 'text-slate-300 hover:bg-slate-800/80 hover:text-white' }}">
                            <i class="fa-solid fa-wallet nav-icon w-5 text-center mr-3 text-[15px] opacity-90"></i>
                            <span class="nav-text">{{ Auth::user()->isSuperAdmin() ? 'All Transactions' : 'My Wallet' }}</span>
                        </a>
                        <div class="tooltip-label hidden absolute left-full ml-2 top-1/2 -translate-y-1/2 px-2.5 py-1 bg-slate-900 text-white text-xs font-medium rounded shadow-lg whitespace-nowrap z-50">
                            Wallet
                        </div>
                    </li>
                    @if(!Auth::user()->isSuperAdmin())
                    <li class="nav-item">
                        <a href="{{ route('wallet.recharge') }}"
                           class="nav-link flex items-center px-3 py-2 rounded-lg text-[13.5px] font-medium transition-all {{ request()->routeIs('wallet.recharge') ? 'bg-[#4e73df] text-white shadow-sm font-semibold' : 'text-slate-300 hover:bg-slate-800/80 hover:text-white' }}">
                            <i class="fa-solid fa-circle-plus nav-icon w-5 text-center mr-3 text-[15px] opacity-90"></i>
                            <span class="nav-text">Recharge Wallet</span>
                        </a>
                        <div class="tooltip-label hidden absolute left-full ml-2 top-1/2 -translate-y-1/2 px-2.5 py-1 bg-slate-900 text-white text-xs font-medium rounded shadow-lg whitespace-nowrap z-50">
                            Recharge
                        </div>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('wallet.failed_transactions') }}"
                           class="nav-link flex items-center px-3 py-2 rounded-lg text-[13.5px] font-medium transition-all {{ request()->routeIs('wallet.failed_transactions') ? 'bg-[#4e73df] text-white shadow-sm font-semibold' : 'text-slate-300 hover:bg-slate-800/80 hover:text-white' }}">
                            <i class="fa-solid fa-triangle-exclamation nav-icon w-5 text-center mr-3 text-[15px] opacity-90"></i>
                            <span class="nav-text">Failed Transactions</span>
                        </a>
                        <div class="tooltip-label hidden absolute left-full ml-2 top-1/2 -translate-y-1/2 px-2.5 py-1 bg-slate-900 text-white text-xs font-medium rounded shadow-lg whitespace-nowrap z-50">
                            Failed Transactions
                        </div>
                    </li>
                    @else
                    <li class="nav-item">
                        <a href="{{ route('wallet.failed_requests') }}"
                           class="nav-link flex items-center px-3 py-2 rounded-lg text-[13.5px] font-medium transition-all {{ request()->routeIs('wallet.failed_requests') ? 'bg-[#4e73df] text-white shadow-sm font-semibold' : 'text-slate-300 hover:bg-slate-800/80 hover:text-white' }}">
                            <i class="fa-solid fa-receipt nav-icon w-5 text-center mr-3 text-[15px] opacity-90"></i>
                            <span class="nav-text">Failed Tx Claims</span>
                        </a>
                        <div class="tooltip-label hidden absolute left-full ml-2 top-1/2 -translate-y-1/2 px-2.5 py-1 bg-slate-900 text-white text-xs font-medium rounded shadow-lg whitespace-nowrap z-50">
                            Failed Claims
                        </div>
                    </li>
                    @endif
                </ul>
            </div>

            <!-- Section: Communication & Notices -->
            <div>
                <div class="section-title px-2 mb-1.5 text-[11px] font-bold tracking-wider uppercase text-slate-500">Communication</div>
                <ul class="space-y-1">
                    <li class="nav-item">
                        <a href="{{ route('notices.notifications') }}"
                           class="nav-link flex items-center justify-between px-3 py-2 rounded-lg text-[13.5px] font-medium transition-all {{ request()->routeIs('notices.notifications') ? 'bg-[#4e73df] text-white shadow-sm font-semibold' : 'text-slate-300 hover:bg-slate-800/80 hover:text-white' }}">
                            <span class="flex items-center">
                                <i class="fa-solid fa-bell nav-icon w-5 text-center mr-3 text-[15px] opacity-90"></i>
                                <span class="nav-text">{{ Auth::user()->isSuperAdmin() ? 'Cert Requests' : 'Notifications' }}</span>
                            </span>
                            @if(Auth::user()->unreadNotificationsCount() > 0)
                            <span class="nav-badge text-[11px] bg-rose-500 text-white font-bold px-1.5 py-0.2 rounded-full leading-none">{{ Auth::user()->unreadNotificationsCount() }}</span>
                            @endif
                        </a>
                        <div class="tooltip-label hidden absolute left-full ml-2 top-1/2 -translate-y-1/2 px-2.5 py-1 bg-slate-900 text-white text-xs font-medium rounded shadow-lg whitespace-nowrap z-50">
                            Notifications
                        </div>
                    </li>
                    @if(Auth::user()->isSuperAdmin())
                    <li class="nav-item">
                        <a href="{{ route('notices.center_notices') }}"
                           class="nav-link flex items-center px-3 py-2 rounded-lg text-[13.5px] font-medium transition-all {{ request()->routeIs('notices.center_notices') ? 'bg-[#4e73df] text-white shadow-sm font-semibold' : 'text-slate-300 hover:bg-slate-800/80 hover:text-white' }}">
                            <i class="fa-solid fa-bullhorn nav-icon w-5 text-center mr-3 text-[15px] opacity-90"></i>
                            <span class="nav-text">Center Notices</span>
                        </a>
                        <div class="tooltip-label hidden absolute left-full ml-2 top-1/2 -translate-y-1/2 px-2.5 py-1 bg-slate-900 text-white text-xs font-medium rounded shadow-lg whitespace-nowrap z-50">
                            Center Notices
                        </div>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('notices.public_notices') }}"
                           class="nav-link flex items-center px-3 py-2 rounded-lg text-[13.5px] font-medium transition-all {{ request()->routeIs('notices.public_notices') ? 'bg-[#4e73df] text-white shadow-sm font-semibold' : 'text-slate-300 hover:bg-slate-800/80 hover:text-white' }}">
                            <i class="fa-solid fa-globe nav-icon w-5 text-center mr-3 text-[15px] opacity-90"></i>
                            <span class="nav-text">Website Notices</span>
                        </a>
                        <div class="tooltip-label hidden absolute left-full ml-2 top-1/2 -translate-y-1/2 px-2.5 py-1 bg-slate-900 text-white text-xs font-medium rounded shadow-lg whitespace-nowrap z-50">
                            Website Notices
                        </div>
                    </li>
                    @endif
                </ul>
            </div>

            <!-- Section: Certificates & Tools -->
            <div>
                <div class="section-title px-2 mb-1.5 text-[11px] font-bold tracking-wider uppercase text-slate-500">Tools</div>
                <ul class="space-y-1">
                    @if(Auth::user()->isSuperAdmin())
                    <li class="nav-item">
                        <a href="{{ route('certificates.authorized_list') }}"
                           class="nav-link flex items-center px-3 py-2 rounded-lg text-[13.5px] font-medium transition-all {{ request()->routeIs('certificates.authorized_list') ? 'bg-[#4e73df] text-white shadow-sm font-semibold' : 'text-slate-300 hover:bg-slate-800/80 hover:text-white' }}">
                            <i class="fa-solid fa-certificate nav-icon w-5 text-center mr-3 text-[15px] opacity-90"></i>
                            <span class="nav-text">All Center Certs</span>
                        </a>
                        <div class="tooltip-label hidden absolute left-full ml-2 top-1/2 -translate-y-1/2 px-2.5 py-1 bg-slate-900 text-white text-xs font-medium rounded shadow-lg whitespace-nowrap z-50">
                            Center Certificates
                        </div>
                    </li>
                    @else
                    <li class="nav-item">
                        <a href="{{ route('certificates.admin_certificate') }}" target="_blank"
                           class="nav-link flex items-center px-3 py-2 rounded-lg text-[13.5px] font-medium text-slate-300 hover:bg-slate-800/80 hover:text-white transition-all">
                            <i class="fa-solid fa-certificate nav-icon w-5 text-center mr-3 text-[15px] opacity-90"></i>
                            <span class="nav-text">My Authorization Cert</span>
                        </a>
                        <div class="tooltip-label hidden absolute left-full ml-2 top-1/2 -translate-y-1/2 px-2.5 py-1 bg-slate-900 text-white text-xs font-medium rounded shadow-lg whitespace-nowrap z-50">
                            Authorization Certificate
                        </div>
                    </li>
                    @endif
                    <li class="nav-item">
                        <a href="{{ route('verify.student') }}"
                           class="nav-link flex items-center px-3 py-2 rounded-lg text-[13.5px] font-medium transition-all {{ request()->routeIs('verify.student') ? 'bg-[#4e73df] text-white shadow-sm font-semibold' : 'text-slate-300 hover:bg-slate-800/80 hover:text-white' }}">
                            <i class="fa-solid fa-magnifying-glass nav-icon w-5 text-center mr-3 text-[15px] opacity-90"></i>
                            <span class="nav-text">Verify Student</span>
                        </a>
                        <div class="tooltip-label hidden absolute left-full ml-2 top-1/2 -translate-y-1/2 px-2.5 py-1 bg-slate-900 text-white text-xs font-medium rounded shadow-lg whitespace-nowrap z-50">
                            Verify Student
                        </div>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('verify.certificate') }}"
                           class="nav-link flex items-center px-3 py-2 rounded-lg text-[13.5px] font-medium transition-all {{ request()->routeIs('verify.certificate') ? 'bg-[#4e73df] text-white shadow-sm font-semibold' : 'text-slate-300 hover:bg-slate-800/80 hover:text-white' }}">
                            <i class="fa-solid fa-stamp nav-icon w-5 text-center mr-3 text-[15px] opacity-90"></i>
                            <span class="nav-text">Verify Certificate</span>
                        </a>
                        <div class="tooltip-label hidden absolute left-full ml-2 top-1/2 -translate-y-1/2 px-2.5 py-1 bg-slate-900 text-white text-xs font-medium rounded shadow-lg whitespace-nowrap z-50">
                            Verify Certificate
                        </div>
                    </li>
                </ul>
            </div>
        </div>

        <!-- Sidebar User Footer -->
        <div class="p-3 border-t border-slate-800 bg-[#090d16] flex items-center space-x-3 flex-shrink-0">
            <div class="w-8 h-8 rounded-full bg-slate-700 text-white flex items-center justify-center font-bold text-xs flex-shrink-0">
                {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
            </div>
            <div class="user-info flex-1 min-w-0">
                <div class="text-[13px] font-semibold text-white truncate leading-tight">{{ Auth::user()->name }}</div>
                <div class="text-[11px] text-slate-400 truncate leading-tight mt-0.5">
                    {{ Auth::user()->isSuperAdmin() ? 'Super Administrator' : (Auth::user()->institute_name ?? 'Center Admin') }}
                </div>
            </div>
        </div>
    </aside>

    <!-- Main Content Wrapper -->
    <div class="flex-1 flex flex-col min-w-0 overflow-hidden">
        <!-- Top Navigation Bar -->
        <header class="h-16 bg-white dark:bg-[#1F2937] border-b border-[#D9E0EE] dark:border-[#374151] px-4 sm:px-6 flex items-center justify-between sticky top-0 z-20 shadow-xs transition-colors duration-200">
            <!-- Left Side: Toggles & Title -->
            <div class="flex items-center space-x-3 sm:space-x-4">
                <!-- Desktop Sidebar Collapse Toggle -->
                <button id="toggleDesktopSidebar"
                        class="hidden md:inline-flex p-2 text-slate-500 dark:text-slate-400 hover:text-slate-800 dark:hover:text-white rounded-lg hover:bg-slate-100 dark:hover:bg-slate-800 transition"
                        title="Toggle Sidebar Width">
                    <i class="fa-solid fa-bars-staggered text-[15px]"></i>
                </button>

                <!-- Mobile Hamburger Button -->
                <button id="openMobileSidebar"
                        class="md:hidden p-2 text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white rounded-lg hover:bg-slate-100 dark:hover:bg-slate-800 transition">
                    <i class="fa-solid fa-bars text-lg"></i>
                </button>

                <!-- Breadcrumb / Page Title (15px-17px font) -->
                <div class="font-semibold text-slate-800 dark:text-slate-100 text-[15px] sm:text-[16px] truncate">
                    @yield('breadcrumb', 'Overview')
                </div>
            </div>

            <!-- Right Side Controls -->
            <div class="flex items-center space-x-2.5 sm:space-x-3.5">
                <!-- Wallet Balance Badge -->
                <a href="{{ route('wallet.index') }}"
                   class="flex items-center space-x-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold border transition-all shadow-2xs {{ Auth::user()->wallet_balance > 0 ? 'bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-400 border-emerald-200 dark:border-emerald-800 hover:bg-emerald-100' : 'bg-rose-50 dark:bg-rose-950/40 text-rose-700 dark:text-rose-400 border-rose-200 dark:border-rose-800 hover:bg-rose-100' }}">
                    <i class="fa-solid fa-wallet text-[13px]"></i>
                    <span class="font-mono text-[13px]">₹{{ number_format(Auth::user()->wallet_balance, 2) }}</span>
                </a>

                <!-- Theme Toggle Button (Light / Dark) -->
                <button id="themeToggleBtn"
                        class="p-2 text-slate-500 dark:text-slate-400 hover:text-slate-800 dark:hover:text-white rounded-lg hover:bg-slate-100 dark:hover:bg-slate-800 transition relative"
                        title="Toggle Theme (Light / Dark Mode)">
                    <!-- Sun Icon (Shown in Dark Mode) -->
                    <i class="fa-solid fa-sun text-[15px] hidden dark:inline text-amber-400"></i>
                    <!-- Moon Icon (Shown in Light Mode) -->
                    <i class="fa-solid fa-moon text-[15px] inline dark:hidden text-slate-600"></i>
                </button>

                <!-- Notifications Bell -->
                <a href="{{ route('notices.notifications') }}"
                   class="relative p-2 text-slate-500 dark:text-slate-400 hover:text-slate-800 dark:hover:text-white rounded-lg hover:bg-slate-100 dark:hover:bg-slate-800 transition"
                   title="Notifications">
                    <i class="fa-regular fa-bell text-[16px]"></i>
                    @if(Auth::user()->unreadNotificationsCount() > 0)
                    <span class="absolute top-1.5 right-1.5 w-2 h-2 rounded-full bg-rose-500 ring-2 ring-white dark:ring-[#151c2c]"></span>
                    @endif
                </a>

                <!-- User Dropdown Menu -->
                <div class="relative" id="userMenuWrapper">
                    <button id="userMenuButton" class="flex items-center space-x-2 pl-2 border-l border-slate-200 dark:border-slate-800 focus:outline-none">
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-bold uppercase tracking-wider {{ Auth::user()->isSuperAdmin() ? 'bg-purple-100 dark:bg-purple-900/40 text-purple-700 dark:text-purple-300' : 'bg-blue-100 dark:bg-blue-900/40 text-blue-700 dark:text-blue-300' }}">
                            {{ Auth::user()->isSuperAdmin() ? 'Super' : 'Center' }}
                        </span>
                        <span class="text-[13.5px] font-semibold text-slate-700 dark:text-slate-200 hidden sm:inline max-w-[120px] truncate">
                            {{ Auth::user()->name }}
                        </span>
                        <i class="fa-solid fa-chevron-down text-[10px] text-slate-400"></i>
                    </button>

                    <!-- Dropdown Content -->
                    <div id="userDropdown"
                         class="hidden absolute right-0 mt-2 w-52 bg-white dark:bg-[#182235] border border-slate-200 dark:border-slate-700 rounded-xl shadow-lg py-1.5 z-50 text-[13px]">
                        <div class="px-3.5 py-2 border-b border-slate-100 dark:border-slate-800">
                            <div class="font-bold text-slate-800 dark:text-white text-xs truncate">{{ Auth::user()->name }}</div>
                            <div class="text-[11px] text-slate-400 truncate">{{ Auth::user()->email }}</div>
                        </div>
                        <a href="{{ route('profile.show') }}" class="flex items-center px-3.5 py-2 text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-700/50 transition">
                            <i class="fa-regular fa-user mr-2.5 text-slate-400"></i>
                            <span>My Profile</span>
                        </a>
                        <a href="{{ route('profile.change_password') }}" class="flex items-center px-3.5 py-2 text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-700/50 transition">
                            <i class="fa-solid fa-key mr-2.5 text-slate-400"></i>
                            <span>Change Password</span>
                        </a>
                        <div class="border-t border-slate-100 dark:border-slate-800 my-1"></div>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="w-full flex items-center px-3.5 py-2 text-rose-600 dark:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-950/30 transition text-left">
                                <i class="fa-solid fa-right-from-bracket mr-2.5"></i>
                                <span>Logout</span>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </header>

        <!-- Flash Alert Messages Area -->
        <div class="px-4 sm:px-6 pt-4">
            @if(session('success'))
            <div class="mb-3 px-4 py-3 rounded-lg bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 text-emerald-800 dark:text-emerald-300 text-[13.5px] flex items-center justify-between shadow-xs">
                <div class="flex items-center space-x-2.5">
                    <i class="fa-solid fa-circle-check text-emerald-600 dark:text-emerald-400 text-base"></i>
                    <span>{{ session('success') }}</span>
                </div>
                <button onclick="this.parentElement.remove()" class="text-emerald-600 hover:text-emerald-900 dark:text-emerald-400 p-1"><i class="fa-solid fa-xmark"></i></button>
            </div>
            @endif

            @if(session('error'))
            <div class="mb-3 px-4 py-3 rounded-lg bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-800 text-rose-800 dark:text-rose-300 text-[13.5px] flex items-center justify-between shadow-xs">
                <div class="flex items-center space-x-2.5">
                    <i class="fa-solid fa-triangle-exclamation text-rose-600 dark:text-rose-400 text-base"></i>
                    <span>{{ session('error') }}</span>
                </div>
                <button onclick="this.parentElement.remove()" class="text-rose-600 hover:text-rose-900 dark:text-rose-400 p-1"><i class="fa-solid fa-xmark"></i></button>
            </div>
            @endif

            @if(session('info'))
            <div class="mb-3 px-4 py-3 rounded-lg bg-blue-50 dark:bg-blue-950/40 border border-blue-200 dark:border-blue-800 text-blue-800 dark:text-blue-300 text-[13.5px] flex items-center justify-between shadow-xs">
                <div class="flex items-center space-x-2.5">
                    <i class="fa-solid fa-circle-info text-blue-600 dark:text-blue-400 text-base"></i>
                    <span>{{ session('info') }}</span>
                </div>
                <button onclick="this.parentElement.remove()" class="text-blue-600 hover:text-blue-900 dark:text-blue-400 p-1"><i class="fa-solid fa-xmark"></i></button>
            </div>
            @endif

            @if($errors->any())
            <div class="mb-3 px-4 py-3 rounded-lg bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-800 text-rose-800 dark:text-rose-300 text-[13.5px] shadow-xs">
                <div class="font-semibold mb-1 flex items-center space-x-2">
                    <i class="fa-solid fa-circle-exclamation"></i>
                    <span>Please correct the errors below:</span>
                </div>
                <ul class="list-disc list-inside space-y-0.5 ml-2 text-xs">
                    @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif
        </div>

        <!-- Main Dynamic Content -->
        <main class="flex-1 p-4 sm:p-6 overflow-y-auto">
            @yield('content')
        </main>

        <!-- Footer -->
        <footer class="h-11 px-6 border-t border-[#D9E0EE] dark:border-[#374151] bg-white dark:bg-[#1F2937] flex items-center justify-between text-xs text-[#374151] dark:text-[#D1D5DB] transition-colors duration-200">
            <div>&copy; {{ date('Y') }} {{ config('rise.app_name', 'RISE') }} - {{ config('rise.tagline') }}</div>
            <div class="text-[11px] font-mono">RISE SaaS Platform v2.0</div>
        </footer>
    </div>

    <!-- Mobile Sidebar Backdrop -->
    <div id="mobileSidebarBackdrop" class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs z-30 hidden md:hidden transition-opacity"></div>

    <!-- Layout Scripts: Sidebar & Theme Toggle -->
    <script>
        // 1. Sidebar Collapse (Desktop) & Off-Canvas (Mobile)
        const toggleDesktopBtn = document.getElementById('toggleDesktopSidebar');
        const openMobileBtn = document.getElementById('openMobileSidebar');
        const closeMobileBtn = document.getElementById('closeMobileSidebar');
        const mobileBackdrop = document.getElementById('mobileSidebarBackdrop');
        const sidebar = document.getElementById('sidebar');

        // Restore desktop collapse state
        if (localStorage.getItem('rise_sidebar_collapsed') === 'true') {
            document.body.classList.add('sidebar-collapsed');
        }

        if (toggleDesktopBtn) {
            toggleDesktopBtn.addEventListener('click', () => {
                document.body.classList.toggle('sidebar-collapsed');
                const isCollapsed = document.body.classList.contains('sidebar-collapsed');
                localStorage.setItem('rise_sidebar_collapsed', isCollapsed ? 'true' : 'false');
            });
        }

        function toggleMobileSidebar(show) {
            if (show) {
                sidebar.classList.remove('-translate-x-full');
                mobileBackdrop.classList.remove('hidden');
            } else {
                sidebar.classList.add('-translate-x-full');
                mobileBackdrop.classList.add('hidden');
            }
        }

        if (openMobileBtn) openMobileBtn.addEventListener('click', () => toggleMobileSidebar(true));
        if (closeMobileBtn) closeMobileBtn.addEventListener('click', () => toggleMobileSidebar(false));
        if (mobileBackdrop) mobileBackdrop.addEventListener('click', () => toggleMobileSidebar(false));

        // 2. Theme Toggle (Light / Dark)
        const themeToggleBtn = document.getElementById('themeToggleBtn');
        if (themeToggleBtn) {
            themeToggleBtn.addEventListener('click', () => {
                const isDark = document.documentElement.classList.toggle('dark');
                localStorage.setItem('rise_theme', isDark ? 'dark' : 'light');
            });
        }

        // 3. User Dropdown Toggle
        const userMenuBtn = document.getElementById('userMenuButton');
        const userDropdown = document.getElementById('userDropdown');

        if (userMenuBtn && userDropdown) {
            userMenuBtn.addEventListener('click', (e) => {
                e.stopPropagation();
                userDropdown.classList.toggle('hidden');
            });

            document.addEventListener('click', (e) => {
                if (!document.getElementById('userMenuWrapper').contains(e.target)) {
                    userDropdown.classList.add('hidden');
                }
            });
        }
    </script>
    @stack('scripts')
</body>
</html>
