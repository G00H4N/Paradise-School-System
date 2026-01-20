<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'parent_id',
        'class_id',
        'transport_route_id', // Module 6
        'admission_no',
        'roll_no',
        'full_name',
        'father_name',
        'mother_name',
        'caste',            // Video 2 Spec
        'gender',
        'birthday',
        'religion',
        'blood_group',
        'phone',
        'address',
        'admission_date',
        'profile_photo_path',
        'is_active'         // Status tracking
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function schoolClass()
    {
        return $this->belongsTo(SchoolClass::class, 'class_id');
    }
    public function parent()
    {
        return $this->belongsTo(User::class, 'parent_id');
    }
    public function transport()
    {
        return $this->belongsTo(TransportRoute::class, 'transport_route_id');
    }
    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }
    public function marks()
    {
        return $this->hasMany(Mark::class);
    }
    public function invoices()
    {
        return $this->hasMany(FeeInvoice::class);
    }
}