<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\File;
use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\Attendance;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Barryvdh\DomPDF\Facade\PDF;

class ReportsController extends Controller
{
    /**
     * Display the reports page
     */

     public function export(Request $request)
    {
        $format = $request->input('format', 'pdf');
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->input('end_date', Carbon::now()->format('Y-m-d'));
        $gradeFilter = $request->input('grade_filter', null);

        // Parse dates
        $startDate = Carbon::parse($startDate);
        $endDate = Carbon::parse($endDate);

        $data = $this->getReportData($startDate, $endDate, $gradeFilter);

        if ($format === 'pdf') {
            return $this->exportPDF($data, $startDate, $endDate, $gradeFilter);
        } elseif ($format === 'excel') {
            return $this->exportExcel($data, $startDate, $endDate, $gradeFilter);
        }

        return response()->json(['error' => 'Invalid format'], 400);
    }


    public function index()
    {
        $startDate = request('start_date', Carbon::now()->startOfMonth());
        $endDate = request('end_date', Carbon::now());
        $grade = request('grade');

        $stats = $this->getStatistics($startDate, $endDate, $grade);

        return view('reports.index', $stats);
    }

    /**
     * Calculate statistics for the given period
     */
    private function getStatistics($startDate, $endDate, $grade = null)
    {
        $startDate = Carbon::parse($startDate);
        $endDate = Carbon::parse($endDate);

        // Calculate total days (excluding weekends)
        $totalDays = 0;
        $current = $startDate->copy();
        while ($current <= $endDate) {
            if ($current->isWeekday()) {
                $totalDays++;
            }
            $current->addDay();
        }

        // Get total students
        $studentsQuery = Student::query();
        if ($grade) {
            $studentsQuery->where('grade', $grade);
        }
        $totalStudents = $studentsQuery->count();

        // Get attendance records for the period
        $attendanceQuery = Attendance::whereBetween('date', [$startDate, $endDate]);
        
        if ($grade) {
            $attendanceQuery->whereHas('student', function($q) use ($grade) {
                $q->where('grade', $grade);
            });
        }

        $totalPresent = $attendanceQuery->clone()->where('status', 'present')->count();
        $totalAbsences = $attendanceQuery->clone()->where('status', 'absent')->count();
        $totalLate = $attendanceQuery->clone()->where('status', 'late')->count();

        // Calculate average attendance rate
        $expectedAttendance = $totalStudents * $totalDays;
        $avgAttendance = $expectedAttendance > 0 
            ? round(($totalPresent / $expectedAttendance) * 100, 1) 
            : 0;

        // Perfect attendance (students with 100% present status)
        $perfectAttendance = Student::when($grade, function($q) use ($grade) {
            $q->where('grade', $grade);
        })->whereHas('attendances', function($q) use ($startDate, $endDate, $totalDays) {
            $q->whereBetween('date', [$startDate, $endDate])
              ->where('status', 'present');
        }, '=', $totalDays)->count();

        return [
            'totalDays' => $totalDays,
            'totalStudents' => $totalStudents,
            'avgAttendance' => $avgAttendance,
            'totalAbsences' => $totalAbsences,
            'totalLate' => $totalLate,
            'perfectAttendance' => $perfectAttendance,
            'trendData' => $this->getAttendanceTrend($startDate, $endDate, $grade),
            'gradeData' => $this->getGradeDistribution(),
            'gradeSummary' => $this->getGradeSummary($startDate, $endDate, $grade),
            'recentAttendance' => $this->getRecentAttendance($startDate, $endDate, $grade),
            'studentReports' => $this->getStudentReports($startDate, $endDate, $grade),
            'insights' => $this->generateInsights($avgAttendance, $grade)
        ];
    }

