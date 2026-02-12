<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\Student;
use App\Models\Attendance;
use Carbon\Carbon;

class StudentsController extends Controller
{
    /**
     * Display a listing of students (for admin dashboard)
     */
    public function index()
    {
        $students = Student::all();
        return view('students.index', compact('students'));
    }

    /**
     * Show the form for creating a new student AND list all students
     * (This is your main student management page)
     */
    public function create(Request $request)
    {
        $query = Student::query();

        // Search filter
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('lrn', 'LIKE', "%{$search}%")
                  ->orWhere('rfid', 'LIKE', "%{$search}%");
            });
        }

        // Grade filter
        if ($request->has('grade') && $request->grade != '') {
            $query->where('grade', $request->grade);
        }

        // RFID filter
        if ($request->has('rfid') && $request->rfid != '') {
            if ($request->rfid == 'with') {
                $query->whereNotNull('rfid');
            } elseif ($request->rfid == 'without') {
                $query->whereNull('rfid');
            }
        }

        // Paginate results
        $students = $query->orderBy('created_at', 'desc')->paginate(15);

        // Get statistics
        $studentsWithRfid = Student::whereNotNull('rfid')->count();
        
        $today = Carbon::now()->toDateString();
        $activeToday = Attendance::whereDate('date', $today)
            ->whereNotNull('time_in')
            ->distinct('student_id')
            ->count();

        return view('students.create', compact(
            'students',
            'studentsWithRfid',
            'activeToday'
        ));
    }

    /**
     * Store a newly created student
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'lrn' => 'required|string|max:12|unique:students,lrn',
            'rfid' => 'nullable|string|max:50|unique:students,rfid',
            'grade' => 'required|string',
            'email' => 'nullable|email|max:255',
        ]);

        $student = Student::create($validated);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Student added successfully!',
                'student' => $student
            ]);
        }

        return redirect()->route('students.create')
            ->with('success', 'Student added successfully.');
    }

    /**
     * Display the specified student
     */
    public function show(Student $student)
    {
        if (request()->wantsJson() || request()->ajax()) {
            return response()->json([
                'id' => $student->id,
                'name' => $student->name,
                'lrn' => $student->lrn,
                'grade' => $student->grade,
                'email' => $student->email ?? null,
                'rfid' => $student->rfid,
            ]);
        }

        return view('students.show', compact('student'));
    }

    /**
     * Update the specified student
     */
    public function update(Request $request, Student $student)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'lrn' => 'required|string|max:12|unique:students,lrn,' . $student->id,
            'rfid' => 'nullable|string|max:50|unique:students,rfid,' . $student->id,
            'grade' => 'required|string',
            'email' => 'nullable|email|max:255',
        ]);

        $student->update($validated);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Student updated successfully!',
                'student' => $student
            ]);
        }

        return redirect()->route('students.create')
            ->with('success', 'Student updated successfully');
    }

    /**
     * Remove the specified student
     */
    public function destroy(Student $student)
    {
        $studentName = $student->name;
        $student->delete();

        if (request()->wantsJson() || request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => $studentName . ' deleted successfully!'
            ]);
        }

        return redirect()->route('students.create')
            ->with('success', $studentName . ' deleted successfully!');
    }
}