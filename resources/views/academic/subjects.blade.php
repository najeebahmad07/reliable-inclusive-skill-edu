@extends('layouts.app')

@section('title', 'Subjects Directory')
@section('breadcrumb', 'Academic / Subjects')

@section('content')
<div class="space-y-4">
    <!-- Header -->
    <div class="bg-white dark:bg-[#151c2c] p-4 rounded-xl border border-slate-200 dark:border-slate-800 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 shadow-xs">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 dark:text-white tracking-tight">Subjects Directory</h1>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Configure curriculum subjects, max marks, and practical exams</p>
        </div>
        <div>
            <button onclick="document.getElementById('addSubjectModal').classList.remove('hidden')" class="px-3.5 py-2 bg-brand-500 hover:bg-brand-600 text-white rounded-lg text-sm font-semibold flex items-center space-x-2 shadow-xs transition">
                <i class="fa-solid fa-plus text-xs"></i>
                <span>Add Subject</span>
            </button>
        </div>
    </div>

    <!-- Filter Bar -->
    <div class="bg-white dark:bg-[#151c2c] p-4 rounded-xl border border-slate-200 dark:border-slate-800 shadow-xs">
        <form method="GET" action="{{ route('academic.subjects') }}" class="flex flex-wrap items-center gap-3">
            <div class="w-72">
                <select name="course_id" onchange="this.form.submit()" class="w-full px-3 py-2 bg-slate-50 dark:bg-[#182235] border border-slate-200 dark:border-slate-700 rounded-lg text-sm text-slate-800 dark:text-slate-100 focus:outline-none focus:ring-1 focus:ring-brand-500">
                    <option value="">All Courses</option>
                    @foreach($courses as $c)
                    <option value="{{ $c->id }}" {{ request('course_id') == $c->id ? 'selected' : '' }}>{{ $c->course_name }}</option>
                    @endforeach
                </select>
            </div>
            @if(request('course_id'))
            <a href="{{ route('academic.subjects') }}" class="px-3 py-2 bg-slate-200 hover:bg-slate-300 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 rounded-lg text-sm font-medium transition">Reset</a>
            @endif
        </form>
    </div>

    <!-- Subjects Table -->
    <div class="bg-white dark:bg-[#151c2c] rounded-xl border border-slate-200 dark:border-slate-800 overflow-hidden shadow-xs">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse table-saas">
                <thead>
                    <tr class="bg-slate-50/75 dark:bg-[#182235]/60 text-slate-500 dark:text-slate-400 border-b border-slate-200 dark:border-slate-800">
                        <th class="w-12 text-center">#</th>
                        <th>Subject Name</th>
                        <th>Course</th>
                        <th>Program</th>
                        <th>Total Marks</th>
                        <th>Practical Marks</th>
                        <th class="text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800/80">
                    @forelse($subjects as $index => $subject)
                    <tr class="hover:bg-slate-50/75 dark:hover:bg-slate-800/30 transition-colors">
                        <td class="text-center text-slate-400 dark:text-slate-500 font-mono text-xs">{{ $subjects->firstItem() + $index }}</td>
                        <td class="font-semibold text-slate-900 dark:text-slate-100 text-sm">
                            {{ $subject->subject_name }}
                        </td>
                        <td class="text-slate-600 dark:text-slate-300 text-sm truncate max-w-[200px]">
                            {{ $subject->course->course_name ?? 'N/A' }}
                        </td>
                        <td class="text-slate-500 dark:text-slate-400 text-xs truncate max-w-[180px]">
                            {{ $subject->program->program_name ?? 'N/A' }}
                        </td>
                        <td class="font-mono text-slate-800 dark:text-slate-200 font-bold text-sm">{{ $subject->total_marks }}</td>
                        <td>
                            @if($subject->has_practical)
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-indigo-50 dark:bg-indigo-950/40 text-indigo-700 dark:text-indigo-400 border border-indigo-200 dark:border-indigo-800">
                                {{ $subject->practical_marks }} Marks
                            </span>
                            @else
                            <span class="text-slate-400 dark:text-slate-500 text-xs">-</span>
                            @endif
                        </td>
                        <td class="text-right">
                            <form action="{{ route('academic.subjects.delete', $subject->id) }}" method="POST" class="inline" onsubmit="return confirm('Delete this subject?');">
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
                        <td colspan="7" class="text-center py-12 text-slate-400 dark:text-slate-500 text-sm">No subjects found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($subjects->hasPages())
        <div class="px-5 py-3.5 border-t border-slate-200 dark:border-slate-800 bg-slate-50/50 dark:bg-[#182235]/40 flex items-center justify-between">
            <span class="text-xs text-slate-500 dark:text-slate-400">Showing page {{ $subjects->currentPage() }} of {{ $subjects->lastPage() }}</span>
            <div>
                {{ $subjects->links() }}
            </div>
        </div>
        @endif
    </div>
