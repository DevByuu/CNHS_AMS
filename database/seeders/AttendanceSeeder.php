<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Student;
use Carbon\Carbon;

class AttendanceSeeder extends Seeder
{
    public function run(): void
    {
        $students = Student::pluck('id'); // existing IDs
        $records = [];

        foreach ($students as $studentId) {

            for ($d = 0; $d < 5; $d++) {

                $date = Carbon::now()->subDays($d);

                $timeIn = Carbon::parse($date->toDateString().' 07:30:00')
                    ->addMinutes(rand(0, 30));

                $timeOut = Carbon::parse($date->toDateString().' 16:00:00')
                    ->addMinutes(rand(0, 20));

                $records[] = [
                    'student_id' => $studentId,
                    'date' => $date->toDateString(),
                    'status' => 'Present',
                    'time_in' => $timeIn,
                    'time_out' => $timeOut,
                    'duration_minutes' => $timeOut->diffInMinutes($timeIn),
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }

        DB::table('attendances')->insert($records);
    }
}
