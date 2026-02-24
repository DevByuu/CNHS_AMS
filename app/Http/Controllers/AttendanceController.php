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

        $today = Carbon::now()->toDateString();

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

        $student = Student::where('rfid', $request->rfid)->first();

        if (!$student) {
            return response()->json([
                'success' => false,
                'message' => 'RFID card not registered. Please contact the administrator.'
            ], 404);
        }

        $today = Carbon::now()->toDateString();

        $attendance = Attendance::where('student_id', $student->id)
            ->whereDate('date', $today)
            ->first();

        if (!$attendance) {
            $timeIn = Carbon::now();

            Attendance::create([
                'student_id' => $student->id,
                'date'       => $today,
                'time_in'    => $timeIn->format('H:i:s'),
                'status'     => 'present'
            ]);

            $stats = $this->getTodayStats();

            return response()->json([
                'success' => true,
                'action' => 'check-in',
                'message' => 'Check-in successful',
                'student' => [
                    'id'      => $student->id,
                    'name'    => $student->name,
                    'lrn'     => $student->lrn,
                    'grade'   => $student->grade ?? 'N/A',
                    'time_in' => $timeIn->format('h:i A'),
                ],
                'stats' => $stats
            ]);
        }

        if ($attendance && !$attendance->time_out) {
            $timeIn = Carbon::parse($attendance->date . ' ' . $attendance->time_in);
            $timeOut = Carbon::now();
            $durationMinutes = $timeIn->diffInMinutes($timeOut);

            $attendance->update([
                'time_out'         => $timeOut->format('H:i:s'),
                'duration_minutes' => $durationMinutes
            ]);

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
     * API endpoint for polling — returns today's check-ins + latest scan info
     */
    public function todayApi()
    {
        $today = Carbon::today();

        $checkIns = DB::table('attendances')
            ->join('students', 'attendances.student_id', '=', 'students.id')
            ->whereDate('attendances.date', $today)
            ->where('attendances.status', 'present')
            ->select(
                'attendances.id',
                'students.name as student_name',
                'students.lrn',
                'students.grade',
                'attendances.time_in',
                'attendances.time_out',
                'attendances.duration_minutes',
                'attendances.updated_at'
            )
            ->orderBy('attendances.updated_at', 'desc')
            ->get()
            ->map(function ($checkIn) {
                return [
                    'id'           => $checkIn->id,
                    'student_name' => $checkIn->student_name ?? 'Unknown',
                    'lrn'          => $checkIn->lrn ?? '',
                    'grade'        => $checkIn->grade ?? '',
                    'time_in'      => $checkIn->time_in  ? Carbon::parse($checkIn->time_in)->format('h:i A')  : '--',
                    'time_out'     => $checkIn->time_out ? Carbon::parse($checkIn->time_out)->format('h:i A') : null,
                    'duration'     => $checkIn->duration_minutes ? $this->formatDuration($checkIn->duration_minutes) : null,
                    'status'       => $checkIn->time_out ? 'checked_out' : 'on_campus',
                    'updated_at'   => $checkIn->updated_at,
                ];
            });

        // Latest record (most recently updated) — used to detect new scans
        $latest = $checkIns->first();

        $stats = $this->getTodayStats();

        return response()->json([
            'success'    => true,
            'checkIns'   => $checkIns,
            'stats'      => $stats,
            'latest_id'  => $latest ? $latest['id'] : null,
            'latest_updated_at' => $latest ? $latest['updated_at'] : null,
        ]);
    }

    /**
     * Get today's check-ins (web route)
     */
    public function todayCheckIns()
    {
        $today = Carbon::today();

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
                return [
                    'student_name' => $checkIn->student_name ?? 'Unknown Student',
                    'lrn'          => $checkIn->lrn ?? '',
                    'grade'        => $checkIn->grade ?? '',
                    'time_in'      => $checkIn->time_in  ? Carbon::parse($checkIn->time_in)->format('h:i A')  : '--',
                    'time_out'     => $checkIn->time_out ? Carbon::parse($checkIn->time_out)->format('h:i A') : 'Not yet',
                    'duration'     => $checkIn->duration_minutes ? $this->formatDuration($checkIn->duration_minutes) : 'On campus',
                    'status'       => $checkIn->time_out ? 'Completed' : 'On campus',
                    'created_at'   => $checkIn->created_at,
                ];
            });

        $stats = $this->getTodayStats();

        return response()->json([
            'success'  => true,
            'checkIns' => $checkIns,
            'stats'    => $stats
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

        return [
            'total'   => $totalStudents,
            'present' => $presentToday,
            'absent'  => $totalStudents - $presentToday,
        ];
    }

    /**
     * Format duration in hours and minutes
     */
    private function formatDuration($minutes)
    {
        if (!$minutes) return '0m';
        $hours = floor($minutes / 60);
        $mins  = $minutes % 60;
        return $hours > 0 ? "{$hours}h {$mins}m" : "{$mins}m";
    }
}