    /**
     * Get attendance trend for the last 4 weeks
     */
    private function getAttendanceTrend($startDate, $endDate, $grade = null)
    {
        $weeks = [];
        $values = [];

        for ($i = 3; $i >= 0; $i--) {
            $weekStart = Carbon::now()->subWeeks($i)->startOfWeek();
            $weekEnd = Carbon::now()->subWeeks($i)->endOfWeek();

            $weeks[] = "Week " . (4 - $i);

            $query = Attendance::whereBetween('date', [$weekStart, $weekEnd])
                ->where('status', 'present');

            if ($grade) {
                $query->whereHas('student', function($q) use ($grade) {
                    $q->where('grade', $grade);
                });
            }

            $present = $query->count();
            
            $totalQuery = Student::query();
            if ($grade) {
                $totalQuery->where('grade', $grade);
            }
            $totalStudents = $totalQuery->count();
            
            $weekDays = 5; // Assume 5 weekdays
            $expected = $totalStudents * $weekDays;
            
            $rate = $expected > 0 ? round(($present / $expected) * 100, 1) : 0;
            $values[] = $rate;
        }

        return [
            'labels' => $weeks,
            'values' => $values
        ];
    }

    /**
     * Get grade distribution (student count per grade)
     */
    private function getGradeDistribution()
    {
        $grades = ['Grade 7', 'Grade 8', 'Grade 9', 'Grade 10', 'Grade 11', 'Grade 12'];
        $values = [];

        foreach ($grades as $grade) {
            $values[] = Student::where('grade', $grade)->count();
        }

        return [
            'labels' => $grades,
            'values' => $values
        ];
    }

    /**
     * Get detailed summary by grade
     */
    private function getGradeSummary($startDate, $endDate, $gradeFilter = null)
    {
        $grades = $gradeFilter 
            ? [$gradeFilter] 
            : ['Grade 7', 'Grade 8', 'Grade 9', 'Grade 10', 'Grade 11', 'Grade 12'];

        $summary = [];

        foreach ($grades as $grade) {
            $totalStudents = Student::where('grade', $grade)->count();
            
            $present = Attendance::whereBetween('date', [$startDate, $endDate])
                ->where('status', 'present')
                ->whereHas('student', function($q) use ($grade) {
                    $q->where('grade', $grade);
                })
                ->count();

            $absent = Attendance::whereBetween('date', [$startDate, $endDate])
                ->where('status', 'absent')
                ->whereHas('student', function($q) use ($grade) {
                    $q->where('grade', $grade);
                })
                ->count();

            $late = Attendance::whereBetween('date', [$startDate, $endDate])
                ->where('status', 'late')
                ->whereHas('student', function($q) use ($grade) {
                    $q->where('grade', $grade);
                })
                ->count();

            // Calculate days
            $current = Carbon::parse($startDate);
            $end = Carbon::parse($endDate);
            $days = 0;
            while ($current <= $end) {
                if ($current->isWeekday()) {
                    $days++;
                }
                $current->addDay();
            }

            $expected = $totalStudents * $days;
            $rate = $expected > 0 ? round(($present / $expected) * 100, 1) : 0;

            $status = 'poor';
            if ($rate >= 85) {
                $status = 'good';
            } elseif ($rate >= 75) {
                $status = 'average';
            }

            $summary[] = [
                'grade' => $grade,
                'total_students' => $totalStudents,
                'present' => $present,
                'absent' => $absent,
                'late' => $late,
                'rate' => $rate,
                'status' => $status
            ];
        }

        return $summary;
    }

    /**
     * Get recent attendance records
     */
    private function getRecentAttendance($startDate, $endDate, $grade = null)
    {
        $query = Attendance::with('student')
            ->whereBetween('date', [$startDate, $endDate])
            ->orderBy('date', 'desc')
            ->orderBy('time_in', 'desc')
            ->limit(50);

        if ($grade) {
            $query->whereHas('student', function($q) use ($grade) {
                $q->where('grade', $grade);
            });
        }

        // Remove attendance records with missing students
        $records = $query->get()->filter(function ($attendance) {
            return $attendance->student !== null;
        });

        return $records->map(function($attendance) {
            return [
                'date' => Carbon::parse($attendance->date)->format('M d, Y'),
                'student_name' => $attendance->student->name,
                'lrn' => $attendance->student->lrn,
                'grade' => $attendance->student->grade,
                'time_in' => $attendance->time_in 
                    ? Carbon::parse($attendance->time_in)->format('h:i A')
                    : '-',
                'status' => $attendance->status,
                'remarks' => $this->getRemarks($attendance)
            ];
        })->toArray();
    }


