@extends('layouts.app')

@section('title', 'Manage Jobs')
@section('breadcrumb', 'Careers / Manage Jobs')

@section('content')
<div class="space-y-4">
    <!-- Header -->
    <div class="bg-white dark:bg-[#151c2c] p-4 rounded-xl border border-slate-200 dark:border-slate-800 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 shadow-xs">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 dark:text-white tracking-tight">Job Postings & Careers</h1>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Post open positions for students and view candidate applications</p>
        </div>
        <div class="flex items-center space-x-2.5">
            <a href="{{ route('jobs.frontend') }}" target="_blank" class="px-3.5 py-2 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 rounded-lg text-sm font-medium inline-flex items-center space-x-1.5 transition">
                <i class="fa-solid fa-arrow-up-right-from-square text-xs"></i>
                <span>Public Portal</span>
            </a>
            <button onclick="document.getElementById('addJobModal').classList.remove('hidden')" class="px-3.5 py-2 bg-brand-500 hover:bg-brand-600 text-white rounded-lg text-sm font-semibold shadow-xs flex items-center space-x-2 transition">
                <i class="fa-solid fa-plus text-xs"></i>
                <span>Post New Job</span>
            </button>
        </div>
    </div>

    <!-- Jobs Table -->
    <div class="bg-white dark:bg-[#151c2c] rounded-xl border border-slate-200 dark:border-slate-800 overflow-hidden shadow-xs">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse table-saas">
                <thead>
                    <tr class="bg-slate-50/75 dark:bg-[#182235]/60 text-slate-500 dark:text-slate-400 border-b border-slate-200 dark:border-slate-800">
                        <th>Job Title</th>
                        <th>Location</th>
                        <th>Employment Type</th>
                        <th>Applications</th>
                        <th>Status</th>
                        <th>Posted Date</th>
                        <th class="text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800/80">
                    @forelse($jobs as $job)
                    <tr class="hover:bg-slate-50/75 dark:hover:bg-slate-800/30 transition-colors">
                        <td>
                            <a href="{{ route('jobs.show', $job->id) }}" class="font-bold text-slate-900 dark:text-white hover:text-brand-500 dark:hover:text-brand-400 text-sm block transition">
                                {{ $job->job_name }}
                            </a>
                        </td>
                        <td class="text-slate-600 dark:text-slate-300 text-sm">{{ $job->location ?? 'Anywhere' }}</td>
                        <td class="text-slate-600 dark:text-slate-300 text-sm">{{ $job->employment_type ?? 'Full Time' }}</td>
                        <td>
                            <a href="{{ route('jobs.show', $job->id) }}" class="inline-flex items-center px-2.5 py-0.5 rounded-full font-mono font-bold text-xs bg-brand-50 dark:bg-brand-950/40 text-brand-700 dark:text-brand-300 border border-brand-200 dark:border-brand-800">
                                {{ $job->applications_count }} Candidates
                            </a>
                        </td>
                        <td>
                            @if($job->is_published)
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 mr-1.5"></span>
                                Published
                            </span>
                            @else
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400">
                                <span class="w-1.5 h-1.5 rounded-full bg-slate-400 mr-1.5"></span>
                                Draft
                            </span>
                            @endif
                        </td>
                        <td class="font-mono text-slate-500 dark:text-slate-400 text-xs">{{ $job->created_at ? $job->created_at->format('d M Y') : 'N/A' }}</td>
                        <td class="text-right space-x-1.5 whitespace-nowrap">
                            <a href="{{ route('jobs.show', $job->id) }}" class="p-1.5 text-slate-500 hover:text-brand-500 dark:text-slate-400 dark:hover:text-brand-400 text-sm transition" title="View Applicants">
                                <i class="fa-regular fa-eye"></i>
                            </a>
                            <form action="{{ route('jobs.toggle_publish', $job->id) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" class="p-1.5 text-slate-400 hover:text-amber-500 dark:text-slate-500 dark:hover:text-amber-400 text-sm transition" title="Toggle Publish">
                                    <i class="fa-solid fa-power-off"></i>
                                </button>
                            </form>
                            <form action="{{ route('jobs.delete', $job->id) }}" method="POST" class="inline" onsubmit="return confirm('Delete this job?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="p-1.5 text-slate-400 hover:text-rose-600 dark:text-slate-500 dark:hover:text-rose-400 text-sm transition" title="Delete">
                                    <i class="fa-regular fa-trash-can"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="p-12 text-center text-slate-400 dark:text-slate-500 text-sm">No jobs posted yet.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Add Job Modal -->
<div id="addJobModal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4 z-50 hidden">
    <div class="bg-white dark:bg-[#151c2c] rounded-xl border border-slate-200 dark:border-slate-800 w-full max-w-lg p-5 space-y-4 shadow-xl">
        <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-3">
            <h2 class="text-sm font-bold text-slate-800 dark:text-slate-100 uppercase tracking-wider">Post New Job Opening</h2>
            <button onclick="document.getElementById('addJobModal').classList.add('hidden')" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 text-base">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
        <form method="POST" action="{{ route('jobs.store') }}" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-medium text-slate-600 dark:text-slate-300 mb-1.5">Job Title *</label>
                <input type="text" name="job_name" required placeholder="e.g. Junior Graphic Designer"
                       class="w-full px-3 py-2 bg-slate-50 dark:bg-[#182235] border border-slate-200 dark:border-slate-700 rounded-lg text-sm text-slate-800 dark:text-slate-100 focus:outline-none focus:ring-1 focus:ring-brand-500">
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-medium text-slate-600 dark:text-slate-300 mb-1.5">Location</label>
                    <input type="text" name="location" placeholder="e.g. Delhi / Remote"
                           class="w-full px-3 py-2 bg-slate-50 dark:bg-[#182235] border border-slate-200 dark:border-slate-700 rounded-lg text-sm text-slate-800 dark:text-slate-100 focus:outline-none focus:ring-1 focus:ring-brand-500">
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-600 dark:text-slate-300 mb-1.5">Employment Type</label>
                    <input type="text" name="employment_type" placeholder="e.g. Full Time, Internship"
                           class="w-full px-3 py-2 bg-slate-50 dark:bg-[#182235] border border-slate-200 dark:border-slate-700 rounded-lg text-sm text-slate-800 dark:text-slate-100 focus:outline-none focus:ring-1 focus:ring-brand-500">
                </div>
            </div>
            <div>
                <label class="block text-xs font-medium text-slate-600 dark:text-slate-300 mb-1.5">Job Description & Requirements</label>
                <textarea name="description" rows="4" placeholder="Skills, qualifications, salary range..."
                          class="w-full px-3 py-2 bg-slate-50 dark:bg-[#182235] border border-slate-200 dark:border-slate-700 rounded-lg text-sm text-slate-800 dark:text-slate-100 focus:outline-none focus:ring-1 focus:ring-brand-500"></textarea>
            </div>
            <div class="flex items-center justify-end space-x-2 pt-2">
                <button type="button" onclick="document.getElementById('addJobModal').classList.add('hidden')" class="px-3.5 py-2 bg-slate-100 dark:bg-slate-800 rounded-lg text-sm text-slate-600 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-700 transition">Cancel</button>
                <button type="submit" class="px-4 py-2 bg-brand-500 hover:bg-brand-600 text-white rounded-lg text-sm font-semibold transition">Publish Job</button>
            </div>
        </form>
    </div>
</div>
@endsection
