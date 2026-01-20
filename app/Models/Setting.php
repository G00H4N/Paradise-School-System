<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasFactory;
    protected $fillable = ['school_name', 'school_address', 'school_phone', 'school_email', 'currency_symbol', 'current_session', 'logo_path'];
}