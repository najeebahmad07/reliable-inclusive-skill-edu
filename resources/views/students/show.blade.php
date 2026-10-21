@extends('layouts.app')

@section('title', $student->full_name . ' - Profile')
@section('breadcrumb', 'Students / ' . $student->full_name)

@section('content')
<div class="max-w-4xl mx-auto space-y-4">
    <!-- Profile Header Card -->
    <div class="bg-white dark:bg-[#151c2c] p-5 rounded-xl border border-slate-200 dark:border-slate-800 shadow-xs">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center space-x-4">
                <div class="w-16 h-16 rounded-xl bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 overflow-hidden flex-shrink-0 flex items-center justify-center text-xl font-bold text-slate-500 dark:text-slate-400">
                    @if($student->photo && file_exists(public_path('uploads/photos/' . $student->photo)))
                    <img src="{{ asset('uploads/photos/' . $student->photo) }}" class="w-full h-full object-cover">
                    @else
                    {{ strtoupper(substr($student->full_name, 0, 1)) }}
                    @endif
                </div>
                <div>
                    <div class="flex items-center space-x-2.5">
                        <h1 class="text-xl font-bold text-slate-900 dark:text-white">{{ $student->full_name }}</h1>
                        @if($student->status === 'Approved')
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 mr-1.5"></span>
                            Approved
                        </span>
                        @else
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-amber-50 dark:bg-amber-950/40 text-amber-700 dark:text-amber-400 border border-amber-200 dark:border-amber-800">
                            <span class="w-1.5 h-1.5 rounded-full bg-amber-500 mr-1.5"></span>
                            {{ $student->status }}
                        </span>
                        @endif
                    </div>
                    <div class="text-xs text-slate-500 dark:text-slate-400 mt-1 flex flex-wrap items-center gap-x-4 gap-y-1">
                        <span>Roll: <strong class="text-slate-700 dark:text-slate-200 font-mono">{{ $student->roll_no }}</strong></span>
                        <span>Enrollment: <strong class="text-slate-700 dark:text-slate-200 font-mono">{{ $student->enrollment_no }}</strong></span>
                        <span>Center: <strong class="text-slate-700 dark:text-slate-200">{{ $student->admin->institute_name ?? 'Center' }}</strong></span>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex flex-wrap items-center gap-2">
                @if($student->isPending())
                <form action="{{ route('students.approve', $student->id) }}" method="POST" onsubmit="return confirm('Approve this student? The program approval fee will be deducted.');">
                    @csrf
                    <button type="submit" class="px-3.5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-xs font-semibold shadow-xs flex items-center space-x-1.5 transition">
                        <i class="fa-solid fa-check text-xs"></i>
                        <span>Approve Student</span>
                    </button>
                </form>
                @endif

                <a href="{{ route('certificates.id_card', $student->id) }}" target="_blank" class="px-3 py-2 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 rounded-lg text-xs font-medium inline-flex items-center space-x-1.5 transition">
                    <i class="fa-solid fa-id-card text-purple-500 text-xs"></i>
                    <span>ID Card</span>
                </a>

                <a href="{{ route('marks.marksheet', $student->id) }}" target="_blank" class="px-3 py-2 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 rounded-lg text-xs font-medium inline-flex items-center space-x-1.5 transition">
                    <i class="fa-solid fa-file-invoice text-amber-500 text-xs"></i>
                    <span>Marksheet</span>
                </a>

                <a href="{{ route('certificates.student_certificate', $student->id) }}" target="_blank" class="px-3 py-2 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 rounded-lg text-xs font-medium inline-flex items-center space-x-1.5 transition">
                    <i class="fa-solid fa-certificate text-indigo-500 text-xs"></i>
                    <span>Certificate</span>
                </a>

                @php
                    $progName = $student->program->program_name ?? '';
                    $crsName = $student->course->course_name ?? '';
                    $isNttCandidate = stripos($progName, 'NTT') !== false || stripos($progName, 'Nursery') !== false || stripos($crsName, 'NTT') !== false || stripos($crsName, 'Nursery') !== false;
                @endphp

                @if($isNttCandidate)
                <div class="relative inline-block text-left" x-data="{ open: false }">
                    <button @click="open = !open" type="button" class="px-3 py-2 bg-emerald-50 hover:bg-emerald-100 dark:bg-emerald-950/40 dark:hover:bg-emerald-900/40 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800 rounded-lg text-xs font-semibold inline-flex items-center space-x-1.5 transition">
                        <i class="fa-solid fa-address-card text-emerald-600 text-xs"></i>
                        <span>NTT Admit Card</span>
                        <i class="fa-solid fa-chevron-down text-[10px]"></i>
                    </button>
                    <div x-show="open" @click.away="open = false" x-cloak class="origin-top-right absolute right-0 mt-2 w-44 rounded-xl shadow-lg bg-white dark:bg-[#1f2937] border border-slate-200 dark:border-slate-700 py-1 z-30">
                        <a href="{{ route('certificates.ntt_admit_card', [$student->id, 'semester' => 1]) }}" target="_blank" class="block px-4 py-2 text-xs text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-700/50">1st Semester</a>
                        <a href="{{ route('certificates.ntt_admit_card', [$student->id, 'semester' => 2]) }}" target="_blank" class="block px-4 py-2 text-xs text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-700/50">2nd Semester</a>
                        <a href="{{ route('certificates.ntt_admit_card', [$student->id, 'semester' => 3]) }}" target="_blank" class="block px-4 py-2 text-xs text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-700/50">3rd Semester</a>
                        <a href="{{ route('certificates.ntt_admit_card', [$student->id, 'semester' => 4]) }}" target="_blank" class="block px-4 py-2 text-xs text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-700/50">4th Semester</a>
                    </div>
                </div>
                @endif

                <a href="{{ route('students.edit', $student->id) }}" class="px-3 py-2 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 rounded-lg text-xs font-medium inline-flex items-center space-x-1.5 transition">
                    <i class="fa-solid fa-pen text-slate-500 dark:text-slate-400 text-xs"></i>
                    <span>Edit</span>
                </a>
            </div>
        </div>

        <!-- Super Admin Transfer Section -->
        @if(Auth::user()->isSuperAdmin())
        <div class="mt-4 pt-4 border-t border-slate-100 dark:border-slate-800 flex flex-wrap items-center justify-between gap-3">
            <span class="text-xs text-slate-600 dark:text-slate-300 font-medium flex items-center space-x-1.5">
                <i class="fa-solid fa-exchange-alt text-brand-500"></i>
                <span>Transfer Student to Another Center:</span>
            </span>
            <form action="{{ route('students.transfer', $student->id) }}" method="POST" class="flex items-center space-x-2">
                @csrf
                <select name="new_admin_id" required class="px-3 py-1.5 bg-slate-50 dark:bg-[#182235] border border-slate-200 dark:border-slate-700 rounded-lg text-xs text-slate-800 dark:text-slate-100 focus:outline-none focus:ring-1 focus:ring-brand-500">
                    <option value="">-- Choose New Center --</option>
                    @foreach(\App\Models\Admin::where('role', 'admin')->where('id', '!=', $student->admin_id)->orderBy('institute_name')->get() as $adm)
                    <option value="{{ $adm->id }}">{{ $adm->institute_name }}</option>
                    @endforeach
                </select>
                <button type="submit" class="px-3 py-1.5 bg-slate-800 hover:bg-slate-900 dark:bg-brand-600 dark:hover:bg-brand-700 text-white rounded-lg text-xs font-medium transition">Transfer</button>
            </form>
        </div>
        @endif
    </div>

    <!-- 2 Column Details Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <!-- Personal Information -->
        <div class="bg-white dark:bg-[#151c2c] p-5 rounded-xl border border-slate-200 dark:border-slate-800 space-y-3 shadow-xs">
            <h2 class="text-sm font-bold text-slate-800 dark:text-slate-100 uppercase tracking-wider border-b border-slate-100 dark:border-slate-800 pb-2.5 flex items-center space-x-2">
                <i class="fa-solid fa-id-badge text-brand-500"></i>
                <span>Personal & Contact Info</span>
            </h2>

            <dl class="grid grid-cols-3 gap-2.5 text-sm">
                <dt class="text-slate-500 dark:text-slate-400 font-medium text-xs">Full Name:</dt>
                <dd class="col-span-2 text-slate-900 dark:text-white font-semibold">{{ $student->full_name }}</dd>

                <dt class="text-slate-500 dark:text-slate-400 font-medium text-xs">Gender:</dt>
                <dd class="col-span-2 text-slate-800 dark:text-slate-200">{{ $student->gender }}</dd>

                <dt class="text-slate-500 dark:text-slate-400 font-medium text-xs">Date of Birth:</dt>
                <dd class="col-span-2 text-slate-800 dark:text-slate-200">{{ $student->dob ? $student->dob->format('d M Y') : 'N/A' }}</dd>

                <dt class="text-slate-500 dark:text-slate-400 font-medium text-xs">Father's Name:</dt>
                <dd class="col-span-2 text-slate-800 dark:text-slate-200">{{ $student->father_name }}</dd>

                <dt class="text-slate-500 dark:text-slate-400 font-medium text-xs">Mother's Name:</dt>
                <dd class="col-span-2 text-slate-800 dark:text-slate-200">{{ $student->mother_name }}</dd>

                <dt class="text-slate-500 dark:text-slate-400 font-medium text-xs">Mobile:</dt>
                <dd class="col-span-2 text-slate-800 dark:text-slate-200">{{ $student->mobile }}</dd>

                <dt class="text-slate-500 dark:text-slate-400 font-medium text-xs">Email:</dt>
                <dd class="col-span-2 text-slate-800 dark:text-slate-200">{{ $student->email ?? 'N/A' }}</dd>

                <dt class="text-slate-500 dark:text-slate-400 font-medium text-xs">Aadhaar No:</dt>
                <dd class="col-span-2 text-slate-800 dark:text-slate-200 font-mono">{{ $student->aadhaar_number }}</dd>

                <dt class="text-slate-500 dark:text-slate-400 font-medium text-xs">Address:</dt>
                <dd class="col-span-2 text-slate-800 dark:text-slate-200 text-xs leading-relaxed">{{ $student->address }}</dd>
            </dl>
        </div>

        <!-- Academic & Program Details -->
        <div class="bg-white dark:bg-[#151c2c] p-5 rounded-xl border border-slate-200 dark:border-slate-800 space-y-3 shadow-xs">
            <h2 class="text-sm font-bold text-slate-800 dark:text-slate-100 uppercase tracking-wider border-b border-slate-100 dark:border-slate-800 pb-2.5 flex items-center space-x-2">
                <i class="fa-solid fa-graduation-cap text-indigo-500"></i>
                <span>Enrolled Program & Course</span>
            </h2>

            <dl class="grid grid-cols-3 gap-2.5 text-sm">
                <dt class="text-slate-500 dark:text-slate-400 font-medium text-xs">Program:</dt>
                <dd class="col-span-2 text-slate-900 dark:text-white font-semibold">{{ $student->program->program_name ?? 'N/A' }}</dd>

                <dt class="text-slate-500 dark:text-slate-400 font-medium text-xs">Course:</dt>
                <dd class="col-span-2 text-brand-600 dark:text-brand-400 font-semibold">{{ $student->course->course_name ?? 'N/A' }}</dd>

                <dt class="text-slate-500 dark:text-slate-400 font-medium text-xs">Session:</dt>
                <dd class="col-span-2 text-slate-800 dark:text-slate-200 font-mono">{{ $student->session_name }}</dd>

                <dt class="text-slate-500 dark:text-slate-400 font-medium text-xs">Batch:</dt>
                <dd class="col-span-2 text-slate-800 dark:text-slate-200">{{ $student->batch }}</dd>

                <dt class="text-slate-500 dark:text-slate-400 font-medium text-xs">Enrollment No:</dt>
                <dd class="col-span-2 text-slate-900 dark:text-white font-mono font-bold">{{ $student->enrollment_no }}</dd>

                <dt class="text-slate-500 dark:text-slate-400 font-medium text-xs">Roll No:</dt>
                <dd class="col-span-2 text-slate-900 dark:text-white font-mono font-bold">{{ $student->roll_no }}</dd>

                <dt class="text-slate-500 dark:text-slate-400 font-medium text-xs">Enrolled Date:</dt>
                <dd class="col-span-2 text-slate-800 dark:text-slate-200">{{ $student->created_at ? $student->created_at->format('d M Y') : 'N/A' }}</dd>

                <dt class="text-slate-500 dark:text-slate-400 font-medium text-xs">Approved Date:</dt>
                <dd class="col-span-2 text-slate-800 dark:text-slate-200">{{ $student->approved_at ? $student->approved_at->format('d M Y') : 'Not Yet' }}</dd>
            </dl>
        </div>
    </div>

    <!-- Qualifications & Documents -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <!-- Prior Academic Qualifications -->
        <div class="bg-white dark:bg-[#151c2c] p-5 rounded-xl border border-slate-200 dark:border-slate-800 space-y-3.5 shadow-xs">
            <h2 class="text-sm font-bold text-slate-800 dark:text-slate-100 uppercase tracking-wider border-b border-slate-100 dark:border-slate-800 pb-2.5 flex items-center space-x-2">
                <i class="fa-solid fa-award text-purple-500"></i>
                <span>Educational Qualifications</span>
            </h2>

            <div class="space-y-2.5">
                <!-- 10th -->
                <div class="p-3 bg-slate-50 dark:bg-[#182235]/60 rounded-xl border border-slate-200 dark:border-slate-700/70">
                    <div class="flex items-center justify-between text-sm font-semibold text-slate-800 dark:text-slate-100">
                        <span>10th / Secondary</span>
                        <span class="text-brand-600 dark:text-brand-400 font-bold">{{ $student->tenth_percentage }}%</span>
                    </div>
                    <div class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                        Board: {{ $student->tenth_board_name }} &bull; Year: {{ $student->tenth_passing_year }} &bull; Marks: {{ $student->tenth_obtained_marks }}/{{ $student->tenth_total_marks }}
                    </div>
                </div>

                <!-- 12th -->
                <div class="p-3 bg-slate-50 dark:bg-[#182235]/60 rounded-xl border border-slate-200 dark:border-slate-700/70">
                    <div class="flex items-center justify-between text-sm font-semibold text-slate-800 dark:text-slate-100">
                        <span>12th / Sr Secondary</span>
                        <span class="text-indigo-600 dark:text-indigo-400 font-bold">{{ $student->twelfth_percentage }}%</span>
                    </div>
                    <div class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                        Board: {{ $student->twelfth_board_name }} &bull; Year: {{ $student->twelfth_passing_year }} &bull; Marks: {{ $student->twelfth_obtained_marks }}/{{ $student->twelfth_total_marks }}
                    </div>
                </div>

                @if($student->ug_university_name)
                <!-- UG -->
                <div class="p-3 bg-slate-50 dark:bg-[#182235]/60 rounded-xl border border-slate-200 dark:border-slate-700/70">
                    <div class="flex items-center justify-between text-sm font-semibold text-slate-800 dark:text-slate-100">
                        <span>Undergraduate (UG)</span>
                        <span class="text-purple-600 dark:text-purple-400 font-bold">{{ $student->ug_percentage }}%</span>
                    </div>
                    <div class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                        University: {{ $student->ug_university_name }} &bull; Year: {{ $student->ug_passing_year }}
                    </div>
                </div>
                @endif
            </div>
        </div>

        <!-- Uploaded Documents Preview -->
        <div class="bg-white dark:bg-[#151c2c] p-5 rounded-xl border border-slate-200 dark:border-slate-800 space-y-3.5 shadow-xs">
            <h2 class="text-sm font-bold text-slate-800 dark:text-slate-100 uppercase tracking-wider border-b border-slate-100 dark:border-slate-800 pb-2.5 flex items-center space-x-2">
                <i class="fa-solid fa-folder-open text-emerald-500"></i>
                <span>Document Attachments</span>
            </h2>

            <div class="grid grid-cols-2 gap-3 text-sm">
                <!-- Photo -->
                <div class="p-3 bg-slate-50 dark:bg-[#182235]/60 rounded-xl border border-slate-200 dark:border-slate-700/70 flex items-center justify-between">
                    <div>
                        <div class="font-semibold text-slate-800 dark:text-slate-100 text-xs">Photo</div>
                        <div class="text-[11px] text-slate-400 dark:text-slate-500">{{ $student->photo ? 'Uploaded' : 'Missing' }}</div>
                    </div>
                    @if($student->photo)
                    <a href="{{ asset('uploads/photos/' . $student->photo) }}" target="_blank" class="text-brand-600 dark:text-brand-400 hover:underline text-xs font-semibold">View</a>
                    @endif
                </div>

                <!-- Signature -->
                <div class="p-3 bg-slate-50 dark:bg-[#182235]/60 rounded-xl border border-slate-200 dark:border-slate-700/70 flex items-center justify-between">
                    <div>
                        <div class="font-semibold text-slate-800 dark:text-slate-100 text-xs">Signature</div>
                        <div class="text-[11px] text-slate-400 dark:text-slate-500">{{ $student->signature ? 'Uploaded' : 'Missing' }}</div>
                    </div>
                    @if($student->signature)
                    <a href="{{ asset('uploads/signatures/' . $student->signature) }}" target="_blank" class="text-brand-600 dark:text-brand-400 hover:underline text-xs font-semibold">View</a>
                    @endif
                </div>

                <!-- Aadhaar -->
                <div class="p-3 bg-slate-50 dark:bg-[#182235]/60 rounded-xl border border-slate-200 dark:border-slate-700/70 flex items-center justify-between">
                    <div>
                        <div class="font-semibold text-slate-800 dark:text-slate-100 text-xs">Aadhaar Card</div>
                        <div class="text-[11px] text-slate-400 dark:text-slate-500">{{ $student->aadhaar_upload ? 'Uploaded' : 'Missing' }}</div>
                    </div>
                    @if($student->aadhaar_upload)
                    <a href="{{ asset('uploads/documents/' . $student->aadhaar_upload) }}" target="_blank" class="text-brand-600 dark:text-brand-400 hover:underline text-xs font-semibold">View</a>
                    @endif
                </div>

                <!-- 10th Marksheet -->
                <div class="p-3 bg-slate-50 dark:bg-[#182235]/60 rounded-xl border border-slate-200 dark:border-slate-700/70 flex items-center justify-between">
                    <div>
                        <div class="font-semibold text-slate-800 dark:text-slate-100 text-xs">10th Marksheet</div>
                        <div class="text-[11px] text-slate-400 dark:text-slate-500">{{ $student->tenth_marksheet_upload ? 'Uploaded' : 'Missing' }}</div>
                    </div>
                    @if($student->tenth_marksheet_upload)
                    <a href="{{ asset('uploads/documents/' . $student->tenth_marksheet_upload) }}" target="_blank" class="text-brand-600 dark:text-brand-400 hover:underline text-xs font-semibold">View</a>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
