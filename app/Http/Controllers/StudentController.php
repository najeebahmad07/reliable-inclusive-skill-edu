<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Student;
use App\Models\Admin;
use App\Models\Program;
use App\Models\Course;
use App\Models\Subject;
use App\Models\WalletTransaction;
use App\Models\Notification;

class StudentController extends Controller
{
    public function index(Request $request)
    {
        /** @var Admin $user */
        $user = Auth::user();

        $query = Student::with(['admin', 'program', 'course']);

        // Scope to admin if not super admin
        if (!$user->isSuperAdmin()) {
            $query->where('admin_id', $user->id);
        } elseif ($request->filled('admin_id')) {
            $query->where('admin_id', $request->admin_id);
        }

        // Search
        if ($request->filled('search')) {
            $s = trim($request->search);
            $query->where(function ($q) use ($s) {
                $q->where('full_name', 'like', "%{$s}%")
                  ->orWhere('enrollment_no', 'like', "%{$s}%")
                  ->orWhere('roll_no', 'like', "%{$s}%")
                  ->orWhere('mobile', 'like', "%{$s}%")
                  ->orWhere('father_name', 'like', "%{$s}%");
            });
        }

        // Filter status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter program
        if ($request->filled('program_id')) {
            $query->where('program_id', $request->program_id);
        }

        // Filter course
        if ($request->filled('course_id')) {
            $query->where('course_id', $request->course_id);
        }

        $students = $query->latest('id')->paginate(15)->withQueryString();

        $programs = Program::orderBy('program_name')->get();
        $centers = $user->isSuperAdmin() ? Admin::where('role', 'admin')->orderBy('institute_name')->get() : collect();

        return view('students.index', compact('students', 'programs', 'centers'));
    }

    public function create()
    {
        /** @var Admin $user */
        $user = Auth::user();

        if ($user->isSuperAdmin()) {
            $programs = Program::orderBy('program_name')->get();
            $centers = Admin::where('role', 'admin')->where('status', 'active')->orderBy('institute_name')->get();
        } else {
            // Get programs assigned to this admin, or all if none assigned
            $assignedPrograms = $user->programs;
            $programs = $assignedPrograms->isNotEmpty() ? $assignedPrograms : Program::orderBy('program_name')->get();
            $centers = collect([$user]);
        }

        return view('students.create', compact('programs', 'centers'));
    }

    public function store(Request $request)
    {
        /** @var Admin $user */
        $user = Auth::user();

        $validated = $request->validate([
            'admin_id' => $user->isSuperAdmin() ? 'required|exists:admins,id' : 'nullable',
            'program_id' => 'required|exists:programs,id',
            'course_id' => 'required|exists:courses,id',
            'session_name' => 'required|string|max:100',
            'batch' => 'required|string|max:100',
            'full_name' => 'required|string|max:200',
            'gender' => 'required|in:Male,Female,Other',
            'dob' => 'required|date',
            'father_name' => 'required|string|max:200',
            'mother_name' => 'required|string|max:200',
            'mobile' => 'required|string|max:15',
            'email' => 'nullable|email|max:255',
            'address' => 'required|string',
            'aadhaar_number' => 'required|string|size:12',
            // 10th
            'tenth_board_name' => 'required|string|max:255',
            'tenth_institute_name' => 'nullable|string|max:255',
            'tenth_passing_year' => 'required|digits:4',
            'tenth_total_marks' => 'nullable|numeric',
            'tenth_obtained_marks' => 'nullable|numeric',
            'tenth_percentage' => 'required|numeric|between:0,100',
            // 12th
            'twelfth_board_name' => 'required|string|max:255',
            'twelfth_institute_name' => 'nullable|string|max:255',
            'twelfth_passing_year' => 'required|digits:4',
            'twelfth_total_marks' => 'nullable|numeric',
            'twelfth_obtained_marks' => 'nullable|numeric',
            'twelfth_percentage' => 'required|numeric|between:0,100',
            // UG (optional)
            'ug_university_name' => 'nullable|string|max:255',
            'ug_institute_name' => 'nullable|string|max:255',
            'ug_passing_year' => 'nullable|digits:4',
            'ug_total_marks' => 'nullable|numeric',
            'ug_obtained_marks' => 'nullable|numeric',
            'ug_percentage' => 'nullable|numeric|between:0,100',
            // Files
            'photo' => 'nullable|image|max:1024',
            'signature' => 'nullable|image|max:1024',
            'aadhaar_upload' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'tenth_marksheet_upload' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'twelfth_marksheet_upload' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ]);

        $adminId = $user->isSuperAdmin() ? ($validated['admin_id'] ?? $user->id) : $user->id;

        // Generate unique enrollment and roll no
        $year = date('Y');
        $randomSeq = rand(10000, 99999);
        $enrollmentNo = 'RISE/' . $year . '/' . $randomSeq;
        $rollNo = $year . rand(100000, 999999);

        // Upload files
        $uploads = [];
        $uploadMap = [
            'photo' => 'uploads/photos',
            'signature' => 'uploads/signatures',
            'aadhaar_upload' => 'uploads/documents',
            'tenth_marksheet_upload' => 'uploads/documents',
            'twelfth_marksheet_upload' => 'uploads/documents',
        ];

        foreach ($uploadMap as $field => $folder) {
            if ($request->hasFile($field)) {
                $file = $request->file($field);
                $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                $destinationPath = public_path($folder);
                if (!file_exists($destinationPath)) {
                    mkdir($destinationPath, 0777, true);
                }
                $file->move($destinationPath, $filename);
                $uploads[$field] = $filename;
            }
        }

        $studentData = array_merge($validated, $uploads, [
            'admin_id' => $adminId,
            'enrollment_no' => $enrollmentNo,
            'roll_no' => $rollNo,
            'status' => 'Pending',
        ]);

        $student = Student::create($studentData);

        return redirect()->route('students.show', $student->id)->with('success', 'Student enrolled successfully! Enrollment No: ' . $enrollmentNo);
    }

