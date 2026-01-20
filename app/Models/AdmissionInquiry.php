<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdmissionInquiry extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_name',
        'father_name',
        'phone',
        'class_id',
        'previous_school',
        'status', // pending, converted, rejected
        'follow_up_date',
        'remarks'
    ];

    public function schoolClass()
    {
        return $this->belongsTo(SchoolClass::class, 'class_id');
    }
}