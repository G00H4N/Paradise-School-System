<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BookIssue extends Model
{
    use HasFactory;
    protected $fillable = ['book_id', 'student_id', 'issue_date', 'return_date', 'returned_on', 'fine_amount', 'status', 'remarks'];

    public function book()
    {
        return $this->belongsTo(Book::class);
    }
    public function student()
    {
        return $this->belongsTo(Student::class);
    }
}