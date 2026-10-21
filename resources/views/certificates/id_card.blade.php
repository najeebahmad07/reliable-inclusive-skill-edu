<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ID Card - {{ $student->full_name }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            font-family: 'Inter', system-ui, sans-serif;
            background: #f1f5f9;
        }
        @media print {
            body { background: transparent; padding: 0; }
            .no-print { display: none !important; }
        }
    </style>
</head>
<body class="p-6 flex flex-col items-center">
    <div class="no-print mb-4 flex items-center justify-between w-full max-w-sm">
        <a href="{{ url()->previous() }}" class="px-3 py-1 bg-slate-200 text-slate-700 rounded text-xs">&larr; Back</a>
        <button onclick="window.print()" class="px-4 py-1.5 bg-blue-600 text-white font-bold rounded text-xs shadow hover:bg-blue-700">
            Print ID Card
        </button>
    </div>

    <!-- ID Card (Standard Card Proportion) -->
    <div class="w-[340px] bg-white rounded-xl shadow-lg border border-slate-300 overflow-hidden relative">
        <!-- Top Navy Header -->
        <div class="bg-[#0f172a] text-white p-3 text-center border-b-2 border-amber-400">
            <div class="flex items-center justify-center space-x-2 mb-1">
                <img src="{{ asset('assets/images/logo.png') }}" class="h-6 w-auto" alt="RISE Logo">
                <h1 class="text-sm font-black tracking-wider uppercase leading-none">{{ config('rise.app_name', 'RISE') }}</h1>
            </div>
            <p class="text-[9px] text-slate-300 tracking-tight mt-0.5">{{ config('rise.tagline', 'Reliable Inclusive Skill Education') }}</p>
            <div class="mt-1 inline-block px-2 py-0.5 bg-amber-400 text-slate-900 rounded-full text-[8.5px] font-bold uppercase tracking-widest leading-none">
                Student Identity Card
            </div>
        </div>

        <!-- Student Photo & Details -->
        <div class="p-4 space-y-3">
            <div class="flex items-center space-x-3">
                <div class="w-20 h-24 rounded border border-slate-300 bg-slate-100 overflow-hidden flex-shrink-0 flex items-center justify-center text-xs text-slate-400 font-bold">
                    @if($student->photo && file_exists(public_path('uploads/photos/' . $student->photo)))
                    <img src="{{ asset('uploads/photos/' . $student->photo) }}" class="w-full h-full object-cover">
                    @else
                    PHOTO
                    @endif
                </div>

                <div class="flex-1 min-w-0 text-xs">
                    <h2 class="font-bold text-slate-900 text-sm leading-tight truncate">{{ $student->full_name }}</h2>
                    <p class="text-[10px] text-blue-600 font-semibold truncate mt-0.5">{{ $student->course->course_name ?? 'Student' }}</p>

                    <div class="mt-2 space-y-0.5 text-[10px] text-slate-600">
                        <div>Roll: <strong class="text-slate-800 font-mono">{{ $student->roll_no }}</strong></div>
                        <div>Enroll: <strong class="text-slate-800 font-mono">{{ $student->enrollment_no }}</strong></div>
                        <div>DOB: <strong class="text-slate-800">{{ $student->dob ? $student->dob->format('d/m/Y') : 'N/A' }}</strong></div>
                    </div>
                </div>
            </div>

            <!-- Extra Info -->
            <div class="pt-2 border-t border-slate-100 text-[10px] text-slate-600 space-y-0.5">
                <div>Father: <span class="text-slate-800 font-medium">{{ $student->father_name }}</span></div>
                <div>Mobile: <span class="text-slate-800 font-mono">{{ $student->mobile }}</span></div>
                <div class="truncate">Center: <span class="text-slate-800 font-medium">{{ $student->admin->institute_name ?? 'Head Office' }}</span></div>
            </div>

            <!-- Signature & Seal -->
            <div class="pt-3 border-t border-slate-100 flex items-end justify-between text-[9px] text-slate-500">
                <div class="text-center">
                    <div class="w-16 h-6 border-b border-slate-300 mx-auto flex items-center justify-center">
                        @if($student->signature && file_exists(public_path('uploads/signatures/' . $student->signature)))
                        <img src="{{ asset('uploads/signatures/' . $student->signature) }}" class="max-h-5 object-contain">
                        @endif
                    </div>
                    <span>Student Sign</span>
                </div>
                <div class="text-center">
                    <div class="w-16 h-6 border-b border-slate-300 mx-auto flex items-center justify-center font-bold text-slate-800 text-[8px]">
                        AUTHORIZED
                    </div>
                    <span>Director Seal</span>
                </div>
            </div>
        </div>

        <!-- Footer strip -->
        <div class="bg-slate-100 px-3 py-1 text-center text-[8px] text-slate-500 border-t border-slate-200">
            Valid across all examination and training centers.
        </div>
    </div>
</body>
</html>

