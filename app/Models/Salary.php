<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Salary extends Model
{
    use HasFactory;

    protected $fillable = [
        'staff_id',
        'salary_month',
        'basic_salary',
        'bonus',
        'deductions',
        'net_salary',
        'status'
    ];

    public function staff()
    {
        return $this->belongsTo(StaffDetail::class, 'staff_id');
    }
}