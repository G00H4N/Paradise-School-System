<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SchoolClass extends Model
{
    use HasFactory;

    // Migration mein jo columns banaye thay unhein fillable karna hai
    protected $fillable = ['class_name', 'section_name', 'numeric_name'];

    // Ek class mein boht se students ho sakte hain
    public function students()
    {
        return $this->hasMany(Student::class, 'class_id');
    }
}