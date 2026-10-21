<?php

namespace App\Services;

use App\Models\Student;
use App\Models\Admin;
use App\Models\Certificate;
use App\Models\AdminCertificate;
use App\Models\Subject;
use App\Models\Mark;
use Illuminate\Support\Facades\DB;
use TCPDF;
use TCPDF_FONTS;

class DocumentPdfService
{
    /**
     * Ensure TCPDF is loaded
     */
    public static function loadTcpdf(): void
    {
        if (!class_exists('TCPDF')) {
            $candidates = [
                base_path('lib/TCPDF/tcpdf.php'),
                base_path('lib/tcpdf/tcpdf.php'),
                base_path('vendor/tecnickcom/tcpdf/tcpdf.php'),
            ];
            foreach ($candidates as $candidate) {
                if (file_exists($candidate)) {
                    require_once $candidate;
                    break;
                }
            }
        }

        if (!class_exists('TCPDF')) {
            abort(500, 'TCPDF library could not be loaded.');
        }
    }

    /**
     * Flatten PNG alpha to JPEG for TCPDF compatibility
     */
    public static function prepareImageForTcpdf(?string $path): array
    {
        if (!$path || !file_exists($path)) {
            return ['path' => null, 'tmp' => null];
        }
        $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
        if ($ext === 'png') {
            $src = @imagecreatefrompng($path);
            if (!$src) {
                return ['path' => null, 'tmp' => null];
            }
            $w = imagesx($src);
            $h = imagesy($src);
            $flat = imagecreatetruecolor($w, $h);
            imagefill($flat, 0, 0, imagecolorallocate($flat, 255, 255, 255));
            imagecopy($flat, $src, 0, 0, 0, 0, $w, $h);
            imagedestroy($src);
            $tmp = tempnam(sys_get_temp_dir(), 'rise_') . '.jpg';
            imagejpeg($flat, $tmp, 95);
            imagedestroy($flat);
            return ['path' => $tmp, 'tmp' => $tmp];
        }
        return ['path' => $path, 'tmp' => null];
    }

    /**
     * Clean up temporary flattened image
     */
    public static function cleanupTmp(?string $tmp): void
    {
        if ($tmp && file_exists($tmp)) {
            @unlink($tmp);
        }
    }

    /**
     * Resolve candidate photo path
     */
    public static function resolvePhotoPath(?string $photo): string
    {
        if (empty($photo)) return '';

        $photo = basename($photo);
        $candidates = [
            public_path('uploads/photos/' . $photo),
            public_path('uploads/' . $photo),
            base_path('uploads/photos/' . $photo),
        ];

        foreach ($candidates as $candidate) {
            if (file_exists($candidate) && is_file($candidate)) {
                return $candidate;
            }
        }

        return '';
    }

    /**
     * Resolve student signature path
     */
    public static function resolveStudentSignaturePath(?string $signature): string
    {
        if (empty($signature)) return '';

        $signature = basename($signature);
        $candidates = [
            public_path('uploads/signatures/' . $signature),
            public_path('uploads/' . $signature),
            base_path('uploads/signatures/' . $signature),
        ];

        // Also check missing extensions
        $ext = strtolower(pathinfo($signature, PATHINFO_EXTENSION));
        if ($ext === '') {
            $base = pathinfo($signature, PATHINFO_FILENAME);
            foreach (['png', 'jpg', 'jpeg', 'webp'] as $testExt) {
                $candidates[] = public_path('uploads/signatures/' . $base . '.' . $testExt);
            }
        }

        foreach ($candidates as $candidate) {
            if (file_exists($candidate) && is_file($candidate)) {
                return $candidate;
            }
        }

        return '';
    }

