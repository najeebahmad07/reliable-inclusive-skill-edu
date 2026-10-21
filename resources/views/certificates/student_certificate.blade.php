<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Certificate - {{ $student->full_name }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@600;700;800;900&family=Great+Vibes&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            font-family: 'Inter', system-ui, sans-serif;
            background: #f1f5f9;
        }
        .font-cinzel { font-family: 'Cinzel', serif; }
        .font-script { font-family: 'Great Vibes', cursive; }
        @media print {
            body { background: transparent; padding: 0; }
            .no-print { display: none !important; }
        }
    </style>
</head>
<body class="p-4 md:p-8 flex flex-col items-center">
    <div class="no-print mb-4 flex items-center justify-between w-full max-w-4xl">
        <a href="{{ url()->previous() }}" class="px-3 py-1 bg-slate-200 text-slate-700 rounded text-xs">&larr; Back</a>
        <button onclick="window.print()" class="px-4 py-1.5 bg-blue-600 text-white font-bold rounded text-xs shadow hover:bg-blue-700">
            Print Certificate
        </button>
    </div>

    <!-- Certificate Container (Landscape A4 proportion) -->
    <div class="w-full max-w-4xl bg-white p-10 rounded-2xl shadow-xl border-8 border-double border-amber-600 relative overflow-hidden">
        <!-- Inner Border Frame -->
        <div class="border-2 border-slate-800 p-8 text-center relative">
            <!-- Corner Ornaments -->
            <div class="absolute top-2 left-2 text-amber-600 text-xl font-serif">&#10022;</div>
            <div class="absolute top-2 right-2 text-amber-600 text-xl font-serif">&#10022;</div>
            <div class="absolute bottom-2 left-2 text-amber-600 text-xl font-serif">&#10022;</div>
            <div class="absolute bottom-2 right-2 text-amber-600 text-xl font-serif">&#10022;</div>

            <!-- Header -->
            <div class="mb-4">
                <div class="flex items-center justify-center space-x-3 mb-2">
                    <img src="{{ asset('assets/images/logo.png') }}" class="h-16 w-auto" alt="RISE Logo">
                </div>
                <h1 class="text-3xl font-black font-cinzel text-slate-900 tracking-widest uppercase">
                    {{ config('rise.app_name', 'RISE') }}
                </h1>
                <p class="text-xs font-bold text-amber-700 tracking-wider uppercase mt-0.5">
                    {{ config('rise.tagline', 'Reliable Inclusive Skill Education') }}
                </p>
                <p class="text-[10px] text-slate-400 mt-0.5">An Autonomous Skill Education & Vocational Training Council</p>
            </div>

            <!-- Certificate Title -->
            <div class="my-6">
                <span class="text-xs font-semibold uppercase tracking-widest text-slate-500 block mb-1">This is to certify that</span>
                <h2 class="text-3xl font-extrabold text-blue-900 font-serif tracking-wide border-b-2 border-amber-400 inline-block px-8 pb-1">
                    {{ $student->full_name }}
                </h2>
                <div class="text-xs text-slate-500 mt-2">
                    Son / Daughter of <strong class="text-slate-800">{{ $student->father_name }}</strong>
                </div>
            </div>

            <!-- Body Statement -->
            <p class="text-xs text-slate-700 max-w-2xl mx-auto leading-relaxed mb-6">
                has successfully completed the prescribed course of study and passed the examination for
                <br>
                <span class="text-base font-bold text-slate-900 block my-2 font-cinzel">
                    {{ $student->course->course_name ?? 'VOCATIONAL TRAINING PROGRAM' }}
                </span>
                with an aggregate score of <strong class="text-blue-900 font-mono">{{ $percentage }}%</strong> under session <strong class="font-mono text-slate-800">{{ $student->session_name }}</strong> through authorized training center <strong class="text-slate-800">{{ $student->admin->institute_name ?? 'RISE Center' }}</strong>.
            </p>

            <!-- Verification Metadata -->
            <div class="flex items-center justify-between text-[11px] text-slate-600 px-6 py-2 bg-slate-50 rounded border border-slate-200 mb-8 max-w-xl mx-auto">
                <div>Enrollment No: <strong class="font-mono text-slate-900">{{ $student->enrollment_no }}</strong></div>
                <div>Certificate No: <strong class="font-mono text-blue-800">{{ $certificate->certificate_id }}</strong></div>
                <div>Issue Date: <strong class="font-mono text-slate-900">{{ $certificate->issue_date ? $certificate->issue_date->format('d-m-Y') : date('d-m-Y') }}</strong></div>
            </div>

            <!-- Signatures & Golden Seal -->
            <div class="grid grid-cols-3 items-end pt-4 text-xs text-slate-700">
                <div>
                    <div class="w-36 border-b border-slate-400 mx-auto mb-1"></div>
                    <span class="font-semibold">Center In-Charge</span>
                </div>
                <div>
                    <div class="w-20 h-20 rounded-full border-4 border-amber-500 bg-amber-50 mx-auto flex items-center justify-center text-center p-1 text-[9px] font-bold text-amber-800 uppercase shadow-inner">
                        OFFICIAL<br>SEAL
                    </div>
                </div>
                <div>
                    <div class="w-36 border-b border-slate-400 mx-auto mb-1"></div>
                    <span class="font-bold text-slate-900">Director / Controller</span>
                </div>
            </div>
        </div>
    </div>
</body>
</html>

