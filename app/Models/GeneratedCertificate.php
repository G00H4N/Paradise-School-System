<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GeneratedCertificate extends Model
{
    use HasFactory;
    protected $fillable = ['campus_id', 'student_id', 'certificate_type', 'serial_number', 'issue_date', 'remarks', 'issued_by'];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }
}