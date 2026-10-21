<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\Certificate;

class VerificationController extends Controller
{
    public function verifyStudent(Request $request)
    {
        $student = null;
        $searched = false;

        if ($request->filled('query')) {
            $searched = true;
            $q = trim($request->query('query'));
            $student = Student::with(['admin', 'program', 'course', 'certificate', 'marks.subject'])
                ->where('roll_no', $q)
                ->orWhere('enrollment_no', $q)
                ->first();
        }

        return view('verification.verify_student', compact('student', 'searched'));
    }

    public function verifyCertificate(Request $request)
    {
        $certificate = null;
        $searched = false;

        if ($request->filled('certificate_id')) {
            $searched = true;
            $cId = trim($request->certificate_id);
            $certificate = Certificate::with(['student.program', 'student.course', 'student.admin'])
                ->where('certificate_id', $cId)
                ->first();
        }

        return view('verification.verify_certificate', compact('certificate', 'searched'));
    }
}

