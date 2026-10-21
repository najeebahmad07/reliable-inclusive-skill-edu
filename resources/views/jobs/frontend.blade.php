<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Careers & Openings | {{ config('rise.app_name', 'RISE') }}</title>
    <link rel="icon" type="image/png" href="{{ asset('assets/images/logo.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'system-ui', 'sans-serif'],
                    },
                    colors: {
                        brand: {
                            500: '#4e73df',
                            600: '#3b5bcc',
                            700: '#2e49a8',
                        }
                    }
                }
            }
        }
    </script>
    <style>
        body { font-family: 'Inter', system-ui, sans-serif; font-size: 14px; }
    </style>
</head>
<body class="min-h-screen flex flex-col bg-slate-50 text-slate-800 antialiased">
    <!-- Navbar -->
    <nav class="bg-[#0f172a] text-white border-b border-slate-800 px-6 py-4 flex items-center justify-between">
        <a href="/" class="flex items-center space-x-3">
            <img src="{{ asset('assets/images/logo.png') }}" alt="RISE Logo" class="h-8 w-auto">
            <span class="font-bold text-sm tracking-wide text-white">{{ config('rise.app_name', 'RISE') }} CAREERS</span>
        </a>
        <a href="{{ route('login') }}" class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-white rounded-lg text-xs font-semibold border border-slate-700 transition">
            Staff Login &rarr;
        </a>
    </nav>

    <!-- Header Banner -->
    <div class="bg-gradient-to-r from-slate-900 via-blue-950 to-slate-900 text-white py-14 px-6 text-center border-b border-slate-800">
        <h1 class="text-3xl font-extrabold tracking-tight">Explore Placement & Job Opportunities</h1>
        <p class="text-sm text-slate-300 mt-2 max-w-xl mx-auto leading-relaxed">Connecting qualified students and certified professionals with verified corporate job openings.</p>
    </div>

    <!-- Main Content -->
    <main class="flex-1 max-w-5xl mx-auto w-full p-6 space-y-6">
        @if(session('success'))
        <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl text-sm flex items-center space-x-2.5">
            <i class="fa-solid fa-circle-check text-emerald-600 text-base"></i>
            <span>{{ session('success') }}</span>
        </div>
        @endif

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
            @forelse($jobs as $j)
            <div class="bg-white rounded-xl border border-slate-200 p-5 flex flex-col justify-between shadow-xs hover:shadow-md transition">
                <div>
                    <div class="flex items-start justify-between gap-2 mb-2.5">
                        <h2 class="font-bold text-base text-slate-900 leading-snug">{{ $j->job_name }}</h2>
                        <span class="text-xs font-semibold px-2.5 py-0.5 rounded-full bg-blue-50 text-blue-700 border border-blue-200 whitespace-nowrap">
                            {{ $j->employment_type ?? 'Full Time' }}
                        </span>
                    </div>

                    <div class="text-xs text-slate-500 flex items-center space-x-3 mb-3">
                        <span><i class="fa-solid fa-location-dot text-slate-400 mr-1.5 text-xs"></i>{{ $j->location ?? 'Anywhere' }}</span>
                        <span><i class="fa-regular fa-clock text-slate-400 mr-1.5 text-xs"></i>{{ $j->created_at ? $j->created_at->diffForHumans() : '' }}</span>
                    </div>

                    <p class="text-sm text-slate-600 line-clamp-3 mb-5 leading-relaxed">
                        {{ $j->description ?? 'Exciting opportunity for skilled candidates. Apply now.' }}
                    </p>
                </div>

                <button onclick="openApplyModal({{ $j->id }}, '{{ addslashes($j->job_name) }}')"
                        class="w-full py-2.5 px-4 bg-slate-900 hover:bg-brand-500 text-white rounded-lg text-sm font-semibold transition text-center shadow-xs">
                    Apply for Position
                </button>
            </div>
            @empty
            <div class="col-span-full py-16 text-center text-slate-400 text-sm bg-white rounded-xl border border-slate-200">
                <i class="fa-solid fa-briefcase text-3xl mb-2 text-slate-300 block"></i>
                No active openings currently published. Please check back later.
            </div>
            @endforelse
        </div>
    </main>

    <!-- Apply Modal -->
    <div id="applyModal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4 z-50 hidden">
        <div class="bg-white rounded-xl border border-slate-200 w-full max-w-md p-6 space-y-4 shadow-2xl">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <div>
                    <h3 class="text-xs font-bold text-slate-500 uppercase tracking-wider">Job Application</h3>
                    <div id="modalJobTitle" class="text-base font-bold text-slate-900"></div>
                </div>
                <button onclick="document.getElementById('applyModal').classList.add('hidden')" class="text-slate-400 hover:text-slate-600 text-base">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <form id="applyForm" method="POST" action="" enctype="multipart/form-data" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-medium text-slate-700 mb-1.5">Your Full Name *</label>
                    <input type="text" name="candidate_name" required placeholder="Full Name"
                           class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-lg text-sm text-slate-800 focus:outline-none focus:ring-1 focus:ring-brand-500">
                </div>

                <div>
                    <label class="block text-xs font-medium text-slate-700 mb-1.5">Email Address *</label>
                    <input type="email" name="email" required placeholder="name@example.com"
                           class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-lg text-sm text-slate-800 focus:outline-none focus:ring-1 focus:ring-brand-500">
                </div>

                <div>
                    <label class="block text-xs font-medium text-slate-700 mb-1.5">Contact / Mobile Number *</label>
                    <input type="text" name="contact_number" required placeholder="10-digit mobile"
                           class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-lg text-sm text-slate-800 focus:outline-none focus:ring-1 focus:ring-brand-500">
                </div>

                <div>
                    <label class="block text-xs font-medium text-slate-700 mb-1.5">Upload Resume (PDF, DOCX max 3MB) *</label>
                    <input type="file" name="resume_file" required accept=".pdf,.doc,.docx"
                           class="w-full text-xs text-slate-500 file:mr-2 file:py-1 file:px-2.5 file:rounded-md file:border-0 file:text-xs file:bg-blue-50 file:text-blue-700">
                </div>

                <div class="flex items-center justify-end space-x-2 pt-2">
                    <button type="button" onclick="document.getElementById('applyModal').classList.add('hidden')" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 rounded-lg text-sm text-slate-600 transition">Cancel</button>
                    <button type="submit" class="px-5 py-2 bg-brand-500 hover:bg-brand-600 text-white rounded-lg text-sm font-semibold shadow-xs transition">Submit Application</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openApplyModal(id, title) {
            document.getElementById('modalJobTitle').innerText = title;
            document.getElementById('applyForm').action = `{{ url('jobs') }}/${id}/apply`;
            document.getElementById('applyModal').classList.remove('hidden');
        }
    </script>
</body>
</html>
