<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\Attendance;
use Carbon\Carbon;

class ReportsController extends Controller
{
    /**
     * Display the reports page
     */
    public function index()
    {
        $startDate = request('start_date', Carbon::now()->startOfMonth());
        $endDate = request('end_date', Carbon::now());
        $grade = request('grade');

        $stats = $this->getStatistics($startDate, $endDate, $grade);

        return view('reports.index', $stats);
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

        $records = $query->get();

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
    public function export(Request $request)
    {
        $format = $request->get('format', 'csv');
        $startDate = $request->get('start_date', Carbon::now()->startOfMonth());
        $endDate = $request->get('end_date', Carbon::now());
        $grade = $request->get('grade');
        $reportType = $request->get('report_type', 'summary');

        $stats = $this->getStatistics($startDate, $endDate, $grade);

        switch ($format) {
            case 'pdf':
                if ($reportType === 'daily_list') {
                    return $this->exportDailyAttendanceListPDF($startDate, $grade);
                }
                return $this->exportPDF($stats, $startDate, $endDate, $grade);
            case 'csv':
                return $this->exportCSV($stats, $startDate, $endDate);
            case 'excel':
                return $this->exportExcel($stats, $startDate, $endDate);
            default:
                return redirect()->back()->with('error', 'Invalid export format');
        }
    }

    /**
     * Export as PDF with school logo and professional design
     */
    private function exportPDF($stats, $startDate, $endDate, $grade = null)
    {
        $data = [
            'school_name' => 'Cebu National High School',
            'school_address' => 'Cebu City, Philippines',
            'report_title' => 'Attendance Report',
            'date_range' => Carbon::parse($startDate)->format('F d, Y') . ' - ' . Carbon::parse($endDate)->format('F d, Y'),
            'generated_date' => Carbon::now()->format('F d, Y h:i A'),
            'grade_filter' => $grade ?: 'All Grades',
            'stats' => $stats,
            'startDate' => Carbon::parse($startDate)->format('F d, Y'),
            'endDate' => Carbon::parse($endDate)->format('F d, Y'),
        ];

        // Load the PDF view
        $pdf = \PDF::loadView('reports.pdf', $data);
        
        // Set paper size and orientation
        $pdf->setPaper('a4', 'portrait');
        
        // Generate filename
        $filename = 'CNHS_Attendance_Report_' . Carbon::now()->format('Y-m-d_His') . '.pdf';
        
        // Download the PDF
        return $pdf->download($filename);
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
        
        $data = [
            'school_name' => 'Cebu National High School',
            'school_address' => 'Cebu City, Philippines',
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
}
