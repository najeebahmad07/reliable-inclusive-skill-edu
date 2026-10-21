@extends('layouts.app')

@section('title', 'Center Dashboard')
@section('breadcrumb', 'Center Overview')

@section('content')
<div class="space-y-6">

    <!-- Center Greeting Banner -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 bg-white dark:bg-[#1F2937] p-5 rounded-2xl border border-[#D9E0EE] dark:border-[#374151] shadow-xs">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-[#172033] dark:text-[#F9FAFB]">{{ Auth::user()->institute_name ?? Auth::user()->name }}</h1>
            <p class="text-sm font-medium text-[#374151] dark:text-[#D1D5DB] mt-0.5">
                Center Code: <span class="font-mono font-bold text-[#172033] dark:text-[#F9FAFB]">RISE-{{ str_pad(Auth::id(), 4, '0', STR_PAD_LEFT) }}</span> &bull; {{ Auth::user()->city ?? 'Authorized Training Center' }}
            </p>
        </div>
        <div class="flex flex-wrap items-center gap-2.5">
            <a href="{{ route('students.create') }}" class="px-3.5 py-2 bg-[#4361EE] hover:bg-[#3730A3] text-white rounded-xl text-sm font-semibold flex items-center space-x-2 shadow-xs transition">
                <i class="fa-solid fa-user-plus text-xs"></i>
                <span>Enroll Student</span>
            </a>
            <a href="{{ route('wallet.recharge') }}" class="px-3.5 py-2 bg-[#059669] hover:bg-[#047857] text-white rounded-xl text-sm font-semibold flex items-center space-x-2 shadow-xs transition">
                <i class="fa-solid fa-bolt text-xs"></i>
                <span>Recharge Wallet</span>
            </a>
            <a href="{{ route('certificates.admin_certificate') }}" target="_blank" class="px-3.5 py-2 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-[#172033] dark:text-[#F9FAFB] rounded-xl text-sm font-semibold transition flex items-center space-x-1.5">
                <i class="fa-solid fa-certificate text-xs text-[#7C3AED]"></i>
                <span>Authorization</span>
            </a>
        </div>
    </div>

    <!-- Notices Carousel/Alert if present -->
    @if($centerNotices->isNotEmpty())
    <div class="p-4 rounded-2xl bg-amber-50 dark:bg-amber-950/40 border border-amber-200 dark:border-amber-800/80 text-amber-900 dark:text-amber-200 text-sm flex items-start space-x-3 shadow-xs">
        <div class="mt-0.5 text-amber-600 dark:text-amber-400"><i class="fa-solid fa-bullhorn text-base"></i></div>
        <div class="flex-1">
            <span class="font-bold text-xs uppercase tracking-wider text-amber-800 dark:text-amber-300 block">Notice from Head Office</span>
            <p class="text-sm mt-0.5 leading-relaxed">{{ $centerNotices->first()->message }}</p>
        </div>
    </div>
    @endif

    <!-- 4 COLORFUL SUMMARY CARDS (EXACTLY FOUR KPI METRICS) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        
        <!-- CARD 1: BLUE (Student-Related Metric) -->
        <div class="bg-gradient-to-br from-blue-500/10 via-white to-blue-50/50 dark:from-blue-950/40 dark:via-[#1F2937] dark:to-blue-900/20 p-5 rounded-2xl border border-blue-200/80 dark:border-blue-900/50 shadow-xs hover:shadow-md transition-all flex flex-col justify-between">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-blue-700 dark:text-blue-400 uppercase tracking-wider">Total Enrolled</span>
                <div class="w-10 h-10 rounded-xl bg-[#4361EE] text-white flex items-center justify-center text-base shadow-sm">
                    <i class="fa-solid fa-user-graduate"></i>
                </div>
            </div>
            <div class="mt-3">
                <div class="text-3xl font-extrabold text-[#172033] dark:text-[#F9FAFB] tracking-tight font-mono">
                    {{ number_format($kpi['total_students']) }}
                </div>
                <div class="flex items-center space-x-1.5 mt-2 text-xs font-semibold text-blue-700 dark:text-blue-300">
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full bg-blue-100 dark:bg-blue-900/60 font-medium">
                        Center Admissions
                    </span>
                    <span class="text-[#374151] dark:text-[#D1D5DB] font-normal">&bull; Active Students</span>
                </div>
            </div>
        </div>

        <!-- CARD 2: GREEN (Approved Admissions Metric) -->
        <div class="bg-gradient-to-br from-emerald-500/10 via-white to-emerald-50/50 dark:from-emerald-950/40 dark:via-[#1F2937] dark:to-emerald-900/20 p-5 rounded-2xl border border-emerald-200/80 dark:border-emerald-900/50 shadow-xs hover:shadow-md transition-all flex flex-col justify-between">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-emerald-700 dark:text-emerald-400 uppercase tracking-wider">Approved Admissions</span>
                <div class="w-10 h-10 rounded-xl bg-[#059669] text-white flex items-center justify-center text-base shadow-sm">
                    <i class="fa-solid fa-circle-check"></i>
                </div>
            </div>
            <div class="mt-3">
                <div class="text-3xl font-extrabold text-[#172033] dark:text-[#F9FAFB] tracking-tight font-mono">
                    {{ number_format($kpi['approved_students']) }}
                </div>
                <div class="flex items-center space-x-1.5 mt-2 text-xs font-semibold text-emerald-700 dark:text-emerald-300">
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full bg-emerald-100 dark:bg-emerald-900/60 font-medium">
                        Verified & Active
                    </span>
                    <span class="text-[#374151] dark:text-[#D1D5DB] font-normal">&bull; {{ $stats['pending_students'] }} Pending</span>
                </div>
            </div>
        </div>

        <!-- CARD 3: PINK (Certificate & Marksheet Metric) -->
        <div class="bg-gradient-to-br from-pink-500/10 via-white to-pink-50/50 dark:from-pink-950/40 dark:via-[#1F2937] dark:to-pink-900/20 p-5 rounded-2xl border border-pink-200/80 dark:border-pink-900/50 shadow-xs hover:shadow-md transition-all flex flex-col justify-between">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-pink-700 dark:text-pink-400 uppercase tracking-wider">Exam Marks Evaluated</span>
                <div class="w-10 h-10 rounded-xl bg-[#E64980] text-white flex items-center justify-center text-base shadow-sm">
                    <i class="fa-solid fa-file-invoice"></i>
                </div>
            </div>
            <div class="mt-3">
                <div class="text-3xl font-extrabold text-[#172033] dark:text-[#F9FAFB] tracking-tight font-mono">
                    {{ number_format($kpi['total_marks']) }}
                </div>
                <div class="flex items-center space-x-1.5 mt-2 text-xs font-semibold text-pink-700 dark:text-pink-300">
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full bg-pink-100 dark:bg-pink-900/60 font-medium">
                        {{ $stats['total_certificates'] }} Certs Issued
                    </span>
                    <span class="text-[#374151] dark:text-[#D1D5DB] font-normal">&bull; Graded</span>
                </div>
            </div>
        </div>

        <!-- CARD 4: VIOLET (Financial / Operational Metric) -->
        <div class="bg-gradient-to-br from-purple-500/10 via-white to-purple-50/50 dark:from-purple-950/40 dark:via-[#1F2937] dark:to-purple-900/20 p-5 rounded-2xl border border-purple-200/80 dark:border-purple-900/50 shadow-xs hover:shadow-md transition-all flex flex-col justify-between">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-purple-700 dark:text-purple-400 uppercase tracking-wider">Available Wallet</span>
                <div class="w-10 h-10 rounded-xl bg-[#7C3AED] text-white flex items-center justify-center text-base shadow-sm">
                    <i class="fa-solid fa-wallet"></i>
                </div>
            </div>
            <div class="mt-3">
                <div class="text-3xl font-extrabold text-[#172033] dark:text-[#F9FAFB] tracking-tight font-mono">
                    ₹{{ number_format($kpi['wallet_balance'], 2) }}
                </div>
                <div class="flex items-center justify-between mt-2 text-xs font-semibold">
                    <span class="text-[#374151] dark:text-[#D1D5DB] font-normal">Ready for approvals</span>
                    <a href="{{ route('wallet.recharge') }}" class="text-purple-700 dark:text-purple-300 hover:underline font-bold">
                        + Add Funds &rarr;
                    </a>
                </div>
            </div>
        </div>

    </div>

    <!-- ANALYTICS SECTION: CHARTS, GRAPHS & HEATMAPS -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- Line Chart: Admissions Trend over Time (2 Cols) -->
        <div class="lg:col-span-2 bg-white dark:bg-[#1F2937] p-5 rounded-2xl border border-[#D9E0EE] dark:border-[#374151] shadow-xs flex flex-col justify-between">
            <div class="flex items-center justify-between mb-2">
                <div>
                    <h2 class="text-base font-bold text-[#172033] dark:text-[#F9FAFB]">Admissions Trend</h2>
                    <p class="text-xs font-medium text-[#374151] dark:text-[#D1D5DB]">Monthly student registrations at your center</p>
                </div>
                <span class="px-2.5 py-1 rounded-lg text-xs font-bold bg-blue-50 dark:bg-blue-950/50 text-[#4361EE] border border-blue-200 dark:border-blue-900">
                    Last 6 Months
                </span>
            </div>
            <div id="chartCenterTrend" class="w-full h-[280px]"></div>
        </div>

        <!-- Donut Chart: Student Status Breakdown (1 Col) -->
        <div class="bg-white dark:bg-[#1F2937] p-5 rounded-2xl border border-[#D9E0EE] dark:border-[#374151] shadow-xs flex flex-col justify-between">
            <div class="flex items-center justify-between mb-2">
                <div>
                    <h2 class="text-base font-bold text-[#172033] dark:text-[#F9FAFB]">Admission Status</h2>
                    <p class="text-xs font-medium text-[#374151] dark:text-[#D1D5DB]">Approval breakdown for enrolled students</p>
                </div>
            </div>
            <div id="chartCenterStatusDonut" class="w-full flex items-center justify-center h-[280px]"></div>
        </div>

        <!-- Bar Chart: Enrolled Courses Distribution (1.5 Cols) -->
        <div class="lg:col-span-2 bg-white dark:bg-[#1F2937] p-5 rounded-2xl border border-[#D9E0EE] dark:border-[#374151] shadow-xs flex flex-col justify-between">
            <div class="flex items-center justify-between mb-2">
                <div>
                    <h2 class="text-base font-bold text-[#172033] dark:text-[#F9FAFB]">Students by Course</h2>
                    <p class="text-xs font-medium text-[#374151] dark:text-[#D1D5DB]">Batch distribution across your registered courses</p>
                </div>
                <span class="px-2.5 py-1 rounded-lg text-xs font-bold bg-emerald-50 dark:bg-emerald-950/50 text-[#059669] border border-emerald-200 dark:border-emerald-900">
                    Courses Distribution
                </span>
            </div>
            <div id="chartCenterCoursesBar" class="w-full h-[280px]"></div>
        </div>

        <!-- Heatmap: Center Activity Heatmap (1 Col) -->
        <div class="bg-white dark:bg-[#1F2937] p-5 rounded-2xl border border-[#D9E0EE] dark:border-[#374151] shadow-xs flex flex-col justify-between">
            <div class="flex items-center justify-between mb-2">
                <div>
                    <h2 class="text-base font-bold text-[#172033] dark:text-[#F9FAFB]">Weekly Activity Heatmap</h2>
                    <p class="text-xs font-medium text-[#374151] dark:text-[#D1D5DB]">Admissions activity intensity by day</p>
                </div>
            </div>
            <div id="chartCenterHeatmap" class="w-full h-[280px]"></div>
        </div>

    </div>

    <!-- MAIN 2-COLUMN ACTIVITY CONTENT -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- Recent Students Table (2 Columns) -->
        <div class="lg:col-span-2 bg-white dark:bg-[#1F2937] rounded-2xl border border-[#D9E0EE] dark:border-[#374151] overflow-hidden shadow-xs">
            <div class="px-5 py-4 border-b border-[#D9E0EE] dark:border-[#374151] flex items-center justify-between bg-slate-50/75 dark:bg-[#182235]/60">
                <div class="flex items-center space-x-2.5">
                    <i class="fa-solid fa-user-graduate text-[#4361EE] text-sm"></i>
                    <h2 class="text-sm font-bold text-[#172033] dark:text-[#F9FAFB]">Recent Enrolled Students</h2>
                </div>
                <a href="{{ route('students.index') }}" class="text-xs text-[#4361EE] hover:underline font-bold">View All &rarr;</a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse table-saas">
                    <thead>
                        <tr class="bg-slate-50/50 dark:bg-[#182235]/40 text-[#374151] dark:text-[#D1D5DB] border-b border-[#D9E0EE] dark:border-[#374151]">
                            <th>Student</th>
                            <th>Enrollment</th>
                            <th>Course</th>
                            <th>Status</th>
                            <th class="text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                        @forelse($recentStudents as $student)
                        <tr class="hover:bg-slate-50/75 dark:hover:bg-slate-800/40 transition">
                            <td>
                                <a href="{{ route('students.show', $student->id) }}" class="font-bold text-[#172033] dark:text-[#F9FAFB] hover:text-[#4361EE] text-sm block">
                                    {{ $student->full_name }}
                                </a>
                                <div class="text-xs text-[#374151] dark:text-[#D1D5DB]">{{ $student->mobile }}</div>
                            </td>
                            <td>
                                <div class="font-mono text-xs font-bold text-[#172033] dark:text-[#F9FAFB]">{{ $student->roll_no }}</div>
                                <div class="font-mono text-[11px] text-[#374151] dark:text-[#D1D5DB]">{{ $student->enrollment_no }}</div>
                            </td>
                            <td class="text-sm text-[#374151] dark:text-[#D1D5DB] truncate max-w-[170px]">
                                {{ $student->course->course_name ?? 'N/A' }}
                            </td>
                            <td>
                                @if($student->status === 'Approved')
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-emerald-50 dark:bg-emerald-950/40 text-[#059669] dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800">
                                    Approved
                                </span>
                                @else
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-amber-50 dark:bg-amber-950/40 text-[#D97706] dark:text-amber-400 border border-amber-200 dark:border-amber-800">
                                    {{ $student->status }}
                                </span>
                                @endif
                            </td>
                            <td class="text-right space-x-1.5 whitespace-nowrap">
                                <a href="{{ route('students.show', $student->id) }}" class="p-1.5 text-slate-500 hover:text-[#4361EE] dark:text-slate-400 dark:hover:text-[#4361EE] text-sm" title="Profile">
                                    <i class="fa-regular fa-eye"></i>
                                </a>
                                <a href="{{ route('certificates.id_card', $student->id) }}" target="_blank" class="p-1.5 text-slate-500 hover:text-purple-600 dark:text-slate-400 dark:hover:text-purple-400 text-sm" title="ID Card">
                                    <i class="fa-solid fa-id-card"></i>
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="p-8 text-center text-[#374151] dark:text-[#D1D5DB] text-sm">No students registered yet.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Recent Transactions Log (1 Column) -->
        <div class="bg-white dark:bg-[#1F2937] rounded-2xl border border-[#D9E0EE] dark:border-[#374151] overflow-hidden shadow-xs flex flex-col justify-between">
            <div>
                <div class="px-5 py-4 border-b border-[#D9E0EE] dark:border-[#374151] flex items-center justify-between bg-slate-50/75 dark:bg-[#182235]/60">
                    <div class="flex items-center space-x-2.5">
                        <i class="fa-solid fa-clock-rotate-left text-[#059669] text-sm"></i>
                        <h2 class="text-sm font-bold text-[#172033] dark:text-[#F9FAFB]">Recent Wallet Activity</h2>
                    </div>
                    <a href="{{ route('wallet.index') }}" class="text-xs text-[#4361EE] hover:underline font-bold">Details &rarr;</a>
                </div>

                <div class="divide-y divide-slate-100 dark:divide-slate-800">
                    @forelse($recentTransactions as $tx)
                    <div class="p-4 flex items-center justify-between text-sm">
                        <div class="min-w-0 pr-3">
                            <div class="font-semibold text-[#172033] dark:text-[#F9FAFB] truncate text-xs">{{ $tx->description }}</div>
                            <div class="text-[11px] text-[#374151] dark:text-[#D1D5DB] font-mono mt-0.5">
                                {{ $tx->created_at ? $tx->created_at->format('d M, h:i A') : 'N/A' }}
                            </div>
                        </div>
                        <div class="text-right flex-shrink-0">
                            <span class="font-mono font-bold text-xs {{ $tx->type === 'credit' ? 'text-[#059669]' : 'text-[#E64980]' }}">
                                {{ $tx->type === 'credit' ? '+' : '-' }}₹{{ number_format($tx->amount, 2) }}
                            </span>
                        </div>
                    </div>
                    @empty
                    <div class="p-8 text-center text-[#374151] dark:text-[#D1D5DB] text-sm">No recent transactions.</div>
                    @endforelse
                </div>
            </div>

            <div class="p-4 border-t border-[#D9E0EE] dark:border-[#374151] bg-slate-50/50 dark:bg-[#182235]/40 text-center">
                <a href="{{ route('wallet.recharge') }}" class="w-full inline-block py-2 px-3 bg-[#059669] hover:bg-[#047857] text-white rounded-xl text-xs font-bold transition shadow-xs">
                    + Instant Recharge
                </a>
            </div>
        </div>

    </div>

</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const isDark = document.documentElement.classList.contains('dark');
        const textColor = isDark ? '#D1D5DB' : '#374151';
        const gridColor = isDark ? '#374151' : '#E5E7EB';

        // 1. Line Chart: Center Admissions Trend
        const optionsLine = {
            series: [{
                name: 'Admissions',
                data: {!! json_encode($trendSeries) !!}
            }],
            chart: {
                type: 'area',
                height: 270,
                toolbar: { show: false },
                background: 'transparent'
            },
            colors: ['#4361EE'],
            fill: {
                type: 'gradient',
                gradient: {
                    shadeIntensity: 1,
                    opacityFrom: 0.45,
                    opacityTo: 0.05,
                    stops: [20, 100]
                }
            },
            dataLabels: { enabled: false },
            stroke: { curve: 'smooth', width: 3 },
            xaxis: {
                categories: {!! json_encode($trendLabels) !!},
                labels: { style: { colors: textColor, fontSize: '12px', fontWeight: 500 } },
                axisBorder: { show: false },
                axisTicks: { show: false }
            },
            yaxis: {
                labels: { style: { colors: textColor, fontSize: '12px' } }
            },
            grid: {
                borderColor: gridColor,
                strokeDashArray: 4
            },
            tooltip: {
                theme: isDark ? 'dark' : 'light'
            }
        };
        new ApexCharts(document.querySelector("#chartCenterTrend"), optionsLine).render();

        // 2. Donut Chart: Center Status
        const optionsDonut = {
            series: {!! json_encode($statusSeries) !!},
            labels: {!! json_encode($statusLabels) !!},
            chart: {
                type: 'donut',
                height: 270,
                background: 'transparent'
            },
            colors: ['#059669', '#E64980', '#D97706'],
            dataLabels: { enabled: true },
            legend: {
                position: 'bottom',
                labels: { colors: textColor }
            },
            stroke: { width: 0 },
            tooltip: {
                theme: isDark ? 'dark' : 'light'
            }
        };
        new ApexCharts(document.querySelector("#chartCenterStatusDonut"), optionsDonut).render();

        // 3. Bar Chart: Center Courses
        const optionsBar = {
            series: [{
                name: 'Enrolled Students',
                data: {!! json_encode($courseSeries) !!}
            }],
            chart: {
                type: 'bar',
                height: 270,
                toolbar: { show: false },
                background: 'transparent'
            },
            plotOptions: {
                bar: {
                    borderRadius: 6,
                    horizontal: true,
                    distributed: true,
                    barHeight: '65%'
                }
            },
            colors: ['#4361EE', '#059669', '#7C3AED', '#E64980', '#0D9488'],
            dataLabels: { enabled: true, style: { fontSize: '11px', fontWeight: 'bold' } },
            xaxis: {
                categories: {!! json_encode($courseLabels) !!},
                labels: { style: { colors: textColor, fontSize: '11px' } }
            },
            yaxis: {
                labels: {
                    style: { colors: textColor, fontSize: '11.5px', fontWeight: 600 },
                    maxWidth: 160
                }
            },
            grid: { borderColor: gridColor, strokeDashArray: 4 },
            legend: { show: false },
            tooltip: { theme: isDark ? 'dark' : 'light' }
        };
        new ApexCharts(document.querySelector("#chartCenterCoursesBar"), optionsBar).render();

        // 4. Heatmap: Center Activity
        const optionsHeatmap = {
            series: {!! json_encode($heatmapSeries) !!},
            chart: {
                type: 'heatmap',
                height: 270,
                toolbar: { show: false },
                background: 'transparent'
            },
            plotOptions: {
                heatmap: {
                    shadeIntensity: 0.5,
                    radius: 4,
                    useWarmShades: false,
                    colorScale: {
                        ranges: [
                            { from: 0, to: 0, color: isDark ? '#1F2937' : '#F1F5F9', name: 'None' },
                            { from: 1, to: 2, color: '#93C5FD', name: 'Low' },
                            { from: 3, to: 5, color: '#3B82F6', name: 'Medium' },
                            { from: 6, to: 20, color: '#1D4ED8', name: 'High' }
                        ]
                    }
                }
            },
            dataLabels: { enabled: false },
            xaxis: {
                labels: { style: { colors: textColor, fontSize: '11px', fontWeight: 500 } }
            },
            yaxis: {
                labels: { style: { colors: textColor, fontSize: '11px' } }
            },
            legend: {
                position: 'bottom',
                labels: { colors: textColor }
            },
            tooltip: { theme: isDark ? 'dark' : 'light' }
        };
        new ApexCharts(document.querySelector("#chartCenterHeatmap"), optionsHeatmap).render();
    });
</script>
@endpush
@endsection