    public function show($id)
    {
        $student = Student::with(['admin', 'program', 'course', 'marks.subject', 'certificate'])->findOrFail($id);

        /** @var Admin $user */
        $user = Auth::user();
        if (!$user->isSuperAdmin() && $student->admin_id !== $user->id) {
            abort(403, 'Unauthorized access to student record.');
        }

        return view('students.show', compact('student'));
    }

    public function edit($id)
    {
        $student = Student::findOrFail($id);

        /** @var Admin $user */
        $user = Auth::user();
        if (!$user->isSuperAdmin() && $student->admin_id !== $user->id) {
            abort(403, 'Unauthorized access.');
        }

        $programs = Program::orderBy('program_name')->get();
        $courses = Course::where('program_id', $student->program_id)->orderBy('course_name')->get();
        $centers = $user->isSuperAdmin() ? Admin::where('role', 'admin')->get() : collect();

        return view('students.edit', compact('student', 'programs', 'courses', 'centers'));
    }

    public function update(Request $request, $id)
    {
        $student = Student::findOrFail($id);

        /** @var Admin $user */
        $user = Auth::user();
        if (!$user->isSuperAdmin() && $student->admin_id !== $user->id) {
            abort(403, 'Unauthorized access.');
        }

        $validated = $request->validate([
            'program_id' => 'required|exists:programs,id',
            'course_id' => 'required|exists:courses,id',
            'session_name' => 'required|string|max:100',
            'batch' => 'required|string|max:100',
            'full_name' => 'required|string|max:200',
            'gender' => 'required|in:Male,Female,Other',
            'dob' => 'required|date',
            'father_name' => 'required|string|max:200',
            'mother_name' => 'required|string|max:200',
            'mobile' => 'required|string|max:15',
            'email' => 'nullable|email|max:255',
            'address' => 'required|string',
            'aadhaar_number' => 'required|string|size:12',
            'tenth_board_name' => 'required|string|max:255',
            'tenth_passing_year' => 'required|digits:4',
            'tenth_percentage' => 'required|numeric|between:0,100',
            'twelfth_board_name' => 'required|string|max:255',
            'twelfth_passing_year' => 'required|digits:4',
            'twelfth_percentage' => 'required|numeric|between:0,100',
            'photo' => 'nullable|image|max:1024',
            'signature' => 'nullable|image|max:1024',
            'aadhaar_upload' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'tenth_marksheet_upload' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'twelfth_marksheet_upload' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ]);

        $uploads = [];
        $uploadMap = [
            'photo' => 'uploads/photos',
            'signature' => 'uploads/signatures',
            'aadhaar_upload' => 'uploads/documents',
            'tenth_marksheet_upload' => 'uploads/documents',
            'twelfth_marksheet_upload' => 'uploads/documents',
        ];

        foreach ($uploadMap as $field => $folder) {
            if ($request->hasFile($field)) {
                $file = $request->file($field);
                $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                $destinationPath = public_path($folder);
                if (!file_exists($destinationPath)) {
                    mkdir($destinationPath, 0777, true);
                }
                $file->move($destinationPath, $filename);
                $uploads[$field] = $filename;
            }
        }

        $student->update(array_merge($validated, $uploads));

        return redirect()->route('students.show', $student->id)->with('success', 'Student details updated successfully.');
    }

