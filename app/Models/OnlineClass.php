<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OnlineClass extends Model
{
    use HasFactory;
    protected $fillable = ['campus_id', 'class_id', 'subject_id', 'teacher_id', 'topic', 'meeting_platform', 'meeting_link', 'meeting_id', 'password', 'start_time', 'end_time', 'status'];

    protected $casts = ['start_time' => 'datetime', 'end_time' => 'datetime'];

    public function schoolClass()
    {
        return $this->belongsTo(SchoolClass::class, 'class_id');
    }
    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }
    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }
}