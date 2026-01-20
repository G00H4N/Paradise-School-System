<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StaffLoan extends Model
{
    use HasFactory;

    protected $fillable = [
        'staff_id',
        'loan_amount',
        'total_installments',
        'monthly_installment',
        'remaining_balance',
        'status'
    ];

    public function staff()
    {
        return $this->belongsTo(StaffDetail::class, 'staff_id');
    }
}