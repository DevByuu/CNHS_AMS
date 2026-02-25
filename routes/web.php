<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\StudentsController;
use App\Http\Controllers\ReportsController;
use Illuminate\Support\Facades\Route;
use App\Models\Student;
use App\Models\Attendance;

/*
|--------------------------------------------------------------------------
| Guest Routes
|--------------------------------------------------------------------------
*/

// Redirect root to login
Route::get('/', function () {
    return redirect()->route('login');
});

// Login
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login.submit');

/*
|--------------------------------------------------------------------------
| Authenticated Routes
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {

    // Logout
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

    // Dashboard
    Route::get('/dashboard', function () {
        $totalStudents = Student::count();
        $presentToday = Attendance::whereDate('date', today())->count();

        $recentActivities = [
            ['text'=>'John Doe marked present', 'time'=>'2 minutes ago', 'icon'=>'bi bi-check-circle','bg_class'=>'bg-success-subtle'],
            ['text'=>'Maria Santos added', 'time'=>'15 minutes ago', 'icon'=>'bi bi-person-plus','bg_class'=>'bg-primary-subtle'],
            ['text'=>'Juan Cruz arrived late', 'time'=>'28 minutes ago', 'icon'=>'bi bi-clock','bg_class'=>'bg-warning-subtle'],
            ['text'=>'Sarah Lee marked absent', 'time'=>'1 hour ago', 'icon'=>'bi bi-x-circle','bg_class'=>'bg-danger-subtle'],
        ];

        return view('dashboard', compact(
            'totalStudents',
            'presentToday',
            'recentActivities'
        ));
    })->name('dashboard');

    /*
    |--------------------------------------------------------------------------
    | Students
    | IMPORTANT: Custom routes MUST come before Route::resource()
    |--------------------------------------------------------------------------
    */
    Route::delete('students/batch-delete', [StudentsController::class, 'batchDestroy'])->name('students.batch-delete');
    Route::get('/students/download-template', [StudentsController::class, 'downloadTemplate'])->name('students.download-template');
    Route::post('/students/import-csv', [StudentsController::class, 'importCsv'])->name('students.import-csv');
    Route::resource('students', StudentsController::class);



    /*
    |--------------------------------------------------------------------------
    | Reports
    |--------------------------------------------------------------------------
    */
    Route::get('/reports', [ReportsController::class, 'index'])->name('reports.index');
    Route::get('/reports/export', [ReportsController::class, 'export'])->name('reports.export');
    Route::get('/reports/export-present', [ReportsController::class, 'exportPresentStudents'])->name('reports.export.present');
    Route::get('/reports/realtime-data', [ReportsController::class, 'realtimeData'])->name('reports.realtime');

    /*
    |--------------------------------------------------------------------------
    | API Routes
    |--------------------------------------------------------------------------
    */
    Route::get('/api/reports/realtime', [ReportsController::class, 'realtimeData']);
    Route::post('/api/reports/realtime', [ReportsController::class, 'getRealTimeData'])->name('api.reports.realtime');
    Route::get('/api/dashboard/stats', [DashboardController::class, 'stats'])->name('dashboard.stats');
    Route::get('/api/attendance/today', [AttendanceController::class, 'todayCheckIns'])->name('attendance.today');
});

/*
|--------------------------------------------------------------------------
| Admin Routes (STRICTLY ADMIN ONLY)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'admin'])->group(function () {

    // Admin users
    Route::get('/admin/users/create', [UserController::class, 'create'])->name('users.create');
    Route::post('/admin/users', [UserController::class, 'store'])->name('users.store');

    // Attendance Routes
    Route::get('/attendance', [AttendanceController::class, 'index'])->name('attendance.index');
    Route::post('/attendance/checkin', [AttendanceController::class, 'storeRfid'])->name('attendance.checkin');

       Route::get('/students/download-template', [StudentsController::class, 'downloadTemplate'])->name('students.download-template');
    Route::post('/students/import-csv', [StudentsController::class, 'importCsv'])->name('students.import-csv');
    Route::resource('students', StudentsController::class);


    });