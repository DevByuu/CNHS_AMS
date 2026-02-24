<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\Attendance;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class RfidController extends Controller
{
    /**
     * Handle RFID scan from Arduino
     */
    public function scan(Request $request)
    {
        $rfid = $request->input('rfid');
        
        if (!$rfid) {
            return response()->json([
                'success' => false,
                'message' => 'No RFID provided'
            ], 400);
        }

        // Find student by RFID
        $student = Student::where('rfid', $rfid)->first();
        
        if (!$student) {
            return response()->json([
                'success' => false,
                'message' => 'Student not found',
                'rfid' => $rfid,
                'sound' => 'error'
            ]);
        }

        $today = Carbon::today()->format('Y-m-d');
        
        // Check if student already has attendance today
        $attendance = Attendance::where('student_id', $student->id)
            ->where('date', $today)
            ->first();

        if (!$attendance) {
            // CREATE: First scan (check-in)
            Attendance::create([
                'student_id' => $student->id,
                'date' => $today,
                'time_in' => Carbon::now()->format('H:i:s'),
                'status' => 'present'
            ]);

            return response()->json([
                'success' => true,
                'action' => 'check_in',
                'message' => 'Good morning, ' . $student->name . '!',
                'student' => [
                    'name' => $student->name,
                    'lrn' => $student->lrn,
                    'grade' => $student->grade,
                    'time_in' => Carbon::now()->format('h:i A')
                ],
                'sound' => 'success'
            ]);
        } elseif ($attendance->time_out === null) {
            // UPDATE: Second scan (check-out)
            $timeIn = Carbon::parse($attendance->time_in);
            $timeOut = Carbon::now();
            $duration = $timeIn->diffInMinutes($timeOut);

            $attendance->update([
                'time_out' => $timeOut->format('H:i:s'),
                'duration_minutes' => $duration
            ]);

            return response()->json([
                'success' => true,
                'action' => 'check_out',
                'message' => 'Goodbye, ' . $student->name . '!',
                'student' => [
                    'name' => $student->name,
                    'lrn' => $student->lrn,
                    'grade' => $student->grade,
                    'time_in' => $timeIn->format('h:i A'),
                    'time_out' => $timeOut->format('h:i A'),
                    'duration' => $this->formatDuration($duration)
                ],
                'sound' => 'success'
            ]);
        } else {
            // Already checked out
            return response()->json([
                'success' => false,
                'message' => 'Already checked out today',
                'student' => [
                    'name' => $student->name,
                    'time_in' => Carbon::parse($attendance->time_in)->format('h:i A'),
                    'time_out' => Carbon::parse($attendance->time_out)->format('h:i A')
                ],
                'sound' => 'error'
            ]);
        }
    }

    /**
     * Format duration in hours and minutes
     */
    private function formatDuration($minutes)
    {
        $hours = floor($minutes / 60);
        $mins = $minutes % 60;
        
        if ($hours > 0) {
            return "{$hours}h {$mins}m";
        }
        return "{$mins}m";
    }

    /**
     * Get real-time attendance status
     */
    public function status()
    {
        $today = Carbon::today()->format('Y-m-d');
        
        $totalStudents = Student::count();
        $present = Attendance::where('date', $today)
            ->where('status', 'present')
            ->count();
        $onCampus = Attendance::where('date', $today)
            ->where('status', 'present')
            ->whereNull('time_out')
            ->count();
        $checkedOut = Attendance::where('date', $today)
            ->where('status', 'present')
            ->whereNotNull('time_out')
            ->count();

        return response()->json([
            'total' => $totalStudents,
            'present' => $present,
            'on_campus' => $onCampus,
            'checked_out' => $checkedOut,
            'absent' => $totalStudents - $present
        ]);
    }
}