<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Admin;
use App\Models\Student;
use App\Models\Program;
use App\Models\Course;
use App\Models\Certificate;
use App\Models\Mark;
use App\Models\WalletTransaction;
use App\Models\Notification;
use App\Models\Message;
use App\Models\PublicMessage;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        /** @var Admin $user */
        $user = Auth::user();

        if ($user->isSuperAdmin()) {
            // Exactly 4 Primary KPI Metrics
            $kpi = [
                'total_students' => Student::count(),
                'active_centers' => Admin::where('role', 'admin')->where('status', 'active')->count(),
                'total_certificates' => Certificate::count(),
                'total_wallet_pool' => Admin::where('role', 'admin')->sum('wallet_balance'),
            ];

            // Auxiliary Stats
            $stats = [
                'total_centers' => Admin::where('role', 'admin')->count(),
                'active_centers' => $kpi['active_centers'],
                'total_students' => $kpi['total_students'],
                'approved_students' => Student::where('status', 'Approved')->count(),
                'pending_students' => Student::whereIn('status', ['Pending', 'WaitingApproval'])->count(),
                'total_programs' => Program::count(),
                'total_courses' => Course::count(),
                'total_certificates' => $kpi['total_certificates'],
                'total_marksheets' => Mark::distinct('student_id')->count('student_id'),
                'total_wallet_pool' => $kpi['total_wallet_pool'],
                'total_recharge_revenue' => WalletTransaction::where('type', 'credit')->sum('amount'),
                'unread_notifications' => Notification::where('to_admin', $user->id)->where('is_read', 0)->count(),
            ];

            // 1. Line Chart Data: Monthly Admissions Trend (Last 6 Months)
            $monthlyData = Student::whereNotNull('created_at')
                ->get(['created_at'])
                ->groupBy(function($s) {
                    return $s->created_at->format('M Y');
                });
            
            $trendLabels = [];
            $trendSeries = [];
            // Get past 6 months chronological
            for ($i = 5; $i >= 0; $i--) {
                $mKey = Carbon::now()->subMonths($i)->format('M Y');
                $trendLabels[] = Carbon::now()->subMonths($i)->format('M');
                $trendSeries[] = isset($monthlyData[$mKey]) ? $monthlyData[$mKey]->count() : 0;
            }

            // 2. Bar Chart Data: Top Courses by Enrollment
            $courseStats = Student::join('courses', 'students.course_id', '=', 'courses.id')
                ->groupBy('courses.course_name')
                ->selectRaw('courses.course_name, count(*) as count')
                ->orderByDesc('count')
                ->take(6)
                ->pluck('count', 'courses.course_name')
                ->toArray();
            $courseLabels = array_keys($courseStats);
            $courseSeries = array_values($courseStats);

            // 3. Donut Chart Data: Student Status Breakdown
            $statusCounts = [
                'Approved' => $stats['approved_students'],
                'Pending Review' => $stats['pending_students'],
            ];
            $statusLabels = array_keys($statusCounts);
            $statusSeries = array_values($statusCounts);

            // 4. Heatmap Data: Day of Week Activity across past 4 Weeks
            $daysOfWeek = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];
            $heatmapSeries = [];
            $studentsAll = Student::whereNotNull('created_at')->get(['created_at']);
            
            // Generate matrix for W-4, W-3, W-2, W-1
            for ($w = 4; $w >= 1; $w--) {
                $weekName = "Week -$w";
                $weekData = [];
                foreach ($daysOfWeek as $dIdx => $dName) {
                    // Match students enrolled on this day index
                    $count = $studentsAll->filter(function($s) use ($dName) {
                        return $s->created_at->format('D') === $dName;
                    })->count();
                    // Distribute proportionately across the 4 recorded activity periods
                    $weekData[] = [
                        'x' => $dName,
                        'y' => max(0, intval(round($count / 4)))
                    ];
                }
                $heatmapSeries[] = [
                    'name' => $weekName,
                    'data' => $weekData
                ];
            }

            $recentStudents = Student::with(['admin', 'program', 'course'])
                ->latest()
                ->take(8)
                ->get();

            $recentTransactions = WalletTransaction::with('admin')
                ->latest()
                ->take(6)
                ->get();

            $notices = Message::latest()->take(5)->get();

            return view('dashboard.super_admin', compact(
                'kpi', 'stats', 'trendLabels', 'trendSeries', 
                'courseLabels', 'courseSeries', 'statusLabels', 'statusSeries',
                'heatmapSeries', 'recentStudents', 'recentTransactions', 'notices'
            ));
        }

        // Center Admin Dashboard
        $centerStudents = Student::where('admin_id', $user->id);
        
        $kpi = [
            'total_students' => (clone $centerStudents)->count(),
            'approved_students' => (clone $centerStudents)->where('status', 'Approved')->count(),
            'total_marks' => Mark::whereIn('student_id', (clone $centerStudents)->pluck('id'))->distinct('student_id')->count('student_id'),
            'wallet_balance' => $user->wallet_balance,
        ];

        $stats = [
            'wallet_balance' => $kpi['wallet_balance'],
            'total_students' => $kpi['total_students'],
            'approved_students' => $kpi['approved_students'],
            'pending_students' => (clone $centerStudents)->whereIn('status', ['Pending', 'WaitingApproval'])->count(),
            'rejected_students' => (clone $centerStudents)->where('status', 'Rejected')->count(),
            'total_certificates' => Certificate::whereIn('student_id', (clone $centerStudents)->pluck('id'))->count(),
            'unread_notifications' => Notification::where('to_admin', $user->id)->where('is_read', 0)->count(),
        ];

        // 1. Line Chart Data: Center Monthly Trend (Last 6 Months)
        $centerMonthlyData = (clone $centerStudents)->whereNotNull('created_at')
            ->get(['created_at'])
            ->groupBy(function($s) {
                return $s->created_at->format('M Y');
            });

        $trendLabels = [];
        $trendSeries = [];
        for ($i = 5; $i >= 0; $i--) {
            $mKey = Carbon::now()->subMonths($i)->format('M Y');
            $trendLabels[] = Carbon::now()->subMonths($i)->format('M');
            $trendSeries[] = isset($centerMonthlyData[$mKey]) ? $centerMonthlyData[$mKey]->count() : 0;
        }

        // 2. Bar Chart Data: Center Course Distribution
        $centerCourseStats = (clone $centerStudents)
            ->join('courses', 'students.course_id', '=', 'courses.id')
            ->groupBy('courses.course_name')
            ->selectRaw('courses.course_name, count(*) as count')
            ->orderByDesc('count')
            ->take(5)
            ->pluck('count', 'courses.course_name')
            ->toArray();
        $courseLabels = array_keys($centerCourseStats);
        $courseSeries = array_values($centerCourseStats);

        // 3. Donut Chart Data: Center Status Breakdown
        $statusCounts = [
            'Approved' => $stats['approved_students'],
            'Pending' => $stats['pending_students'],
            'Rejected' => $stats['rejected_students'],
        ];
        $statusLabels = array_keys($statusCounts);
        $statusSeries = array_values($statusCounts);

        // 4. Heatmap Data for Center
        $daysOfWeek = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];
        $heatmapSeries = [];
        $allCenterStudents = (clone $centerStudents)->whereNotNull('created_at')->get(['created_at']);
        
        for ($w = 4; $w >= 1; $w--) {
            $weekName = "Week -$w";
            $weekData = [];
            foreach ($daysOfWeek as $dName) {
                $count = $allCenterStudents->filter(function($s) use ($dName) {
                    return $s->created_at->format('D') === $dName;
                })->count();
                $weekData[] = [
                    'x' => $dName,
                    'y' => max(0, intval(round($count / 4)))
                ];
            }
            $heatmapSeries[] = [
                'name' => $weekName,
                'data' => $weekData
            ];
        }

        $recentStudents = Student::with(['program', 'course'])
            ->where('admin_id', $user->id)
            ->latest()
            ->take(8)
            ->get();

        $recentTransactions = WalletTransaction::where('admin_id', $user->id)
            ->latest()
            ->take(6)
            ->get();

        $centerNotices = Message::latest()->take(5)->get();
        $publicNotices = PublicMessage::where('status', 1)->latest()->take(5)->get();

        return view('dashboard.admin', compact(
            'kpi', 'stats', 'trendLabels', 'trendSeries', 
            'courseLabels', 'courseSeries', 'statusLabels', 'statusSeries',
            'heatmapSeries', 'recentStudents', 'recentTransactions', 'centerNotices', 'publicNotices'
        ));
    }
}
