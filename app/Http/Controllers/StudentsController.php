<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

use App\Models\Student; // Import the Student model

class StudentsController extends Controller
{
    public function index()
    {
        $students = Student::all(); // Fetch all students
        return view('students.index', compact('students'));
    }

    public function create()
    {
        $students = Student::paginate(10);
        return view('students.create', compact('students'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'lrn' => 'required|string|unique:students',
            'rfid' => 'nullable|string|unique:students',
            'grade' => 'required|string|max:10',
        ]);

        Student::create($request->all());

        return redirect()->route('students.create')->with('success', 'Student added successfully.');
    }
    public function update(Request $request, Student $student)
    {
        $validated = $request->validate([
            'name' => 'required|string',
            'lrn' => 'required|string|max:12',
            'grade' => 'required|string',
            'rfid' => 'nullable|string'
        ]);

        $student->update($validated);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Student updated successfully'
            ]);
        }

        return redirect()->route('students.index')
            ->with('success', 'Student updated successfully');
    }
    public function show(Student $student)
    {
        if (request()->wantsJson() || request()->ajax()) {
            return response()->json([
                'id' => $student->id,
                'name' => $student->name,
                'lrn' => $student->lrn,
                'grade' => $student->grade,
                'rfid' => $student->rfid,
            ]);
        }

        return view('students.show', compact('student'));
        
    }
    public function destroy(Student $student)
{
    $student->delete();

    return response()->json([
        'success' => true,
        'message' => 'Student deleted successfully!'
    ]);
}
}
