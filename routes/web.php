<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\AdminManagementController;
use App\Http\Controllers\AcademicController;
use App\Http\Controllers\MarksController;
use App\Http\Controllers\CertificateController;
use App\Http\Controllers\WalletController;
use App\Http\Controllers\JobController;
use App\Http\Controllers\PlacementStudentController;
use App\Http\Controllers\NoticeNotificationController;
use App\Http\Controllers\VerificationController;
use App\Http\Controllers\ProfileController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Redirect root to dashboard or login
Route::get('/', function () {
    return redirect()->route('login');
});

// Authentication
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Public Verifications & Portal
Route::get('/verify-student', [VerificationController::class, 'verifyStudent'])->name('verify.student');
Route::get('/verify-certificate', [VerificationController::class, 'verifyCertificate'])->name('verify.certificate');
Route::get('/careers', [JobController::class, 'frontend'])->name('jobs.frontend');
Route::post('/jobs/{id}/apply', [JobController::class, 'apply'])->name('jobs.apply');

// Protected Routes
Route::middleware(['auth', 'active'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Students
    Route::prefix('students')->name('students.')->group(function () {
        Route::get('/', [StudentController::class, 'index'])->name('index');
        Route::get('/create', [StudentController::class, 'create'])->name('create');
        Route::post('/', [StudentController::class, 'store'])->name('store');
        Route::get('/{id}', [StudentController::class, 'show'])->name('show');
        Route::get('/{id}/edit', [StudentController::class, 'edit'])->name('edit');
        Route::put('/{id}', [StudentController::class, 'update'])->name('update');
        Route::delete('/{id}', [StudentController::class, 'destroy'])->name('destroy');
        Route::post('/{id}/approve', [StudentController::class, 'approve'])->name('approve');
        Route::post('/{id}/transfer', [StudentController::class, 'transfer'])->name('transfer');
        Route::get('/ajax/courses/{programId}', [StudentController::class, 'ajaxCourses'])->name('ajax.courses');
        Route::get('/ajax/subjects/{courseId}', [StudentController::class, 'ajaxSubjects'])->name('ajax.subjects');
    });

    // Marks Entry & Marksheets
    Route::prefix('marks')->name('marks.')->group(function () {
        Route::get('/', [MarksController::class, 'index'])->name('index');
        Route::post('/', [MarksController::class, 'store'])->name('store');
        Route::get('/marksheet/{studentId}', [MarksController::class, 'marksheet'])->name('marksheet');
        Route::get('/superadmin/search', [MarksController::class, 'superadminMarksheet'])->name('superadmin_marksheet');
    });

    // Certificates & ID Cards
    Route::prefix('certificates')->name('certificates.')->group(function () {
        Route::get('/id-card/{studentId}', [CertificateController::class, 'idCard'])->name('id_card');
        Route::get('/student/{studentId}', [CertificateController::class, 'generateCertificate'])->name('student_certificate');
        Route::get('/authorization/{adminId?}', [CertificateController::class, 'adminCertificate'])->name('admin_certificate');
        Route::get('/authorized-centers', [CertificateController::class, 'authorizedCertificates'])->name('authorized_list');
        Route::get('/ntt-admit-card/{studentId}', [CertificateController::class, 'nttAdmitCard'])->name('ntt_admit_card');
    });

    // Wallet & Transactions
    Route::prefix('wallet')->name('wallet.')->group(function () {
        Route::get('/', [WalletController::class, 'index'])->name('index');
        Route::get('/recharge', [WalletController::class, 'recharge'])->name('recharge');
        Route::post('/create-order', [WalletController::class, 'createOrder'])->name('create_order');
        Route::post('/verify-payment', [WalletController::class, 'verifyPayment'])->name('verify_payment');
        Route::get('/failed-transactions', [WalletController::class, 'failedTransactions'])->name('failed_transactions');
        Route::post('/failed-transactions', [WalletController::class, 'storeFailedTransaction'])->name('store_failed_transaction');
        Route::get('/failed-requests', [WalletController::class, 'failedTransactionRequests'])->name('failed_requests');
        Route::post('/failed-requests/{id}/review', [WalletController::class, 'reviewFailedTransaction'])->name('review_failed_transaction');
    });

    // Jobs
    Route::prefix('jobs')->name('jobs.')->group(function () {
        Route::get('/', [JobController::class, 'index'])->name('index');
        Route::post('/', [JobController::class, 'store'])->name('store');
        Route::get('/{id}', [JobController::class, 'show'])->name('show');
        Route::post('/{id}/toggle-publish', [JobController::class, 'togglePublish'])->name('toggle_publish');
        Route::delete('/{id}', [JobController::class, 'delete'])->name('delete');
    });

    // Placements
    Route::prefix('placements')->name('placements.')->group(function () {
        Route::get('/', [PlacementStudentController::class, 'index'])->name('index');
        Route::post('/', [PlacementStudentController::class, 'store'])->name('store');
        Route::delete('/{id}', [PlacementStudentController::class, 'destroy'])->name('destroy');
    });

    // Notifications & Notices
    Route::prefix('notices')->name('notices.')->group(function () {
        Route::get('/notifications', [NoticeNotificationController::class, 'notifications'])->name('notifications');
        Route::get('/center', [NoticeNotificationController::class, 'centerNotices'])->name('center_notices');
        Route::post('/center', [NoticeNotificationController::class, 'storeCenterNotice'])->name('center_notices.store');
        Route::delete('/center/{id}', [NoticeNotificationController::class, 'deleteCenterNotice'])->name('center_notices.delete');
        Route::get('/public', [NoticeNotificationController::class, 'publicNotices'])->name('public_notices');
        Route::post('/public', [NoticeNotificationController::class, 'storePublicNotice'])->name('public_notices.store');
        Route::post('/public/{id}/toggle', [NoticeNotificationController::class, 'togglePublicNotice'])->name('public_notices.toggle');
        Route::delete('/public/{id}', [NoticeNotificationController::class, 'deletePublicNotice'])->name('public_notices.delete');
    });

    // Super Admin Management & Academic Routes
    Route::middleware('super_admin')->group(function () {
        // Admin / Center Management
        Route::prefix('admin-management')->name('admin_management.')->group(function () {
            Route::get('/', [AdminManagementController::class, 'index'])->name('index');
            Route::get('/create', [AdminManagementController::class, 'create'])->name('create');
            Route::post('/', [AdminManagementController::class, 'store'])->name('store');
            Route::get('/{id}', [AdminManagementController::class, 'show'])->name('show');
            Route::get('/{id}/edit', [AdminManagementController::class, 'edit'])->name('edit');
            Route::put('/{id}', [AdminManagementController::class, 'update'])->name('update');
            Route::post('/{id}/toggle-status', [AdminManagementController::class, 'toggleStatus'])->name('toggle_status');
            Route::post('/{id}/adjust-wallet', [AdminManagementController::class, 'adjustWallet'])->name('adjust_wallet');
        });

        // Academic Programs, Courses, Subjects
        Route::prefix('academic')->name('academic.')->group(function () {
            Route::get('/programs', [AcademicController::class, 'programs'])->name('programs');
            Route::post('/programs', [AcademicController::class, 'storeProgram'])->name('programs.store');
            Route::put('/programs/{id}', [AcademicController::class, 'updateProgram'])->name('programs.update');
            Route::delete('/programs/{id}', [AcademicController::class, 'deleteProgram'])->name('programs.delete');

            Route::get('/courses', [AcademicController::class, 'courses'])->name('courses');
            Route::post('/courses', [AcademicController::class, 'storeCourse'])->name('courses.store');
            Route::put('/courses/{id}', [AcademicController::class, 'updateCourse'])->name('courses.update');
            Route::delete('/courses/{id}', [AcademicController::class, 'deleteCourse'])->name('courses.delete');

            Route::get('/subjects', [AcademicController::class, 'subjects'])->name('subjects');
            Route::post('/subjects', [AcademicController::class, 'storeSubject'])->name('subjects.store');
            Route::put('/subjects/{id}', [AcademicController::class, 'updateSubject'])->name('subjects.update');
            Route::delete('/subjects/{id}', [AcademicController::class, 'deleteSubject'])->name('subjects.delete');
        });
    });

    // Profile & Password
    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('/', [ProfileController::class, 'profile'])->name('show');
        Route::post('/', [ProfileController::class, 'updateProfile'])->name('update');
        Route::get('/change-password', [ProfileController::class, 'changePassword'])->name('change_password');
        Route::post('/change-password', [ProfileController::class, 'updatePassword'])->name('change_password.update');
    });
});
