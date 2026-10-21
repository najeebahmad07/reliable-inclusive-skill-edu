@extends('layouts.app')

@section('title', 'Enroll New Student')
@section('breadcrumb', 'Students / New Enrollment')

@section('content')
<div class="max-w-4xl mx-auto space-y-4">
    <!-- Header -->
    <div class="bg-white dark:bg-[#151c2c] p-4 rounded-xl border border-slate-200 dark:border-slate-800 flex items-center justify-between shadow-xs">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 dark:text-white tracking-tight">Enroll New Student</h1>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Enter personal, academic, and document details for registration</p>
        </div>
        <a href="{{ route('students.index') }}" class="px-3.5 py-2 text-slate-600 dark:text-slate-300 hover:text-slate-800 dark:hover:text-white bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 rounded-lg text-sm font-medium transition flex items-center space-x-1.5">
            <i class="fa-solid fa-arrow-left text-xs"></i>
            <span>Back to Directory</span>
        </a>
    </div>

    <form method="POST" action="{{ route('students.store') }}" enctype="multipart/form-data" class="space-y-4">
        @csrf

        <!-- 1. Center & Program Selection -->
        <div class="bg-white dark:bg-[#151c2c] p-5 rounded-xl border border-slate-200 dark:border-slate-800 space-y-4 shadow-xs">
            <h2 class="text-sm font-bold text-slate-800 dark:text-slate-100 uppercase tracking-wider flex items-center space-x-2 border-b border-slate-100 dark:border-slate-800 pb-3">
                <i class="fa-solid fa-graduation-cap text-brand-500 text-base"></i>
                <span>1. Academic Course & Center</span>
            </h2>

            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4">
                @if(Auth::user()->isSuperAdmin())
                <div class="sm:col-span-2">
                    <label class="block text-xs font-medium text-slate-600 dark:text-slate-300 mb-1.5">Select Center *</label>
                    <select name="admin_id" required class="w-full px-3 py-2 bg-slate-50 dark:bg-[#182235] border border-slate-200 dark:border-slate-700 rounded-lg text-sm text-slate-800 dark:text-slate-100 focus:outline-none focus:ring-1 focus:ring-brand-500">
                        <option value="">-- Choose Center --</option>
                        @foreach($centers as $c)
                        <option value="{{ $c->id }}" {{ old('admin_id') == $c->id ? 'selected' : '' }}>
                            {{ $c->institute_name ?? $c->name }} ({{ $c->city ?? 'Center' }})
                        </option>
                        @endforeach
                    </select>
                </div>
                @endif

                <div>
                    <label class="block text-xs font-medium text-slate-600 dark:text-slate-300 mb-1.5">Program *</label>
                    <select name="program_id" id="programSelect" required class="w-full px-3 py-2 bg-slate-50 dark:bg-[#182235] border border-slate-200 dark:border-slate-700 rounded-lg text-sm text-slate-800 dark:text-slate-100 focus:outline-none focus:ring-1 focus:ring-brand-500">
                        <option value="">-- Select Program --</option>
                        @foreach($programs as $prog)
                        <option value="{{ $prog->id }}" {{ old('program_id') == $prog->id ? 'selected' : '' }}>
                            {{ $prog->program_name }}
                        </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-medium text-slate-600 dark:text-slate-300 mb-1.5">Course *</label>
                    <select name="course_id" id="courseSelect" required class="w-full px-3 py-2 bg-slate-50 dark:bg-[#182235] border border-slate-200 dark:border-slate-700 rounded-lg text-sm text-slate-800 dark:text-slate-100 focus:outline-none focus:ring-1 focus:ring-brand-500">
                        <option value="">-- Select Course --</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-medium text-slate-600 dark:text-slate-300 mb-1.5">Session *</label>
                    <input type="text" name="session_name" value="{{ old('session_name', date('Y') . '-' . (date('Y') + 1)) }}" required
                           class="w-full px-3 py-2 bg-slate-50 dark:bg-[#182235] border border-slate-200 dark:border-slate-700 rounded-lg text-sm text-slate-800 dark:text-slate-100 focus:outline-none focus:ring-1 focus:ring-brand-500" placeholder="2026-2027">
                </div>

                <div>
                    <label class="block text-xs font-medium text-slate-600 dark:text-slate-300 mb-1.5">Batch *</label>
                    <input type="text" name="batch" value="{{ old('batch', date('F Y')) }}" required
                           class="w-full px-3 py-2 bg-slate-50 dark:bg-[#182235] border border-slate-200 dark:border-slate-700 rounded-lg text-sm text-slate-800 dark:text-slate-100 focus:outline-none focus:ring-1 focus:ring-brand-500" placeholder="e.g. October 2026">
                </div>
            </div>
        </div>

        <!-- 2. Personal & Contact Information -->
        <div class="bg-white dark:bg-[#151c2c] p-5 rounded-xl border border-slate-200 dark:border-slate-800 space-y-4 shadow-xs">
            <h2 class="text-sm font-bold text-slate-800 dark:text-slate-100 uppercase tracking-wider flex items-center space-x-2 border-b border-slate-100 dark:border-slate-800 pb-3">
                <i class="fa-solid fa-user text-indigo-500 text-base"></i>
                <span>2. Personal & Contact Details</span>
            </h2>

            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-medium text-slate-600 dark:text-slate-300 mb-1.5">Student Full Name *</label>
                    <input type="text" name="full_name" value="{{ old('full_name') }}" required
                           class="w-full px-3 py-2 bg-slate-50 dark:bg-[#182235] border border-slate-200 dark:border-slate-700 rounded-lg text-sm text-slate-800 dark:text-slate-100 focus:outline-none focus:ring-1 focus:ring-brand-500" placeholder="As per official certificate">
                </div>

                <div>
                    <label class="block text-xs font-medium text-slate-600 dark:text-slate-300 mb-1.5">Gender *</label>
                    <select name="gender" required class="w-full px-3 py-2 bg-slate-50 dark:bg-[#182235] border border-slate-200 dark:border-slate-700 rounded-lg text-sm text-slate-800 dark:text-slate-100 focus:outline-none focus:ring-1 focus:ring-brand-500">
                        <option value="Male" {{ old('gender') === 'Male' ? 'selected' : '' }}>Male</option>
                        <option value="Female" {{ old('gender') === 'Female' ? 'selected' : '' }}>Female</option>
                        <option value="Other" {{ old('gender') === 'Other' ? 'selected' : '' }}>Other</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-medium text-slate-600 dark:text-slate-300 mb-1.5">Date of Birth *</label>
                    <input type="date" name="dob" value="{{ old('dob') }}" required
                           class="w-full px-3 py-2 bg-slate-50 dark:bg-[#182235] border border-slate-200 dark:border-slate-700 rounded-lg text-sm text-slate-800 dark:text-slate-100 focus:outline-none focus:ring-1 focus:ring-brand-500">
                </div>

                <div>
                    <label class="block text-xs font-medium text-slate-600 dark:text-slate-300 mb-1.5">Father's Name *</label>
                    <input type="text" name="father_name" value="{{ old('father_name') }}" required
                           class="w-full px-3 py-2 bg-slate-50 dark:bg-[#182235] border border-slate-200 dark:border-slate-700 rounded-lg text-sm text-slate-800 dark:text-slate-100 focus:outline-none focus:ring-1 focus:ring-brand-500">
                </div>

                <div>
                    <label class="block text-xs font-medium text-slate-600 dark:text-slate-300 mb-1.5">Mother's Name *</label>
                    <input type="text" name="mother_name" value="{{ old('mother_name') }}" required
                           class="w-full px-3 py-2 bg-slate-50 dark:bg-[#182235] border border-slate-200 dark:border-slate-700 rounded-lg text-sm text-slate-800 dark:text-slate-100 focus:outline-none focus:ring-1 focus:ring-brand-500">
                </div>

                <div>
                    <label class="block text-xs font-medium text-slate-600 dark:text-slate-300 mb-1.5">Mobile Number *</label>
                    <input type="text" name="mobile" value="{{ old('mobile') }}" required maxlength="15"
                           class="w-full px-3 py-2 bg-slate-50 dark:bg-[#182235] border border-slate-200 dark:border-slate-700 rounded-lg text-sm text-slate-800 dark:text-slate-100 focus:outline-none focus:ring-1 focus:ring-brand-500" placeholder="10-digit mobile">
                </div>

                <div>
                    <label class="block text-xs font-medium text-slate-600 dark:text-slate-300 mb-1.5">Email Address</label>
                    <input type="email" name="email" value="{{ old('email') }}"
                           class="w-full px-3 py-2 bg-slate-50 dark:bg-[#182235] border border-slate-200 dark:border-slate-700 rounded-lg text-sm text-slate-800 dark:text-slate-100 focus:outline-none focus:ring-1 focus:ring-brand-500" placeholder="optional">
                </div>

                <div>
                    <label class="block text-xs font-medium text-slate-600 dark:text-slate-300 mb-1.5">Aadhaar Number (12 Digits) *</label>
                    <input type="text" name="aadhaar_number" value="{{ old('aadhaar_number') }}" required maxlength="12" pattern="\d{12}"
                           class="w-full px-3 py-2 bg-slate-50 dark:bg-[#182235] border border-slate-200 dark:border-slate-700 rounded-lg text-sm text-slate-800 dark:text-slate-100 focus:outline-none focus:ring-1 focus:ring-brand-500" placeholder="123456789012">
                </div>

                <div class="sm:col-span-2 md:col-span-3">
                    <label class="block text-xs font-medium text-slate-600 dark:text-slate-300 mb-1.5">Full Residential Address *</label>
                    <textarea name="address" rows="2" required class="w-full px-3 py-2 bg-slate-50 dark:bg-[#182235] border border-slate-200 dark:border-slate-700 rounded-lg text-sm text-slate-800 dark:text-slate-100 focus:outline-none focus:ring-1 focus:ring-brand-500" placeholder="Street, Village, Post, District, State, Pin Code">{{ old('address') }}</textarea>
                </div>
            </div>
        </div>

        <!-- 3. Academic Qualifications -->
        <div class="bg-white dark:bg-[#151c2c] p-5 rounded-xl border border-slate-200 dark:border-slate-800 space-y-4 shadow-xs">
            <h2 class="text-sm font-bold text-slate-800 dark:text-slate-100 uppercase tracking-wider flex items-center space-x-2 border-b border-slate-100 dark:border-slate-800 pb-3">
                <i class="fa-solid fa-award text-purple-500 text-base"></i>
                <span>3. Academic Qualifications</span>
            </h2>

            <!-- 10th Standard -->
            <div class="p-4 bg-slate-50/75 dark:bg-[#182235]/60 rounded-xl border border-slate-200 dark:border-slate-700/70 space-y-3">
                <span class="text-xs font-bold text-slate-700 dark:text-slate-200 block uppercase tracking-wide">10th / Secondary School Details *</span>
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-6 gap-3">
                    <div class="md:col-span-2">
                        <label class="block text-xs font-medium text-slate-600 dark:text-slate-300 mb-1">Board Name *</label>
                        <input type="text" name="tenth_board_name" value="{{ old('tenth_board_name', 'CBSE') }}" required
                               class="w-full px-3 py-1.5 bg-white dark:bg-[#151c2c] border border-slate-200 dark:border-slate-700 rounded-lg text-sm text-slate-800 dark:text-slate-100">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-600 dark:text-slate-300 mb-1">Passing Year *</label>
                        <input type="number" name="tenth_passing_year" value="{{ old('tenth_passing_year', '2020') }}" required
                               class="w-full px-3 py-1.5 bg-white dark:bg-[#151c2c] border border-slate-200 dark:border-slate-700 rounded-lg text-sm text-slate-800 dark:text-slate-100">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-600 dark:text-slate-300 mb-1">Total Marks</label>
                        <input type="number" id="tenth_total" name="tenth_total_marks" value="{{ old('tenth_total_marks', 500) }}"
                               class="w-full px-3 py-1.5 bg-white dark:bg-[#151c2c] border border-slate-200 dark:border-slate-700 rounded-lg text-sm text-slate-800 dark:text-slate-100">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-600 dark:text-slate-300 mb-1">Obtained Marks</label>
                        <input type="number" id="tenth_obtained" name="tenth_obtained_marks" value="{{ old('tenth_obtained_marks') }}"
                               class="w-full px-3 py-1.5 bg-white dark:bg-[#151c2c] border border-slate-200 dark:border-slate-700 rounded-lg text-sm text-slate-800 dark:text-slate-100">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-600 dark:text-slate-300 mb-1">Percentage % *</label>
                        <input type="number" step="0.01" id="tenth_pct" name="tenth_percentage" value="{{ old('tenth_percentage') }}" required
                               class="w-full px-3 py-1.5 bg-white dark:bg-[#151c2c] border border-slate-200 dark:border-slate-700 rounded-lg text-sm text-slate-800 dark:text-slate-100 font-semibold text-brand-600 dark:text-brand-400">
                    </div>
                </div>
            </div>

            <!-- 12th Standard -->
            <div class="p-4 bg-slate-50/75 dark:bg-[#182235]/60 rounded-xl border border-slate-200 dark:border-slate-700/70 space-y-3">
                <span class="text-xs font-bold text-slate-700 dark:text-slate-200 block uppercase tracking-wide">12th / Senior Secondary Details *</span>
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-6 gap-3">
                    <div class="md:col-span-2">
                        <label class="block text-xs font-medium text-slate-600 dark:text-slate-300 mb-1">Board Name *</label>
                        <input type="text" name="twelfth_board_name" value="{{ old('twelfth_board_name', 'CBSE') }}" required
                               class="w-full px-3 py-1.5 bg-white dark:bg-[#151c2c] border border-slate-200 dark:border-slate-700 rounded-lg text-sm text-slate-800 dark:text-slate-100">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-600 dark:text-slate-300 mb-1">Passing Year *</label>
                        <input type="number" name="twelfth_passing_year" value="{{ old('twelfth_passing_year', '2022') }}" required
                               class="w-full px-3 py-1.5 bg-white dark:bg-[#151c2c] border border-slate-200 dark:border-slate-700 rounded-lg text-sm text-slate-800 dark:text-slate-100">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-600 dark:text-slate-300 mb-1">Total Marks</label>
                        <input type="number" id="twelfth_total" name="twelfth_total_marks" value="{{ old('twelfth_total_marks', 500) }}"
                               class="w-full px-3 py-1.5 bg-white dark:bg-[#151c2c] border border-slate-200 dark:border-slate-700 rounded-lg text-sm text-slate-800 dark:text-slate-100">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-600 dark:text-slate-300 mb-1">Obtained Marks</label>
                        <input type="number" id="twelfth_obtained" name="twelfth_obtained_marks" value="{{ old('twelfth_obtained_marks') }}"
                               class="w-full px-3 py-1.5 bg-white dark:bg-[#151c2c] border border-slate-200 dark:border-slate-700 rounded-lg text-sm text-slate-800 dark:text-slate-100">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-600 dark:text-slate-300 mb-1">Percentage % *</label>
                        <input type="number" step="0.01" id="twelfth_pct" name="twelfth_percentage" value="{{ old('twelfth_percentage') }}" required
                               class="w-full px-3 py-1.5 bg-white dark:bg-[#151c2c] border border-slate-200 dark:border-slate-700 rounded-lg text-sm text-slate-800 dark:text-slate-100 font-semibold text-brand-600 dark:text-brand-400">
                    </div>
                </div>
            </div>
        </div>

        <!-- 4. Document & Photo Uploads -->
        <div class="bg-white dark:bg-[#151c2c] p-5 rounded-xl border border-slate-200 dark:border-slate-800 space-y-4 shadow-xs">
            <h2 class="text-sm font-bold text-slate-800 dark:text-slate-100 uppercase tracking-wider flex items-center space-x-2 border-b border-slate-100 dark:border-slate-800 pb-3">
                <i class="fa-solid fa-cloud-arrow-up text-emerald-500 text-base"></i>
                <span>4. Photographs & Document Uploads</span>
            </h2>

            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4">
                <div class="p-3 border border-dashed border-slate-300 dark:border-slate-700 rounded-xl text-center bg-slate-50 dark:bg-[#182235]/40 hover:border-brand-500 dark:hover:border-brand-500 transition">
                    <i class="fa-solid fa-camera text-slate-400 dark:text-slate-500 text-lg mb-1.5 block"></i>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-200">Passport Photo</label>
                    <span class="text-[11px] text-slate-400 dark:text-slate-500 block mb-2">Max 500KB (JPG/PNG)</span>
                    <input type="file" name="photo" accept="image/*" class="w-full text-xs text-slate-500 file:mr-2 file:py-1 file:px-2.5 file:rounded-md file:border-0 file:text-xs file:bg-brand-50 file:text-brand-700 dark:file:bg-brand-900/30 dark:file:text-brand-300">
                </div>

                <div class="p-3 border border-dashed border-slate-300 dark:border-slate-700 rounded-xl text-center bg-slate-50 dark:bg-[#182235]/40 hover:border-brand-500 dark:hover:border-brand-500 transition">
                    <i class="fa-solid fa-signature text-slate-400 dark:text-slate-500 text-lg mb-1.5 block"></i>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-200">Signature</label>
                    <span class="text-[11px] text-slate-400 dark:text-slate-500 block mb-2">Max 500KB (JPG/PNG)</span>
                    <input type="file" name="signature" accept="image/*" class="w-full text-xs text-slate-500 file:mr-2 file:py-1 file:px-2.5 file:rounded-md file:border-0 file:text-xs file:bg-brand-50 file:text-brand-700 dark:file:bg-brand-900/30 dark:file:text-brand-300">
                </div>

                <div class="p-3 border border-dashed border-slate-300 dark:border-slate-700 rounded-xl text-center bg-slate-50 dark:bg-[#182235]/40 hover:border-brand-500 dark:hover:border-brand-500 transition">
                    <i class="fa-solid fa-id-card text-slate-400 dark:text-slate-500 text-lg mb-1.5 block"></i>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-200">Aadhaar Card</label>
                    <span class="text-[11px] text-slate-400 dark:text-slate-500 block mb-2">PDF or Image</span>
                    <input type="file" name="aadhaar_upload" class="w-full text-xs text-slate-500 file:mr-2 file:py-1 file:px-2.5 file:rounded-md file:border-0 file:text-xs file:bg-brand-50 file:text-brand-700 dark:file:bg-brand-900/30 dark:file:text-brand-300">
                </div>

                <div class="p-3 border border-dashed border-slate-300 dark:border-slate-700 rounded-xl text-center bg-slate-50 dark:bg-[#182235]/40 hover:border-brand-500 dark:hover:border-brand-500 transition">
                    <i class="fa-solid fa-file-invoice text-slate-400 dark:text-slate-500 text-lg mb-1.5 block"></i>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-200">10th Marksheet</label>
                    <span class="text-[11px] text-slate-400 dark:text-slate-500 block mb-2">PDF or Image</span>
                    <input type="file" name="tenth_marksheet_upload" class="w-full text-xs text-slate-500 file:mr-2 file:py-1 file:px-2.5 file:rounded-md file:border-0 file:text-xs file:bg-brand-50 file:text-brand-700 dark:file:bg-brand-900/30 dark:file:text-brand-300">
                </div>
            </div>
        </div>

        <!-- Submit Button -->
        <div class="flex items-center justify-end space-x-3 pt-2">
            <a href="{{ route('students.index') }}" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 rounded-lg text-sm font-medium transition">Cancel</a>
            <button type="submit" class="px-6 py-2.5 bg-brand-500 hover:bg-brand-600 text-white rounded-lg text-sm font-bold shadow-sm transition flex items-center space-x-2">
                <i class="fa-solid fa-check text-xs"></i>
                <span>Complete Enrollment</span>
            </button>
        </div>
    </form>
