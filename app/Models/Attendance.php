<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Student;

class Attendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',   // CHANGE THIS
        'date',
        'time_in',
        'time_out',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }
}
