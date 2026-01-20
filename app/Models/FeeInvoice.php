<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FeeInvoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'fee_type_id',
        'invoice_title',
        'total_amount',
        'paid_amount',
        'discount_amount', // Sibling Logic
        'status',          // paid, unpaid, partial
        'due_date',
        'session_year'
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }
    public function feeType()
    {
        return $this->belongsTo(FeeType::class);
    }
    public function payments()
    {
        return $this->hasMany(FeePayment::class);
    }
}