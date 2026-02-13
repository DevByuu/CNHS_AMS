<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\Attendance;
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

    // Handle RFID check-in
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

        // Check if student already checked in today
        $alreadyCheckedIn = Attendance::where('student_id', $student->id) // Using student->id here!
            ->whereDate('date', $today)
            ->whereNotNull('time_in')
            ->first();

        if ($alreadyCheckedIn) {
            $checkInTime = Carbon::parse($alreadyCheckedIn->time_in)->format('h:i A');

            return response()->json([
                'success' => false,
                'message' => $student->name . ' already checked in today at ' . $checkInTime
            ], 400);
        }

        // Record attendance with STUDENT ID (not auth user id)
        Attendance::create([
            'student_id' => $student->id, // IMPORTANT: This is the student's ID
            'date'       => $today,
            'time_in'    => Carbon::now()->format('H:i:s'),
        ]);

        // Refresh stats
        $total = Student::count();
        $present = Attendance::whereDate('date', $today)
            ->whereNotNull('time_in')
            ->count();
        $absent = $total - $present;

        return response()->json([
            'success' => true,
            'student' => [
                'name'  => $student->name,
                'lrn'   => $student->lrn,
                'grade' => $student->grade ?? 'N/A',
            ],
            'stats' => [
                'total'   => $total,
                'present' => $present,
                'absent'  => $absent,
            ]
        ]);
    }
    public function todayCheckIns()
    {
        $today = Carbon::today();

        // Get all check-ins for today with student info
        $checkIns = DB::table('attendances')
            ->join('students', 'attendances.student_id', '=', 'students.id')
            ->whereDate('attendances.date', $today) // ✅ safer than exact match
            ->where('attendances.status', 'present')
            ->select(
                'students.name as student_name',
                'students.lrn',
                'students.grade',
                'attendances.time_in',
                'attendances.created_at'
            )
            ->orderBy('attendances.created_at', 'desc')
            ->get()
            ->map(function ($checkIn) {
                return [
                    'student_name' => $checkIn->student_name ?? 'Unknown Student',
                    'lrn' => $checkIn->lrn ?? '',
                    'grade' => $checkIn->grade ?? '',
                    'time_in' => $checkIn->time_in ? Carbon::parse($checkIn->time_in)->format('h:i A') : '--',
                    'created_at' => $checkIn->created_at
                ];
            });

        // Get stats
        $totalStudents = Student::count();
        $presentCount = $checkIns->count();
        $absentCount = $totalStudents - $presentCount;

        return response()->json([
            'success' => true,
            'checkIns' => $checkIns,
            'stats' => [
                'total' => $totalStudents,
                'present' => $presentCount,
                'absent' => $absentCount
            ]
        ]);
    }
}
