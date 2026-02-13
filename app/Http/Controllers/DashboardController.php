<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\Attendance;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $data = $this->getDashboardStats();
        return view('dashboard', $data);
    }

    public function stats()
    {
        $data = $this->getDashboardStats();
        return response()->json($data);
    }

    private function getDashboardStats()
    {
        $today = Carbon::today();
        
        // Total students
        $totalStudents = Student::count();
        
        // Today's attendance
        $presentToday = Attendance::where('date', $today)
            ->where('status', 'present')
            ->count();
            
        $absentToday = Attendance::where('date', $today)
            ->where('status', 'absent')
            ->count();
            
        $lateToday = Attendance::where('date', $today)
            ->where('status', 'late')
            ->count();
        
        // Calculate percentages
        $presentPercentage = $totalStudents > 0 
            ? round(($presentToday / $totalStudents) * 100, 1) 
            : 0;
            
        $absentPercentage = $totalStudents > 0 
            ? round(($absentToday / $totalStudents) * 100, 1) 
            : 0;
            
        $latePercentage = $presentToday > 0 
            ? round(($lateToday / $presentToday) * 100, 1) 
            : 0;
        
        // Last 7 days trend
        $trendLabels = [];
        $trendPresent = [];
        $trendAbsent = [];
        
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $trendLabels[] = $date->format('M d');
            
            $present = Attendance::where('date', $date)
                ->where('status', 'present')
                ->count();
                
            $absent = Attendance::where('date', $date)
                ->where('status', 'absent')
                ->count();
            
            $trendPresent[] = $present;
            $trendAbsent[] = $absent;
        }
        
        return [
            'totalStudents' => $totalStudents,
            'presentToday' => $presentToday,
            'absentToday' => $absentToday,
            'lateToday' => $lateToday,
            'presentPercentage' => $presentPercentage,
            'absentPercentage' => $absentPercentage,
            'latePercentage' => $latePercentage,
            'trendLabels' => $trendLabels,
            'trendPresent' => $trendPresent,
            'trendAbsent' => $trendAbsent,
        ];
    }
}