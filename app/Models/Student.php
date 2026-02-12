<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'lrn',
        'rfid',
        'grade',
        'email',
    ];

    /**
     * Get all attendance records for this student
     */
    public function attendances()
    {
        return $this->hasMany(Attendance::class, 'student_id', 'id');
    }

    /**
     * Get today's attendance for this student
     */
    public function todayAttendance()
    {
        return $this->attendances()
            ->whereDate('date', today())
            ->first();
    }
}