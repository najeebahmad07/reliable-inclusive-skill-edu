@extends('layouts.app')

@section('title', 'Verify Certificate')
@section('breadcrumb', 'Verification / Certificate Verification')

@section('content')
<div class="max-w-2xl mx-auto space-y-4">
    <!-- Header -->
    <div class="bg-white dark:bg-[#151c2c] p-4 rounded-xl border border-slate-200 dark:border-slate-800 shadow-xs">
        <h1 class="text-2xl font-bold text-slate-900 dark:text-white tracking-tight">Certificate Authentication Verification</h1>
        <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Verify certificate authenticity by entering the unique Certificate ID</p>
    </div>

    <!-- Search Form -->
    <div class="bg-white dark:bg-[#151c2c] p-5 rounded-xl border border-slate-200 dark:border-slate-800 shadow-xs">
        <form method="GET" action="{{ route('verify.certificate') }}" class="flex items-center space-x-3">
            <div class="relative flex-1">
                <input type="text" name="certificate_id" value="{{ request('certificate_id') }}" required placeholder="e.g. RISE/CERT/2026/00025..."
                       class="w-full pl-9 pr-3 py-2.5 bg-slate-50 dark:bg-[#182235] border border-slate-200 dark:border-slate-700 rounded-lg text-sm text-slate-800 dark:text-slate-100 font-mono focus:outline-none focus:ring-1 focus:ring-brand-500">
                <i class="fa-solid fa-stamp absolute left-3 top-3.5 text-slate-400 text-xs"></i>
            </div>
            <button type="submit" class="px-5 py-2.5 bg-brand-500 hover:bg-brand-600 text-white rounded-lg text-sm font-semibold shadow-xs transition flex items-center space-x-2">
                <span>Verify</span>
            </button>
        </form>
    </div>

    @if($certificate)
    <!-- Verified Certificate Card -->
    <div class="bg-white dark:bg-[#151c2c] p-6 rounded-xl border-2 border-emerald-500 dark:border-emerald-500/80 space-y-4 shadow-sm">
        <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-3.5">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 rounded-full bg-emerald-100 dark:bg-emerald-950/50 text-emerald-600 dark:text-emerald-400 flex items-center justify-center text-base font-bold">
                    <i class="fa-solid fa-stamp"></i>
                </div>
                <div>
                    <h2 class="text-sm font-bold text-emerald-700 dark:text-emerald-400 uppercase tracking-wider">Authentic Certificate Verified</h2>
                    <p class="text-xs text-slate-400 dark:text-slate-500">Officially issued by RISE Council</p>
                </div>
            </div>
            <span class="font-mono text-xs font-bold text-slate-900 dark:text-slate-100 bg-slate-100 dark:bg-slate-800 px-3 py-1 rounded-full border border-slate-200 dark:border-slate-700">
                {{ $certificate->certificate_id }}
            </span>
        </div>

        <dl class="grid grid-cols-2 gap-x-4 gap-y-2.5 text-sm">
            <div><span class="text-xs text-slate-400 dark:text-slate-500">Issued To:</span> <strong class="text-slate-900 dark:text-white block font-semibold">{{ $certificate->student->full_name ?? 'N/A' }}</strong></div>
            <div><span class="text-xs text-slate-400 dark:text-slate-500">Enrollment No:</span> <strong class="text-slate-900 dark:text-white block font-mono">{{ $certificate->student->enrollment_no ?? 'N/A' }}</strong></div>
            <div><span class="text-xs text-slate-400 dark:text-slate-500">Program / Course:</span> <strong class="text-brand-600 dark:text-brand-400 block font-medium">{{ $certificate->student->course->course_name ?? 'N/A' }}</strong></div>
            <div><span class="text-xs text-slate-400 dark:text-slate-500">Date of Issue:</span> <strong class="text-slate-800 dark:text-slate-200 block font-mono">{{ $certificate->issue_date ? $certificate->issue_date->format('d M Y') : 'N/A' }}</strong></div>
            <div class="col-span-2"><span class="text-xs text-slate-400 dark:text-slate-500">Authorized Center:</span> <strong class="text-slate-800 dark:text-slate-200 block">{{ $certificate->student->admin->institute_name ?? 'Center' }}</strong></div>
        </dl>
    </div>
    @elseif($searched)
    <div class="bg-white dark:bg-[#151c2c] p-8 rounded-xl border-2 border-rose-300 dark:border-rose-900/60 text-center text-sm space-y-2 shadow-xs">
        <div class="w-12 h-12 rounded-full bg-rose-100 dark:bg-rose-950/50 text-rose-600 dark:text-rose-400 mx-auto flex items-center justify-center text-xl">
            <i class="fa-solid fa-triangle-exclamation"></i>
        </div>
        <h2 class="text-sm font-bold text-rose-700 dark:text-rose-400 uppercase tracking-wider">Invalid Certificate ID</h2>
        <p class="text-slate-500 dark:text-slate-400 max-w-sm mx-auto text-xs">No certificate record matched "<span class="font-mono text-slate-700 dark:text-slate-300">{{ request('certificate_id') }}</span>". Please verify the code on your certificate document.</p>
    </div>
    @endif
</div>
@endsection
