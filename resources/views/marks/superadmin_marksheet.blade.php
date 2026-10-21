@extends('layouts.app')

@section('title', 'Search Original Marksheet')
@section('breadcrumb', 'Academics / Original Marksheet Search')

@section('content')
<div class="max-w-4xl mx-auto space-y-4">
    <!-- Search Bar -->
    <div class="bg-white dark:bg-[#151c2c] p-5 rounded-xl border border-slate-200 dark:border-slate-800 shadow-xs">
        <h1 class="text-2xl font-bold text-slate-900 dark:text-white tracking-tight mb-1">Search & Print Original Marksheet</h1>
        <p class="text-xs text-slate-500 dark:text-slate-400 mb-4">Lookup official student examination record by Roll Number or Enrollment Number</p>

        <form method="GET" action="{{ route('marks.superadmin_marksheet') }}" class="flex items-center space-x-3">
            <div class="relative flex-1">
                <input type="text" name="query" value="{{ request('query') }}" placeholder="Enter Student Roll No or Enrollment No..." required
                       class="w-full pl-9 pr-3 py-2.5 bg-slate-50 dark:bg-[#182235] border border-slate-200 dark:border-slate-700 rounded-lg text-sm text-slate-800 dark:text-slate-100 placeholder-slate-400 focus:outline-none focus:ring-1 focus:ring-brand-500">
                <i class="fa-solid fa-magnifying-glass absolute left-3 top-3.5 text-slate-400 text-xs"></i>
            </div>
            <button type="submit" class="px-5 py-2.5 bg-brand-500 hover:bg-brand-600 text-white rounded-lg text-sm font-semibold shadow-xs transition flex items-center space-x-2">
                <span>Search</span>
            </button>
        </form>
    </div>

    @if($student)
    <!-- Marksheet Container embedded -->
    <div class="bg-white dark:bg-[#151c2c] p-5 rounded-xl border border-slate-200 dark:border-slate-800 space-y-4 shadow-xs">
        <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-800">
            <span class="text-sm font-bold text-slate-900 dark:text-white">Examination Result: {{ $student->full_name }}</span>
            <a href="{{ route('marks.marksheet', $student->id) }}" target="_blank" class="px-3.5 py-1.5 bg-brand-500 hover:bg-brand-600 text-white rounded-lg text-xs font-semibold inline-flex items-center space-x-1.5 transition">
                <span>Open Printable Version</span>
                <i class="fa-solid fa-arrow-up-right-from-square text-xs"></i>
            </a>
        </div>

        <div class="border border-slate-200 dark:border-slate-700/80 p-5 rounded-xl bg-slate-50/50 dark:bg-[#182235]/50">
            <div class="grid grid-cols-2 gap-3 text-sm mb-5 text-slate-700 dark:text-slate-300">
                <div><strong class="text-slate-900 dark:text-white">Student Name:</strong> {{ $student->full_name }}</div>
                <div><strong class="text-slate-900 dark:text-white">Roll No:</strong> <span class="font-mono font-medium">{{ $student->roll_no }}</span></div>
                <div><strong class="text-slate-900 dark:text-white">Father Name:</strong> {{ $student->father_name }}</div>
                <div><strong class="text-slate-900 dark:text-white">Enrollment:</strong> <span class="font-mono font-medium">{{ $student->enrollment_no }}</span></div>
                <div><strong class="text-slate-900 dark:text-white">Course:</strong> {{ $student->course->course_name ?? 'N/A' }}</div>
                <div><strong class="text-slate-900 dark:text-white">Center:</strong> {{ $student->admin->institute_name ?? 'Center' }}</div>
            </div>

            <table class="w-full text-sm border border-slate-200 dark:border-slate-700 mb-4 bg-white dark:bg-[#151c2c] rounded-lg overflow-hidden">
                <thead class="bg-slate-50 dark:bg-[#182235] text-slate-600 dark:text-slate-300 border-b border-slate-200 dark:border-slate-700">
                    <tr>
                        <th class="p-3 text-left">Subject</th>
                        <th class="p-3 text-center w-24">Max Marks</th>
                        <th class="p-3 text-center w-24">Theory</th>
                        <th class="p-3 text-center w-24">Practical</th>
                        <th class="p-3 text-center w-24">Obtained</th>
                        <th class="p-3 text-center w-20">Grade</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    @forelse($marks as $m)
                    <tr>
                        <td class="p-3 font-medium text-slate-800 dark:text-slate-200">{{ $m->subject->subject_name ?? '' }}</td>
                        <td class="p-3 text-center font-mono text-slate-500 dark:text-slate-400">{{ $m->subject->total_marks ?? 100 }}</td>
                        <td class="p-3 text-center font-mono text-slate-700 dark:text-slate-300">{{ $m->th_marks }}</td>
                        <td class="p-3 text-center font-mono text-slate-700 dark:text-slate-300">{{ $m->pr_marks }}</td>
                        <td class="p-3 text-center font-mono font-bold text-slate-900 dark:text-white">{{ $m->marks_obtained }}</td>
                        <td class="p-3 text-center font-mono font-bold text-brand-600 dark:text-brand-400">{{ $m->grade }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="p-6 text-center text-slate-400 dark:text-slate-500 text-sm">No marks entered for this student.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>

            <div class="flex items-center justify-between font-bold text-sm text-slate-800 dark:text-slate-200 pt-2">
                <span>Result: <span class="{{ $percentage >= 40 ? 'text-emerald-600 dark:text-emerald-400' : 'text-rose-600 dark:text-rose-400' }}">{{ $percentage >= 40 ? 'PASSED' : 'FAILED' }}</span></span>
                <span>Percentage: <span class="font-mono text-brand-600 dark:text-brand-400">{{ $percentage }}%</span></span>
                <span>Total: <span class="font-mono">{{ $totalObtainedMarks }} / {{ $totalMaxMarks }}</span></span>
            </div>
        </div>
    </div>
    @elseif(request('query'))
    <div class="bg-white dark:bg-[#151c2c] p-10 rounded-xl border border-slate-200 dark:border-slate-800 text-center text-slate-400 dark:text-slate-500 text-sm shadow-xs">
        <i class="fa-solid fa-user-xmark text-3xl text-slate-300 dark:text-slate-600 mb-2 block"></i>
        No student found matching Roll / Enrollment number "<span class="font-mono text-slate-600 dark:text-slate-400">{{ request('query') }}</span>".
    </div>
    @endif
</div>
@endsection