</div>

<!-- Add Subject Modal -->
<div id="addSubjectModal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4 z-50 hidden">
    <div class="bg-white dark:bg-[#151c2c] rounded-xl border border-slate-200 dark:border-slate-800 w-full max-w-md p-5 space-y-4 shadow-xl">
        <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-3">
            <h2 class="text-sm font-bold text-slate-800 dark:text-slate-100 uppercase tracking-wider">Add New Subject</h2>
            <button onclick="document.getElementById('addSubjectModal').classList.add('hidden')" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 text-base">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
        <form method="POST" action="{{ route('academic.subjects.store') }}" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-medium text-slate-600 dark:text-slate-300 mb-1.5">Select Program *</label>
                <select name="program_id" id="modalProgSelect" required class="w-full px-3 py-2 bg-slate-50 dark:bg-[#182235] border border-slate-200 dark:border-slate-700 rounded-lg text-sm text-slate-800 dark:text-slate-100 focus:outline-none focus:ring-1 focus:ring-brand-500">
                    <option value="">-- Choose Program --</option>
                    @foreach($programs as $p)
                    <option value="{{ $p->id }}">{{ $p->program_name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-slate-600 dark:text-slate-300 mb-1.5">Select Course *</label>
                <select name="course_id" id="modalCourseSelect" required class="w-full px-3 py-2 bg-slate-50 dark:bg-[#182235] border border-slate-200 dark:border-slate-700 rounded-lg text-sm text-slate-800 dark:text-slate-100 focus:outline-none focus:ring-1 focus:ring-brand-500">
                    <option value="">-- Choose Course --</option>
                    @foreach($courses as $c)
                    <option value="{{ $c->id }}" data-prog="{{ $c->program_id }}">{{ $c->course_name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-slate-600 dark:text-slate-300 mb-1.5">Subject Name *</label>
                <input type="text" name="subject_name" required placeholder="e.g. Fundamental of Computers"
                       class="w-full px-3 py-2 bg-slate-50 dark:bg-[#182235] border border-slate-200 dark:border-slate-700 rounded-lg text-sm text-slate-800 dark:text-slate-100 focus:outline-none focus:ring-1 focus:ring-brand-500">
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-medium text-slate-600 dark:text-slate-300 mb-1.5">Total Marks *</label>
                    <input type="number" name="total_marks" value="100" required class="w-full px-3 py-2 bg-slate-50 dark:bg-[#182235] border border-slate-200 dark:border-slate-700 rounded-lg text-sm text-slate-800 dark:text-slate-100 focus:outline-none focus:ring-1 focus:ring-brand-500 font-mono">
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-600 dark:text-slate-300 mb-1.5">Practical Marks</label>
                    <input type="number" name="practical_marks" value="0" class="w-full px-3 py-2 bg-slate-50 dark:bg-[#182235] border border-slate-200 dark:border-slate-700 rounded-lg text-sm text-slate-800 dark:text-slate-100 focus:outline-none focus:ring-1 focus:ring-brand-500 font-mono">
                </div>
            </div>
            <div class="flex items-center space-x-2.5 pt-1">
                <input type="checkbox" name="has_practical" value="1" id="has_pr" class="rounded text-brand-500 focus:ring-brand-500">
                <label for="has_pr" class="text-sm text-slate-700 dark:text-slate-200">Has Practical Exam</label>
            </div>
            <div class="flex items-center justify-end space-x-2 pt-2">
                <button type="button" onclick="document.getElementById('addSubjectModal').classList.add('hidden')" class="px-3.5 py-2 bg-slate-100 dark:bg-slate-800 rounded-lg text-sm text-slate-600 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-700 transition">Cancel</button>
                <button type="submit" class="px-4 py-2 bg-brand-500 hover:bg-brand-600 text-white rounded-lg text-sm font-semibold transition">Save Subject</button>
            </div>
        </form>
    </div>
</div>
@endsection
