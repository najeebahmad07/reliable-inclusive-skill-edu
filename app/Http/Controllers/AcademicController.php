<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Program;
use App\Models\Course;
use App\Models\Subject;

class AcademicController extends Controller
{
    // Programs
    public function programs()
    {
        $programs = Program::withCount(['courses', 'subjects', 'students'])->orderBy('program_name')->get();
        return view('academic.programs', compact('programs'));
    }

    public function storeProgram(Request $request)
    {
        $validated = $request->validate([
            'program_name' => 'required|string|max:255',
            'duration' => 'required|string|max:100',
        ]);

        Program::create($validated);
        return back()->with('success', 'Program added successfully.');
    }

    public function updateProgram(Request $request, $id)
    {
        $program = Program::findOrFail($id);
        $validated = $request->validate([
            'program_name' => 'required|string|max:255',
            'duration' => 'required|string|max:100',
        ]);

        $program->update($validated);
        return back()->with('success', 'Program updated successfully.');
    }

    public function deleteProgram($id)
    {
        $program = Program::findOrFail($id);
        if ($program->students()->count() > 0) {
            return back()->with('error', 'Cannot delete program with registered students.');
        }

        $program->courses()->delete();
        $program->subjects()->delete();
        $program->delete();

        return back()->with('success', 'Program deleted successfully.');
    }

    // Courses
    public function courses(Request $request)
    {
        $query = Course::with('program')->withCount('subjects', 'students');

        if ($request->filled('program_id')) {
            $query->where('program_id', $request->program_id);
        }

        $courses = $query->orderBy('course_name')->paginate(20)->withQueryString();
        $programs = Program::orderBy('program_name')->get();

        return view('academic.courses', compact('courses', 'programs'));
    }

    public function storeCourse(Request $request)
    {
        $validated = $request->validate([
            'program_id' => 'required|exists:programs,id',
            'course_name' => 'required|string|max:255',
        ]);

        Course::create($validated);
        return back()->with('success', 'Course added successfully.');
    }

    public function updateCourse(Request $request, $id)
    {
        $course = Course::findOrFail($id);
        $validated = $request->validate([
            'program_id' => 'required|exists:programs,id',
            'course_name' => 'required|string|max:255',
        ]);

        $course->update($validated);
        return back()->with('success', 'Course updated successfully.');
    }

    public function deleteCourse($id)
    {
        $course = Course::findOrFail($id);
        if ($course->students()->count() > 0) {
            return back()->with('error', 'Cannot delete course with active students.');
        }

        $course->subjects()->delete();
        $course->delete();

        return back()->with('success', 'Course deleted successfully.');
    }

    // Subjects
    public function subjects(Request $request)
    {
        $query = Subject::with(['program', 'course']);

        if ($request->filled('program_id')) {
            $query->where('program_id', $request->program_id);
        }
        if ($request->filled('course_id')) {
            $query->where('course_id', $request->course_id);
        }

        $subjects = $query->orderBy('subject_name')->paginate(20)->withQueryString();
        $programs = Program::orderBy('program_name')->get();
        $courses = Course::orderBy('course_name')->get();

        return view('academic.subjects', compact('subjects', 'programs', 'courses'));
    }

    public function storeSubject(Request $request)
    {
        $validated = $request->validate([
            'program_id' => 'required|exists:programs,id',
            'course_id' => 'required|exists:courses,id',
            'subject_name' => 'required|string|max:255',
            'total_marks' => 'required|integer|min:1',
            'has_practical' => 'nullable|boolean',
            'practical_marks' => 'nullable|integer|min:0',
        ]);

        $hasPractical = $request->has('has_practical');
        Subject::create([
            'program_id' => $validated['program_id'],
            'course_id' => $validated['course_id'],
            'subject_name' => $validated['subject_name'],
            'total_marks' => $validated['total_marks'],
            'has_practical' => $hasPractical ? 1 : 0,
            'practical_marks' => $hasPractical ? ($validated['practical_marks'] ?? 0) : 0,
        ]);

        return back()->with('success', 'Subject added successfully.');
    }

    public function updateSubject(Request $request, $id)
    {
        $subject = Subject::findOrFail($id);
        $validated = $request->validate([
            'program_id' => 'required|exists:programs,id',
            'course_id' => 'required|exists:courses,id',
            'subject_name' => 'required|string|max:255',
            'total_marks' => 'required|integer|min:1',
            'has_practical' => 'nullable|boolean',
            'practical_marks' => 'nullable|integer|min:0',
        ]);

        $hasPractical = $request->has('has_practical');
        $subject->update([
            'program_id' => $validated['program_id'],
            'course_id' => $validated['course_id'],
            'subject_name' => $validated['subject_name'],
            'total_marks' => $validated['total_marks'],
            'has_practical' => $hasPractical ? 1 : 0,
            'practical_marks' => $hasPractical ? ($validated['practical_marks'] ?? 0) : 0,
        ]);

        return back()->with('success', 'Subject updated successfully.');
    }

    public function deleteSubject($id)
    {
        $subject = Subject::findOrFail($id);
        $subject->delete();

        return back()->with('success', 'Subject deleted successfully.');
    }
}

