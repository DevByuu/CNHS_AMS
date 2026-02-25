<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
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
        $students = $query->orderBy('created_at', 'desc')->paginate(100);

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

    /**
     * Download CSV template
     */
    public function downloadTemplate()
    {
        $filename = 'student_import_template.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function() {
            $file = fopen('php://output', 'w');
            
            // Headers
            fputcsv($file, ['name', 'lrn', 'grade', 'email', 'rfid']);
            
            // Sample data
            fputcsv($file, ['Juan Dela Cruz', '1000000001', 'Grade 7', 'juan@example.com', '']);
            fputcsv($file, ['Maria Santos', '1000000002', 'Grade 8', 'maria@example.com', 'RFID101']);
            fputcsv($file, ['Pedro Reyes', '1000000003', 'Grade 9', 'pedro@example.com', '']);
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Import students from CSV
     */
    public function importCsv(Request $request)
    {
        try {
            // Validate file
            $validator = Validator::make($request->all(), [
                'csv_file' => 'required|file|max:5120'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid file. Please upload a CSV file (max 5MB)',
                    'errors' => $validator->errors()->all()
                ], 422);
            }

            $file = $request->file('csv_file');

            // Check extension manually
            $extension = strtolower($file->getClientOriginalExtension());
            if (!in_array($extension, ['csv', 'txt'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid file. Please upload a CSV file.',
                ], 422);
            }

            $path = $file->getRealPath();

            // Read CSV - handle all line ending formats (Windows \r\n, Mac \r, Linux \n)
            $content = file_get_contents($path);
            $content = str_replace("\r\n", "\n", $content);
            $content = str_replace("\r", "\n", $content);
            $lines = array_filter(explode("\n", $content), fn($line) => trim($line) !== '');
            $csvData = array_map('str_getcsv', array_values($lines));

            if (empty($csvData)) {
                return response()->json([
                    'success' => false,
                    'message' => 'CSV file is empty'
                ], 422);
            }

            // Get headers
            $headers = array_map('trim', array_map('strtolower', $csvData[0]));

            // Validate required columns
            $requiredColumns = ['name', 'lrn', 'grade'];
            $missingColumns = array_diff($requiredColumns, $headers);

            if (!empty($missingColumns)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Missing required columns: ' . implode(', ', $missingColumns)
                ], 422);
            }

            // Process rows
            $imported = 0;
            $skipped = 0;
            $errors = 0;
            $errorMessages = [];

            for ($i = 1; $i < count($csvData); $i++) {
                $row = $csvData[$i];

                // Skip empty rows
                if (empty(array_filter($row, fn($v) => trim($v) !== ''))) {
                    continue;
                }

                // Map row data to headers
                $data = [];
                foreach ($headers as $index => $header) {
                    $data[$header] = isset($row[$index]) ? trim($row[$index]) : null;
                }

                // Validate required fields
                if (empty($data['name']) || empty($data['lrn']) || empty($data['grade'])) {
                    $errors++;
                    $errorMessages[] = "Row " . ($i + 1) . ": Missing required fields (name, lrn, or grade)";
                    continue;
                }

                // Normalize grade - accept "7" or "Grade 7"
                $grade = trim($data['grade']);
                if (!str_starts_with(strtolower($grade), 'grade')) {
                    $grade = 'Grade ' . $grade;
                }

                // Validate grade — FIXED: must match normalized "Grade X" format
                $validGrades = ['Grade 7', 'Grade 8', 'Grade 9', 'Grade 10', 'Grade 11', 'Grade 12'];
                if (!in_array($grade, $validGrades)) {
                    $errors++;
                    $errorMessages[] = "Row " . ($i + 1) . ": Invalid grade '{$grade}'";
                    continue;
                }

                // Check if student already exists by LRN
                if (Student::where('lrn', $data['lrn'])->exists()) {
                    $skipped++;
                    continue;
                }

                // Create student
                try {
                    Student::create([
                        'name'  => $data['name'],
                        'lrn'   => $data['lrn'],
                        'grade' => $grade,
                        'email' => !empty($data['email']) ? $data['email'] : null,
                        'rfid'  => !empty($data['rfid']) ? $data['rfid'] : null,
                    ]);
                    $imported++;
                } catch (\Exception $e) {
                    $errors++;
                    $errorMessages[] = "Row " . ($i + 1) . ": " . $e->getMessage();
                }
            }

            return response()->json([
                'success' => true,
                'message' => "Import completed successfully",
                'imported' => $imported,
                'skipped' => $skipped,
                'errors' => $errors,
                'error_messages' => array_slice($errorMessages, 0, 10)
            ]);

        } catch (\Exception $e) {
            Log::error('CSV Import Error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to process CSV file: ' . $e->getMessage()
            ], 500);
        }
    }
    public function batchDestroy(Request $request)
{
    $ids = $request->input('ids', []);

    if (empty($ids)) {
        return response()->json([
            'success' => false,
            'message' => 'No students selected.'
        ], 422);
    }

    $deleted = Student::whereIn('id', $ids)->delete();

    return response()->json([
        'success' => true,
        'message' => $deleted . ' student(s) deleted successfully.',
        'deleted' => $deleted
    ]);
}
}