    public function destroy($id)
    {
        $student = Student::findOrFail($id);

        /** @var Admin $user */
        $user = Auth::user();
        if (!$user->isSuperAdmin() && $student->admin_id !== $user->id) {
            abort(403, 'Unauthorized access.');
        }

        $student->marks()->delete();
        $student->delete();

        return redirect()->route('students.index')->with('success', 'Student record deleted successfully.');
    }

    public function approve($id)
    {
        $student = Student::with('admin')->findOrFail($id);

        if ($student->isApproved()) {
            return back()->with('info', 'Student is already approved.');
        }

        /** @var Admin $currentUser */
        $currentUser = Auth::user();

        // Calculate approval fee
        $specialFees = config('rise.program_approval_fees', []);
        $fee = $specialFees[$student->program_id] ?? config('rise.default_approval_fee', 2000);

        $center = $student->admin;

        // If super admin approves, or if center admin approves own student
        if (!$currentUser->isSuperAdmin()) {
            if ($center->wallet_balance < $fee) {
                return back()->with('error', 'Insufficient wallet balance (₹' . number_format($center->wallet_balance, 2) . '). Required: ₹' . number_format($fee, 2) . '. Please recharge your wallet.');
            }
        }

        DB::transaction(function () use ($student, $center, $fee, $currentUser) {
            // Deduct from center wallet balance
            $center->decrement('wallet_balance', $fee);

            // Create wallet transaction record
            WalletTransaction::create([
                'admin_id' => $center->id,
                'amount' => $fee,
                'type' => 'debit',
                'transaction_type' => 'approval_fee',
                'description' => "Student Approval Fee: {$student->full_name} ({$student->enrollment_no})",
                'status' => 'success',
                'created_at' => now(),
            ]);

            // Update student
            $student->update([
                'status' => 'Approved',
                'approved_at' => now(),
            ]);

            // Notification
            Notification::create([
                'from_admin' => $currentUser->id,
                'to_admin' => $center->id,
                'student_id' => $student->id,
                'type' => 'student_approved',
                'message' => "Student {$student->full_name} ({$student->enrollment_no}) has been approved.",
                'is_read' => 0,
                'created_at' => now(),
            ]);
        });

        return back()->with('success', "Student {$student->full_name} approved successfully! (Fee deducted: ₹{$fee})");
    }

    public function transfer(Request $request, $id)
    {
        $student = Student::findOrFail($id);
        $request->validate([
            'new_admin_id' => 'required|exists:admins,id',
        ]);

        $newAdmin = Admin::findOrFail($request->new_admin_id);
        $oldAdminName = $student->admin ? $student->admin->institute_name : 'Previous Center';

        $student->update([
            'admin_id' => $newAdmin->id,
        ]);

        return back()->with('success', "Student transferred from {$oldAdminName} to {$newAdmin->institute_name} successfully.");
    }

    public function ajaxCourses($programId)
    {
        $courses = Course::where('program_id', $programId)->orderBy('course_name')->get(['id', 'course_name']);
        return response()->json($courses);
    }

    public function ajaxSubjects($courseId)
    {
        $subjects = Subject::where('course_id', $courseId)->orderBy('subject_name')->get(['id', 'subject_name', 'total_marks', 'has_practical', 'practical_marks']);
        return response()->json($subjects);
    }
}

