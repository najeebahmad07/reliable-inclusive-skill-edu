@extends('layouts.app')

@section('title', 'Edit Student - ' . $student->full_name)
@section('breadcrumb', 'Students / Edit / ' . $student->full_name)

@section('content')
<div class="max-w-4xl mx-auto space-y-4">
    <!-- Header -->
    <div class="bg-white dark:bg-[#151c2c] p-4 rounded-xl border border-slate-200 dark:border-slate-800 flex items-center justify-between shadow-xs">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 dark:text-white tracking-tight">Edit Student Record</h1>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Roll No: <span class="font-mono font-medium text-slate-700 dark:text-slate-300">{{ $student->roll_no }}</span> &bull; Enrollment: <span class="font-mono font-medium text-slate-700 dark:text-slate-300">{{ $student->enrollment_no }}</span></p>
        </div>
        <a href="{{ route('students.show', $student->id) }}" class="px-3.5 py-2 text-slate-600 dark:text-slate-300 hover:text-slate-800 dark:hover:text-white bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 rounded-lg text-sm font-medium transition flex items-center space-x-1.5">
            <i class="fa-solid fa-arrow-left text-xs"></i>
            <span>View Profile</span>
        </a>
    </div>

    <form method="POST" action="{{ route('students.update', $student->id) }}" enctype="multipart/form-data" class="space-y-4">
        @csrf
        @method('PUT')

        <!-- Academic Selection -->
        <div class="bg-white dark:bg-[#151c2c] p-5 rounded-xl border border-slate-200 dark:border-slate-800 space-y-4 shadow-xs">
            <h2 class="text-sm font-bold text-slate-800 dark:text-slate-100 uppercase tracking-wider flex items-center space-x-2 border-b border-slate-100 dark:border-slate-800 pb-3">
                <i class="fa-solid fa-graduation-cap text-brand-500 text-base"></i>
                <span>Academic & Batch Info</span>
            </h2>

            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4">
                <div>
                    <label class="block text-xs font-medium text-slate-600 dark:text-slate-300 mb-1.5">Program *</label>
                    <select name="program_id" id="programSelect" required class="w-full px-3 py-2 bg-slate-50 dark:bg-[#182235] border border-slate-200 dark:border-slate-700 rounded-lg text-sm text-slate-800 dark:text-slate-100 focus:outline-none focus:ring-1 focus:ring-brand-500">
                        @foreach($programs as $prog)
                        <option value="{{ $prog->id }}" {{ old('program_id', $student->program_id) == $prog->id ? 'selected' : '' }}>
                            {{ $prog->program_name }}
                        </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-medium text-slate-600 dark:text-slate-300 mb-1.5">Course *</label>
                    <select name="course_id" id="courseSelect" required class="w-full px-3 py-2 bg-slate-50 dark:bg-[#182235] border border-slate-200 dark:border-slate-700 rounded-lg text-sm text-slate-800 dark:text-slate-100 focus:outline-none focus:ring-1 focus:ring-brand-500">
                        @foreach($courses as $c)
                        <option value="{{ $c->id }}" {{ old('course_id', $student->course_id) == $c->id ? 'selected' : '' }}>
                            {{ $c->course_name }}
                        </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-medium text-slate-600 dark:text-slate-300 mb-1.5">Session *</label>
                    <input type="text" name="session_name" value="{{ old('session_name', $student->session_name) }}" required
                           class="w-full px-3 py-2 bg-slate-50 dark:bg-[#182235] border border-slate-200 dark:border-slate-700 rounded-lg text-sm text-slate-800 dark:text-slate-100 focus:outline-none focus:ring-1 focus:ring-brand-500">
                </div>

                <div>
                    <label class="block text-xs font-medium text-slate-600 dark:text-slate-300 mb-1.5">Batch *</label>
                    <input type="text" name="batch" value="{{ old('batch', $student->batch) }}" required
                           class="w-full px-3 py-2 bg-slate-50 dark:bg-[#182235] border border-slate-200 dark:border-slate-700 rounded-lg text-sm text-slate-800 dark:text-slate-100 focus:outline-none focus:ring-1 focus:ring-brand-500">
                </div>
            </div>
        </div>

        <!-- Personal Info -->
        <div class="bg-white dark:bg-[#151c2c] p-5 rounded-xl border border-slate-200 dark:border-slate-800 space-y-4 shadow-xs">
            <h2 class="text-sm font-bold text-slate-800 dark:text-slate-100 uppercase tracking-wider flex items-center space-x-2 border-b border-slate-100 dark:border-slate-800 pb-3">
                <i class="fa-solid fa-user text-indigo-500 text-base"></i>
                <span>Personal & Contact Info</span>
            </h2>

            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-medium text-slate-600 dark:text-slate-300 mb-1.5">Full Name *</label>
                    <input type="text" name="full_name" value="{{ old('full_name', $student->full_name) }}" required
                           class="w-full px-3 py-2 bg-slate-50 dark:bg-[#182235] border border-slate-200 dark:border-slate-700 rounded-lg text-sm text-slate-800 dark:text-slate-100 focus:outline-none focus:ring-1 focus:ring-brand-500">
                </div>

                <div>
                    <label class="block text-xs font-medium text-slate-600 dark:text-slate-300 mb-1.5">Gender *</label>
                    <select name="gender" required class="w-full px-3 py-2 bg-slate-50 dark:bg-[#182235] border border-slate-200 dark:border-slate-700 rounded-lg text-sm text-slate-800 dark:text-slate-100 focus:outline-none focus:ring-1 focus:ring-brand-500">
                        <option value="Male" {{ old('gender', $student->gender) === 'Male' ? 'selected' : '' }}>Male</option>
                        <option value="Female" {{ old('gender', $student->gender) === 'Female' ? 'selected' : '' }}>Female</option>
                        <option value="Other" {{ old('gender', $student->gender) === 'Other' ? 'selected' : '' }}>Other</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-medium text-slate-600 dark:text-slate-300 mb-1.5">Date of Birth *</label>
                    <input type="date" name="dob" value="{{ old('dob', $student->dob ? $student->dob->format('Y-m-d') : '') }}" required
                           class="w-full px-3 py-2 bg-slate-50 dark:bg-[#182235] border border-slate-200 dark:border-slate-700 rounded-lg text-sm text-slate-800 dark:text-slate-100 focus:outline-none focus:ring-1 focus:ring-brand-500">
                </div>

                <div>
                    <label class="block text-xs font-medium text-slate-600 dark:text-slate-300 mb-1.5">Father's Name *</label>
                    <input type="text" name="father_name" value="{{ old('father_name', $student->father_name) }}" required
                           class="w-full px-3 py-2 bg-slate-50 dark:bg-[#182235] border border-slate-200 dark:border-slate-700 rounded-lg text-sm text-slate-800 dark:text-slate-100 focus:outline-none focus:ring-1 focus:ring-brand-500">
                </div>

                <div>
                    <label class="block text-xs font-medium text-slate-600 dark:text-slate-300 mb-1.5">Mother's Name *</label>
                    <input type="text" name="mother_name" value="{{ old('mother_name', $student->mother_name) }}" required
                           class="w-full px-3 py-2 bg-slate-50 dark:bg-[#182235] border border-slate-200 dark:border-slate-700 rounded-lg text-sm text-slate-800 dark:text-slate-100 focus:outline-none focus:ring-1 focus:ring-brand-500">
                </div>

                <div>
                    <label class="block text-xs font-medium text-slate-600 dark:text-slate-300 mb-1.5">Mobile *</label>
                    <input type="text" name="mobile" value="{{ old('mobile', $student->mobile) }}" required maxlength="15"
                           class="w-full px-3 py-2 bg-slate-50 dark:bg-[#182235] border border-slate-200 dark:border-slate-700 rounded-lg text-sm text-slate-800 dark:text-slate-100 focus:outline-none focus:ring-1 focus:ring-brand-500">
                </div>

                <div>
                    <label class="block text-xs font-medium text-slate-600 dark:text-slate-300 mb-1.5">Email</label>
                    <input type="email" name="email" value="{{ old('email', $student->email) }}"
                           class="w-full px-3 py-2 bg-slate-50 dark:bg-[#182235] border border-slate-200 dark:border-slate-700 rounded-lg text-sm text-slate-800 dark:text-slate-100 focus:outline-none focus:ring-1 focus:ring-brand-500">
                </div>

                <div>
                    <label class="block text-xs font-medium text-slate-600 dark:text-slate-300 mb-1.5">Aadhaar Number *</label>
                    <input type="text" name="aadhaar_number" value="{{ old('aadhaar_number', $student->aadhaar_number) }}" required maxlength="12"
                           class="w-full px-3 py-2 bg-slate-50 dark:bg-[#182235] border border-slate-200 dark:border-slate-700 rounded-lg text-sm text-slate-800 dark:text-slate-100 focus:outline-none focus:ring-1 focus:ring-brand-500">
                </div>

                <div class="sm:col-span-2 md:col-span-3">
                    <label class="block text-xs font-medium text-slate-600 dark:text-slate-300 mb-1.5">Address *</label>
                    <textarea name="address" rows="2" required class="w-full px-3 py-2 bg-slate-50 dark:bg-[#182235] border border-slate-200 dark:border-slate-700 rounded-lg text-sm text-slate-800 dark:text-slate-100 focus:outline-none focus:ring-1 focus:ring-brand-500">{{ old('address', $student->address) }}</textarea>
                </div>
            </div>
        </div>

        <!-- 10th & 12th Academic -->
        <div class="bg-white dark:bg-[#151c2c] p-5 rounded-xl border border-slate-200 dark:border-slate-800 space-y-4 shadow-xs">
            <h2 class="text-sm font-bold text-slate-800 dark:text-slate-100 uppercase tracking-wider flex items-center space-x-2 border-b border-slate-100 dark:border-slate-800 pb-3">
                <i class="fa-solid fa-award text-purple-500 text-base"></i>
                <span>Academic Record</span>
            </h2>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-medium text-slate-600 dark:text-slate-300 mb-1.5">10th Board *</label>
                    <input type="text" name="tenth_board_name" value="{{ old('tenth_board_name', $student->tenth_board_name) }}" required
                           class="w-full px-3 py-2 bg-slate-50 dark:bg-[#182235] border border-slate-200 dark:border-slate-700 rounded-lg text-sm text-slate-800 dark:text-slate-100 focus:outline-none focus:ring-1 focus:ring-brand-500">
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-600 dark:text-slate-300 mb-1.5">10th Year *</label>
                    <input type="number" name="tenth_passing_year" value="{{ old('tenth_passing_year', $student->tenth_passing_year) }}" required
                           class="w-full px-3 py-2 bg-slate-50 dark:bg-[#182235] border border-slate-200 dark:border-slate-700 rounded-lg text-sm text-slate-800 dark:text-slate-100 focus:outline-none focus:ring-1 focus:ring-brand-500">
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-600 dark:text-slate-300 mb-1.5">10th Percentage % *</label>
                    <input type="number" step="0.01" name="tenth_percentage" value="{{ old('tenth_percentage', $student->tenth_percentage) }}" required
                           class="w-full px-3 py-2 bg-slate-50 dark:bg-[#182235] border border-slate-200 dark:border-slate-700 rounded-lg text-sm text-slate-800 dark:text-slate-100 focus:outline-none focus:ring-1 focus:ring-brand-500 font-semibold text-brand-600 dark:text-brand-400">
                </div>

                <div>
                    <label class="block text-xs font-medium text-slate-600 dark:text-slate-300 mb-1.5">12th Board *</label>
                    <input type="text" name="twelfth_board_name" value="{{ old('twelfth_board_name', $student->twelfth_board_name) }}" required
                           class="w-full px-3 py-2 bg-slate-50 dark:bg-[#182235] border border-slate-200 dark:border-slate-700 rounded-lg text-sm text-slate-800 dark:text-slate-100 focus:outline-none focus:ring-1 focus:ring-brand-500">
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-600 dark:text-slate-300 mb-1.5">12th Year *</label>
                    <input type="number" name="twelfth_passing_year" value="{{ old('twelfth_passing_year', $student->twelfth_passing_year) }}" required
                           class="w-full px-3 py-2 bg-slate-50 dark:bg-[#182235] border border-slate-200 dark:border-slate-700 rounded-lg text-sm text-slate-800 dark:text-slate-100 focus:outline-none focus:ring-1 focus:ring-brand-500">
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-600 dark:text-slate-300 mb-1.5">12th Percentage % *</label>
                    <input type="number" step="0.01" name="twelfth_percentage" value="{{ old('twelfth_percentage', $student->twelfth_percentage) }}" required
                           class="w-full px-3 py-2 bg-slate-50 dark:bg-[#182235] border border-slate-200 dark:border-slate-700 rounded-lg text-sm text-slate-800 dark:text-slate-100 focus:outline-none focus:ring-1 focus:ring-brand-500 font-semibold text-brand-600 dark:text-brand-400">
                </div>
            </div>
        </div>

        <!-- Document Replacement Uploads -->
        <div class="bg-white dark:bg-[#151c2c] p-5 rounded-xl border border-slate-200 dark:border-slate-800 space-y-4 shadow-xs">
            <h2 class="text-sm font-bold text-slate-800 dark:text-slate-100 uppercase tracking-wider flex items-center space-x-2 border-b border-slate-100 dark:border-slate-800 pb-3">
                <i class="fa-solid fa-cloud-arrow-up text-emerald-500 text-base"></i>
                <span>Update Documents (Leave blank to keep existing)</span>
            </h2>

            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4">
                <div class="p-3 border border-dashed border-slate-300 dark:border-slate-700 rounded-xl bg-slate-50 dark:bg-[#182235]/40 hover:border-brand-500 transition">
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-200 mb-1">Photo</label>
                    <input type="file" name="photo" accept="image/*" class="w-full text-xs text-slate-500 file:mr-2 file:py-1 file:px-2 file:rounded file:border-0 file:text-xs file:bg-brand-50 file:text-brand-700 dark:file:bg-brand-900/30 dark:file:text-brand-300">
                </div>
                <div class="p-3 border border-dashed border-slate-300 dark:border-slate-700 rounded-xl bg-slate-50 dark:bg-[#182235]/40 hover:border-brand-500 transition">
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-200 mb-1">Signature</label>
                    <input type="file" name="signature" accept="image/*" class="w-full text-xs text-slate-500 file:mr-2 file:py-1 file:px-2 file:rounded file:border-0 file:text-xs file:bg-brand-50 file:text-brand-700 dark:file:bg-brand-900/30 dark:file:text-brand-300">
                </div>
                <div class="p-3 border border-dashed border-slate-300 dark:border-slate-700 rounded-xl bg-slate-50 dark:bg-[#182235]/40 hover:border-brand-500 transition">
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-200 mb-1">Aadhaar Card</label>
                    <input type="file" name="aadhaar_upload" class="w-full text-xs text-slate-500 file:mr-2 file:py-1 file:px-2 file:rounded file:border-0 file:text-xs file:bg-brand-50 file:text-brand-700 dark:file:bg-brand-900/30 dark:file:text-brand-300">
                </div>
                <div class="p-3 border border-dashed border-slate-300 dark:border-slate-700 rounded-xl bg-slate-50 dark:bg-[#182235]/40 hover:border-brand-500 transition">
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-200 mb-1">10th Marksheet</label>
                    <input type="file" name="tenth_marksheet_upload" class="w-full text-xs text-slate-500 file:mr-2 file:py-1 file:px-2 file:rounded file:border-0 file:text-xs file:bg-brand-50 file:text-brand-700 dark:file:bg-brand-900/30 dark:file:text-brand-300">
                </div>
            </div>
        </div>

        <!-- Submit -->
        <div class="flex items-center justify-end space-x-3 pt-2">
            <a href="{{ route('students.show', $student->id) }}" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 rounded-lg text-sm font-medium transition">Cancel</a>
            <button type="submit" class="px-6 py-2.5 bg-brand-500 hover:bg-brand-600 text-white rounded-lg text-sm font-bold shadow-sm transition">
                Save Changes
            </button>
        </div>
    </form>
</div>
@endsection
