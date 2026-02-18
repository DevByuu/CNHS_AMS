<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\Attendance;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    // Display the live attendance monitor
    public function index()
    {
        $totalStudents = Student::count();

        // Use Carbon to ensure timezone consistency
        $today = Carbon::now()->toDateString();

        // Count students who have checked in today (have time_in)
        $presentToday = Attendance::whereDate('date', $today)
            ->whereNotNull('time_in')
            ->count();

        $absentToday = $totalStudents - $presentToday;

        return view('attendance.index', compact('totalStudents', 'presentToday', 'absentToday'));
    }

    // Show RFID check-in page
    public function rfidCheck()
    {
        return view('attendance.rfid-check');
    }

    // Handle RFID check-in AND check-out
    public function storeRfid(Request $request)
    {
        $request->validate([
            'rfid' => 'required|string',
        ]);

        // Find student by RFID
        $student = Student::where('rfid', $request->rfid)->first();

        if (!$student) {
            return response()->json([
                'success' => false,
                'message' => 'RFID card not registered. Please contact the administrator.'
            ], 404);
        }

        $today = Carbon::now()->toDateString();

        // Check if student has an attendance record for today
        $attendance = Attendance::where('student_id', $student->id)
            ->whereDate('date', $today)
            ->first();

        // CASE 1: No record exists - CREATE CHECK-IN
        if (!$attendance) {
            $timeIn = Carbon::now();
            
            Attendance::create([
                'student_id' => $student->id,
                'date'       => $today,
                'time_in'    => $timeIn->format('H:i:s'),
                'status'     => 'present'
            ]);

            // Get updated stats
            $stats = $this->getTodayStats();

            return response()->json([
                'success' => true,
                'action' => 'check-in',
                'message' => 'Check-in successful',
                'student' => [
                    'id'    => $student->id,
                    'name'  => $student->name,
                    'lrn'   => $student->lrn,
                    'grade' => $student->grade ?? 'N/A',
                    'time_in' => $timeIn->format('h:i A'),
                ],
                'stats' => $stats
            ]);
        }

        // CASE 2: Has record but NO time_out - PROCESS CHECK-OUT
        if ($attendance && !$attendance->time_out) {
            $timeIn = Carbon::parse($attendance->date . ' ' . $attendance->time_in);
            $timeOut = Carbon::now();
            
            // Calculate duration in minutes
            $durationMinutes = $timeIn->diffInMinutes($timeOut);
            
            $attendance->update([
                'time_out' => $timeOut->format('H:i:s'),
                'duration_minutes' => $durationMinutes
            ]);

            // Get updated stats
            $stats = $this->getTodayStats();

            return response()->json([
                'success' => true,
                'action' => 'check-out',
                'message' => 'Check-out successful',
                'student' => [
                    'id'       => $student->id,
                    'name'     => $student->name,
                    'lrn'      => $student->lrn,
                    'grade'    => $student->grade ?? 'N/A',
                    'time_in'  => Carbon::parse($attendance->time_in)->format('h:i A'),
                    'time_out' => $timeOut->format('h:i A'),
                    'duration' => $this->formatDuration($durationMinutes),
                ],
                'stats' => $stats
            ]);
        }

        // CASE 3: Already checked out
        return response()->json([
            'success' => false,
            'message' => $student->name . ' already checked out for today.',
            'student' => [
                'name'     => $student->name,
                'time_in'  => Carbon::parse($attendance->time_in)->format('h:i A'),
                'time_out' => Carbon::parse($attendance->time_out)->format('h:i A'),
                'duration' => $this->formatDuration($attendance->duration_minutes),
            ]
        ], 400);
    }

    /**
     * Get today's check-ins with time-out information
     */
    public function todayCheckIns()
    {
        $today = Carbon::today();

        // Get all check-ins for today with student info
        $checkIns = DB::table('attendances')
            ->join('students', 'attendances.student_id', '=', 'students.id')
            ->whereDate('attendances.date', $today)
            ->where('attendances.status', 'present')
            ->select(
                'students.name as student_name',
                'students.lrn',
                'students.grade',
                'attendances.time_in',
                'attendances.time_out',
                'attendances.duration_minutes',
                'attendances.created_at'
            )
            ->orderBy('attendances.created_at', 'desc')
            ->get()
            ->map(function ($checkIn) {
                $timeIn = $checkIn->time_in ? Carbon::parse($checkIn->time_in)->format('h:i A') : '--';
                $timeOut = $checkIn->time_out ? Carbon::parse($checkIn->time_out)->format('h:i A') : 'Not yet';
                $duration = $checkIn->duration_minutes ? $this->formatDuration($checkIn->duration_minutes) : 'On campus';
                $status = $checkIn->time_out ? 'Completed' : 'On campus';

                return [
                    'student_name' => $checkIn->student_name ?? 'Unknown Student',
                    'lrn' => $checkIn->lrn ?? '',
                    'grade' => $checkIn->grade ?? '',
                    'time_in' => $timeIn,
                    'time_out' => $timeOut,
                    'duration' => $duration,
                    'status' => $status,
                    'created_at' => $checkIn->created_at
                ];
            });

        // Get stats
        $stats = $this->getTodayStats();

        return response()->json([
            'success' => true,
            'checkIns' => $checkIns,
            'stats' => $stats
        ]);
    }

    /**
     * Get today's statistics
     */
    private function getTodayStats()
    {
        $today = Carbon::now()->toDateString();
        $totalStudents = Student::count();
        
        $presentToday = Attendance::whereDate('date', $today)
            ->whereNotNull('time_in')
            ->count();
        
        $absentToday = $totalStudents - $presentToday;

        return [
            'total' => $totalStudents,
            'present' => $presentToday,
            'absent' => $absentToday
        ];
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
}