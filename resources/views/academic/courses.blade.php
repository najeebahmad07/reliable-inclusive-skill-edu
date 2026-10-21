@extends('layouts.app')

@section('title', 'Courses Management')
@section('breadcrumb', 'Academic / Courses')

@section('content')
<div class="space-y-4">
    <!-- Header -->
    <div class="bg-white dark:bg-[#151c2c] p-4 rounded-xl border border-slate-200 dark:border-slate-800 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 shadow-xs">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 dark:text-white tracking-tight">Courses Directory</h1>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Manage certified courses linked to academic programs</p>
        </div>
        <div>
            <button onclick="document.getElementById('addCourseModal').classList.remove('hidden')" class="px-3.5 py-2 bg-brand-500 hover:bg-brand-600 text-white rounded-lg text-sm font-semibold flex items-center space-x-2 shadow-xs transition">
                <i class="fa-solid fa-plus text-xs"></i>
                <span>Add Course</span>
            </button>
        </div>
    </div>

    <!-- Filter Bar -->
    <div class="bg-white dark:bg-[#151c2c] p-4 rounded-xl border border-slate-200 dark:border-slate-800 shadow-xs">
        <form method="GET" action="{{ route('academic.courses') }}" class="flex items-center space-x-3">
            <div class="w-72">
                <select name="program_id" onchange="this.form.submit()" class="w-full px-3 py-2 bg-slate-50 dark:bg-[#182235] border border-slate-200 dark:border-slate-700 rounded-lg text-sm text-slate-800 dark:text-slate-100 focus:outline-none focus:ring-1 focus:ring-brand-500">
                    <option value="">All Programs</option>
                    @foreach($programs as $p)
                    <option value="{{ $p->id }}" {{ request('program_id') == $p->id ? 'selected' : '' }}>{{ $p->program_name }}</option>
                    @endforeach
                </select>
            </div>
            @if(request('program_id'))
            <a href="{{ route('academic.courses') }}" class="px-3 py-2 bg-slate-200 hover:bg-slate-300 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 rounded-lg text-sm font-medium transition">Reset</a>
            @endif
        </form>
    </div>

    <!-- Courses Table -->
    <div class="bg-white dark:bg-[#151c2c] rounded-xl border border-slate-200 dark:border-slate-800 overflow-hidden shadow-xs">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse table-saas">
                <thead>
                    <tr class="bg-slate-50/75 dark:bg-[#182235]/60 text-slate-500 dark:text-slate-400 border-b border-slate-200 dark:border-slate-800">
                        <th class="w-12 text-center">#</th>
                        <th>Course Title</th>
                        <th>Parent Program</th>
                        <th>Subjects</th>
                        <th>Enrolled Students</th>
                        <th class="text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800/80">
                    @forelse($courses as $index => $course)
                    <tr class="hover:bg-slate-50/75 dark:hover:bg-slate-800/30 transition-colors">
                        <td class="text-center text-slate-400 dark:text-slate-500 font-mono text-xs">{{ $courses->firstItem() + $index }}</td>
                        <td class="font-semibold text-slate-900 dark:text-slate-100 text-sm">
                            {{ $course->course_name }}
                        </td>
                        <td class="text-slate-600 dark:text-slate-300 text-sm truncate max-w-[220px]">
                            {{ $course->program->program_name ?? 'N/A' }}
                        </td>
                        <td class="text-sm font-mono font-semibold text-indigo-600 dark:text-indigo-400">{{ $course->subjects_count }}</td>
                        <td class="text-sm font-mono font-semibold text-emerald-600 dark:text-emerald-400">{{ $course->students_count }}</td>
                        <td class="text-right space-x-2 whitespace-nowrap">
                            <a href="{{ route('academic.subjects', ['course_id' => $course->id]) }}" class="text-xs text-brand-600 dark:text-brand-400 hover:underline font-semibold">Subjects</a>
                            <form action="{{ route('academic.courses.delete', $course->id) }}" method="POST" class="inline" onsubmit="return confirm('Delete this course?');">
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
                        <td colspan="6" class="text-center py-12 text-slate-400 dark:text-slate-500 text-sm">No courses found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($courses->hasPages())
        <div class="px-5 py-3.5 border-t border-slate-200 dark:border-slate-800 bg-slate-50/50 dark:bg-[#182235]/40 flex items-center justify-between">
            <span class="text-xs text-slate-500 dark:text-slate-400">Showing page {{ $courses->currentPage() }} of {{ $courses->lastPage() }}</span>
            <div>
                {{ $courses->links() }}
            </div>
        </div>
        @endif
    </div>
</div>

<!-- Add Course Modal -->
<div id="addCourseModal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4 z-50 hidden">
    <div class="bg-white dark:bg-[#151c2c] rounded-xl border border-slate-200 dark:border-slate-800 w-full max-w-md p-5 space-y-4 shadow-xl">
        <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-3">
            <h2 class="text-sm font-bold text-slate-800 dark:text-slate-100 uppercase tracking-wider">Add New Course</h2>
            <button onclick="document.getElementById('addCourseModal').classList.add('hidden')" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 text-base">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
        <form method="POST" action="{{ route('academic.courses.store') }}" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-medium text-slate-600 dark:text-slate-300 mb-1.5">Select Program *</label>
                <select name="program_id" required class="w-full px-3 py-2 bg-slate-50 dark:bg-[#182235] border border-slate-200 dark:border-slate-700 rounded-lg text-sm text-slate-800 dark:text-slate-100 focus:outline-none focus:ring-1 focus:ring-brand-500">
                    <option value="">-- Choose Program --</option>
                    @foreach($programs as $p)
                    <option value="{{ $p->id }}">{{ $p->program_name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-slate-600 dark:text-slate-300 mb-1.5">Course Title *</label>
                <input type="text" name="course_name" required placeholder="e.g. Diploma in Web Design"
                       class="w-full px-3 py-2 bg-slate-50 dark:bg-[#182235] border border-slate-200 dark:border-slate-700 rounded-lg text-sm text-slate-800 dark:text-slate-100 focus:outline-none focus:ring-1 focus:ring-brand-500">
            </div>
            <div class="flex items-center justify-end space-x-2 pt-2">
                <button type="button" onclick="document.getElementById('addCourseModal').classList.add('hidden')" class="px-3.5 py-2 bg-slate-100 dark:bg-slate-800 rounded-lg text-sm text-slate-600 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-700 transition">Cancel</button>
                <button type="submit" class="px-4 py-2 bg-brand-500 hover:bg-brand-600 text-white rounded-lg text-sm font-semibold transition">Save Course</button>
            </div>
        </form>
    </div>
</div>
@endsection
