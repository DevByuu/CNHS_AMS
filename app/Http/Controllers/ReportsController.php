<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\File;
use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\Attendance;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

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
     * Export reports
     */

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

    // ✨ FORMAT DATE FOR PDF SUMMARY SECTION
    // If same day, show single date; otherwise show range
    if ($startDate->isSameDay($endDate)) {
        $report_date = $startDate->format('F d, Y'); // "February 12, 2026"
    } else {
        $report_date = $startDate->format('F d, Y') . ' - ' . $endDate->format('F d, Y');
    }

    // ✨ FORMAT GRADE FILTER FOR PDF SUMMARY SECTION
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
        
        // ✨ THESE NOW MATCH YOUR PDF TEMPLATE VARIABLES
        'report_date' => $report_date,           // ← Populates {{ $report_date }} in template
        'grade_filter' => $grade_filter,         // ← Populates {{ $grade_filter }} in template
        
        'generated_date' => Carbon::now()->format('F d, Y h:i A'),
        'cnhs_logo_base64' => $cnhs_logo_base64,
        'deped_logo_base64' => $deped_logo_base64,

        'stats' => [
            'totalPresent' => count($presentStudents),      // ← Populates {{ $stats['totalPresent'] }}
            'totalEnrolled' => $stats['totalStudents'],     // ← Populates {{ $stats['totalEnrolled'] }}
            'attendanceRate' => $stats['avgAttendance'],    // ← Populates {{ $stats['attendanceRate'] }}
            'gradeBreakdown' => $gradeBreakdown,            // ← Populates {{ $stats['gradeBreakdown'] }}
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

    // ✨ IMPROVED FILENAME WITH DATE RANGE AND GRADE
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
    $date = $request->input('date', now()->format('Y-m-d'));
    $gradeFilter = $request->input('grade_filter', null);

    $reportDate = Carbon::parse($date);

    // Base query for present students
    $presentQuery = DB::table('attendances')
        ->join('students', 'attendances.student_id', '=', 'students.id')
        ->select('students.lrn', 'students.name', 'students.grade', 'attendances.time_in', 'attendances.status')
        ->where('attendances.date', $reportDate->format('Y-m-d'))
        ->where('attendances.status', 'present')
        ->orderBy('students.grade')
        ->orderBy('students.name');

    if ($gradeFilter) {
        $presentQuery->where('students.grade', $gradeFilter);
    }

    $presentStudents = $presentQuery->get();

    // Total present
    $totalPresent = $presentStudents->count();

    // Total enrolled
    $enrolledQuery = DB::table('students');
    if ($gradeFilter) {
        $enrolledQuery->where('grade', $gradeFilter);
    }
    $totalEnrolled = $enrolledQuery->count();

    // Attendance rate
    $attendanceRate = $totalEnrolled > 0
        ? round(($totalPresent / $totalEnrolled) * 100, 2)
        : 0;

    // ✅ FIXED: Fetch grade breakdown with correct grade format
    $gradeBreakdown = [];
    
    // Define grades - adjust this based on how grades are stored in your database
    // If stored as "11", "12", etc., use this:
    $gradeNumbers = $gradeFilter 
        ? [str_replace('Grade ', '', $gradeFilter)] 
        : ['7', '8', '9', '10', '11', '12'];
    
    // If stored as "Grade 7", "Grade 8", etc., use this instead:
    $gradeNumbers = $gradeFilter 
        ? [$gradeFilter] 
        : ['Grade 7','Grade 8','Grade 9','Grade 10','Grade 11','Grade 12'];
    
    foreach ($gradeNumbers as $gradeNum) {
        // Query using the actual format in database
        $gradePresent = DB::table('attendances')
            ->join('students', 'attendances.student_id', '=', 'students.id')
            ->where('attendances.date', $reportDate->format('Y-m-d'))
            ->where('attendances.status', 'present')
            ->where('students.grade', $gradeNum) // ✅ Use correct format
            ->count();

        $gradeTotal = DB::table('students')
            ->where('grade', $gradeNum) // ✅ Use correct format
            ->count();

        $gradeRate = $gradeTotal > 0 ? round(($gradePresent / $gradeTotal) * 100, 2) : 0;

        $gradeBreakdown[] = [
            'grade' => 'Grade ' . $gradeNum, // ✅ Format for display
            'present' => $gradePresent,
            'total' => $gradeTotal,
            'rate' => $gradeRate
        ];
    }

    // ✅ DEBUG: Add this temporarily to see what's happening
    \Log::info('Grade Breakdown Debug', [
        'date' => $reportDate->format('Y-m-d'),
        'gradeBreakdown' => $gradeBreakdown,
        'presentStudents' => $presentStudents->toArray()
    ]);

    $pdfData = [
        'school_name' => config('app.school_name', 'Concepcion National High School'),
        'school_address' => config('app.school_address', 'Concepcion, Mabini, Bohol'),
        'report_date' => $reportDate->format('F d, Y'),
        'grade_filter' => $gradeFilter ?? 'All Grades',
        'generated_date' => now()->format('F d, Y g:i A'),
        'presentStudents' => $presentStudents->map(function($student){
            return [
                'lrn' => $student->lrn,
                'name' => $student->name,
                'grade' => $student->grade,
                'time_in' => $student->time_in ? Carbon::parse($student->time_in)->format('h:i A') : '-',
            ];
        })->toArray(),
        'stats' => [
            'totalPresent' => $totalPresent,
            'totalEnrolled' => $totalEnrolled,
            'attendanceRate' => $attendanceRate,
            'gradeBreakdown' => $gradeBreakdown
        ],
        'cnhs_logo_base64' => $this->getLogoBase64('cnhs.png'),
        'deped_logo_base64' => $this->getLogoBase64('deped-logo.png'),
    ];

    $pdf = app('dompdf.wrapper');
    $pdf->loadView('reports.pdf', $pdfData);
    $pdf->setPaper('letter', 'portrait');

    $filename = 'CNHS_PRESENT-' . $reportDate->format('Y-m-d');
    if ($gradeFilter) {
        $filename .= '-' . str_replace(' ', '-', strtolower($gradeFilter));
    }
    $filename .= '-' . Str::random(6) . '.pdf';

    return $pdf->download($filename);
}


    /**
     * Get base64 encoded logo
     */
    private function getLogoBase64($filename)
    {
        $path = public_path('img/' . $filename);
        
        if (file_exists($path)) {
            $type = pathinfo($path, PATHINFO_EXTENSION);
            $data = file_get_contents($path);
            return 'data:image/' . $type . ';base64,' . base64_encode($data);
        }
        
        return null;
    }


    /**
     * Export daily attendance list by grade level
     */
    private function exportDailyAttendanceListPDF($date, $grade = null)
    {
        $targetDate = Carbon::parse($date);
        
        // Build query for students with their attendance
        $query = Student::with(['attendances' => function($q) use ($targetDate) {
            $q->where('date', $targetDate->format('Y-m-d'));
        }]);
        
        // Filter by grade if specified
        if ($grade) {
            $query->where('grade', $grade);
        }
        
        // Get all students ordered by grade and name
        $students = $query->orderBy('grade')->orderBy('name')->get();
        
        // Group students by grade
        $studentsByGrade = $students->groupBy('grade');
        
        // Calculate statistics for each grade
        $gradeStats = [];
        foreach ($studentsByGrade as $gradeName => $gradeStudents) {
            $present = $gradeStudents->filter(function($student) {
                return $student->attendances->isNotEmpty() && 
                       in_array($student->attendances->first()->status, ['present', 'late']);
            });
            
            $presentList = $gradeStudents->filter(function($student) {
                return $student->attendances->isNotEmpty() && 
                       $student->attendances->first()->status === 'present';
            });
            
            $lateList = $gradeStudents->filter(function($student) {
                return $student->attendances->isNotEmpty() && 
                       $student->attendances->first()->status === 'late';
            });
            
            $absentList = $gradeStudents->filter(function($student) {
                return $student->attendances->isEmpty() || 
                       $student->attendances->first()->status === 'absent';
            });
            
            $gradeStats[$gradeName] = [
                'total' => $gradeStudents->count(),
                'present' => $presentList->count(),
                'late' => $lateList->count(),
                'absent' => $absentList->count(),
                'attendance_rate' => $gradeStudents->count() > 0 
                    ? round(($present->count() / $gradeStudents->count()) * 100, 1) 
                    : 0,
                'present_students' => $presentList,
                'late_students' => $lateList,
                'absent_students' => $absentList
            ];
        }
        
        // Prepare all data for the view
        $data = [
            'school_name' => 'Concepcion National High School',
            'school_address' => 'Concepcion, Tarlac',
            'report_title' => $grade ? "Daily Attendance List - {$grade}" : 'Daily Attendance List - All Grades',
            'report_date' => $targetDate->format('F d, Y (l)'),
            'generated_date' => Carbon::now()->format('F d, Y h:i A'),
            'grade_filter' => $grade ?: 'All Grades',
            'target_date' => $targetDate,
            'grades_data' => $gradeStats,
            'total_students' => $students->count(),
            'total_present' => collect($gradeStats)->sum('present'),
            'total_late' => collect($gradeStats)->sum('late'),
            'total_absent' => collect($gradeStats)->sum('absent'),
        ];
        
        // Load the PDF view
        $pdf = \PDF::loadView('reports.daily-attendance-list', $data);
        
        // Set paper size and orientation
        $pdf->setPaper('a4', 'portrait');
        
        // Generate filename
        $filename = 'CNHS_Daily_Attendance_' . $targetDate->format('Y-m-d') . ($grade ? '_' . str_replace(' ', '_', $grade) : '') . '.pdf';
        
        // Download the PDF
        return $pdf->download($filename);
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
        // To implement Excel: composer require maatwebsite/excel
        return $this->exportCSV($stats, $startDate, $endDate);
    }

    /**
 * Get real-time data for AJAX requests
 */
public function realtimeData(Request $request)
{
    $startDate = $request->get('start_date', Carbon::now()->startOfMonth());
    $endDate = $request->get('end_date', Carbon::now());
    $grade = $request->get('grade');

    $stats = $this->getStatistics($startDate, $endDate, $grade);
    
    return response()->json(array_merge($stats, [
        'success' => true,
        'timestamp' => Carbon::now()->toISOString()
    ]));
}
}