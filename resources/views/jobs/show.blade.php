@extends('layouts.app')

@section('title', $job->job_name . ' - Applications')
@section('breadcrumb', 'Jobs / ' . $job->job_name)

@section('content')
<div class="max-w-4xl mx-auto space-y-4">
    <!-- Header -->
    <div class="bg-white dark:bg-[#151c2c] p-5 rounded-xl border border-slate-200 dark:border-slate-800 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 shadow-xs">
        <div>
            <div class="flex items-center space-x-2.5">
                <h1 class="text-xl font-bold text-slate-900 dark:text-white">{{ $job->job_name }}</h1>
                @if($job->is_published)
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 mr-1.5"></span>
                    Published
                </span>
                @else
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400">
                    Draft
                </span>
                @endif
            </div>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Location: <strong class="text-slate-700 dark:text-slate-300">{{ $job->location ?? 'Any' }}</strong> &bull; Type: <strong class="text-slate-700 dark:text-slate-300">{{ $job->employment_type ?? 'Full Time' }}</strong></p>
        </div>
        <a href="{{ route('jobs.index') }}" class="px-3.5 py-2 text-slate-600 dark:text-slate-300 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 rounded-lg text-sm font-medium transition flex items-center space-x-1.5">
            <i class="fa-solid fa-arrow-left text-xs"></i>
            <span>Back to Jobs</span>
        </a>
    </div>

    <!-- Description -->
    @if($job->description)
    <div class="bg-white dark:bg-[#151c2c] p-5 rounded-xl border border-slate-200 dark:border-slate-800 text-sm shadow-xs">
        <h2 class="text-sm font-bold text-slate-800 dark:text-slate-100 uppercase tracking-wider mb-2">Job Description</h2>
        <div class="text-slate-700 dark:text-slate-300 leading-relaxed whitespace-pre-line">{{ $job->description }}</div>
    </div>
    @endif

    <!-- Applications List -->
    <div class="bg-white dark:bg-[#151c2c] rounded-xl border border-slate-200 dark:border-slate-800 overflow-hidden shadow-xs">
        <div class="px-5 py-3.5 bg-slate-50/75 dark:bg-[#182235]/60 border-b border-slate-200 dark:border-slate-800 flex items-center justify-between">
            <h2 class="text-sm font-bold text-slate-800 dark:text-slate-100 uppercase tracking-wider">Candidate Applications ({{ $job->applications->count() }})</h2>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse table-saas">
                <thead>
                    <tr class="bg-slate-50/50 dark:bg-[#182235]/40 text-slate-500 dark:text-slate-400 border-b border-slate-200 dark:border-slate-800">
                        <th>Candidate Name</th>
                        <th>Email</th>
                        <th>Contact Number</th>
                        <th>Applied On</th>
                        <th class="text-right">Resume</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800/80">
                    @forelse($job->applications as $app)
                    <tr class="hover:bg-slate-50/75 dark:hover:bg-slate-800/30 transition-colors">
                        <td class="font-bold text-slate-900 dark:text-white text-sm">{{ $app->candidate_name }}</td>
                        <td class="text-slate-600 dark:text-slate-300 text-sm">{{ $app->email }}</td>
                        <td class="text-slate-700 dark:text-slate-300 text-sm font-mono">{{ $app->contact_number }}</td>
                        <td class="text-slate-400 dark:text-slate-500 text-xs font-mono">{{ $app->applied_at ? $app->applied_at->format('d M Y, h:i A') : 'N/A' }}</td>
                        <td class="text-right">
                            @if($app->resume_file)
                            <a href="{{ asset('uploads/job_resumes/' . $app->resume_file) }}" target="_blank" class="px-2.5 py-1 rounded-md bg-brand-50 hover:bg-brand-100 dark:bg-brand-950/40 dark:hover:bg-brand-900/40 text-brand-700 dark:text-brand-300 border border-brand-200 dark:border-brand-800 text-xs font-semibold inline-flex items-center space-x-1.5 transition">
                                <i class="fa-solid fa-file-arrow-down text-xs"></i>
                                <span>Download Resume</span>
                            </a>
                            @else
                            <span class="text-slate-400 dark:text-slate-500 text-xs">No file</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="p-10 text-center text-slate-400 dark:text-slate-500 text-sm">No candidate applications submitted yet.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
