<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subject extends Model
{
    use HasFactory;

    // Specification: Har class ke alag subjects (Maths, English, Urdu)
    protected $fillable = ['class_id', 'subject_name', 'subject_code', 'passing_marks', 'total_marks'];

    public function schoolClass()
    {
        return $this->belongsTo(SchoolClass::class, 'class_id');
    }
}