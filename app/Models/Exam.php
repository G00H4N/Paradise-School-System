<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Exam extends Model
{
    use HasFactory;

    // Specification: Exam Title (Mid-Term), Date, Session
    protected $fillable = ['exam_title', 'start_date', 'session_year'];

    // Relationship: Aik Exam mein boht se Marks hotay hain
    public function marks()
    {
        return $this->hasMany(Mark::class);
    }
}