    // =========================================================================
    // 1. GENERATE ID CARD
    // =========================================================================
    public static function generateIdCard(Student $student, bool $force = false)
    {
        self::loadTcpdf();

        $student->loadMissing(['admin', 'program', 'course']);

        // Check existing file
        if (!$force && !empty($student->id_card_pdf)) {
            $existingPath = public_path('uploads/id_cards/' . $student->id_card_pdf);
            if (file_exists($existingPath)) {
                return response()->file($existingPath, [
                    'Content-Type' => 'application/pdf',
                    'Content-Disposition' => 'inline; filename="' . basename($existingPath) . '"'
                ]);
            }
        }

        // Custom TCPDF class for ID card
        $pdf = new class('L', 'mm', [90, 68], true, 'UTF-8', false) extends TCPDF {
            public function Header() {}
            public function Footer() {}
        };

        $pdf->SetCreator('RISE SaaS');
        $pdf->SetAuthor('RISE');
        $pdf->SetTitle('ID Card - ' . $student->full_name);
        $pdf->SetMargins(0, 0, 0);
        $pdf->SetAutoPageBreak(false, 0);
        $pdf->SetPrintHeader(false);
        $pdf->SetPrintFooter(false);

        // ── FRONT SIDE ──
        $pdf->AddPage();
        $CARD_W = $pdf->getPageWidth();
        $CARD_H = $pdf->getPageHeight();
        $SX = $CARD_W / 90;
        $SY = $CARD_H / 68;

        // White base
        $pdf->SetFillColor(255, 255, 255);
        $pdf->Rect(0, 0, $CARD_W, $CARD_H, 'F');

        // Decorative background
        // Light blue top-left polygon
        $pdf->SetFillColor(229, 241, 246);
        $pdf->Polygon([0, 0, 40, 0, 30, 11, 0, 11], 'F');

        // Light blue curved/soft area
        $pdf->SetFillColor(229, 241, 246);
        $pdf->Ellipse(5, 2, 19, 18, 0, 0, 360, 'F');

        // Dark navy upper-right panel
        $pdf->SetFillColor(19, 40, 82);
        $pdf->Rect(64, 0, 26, 43, 'F');
        $pdf->Ellipse(64, 13, 18, 18, 0, 0, 360, 'F');

        // Cyan accent behind QR
        $pdf->SetFillColor(38, 169, 190);
        $pdf->Ellipse(61, 42, 13, 13, 0, 0, 360, 'F');

        // Green lower-right shape
        $pdf->SetFillColor(88, 178, 62);
        $pdf->Polygon([64, 43, 90, 43, 90, 68, 55, 68], 'F');

        // Cyan lower-left triangle
        $pdf->SetFillColor(38, 169, 190);
        $pdf->Polygon([0, 49, 12, 68, 0, 68], 'F');

        // Logo
        $logoRaw = public_path('assets/images/logo.png');
        if (!file_exists($logoRaw)) {
            $logoRaw = public_path('assets/images/logo.jpg');
        }
        $logoImg = self::prepareImageForTcpdf($logoRaw);
        if ($logoImg['path']) {
            $pdf->Image($logoImg['path'], 5.5, 4.0, 18.5, 0, '', '', '', true, 300);
            self::cleanupTmp($logoImg['tmp']);
        }

        // Student Info
        $centerName = $student->admin ? ($student->admin->college_name ?: $student->admin->name) : '';
        $frontRows = [
            ['Roll No',      $student->roll_no ?? ''],
            ['Enrolment No', $student->enrollment_no ?? ''],
            ['Student Name', $student->full_name ?? ''],
            ['Father Name',  $student->father_name ?? ''],
            ['Course',       $student->course ? $student->course->course_name : ''],
            ['Session',      $student->session_name ?? ''],
            ['Batch',        $student->batch ?? ($student->session_name ?? '')],
            ['Center Name',  $centerName],
        ];

        $label_x = 5;
        $value_x = 24.5;
        $label_w = 18;
        $value_w = 31.5;
        $row_y   = 17.2;

        foreach ($frontRows as $row) {
            $label = $row[0];
            $value = trim((string)$row[1]);
            $row_h = ($label === 'Course' && strlen($value) > 30) ? 6.4 : 4.25;

            $pdf->SetFont('helvetica', 'B', 6.1);
            $pdf->SetTextColor(35, 35, 35);
            $pdf->SetXY($label_x, $row_y);
            $pdf->Cell($label_w, 4, $label . ' :', 0, 0, 'L');

            $pdf->SetFont('helvetica', '', 5.8);
            $pdf->SetTextColor(25, 25, 25);
            $pdf->SetXY($value_x, $row_y);

            if ($label === 'Course' && strlen($value) > 30) {
                $pdf->MultiCell($value_w, 3.1, strtoupper($value), 0, 'L', false, 1);
            } else {
                $pdf->Cell($value_w, 4, strtoupper($value), 0, 1, 'L');
            }

            $row_y += $row_h;
        }

        // Student Photo
        $photoRaw = self::resolvePhotoPath($student->photo);
        $photoImg = self::prepareImageForTcpdf($photoRaw);
        $photo_x = 63.0 * $SX;
        $photo_y = 7.0 * $SY;
        $photo_w = 19.0 * $SX;
        $photo_h = 25.0 * $SY;

        $pdf->SetFillColor(255, 255, 255);
        $pdf->Rect($photo_x - 1.2, $photo_y - 1.2, $photo_w + 2.4, $photo_h + 2.4, 'F');

        if (!empty($student->photo) && $photoImg['path']) {
            $pdf->Image($photoImg['path'], $photo_x, $photo_y, $photo_w, $photo_h, '', '', '', true, 300);
            self::cleanupTmp($photoImg['tmp']);
        } else {
            $pdf->SetFillColor(245, 245, 245);
            $pdf->Rect($photo_x, $photo_y, $photo_w, $photo_h, 'F');
            $pdf->SetFont('helvetica', 'B', 5);
            $pdf->SetTextColor(140, 140, 140);
            $pdf->SetXY($photo_x, $photo_y + 10);
            $pdf->Cell($photo_w, 4, 'PHOTO', 0, 0, 'C');
        }

        // QR Code
        $verifyUrl = route('verify.student') . '?enrollment=' . urlencode($student->enrollment_no ?? '');
        $qr_box_x = 67.0 * $SX;
        $qr_box_y = 34.0 * $SY;
        $qr_box_w = 16.5 * $SX;
        $qr_box_h = 16.5 * $SY;

        $pdf->SetFillColor(255, 255, 255);
        $pdf->Rect($qr_box_x, $qr_box_y, $qr_box_w, $qr_box_h, 'F');

        $pdf->write2DBarcode(
            $verifyUrl,
            'QRCODE,H',
            $qr_box_x + 1.5 * $SX,
            $qr_box_y + 1.0 * $SY,
            13.5 * $SX,
            13.5 * $SY,
            [
                'border'        => false,
                'vpadding'      => 0,
                'hpadding'      => 0,
                'fgcolor'       => [20, 20, 20],
                'bgcolor'       => [255, 255, 255],
                'module_width'  => 1,
                'module_height' => 1,
            ],
            'N'
        );

        $pdf->SetFont('helvetica', 'B', 3.8);
        $pdf->SetTextColor(30, 30, 30);
        $pdf->SetXY($qr_box_x, $qr_box_y + 13.9 * $SY);
        $pdf->Cell($qr_box_w, 2.2 * $SY, 'Scan to Verify', 0, 0, 'C');

        // ── BACK SIDE ──
        $pdf->AddPage();
        $pdf->SetFillColor(255, 255, 255);
        $pdf->Rect(0, 0, $CARD_W, $CARD_H, 'F');

        // Background shapes
        $pdf->SetFillColor(229, 241, 246);
        $pdf->Polygon([0, 0, 40, 0, 30, 11, 0, 11], 'F');
        $pdf->Ellipse(5, 2, 19, 18, 0, 0, 360, 'F');

        $pdf->SetFillColor(19, 40, 82);
        $pdf->Rect(64, 0, 26, 43, 'F');
        $pdf->Ellipse(64, 13, 18, 18, 0, 0, 360, 'F');

        $pdf->SetFillColor(38, 169, 190);
        $pdf->Ellipse(61, 42, 13, 13, 0, 0, 360, 'F');

        $pdf->SetFillColor(88, 178, 62);
        $pdf->Polygon([64, 43, 90, 43, 90, 68, 55, 68], 'F');

        $pdf->SetFillColor(38, 169, 190);
        $pdf->Polygon([0, 49, 12, 68, 0, 68], 'F');

        // Logo on back
        $logoImg = self::prepareImageForTcpdf($logoRaw);
        if ($logoImg['path']) {
            $pdf->Image($logoImg['path'], 57.5, 6.0, 24.5, 0, '', '', '', true, 300);
            self::cleanupTmp($logoImg['tmp']);
        }

        // Student Address
        $pdf->SetTextColor(30, 30, 30);
        $pdf->SetFont('helvetica', 'B', 6.2);
        $pdf->SetXY(10.5, 17.5);
        $pdf->Cell(35, 4, 'Student Address :', 0, 1, 'L');

        $pdf->SetFont('helvetica', '', 5.8);
        $pdf->SetXY(10.5, 21.0);
        $studentAddress = trim((string)($student->address ?? 'Not Available')) ?: 'Not Available';
        $pdf->MultiCell(38, 3.5, strtoupper($studentAddress), 0, 'L', false, 1);

        // Study Center Address
        $pdf->SetFont('helvetica', 'B', 6.2);
        $pdf->SetXY(10.5, 34.0);
        $pdf->Cell(40, 4, 'Study Center Address :', 0, 1, 'L');

        $pdf->SetFont('helvetica', '', 5.8);
        $pdf->SetXY(10.5, 38.0);
        $centerAddress = trim((string)($student->admin ? ($student->admin->college_name ?: $student->admin->name) : 'Not Available'));
        $pdf->MultiCell(38, 3.5, strtoupper($centerAddress), 0, 'L', false, 1);

        // Student Signature
        $studentSigRaw = self::resolveStudentSignaturePath($student->signature);
        $studentSigImg = self::prepareImageForTcpdf($studentSigRaw);
        if ($studentSigImg['path']) {
            $pdf->Image($studentSigImg['path'], 9.5 * $SX, 51.5 * $SY, 23 * $SX, 0, '', '', '', true, 300);
            self::cleanupTmp($studentSigImg['tmp']);
        }
        $pdf->SetFont('helvetica', 'B', 5.2);
        $pdf->SetTextColor(30, 30, 30);
        $pdf->SetXY(8.0 * $SX, 59.0 * $SY);
        $pdf->Cell(26 * $SX, 3 * $SY, 'Students Signature', 0, 0, 'C');

        // Authorized Signature
        $sigRaw = file_exists(public_path('assets/images/authorized_signature.jpg'))
            ? public_path('assets/images/authorized_signature.jpg')
            : public_path('assets/images/authorized_signature.png');
        $sigImg = self::prepareImageForTcpdf($sigRaw);
        if ($sigImg['path']) {
            $pdf->Image($sigImg['path'], 37.0, 52.0, 22, 7, '', '', '', true, 300);
            self::cleanupTmp($sigImg['tmp']);
        }
        $pdf->SetFont('helvetica', 'B', 5.2);
        $pdf->SetTextColor(30, 30, 30);
        $pdf->SetXY(35.0, 59.0);
        $pdf->Cell(26, 3, 'Authorised Signature', 0, 0, 'C');

        // Save PDF to disk
        $pdfFilename = 'ID_' . $student->enrollment_no . '_' . time() . '.pdf';
        $dir = public_path('uploads/id_cards');
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
        $pdfPath = $dir . '/' . $pdfFilename;
        $pdf->Output($pdfPath, 'F');

        $student->update(['id_card_pdf' => $pdfFilename]);

        return response()->file($pdfPath, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="RISE_ID_Card_' . $student->enrollment_no . '.pdf"'
        ]);
    }

    // =========================================================================
    // 2. GENERATE STUDENT CERTIFICATE
    // =========================================================================
    public static function generateCertificate(Student $student, bool $force = false)
    {
        self::loadTcpdf();

        $student->loadMissing(['admin', 'program', 'course', 'marks.subject']);

        if ($student->status !== 'Approved') {
            abort(403, 'Certificate can only be generated for approved students.');
        }

        // Marks check
        if ($student->marks->isEmpty()) {
            abort(400, 'Marks must be entered before generating certificate.');
        }

        // Check existing file
        if (!$force && !empty($student->certificate_pdf)) {
            $existingPath = public_path('uploads/certificates/' . $student->certificate_pdf);
            if (file_exists($existingPath)) {
                return response()->file($existingPath, [
                    'Content-Type' => 'application/pdf',
                    'Content-Disposition' => 'inline; filename="' . basename($existingPath) . '"'
                ]);
            }
        }

        // Calculate marks / percentage
        $totalMax = 0;
        $totalObtained = 0;
        foreach ($student->marks as $mark) {
            $thMax = (int)($mark->subject->total_marks ?? 0);
            $prMax = (int)($mark->subject->practical_marks ?? 0);
            $totalMax += ($thMax + $prMax);

            $thObt = ($mark->th_marks !== null && $mark->th_marks !== '') ? (int)$mark->th_marks : 0;
            $prObt = ($mark->pr_marks !== null && $mark->pr_marks !== '') ? (int)$mark->pr_marks : 0;

            $subjectObtained = ($mark->th_marks !== null || $mark->pr_marks !== null)
                ? ($thObt + $prObt)
                : (int)($mark->marks_obtained ?? 0);

            $totalObtained += $subjectObtained;
        }

        $percentage = ($totalMax > 0) ? round(($totalObtained / $totalMax) * 100, 2) : 0;
        if ($percentage >= 75)     { $grade = 'A'; $grade_text = 'First Division with Distinction'; }
        elseif ($percentage >= 60) { $grade = 'B'; $grade_text = 'First Division'; }
        elseif ($percentage >= 50) { $grade = 'C'; $grade_text = 'Second Division'; }
        else                       { $grade = 'Fail'; $grade_text = 'Fail'; }

        if ($grade === 'Fail') {
            abort(400, 'Certificate cannot be generated for failed students.');
        }

        // Certificate record
        $certificate = Certificate::firstOrCreate(
            ['student_id' => $student->id],
            [
                'certificate_id' => 'RISE-' . date('Y') . '-' . str_pad($student->id, 6, '0', STR_PAD_LEFT),
                'issue_date'     => date('Y-m-d'),
                'created_at'     => now(),
            ]
        );

        $collegeName = trim($student->admin ? ($student->admin->college_name ?: $student->admin->name) : '');

        // Assets
        $studentPhotoPath = self::resolvePhotoPath($student->photo);
        $logoPath         = public_path('assets/images/logo.png');
        $hasLogo          = file_exists($logoPath);
        $logosPath        = public_path('assets/images/accreditation_logos.png');
        $hasLogos         = file_exists($logosPath);
        $controllerSign   = public_path('assets/images/director_signature.jpg');
        $hasCtrlSign      = file_exists($controllerSign);
        $directorSign     = public_path('assets/images/controller_signature.jpg');
        $hasDirSign       = file_exists($directorSign);
        $fallbackSign     = public_path('assets/images/authorized_signature.jpg');
        $hasFallbackSign  = file_exists($fallbackSign);
        $sealPath         = public_path('assets/images/seal.jpg');
        $hasSeal          = file_exists($sealPath);

        $verifyUrl = route('verify.certificate') . '?cert_id=' . urlencode($certificate->certificate_id) . '&enrollment=' . urlencode($student->enrollment_no ?? '');

        // TCPDF instance
        $pdf = new class('L', 'mm', 'A4', true, 'UTF-8', false) extends TCPDF {
            public function Header() {}
            public function Footer() {}
        };

        $pdf->SetCreator('RISE SaaS');
        $pdf->SetAuthor('RISE');
        $pdf->SetTitle('Certificate – ' . $student->full_name);
        $pdf->SetMargins(0, 0, 0);
        $pdf->SetAutoPageBreak(false, 0);
        $pdf->SetPrintHeader(false);
        $pdf->SetPrintFooter(false);
        $pdf->AddPage();

        $W = 297; $H = 210;

        // Palette
        $navy       = [13, 54, 100];
        $headerBlue = [198, 224, 248];
        $headerTxt  = [15, 55, 105];
        $gold       = [184, 150, 46];
        $gold2      = [212, 175, 55];
        $white      = [255, 255, 255];
        $cream      = [250, 247, 240];
        $lBlue2     = [240, 246, 255];
        $grey1      = [85, 95, 110];
        $grey2      = [65, 78, 95];
        $green      = [26, 122, 69];
        $dkGrey     = [35, 42, 55];

        // 1. Cream background
        $pdf->SetFillColor(...$cream);
        $pdf->Rect(0, 0, $W, $H, 'F');

        // 2. Outer gold border
        $pdf->SetDrawColor(...$gold);
        $pdf->SetLineWidth(1.0);
        $pdf->Rect(4, 4, $W - 8, $H - 8, 'D');

        // 3. Inner navy border
        $pdf->SetDrawColor(...$navy);
        $pdf->SetLineWidth(2.0);
        $pdf->Rect(6.5, 6.5, $W - 13, $H - 13, 'D');

        // 4. Light-blue header band
        $hdr_h = 86;
        $pdf->SetFillColor(...$headerBlue);
        $pdf->Rect(6.5, 6.5, $W - 13, $hdr_h, 'F');

        // Gold line at bottom of header
        $pdf->SetDrawColor(...$gold);
        $pdf->SetLineWidth(0.7);
        $pdf->Line(6.5, 6.5 + $hdr_h, $W - 6.5, 6.5 + $hdr_h);

        // 5. Cert No
        $pdf->SetFont('helvetica', 'B', 10.5);
        $pdf->SetTextColor(...$headerTxt);
        $pdf->SetXY(11, 7.0);
        $pdf->Cell(110, 6, 'Certificate No: ' . $certificate->certificate_id, 0, 0, 'L');

        // 6. Enrollment No
        $pdf->SetFont('helvetica', 'B', 10.5);
        $pdf->SetTextColor(...$headerTxt);
        $pdf->SetXY($W - 121, 7.0);
        $pdf->Cell(110, 6, 'Enrollment No: ' . $student->enrollment_no, 0, 0, 'R');

        // 7. Centered Logo
        $logo_w = 50;
        $logo_y = 8;
        $logo_x = ($W - $logo_w) / 2;
        $logo_h = 0;

        if ($hasLogo) {
            list($orig_w, $orig_h) = getimagesize($logoPath);
            $logo_h = ($orig_h / $orig_w) * $logo_w;
            $pdf->Image($logoPath, $logo_x, $logo_y, $logo_w, 0, '', '', '', true, 300);
        }
        $text_after_logo_y = $logo_y + max($logo_h, 18);

        // Institute name
        $pdf->SetFont('helvetica', 'B', 21.5);
        $pdf->SetTextColor(...$headerTxt);
        $pdf->SetXY(6.5, $text_after_logo_y + 1);
        $pdf->Cell($W - 13, 10, 'RELIABLE INCLUSIVE SKILL EDUCATION', 0, 1, 'C');

        // Subline
        $pdf->SetFont('helvetica', '', 9.5);
        $pdf->SetTextColor(...$grey2);
        $pdf->SetXY(6.5, $text_after_logo_y + 11.5);
        $pdf->Cell(
            $W - 13,
            7.5,
            '(An Autonomous Institution Regd. under the Companies Act, 2013, and Incorporated under the Ministry of Corporate Affairs, Govt. of India)',
            0,
            1,
            'C'
        );

        // Accreditations
        $acc_left = [
            'Registered Under Ministry of Skill Development and Entrepreneurship, Govt. of India',
            'Micro, Small and Medium Enterprises Development, MSME Govt. of India',
        ];
        $acc_right = [
            'International Accreditation Organization (IAO) U.S.A',
            'An IAF ISO 9001:2015/29990:2010 Certified Organization',
        ];

        $acc_margin = 18;
        $acc_gap    = 8;
        $acc_col_w  = ($W - ($acc_margin * 2) - $acc_gap) / 2;
        $acc_y      = $text_after_logo_y + 21.5;

        $pdf->SetFont('helvetica', 'B', 9.0);
        $pdf->SetTextColor(...$headerTxt);

        // Left column
        $pdf->SetXY($acc_margin, $acc_y);
        $pdf->MultiCell($acc_col_w, 5.8, $acc_left[0], 0, 'C', false, 1, '', '', true, 0, false, true, 11.6, 'M');
        $pdf->SetXY($acc_margin, $acc_y + 12.2);
        $pdf->MultiCell($acc_col_w, 5.8, $acc_left[1], 0, 'C', false, 1, '', '', true, 0, false, true, 11.6, 'M');

        // Right column
        $acc_right_x = $acc_margin + $acc_col_w + $acc_gap;
        $pdf->SetXY($acc_right_x, $acc_y);
        $pdf->MultiCell($acc_col_w, 5.8, $acc_right[0], 0, 'C', false, 1, '', '', true, 0, false, true, 11.6, 'M');
        $pdf->SetXY($acc_right_x, $acc_y + 12.2);
        $pdf->MultiCell($acc_col_w, 5.8, $acc_right[1], 0, 'C', false, 1, '', '', true, 0, false, true, 11.6, 'M');

        // Gold line
        $acc_bottom = $acc_y + 23.8;
        $line_y = $acc_bottom + 2.0;
        $pdf->SetDrawColor(...$gold2);
        $pdf->SetLineWidth(0.6);
        $pdf->Line(40, $line_y, $W - 40, $line_y);

        // Title
        $pdf->SetFont('helvetica', 'B', 16);
        $pdf->SetTextColor(212, 175, 55);
        $pdf->SetXY(6.5, $line_y + 2);
        $pdf->Cell($W - 13, 8, 'C E R T I F I C A T E   O F   C O M P L E T I O N', 0, 1, 'C');

        // 8. Body
        $body_top = 6.5 + $hdr_h + 0.7;
        $pdf->SetFillColor(...$cream);
        $pdf->Rect(6.5, $body_top, $W - 13, $H - $body_top - 6.5, 'F');

        // Watermark
        $pdf->StartTransform();
        $pdf->Rotate(25, 224, 133);
        $pdf->SetAlpha(0.10);
        $pdf->SetFont('helvetica', 'B', 52);
        $pdf->SetTextColor(13, 54, 100);
        $pdf->SetXY(177, 117);
        $pdf->Cell(94, 28, 'RISE', 0, 0, 'C');
        $pdf->SetFont('helvetica', 'B', 8.5);
        $pdf->SetXY(178, 143);
        $pdf->Cell(92, 7, 'A B O V E   T H E   O R D I N A R Y', 0, 0, 'C');
        $pdf->SetAlpha(1);
        $pdf->StopTransform();

        // 9. "This is to certify that"
        $bt = $body_top + 5;
        $pdf->SetFont('times', 'I', 11);
        $pdf->SetTextColor(...$grey1);
        $pdf->SetXY(0, $bt);
        $pdf->Cell($W, 6, 'This is to certify that', 0, 1, 'C');

        // 10. Student name
        $pdf->SetFont('times', 'B', 32);
        $pdf->SetTextColor(...$navy);
        $pdf->SetXY(0, $bt + 6);
        $pdf->Cell($W, 14, $student->full_name, 0, 1, 'C');

        // Underline
        $nw = min($pdf->GetStringWidth($student->full_name), 175);
        $nux = ($W - $nw) / 2;
        $nuy = $bt + 20.5;
        $pdf->SetDrawColor(...$navy);
        $pdf->SetLineWidth(0.8);
        $pdf->Line($nux, $nuy, $nux + $nw, $nuy);

        // 11. Statement
        $pdf->SetFont('times', 'B', 13.5);
        $pdf->SetTextColor(...$dkGrey);
        $pdf->SetXY(0, $bt + 23);
        $pdf->Cell($W, 7, 'has successfully completed all requirements of the program', 0, 1, 'C');

        // 12. Program pill
        $pb_w = 172; $pb_x = ($W - $pb_w) / 2; $pb_y = $bt + 29; $pb_h = 11;
        $pdf->SetFillColor(...$navy);
        $pdf->SetDrawColor(...$gold2);
        $pdf->SetLineWidth(0.5);
        $pdf->RoundedRect($pb_x, $pb_y, $pb_w, $pb_h, 3, '1111', 'DF');

        $pdf->SetFillColor(...$gold);
        $pdf->RoundedRect($pb_x, $pb_y, 4, $pb_h, 3, '1000', 'F');

        $pdf->SetFont('times', 'B', 14.5);
        $pdf->SetTextColor(...$white);
        $pdf->SetXY($pb_x + 4, $pb_y + 1.5);
        $pdf->Cell($pb_w - 4, 8, $student->course ? $student->course->course_name : '', 0, 0, 'C');

        // 13. Session / Batch row
        $pdf->SetFont('helvetica', '', 8);
        $pdf->SetTextColor(...$grey2);
        $pdf->SetXY(0, $pb_y + $pb_h + 2.2);
        $session_val = $student->session_name ?? 'N/A';
        $batch_val   = $student->batch ?? 'N/A';
        $course_val  = $student->program ? $student->program->program_name : '';
        $pdf->Cell($W, 5, "Department: {$course_val}   |   Session: {$session_val}   |   Batch: {$batch_val}", 0, 1, 'C');

        // 14. Grade pills
        $pl_w = 125; $pl_x = ($W - $pl_w) / 2; $pl_y = $pb_y + $pb_h + 6.5;

        // Score
        $pdf->SetFillColor(...$green);
        $pdf->RoundedRect($pl_x, $pl_y, 36, 7.5, 3.75, '1111', 'F');
        $pdf->SetFont('helvetica', 'B', 7.5);
        $pdf->SetTextColor(...$white);
        $pdf->SetXY($pl_x, $pl_y + 1);
        $pdf->Cell(36, 5.5, 'Score: ' . $percentage . '%', 0, 0, 'C');

        // Grade
        $pdf->SetFillColor(...$navy);
        $pdf->RoundedRect($pl_x + 38, $pl_y, 24, 7.5, 3.75, '1111', 'F');
        $pdf->SetXY($pl_x + 38, $pl_y + 1);
        $pdf->Cell(24, 5.5, 'Grade: ' . $grade, 0, 0, 'C');

        // Grade text
        $pdf->RoundedRect($pl_x + 64, $pl_y, 60, 7.5, 3.75, '1111', 'F');
        $pdf->SetXY($pl_x + 64, $pl_y + 1);
        $pdf->Cell(60, 5.5, $grade_text, 0, 0, 'C');

        // 15. Student info boxes
        $m = 14;
        $grid_y = $pl_y + 10;
        $gap = 8;
        $box_w = 112;
        $box_h = 8.5;
        $left_x = $m + 2;
        $right_x = $left_x + $box_w + $gap;

        $dobFormatted = !empty($student->dob) ? date('d M Y', strtotime($student->dob)) : 'N/A';
        $durationVal = ($student->program ? $student->program->duration : ($student->duration ?? 'N/A')) . ' Months';

        $drawInfoBox = function($x, $y, $label, $value) use ($pdf, $box_w, $box_h, $navy, $lBlue2, $dkGrey) {
            $pdf->SetFillColor(...$lBlue2);
            $pdf->Rect($x, $y, $box_w, $box_h, 'F');
            $pdf->SetFont('helvetica', 'B', 8.2);
            $pdf->SetTextColor(...$navy);
            $pdf->SetXY($x + 4, $y + 1.8);
            $pdf->Cell(36, 5.5, $label . ':', 0, 0, 'L');
            $pdf->SetFont('helvetica', '', 8.2);
            $pdf->SetTextColor(...$dkGrey);
            $pdf->SetXY($x + 40, $y + 1.8);
            $pdf->Cell($box_w - 44, 5.5, $value, 0, 0, 'L');
        };

        // Row 1
        $drawInfoBox($left_x, $grid_y, "Father's Name", $student->father_name ?? 'N/A');
        $drawInfoBox($right_x, $grid_y, "Duration", $durationVal);

        // Row 2
        $drawInfoBox($left_x, $grid_y + $box_h + 2.5, "Date of Birth", $dobFormatted);
        $drawInfoBox($right_x, $grid_y + $box_h + 2.5, "Center Name", $collegeName ?: 'N/A');

        // 16. Student photo
        $ph_w = 29; $ph_h = 36;
        $ph_x = $W - $m - $ph_w - 4;
        $ph_y = $body_top + 2;

        $pdf->SetFillColor(...$navy);
        $pdf->Rect($ph_x - 2, $ph_y - 2, $ph_w + 4, $ph_h + 4, 'F');

        if ($studentPhotoPath) {
            $pdf->Image($studentPhotoPath, $ph_x, $ph_y, $ph_w, $ph_h, '', '', '', true, 150);
        } else {
            $pdf->SetFillColor(215, 230, 248);
            $pdf->Rect($ph_x, $ph_y, $ph_w, $ph_h, 'F');
            $pdf->SetFont('helvetica', '', 6);
            $pdf->SetTextColor(60, 90, 130);
            $pdf->SetXY($ph_x, $ph_y + ($ph_h / 2) - 4);
            $pdf->Cell($ph_w, 6, 'PHOTO', 0, 0, 'C');
        }

        $pdf->SetFont('helvetica', '', 5.5);
        $pdf->SetTextColor(80, 92, 110);
        $pdf->SetXY($ph_x - 2, $ph_y + $ph_h + 3);
        $pdf->Cell($ph_w + 4, 4, 'Certified Candidate', 0, 0, 'C');

        // 17. Footer
        $sep_y = $H - 38;
        $sig_y = $H - 16;
        $sig_lbl = $sig_y + 2;
        $sl = 54;

        $pdf->SetDrawColor(185, 205, 228);
        $pdf->SetLineWidth(0.35);
        $pdf->Line($m, $sep_y, $W - $m, $sep_y);

        $pdf->SetFillColor(245, 249, 255);
        $pdf->Rect($m, $sep_y, $W - ($m * 2), $H - $sep_y - 6.5, 'F');

        // Controller of Examinations
        $s1x = $m + 6;
        if ($hasCtrlSign) {
            $pdf->Image($controllerSign, $s1x + ($sl / 2) - 20, $sig_y - 17, 40, 14, '', '', '', true, 150, '', false, false, 0, 'CM');
        }
        $pdf->SetDrawColor(...$navy);
        $pdf->SetLineWidth(0.5);
        $pdf->Line($s1x, $sig_y, $s1x + $sl, $sig_y);
        $pdf->SetFont('helvetica', 'B', 7);
        $pdf->SetTextColor(...$navy);
        $pdf->SetXY($s1x, $sig_lbl);
        $pdf->Cell($sl, 4, 'Controller of Examinations', 0, 0, 'C');

        // Center: Date + Seal
        $cx = $W / 2;
        $pdf->SetFont('helvetica', '', 7);
        $pdf->SetTextColor(75, 90, 110);
        $pdf->SetXY(0, $sep_y + 3);
        $pdf->Cell($W, 4.5, 'DATE: ' . date('d F Y', strtotime($certificate->issue_date)), 0, 1, 'C');

        $seal_size = 16;
        $sc_x = $cx - ($seal_size / 2);
        $sc_y = $sep_y + 7;

        if ($hasSeal) {
            $sealExt = strtolower(pathinfo($sealPath, PATHINFO_EXTENSION));
            $pdf->Image($sealPath, $sc_x, $sc_y, $seal_size, $seal_size, strtoupper($sealExt), '', '', true, 150);
        } else {
            $pdf->SetDrawColor(...$navy);
            $pdf->SetLineWidth(0.6);
            $pdf->Circle($cx, $sc_y + $seal_size / 2, $seal_size / 2, 0, 360, 'D');
        }

        // Director
        $s2x = $W - $m - 6 - $sl;
        $useSign = $hasDirSign ? $directorSign : ($hasFallbackSign ? $fallbackSign : null);
        if ($useSign) {
            $pdf->Image($useSign, $s2x + ($sl / 2) - 20, $sig_y - 17, 40, 14, 'JPG', '', '', true, 150, '', false, false, 0, 'CM');
        }
        $pdf->SetDrawColor(...$navy);
        $pdf->SetLineWidth(0.5);
        $pdf->Line($s2x, $sig_y, $s2x + $sl, $sig_y);
        $pdf->SetFont('helvetica', 'B', 7);
        $pdf->SetTextColor(...$navy);
        $pdf->SetXY($s2x, $sig_lbl);
        $pdf->Cell($sl, 4, 'Director', 0, 0, 'C');

        // QR Code
        $qr_s = 16;
        $qr_x = $W - $m - $qr_s - 1;
        $qr_y = $sep_y + 3;

        $pdf->SetFillColor(...$white);
        $pdf->SetDrawColor(...$navy);
        $pdf->SetLineWidth(0.4);
        $pdf->Rect($qr_x - 1.2, $qr_y - 1.2, $qr_s + 2.4, $qr_s + 2.4, 'DF');

        $pdf->write2DBarcode(
            $verifyUrl,
            'QRCODE,H',
            $qr_x,
            $qr_y,
            $qr_s,
            $qr_s,
            ['border' => false, 'vpadding' => 0, 'hpadding' => 0, 'fgcolor' => $navy, 'bgcolor' => $white],
            'N'
        );
        $pdf->SetFont('helvetica', '', 5.5);
        $pdf->SetTextColor(75, 90, 110);
        $pdf->SetXY($qr_x - 1.5, $qr_y + $qr_s + 2.5);
        $pdf->Cell($qr_s + 3, 3.5, 'Scan to Verify', 0, 1, 'C');

        // Accreditation logos
        if ($hasLogos) {
            $lg_w = 90; $lg_h = 9;
            $pdf->Image($logosPath, ($W - $lg_w) / 2, $H - 12, $lg_w, $lg_h, '', '', '', true, 150);
        }

        // Save PDF to disk
        $pdfFilename = 'CERT_' . $student->enrollment_no . '_' . time() . '.pdf';
        $dir = public_path('uploads/certificates');
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
        $pdfPath = $dir . '/' . $pdfFilename;
        $pdf->Output($pdfPath, 'F');

        $student->update(['certificate_pdf' => $pdfFilename]);

        return response()->file($pdfPath, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="RISE_Certificate_' . $student->enrollment_no . '.pdf"'
        ]);
    }

    // =========================================================================
    // 3. GENERATE ADMIN AUTHORIZATION CERTIFICATE
    // =========================================================================
    public static function generateAdminCertificate(Admin $admin, bool $force = false)
    {
        self::loadTcpdf();

        $collegeName = trim($admin->college_name ?: $admin->name);
        $adminName   = trim($admin->name);

        // Fetch department / program
        $departmentName = DB::table('admin_programs')
            ->join('programs', 'programs.id', '=', 'admin_programs.program_id')
            ->where('admin_programs.admin_id', $admin->id)
            ->value('programs.program_name') ?? '';

        $cert = AdminCertificate::firstOrCreate(
            ['admin_id' => $admin->id],
            [
                'certificate_id' => 'RISE-AUTH-' . date('Y') . '-' . str_pad($admin->id, 4, '0', STR_PAD_LEFT),
                'issue_date'     => date('Y-m-d'),
                'created_at'     => now(),
            ]
        );

        // Assets
        $logoRaw = file_exists(public_path('assets/images/logo.jpg'))
            ? public_path('assets/images/logo.jpg')
            : public_path('assets/images/logo.png');
        $logoImg = self::prepareImageForTcpdf($logoRaw);

        $sigRaw = file_exists(public_path('assets/images/authorized_signature.jpg'))
            ? public_path('assets/images/authorized_signature.jpg')
            : (file_exists(public_path('assets/images/authorized_signature.png'))
                ? public_path('assets/images/authorized_signature.png') : '');
        $sigImg = $sigRaw ? self::prepareImageForTcpdf($sigRaw) : ['path' => null, 'tmp' => null];

        $sealRaw = file_exists(public_path('assets/images/seal.jpg'))
            ? public_path('assets/images/seal.jpg')
            : '';
        $sealImg = $sealRaw ? self::prepareImageForTcpdf($sealRaw) : ['path' => null, 'tmp' => null];

        $clogoRaw = '';
        foreach (['college_logo.jpg', 'college_logo.png'] as $f) {
            if (file_exists(public_path('assets/images/' . $f))) {
                $clogoRaw = public_path('assets/images/' . $f);
                break;
            }
        }
        $clogoImg = $clogoRaw ? self::prepareImageForTcpdf($clogoRaw) : ['path' => null, 'tmp' => null];

        $verifyUrl = route('verify.certificate') . '?cert_id=' . urlencode($cert->certificate_id);

        $pdf = new class('L', 'mm', 'A4', true, 'UTF-8', false) extends TCPDF {
            public function Header() {}
            public function Footer() {}
        };

        $pdf->SetCreator('RISE SaaS');
        $pdf->SetAuthor('RISE — Reliable Inclusive Skill Education');
        $pdf->SetTitle('Authorization Certificate — ' . $collegeName);
        $pdf->SetMargins(0, 0, 0);
        $pdf->SetAutoPageBreak(false, 0);
        $pdf->SetPrintHeader(false);
        $pdf->SetPrintFooter(false);
        $pdf->AddPage();

        $W = 297; $H = 210;
        $navy  = [13, 54, 100];
        $gold  = [180, 140, 50];
        $white = [255, 255, 255];

        // 1. White Background
        $pdf->SetFillColor(255, 255, 255);
        $pdf->Rect(0, 0, $W, $H, 'F');

        // 2. Borders
        $pdf->SetDrawColor(...$navy);
        $pdf->SetLineWidth(3.5);
        $pdf->Rect(5, 5, $W - 10, $H - 10, 'D');

        $pdf->SetDrawColor(...$gold);
        $pdf->SetLineWidth(1.0);
        $pdf->Rect(9, 9, $W - 18, $H - 18, 'D');

        $pdf->SetDrawColor(...$navy);
        $pdf->SetLineWidth(0.4);
        $pdf->Rect(12, 12, $W - 24, $H - 24, 'D');

        // Corner ornaments
        $pdf->SetFillColor(...$gold);
        foreach ([[9,9], [$W-9,9], [9,$H-9], [$W-9,$H-9]] as [$cx,$cy]) {
            $r = 3;
            $pdf->Polygon([$cx,$cy-$r, $cx+$r,$cy, $cx,$cy+$r, $cx-$r,$cy], 'F');
        }

        // 3. Header band
        $hdr_h = 55;
        $pdf->SetFillColor(...$navy);
        $pdf->Rect(5, 5, $W - 10, $hdr_h, 'F');

        $pdf->SetFillColor(25, 80, 150);
        $pdf->Rect(5, 5, $W - 10, 3, 'F');

        $pdf->SetDrawColor(...$gold);
        $pdf->SetLineWidth(1.0);
        $pdf->Line(5, 5 + $hdr_h - 1, $W - 5, 5 + $hdr_h - 1);

        // Logo
        $logo_w = 36;
        $logo_h = 14;
        $logo_x = ($W - $logo_w) / 2;
        $logo_y = 5 + 4;

        if ($logoImg['path']) {
            $pdf->Image($logoImg['path'], $logo_x, $logo_y, $logo_w, $logo_h, '', '', '', true, 300);
            self::cleanupTmp($logoImg['tmp']);
        }

        // Cert No & Date
        $pdf->SetFont('helvetica', 'B', 9);
        $pdf->SetTextColor(...$gold);
        $pdf->SetXY(15, $logo_y + 1);
        $pdf->Cell(80, 4.5, 'Cert No:', 0, 1, 'L');
        $pdf->SetFont('helvetica', 'B', 10);
        $pdf->SetTextColor(...$white);
        $pdf->SetXY(15, $logo_y + 5.5);
        $pdf->Cell(80, 5.5, $cert->certificate_id, 0, 0, 'L');

        $pdf->SetFont('helvetica', 'B', 9);
        $pdf->SetTextColor(...$gold);
        $pdf->SetXY($W - 95, $logo_y + 1);
        $pdf->Cell(80, 4.5, 'Issue Date:', 0, 1, 'R');
        $pdf->SetFont('helvetica', 'B', 10);
        $pdf->SetTextColor(...$white);
        $pdf->SetXY($W - 95, $logo_y + 5.5);
        $pdf->Cell(80, 5.5, date('d F Y', strtotime($cert->issue_date)), 0, 0, 'R');

        // Heading
        $pdf->SetFont('helvetica', 'B', 16);
        $pdf->SetTextColor(255, 255, 255);
        $pdf->SetXY(5, $logo_y + $logo_h + 2);
        $pdf->Cell($W - 10, 7, 'Reliable Inclusive Skill Education', 0, 1, 'C');

        // Tagline
        $pdf->SetFont('helvetica', 'B', 7.5);
        $pdf->SetTextColor(200, 222, 248);
        $pdf->SetXY(5, $pdf->GetY());
        $pdf->MultiCell($W - 10, 4,
            '(An Autonomous Institution Regd. under the Companies Act, 2013, and Incorporated under the Ministry of Corporate Affairs, Govt. of India',
            0, 'C'
        );

        $cur_y = $pdf->GetY() + 1;
        if ($clogoImg['path']) {
            $clogo_w = 16; $clogo_h = 16;
            $clogo_x = ($W - $clogo_w) / 2;
            $pdf->Image($clogoImg['path'], $clogo_x, $cur_y, $clogo_w, $clogo_h, '', '', '', true, 200);
            self::cleanupTmp($clogoImg['tmp']);
            $cur_y += $clogo_h + 1;
        }

        $pdf->SetFont('helvetica', 'B', 15);
        $pdf->SetTextColor(...$white);
        $pdf->SetXY(5, $cur_y);
        $pdf->Cell($W - 10, 6, $adminName, 0, 1, 'C');

        // 4. Body
        $body_y = 5 + $hdr_h + 10;
        $pdf->SetFont('helvetica', 'B', 14);
        $pdf->SetTextColor(...$navy);
        $pdf->SetXY(0, $body_y);
        $pdf->Cell($W, 8, 'A U T H O R I Z A T I O N   C E R T I F I C A T E', 0, 1, 'C');

        $pdf->SetDrawColor(...$gold);
        $pdf->SetLineWidth(0.8);
        $pdf->Line(60, $body_y + 9, $W - 60, $body_y + 9);

        $pdf->SetFont('times', 'I', 13);
        $pdf->SetTextColor(70, 80, 100);
        $pdf->SetXY(0, $body_y + 13);
        $pdf->Cell($W, 7, 'This is to certify that', 0, 1, 'C');

        // College Name Box
        $cn_y = $body_y + 22;
        $cn_w = $W - 60;
        $cn_x = 30;

        $pdf->SetDrawColor(...$gold);
        $pdf->SetLineWidth(1.5);
        $pdf->RoundedRect($cn_x - 2, $cn_y - 2, $cn_w + 4, 22, 4, '1111', 'D');

        $pdf->SetFillColor(...$navy);
        $pdf->RoundedRect($cn_x, $cn_y, $cn_w, 18, 3, '1111', 'F');

        $cn_fontsize = strlen($collegeName) > 40 ? 16 : (strlen($collegeName) > 28 ? 19 : 22);
        $pdf->SetFont('times', 'B', $cn_fontsize);
        $pdf->SetTextColor(...$white);
        $pdf->SetXY($cn_x, $cn_y + 1);
        $pdf->Cell($cn_w, 16, $collegeName, 0, 0, 'C');

        $pdf->SetFont('times', '', 12);
        $pdf->SetTextColor(50, 60, 80);
        $pdf->SetXY(0, $cn_y + 22);
        $pdf->Cell($W, 7, 'is an Officially Authorized Training Center of', 0, 1, 'C');

        $pdf->SetFont('times', 'B', 15);
        $pdf->SetTextColor(...$navy);
        $pdf->SetX(0);
        $pdf->Cell($W, 7, !empty($departmentName) ? $departmentName : 'Department Not Assigned', 0, 1, 'C');

        // 5. Signature Section
        $sig_base_y = $H - 42;
        $pdf->SetDrawColor(185, 205, 228);
        $pdf->SetLineWidth(0.3);
        $pdf->Line(15, $sig_base_y, $W - 15, $sig_base_y);

        $pdf->SetFillColor(247, 250, 255);
        $pdf->Rect(14, $sig_base_y, $W - 28, 28, 'F');

        $sig_line_y = $sig_base_y + 20;
        $sl = 65;

        // QR Code
        $qr_s = 24;
        $qr_x = 22;
        $qr_y2 = $sig_base_y + 2;

        $pdf->SetFillColor(255, 255, 255);
        $pdf->SetDrawColor(...$navy);
        $pdf->SetLineWidth(0.4);
        $pdf->Rect($qr_x - 1.5, $qr_y2 - 1.5, $qr_s + 3, $qr_s + 3, 'DF');

        $pdf->write2DBarcode(
            $verifyUrl, 'QRCODE,H',
            $qr_x, $qr_y2, $qr_s, $qr_s,
            ['border' => false, 'vpadding' => 0, 'hpadding' => 0, 'fgcolor' => $navy, 'bgcolor' => $white],
            'N'
        );
        $pdf->SetFont('helvetica', 'B', 6);
        $pdf->SetTextColor(...$navy);
        $pdf->SetXY($qr_x - 1.5, $qr_y2 + $qr_s + 1.5);
        $pdf->Cell($qr_s + 3, 3.5, 'Scan to Verify', 0, 0, 'C');

        // Seal
        $seal_cx = $W / 2;
        $seal_cy = $sig_base_y + 16;
        if ($sealImg['path']) {
            $seal_s = 24;
            $pdf->Image($sealImg['path'], $seal_cx - $seal_s / 2, $seal_cy - $seal_s / 2, $seal_s, $seal_s, '', '', '', true, 200);
            self::cleanupTmp($sealImg['tmp']);
        }

        // Director signature
        $s2x = $W - 22 - $sl;
        if ($sigImg['path']) {
            $pdf->Image($sigImg['path'], $s2x + ($sl / 2) - 18, $sig_line_y - 13, 36, 12, '', '', '', true, 150, '', false, false, 0, 'CM');
            self::cleanupTmp($sigImg['tmp']);
        }

        $pdf->SetDrawColor(...$navy);
        $pdf->SetLineWidth(0.5);
        $pdf->Line($s2x, $sig_line_y, $s2x + $sl, $sig_line_y);
        $pdf->SetFont('helvetica', 'B', 7);
        $pdf->SetTextColor(...$navy);
        $pdf->SetXY($s2x, $sig_line_y + 2);
        $pdf->Cell($sl, 4, 'Director', 0, 0, 'C');

        $pdf->SetFont('helvetica', '', 6.5);
        $pdf->SetTextColor(90, 105, 125);
        $pdf->SetXY($s2x, $sig_line_y + 6);
        $pdf->Cell($sl, 4, 'RISE — Reliable Inclusive Skill Education', 0, 0, 'C');

        $safeName = preg_replace('/[^A-Za-z0-9_]/', '_', $collegeName);

        return response($pdf->Output('', 'S'), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="RISE_Authorization_' . $safeName . '.pdf"'
        ]);
    }

    // =========================================================================
    // 4. GENERATE MARKSHEET
    // =========================================================================
    public static function generateMarksheet(Student $student, bool $force = false)
    {
        self::loadTcpdf();

        $student->loadMissing(['admin', 'program', 'course', 'marks.subject']);

        if ($student->status !== 'Approved') {
            abort(403, 'Marksheet can only be generated for approved students.');
        }

        // Check existing file
        if (!$force && !empty($student->marksheet_pdf)) {
            $existingPath = public_path('uploads/marksheets/' . $student->marksheet_pdf);
            if (file_exists($existingPath)) {
                return response()->file($existingPath, [
                    'Content-Type' => 'application/pdf',
                    'Content-Disposition' => 'inline; filename="' . basename($existingPath) . '"'
                ]);
            }
        }

        // Auto serial number
        $serialNumber = $student->marksheet_serial ?? '';
        if (empty($serialNumber)) {
            $yearSuffix = date('y');
            $studentCode = str_pad($student->id, 2, '0', STR_PAD_LEFT);
            $serialPrefix = $yearSuffix . $studentCode;

            $lastSerial = Student::where('marksheet_serial', 'LIKE', $serialPrefix . '%')
                ->orderBy('marksheet_serial', 'desc')
                ->value('marksheet_serial');

            if ($lastSerial) {
                $lastNum = (int) substr($lastSerial, -4);
                $nextNum = $lastNum + 1;
            } else {
                $nextNum = 1;
            }

            $serialNumber = $serialPrefix . str_pad($nextNum, 4, '0', STR_PAD_LEFT);
            $student->update(['marksheet_serial' => $serialNumber]);
        }

        // Fetch marks with practicals
        $marks = $student->marks()->with('subject')->get()->sortBy('subject.subject_name');
        if ($marks->isEmpty()) {
            abort(400, 'No marks found. Please enter marks first.');
        }

        $subjectCount = $marks->count();
        $compact = ($subjectCount > 10);

        // Totals
        $totalMax = 0;
        $totalObtained = 0;
        foreach ($marks as $m) {
            $thMax = (int)($m->subject->total_marks ?? 0);
            $prMax = (int)($m->subject->practical_marks ?? 0);
            $totalMax += ($thMax + $prMax);
            $totalObtained += (int)$m->marks_obtained;
        }

        $percentage = ($totalMax > 0) ? round(($totalObtained / $totalMax) * 100, 2) : 0;
        if ($percentage >= 75)      { $gradeText = 'First Division';  $overallGrade = 'A'; }
        elseif ($percentage >= 60)  { $gradeText = 'Second Division'; $overallGrade = 'B'; }
        elseif ($percentage >= 50)  { $gradeText = 'Third Division';  $overallGrade = 'C'; }
        else                        { $gradeText = 'Fail';            $overallGrade = 'F'; }
        $resultStatus = ($percentage >= 50) ? 'PASS' : 'FAIL';

        $pdf = new class('P', 'mm', 'A4', true, 'UTF-8', false) extends TCPDF {
            public function Header() {}
            public function Footer() {}
        };

        $pdf->SetCreator('RISE SaaS');
        $pdf->SetAuthor('RISE');
        $pdf->SetTitle('Marksheet - ' . $student->full_name);
        $pdf->SetMargins(15, 10, 15);
        $pdf->SetAutoPageBreak(false, 0);
        $pdf->SetPrintHeader(false);
        $pdf->SetPrintFooter(false);
        $pdf->AddPage();

        // Watermark
        $pdf->SetFont('helvetica', 'B', 45);
        $pdf->SetTextColor(220, 220, 220);
        $pdf->SetAlpha(0.15);
        $pdf->StartTransform();
        $pdf->Rotate(45, 105, 148);
        $pdf->SetXY(20, 120);
        $pdf->Cell(160, 20, 'Reliable Inclusive Skill Education', 0, 0, 'C');
        $pdf->StopTransform();
        $pdf->SetAlpha(1);
        $pdf->SetTextColor(33, 37, 41);

        // Borders
        $pdf->SetDrawColor(13, 110, 253);
        $pdf->SetLineWidth(1);
        $pdf->Rect(8, 8, 194, 281, 'D');
        $pdf->SetLineWidth(0.3);
        $pdf->Rect(10, 10, 190, 277, 'D');

        // Logo
        $logoPath = file_exists(public_path('assets/images/logo.jpg'))
            ? public_path('assets/images/logo.jpg')
            : public_path('assets/images/logo.png');
        if (file_exists($logoPath)) {
            $pdf->Image($logoPath, 85, 13, 40, 0, '', '', '', true, 300);
        }

        // Institute Name
        $pdf->SetFont('helvetica', 'B', 18);
        $pdf->SetTextColor(13, 54, 100);
        $pdf->SetXY(15, 28);
        $pdf->Cell(180, 8, 'RELIABLE INCLUSIVE SKILL EDUCATION', 0, 1, 'C');
        $pdf->SetFont('helvetica', '', 7);
        $pdf->SetTextColor(80, 80, 80);
        $pdf->SetX(15);
        $pdf->Cell(180, 4,
            '(An Autonomous Institution Registered Under the Companies Act, 2013 / Sec 18, Incorporated Under Ministry of Corporate Affairs, Government of India)',
            0, 1, 'C'
        );

        // Statement of Marks bar
        $pdf->Ln(2);
        $pdf->SetFillColor(13, 110, 253);
        $pdf->SetTextColor(255, 255, 255);
        $pdf->SetFont('helvetica', 'B', 14);
        $pdf->SetX(50);
        $pdf->Cell(110, 9, 'STATEMENT OF MARKS', 0, 1, 'C', true);

        // Student details + photo
        $details_start_y = $pdf->GetY() + 4;
        $photo_x = 163; $photo_y = $details_start_y; $photo_w = 28; $photo_h = 33;
        $photoPath = self::resolvePhotoPath($student->photo);
        if ($photoPath) {
            $pdf->Image($photoPath, $photo_x, $photo_y, $photo_w, $photo_h, '', '', '', true, 300);
        }
        $pdf->SetDrawColor(13, 110, 253);
        $pdf->SetLineWidth(0.5);
        $pdf->Rect($photo_x - 0.5, $photo_y - 0.5, $photo_w + 1, $photo_h + 1, 'D');

        $detailsLeft = [
            'Center Name'   => strtoupper($student->admin ? ($student->admin->college_name ?: $student->admin->name) : ''),
            'Student Name'  => strtoupper($student->full_name ?? ''),
            "Father's Name" => strtoupper($student->father_name ?? ''),
            "Mother's Name" => strtoupper($student->mother_name ?? ''),
            'Date of Birth' => !empty($student->dob) ? date('d-m-Y', strtotime($student->dob)) : '',
            'Enrollment No' => $student->enrollment_no ?? '',
            'Roll No'       => $student->roll_no ?? '',
            'Program'       => $student->program ? $student->program->program_name : '',
            'Course'        => $student->course ? $student->course->course_name : '',
            'Session'       => $student->session_name ?? '',
            'Batch'         => $student->batch ?? '',
        ];

        $y = $details_start_y;
        $row_h = $compact ? 4.5 : 5.5;
        foreach ($detailsLeft as $label => $value) {
            $pdf->SetFont('helvetica', 'B', 9);
            $pdf->SetTextColor(33, 37, 41);
            $pdf->SetXY(18, $y);
            $pdf->Cell(35, $row_h, $label, 0, 0, 'L');
            $pdf->SetFont('helvetica', '', 9);
            $pdf->Cell(5, $row_h, ':', 0, 0, 'C');
            $pdf->Cell(100, $row_h, $value, 0, 1, 'L');
            $y += $row_h;
        }

        // Serial number top left
        $pdf->SetFont('helvetica', 'B', 10);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetXY(13, 14);
        $pdf->Cell(22, 5, 'Serial No :', 0, 0, 'L');
        $pdf->SetFont('helvetica', 'B', 11);
        $pdf->Cell(35, 5, $serialNumber, 0, 1, 'L');

        // Full width marks table
        $table_start_y = $y + ($compact ? 2 : 4);
        $pageWidth = $pdf->getPageWidth();
        $leftMargin = 10;
        $rightMargin = 10;
        $usableWidth = $pageWidth - ($leftMargin + $rightMargin);

        $pdf->SetXY($leftMargin, $table_start_y);
        $pdf->SetFont('helvetica', '', 7.5);
        $pdf->SetCellPadding($compact ? 0.3 : 1);

        $mark_row_h = 7;
        $data_font  = 7.5;
        $total_row_h = 8;
        if ($compact) {
            $table_bottom_limit = 218;
            $avail = $table_bottom_limit - ($table_start_y + 8);
            $mark_row_h = max(3.6, min(7, $avail / $subjectCount));
            $data_font  = ($mark_row_h < 5) ? 6.5 : 7;
            $total_row_h = 6;
        }

        $anyPractical = false;
        foreach ($marks as $m) {
            if ($m->subject && $m->subject->has_practical) {
                $anyPractical = true;
                break;
            }
        }

        $wSno     = 8;
        $wSubject = 70;
        $wGrade   = 12;

        $remaining = $usableWidth - ($wSno + $wSubject + $wGrade);
        $colCount  = $anyPractical ? 8 : 6;
        $col       = $remaining / $colCount;

        $wThMax  = $col;
        $wThMin  = $col;
        $wPrMax  = $col;
        $wPrMin  = $col;
        $wTotMax = $col;
        $wTh    = $col;
        $wPr    = $col;
        $wTotal = $col;

        // Header
        $pdf->SetFillColor(13, 110, 253);
        $pdf->SetTextColor(255, 255, 255);
        $pdf->SetFont('helvetica', 'B', 8);

        $pdf->SetX($leftMargin);
        $pdf->Cell($wSno, 8, 'S.No', 1, 0, 'C', true);
        $pdf->Cell($wSubject, 8, 'Subject', 1, 0, 'C', true);

        $schemeWidth = $wThMax + $wThMin + ($anyPractical ? $wPrMax + $wPrMin : 0) + $wTotMax;
        $obtainedWidth = $wTh + ($anyPractical ? $wPr : 0) + $wTotal + $wGrade;

        $pdf->Cell($schemeWidth, 4, 'SCHEME OF MARKS', 1, 0, 'C', true);
        $pdf->Cell($obtainedWidth, 4, 'MARKS OBTAINED', 1, 1, 'C', true);

        $pdf->SetX($leftMargin + $wSno + $wSubject);
        $pdf->Cell($wThMax, 4, 'TH MAX', 1, 0, 'C', true);
        $pdf->Cell($wThMin, 4, 'TH MIN', 1, 0, 'C', true);

        if ($anyPractical) {
            $pdf->Cell($wPrMax, 4, 'PR MAX', 1, 0, 'C', true);
            $pdf->Cell($wPrMin, 4, 'PR MIN', 1, 0, 'C', true);
        }

        $pdf->Cell($wTotMax, 4, 'TOTAL', 1, 0, 'C', true);
        $pdf->Cell($wTh, 4, 'TH', 1, 0, 'C', true);

        if ($anyPractical) {
            $pdf->Cell($wPr, 4, 'PR', 1, 0, 'C', true);
        }

        $pdf->Cell($wTotal, 4, 'TOTAL', 1, 0, 'C', true);
        $pdf->Cell($wGrade, 4, 'GRADE', 1, 1, 'C', true);

        // Data Rows
        $pdf->SetTextColor(33, 37, 41);
        $pdf->SetDrawColor(200, 200, 200);
        $pdf->SetFont('helvetica', '', $data_font);

        $sno = 1;
        $fill = false;
        $total_th = 0;
        $total_pr = 0;

        foreach ($marks as $mark) {
            $th_max = (int)($mark->subject->total_marks ?? 0);
            $pr_max = (int)($mark->subject->practical_marks ?? 0);

            $th_min = round($th_max * 0.35);
            $pr_min = round($pr_max * 0.35);

            $th_obt = (int)($mark->th_marks ?? 0);
            $pr_obt = (int)($mark->pr_marks ?? 0);

            $tot_max = $th_max + $pr_max;
            $tot_obt = $th_obt + $pr_obt;

            $total_th += $th_obt;
            $total_pr += $pr_obt;

            $pct = ($tot_max > 0) ? ($tot_obt / $tot_max) * 100 : 0;
            $grade = ($pct >= 90) ? 'A+' : (($pct >= 75) ? 'A' : (($pct >= 60) ? 'B' : (($pct >= 50) ? 'C' : 'F')));

            $pdf->SetFillColor($fill ? 248 : 255, $fill ? 249 : 255, $fill ? 250 : 255);
            $pdf->SetX($leftMargin);
            $pdf->Cell($wSno, $mark_row_h, $sno, 1, 0, 'C', true);
            $pdf->Cell($wSubject, $mark_row_h, mb_substr($mark->subject->subject_name ?? 'Subject', 0, 48), 1, 0, 'L', true);

            $pdf->Cell($wThMax, $mark_row_h, $th_max, 1, 0, 'C', true);
            $pdf->Cell($wThMin, $mark_row_h, $th_min, 1, 0, 'C', true);

            if ($anyPractical) {
                $hasPr = $mark->subject && $mark->subject->has_practical;
                $pdf->Cell($wPrMax, $mark_row_h, $hasPr ? $pr_max : '-', 1, 0, 'C', true);
                $pdf->Cell($wPrMin, $mark_row_h, $hasPr ? $pr_min : '-', 1, 0, 'C', true);
            }

            $pdf->Cell($wTotMax, $mark_row_h, $tot_max, 1, 0, 'C', true);
            $pdf->Cell($wTh, $mark_row_h, $th_obt, 1, 0, 'C', true);

            if ($anyPractical) {
                $hasPr = $mark->subject && $mark->subject->has_practical;
                $pdf->Cell($wPr, $mark_row_h, $hasPr ? $pr_obt : '-', 1, 0, 'C', true);
            }

            $pdf->Cell($wTotal, $mark_row_h, $tot_obt, 1, 0, 'C', true);
            $pdf->Cell($wGrade, $mark_row_h, $grade, 1, 1, 'C', true);

            $fill = !$fill;
            $sno++;
        }

        // Total row
        $pdf->SetFont('helvetica', 'B', 8);
        $pdf->SetFillColor(13, 110, 253);
        $pdf->SetTextColor(255, 255, 255);
        $pdf->SetX($leftMargin);

        $colspan = $wSno + $wSubject + $wThMax + $wThMin + ($anyPractical ? $wPrMax + $wPrMin : 0) + $wTotMax;
        $pdf->Cell($colspan, $total_row_h, 'TOTAL', 1, 0, 'R', true);
        $pdf->Cell($wTh, $total_row_h, $total_th, 1, 0, 'C', true);

        if ($anyPractical) {
            $pdf->Cell($wPr, $total_row_h, $total_pr, 1, 0, 'C', true);
        }

        $pdf->Cell($wTotal, $total_row_h, $totalObtained, 1, 0, 'C', true);
        $pdf->Cell($wGrade, $total_row_h, $overallGrade, 1, 1, 'C', true);

        $pdf->SetCellPadding(1);

        // Result summary
        $sum_h = $compact ? 4.8 : 7;
        $pdf->Ln($compact ? 1 : 4);
        $pdf->SetTextColor(33, 37, 41);
        $pdf->SetFont('helvetica', 'B', 10);
        $pdf->SetX(20);
        $pdf->Cell(60, $sum_h, 'Total Maximum Marks:', 0, 0, 'L');
        $pdf->SetFont('helvetica', '', 10);
        $pdf->Cell(30, $sum_h, $totalMax, 0, 1, 'L');

        $pdf->SetFont('helvetica', 'B', 10);
        $pdf->SetX(20);
        $pdf->Cell(60, $sum_h, 'Total Obtained Marks:', 0, 0, 'L');
        $pdf->SetFont('helvetica', '', 10);
        $pdf->Cell(30, $sum_h, $totalObtained, 0, 1, 'L');

        $pdf->SetFont('helvetica', 'B', 10);
        $pdf->SetX(20);
        $pdf->Cell(40, $sum_h, 'Percentage:', 0, 0, 'L');
        $pdf->SetFont('helvetica', '', 10);
        $pdf->Cell(40, $sum_h, $percentage . '%', 0, 0, 'L');
        $pdf->SetFont('helvetica', 'B', 10);
        $pdf->Cell(30, $sum_h, 'Division:', 0, 0, 'L');
        $pdf->SetFont('helvetica', '', 10);
        $pdf->Cell(50, $sum_h, $gradeText, 0, 1, 'L');

        $pdf->SetFont('helvetica', 'B', 10);
        $pdf->SetX(20);
        $pdf->Cell(40, $sum_h, 'Overall Grade:', 0, 0, 'L');
        $pdf->SetFont('helvetica', '', 10);
        $pdf->Cell(40, $sum_h, $overallGrade, 0, 0, 'L');
        $pdf->SetFont('helvetica', 'B', 10);
        $pdf->Cell(30, $sum_h, 'Result:', 0, 0, 'L');
        $pdf->SetTextColor($resultStatus === 'PASS' ? 25 : 220, $resultStatus === 'PASS' ? 135 : 53, $resultStatus === 'PASS' ? 84 : 69);
        $pdf->SetFont('helvetica', 'B', 13);
        $pdf->Cell(50, $sum_h, $resultStatus, 0, 1, 'L');
        $pdf->SetTextColor(33, 37, 41);

        // Issue Date
        $pdf->Ln($compact ? 1 : 3);
        $pdf->SetFont('helvetica', '', 9);
        $pdf->SetX(18);
        $pdf->Cell(80, 5, 'Date of Issue: ' . date('d-m-Y'), 0, 0, 'L');
        $pdf->Ln(5);

        // QR + Signature
        $qr_size = $compact ? 24 : 32;
        $sig_y = $pdf->GetY();
        $verifyUrl = route('verify.student') . '?enrollment=' . urlencode($student->enrollment_no ?? '') . '&serial=' . urlencode($serialNumber);

        $qr_style = ['border' => 0, 'padding' => 1, 'fgcolor' => [13, 54, 100], 'bgcolor' => false];
        $pdf->write2DBarcode($verifyUrl, 'QRCODE,H', 18, $sig_y, $qr_size, $qr_size, $qr_style, 'N');
        $pdf->SetFont('helvetica', 'B', 9);
        $pdf->SetTextColor(13, 54, 100);
        $pdf->SetXY(18, $sig_y + $qr_size + 1);
        $pdf->Cell($qr_size, 5, 'Scan to Verify', 0, 0, 'C');

        $signaturePath = public_path('assets/images/authorized_signature.jpg');
        if (file_exists($signaturePath)) {
            $pdf->Image($signaturePath, 138, $sig_y, 40, 14, 'JPG');
        }
        $pdf->SetDrawColor(33, 37, 41);
        $pdf->SetLineWidth(0.3);
        $pdf->Line(133, $sig_y + 16, 183, $sig_y + 16);
        $pdf->SetFont('helvetica', 'B', 9);
        $pdf->SetTextColor(33, 37, 41);
        $pdf->SetXY(133, $sig_y + 17);
        $pdf->Cell(50, 5, 'Director', 0, 0, 'C');

        // Grading scale
        $pdf->SetFont('helvetica', 'B', 7);
        $pdf->SetTextColor(33, 37, 41);
        $pdf->SetXY(18, $sig_y + $qr_size + ($compact ? 7 : 12));
        $pdf->Cell(164, 4, 'Grading Scale: A+ (90%+) | A (75-89%) | B (60-74%) | C (50-59%) | F (Below 50%)', 0, 1, 'L');

        // ── PAGE 2: BACK PAGE ──
        $pdf->AddPage();
        $pdf->SetAutoPageBreak(true, 15);
        $pdf->SetTextColor(33, 37, 41);
        $pdf->SetDrawColor(13, 110, 253);
        $pdf->SetLineWidth(1);
        $pdf->Rect(8, 8, 194, 280, 'D');
        $pdf->SetLineWidth(0.3);
        $pdf->Rect(10, 10, 190, 276, 'D');

        $pdf->SetFont('helvetica', 'B', 14);
        $pdf->SetTextColor(33, 37, 41);
        $pdf->SetXY(15, 18);
        $pdf->Cell(180, 8, 'Instruction', 0, 1, 'C');
        $pdf->SetDrawColor(33, 37, 41);
        $pdf->SetLineWidth(0.5);
        $pdf->Line(75, 26, 135, 26);
        $pdf->Ln(4);

        $pdf->SetFont('helvetica', '', 9);
        $pdf->SetLineWidth(0.2);
        $instructions = [
            'Pass : 35% marks in each subject with an overall aggregate of 35% for all put together.',
            "Division :",
            'Carry Forward : One in which a student scores less than 35% marks, examination for carry forward paper will be held with regular student only.',
            'Re-evaluation : Application for re-evaluation of only theory papers(s) shall be accepted within 15 days of the date of declaration of results.',
            'This mark card cannot be used as a legal proof for date of birth.',
        ];

        $y = $pdf->GetY();
        foreach ($instructions as $idx => $instruction) {
            $pdf->SetXY(15, $y);
            $pdf->SetFont('helvetica', '', 9);
            $pdf->Cell(7, 5, ($idx + 1) . '.', 0, 0, 'L');
            if ($idx === 1) {
                $pdf->SetXY(22, $y);
                $pdf->Cell(20, 5, 'Division :', 0, 0, 'L');
                $y += 5;
                foreach ([['Distinction','80% & above'],['First Division','65% & above'],['Second Division','55% & above']] as $div) {
                    $pdf->SetXY(30, $y);
                    $pdf->Cell(35, 5, $div[0], 0, 0, 'L');
                    $pdf->Cell(8,  5, '-',     0, 0, 'C');
                    $pdf->Cell(35, 5, $div[1], 0, 0, 'L');
                    $y += 5;
                }
            } else {
                $pdf->SetXY(22, $y);
                $pdf->MultiCell(168, 5, $instruction, 0, 'L');
                $y = $pdf->GetY() + 2;
            }
            $y += 2;
        }

        $pdf->Ln(4);
        $pdf->SetFont('helvetica', 'B', 9);
        foreach (['TH - THEORY','PR - PRACTICAL','IA - INTERNAL ASSESSMENT','YR - YEAR','SEM - SEMESTER'] as $line) {
            $pdf->SetX(15);
            $pdf->Cell(180, 6, $line, 0, 1, 'L');
        }

        $pdf->Ln(4);
        $pdf->SetFillColor(33, 37, 41);
        $pdf->SetTextColor(255, 255, 255);
        $pdf->SetFont('helvetica', 'B', 11);
        $pdf->SetX(15);
        $pdf->Cell(180, 8, 'Grading of Marks Obtained', 1, 1, 'C', true);

        $pdf->SetFillColor(230, 230, 230);
        $pdf->SetTextColor(33, 37, 41);
        $pdf->SetFont('helvetica', 'B', 9);
        $pdf->SetX(15);
        foreach (['Marks Range','Grade','Marks Range','Grade'] as $h) {
            $pdf->Cell(45, 7, $h, 1, 0, 'C', true);
        }
        $pdf->Ln();

        $grade_rows = [['90 - 100','A+','40 - 49','C'],['75 - 89','A','33 - 39','D+'],['70 - 74','B+','20 - 32','D'],['60 - 69','B','0 - 19','E'],['50 - 59','C+','','']];
        $pdf->SetFont('helvetica', '', 9);
        $fill_row = false;
        foreach ($grade_rows as $row) {
            $pdf->SetFillColor($fill_row ? 248 : 255, $fill_row ? 249 : 255, $fill_row ? 250 : 255);
            $pdf->SetX(15);
            foreach ($row as $cell) $pdf->Cell(45, 6, $cell, 1, 0, 'C', true);
            $pdf->Ln();
            $fill_row = !$fill_row;
        }

        // Back page QR + Seal
        $pdf->Ln(10);
        $back_qr_y = $pdf->GetY();
        $pdf->write2DBarcode($verifyUrl, 'QRCODE,H', 15, $back_qr_y, 32, 32, $qr_style, 'N');
        $pdf->SetFont('helvetica', 'B', 9);
        $pdf->SetTextColor(13, 54, 100);
        $pdf->SetXY(15, $back_qr_y + 33);
        $pdf->Cell(32, 5, 'Scan to Verify', 0, 0, 'C');
        $pdf->SetFont('helvetica', 'B', 8);
        $pdf->SetTextColor(33, 37, 41);
        $pdf->SetXY(15, $back_qr_y + 38);
        $pdf->Cell(32, 4, 'Serial No : ' . $serialNumber, 0, 0, 'C');

        $pdf->SetFont('helvetica', 'B', 9);
        $pdf->SetTextColor(33, 37, 41);
        $pdf->SetXY(110, $back_qr_y);
        $pdf->Cell(85, 5, 'Checked and Entered in the result record', 0, 1, 'C');
        $pdf->SetDrawColor(13, 110, 253);
        $pdf->SetLineWidth(0.5);
        $pdf->Circle(152, $back_qr_y + 16, 12, 0, 360, 'D');

        $sealPath = public_path('assets/images/seal.jpg');
        if (file_exists($sealPath)) {
            $pdf->Image($sealPath, 140, $back_qr_y + 4, 24, 24, '', '', '', true, 300);
        }
        $pdf->SetDrawColor(33, 37, 41);
        $pdf->SetLineWidth(0.4);
        $pdf->Line(110, $back_qr_y + 32, 195, $back_qr_y + 32);
        $pdf->SetFont('helvetica', 'B', 9);
        $pdf->SetXY(110, $back_qr_y + 33);
        $pdf->Cell(85, 5, 'Authorized Seal', 0, 0, 'C');

        // Save & Stream
        $pdfFilename = 'MS_' . $student->enrollment_no . '_' . time() . '.pdf';
        $dir = public_path('uploads/marksheets');
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
        $pdfPath = $dir . '/' . $pdfFilename;
        $pdf->Output($pdfPath, 'F');

        $student->update(['marksheet_pdf' => $pdfFilename]);

        return response()->file($pdfPath, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="RISE_Marksheet_' . $student->enrollment_no . '.pdf"'
        ]);
    }

    // =========================================================================
    // 5. GENERATE NTT ADMIT CARD
    // =========================================================================
    public static function generateNttAdmitCard(Student $student, int $semester = 1)
    {
        self::loadTcpdf();

        $student->loadMissing(['admin', 'program', 'course']);

        if (!empty($student->is_disabled)) {
            abort(403, 'Admit card cannot be generated for a disabled student.');
        }

        if ($semester < 1 || $semester > 4) {
            abort(400, 'Please select a semester from 1 to 4.');
        }

        $programName = $student->program ? $student->program->program_name : '';
        $courseName  = $student->course ? $student->course->course_name : '';

        $isNTT = stripos($programName, 'NTT') !== false
            || stripos($programName, 'Nursery Teachers Training') !== false
            || stripos($programName, 'Nursery Teacher Training') !== false
            || stripos($courseName, 'NTT') !== false
            || stripos($courseName, 'Nursery Teachers Training') !== false
            || stripos($courseName, 'Nursery Teacher Training') !== false;

        if (!$isNTT) {
            abort(400, 'This admit card is only available for NTT candidates.');
        }

        // Fetch subjects
        $allSubjects = Subject::where('course_id', $student->course_id)
            ->orderBy('subject_name')
            ->orderBy('id')
            ->pluck('subject_name')
            ->toArray();

        $totalSubjects = count($allSubjects);
        $subjectsPerSemester = (int)floor($totalSubjects / 4);
        $subjectOffset = $subjectsPerSemester * ($semester - 1);
        $subjectLimit = ($semester === 4)
            ? max(0, $totalSubjects - $subjectOffset)
            : $subjectsPerSemester;
        $subjects = array_slice($allSubjects, $subjectOffset, $subjectLimit);

        $semesterNames = [1 => '1st Semester', 2 => '2nd Semester', 3 => '3rd Semester', 4 => '4th Semester'];
        $semesterName = $semesterNames[$semester];

        $pdf = new class('P', 'mm', 'A4', true, 'UTF-8', false) extends TCPDF {
            public function Header() {}
            public function Footer() {
                $this->SetY(-8);
                $this->SetFont('helvetica', '', 7);
                $this->SetTextColor(100, 100, 100);
            }
        };

        $pdf->SetCreator('RISE');
        $pdf->SetAuthor('Reliable Inclusive Skill Education');
        $pdf->SetTitle('RISE NTT ' . $semesterName . ' Admit Card - ' . $student->full_name);
        $pdf->SetMargins(10, 8, 10);
        $pdf->SetAutoPageBreak(false, 8);
        $pdf->AddPage();

        $pdf->SetLineWidth(0.55);
        $pdf->SetDrawColor(35, 35, 35);
        $pdf->Rect(7, 6, 196, 285);

        // Assets
        $logoPath       = public_path('assets/images/logo.png');
        $hasLogo        = file_exists($logoPath);
        $logosPath      = public_path('assets/images/accreditation_logos.png');
        $hasLogos       = file_exists($logosPath);
        $controllerSign = public_path('assets/images/controller_signature.jpg');
        $hasCtrlSign    = file_exists($controllerSign);
        $directorSign   = public_path('assets/images/director_signature.jpg');
        $hasDirSign     = file_exists($directorSign);
        $fallbackSign   = public_path('assets/images/authorized_signature.jpg');
        $hasFallbackSign= file_exists($fallbackSign);
        $sealPath       = public_path('assets/images/seal.jpg');
        $hasSeal        = file_exists($sealPath);

        if ($hasLogo) {
            $pdf->Image($logoPath, 12, 10, 22, 22, '', '', '', true, 150, '', false, false, 0, 'CM');
        }
        if ($hasLogos) {
            $pdf->Image($logosPath, 174, 11, 23, 18, '', '', '', true, 150, '', false, false, 0, 'CM');
        }

        $pdf->SetXY(36, 10);
        $pdf->SetFont('helvetica', 'B', 13.2);
        $pdf->SetTextColor(15, 34, 76);
        $pdf->MultiCell(160, 7, 'RELIABLE INCLUSIVE SKILL EDUCATION', 0, 'C', false, 1);
        $pdf->SetX(36);
        $pdf->SetFont('helvetica', '', 7.2);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->MultiCell(160, 4.2, '(An Autonomous Institution Registered Under the Companies Act, 2013 / Sec 18, Incorporated Under Ministry of Corporate Affairs, Government of India)', 0, 'C', false, 1);
        $pdf->SetFont('helvetica', 'B', 9.5);
        $pdf->Cell(0, 5.5, 'DEPARTMENT OF NURSERY TEACHERS TRAINING (NTT)', 0, 1, 'C');
        $pdf->SetFont('helvetica', 'B', 10.5);
        $pdf->Cell(0, 6, 'ADMIT CARD / HALL TICKET', 0, 1, 'C');
        $pdf->SetFont('helvetica', 'B', 9);
        $pdf->Cell(0, 5, strtoupper($semesterName), 0, 1, 'C');

        // Candidate information panel
        $panelY = 48;
        $pdf->SetFillColor(245, 245, 245);
        $pdf->SetDrawColor(120, 120, 120);
        $pdf->Rect(11, $panelY, 188, 62, 'DF');

        $centerName   = trim((string)($student->admin ? ($student->admin->college_name ?: $student->admin->name) : ''));
        $gender       = (string)($student->gender ?? '');
        $session      = (string)($student->session_name ?? '');
        $registration = (string)($student->enrollment_no ?? '');
        $rollNo       = (string)($student->roll_no ?? '');
        $dob          = !empty($student->dob) ? date('d-m-Y', strtotime($student->dob)) : '';

        $leftFields = [
            ['Study Centre', $centerName],
            ['Student Name', (string)($student->full_name ?? '')],
            ["Father's Name", (string)($student->father_name ?? '')],
            ["Mother's Name", (string)($student->mother_name ?? '')],
            ['Date of Birth', $dob],
            ['Programme', $programName ?: 'NTT']
        ];
        $rightFields = [
            ['Session', $session],
            ['Registration No.', $registration],
            ['Roll Number', $rollNo !== '' ? $rollNo : 'Not assigned'],
            ['Sex', $gender],
            ['Semester', $semesterName],
            ['Exam Centre', $centerName]
        ];

        $y = 51;
        foreach ($leftFields as $field) {
            $pdf->SetXY(14, $y);
            $pdf->SetFont('helvetica', 'B', 6.8);
            $pdf->Cell(27, 4.5, $field[0], 0, 0, 'L');
            $pdf->Cell(2.5, 4.5, ':', 0, 0, 'C');
            $pdf->SetFont('helvetica', '', 6.8);
            $pdf->Cell(51, 4.5, $field[1] !== '' ? $field[1] : '—', 0, 1, 'L');
            $y += 8.8;
        }
        $y = 51;
        foreach ($rightFields as $field) {
            $pdf->SetXY(96, $y);
            $pdf->SetFont('helvetica', 'B', 6.8);
            $pdf->Cell(25, 4.5, $field[0], 0, 0, 'L');
            $pdf->Cell(2.5, 4.5, ':', 0, 0, 'C');
            $pdf->SetFont('helvetica', '', 6.8);
            $pdf->Cell(48, 4.5, $field[1] !== '' ? $field[1] : '—', 0, 1, 'L');
            $y += 8.8;
        }

        // Candidate photo
        $photoFile = self::resolvePhotoPath($student->photo);
        $pdf->SetDrawColor(50, 50, 50);
        $pdf->Rect(174, 52, 21, 29);
        if ($photoFile !== '') {
            $pdf->Image($photoFile, 174.5, 52.5, 20, 28, '', '', '', true, 100, '', false, false, 0, 'CM');
        } else {
            $pdf->SetFont('helvetica', '', 6.2);
            $pdf->SetXY(174, 62);
            $pdf->MultiCell(21, 5, "CANDIDATE\nPHOTO", 0, 'C');
        }

        // Subject table
        $tableY = 114;
        $pdf->SetXY(11, $tableY);
        $pdf->SetFont('helvetica', 'B', 8.5);
        $pdf->SetFillColor(255, 255, 255);
        $pdf->Cell(188, 6.5, 'SUBJECTS OF THEORY EXAMINATION — ' . strtoupper($semesterName), 1, 1, 'C', true);
        $pdf->SetFont('helvetica', 'B', 7.5);
        $pdf->SetFillColor(232, 235, 240);
        $pdf->Cell(15, 6, 'PAPER', 1, 0, 'C', true);
        $pdf->Cell(78, 6, 'SUBJECT', 1, 0, 'C', true);
        $pdf->Cell(43, 6, 'EXAM DATE', 1, 0, 'C', true);
        $pdf->Cell(52, 6, 'TIME / SITTING', 1, 1, 'C', true);
        $pdf->SetFont('helvetica', '', 7.2);

        if (empty($subjects)) {
            $pdf->Cell(15, 7, '—', 1, 0, 'C');
            $pdf->Cell(78, 7, 'No subjects are mapped to this student course.', 1, 0, 'L');
            $pdf->Cell(43, 7, 'To be announced', 1, 0, 'C');
            $pdf->Cell(52, 7, 'To be announced', 1, 1, 'C');
        } else {
            foreach ($subjects as $index => $subjectName) {
                $paperNumber = $subjectOffset + $index + 1;
                $pdf->Cell(15, 7, (string)$paperNumber, 1, 0, 'C');
                $pdf->Cell(78, 7, (string)$subjectName, 1, 0, 'L');
                $pdf->Cell(43, 7, 'To be announced', 1, 0, 'C');
                $pdf->Cell(52, 7, 'To be announced', 1, 1, 'C');
            }
        }

        // Signatures
        $controllerSignature = $hasCtrlSign ? $controllerSign : ($hasFallbackSign ? $fallbackSign : '');
        $directorSignature   = $hasDirSign ? $directorSign : ($hasFallbackSign ? $fallbackSign : '');

        $signY = 170;
        if ($directorSignature !== '') {
            $pdf->Image($directorSignature, 24, $signY, 34, 12, '', '', '', true, 120, '', false, false, 0, 'CM');
        }
        if ($hasSeal) {
            $pdf->Image($sealPath, 84, $signY - 1, 18, 18, '', '', '', true, 120, '', false, false, 0, 'CM');
        }
        if ($controllerSignature !== '') {
            $pdf->Image($controllerSignature, 145, $signY, 34, 12, '', '', '', true, 120, '', false, false, 0, 'CM');
        }

        $pdf->SetFont('helvetica', 'B', 7.5);
        $pdf->SetXY(12, 183);
        $pdf->Cell(65, 5, 'Director / Authorized Signature', 0, 0, 'C');
        $pdf->SetXY(78, 183);
        $pdf->Cell(30, 5, 'Official Seal', 0, 0, 'C');
        $pdf->SetXY(128, 183);
        $pdf->Cell(65, 5, 'Examination Controller', 0, 1, 'C');
        $pdf->SetDrawColor(120, 120, 120);
        $pdf->Line(11, 190, 199, 190);
        $pdf->SetXY(11, 192);
        $pdf->SetFont('helvetica', 'B', 8.2);
        $pdf->Cell(188, 5, 'INSTRUCTIONS FOR CANDIDATES', 0, 1, 'C');
        $pdf->SetFont('helvetica', '', 7.1);

        $instructions = [
            '1. Bring this admit card and a valid photo identity card to the examination centre.',
            '2. Reach the examination centre at least 30 minutes before the scheduled time.',
            '3. Verify your name, photograph, registration number and other details before the examination.',
            '4. Report any error in the admit card to your study centre before the examination.',
            '5. Carry only permitted stationery and examination materials into the hall.',
            '6. Mobile phones, smart watches, calculators (unless permitted), notes and electronic devices are prohibited.',
            '7. Do not use unfair means, communicate with other candidates, or disturb the examination.',
            '8. Follow all instructions given by the invigilator and remain seated until permitted to leave.',
            '9. Do not damage, alter or write anything on this admit card except where instructed.',
            '10. Examination dates and timings will be announced by the institution; confirm the schedule with your study centre.'
        ];

        foreach ($instructions as $instruction) {
            $pdf->MultiCell(188, 4.2, $instruction, 0, 'L', false, 1, 11, $pdf->GetY());
            $pdf->Ln(2.2);
        }

        $safeReg = preg_replace('/[^A-Za-z0-9_-]/', '_', $registration ?: (string)$student->id);

        return response($pdf->Output('', 'S'), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="NTT_Semester_' . $semester . '_Admit_Card_' . $safeReg . '.pdf"'
        ]);
    }
}

