<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FeePayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'fee_invoice_id',
        'amount_paid',
        'payment_method',
        'payment_date',
        'received_by',
        'note'
    ];

    public function invoice()
    {
        return $this->belongsTo(FeeInvoice::class, 'fee_invoice_id');
    }
}