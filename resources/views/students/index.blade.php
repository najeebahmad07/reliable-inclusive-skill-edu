@extends('layouts.app')

@section('title', 'Students Directory')
@section('breadcrumb', 'Students / Directory')

@section('content')
<div class="space-y-4">
    <!-- Header with Action -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 bg-white dark:bg-[#151c2c] p-4 rounded-xl border border-slate-200 dark:border-slate-800 shadow-xs">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 dark:text-white tracking-tight">Student Directory</h1>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Manage all registered students, enrollment records, and approval workflows</p>
        </div>
        <div>
            <a href="{{ route('students.create') }}" class="px-3.5 py-2 bg-brand-500 hover:bg-brand-600 text-white rounded-lg text-sm font-semibold flex items-center space-x-2 shadow-xs transition">
                <i class="fa-solid fa-user-plus text-xs"></i>
                <span>Enroll New Student</span>
            </a>
        </div>
    </div>

    <!-- Filters Bar -->
    <div class="bg-white dark:bg-[#151c2c] p-4 rounded-xl border border-slate-200 dark:border-slate-800 shadow-xs">
        <form method="GET" action="{{ route('students.index') }}" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-5 gap-3 items-end">
            <div>
                <label class="block text-xs font-medium text-slate-600 dark:text-slate-300 mb-1.5">Search Keyword</label>
                <div class="relative">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Name, Roll, Enrollment..."
                           class="w-full pl-8 pr-3 py-2 bg-slate-50 dark:bg-[#182235] border border-slate-200 dark:border-slate-700 rounded-lg text-sm text-slate-800 dark:text-slate-100 placeholder-slate-400 focus:outline-none focus:ring-1 focus:ring-brand-500">
                    <i class="fa-solid fa-magnifying-glass absolute left-3 top-3 text-slate-400 text-xs"></i>
                </div>
            </div>

            <div>
                <label class="block text-xs font-medium text-slate-600 dark:text-slate-300 mb-1.5">Status</label>
                <select name="status" class="w-full px-3 py-2 bg-slate-50 dark:bg-[#182235] border border-slate-200 dark:border-slate-700 rounded-lg text-sm text-slate-800 dark:text-slate-100 focus:outline-none focus:ring-1 focus:ring-brand-500">
                    <option value="">All Statuses</option>
                    <option value="Approved" {{ request('status') === 'Approved' ? 'selected' : '' }}>Approved</option>
                    <option value="Pending" {{ request('status') === 'Pending' ? 'selected' : '' }}>Pending</option>
                    <option value="WaitingApproval" {{ request('status') === 'WaitingApproval' ? 'selected' : '' }}>Waiting Approval</option>
                    <option value="Rejected" {{ request('status') === 'Rejected' ? 'selected' : '' }}>Rejected</option>
                </select>
            </div>

            <div>
                <label class="block text-xs font-medium text-slate-600 dark:text-slate-300 mb-1.5">Program</label>
                <select name="program_id" class="w-full px-3 py-2 bg-slate-50 dark:bg-[#182235] border border-slate-200 dark:border-slate-700 rounded-lg text-sm text-slate-800 dark:text-slate-100 focus:outline-none focus:ring-1 focus:ring-brand-500 truncate">
                    <option value="">All Programs</option>
                    @foreach($programs as $prog)
                    <option value="{{ $prog->id }}" {{ request('program_id') == $prog->id ? 'selected' : '' }}>{{ $prog->program_name }}</option>
                    @endforeach
                </select>
            </div>

            @if(Auth::user()->isSuperAdmin())
            <div>
                <label class="block text-xs font-medium text-slate-600 dark:text-slate-300 mb-1.5">Center</label>
                <select name="admin_id" class="w-full px-3 py-2 bg-slate-50 dark:bg-[#182235] border border-slate-200 dark:border-slate-700 rounded-lg text-sm text-slate-800 dark:text-slate-100 focus:outline-none focus:ring-1 focus:ring-brand-500 truncate">
                    <option value="">All Centers</option>
                    @foreach($centers as $c)
                    <option value="{{ $c->id }}" {{ request('admin_id') == $c->id ? 'selected' : '' }}>{{ $c->institute_name ?? $c->name }}</option>
                    @endforeach
                </select>
            </div>
            @else
            <div></div>
            @endif

            <div class="flex items-center space-x-2">
                <button type="submit" class="flex-1 py-2 px-3.5 bg-slate-800 hover:bg-slate-900 dark:bg-brand-600 dark:hover:bg-brand-700 text-white rounded-lg text-sm font-medium text-center transition">
                    Filter
                </button>
                @if(request()->hasAny(['search', 'status', 'program_id', 'admin_id']))
                <a href="{{ route('students.index') }}" class="py-2 px-3 bg-slate-200 hover:bg-slate-300 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 rounded-lg text-sm font-medium transition">
                    Reset
                </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Students Table -->
    <div class="bg-white dark:bg-[#151c2c] rounded-xl border border-slate-200 dark:border-slate-800 overflow-hidden shadow-xs">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse table-saas">
                <thead>
                    <tr class="bg-slate-50/75 dark:bg-[#182235]/60 text-slate-500 dark:text-slate-400 border-b border-slate-200 dark:border-slate-800">
                        <th class="w-12 text-center">#</th>
                        <th>Student</th>
                        <th>Roll / Enrollment</th>
                        <th>Program & Course</th>
                        @if(Auth::user()->isSuperAdmin())
                        <th>Center</th>
                        @endif
                        <th>Status</th>
                        <th class="text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800/80">
                    @forelse($students as $index => $student)
                    <tr class="hover:bg-slate-50/75 dark:hover:bg-slate-800/30 transition-colors">
                        <td class="text-center text-slate-400 dark:text-slate-500 font-mono text-xs">{{ $students->firstItem() + $index }}</td>
                        <td>
                            <div class="flex items-center space-x-3">
                                <div class="w-8 h-8 rounded-full bg-slate-200 dark:bg-slate-700 overflow-hidden flex-shrink-0 flex items-center justify-center text-xs font-bold text-slate-600 dark:text-slate-300">
                                    @if($student->photo && file_exists(public_path('uploads/photos/' . $student->photo)))
                                    <img src="{{ asset('uploads/photos/' . $student->photo) }}" class="w-full h-full object-cover">
                                    @else
                                    {{ strtoupper(substr($student->full_name, 0, 1)) }}
                                    @endif
                                </div>
                                <div class="min-w-0">
                                    <a href="{{ route('students.show', $student->id) }}" class="font-semibold text-slate-800 dark:text-slate-100 hover:text-brand-500 dark:hover:text-brand-400 truncate block text-sm">
                                        {{ $student->full_name }}
                                    </a>
                                    <div class="text-xs text-slate-400 dark:text-slate-500">Mob: {{ $student->mobile }}</div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="font-mono text-slate-700 dark:text-slate-200 text-xs font-medium">{{ $student->roll_no }}</div>
                            <div class="font-mono text-slate-400 dark:text-slate-500 text-[11px]">{{ $student->enrollment_no }}</div>
                        </td>
                        <td>
                            <div class="font-medium text-slate-800 dark:text-slate-200 text-sm truncate max-w-[200px]">{{ $student->course->course_name ?? 'N/A' }}</div>
                            <div class="text-xs text-slate-400 dark:text-slate-500 truncate max-w-[200px]">{{ $student->program->program_name ?? 'N/A' }}</div>
                        </td>
                        @if(Auth::user()->isSuperAdmin())
                        <td class="text-slate-600 dark:text-slate-300 text-sm truncate max-w-[160px]">
                            {{ $student->admin->institute_name ?? 'Head Office' }}
                        </td>
                        @endif
                        <td>
                            @if($student->status === 'Approved')
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 mr-1.5"></span>
                                Approved
                            </span>
                            @elseif(in_array($student->status, ['Pending', 'WaitingApproval']))
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-amber-50 dark:bg-amber-950/40 text-amber-700 dark:text-amber-400 border border-amber-200 dark:border-amber-800">
                                <span class="w-1.5 h-1.5 rounded-full bg-amber-500 mr-1.5"></span>
                                Pending
                            </span>
                            @else
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-rose-50 dark:bg-rose-950/40 text-rose-700 dark:text-rose-400 border border-rose-200 dark:border-rose-800">
                                {{ $student->status }}
                            </span>
                            @endif
                        </td>
                        <td class="text-right space-x-1 whitespace-nowrap">
                            @if($student->isPending())
                            <form action="{{ route('students.approve', $student->id) }}" method="POST" class="inline" onsubmit="return confirm('Approve this student? The program approval fee will be deducted.');">
                                @csrf
                                <button type="submit" class="px-2.5 py-1 rounded-md bg-emerald-50 dark:bg-emerald-950/50 hover:bg-emerald-100 dark:hover:bg-emerald-900/60 text-emerald-700 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800 text-xs font-semibold transition" title="Approve Student">
                                    Approve
                                </button>
                            </form>
                            @endif

                            <a href="{{ route('students.show', $student->id) }}" class="p-1.5 text-slate-500 hover:text-brand-500 dark:text-slate-400 dark:hover:text-brand-400 text-sm transition" title="View Profile">
                                <i class="fa-regular fa-eye"></i>
                            </a>
                            <a href="{{ route('students.edit', $student->id) }}" class="p-1.5 text-slate-500 hover:text-indigo-600 dark:text-slate-400 dark:hover:text-indigo-400 text-sm transition" title="Edit">
                                <i class="fa-regular fa-pen-to-square"></i>
                            </a>
                            <a href="{{ route('certificates.id_card', $student->id) }}" target="_blank" class="p-1.5 text-slate-500 hover:text-purple-600 dark:text-slate-400 dark:hover:text-purple-400 text-sm transition" title="ID Card">
                                <i class="fa-solid fa-id-card"></i>
                            </a>
                            <a href="{{ route('marks.marksheet', $student->id) }}" target="_blank" class="p-1.5 text-slate-500 hover:text-amber-600 dark:text-slate-400 dark:hover:text-amber-400 text-sm transition" title="Marksheet">
                                <i class="fa-solid fa-file-invoice"></i>
                            </a>
                            <a href="{{ route('certificates.student_certificate', $student->id) }}" target="_blank" class="p-1.5 text-slate-500 hover:text-indigo-600 dark:text-slate-400 dark:hover:text-indigo-400 text-sm transition" title="Certificate">
                                <i class="fa-solid fa-certificate"></i>
                            </a>
                            @php
                                $prog = $student->program->program_name ?? '';
                                $crs = $student->course->course_name ?? '';
                                $isNtt = stripos($prog, 'NTT') !== false || stripos($prog, 'Nursery') !== false || stripos($crs, 'NTT') !== false || stripos($crs, 'Nursery') !== false;
                            @endphp
                            @if($isNtt)
                            <a href="{{ route('certificates.ntt_admit_card', [$student->id, 'semester' => 1]) }}" target="_blank" class="p-1.5 text-slate-500 hover:text-emerald-600 dark:text-slate-400 dark:hover:text-emerald-400 text-sm transition" title="NTT Admit Card">
                                <i class="fa-solid fa-address-card"></i>
                            </a>
                            @endif
                            <form action="{{ route('students.destroy', $student->id) }}" method="POST" class="inline" onsubmit="return confirm('Permanently delete student record?');">
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
                        <td colspan="7" class="text-center py-12 text-slate-400 dark:text-slate-500 text-sm">
                            <i class="fa-solid fa-user-slash text-3xl mb-2 text-slate-300 dark:text-slate-600 block"></i>
                            No students match your criteria.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($students->hasPages())
        <div class="px-5 py-3.5 border-t border-slate-200 dark:border-slate-800 bg-slate-50/50 dark:bg-[#182235]/40 flex flex-col sm:flex-row items-center justify-between gap-3">
            <span class="text-xs text-slate-500 dark:text-slate-400">
                Showing {{ $students->firstItem() }} to {{ $students->lastItem() }} of {{ $students->total() }} students
            </span>
            <div>
                {{ $students->links() }}
            </div>
        </div>
        @endif
    </div>
</div>
@endsection
