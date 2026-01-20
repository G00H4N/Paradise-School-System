<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    use HasFactory;

    // app/Models/Attendance.php ke andar yeh add karein:

    protected $fillable = [
        'student_id',
        'staff_id', // <--- NEW ADDITION
        'attendance_date',
        'status',
        'check_in',
    ];

    // Relationship
    public function staff()
    {
        return $this->belongsTo(StaffDetail::class, 'staff_id');
    }

    // Dates ko automatic Carbon object banata hai taake format karna asaan ho
    protected $casts = [
        'attendance_date' => 'date',
        'check_in' => 'datetime', // Time format handle karne ke liye
    ];

    // Relationship: Har attendance aik student ki hoti hai
    public function student()
    {
        return $this->belongsTo(Student::class);
    }
}