    /**
     * Get student-wise reports
     */
    private function getStudentReports($startDate, $endDate, $grade = null)
    {
        $query = Student::with(['attendances' => function($q) use ($startDate, $endDate) {
            $q->whereBetween('date', [$startDate, $endDate]);
        }]);

        if ($grade) {
            $query->where('grade', $grade);
        }

        $students = $query->limit(20)->get();

        // Calculate days
        $current = Carbon::parse($startDate);
        $end = Carbon::parse($endDate);
        $totalDays = 0;
        while ($current <= $end) {
            if ($current->isWeekday()) {
                $totalDays++;
            }
            $current->addDay();
        }

        return $students->map(function($student) use ($totalDays) {
            $present = $student->attendances->where('status', 'present')->count();
            $absent = $student->attendances->where('status', 'absent')->count();
            $late = $student->attendances->where('status', 'late')->count();
            
            $rate = $totalDays > 0 ? round(($present / $totalDays) * 100, 0) : 0;

            return [
                'id' => $student->id,
                'name' => $student->name,
                'lrn' => $student->lrn,
                'grade' => $student->grade,
                'present' => $present,
                'absent' => $absent,
                'late' => $late,
                'rate' => $rate
            ];
        })->toArray();
    }

    /**
     * Generate insights based on data
     */
    private function generateInsights($avgAttendance, $grade = null)
    {
        $insights = [];

        if ($avgAttendance >= 85) {
            $insights[] = [
                'type' => 'success',
                'title' => 'Strong Overall Performance',
                'message' => "{$avgAttendance}% average attendance rate exceeds the target of 85%. Keep up the good work!",
                'badge' => 'Positive Trend'
            ];
        } else {
            $insights[] = [
                'type' => 'warning',
                'title' => 'Attendance Below Target',
                'message' => "{$avgAttendance}% average attendance rate is below the 85% target. Consider intervention strategies.",
                'badge' => 'Action Required'
            ];
        }

        return $insights;
    }

    /**
     * Get remarks for attendance record
     */
    private function getRemarks($attendance)
    {
        if ($attendance->status === 'present') {
            if ($attendance->time_in) {
                $timeIn = Carbon::parse($attendance->time_in);
                $cutoffTime = Carbon::parse('08:00');
                
                if ($timeIn->lessThan($cutoffTime)) {
                    return 'On time';
                }
            }
            return 'Present';
        } elseif ($attendance->status === 'late') {
            if ($attendance->time_in) {
                $timeIn = Carbon::parse($attendance->time_in);
                $cutoffTime = Carbon::parse('08:00');
                $minutesLate = $timeIn->diffInMinutes($cutoffTime);
                return "{$minutesLate} mins late";
            }
            return 'Late';
        } else {
            return 'No check-in';
        }
    }

