<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StaffDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'designation',
        'basic_salary',
        'joining_date',
        'qualification',
        'is_active'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function salaries()
    {
        return $this->hasMany(Salary::class, 'staff_id');
    }
    public function loans()
    {
        return $this->hasMany(StaffLoan::class, 'staff_id');
    }
}