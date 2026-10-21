<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Student;
use App\Models\Program;
use App\Models\Course;
use App\Models\Subject;
use App\Models\Mark;
use App\Models\Admin;
use App\Services\DocumentPdfService;

class MarksController extends Controller
{
    public function index(Request $request)
    {
        /** @var Admin $user */
        $user = Auth::user();

        $programs = Program::orderBy('program_name')->get();

        $selectedStudent = null;
        $subjects = collect();
        $existingMarks = collect();

        if ($request->filled('student_id')) {
            $studentQuery = Student::with(['program', 'course', 'admin'])->where('id', $request->student_id);
            if (!$user->isSuperAdmin()) {
                $studentQuery->where('admin_id', $user->id);
            }
            $selectedStudent = $studentQuery->first();

            if ($selectedStudent) {
                $subjects = Subject::where('course_id', $selectedStudent->course_id)
                    ->orderBy('subject_name')
                    ->get();

                $existingMarks = Mark::where('student_id', $selectedStudent->id)
                    ->get()
                    ->keyBy('subject_id');
            }
        }

        // Available students query for selector
        $studentsQuery = Student::where('status', 'Approved')->orderBy('full_name');
        if (!$user->isSuperAdmin()) {
            $studentsQuery->where('admin_id', $user->id);
        }
        $students = $studentsQuery->get();

        return view('marks.index', compact('programs', 'students', 'selectedStudent', 'subjects', 'existingMarks'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'student_id' => 'required|exists:students,id',
            'marks' => 'required|array',
        ]);

        $student = Student::findOrFail($request->student_id);

        /** @var Admin $user */
        $user = Auth::user();
        if (!$user->isSuperAdmin() && $student->admin_id !== $user->id) {
            abort(403);
        }

        foreach ($request->marks as $subjectId => $data) {
            $subject = Subject::find($subjectId);
            if (!$subject) continue;

            $thMarks = (int) ($data['th_marks'] ?? 0);
            $prMarks = (int) ($data['pr_marks'] ?? 0);
            $totalObtained = $thMarks + $prMarks;

            // Simple grade calculation
            $pct = ($subject->total_marks > 0) ? ($totalObtained / $subject->total_marks) * 100 : 0;
            if ($pct >= 85) $grade = 'A+';
            elseif ($pct >= 70) $grade = 'A';
            elseif ($pct >= 55) $grade = 'B';
            elseif ($pct >= 40) $grade = 'C';
            else $grade = 'D';

            Mark::updateOrCreate(
                [
                    'student_id' => $student->id,
                    'subject_id' => $subjectId,
                ],
                [
                    'th_marks' => $thMarks,
                    'pr_marks' => $prMarks,
                    'marks_obtained' => $totalObtained,
                    'grade' => $grade,
                    'created_at' => now(),
                ]
            );
        }

        return back()->with('success', 'Marks saved successfully for ' . $student->full_name);
    }

    public function marksheet(Request $request, $studentId)
    {
        $student = Student::with(['admin', 'program', 'course', 'marks.subject'])->findOrFail($studentId);

        /** @var Admin $user */
        $user = Auth::user();
        if (!$user->isSuperAdmin() && $student->admin_id !== $user->id) {
            abort(403);
        }

        if (!$user->isSuperAdmin()) {
            if (($student->marksheet_approved ?? 'Pending') !== 'Approved') {
                return redirect()->route('students.show', $studentId)
                    ->with('error', 'Marksheet is locked until Super Admin approval.');
            }
        }

        if ($request->query('format') === 'html') {
            $marks = $student->marks;
            $totalMaxMarks = $marks->sum(function ($m) {
                return $m->subject ? $m->subject->total_marks : 100;
            });
            $totalObtainedMarks = $marks->sum('marks_obtained');
            $percentage = $totalMaxMarks > 0 ? round(($totalObtainedMarks / $totalMaxMarks) * 100, 2) : 0;

            return view('marks.marksheet', compact('student', 'marks', 'totalMaxMarks', 'totalObtainedMarks', 'percentage'));
        }

        return DocumentPdfService::generateMarksheet($student, $request->boolean('regenerate'));
    }

    public function superadminMarksheet(Request $request)
    {
        $student = null;
        $marks = collect();
        $totalMaxMarks = 0;
        $totalObtainedMarks = 0;
        $percentage = 0;

        if ($request->filled('query')) {
            $q = trim($request->query('query'));
            $student = Student::with(['admin', 'program', 'course', 'marks.subject'])
                ->where('roll_no', $q)
                ->orWhere('enrollment_no', $q)
                ->first();

            if ($student) {
                $marks = $student->marks;
                $totalMaxMarks = $marks->sum(fn($m) => $m->subject ? $m->subject->total_marks : 100);
                $totalObtainedMarks = $marks->sum('marks_obtained');
                $percentage = $totalMaxMarks > 0 ? round(($totalObtainedMarks / $totalMaxMarks) * 100, 2) : 0;
            }
        }

        return view('marks.superadmin_marksheet', compact('student', 'marks', 'totalMaxMarks', 'totalObtainedMarks', 'percentage'));
    }
}