    /**
     * Export as PDF with school logo and professional design
     */
    private function exportPDF($stats, $startDate, $endDate, $grade = null)
    {
        // CNHS logo
        $logoPath = public_path('img/cnhs.png');
        $cnhs_logo_base64 = '';
        if (File::exists($logoPath)) {
            $type = pathinfo($logoPath, PATHINFO_EXTENSION);
            $data = File::get($logoPath);
            $cnhs_logo_base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
        }

        // DepEd logo
        $depedLogoPath = public_path('img/deped-logo.png');
        $deped_logo_base64 = '';
        if (File::exists($depedLogoPath)) {
            $type = pathinfo($depedLogoPath, PATHINFO_EXTENSION);
            $data = File::get($depedLogoPath);
            $deped_logo_base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
        }

        $startDate = Carbon::parse($startDate);
        $endDate = Carbon::parse($endDate);

        // FORMAT DATE FOR PDF SUMMARY SECTION
        if ($startDate->isSameDay($endDate)) {
            $report_date = $startDate->format('F d, Y');
        } else {
            $report_date = $startDate->format('F d, Y') . ' - ' . $endDate->format('F d, Y');
        }

        // FORMAT GRADE FILTER FOR PDF SUMMARY SECTION
        $grade_filter = $grade ?: 'All Grades';

        $presentStudentsQuery = Attendance::with('student')
            ->whereBetween('date', [$startDate, $endDate])
            ->where('status', 'present')
            ->select('student_id', 'date', 'time_in')
            ->orderBy('date', 'desc')
            ->orderBy('time_in', 'asc');

        if ($grade) {
            $presentStudentsQuery->whereHas('student', function($q) use ($grade) {
                $q->where('grade', $grade);
            });
        }

        // Remove records without students
        $presentRecords = $presentStudentsQuery->get()->filter(function ($attendance) {
            return $attendance->student !== null;
        });

        // Safe mapping
        $presentStudents = $presentRecords->map(function($attendance) {
            if (!$attendance->student) {
                return null;
            }

            $student = $attendance->student;

            return [
                'lrn' => $student->lrn ?? 'N/A',
                'name' => $student->name ?? 'Unknown',
                'grade' => $student->grade ?? 'N/A',
                'section' => 'N/A',
                'sex' => 'N/A',
                'time_in' => $attendance->time_in
                    ? Carbon::parse($attendance->time_in)->format('h:i A')
                    : '-',
            ];
        })->filter()->values()->toArray();

        $grades = $grade
            ? [$grade]
            : ['Grade 7', 'Grade 8', 'Grade 9', 'Grade 10', 'Grade 11', 'Grade 12'];

        $gradeBreakdown = [];
        foreach ($grades as $gradeLevel) {
            $totalStudents = Student::where('grade', $gradeLevel)->count();

            $presentCount = Attendance::whereBetween('date', [$startDate, $endDate])
                ->where('status', 'present')
                ->whereHas('student', function($q) use ($gradeLevel) {
                    $q->where('grade', $gradeLevel);
                })
                ->count();

            $rate = $totalStudents > 0
                ? round(($presentCount / ($totalStudents * max($stats['totalDays'], 1))) * 100, 1)
                : 0;

            $gradeBreakdown[] = [
                'grade' => $gradeLevel,
                'present' => $presentCount,
                'total' => $totalStudents,
                'rate' => $rate
            ];
        }

        $data = [
            'school_name' => 'Concepcion National High School',
            'school_address' => 'Concepcion, Mabini, Bohol',
            'report_title' => 'Present Students Report',
            'report_date' => $report_date,
            'grade_filter' => $grade_filter,
            'generated_date' => Carbon::now()->format('F d, Y h:i A'),
            'cnhs_logo_base64' => $cnhs_logo_base64,
            'deped_logo_base64' => $deped_logo_base64,
            'stats' => [
                'totalPresent' => count($presentStudents),
                'totalEnrolled' => $stats['totalStudents'],
                'attendanceRate' => $stats['avgAttendance'],
                'gradeBreakdown' => $gradeBreakdown,
                'totalDays' => $stats['totalDays'],
                'totalStudents' => $stats['totalStudents'],
                'avgAttendance' => $stats['avgAttendance'],
                'totalAbsences' => $stats['totalAbsences'],
                'perfectAttendance' => $stats['perfectAttendance'],
                'gradeSummary' => $stats['gradeSummary'],
                'recentAttendance' => $stats['recentAttendance'],
            ],
            'presentStudents' => $presentStudents,
        ];

        $pdf = \PDF::loadView('reports.pdf', $data);
        $pdf->setPaper('a4', 'portrait');

        // IMPROVED FILENAME WITH DATE RANGE AND GRADE
        $filename = 'CNHS_Present_Students_' . $startDate->format('Y-m-d');
        if (!$startDate->isSameDay($endDate)) {
            $filename .= '_to_' . $endDate->format('Y-m-d');
        }
        if ($grade) {
            $filename .= '_' . str_replace(' ', '_', $grade);
        }
        $filename .= '.pdf';

        return $pdf->download($filename);
    }

