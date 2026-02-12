<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',  // This should be student ID, not user/admin ID
        'date',
        'time_in',
        'time_out',
    ];

    protected $casts = [
        'date' => 'date',
    ];

    /**
     * Get the student that owns this attendance record
     */
    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id', 'id');
    }
}