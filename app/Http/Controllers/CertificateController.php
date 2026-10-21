<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Student;
use App\Models\Admin;
use App\Models\Certificate;
use App\Models\AdminCertificate;
use App\Services\DocumentPdfService;

class CertificateController extends Controller
{
    public function idCard(Request $request, $studentId)
    {
        $student = Student::with(['admin', 'program', 'course'])->findOrFail($studentId);

        /** @var Admin $user */
        $user = Auth::user();
        if (!$user->isSuperAdmin() && $student->admin_id !== $user->id) {
            abort(403);
        }

        if ($request->query('format') === 'html') {
            return view('certificates.id_card', compact('student'));
        }

        return DocumentPdfService::generateIdCard($student, $request->boolean('regenerate'));
    }

    public function generateCertificate(Request $request, $studentId)
    {
        $student = Student::with(['admin', 'program', 'course', 'marks.subject'])->findOrFail($studentId);

        /** @var Admin $user */
        $user = Auth::user();
        if (!$user->isSuperAdmin() && $student->admin_id !== $user->id) {
            abort(403);
        }

        if (!$user->isSuperAdmin()) {
            if (($student->certificate_approved ?? 'Pending') !== 'Approved') {
                return redirect()->route('students.show', $studentId)
                    ->with('error', 'Certificate generation has not been approved by Super Admin yet.');
            }
        }

        if ($request->query('format') === 'html') {
            $certificate = Certificate::firstOrCreate(
                ['student_id' => $student->id],
                [
                    'certificate_id' => 'RISE-' . date('Y') . '-' . str_pad($student->id, 6, '0', STR_PAD_LEFT),
                    'issue_date'     => date('Y-m-d'),
                    'created_at'     => now(),
                ]
            );

            $marks = $student->marks;
            $totalMax = $marks->sum(fn($m) => $m->subject ? $m->subject->total_marks : 100);
            $totalObtained = $marks->sum('marks_obtained');
            $percentage = $totalMax > 0 ? round(($totalObtained / $totalMax) * 100, 2) : 0;

            return view('certificates.student_certificate', compact('student', 'certificate', 'percentage'));
        }

        return DocumentPdfService::generateCertificate($student, $request->boolean('regenerate'));
    }

    public function adminCertificate(Request $request, $adminId = null)
    {
        /** @var Admin $user */
        $user = Auth::user();

        if ($user->isSuperAdmin() && $adminId) {
            $admin = Admin::findOrFail($adminId);
        } else {
            $admin = $user;
        }

        if ($request->query('format') === 'html') {
            $certificate = AdminCertificate::firstOrCreate(
                ['admin_id' => $admin->id],
                [
                    'certificate_id' => 'RISE-AUTH-' . date('Y') . '-' . str_pad($admin->id, 4, '0', STR_PAD_LEFT),
                    'issue_date'     => date('Y-m-d'),
                    'created_at'     => now(),
                ]
            );

            return view('certificates.admin_certificate', compact('admin', 'certificate'));
        }

        return DocumentPdfService::generateAdminCertificate($admin, $request->boolean('regenerate'));
    }

    public function nttAdmitCard(Request $request, $studentId)
    {
        $student = Student::with(['admin', 'program', 'course'])->findOrFail($studentId);

        /** @var Admin $user */
        $user = Auth::user();
        if (!$user->isSuperAdmin() && $student->admin_id !== $user->id) {
            abort(403);
        }

        $semester = (int) $request->input('semester', 1);
        if ($semester < 1 || $semester > 4) {
            $semester = 1;
        }

        return DocumentPdfService::generateNttAdmitCard($student, $semester);
    }

    public function authorizedCertificates()
    {
        $centers = Admin::where('role', 'admin')->with('certificates')->orderBy('institute_name')->paginate(20);
        return view('certificates.authorized_list', compact('centers'));
    }
}