    // Present Students PDF Export
    public function exportPresentStudents(Request $request)
{
    $reportDate = $request->input('date', Carbon::today()->format('Y-m-d'));
    $gradeFilter = $request->input('grade_filter');

    // Get present students with time-in, time-out, and duration
    $presentQuery = DB::table('attendances')
        ->join('students', 'attendances.student_id', '=', 'students.id')
        ->where('attendances.date', $reportDate)
        ->where('attendances.status', 'present')
        ->select(
            'students.lrn',
            'students.name',
            'students.grade',
            'attendances.time_in',
            'attendances.time_out',
            'attendances.duration_minutes'
        )
        ->orderBy('students.grade')
        ->orderBy('students.name');

    // FIX: filter using full "Grade X" format since DB stores "Grade 7" not "7"
    if ($gradeFilter) {
        $presentQuery->where('students.grade', $gradeFilter);
    }

    $presentStudents = $presentQuery->get();

    // Format students data with time-out info
    $formattedStudents = $presentStudents->map(function ($student) {
        $timeIn = $student->time_in ? Carbon::parse($student->time_in)->format('h:i A') : '--';
        $timeOut = $student->time_out ? Carbon::parse($student->time_out)->format('h:i A') : 'Not yet';
        $duration = $student->duration_minutes ? $this->formatDuration($student->duration_minutes) : 'On campus';
        $status = $student->time_out ? 'Completed' : 'On campus';

        return [
            'lrn' => $student->lrn,
            'name' => $student->name,
            'grade' => $student->grade, // already stored as "Grade 7"
            'time_in' => $timeIn,
            'time_out' => $timeOut,
            'duration' => $duration,
            'status' => $status
        ];
    })->toArray();

    // FIX: use $gradeFilter directly with full "Grade X" format
    $totalEnrolled = $gradeFilter
        ? Student::where('grade', $gradeFilter)->count()
        : Student::count();

    $totalPresent = count($formattedStudents);
    $totalAbsent = $totalEnrolled - $totalPresent;
    $attendanceRate = $totalEnrolled > 0 ? round(($totalPresent / $totalEnrolled) * 100, 2) : 0;

    // Count on campus vs checked out
    $onCampus = collect($formattedStudents)->where('status', 'On campus')->count();
    $checkedOut = collect($formattedStudents)->where('status', 'Completed')->count();

    // FIX: use full "Grade X" format to match DB values
    $grades = $gradeFilter
        ? [$gradeFilter]
        : ['Grade 7', 'Grade 8', 'Grade 9', 'Grade 10', 'Grade 11', 'Grade 12'];

    $gradeBreakdown = [];
    foreach ($grades as $gradeLevel) {
        $gradeTotal = Student::where('grade', $gradeLevel)->count();

        $gradePresent = DB::table('attendances')
            ->join('students', 'attendances.student_id', '=', 'students.id')
            ->where('attendances.date', $reportDate)
            ->where('attendances.status', 'present')
            ->where('students.grade', $gradeLevel)
            ->count();

        $gradeOnCampus = DB::table('attendances')
            ->join('students', 'attendances.student_id', '=', 'students.id')
            ->where('attendances.date', $reportDate)
            ->where('attendances.status', 'present')
            ->where('students.grade', $gradeLevel)
            ->whereNull('attendances.time_out')
            ->count();

        $gradeCheckedOut = $gradePresent - $gradeOnCampus;
        $gradeAbsent = $gradeTotal - $gradePresent;
        $gradeRate = $gradeTotal > 0 ? round(($gradePresent / $gradeTotal) * 100, 2) : 0;

        $gradeBreakdown[] = [
            'grade' => $gradeLevel,
            'total' => $gradeTotal,
            'present' => $gradePresent,
            'absent' => $gradeAbsent,
            'on_campus' => $gradeOnCampus,
            'checked_out' => $gradeCheckedOut,
            'rate' => $gradeRate
        ];
    }

    // Build stats array
    $stats = [
        'totalEnrolled' => $totalEnrolled,
        'totalPresent' => $totalPresent,
        'totalAbsent' => $totalAbsent,
        'attendanceRate' => $attendanceRate,
        'onCampus' => $onCampus,
        'checkedOut' => $checkedOut,
        'gradeBreakdown' => $gradeBreakdown
    ];

    // Get school info and logos
    $schoolName = config('app.school_name', 'CONCEPCION NATIONAL HIGH SCHOOL');
    $schoolAddress = config('app.school_address', 'Concepcion, Mabini, Bohol');

    $cnhsLogoPath = public_path('img/cnhs.png');
    $depedLogoPath = public_path('img/deped-logo.png');

    $cnhsLogoBase64 = file_exists($cnhsLogoPath)
        ? 'data:image/png;base64,' . base64_encode(file_get_contents($cnhsLogoPath))
        : null;

    $depedLogoBase64 = file_exists($depedLogoPath)
        ? 'data:image/png;base64,' . base64_encode(file_get_contents($depedLogoPath))
        : null;

    $formattedReportDate = Carbon::parse($reportDate)->format('F d, Y (l)');

    $pdf = PDF::loadView('reports.pdf', [
        'presentStudents' => $formattedStudents,
        'report_date' => $formattedReportDate,
        'grade_filter' => $gradeFilter,
        'stats' => $stats,
        'school_name' => $schoolName,
        'school_address' => $schoolAddress,
        'cnhs_logo_base64' => $cnhsLogoBase64,
        'deped_logo_base64' => $depedLogoBase64
    ]);

    $gradeSlug = $gradeFilter ? '-' . strtolower(str_replace(' ', '-', $gradeFilter)) : '-all-grades';
    $filename = 'CNHS_PRESENT-' . $reportDate . $gradeSlug . '-' . substr(md5(time()), 0, 6) . '.pdf';

    return $pdf->setPaper('a4', 'portrait')->download($filename);
}
    /**
     * Format duration in hours and minutes
     */
    private function formatDuration($minutes)
    {
        if (!$minutes) {
            return '0m';
        }

        $hours = floor($minutes / 60);
        $mins = $minutes % 60;
        
        if ($hours > 0) {
            return "{$hours}h {$mins}m";
        }
        return "{$mins}m";
    }

