<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FeeType extends Model
{
    use HasFactory;

    protected $fillable = ['fee_title', 'default_amount', 'description'];

    public function invoices()
    {
        return $this->hasMany(FeeInvoice::class);
    }
}