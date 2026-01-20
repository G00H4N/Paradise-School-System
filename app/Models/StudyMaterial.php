<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudyMaterial extends Model
{
    use HasFactory;
    protected $fillable = ['class_id', 'subject_id', 'title', 'description', 'file_path', 'youtube_url', 'uploaded_by'];

    public function schoolClass()
    {
        return $this->belongsTo(SchoolClass::class, 'class_id');
    }
    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }
}