    /**
     * Export as CSV
     */
    private function exportCSV($stats, $startDate, $endDate)
    {
        $filename = 'attendance_report_' . date('Y-m-d') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function() use ($stats, $startDate, $endDate) {
            $file = fopen('php://output', 'w');
            
            // Report Header
            fputcsv($file, ['CNHS Attendance Report']);
            fputcsv($file, ['Period:', Carbon::parse($startDate)->format('M d, Y') . ' - ' . Carbon::parse($endDate)->format('M d, Y')]);
            fputcsv($file, ['Generated:', now()->format('M d, Y h:i A')]);
            fputcsv($file, []);
            
            // Grade Summary
            fputcsv($file, ['Grade Level Summary']);
            fputcsv($file, ['Grade', 'Total Students', 'Present', 'Absent', 'Late', 'Attendance Rate', 'Status']);
            
            foreach ($stats['gradeSummary'] as $grade) {
                fputcsv($file, [
                    $grade['grade'],
                    $grade['total_students'],
                    $grade['present'],
                    $grade['absent'],
                    $grade['late'],
                    $grade['rate'] . '%',
                    ucfirst($grade['status'])
                ]);
            }
            
            fputcsv($file, []);
            
            // Recent Attendance
            fputcsv($file, ['Recent Attendance Records']);
            fputcsv($file, ['Date', 'Student Name', 'LRN', 'Grade', 'Time In', 'Status', 'Remarks']);
            
            foreach ($stats['recentAttendance'] as $record) {
                fputcsv($file, [
                    $record['date'],
                    $record['student_name'],
                    $record['lrn'],
                    $record['grade'],
                    $record['time_in'],
                    ucfirst($record['status']),
                    $record['remarks']
                ]);
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Export as Excel (placeholder for future implementation)
     */
    private function exportExcel($stats, $startDate, $endDate)
    {
        // For now, use CSV format
        return $this->exportCSV($stats, $startDate, $endDate);
    }

    /**
     * Get real-time data for AJAX requests with grade distribution
     */
    public function realtimeData(Request $request)
{
    $startDate = $request->input('start_date', Carbon::today()->format('Y-m-d'));
    $endDate = $request->input('end_date', Carbon::today()->format('Y-m-d'));
    $grade = $request->input('grade');

    $start = Carbon::parse($startDate);
    $end = Carbon::parse($endDate);
    $totalDays = $start->diffInDays($end) + 1;

    // Get total students
    $totalStudentsQuery = Student::query();
    if ($grade) {
        $totalStudentsQuery->where('grade', 'Grade ' . $grade);
    }
    $totalStudents = $totalStudentsQuery->count();

    // Get attendance data
    $attendanceQuery = DB::table('attendances')
        ->join('students', 'attendances.student_id', '=', 'students.id')
        ->whereBetween('attendances.date', [$startDate, $endDate])
        ->where('attendances.status', 'present');

    if ($grade) {
        $attendanceQuery->where('students.grade', 'Grade ' . $grade);
    }

    $totalPresent = $attendanceQuery->count();
    $totalAbsent = ($totalStudents * $totalDays) - $totalPresent;

    $avgAttendance = $totalStudents > 0
        ? round(($totalPresent / ($totalStudents * $totalDays)) * 100, 1)
        : 0;

    // Perfect attendance
    $perfectAttendance = DB::table('students')
        ->where(function($query) use ($grade) {
            if ($grade) $query->where('grade', 'Grade ' . $grade);
        })
        ->whereExists(function($query) use ($startDate, $endDate, $totalDays) {
            $query->select(DB::raw(1))
                ->from('attendances')
                ->whereColumn('attendances.student_id', 'students.id')
                ->whereBetween('attendances.date', [$startDate, $endDate])
                ->where('attendances.status', 'present')
                ->groupBy('attendances.student_id')
                ->havingRaw('COUNT(*) = ?', [$totalDays]);
        })
        ->count();

    // Grade distribution for doughnut chart
    $gradeLabels = [];
    $gradeValues = [];
    $allGrades = ['Grade 7', 'Grade 8', 'Grade 9', 'Grade 10', 'Grade 11', 'Grade 12'];

    if ($grade) {
        $gradeLabels[] = 'Grade ' . $grade;
        $gradeValues[] = DB::table('attendances')
            ->join('students', 'attendances.student_id', '=', 'students.id')
            ->whereBetween('attendances.date', [$startDate, $endDate])
            ->where('attendances.status', 'present')
            ->where('students.grade', 'Grade ' . $grade)
            ->distinct('students.id')
            ->count('students.id');
    } else {
        foreach ($allGrades as $gradeLevel) {
            $gradeLabels[] = $gradeLevel;
            $gradeValues[] = DB::table('attendances')
                ->join('students', 'attendances.student_id', '=', 'students.id')
                ->whereBetween('attendances.date', [$startDate, $endDate])
                ->where('attendances.status', 'present')
                ->where('students.grade', $gradeLevel)
                ->distinct('students.id')
                ->count('students.id');
        }
    }

    // Grade-by-day data for multi-line chart
    $gradeByDayLabels = [];
    for ($date = $start->copy(); $date->lte($end); $date->addDay()) {
        $gradeByDayLabels[] = $date->format('M d');
    }

    $gradesToChart = $grade ? ['Grade ' . $grade] : $allGrades;
    $gradeByDayDatasets = [];

    foreach ($gradesToChart as $gradeLevel) {
        $gradeStudentCount = DB::table('students')->where('grade', $gradeLevel)->count();
        $values = [];

        for ($date = $start->copy(); $date->lte($end); $date->addDay()) {
            $dayPresent = DB::table('attendances')
                ->join('students', 'attendances.student_id', '=', 'students.id')
                ->where('attendances.date', $date->format('Y-m-d'))
                ->where('attendances.status', 'present')
                ->where('students.grade', $gradeLevel)
                ->count();

            $rate = $gradeStudentCount > 0
                ? round(($dayPresent / $gradeStudentCount) * 100, 1)
                : 0;

            $values[] = $rate;
        }

        $gradeByDayDatasets[] = [
            'label'  => $gradeLevel,
            'values' => $values,
        ];
    }

    return response()->json([
        'totalDays'        => $totalDays,
        'totalStudents'    => $totalStudents,
        'avgAttendance'    => $avgAttendance,
        'totalAbsences'    => $totalAbsent,
        'perfectAttendance'=> $perfectAttendance,
        'gradeData'        => [
            'labels' => $gradeLabels,
            'values' => $gradeValues,
        ],
        'gradeByDayData'   => [
            'labels'   => $gradeByDayLabels,
            'datasets' => $gradeByDayDatasets,
        ],
    ]);
}
}