</div>

@push('scripts')
<script>
    // Dynamic Course Loading
    const programSelect = document.getElementById('programSelect');
    const courseSelect = document.getElementById('courseSelect');

    if (programSelect && courseSelect) {
        programSelect.addEventListener('change', function() {
            const progId = this.value;
            courseSelect.innerHTML = '<option value="">Loading courses...</option>';
            if (!progId) {
                courseSelect.innerHTML = '<option value="">-- Select Course --</option>';
                return;
            }

            fetch(`{{ url('students/ajax/courses') }}/${progId}`)
                .then(res => res.json())
                .then(data => {
                    let options = '<option value="">-- Select Course --</option>';
                    data.forEach(c => {
                        options += `<option value="${c.id}">${c.course_name}</option>`;
                    });
                    courseSelect.innerHTML = options;
                })
                .catch(err => {
                    console.error(err);
                    courseSelect.innerHTML = '<option value="">Failed to load</option>';
                });
        });
    }

    // Auto percentage calculators
    function bindCalc(totalId, obtainedId, pctId) {
        const t = document.getElementById(totalId);
        const o = document.getElementById(obtainedId);
        const p = document.getElementById(pctId);
        function calc() {
            const tot = parseFloat(t.value) || 0;
            const obt = parseFloat(o.value) || 0;
            if (tot > 0 && obt >= 0) {
                p.value = ((obt / tot) * 100).toFixed(2);
            }
        }
        if (t && o && p) {
            t.addEventListener('input', calc);
            o.addEventListener('input', calc);
        }
    }

    bindCalc('tenth_total', 'tenth_obtained', 'tenth_pct');
    bindCalc('twelfth_total', 'twelfth_obtained', 'twelfth_pct');
</script>
@endpush
@endsection
