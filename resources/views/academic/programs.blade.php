@extends('layouts.app')

@section('title', 'Programs Management')
@section('breadcrumb', 'Academic / Programs')

@section('content')
<div class="space-y-4">
    <!-- Header -->
    <div class="bg-white dark:bg-[#151c2c] p-4 rounded-xl border border-slate-200 dark:border-slate-800 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 shadow-xs">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 dark:text-white tracking-tight">Educational Programs</h1>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Configure academic programs, diplomas, certifications, and durations</p>
        </div>
        <div>
            <button onclick="document.getElementById('addProgramModal').classList.remove('hidden')" class="px-3.5 py-2 bg-brand-500 hover:bg-brand-600 text-white rounded-lg text-sm font-semibold flex items-center space-x-2 shadow-xs transition">
                <i class="fa-solid fa-plus text-xs"></i>
                <span>Add Program</span>
            </button>
        </div>
    </div>

    <!-- Programs Grid / Table -->
    <div class="bg-white dark:bg-[#151c2c] rounded-xl border border-slate-200 dark:border-slate-800 overflow-hidden shadow-xs">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse table-saas">
                <thead>
                    <tr class="bg-slate-50/75 dark:bg-[#182235]/60 text-slate-500 dark:text-slate-400 border-b border-slate-200 dark:border-slate-800">
                        <th class="w-12 text-center">#</th>
                        <th>Program Name</th>
                        <th>Duration</th>
                        <th>Courses</th>
                        <th>Subjects</th>
                        <th>Enrolled Students</th>
                        <th class="text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800/80">
                    @forelse($programs as $index => $program)
                    <tr class="hover:bg-slate-50/75 dark:hover:bg-slate-800/30 transition-colors">
                        <td class="text-center text-slate-400 dark:text-slate-500 font-mono text-xs">{{ $index + 1 }}</td>
                        <td class="font-semibold text-slate-900 dark:text-slate-100 text-sm">
                            {{ $program->program_name }}
                        </td>
                        <td>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 text-xs font-medium">
                                {{ $program->duration }}
                            </span>
                        </td>
                        <td class="text-sm font-mono font-semibold text-brand-600 dark:text-brand-400">{{ $program->courses_count }}</td>
                        <td class="text-sm font-mono font-semibold text-indigo-600 dark:text-indigo-400">{{ $program->subjects_count }}</td>
                        <td class="text-sm font-mono font-semibold text-emerald-600 dark:text-emerald-400">{{ $program->students_count }}</td>
                        <td class="text-right space-x-2 whitespace-nowrap">
                            <a href="{{ route('academic.courses', ['program_id' => $program->id]) }}" class="text-xs text-brand-600 dark:text-brand-400 hover:underline font-semibold">View Courses</a>
                            <form action="{{ route('academic.programs.delete', $program->id) }}" method="POST" class="inline" onsubmit="return confirm('Delete this program?');">
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
                        <td colspan="7" class="text-center py-12 text-slate-400 dark:text-slate-500 text-sm">No programs found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Add Program Modal -->
<div id="addProgramModal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4 z-50 hidden">
    <div class="bg-white dark:bg-[#151c2c] rounded-xl border border-slate-200 dark:border-slate-800 w-full max-w-md p-5 space-y-4 shadow-xl">
        <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-3">
            <h2 class="text-sm font-bold text-slate-800 dark:text-slate-100 uppercase tracking-wider">Add New Program</h2>
            <button onclick="document.getElementById('addProgramModal').classList.add('hidden')" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 text-base">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
        <form method="POST" action="{{ route('academic.programs.store') }}" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-medium text-slate-600 dark:text-slate-300 mb-1.5">Program Title *</label>
                <input type="text" name="program_name" required placeholder="e.g. Diploma in Computer Application"
                       class="w-full px-3 py-2 bg-slate-50 dark:bg-[#182235] border border-slate-200 dark:border-slate-700 rounded-lg text-sm text-slate-800 dark:text-slate-100 focus:outline-none focus:ring-1 focus:ring-brand-500">
            </div>
            <div>
                <label class="block text-xs font-medium text-slate-600 dark:text-slate-300 mb-1.5">Duration *</label>
                <input type="text" name="duration" required placeholder="e.g. 1 Year / 6 Months"
                       class="w-full px-3 py-2 bg-slate-50 dark:bg-[#182235] border border-slate-200 dark:border-slate-700 rounded-lg text-sm text-slate-800 dark:text-slate-100 focus:outline-none focus:ring-1 focus:ring-brand-500">
            </div>
            <div class="flex items-center justify-end space-x-2 pt-2">
                <button type="button" onclick="document.getElementById('addProgramModal').classList.add('hidden')" class="px-3.5 py-2 bg-slate-100 dark:bg-slate-800 rounded-lg text-sm text-slate-600 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-700 transition">Cancel</button>
                <button type="submit" class="px-4 py-2 bg-brand-500 hover:bg-brand-600 text-white rounded-lg text-sm font-semibold transition">Save Program</button>
            </div>
        </form>
    </div>
</div>
@endsection
