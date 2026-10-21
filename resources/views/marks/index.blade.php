@extends('layouts.app')

@section('title', 'Marks Entry')
@section('breadcrumb', 'Academics / Marks Entry')

@section('content')
<div class="max-w-4xl mx-auto space-y-4">
    <!-- Header -->
    <div class="bg-white dark:bg-[#151c2c] p-4 rounded-xl border border-slate-200 dark:border-slate-800 flex items-center justify-between shadow-xs">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 dark:text-white tracking-tight">Student Marks Entry</h1>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Select an approved student to input theoretical and practical marks</p>
        </div>
    </div>

    <!-- Student Selection Box -->
    <div class="bg-white dark:bg-[#151c2c] p-5 rounded-xl border border-slate-200 dark:border-slate-800 shadow-xs">
        <form method="GET" action="{{ route('marks.index') }}" class="flex flex-col sm:flex-row items-end gap-3">
            <div class="flex-1 w-full">
                <label class="block text-xs font-medium text-slate-600 dark:text-slate-300 mb-1.5">Select Approved Student *</label>
                <select name="student_id" required class="w-full px-3 py-2 bg-slate-50 dark:bg-[#182235] border border-slate-200 dark:border-slate-700 rounded-lg text-sm text-slate-800 dark:text-slate-100 focus:outline-none focus:ring-1 focus:ring-brand-500">
                    <option value="">-- Choose Student --</option>
                    @foreach($students as $st)
                    <option value="{{ $st->id }}" {{ request('student_id') == $st->id ? 'selected' : '' }}>
                        {{ $st->full_name }} (Roll: {{ $st->roll_no }} | {{ $st->course->course_name ?? 'N/A' }})
                    </option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="px-5 py-2.5 bg-brand-500 hover:bg-brand-600 text-white rounded-lg text-sm font-semibold shadow-xs transition flex items-center space-x-2">
                <i class="fa-solid fa-list-check text-xs"></i>
                <span>Load Subjects</span>
            </button>
        </form>
    </div>

    @if($selectedStudent)
    <!-- Student Quick Info -->
    <div class="bg-white dark:bg-[#151c2c] p-4 rounded-xl border border-slate-200 dark:border-slate-800 flex flex-wrap items-center justify-between gap-3 text-sm shadow-xs">
        <div>
            <span class="font-bold text-slate-900 dark:text-white text-base">{{ $selectedStudent->full_name }}</span>
            <span class="text-slate-500 dark:text-slate-400 block text-xs mt-0.5">
                Roll: <strong class="text-slate-700 dark:text-slate-200 font-mono">{{ $selectedStudent->roll_no }}</strong> &bull;
                Course: <strong class="text-slate-700 dark:text-slate-200">{{ $selectedStudent->course->course_name ?? 'N/A' }}</strong> &bull;
                Session: <strong class="text-slate-700 dark:text-slate-200 font-mono">{{ $selectedStudent->session_name }}</strong>
            </span>
        </div>
        <div>
            <a href="{{ route('marks.marksheet', $selectedStudent->id) }}" target="_blank" class="px-3.5 py-2 bg-amber-50 hover:bg-amber-100 dark:bg-amber-950/40 dark:hover:bg-amber-900/40 text-amber-700 dark:text-amber-300 border border-amber-200 dark:border-amber-800 rounded-lg text-xs font-semibold inline-flex items-center space-x-1.5 transition">
                <i class="fa-solid fa-file-invoice text-xs"></i>
                <span>Preview Marksheet</span>
            </a>
        </div>
    </div>

    <!-- Marks Entry Form -->
    <div class="bg-white dark:bg-[#151c2c] rounded-xl border border-slate-200 dark:border-slate-800 overflow-hidden shadow-xs">
        <div class="px-5 py-3.5 bg-slate-50/75 dark:bg-[#182235]/60 border-b border-slate-200 dark:border-slate-800 flex items-center justify-between">
            <h2 class="text-sm font-bold text-slate-800 dark:text-slate-100 uppercase tracking-wider">Curriculum Subjects & Marks</h2>
            <span class="text-xs text-slate-500 dark:text-slate-400">Grades are calculated automatically</span>
        </div>

        <form method="POST" action="{{ route('marks.store') }}">
            @csrf
            <input type="hidden" name="student_id" value="{{ $selectedStudent->id }}">

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse table-saas">
                    <thead>
                        <tr class="bg-slate-50/50 dark:bg-[#182235]/40 text-slate-500 dark:text-slate-400 border-b border-slate-200 dark:border-slate-800">
                            <th>Subject Name</th>
                            <th>Total Max</th>
                            <th>Theory Marks</th>
                            <th>Practical Marks</th>
                            <th>Total Obtained</th>
                            <th>Grade</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800/80">
                        @forelse($subjects as $sub)
                        @php
                            $m = $existingMarks->get($sub->id);
                        @endphp
                        <tr class="hover:bg-slate-50/75 dark:hover:bg-slate-800/30 transition-colors">
                            <td>
                                <span class="font-medium text-slate-800 dark:text-slate-200 text-sm">{{ $sub->subject_name }}</span>
                            </td>
                            <td class="font-mono text-slate-500 dark:text-slate-400 text-sm">{{ $sub->total_marks }}</td>
                            <td class="w-32">
                                <input type="number" name="marks[{{ $sub->id }}][th_marks]" value="{{ $m ? $m->th_marks : 0 }}" min="0" max="{{ $sub->total_marks }}"
                                       class="w-full px-3 py-1.5 bg-slate-50 dark:bg-[#182235] border border-slate-200 dark:border-slate-700 rounded-lg text-sm text-slate-800 dark:text-slate-100 font-mono text-center focus:outline-none focus:ring-1 focus:ring-brand-500">
                            </td>
                            <td class="w-32">
                                @if($sub->has_practical)
                                <input type="number" name="marks[{{ $sub->id }}][pr_marks]" value="{{ $m ? $m->pr_marks : 0 }}" min="0" max="{{ $sub->practical_marks }}"
                                       class="w-full px-3 py-1.5 bg-slate-50 dark:bg-[#182235] border border-slate-200 dark:border-slate-700 rounded-lg text-sm text-slate-800 dark:text-slate-100 font-mono text-center focus:outline-none focus:ring-1 focus:ring-brand-500">
                                @else
                                <span class="text-slate-400 dark:text-slate-500 text-center block text-sm">-</span>
                                @endif
                            </td>
                            <td class="font-mono font-bold text-slate-800 dark:text-slate-100 text-sm">
                                {{ $m ? $m->marks_obtained : '-' }}
                            </td>
                            <td>
                                @if($m && $m->grade)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-brand-50 dark:bg-brand-950/40 text-brand-700 dark:text-brand-300 border border-brand-200 dark:border-brand-800 font-mono">{{ $m->grade }}</span>
                                @else
                                <span class="text-slate-400 dark:text-slate-500 text-xs">-</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-10 text-slate-400 dark:text-slate-500 text-sm">
                                No subjects mapped to course '{{ $selectedStudent->course->course_name ?? '' }}'. Please add subjects in Academic &rarr; Subjects.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($subjects->isNotEmpty())
            <div class="p-4 border-t border-slate-200 dark:border-slate-800 bg-slate-50/50 dark:bg-[#182235]/40 flex items-center justify-end">
                <button type="submit" class="px-6 py-2.5 bg-brand-500 hover:bg-brand-600 text-white rounded-lg text-sm font-bold shadow-sm transition">
                    Save Marks
                </button>
            </div>
            @endif
        </form>
    </div>
    @endif
</div>
@endsection
