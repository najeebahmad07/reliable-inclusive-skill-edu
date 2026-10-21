@extends('layouts.app')

@section('title', 'Verify Student')
@section('breadcrumb', 'Verification / Verify Student')

@section('content')
<div class="max-w-2xl mx-auto space-y-4">
    <!-- Header -->
    <div class="bg-white dark:bg-[#151c2c] p-4 rounded-xl border border-slate-200 dark:border-slate-800 shadow-xs">
        <h1 class="text-2xl font-bold text-slate-900 dark:text-white tracking-tight">Student Credential Verification</h1>
        <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Verify student enrollment authenticity by entering their Roll Number or Enrollment Number</p>
    </div>

    <!-- Search Form -->
    <div class="bg-white dark:bg-[#151c2c] p-5 rounded-xl border border-slate-200 dark:border-slate-800 shadow-xs">
        <form method="GET" action="{{ route('verify.student') }}" class="flex items-center space-x-3">
            <div class="relative flex-1">
                <input type="text" name="query" value="{{ request('query') }}" required placeholder="Enter Enrollment No or Roll No..."
                       class="w-full pl-9 pr-3 py-2.5 bg-slate-50 dark:bg-[#182235] border border-slate-200 dark:border-slate-700 rounded-lg text-sm text-slate-800 dark:text-slate-100 font-mono focus:outline-none focus:ring-1 focus:ring-brand-500">
                <i class="fa-solid fa-magnifying-glass absolute left-3 top-3.5 text-slate-400 text-xs"></i>
            </div>
            <button type="submit" class="px-5 py-2.5 bg-brand-500 hover:bg-brand-600 text-white rounded-lg text-sm font-semibold shadow-xs transition flex items-center space-x-2">
                <span>Verify</span>
            </button>
        </form>
    </div>

    @if($student)
    <!-- Verified Result Card -->
    <div class="bg-white dark:bg-[#151c2c] p-6 rounded-xl border-2 border-emerald-500 dark:border-emerald-500/80 space-y-4 shadow-sm relative">
        <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-3.5">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 rounded-full bg-emerald-100 dark:bg-emerald-950/50 text-emerald-600 dark:text-emerald-400 flex items-center justify-center text-base font-bold">
                    <i class="fa-solid fa-check"></i>
                </div>
                <div>
                    <h2 class="text-sm font-bold text-emerald-700 dark:text-emerald-400 uppercase tracking-wider">Verified Authentic Student</h2>
                    <p class="text-xs text-slate-400 dark:text-slate-500">Record found in central RISE registry</p>
                </div>
            </div>
            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800 font-mono">
                {{ $student->status }}
            </span>
        </div>

        <div class="flex flex-col sm:flex-row items-center gap-5">
            <div class="w-24 h-28 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 overflow-hidden flex-shrink-0 flex items-center justify-center text-sm text-slate-400 font-bold">
                @if($student->photo && file_exists(public_path('uploads/photos/' . $student->photo)))
                <img src="{{ asset('uploads/photos/' . $student->photo) }}" class="w-full h-full object-cover">
                @else
                {{ strtoupper(substr($student->full_name, 0, 1)) }}
                @endif
            </div>

            <dl class="flex-1 grid grid-cols-2 gap-x-4 gap-y-2 text-sm">
                <div><span class="text-xs text-slate-400 dark:text-slate-500">Full Name:</span> <strong class="text-slate-900 dark:text-white block font-semibold">{{ $student->full_name }}</strong></div>
                <div><span class="text-xs text-slate-400 dark:text-slate-500">Roll No:</span> <strong class="text-slate-900 dark:text-white block font-mono">{{ $student->roll_no }}</strong></div>
                <div><span class="text-xs text-slate-400 dark:text-slate-500">Enrollment No:</span> <strong class="text-slate-900 dark:text-white block font-mono">{{ $student->enrollment_no }}</strong></div>
                <div><span class="text-xs text-slate-400 dark:text-slate-500">Father's Name:</span> <strong class="text-slate-800 dark:text-slate-200 block">{{ $student->father_name }}</strong></div>
                <div><span class="text-xs text-slate-400 dark:text-slate-500">Course:</span> <strong class="text-brand-600 dark:text-brand-400 block font-semibold">{{ $student->course->course_name ?? 'N/A' }}</strong></div>
                <div><span class="text-xs text-slate-400 dark:text-slate-500">Session:</span> <strong class="text-slate-800 dark:text-slate-200 block font-mono">{{ $student->session_name }}</strong></div>
                <div class="col-span-2"><span class="text-xs text-slate-400 dark:text-slate-500">Training Center:</span> <strong class="text-slate-800 dark:text-slate-200 block">{{ $student->admin->institute_name ?? 'Head Office' }}</strong></div>
            </dl>
        </div>

        <div class="pt-3 border-t border-slate-100 dark:border-slate-800 flex items-center justify-end space-x-3 text-xs">
            <a href="{{ route('certificates.id_card', $student->id) }}" target="_blank" class="text-brand-600 dark:text-brand-400 hover:underline font-semibold">View ID Card &rarr;</a>
            <a href="{{ route('marks.marksheet', $student->id) }}" target="_blank" class="text-brand-600 dark:text-brand-400 hover:underline font-semibold">View Marksheet &rarr;</a>
        </div>
    </div>
    @elseif($searched)
    <div class="bg-white dark:bg-[#151c2c] p-8 rounded-xl border-2 border-rose-300 dark:border-rose-900/60 text-center text-sm space-y-2 shadow-xs">
        <div class="w-12 h-12 rounded-full bg-rose-100 dark:bg-rose-950/50 text-rose-600 dark:text-rose-400 mx-auto flex items-center justify-center text-xl">
            <i class="fa-solid fa-triangle-exclamation"></i>
        </div>
        <h2 class="text-sm font-bold text-rose-700 dark:text-rose-400 uppercase tracking-wider">Record Not Found</h2>
        <p class="text-slate-500 dark:text-slate-400 max-w-sm mx-auto text-xs">No enrolled student matched the roll or enrollment number "<span class="font-mono text-slate-700 dark:text-slate-300">{{ request('query') }}</span>". Please verify the credentials or contact administration.</p>
    </div>
    @endif
</div>
@endsection
