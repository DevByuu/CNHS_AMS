<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    public function index()
    {
        // Example data for testing
        $attendance_today = 25; // replace with real logic

        return view('attendance.index', compact('attendance_today'));
    }

    public function rfidCheck()
{
    return view('attendance.rfid-check');
}

public function storeRfid(Request $request)
{
    $request->validate([
        'rfid' => 'required|string|exists:students,rfid',
    ]);

    $student = \App\Models\Student::where('rfid', $request->rfid)->first();

    // Mark attendance
    $student->attendances()->create([
        'status' => 'present', // or calculate late/absent based on time
        'created_at' => now(),
    ]);

    return back()->with('success', $student->name . ' attendance recorded!');
}

}
