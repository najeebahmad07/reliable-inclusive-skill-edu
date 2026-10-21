<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Marksheet - {{ $student->full_name }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@700&family=Inter:wght@400;500;600;700&family=Playfair+Display:wght@700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            font-family: 'Inter', system-ui, sans-serif;
            background-color: #f1f5f9;
        }
        @media print {
            body { background: transparent; padding: 0; }
            .no-print { display: none !important; }
            .print-border { border: 2px solid #0f172a !important; }
        }
    </style>
</head>
<body class="p-4 md:p-8 flex flex-col items-center">
    <!-- Print Button Bar -->
    <div class="no-print w-full max-w-3xl mb-4 flex items-center justify-between">
        <a href="{{ url()->previous() }}" class="px-3 py-1.5 bg-slate-200 text-slate-700 text-xs font-semibold rounded hover:bg-slate-300 transition">&larr; Back</a>
        <button onclick="window.print()" class="px-4 py-1.5 bg-blue-600 text-white text-xs font-bold rounded shadow hover:bg-blue-700 transition">
            Print Marksheet
        </button>
    </div>

    <!-- Marksheet Container -->
    <div class="w-full max-w-3xl bg-white p-8 rounded-xl shadow-lg border border-slate-300 relative print-border">
        <!-- Watermark -->
        <div class="absolute inset-0 flex items-center justify-center opacity-5 pointer-events-none select-none">
            <span class="text-8xl font-black uppercase text-slate-900 tracking-widest">RISE</span>
        </div>

        <!-- Institute Header -->
        <div class="text-center border-b-2 border-slate-800 pb-4 mb-4">
            <div class="flex items-center justify-center space-x-3 mb-2">
                <img src="{{ asset('assets/images/logo.png') }}" class="h-14 w-auto" alt="RISE Logo">
            </div>
            <h1 class="text-xl font-black tracking-widest text-slate-900 font-serif uppercase">
                {{ config('rise.app_name', 'RISE') }}
            </h1>
            <p class="text-xs font-semibold text-slate-600 uppercase tracking-wider">{{ config('rise.tagline', 'Reliable Inclusive Skill Education') }}</p>
            <p class="text-[10px] text-slate-400 mt-0.5">An Autonomous Skill Education & Vocational Training Council</p>
            <div class="mt-2 inline-block px-4 py-1 bg-slate-900 text-white rounded text-xs font-bold uppercase tracking-widest">
                Statement of Marks
            </div>
        </div>

        <!-- Student Details Grid -->
        <div class="grid grid-cols-2 gap-x-6 gap-y-1.5 text-xs text-slate-700 mb-5 bg-slate-50/70 p-3.5 rounded border border-slate-200">
            <div><span class="text-slate-400 font-medium">Student Name:</span> <strong class="text-slate-900">{{ $student->full_name }}</strong></div>
            <div><span class="text-slate-400 font-medium">Roll No:</span> <strong class="text-slate-900 font-mono">{{ $student->roll_no }}</strong></div>
            <div><span class="text-slate-400 font-medium">Father's Name:</span> <strong class="text-slate-900">{{ $student->father_name }}</strong></div>
            <div><span class="text-slate-400 font-medium">Enrollment No:</span> <strong class="text-slate-900 font-mono">{{ $student->enrollment_no }}</strong></div>
            <div><span class="text-slate-400 font-medium">Course:</span> <strong class="text-slate-900">{{ $student->course->course_name ?? 'N/A' }}</strong></div>
            <div><span class="text-slate-400 font-medium">Session / Batch:</span> <strong class="text-slate-900 font-mono">{{ $student->session_name }}</strong> ({{ $student->batch }})</div>
            <div class="col-span-2"><span class="text-slate-400 font-medium">Center:</span> <strong class="text-slate-900">{{ $student->admin->institute_name ?? 'Center' }}</strong></div>
        </div>

        <!-- Marks Table -->
        <table class="w-full text-xs border-collapse border border-slate-300 mb-5">
            <thead>
                <tr class="bg-slate-100 text-slate-700 font-bold uppercase text-[11px]">
                    <th class="border border-slate-300 p-2 text-left">Subject Title</th>
                    <th class="border border-slate-300 p-2 text-center w-20">Max Marks</th>
                    <th class="border border-slate-300 p-2 text-center w-20">Theory</th>
                    <th class="border border-slate-300 p-2 text-center w-20">Practical</th>
                    <th class="border border-slate-300 p-2 text-center w-24">Obtained</th>
                    <th class="border border-slate-300 p-2 text-center w-16">Grade</th>
                </tr>
            </thead>
            <tbody>
                @forelse($marks as $m)
                <tr>
                    <td class="border border-slate-300 p-2 font-medium text-slate-800">{{ $m->subject->subject_name ?? 'Subject' }}</td>
                    <td class="border border-slate-300 p-2 text-center font-mono">{{ $m->subject->total_marks ?? 100 }}</td>
                    <td class="border border-slate-300 p-2 text-center font-mono">{{ $m->th_marks }}</td>
                    <td class="border border-slate-300 p-2 text-center font-mono">{{ $m->subject->has_practical ? $m->pr_marks : '-' }}</td>
                    <td class="border border-slate-300 p-2 text-center font-mono font-bold text-slate-900">{{ $m->marks_obtained }}</td>
                    <td class="border border-slate-300 p-2 text-center font-mono font-bold text-blue-700">{{ $m->grade ?? '-' }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="border border-slate-300 p-4 text-center text-slate-400">No marks entered yet.</td>
                </tr>
                @endforelse
            </tbody>
            <tfoot>
                <tr class="bg-slate-50 font-bold text-xs text-slate-800">
                    <td class="border border-slate-300 p-2 text-right">TOTAL:</td>
                    <td class="border border-slate-300 p-2 text-center font-mono">{{ $totalMaxMarks }}</td>
                    <td colspan="2" class="border border-slate-300 p-2 text-right">OBTAINED:</td>
                    <td class="border border-slate-300 p-2 text-center font-mono text-emerald-700 font-extrabold">{{ $totalObtainedMarks }}</td>
                    <td class="border border-slate-300 p-2 text-center font-mono text-blue-700">{{ $percentage }}%</td>
                </tr>
            </tfoot>
        </table>

        <!-- Result Summary -->
        <div class="flex items-center justify-between text-xs font-semibold text-slate-700 mb-12 p-3 bg-slate-50 rounded border border-slate-200">
            <div>Result: <span class="text-emerald-700 font-bold uppercase">{{ $percentage >= 40 ? 'PASSED' : 'FAILED' }}</span></div>
            <div>Percentage: <span class="font-mono text-slate-900">{{ $percentage }}%</span></div>
            <div>Issue Date: <span class="font-mono text-slate-900">{{ date('d-m-Y') }}</span></div>
        </div>

        <!-- Signatures -->
        <div class="grid grid-cols-3 text-center text-xs text-slate-600 pt-6">
            <div>
                <div class="border-b border-slate-400 w-32 mx-auto mb-1"></div>
                <span>Prepared By</span>
            </div>
            <div>
                <div class="border-b border-slate-400 w-32 mx-auto mb-1"></div>
                <span>Checked By</span>
            </div>
            <div>
                <div class="border-b border-slate-400 w-32 mx-auto mb-1"></div>
                <span class="font-bold text-slate-800">Controller of Examination</span>
            </div>
        </div>
    </div>
</body>
</html>

