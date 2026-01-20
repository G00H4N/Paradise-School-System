<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ParentWallet extends Model
{
    use HasFactory;

    protected $fillable = ['parent_user_id', 'balance'];

    public function parent()
    {
        return $this->belongsTo(User::class, 'parent_user_id');
    }
}